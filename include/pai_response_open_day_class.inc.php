<?php
/*
 * �Ķ���������
 * xiao xiao
 */

class pai_response_open_day_class extends POCO_TDG
{
	
	/**
	 * ���캯��
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
	 * @param  string $time [ʱ��]
	 * @return [void]       [û�з�������]
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
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '�����ݲ���Ϊ��' );
		}
		return $this->insert($data,"REPLACE");
	}
	/**
	 * *��ȡ�����Ƿ����
	 * @param  [int] $user_id    [�û�id]
	 * @param  [date] $add_time  [���ʱ��Ϊ��]
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
	 * ��������
	 * @param  [int] $user_id  [�û�id]
	 * @param  [array] $data    [����]
	 * @return [boolean]        [false|true]
	 */
	public function update_info($user_id,$add_time,$data)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '���û�ID����Ϊ��' );
		}
		if (empty($add_time)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '��ʱ�䲻��Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '�����ݲ���Ϊ��' );
		}
		return $this->update($data,"user_id={$user_id} AND add_time='{$add_time}'");
	}

	/**
	 * ��ȡ�Ķ���
	 * @param  [int] $user_id [�û�id]
	 * @param  [date] $add_time [���ʱ��]
	 * @return [boolean]          [false|int]
	 */
	public function get_open_count_by_user_add_time($user_id, $add_time)
	{
		$month = strtotime($add_time);
		//ѡ�����ݿ�
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
	  * @param  [string]  $tablename      [����]
	  * @param  boolean $b_select_count [�Ƿ��ѯ����]
	  * @param  string  $where_str      [����]
	  * @param  string  $order_by       [����]
	  * @param  string  $limit          [ѭ������]
	  * @param  string  $fields         [�����ֶ�]
	  * @return [array]                 [����ֵ]
	  */
	 public function get_open_list_v2($tablename , $b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,30', $fields = '*')
	{
		if (empty($tablename)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '����ѡ�����' );
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
	 * @param  [string] $month [���·�]
	 * @return [string|boolean]   [�������»���false]
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