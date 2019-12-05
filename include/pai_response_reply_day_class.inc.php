<?php
/*
 * 每天分析操作类
 * xiao xiao
 */

class pai_response_reply_day_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 22 );
		$this->setDBName ( 'yueyue_log_tmp_db' );
		$this->setTableName ( 'sendserver_response_reply_day_log' );
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
		$tablename = "sendserver_response_reply_day_log_{$time}";
		//echo $tablename;exit;
		$res = $this->query("SHOW TABLES FROM yueyue_log_tmp_db LIKE '{$tablename}'");
		if (empty($res) || !is_array($res)) 
		{
			$tab = "yueyue_log_tmp_db.{$tablename}";
			$creat_sql = "CREATE TABLE IF NOT EXISTS {$tab} (
                          `id` int(10) NOT NULL auto_increment,
                          `user_id` int(10) unsigned NOT NULL default '0',
                          `5i` int(10) unsigned NOT NULL default '0',
                          `10i` int(10) unsigned NOT NULL default '0',
                          `20i` int(10) unsigned NOT NULL default '0',
                          `30i` int(10) unsigned NOT NULL default '0',
                          `1h` int(10) unsigned NOT NULL default '0',
                          `12h` int(10) unsigned NOT NULL default '0',
                          `24h` int(10) unsigned NOT NULL default '0',
                          `no_response` int(10) unsigned NOT NULL default '0',
                          `add_time` date NOT NULL default '0000-00-00',
                          PRIMARY KEY  (`id`),
                          UNIQUE KEY `id` (`id`,`add_time`)
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
			return $this->insert($data, "REPLACE");
		}
		/**
		 * *获取数据是否存在
		 * @param  [int] $user_id    [用户id]
		 * @param  [date] $add_time  [添加时间为天]
		 * @return [boolean]         [false|true]
		 */
		public function find_id_by_user_add_time($user_id, $add_time)
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
		 * 
		 * @param  boolean $b_select_count [是否查询总数]
		 * @param  string  $where_str      [查询条件]
		 * @param  string  $order_by       [排序]
		 * @param  string  $limit          [循环个数]
		 * @param  string  $fields         [字段]
		 * @return [array]                  [返回数组|boolean]
		 */
		public function get_replay_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,20', $fields = '*')
		{
			$this->selectTable();
			if ($b_select_count == true) 
			{
				$ret = $this->findCount($where_str);
			}
			else
			{
				$ret = $this->findAll($where_str, $limit, $order_by,$fields);
			}
			return $ret;
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
	 public function get_replay_list_v2($tablename , $b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,30', $fields = '*')
	{
		if (empty($tablename)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '：表选择出错' );
		}
		$this->setTableName($tablename);
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str , $fields);
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
	public function get_tablename_by_month($month = '')
	{
		//$month = $month;
		if (empty($month)) 
		{
			$month = date('Y-m', time());
		}
		$month = date('Ym', strtotime($month) );
		$tablename = 'sendserver_response_reply_day_log_'.$month;
		$res = $this->query("SHOW TABLES FROM `yueyue_log_tmp_db` LIKE '{$tablename}'");
		if (empty($res) || !is_array($res)) 
		{
			return false;
		}
		return $tablename;
	}

	/**
     * 获取回复速度
     * @param  string $where_str [条件]
     * @param  [string] $month     [年月]
     * @return [array]            [返回一个数组]
     */
	public function get_count_replay_by_user_id($where_str = '', $month)
	{
		if(empty($month))
		{
			return false;
		}
		//echo $month;
		$begin_month = beginMonth($month,true);
		$end_month   = endMonth($month,true);
		$tablename = $this->get_tablename_by_month($month);
		if (empty($tablename)) 
		{
			return false;
		}
		//$tmp_month   = date('Ym', strtotime($month));
		if(strlen($where_str) > 0) $where_str .= ' AND ';
		$where_str .= " add_time >= '{$begin_month}' AND add_time <= '{$end_month}'";
		$sql = "SELECT user_id,sum(5i) AS 5i,sum(10i) AS 10i,sum(20i) AS 20i,sum(30i) AS 30i,sum(1h) AS 1h,sum(12h) AS 12h,sum(24h) AS 24h,sum(no_response) AS no_response from yueyue_log_tmp_db.{$tablename} where {$where_str} GROUP BY user_id";
		$ret = $this->findBySql($sql);
		return $ret;
	}

	/**
     * 获取回复速度数据
     * @param  string $where_str [条件]
     * @param  [string] $day    [天]
     * @return [array]            [返回一个数组]
     */
	public function get_count_replay_by_user_id_day($where_str = '', $day)
	{
		if(empty($day))
		{
			return false;
		}
		$tablename = $this->get_tablename_by_month($day);
		if (empty($tablename)) 
		{
			return false;
		}
		/*if(strlen($where_str) > 0) $where_str .= ' AND ';
		$where_str .= " add_time = '{$day}'";*/
		$sql = "SELECT user_id,sum(5i) AS 5i,sum(10i) AS 10i,sum(20i) AS 20i,sum(30i) AS 30i,sum(1h) AS 1h,sum(12h) AS 12h,sum(24h) AS 24h,sum(no_response) AS no_response from yueyue_log_tmp_db.{$tablename} GROUP BY user_id";
		$ret = $this->findBySql($sql);
		return $ret;
	}


}

?>