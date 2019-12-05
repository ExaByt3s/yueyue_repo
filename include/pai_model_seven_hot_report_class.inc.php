<?php

/**
 * @desc 模特7天收入排行榜
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月17日
 * @version 1
 */
 class pai_model_seven_hot_report_class extends POCO_TDG
 {
 	/**
 	 * 构造函数
 	 *
 	 */
 	public function __construct()
 	{
 		$this->setServerId ( 22 );
 		$this->setDBName ( 'yueyue_model_hot_db' );
 		$this->setTableName ( 'yueyue_model_seven_hot_tbl_20150415' );
 		//排除的用户
 		$this->notShowUser = '102654,102616,107819,102688,102506,109265,109266,100042,100046';
 	}
 	
 	
 	/**
 	 * 模特7收入天排行榜
 	 * $date          date     时间
 	 * $b_select_true boolean  是否查询个数
 	 * $where_str     string   条件
 	 * $order_by      string   排序
 	 * $limit         string   循环条数
 	 * $fields        string   查询字段
 	 *
 	 * */
 	public function get_seven_hot_list($date, $b_select_count = false,$location_id = 0,$where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
 	{
 		
 		//处理数据
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
 		//平常一样查询
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
 	 * 判断表是否存在并选择表
 	 * $date date     时间
 	 *
 	 * */
 	public function select_table($date)
 	{
 		if(empty($date)) return false;
 		$table_num  = date('Ymd',strtotime($date));
 		$sign_tab =  'yueyue_model_seven_hot_tbl_'.$table_num;
 		$res = db_simple_getdata("SHOW TABLES FROM yueyue_model_hot_db LIKE '{$sign_tab}'", TRUE, 22);
 		//不存在表退出
 		if(!is_array($res) || empty($res))
 		{
 			return 0;
 		}
 		$this->setTableName($sign_tab);
 		return 1;
 	}
 	
 	
 }
 
 