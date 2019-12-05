<?php

/**
 * @desc ��Ӱʦ�º�����
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��17��
 * @version 1
 */
 class pai_cameraman_month_hot_report_class extends POCO_TDG
 {
 	/**
 	 * ���캯��
 	 *
 	 */
 	public function __construct()
 	{
 		$this->setServerId ( 22 );
 		$this->setDBName ( 'yueyue_cameraman_hot_db' );
 		$this->setTableName ( 'yueyue_cameraman_month_score_tbl_20150415' );
 		//�ų����û�ID
 		$this->notShowUser = '103380,102611,100079,103572,100007,102208,100036,100832';
 	}

     /**
      * �������а�
      * @param date  $date           ʱ��
      * @param bool  $b_select_count �Ƿ��ѯ����
      * @param int   $location_id    ����ID
      * @param string $where_str     ����
      * @param string $order_by      ����
      * @param string $limit         ѭ������
      * @param string $fields        ��ѯ�ֶ�
      * @return array|int            ����ֵ
      */
     public function get_month_hot_list($date, $b_select_count = false,$location_id, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
 	{
 		//��������
        $date  = trim($date);
 		if(strlen($date) <1) return false;
 		$info = $this->select_table($date);
 		if(empty($info)) return false;

        $location_id = intval($location_id);
 		//�ų��û�
 		if(strlen($where_str) >0 ) $where_str .= ' AND ';
 		$where_str .= "user_id NOT IN ({$this->notShowUser})";
 		
 		if($location_id >0)
 		{
 			if(strlen($where_str) >0) $where_str .= ' AND ';
 			$where_str .= "location_id = {$location_id}";
 		}
 		
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
      * @param date  $date
      * @return int  ����ֵ
      */
     public function select_table($date)
 	{
        $date = trim($date);
 		if(strlen($date) <1) return false;
 		$table_num  = date('Ymd',strtotime($date));
 		$sign_tab =  'yueyue_cameraman_month_score_tbl_'.$table_num;
 		$res = db_simple_getdata("SHOW TABLES FROM yueyue_cameraman_hot_db LIKE '{$sign_tab}'", TRUE, 22);
 		//�����ڱ��˳�
 		if(!is_array($res) || empty($res))
 		{
 			return 0;
 		}
 		$this->setTableName($sign_tab);
 		return 1;
 	}
 	
 	
 }
 
 