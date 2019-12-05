<?php
/*
 * ÿ�����������
 * xiao xiao
 */

class pai_response_reply_day_class extends POCO_TDG
{
	
	/**
	 * ���캯��
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
				throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '�����ݲ���Ϊ��' );
			}
			return $this->insert($data, "REPLACE");
		}
		/**
		 * *��ȡ�����Ƿ����
		 * @param  [int] $user_id    [�û�id]
		 * @param  [date] $add_time  [���ʱ��Ϊ��]
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
		 * 
		 * @param  boolean $b_select_count [�Ƿ��ѯ����]
		 * @param  string  $where_str      [��ѯ����]
		 * @param  string  $order_by       [����]
		 * @param  string  $limit          [ѭ������]
		 * @param  string  $fields         [�ֶ�]
		 * @return [array]                  [��������|boolean]
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
	  * @param  [string]  $tablename      [����]
	  * @param  boolean $b_select_count [�Ƿ��ѯ����]
	  * @param  string  $where_str      [����]
	  * @param  string  $order_by       [����]
	  * @param  string  $limit          [ѭ������]
	  * @param  string  $fields         [�����ֶ�]
	  * @return [array]                 [����ֵ]
	  */
	 public function get_replay_list_v2($tablename , $b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,30', $fields = '*')
	{
		if (empty($tablename)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '����ѡ�����' );
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
	 * @param  [string] $month [���·�]
	 * @return [string|boolean]   [�������»���false]
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
     * ��ȡ�ظ��ٶ�
     * @param  string $where_str [����]
     * @param  [string] $month     [����]
     * @return [array]            [����һ������]
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
     * ��ȡ�ظ��ٶ�����
     * @param  string $where_str [����]
     * @param  [string] $day    [��]
     * @return [array]            [����һ������]
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