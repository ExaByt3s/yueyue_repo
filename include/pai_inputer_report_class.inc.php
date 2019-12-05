<?php

/**
 * �����߱�����
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-04-02 16:19:50
 * @version 1
 */

class pai_inputer_report_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 22 );
		$this->setDBName ( 'yueyue_stat_db' );
		$this->setTableName ( 'yueyue_inputer_report_tbl_201503' );
	}
	/**
	 * ��ȡ����
	 * @param int $month
	 */
	public function get_tablename_by_month($month = '')
	{
		//$month = $month;
		if (empty($month)) 
		{
			$month = date('Y-m', time());
		}
		$month = date('Ym', strtotime($month) );
		$tablename = 'yueyue_inputer_report_tbl_'.$month;
		$res = $this->query("SHOW TABLES FROM `yueyue_stat_db` LIKE '{$tablename}'");
		if (empty($res) || !is_array($res)) 
		{
			$tab = "yueyue_stat_db.{$tablename}";
			$creat_sql = "CREATE TABLE IF NOT EXISTS {$tab} (
                          `id` int(10) unsigned NOT NULL auto_increment,
                           `user_id` int(10) unsigned NOT NULL default '0',
                           `login_num` int(10) unsigned NOT NULL default '0',
                           `mes_count` int(10) unsigned NOT NULL default '0',
                           `replay_count` int(10) unsigned NOT NULL default '0',
                           `no_replay` int(10) unsigned NOT NULL default '0',
                           `detail_price` decimal(10,0) unsigned NOT NULL default '0',
                           `detail_num` int(10) unsigned NOT NULL default '0',
                           `cancel_num` int(10) unsigned NOT NULL default '0',
                           `inputer_name` varchar(100) NOT NULL default '',
                           `inputer_id` int(10) unsigned NOT NULL default '0',
                           `add_time` date NOT NULL default '0000-00-00',
                           PRIMARY KEY  (`id`),
                           UNIQUE KEY `user_id` (`user_id`,`add_time`)
                        ) TYPE=MyISAM COMMENT='�������û���'";
			$this->query($creat_sql);
		}
		return $tablename;
	}

	/*
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_inpuer_report_report_list($b_select_count = false,$month, $inputer_id = 0,$where_str = '', $order_by = 'count_price DESC', $limit = '0,20', $fields = '*')
	{
		if(empty($month))
        {
            return false;
        }
        $tablename = $this->get_tablename_by_month($month);
		$this->setTableName ( $tablename );
        $begin_month = beginMonth($month,true);
        $end_month   = endMonth($month,true);
        if(strlen($where_str) >0) $where_str .= ' AND ';
        $day = date('Y-m-d',time()-24*3600);
        $where_str .= " add_time = '{$day}'";
        if(strlen($inputer_id) >0) $where_str .= " AND inputer_id = '{$inputer_id}'";
		if ($b_select_count == true)
		{
			$sql = "SELECT count(*) FROM yueyue_stat_db.{$tablename} WHERE {$where_str} GROUP BY user_id";
			$ret = $this->findBySql($sql);
			return count($ret);
		}
		else
		{
			$sql = "SELECT sum(login_num) as login_count,sum(mes_count) as mes_count,sum(replay_count) as replay_count,sum(no_replay) as no_replay,sum(detail_price) as count_price,sum(detail_num) as count_num,sum(cancel_num) as cancel_num,inputer_id,user_id FROM yueyue_stat_db.{$tablename} WHERE {$where_str} GROUP BY user_id order by {$order_by} limit {$limit}";
			$ret = $this->findBySql($sql);
		}
		//print_r($ret);exit;
		//print_r($list);exit;
		return $ret;
	}

	/**
 	 * �������а�
 	 * $date          date     ʱ��
 	 * $b_select_true boolean  �Ƿ��ѯ����
 	 * $where_str     string   ����
 	 * $order_by      string   ����
 	 * $limit         string   ѭ������
 	 * $fields        string   ��ѯ�ֶ�
 	 *
 	 * */
 	public function get_inpuer_report_report_list_v2($date, $b_select_count = false, $inputer_id = 0, $order_by = 'count_price DESC', $limit = '0,20', $fields = '*')
 	{
 		//��������
 		if(empty($date)) return false;
 		$info = $this->select_table($date);
 		if(empty($info)) return false;

 		$where_str = '';
 		if($inputer_id > 0) $where_str .= "inputer_id = {$inputer_id}";
 		//ƽ��һ����ѯ
 		if ($b_select_count == true)
 		{
 			$ret = $this->findCount ( $where_str );
 		}
 		else
 		{
 			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
 		}
 		return $ret;
 	}
 	
 	/**
 	 * �жϱ��Ƿ���ڲ�ѡ���
 	 * $date date     ʱ��
 	 *
 	 * */
 	public function select_table($date)
 	{
 		if(empty($date)) return false;
 		$table_num  = date('Ymd',strtotime($date));
 		$sign_tab =  'yueyue_inputer_report_tbl_'.$table_num;
 		$res = db_simple_getdata("SHOW TABLES FROM yueyue_stat_db LIKE '{$sign_tab}'", TRUE, 22);
 		//�����ڱ��˳�
 		if(!is_array($res) || empty($res))
 		{
 			return 0;
 		}
 		$this->setTableName($sign_tab);
 		return 1;
 	}

	/**
	 * ������ݽ������
	 * @param [array] $data [���ݱ�]
	 */
	public function add_info($data,$month)
	{
		$tablename = $this->get_tablename_by_month($month);
		$this->setTableName ( $tablename );
		if(!is_array($data) || empty($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '�����ݲ���Ϊ��' );
		}
		$ret = $this->insert($data, 'REPLACE');
		return true;
	}

}

?>