<?php
/*
 * 阅读数操作类
 * xiao xiao
 */

class pai_response_open_day_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 22 );
		$this->setDBName ( 'yueyue_log_tmp_db' );
		$this->setTableName ( 'sendserver_response_open_day_log' );
	}

	/**
	 * 
	 * @param  string $time [时间]
	 * @return [void]       [没有返回数据]
	 */
	public function selectTable($time = '')
	{
		if (empty($time)) 
		{
			$time = time();
		}
		$time = date('Ym', $time);
		$tablename = "sendserver_response_open_day_log_{$time}";
		//echo $tablename;exit;
		$res = $this->query("SHOW TABLES FROM yueyue_log_tmp_db LIKE '{$tablename}'");
		if (empty($res) || !is_array($res)) 
		{
			$tab = "yueyue_log_tmp_db.{$tablename}";
			$creat_sql = "CREATE TABLE IF NOT EXISTS {$tab} (
                         `id` int(11) unsigned NOT NULL auto_increment,
                         `user_id` int(11) unsigned NOT NULL default '0',
                         `open_count` int(11) unsigned NOT NULL default '0',
                         `person_count` int(11) unsigned NOT NULL default '0',
                         `system_count` int(11) NOT NULL default '0',
                         `add_time` date NOT NULL default '0000-00-00',
                         PRIMARY KEY  (`id`),
                         UNIQUE KEY `user_id` (`user_id`,`add_time`)
                        ) TYPE=MyISAM";
			$this->query($creat_sql);
		}
		$this->setTableName ( $tablename );
	}
	
	public function insert_info($data)
	{
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '：数据不能为空' );
		}
		return $this->insert($data,"REPLACE");
	}
	/**
	 * *获取数据是否存在
	 * @param  [int] $user_id    [用户id]
	 * @param  [date] $add_time  [添加时间为天]
	 * @return [boolean]         [false|true]
	 */
	public function find_id_by_user_id($user_id, $add_time)
	{
		$where_str = "user_id = {$user_id} AND add_time= '{$add_time}'";
		$ret = $this->find($where_str,'id');
		if ($ret) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 更新数据
	 * @param  [int] $user_id  [用户id]
	 * @param  [array] $data    [数组]
	 * @return [boolean]        [false|true]
	 */
	public function update_info($user_id,$add_time,$data)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '：用户ID不能为空' );
		}
		if (empty($add_time)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '：时间不能为空' );
		}
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '：数据不能为空' );
		}
		return $this->update($data,"user_id={$user_id} AND add_time='{$add_time}'");
	}

	/**
	 * 获取阅读数
	 * @param  [int] $user_id [用户id]
	 * @param  [date] $add_time [添加时间]
	 * @return [boolean]          [false|int]
	 */
	public function get_open_count_by_user_add_time($user_id, $add_time)
	{
		$month = strtotime($add_time);
		//选择数据库
		$this->selectTable($month);
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			return false;
		}
		if (empty($add_time)) 
		{
			return false;
		}
		$where_str = "user_id = {$user_id} AND add_time = '{$add_time}'";
		$ret = $this->find($where_str, 'person_count');
		return (int)$ret['person_count'];
	}

	/**
	  * 
	  * @param  [string]  $tablename      [表名]
	  * @param  boolean $b_select_count [是否查询总数]
	  * @param  string  $where_str      [条件]
	  * @param  string  $order_by       [排序]
	  * @param  string  $limit          [循环条数]
	  * @param  string  $fields         [返回字段]
	  * @return [array]                 [返回值]
	  */
	 public function get_open_list_v2($tablename , $b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,30', $fields = '*')
	{
		if (empty($tablename)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '：表选择出错' );
		}
		$this->setTableName($tablename);
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str, $fields);
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}

	/**
	 * *
	 * @param  [string] $month [年月份]
	 * @return [string|boolean]   [返回年月或者false]
	 */
	public function get_tablename_by_month($month)
	{
		$month = $month;
		if (empty($month)) 
		{
			$month = date('Y-m', time());
		}
		$month = date('Ym', strtotime($month) );
		$tablename = 'sendserver_response_open_day_log_'.$month;
		$res = $this->query("SHOW TABLES FROM `yueyue_log_tmp_db` LIKE '{$tablename}'");
		if (empty($res) || !is_array($res)) 
		{
			return false;
		}
		return $tablename;
	}
}

?>