<?php

/**
 * @desc ģ��7���������а�
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��17��
 * @version 1
 */
 class pai_model_seven_hot_report_class extends POCO_TDG
 {
 	/**
 	 * ���캯��
 	 *
 	 */
 	public function __construct()
 	{
 		$this->setServerId ( 22 );
 		$this->setDBName ( 'yueyue_model_hot_db' );
 		$this->setTableName ( 'yueyue_model_seven_hot_tbl_20150415' );
 		//�ų����û�
 		$this->notShowUser = '102654,102616,107819,102688,102506,109265,109266,100042,100046';
 	}
 	
 	
 	/**
 	 * ģ��7���������а�
 	 * $date          date     ʱ��
 	 * $b_select_true boolean  �Ƿ��ѯ����
 	 * $where_str     string   ����
 	 * $order_by      string   ����
 	 * $limit         string   ѭ������
 	 * $fields        string   ��ѯ�ֶ�
 	 *
 	 * */
 	public function get_seven_hot_list($date, $b_select_count = false,$location_id = 0,$where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
 	{
 		
 		//��������
 		if(empty($date)) return false;
 		$info = $this->select_table($date);
 		if(empty($info)) return false;
 		if(strlen($where_str) >0 )$where_str .= ' AND ';
 		$where_str .= "user_id NOT IN({$this->notShowUser})";
 		
 		if($location_id > 0)
 		{
 			if(strlen($where_str)>0) $where_str .= ' AND ';
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
 	 * $date date     ʱ��
 	 *
 	 * */
 	public function select_table($date)
 	{
 		if(empty($date)) return false;
 		$table_num  = date('Ymd',strtotime($date));
 		$sign_tab =  'yueyue_model_seven_hot_tbl_'.$table_num;
 		$res = db_simple_getdata("SHOW TABLES FROM yueyue_model_hot_db LIKE '{$sign_tab}'", TRUE, 22);
 		//�����ڱ��˳�
 		if(!is_array($res) || empty($res))
 		{
 			return 0;
 		}
 		$this->setTableName($sign_tab);
 		return 1;
 	}
 	
 	
 }
 
 