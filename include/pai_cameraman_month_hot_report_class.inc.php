<?php

/**
 * @desc 摄影师月好评榜
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月17日
 * @version 1
 */
 class pai_cameraman_month_hot_report_class extends POCO_TDG
 {
 	/**
 	 * 构造函数
 	 *
 	 */
 	public function __construct()
 	{
 		$this->setServerId ( 22 );
 		$this->setDBName ( 'yueyue_cameraman_hot_db' );
 		$this->setTableName ( 'yueyue_cameraman_month_score_tbl_20150415' );
 		//排除的用户ID
 		$this->notShowUser = '103380,102611,100079,103572,100007,102208,100036,100832';
 	}

     /**
      * 其他排行榜
      * @param date  $date           时间
      * @param bool  $b_select_count 是否查询个数
      * @param int   $location_id    地区ID
      * @param string $where_str     条件
      * @param string $order_by      排序
      * @param string $limit         循环条数
      * @param string $fields        查询字段
      * @return array|int            返回值
      */
     public function get_month_hot_list($date, $b_select_count = false,$location_id, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
 	{
 		//处理数据
        $date  = trim($date);
 		if(strlen($date) <1) return false;
 		$info = $this->select_table($date);
 		if(empty($info)) return false;

        $location_id = intval($location_id);
 		//排除用户
 		if(strlen($where_str) >0 ) $where_str .= ' AND ';
 		$where_str .= "user_id NOT IN ({$this->notShowUser})";
 		
 		if($location_id >0)
 		{
 			if(strlen($where_str) >0) $where_str .= ' AND ';
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
      * @param date  $date
      * @return int  返回值
      */
     public function select_table($date)
 	{
        $date = trim($date);
 		if(strlen($date) <1) return false;
 		$table_num  = date('Ymd',strtotime($date));
 		$sign_tab =  'yueyue_cameraman_month_score_tbl_'.$table_num;
 		$res = db_simple_getdata("SHOW TABLES FROM yueyue_cameraman_hot_db LIKE '{$sign_tab}'", TRUE, 22);
 		//不存在表退出
 		if(!is_array($res) || empty($res))
 		{
 			return 0;
 		}
 		$this->setTableName($sign_tab);
 		return 1;
 	}
 	
 	
 }
 
 