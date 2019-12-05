<?php

/**
 * 模特实时报表
 * @authors xiao xiao (xiaojm@yueyue.com)
 * @date    2015年4月13日
 * @version 1
 */

 class pai_model_realtime_report_class extends POCO_TDG
 {
 	/**
 	 * 构造函数
 	 *
 	 */
 	public function __construct()
 	{
 		$this->setServerId ( 22 );
 		$this->setDBName ( 'yueyue_modelinfo_db' );
 		$this->setTableName ( 'model_userinfo_tbl' );
 	}
 	
 	/**
 	 * 获取模特实时数据
 	 * $b_select_count [boolean] true|false     是否查询条数
 	 * $user_id        [int]      0|int         模特ID
 	 * $where_str      [string]   ''|where_str  条件
 	 * $limit          [string]   '0,10'        循环条数
 	 * $order_by       [string]                 排序
 	 * $fields         [string]    *|查询字段                 查询字段
 	 * */
 	public function get_model_realtime_list($b_select_count = false,$user_id = 0, $where_str ='', $limit = '0,10', $order_by = 'last_login_time DESC',  $fields = '*')
 	{
 		$user_id = (int) $user_id;
 		if($user_id > 0)
 		{
 			if(strlen($where_str) > 0) $where_str .= ' AND ';
 			$where_str .= "user_id = {$user_id}";
 		}
 		if($b_select_count == true)
 		{
 			$ret = $this->findCount($where_str);
 			return $ret;
 		}
 		$ret = $this->findAll($where_str,$limit, $order_by, $fields);
 		return $ret;
 	}
 	
 }
 
 
 