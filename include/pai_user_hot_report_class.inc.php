<?php

/**
 * @desc 模特和摄影师集合榜单作为中间控制器使用
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月28日
 * @version 1
 */
 class pai_user_hot_report_class extends POCO_TDG
 {
 	/**
 	 * 构造函数
 	 *
 	 */
 	public function __construct()
 	{
 		/* $this->setServerId ( 22 );
 		$this->setDBName ( 'yueyue_cameraman_hot_db' );
 		$this->setTableName ( 'yueyue_cameraman_seven_hot_tbl_20150415' ); */
 		
 	}
 	
 	/*
 	 * 获取热榜数据
 	 * @param $location_id [int]    地区ID     [101029001]
 	 * @param $role        [string] 角色                    [model|cameraman|'']
 	 * @param $module      [string] 获取所得名称    
 	 * [model(all[总消费],week_cash[周消费],week_order[周订单],three_visit[三天访问值]), 
 	 * cameraman(all[总消费],week_cash[周消费],week_order[周订单],month_praise[月好评])]
  	 * 
 	 * */
 	public function get_hot_list($location_id=101029001,$role='model',$module = 'all')
 	{
 		$post_data = array();
 		$date = date('Y-m-d',time()-24*3600);
 		$post_data['update_time'] = $date;
 		
 		if($role == 'model')
 		{
 			if($module == 'all')
 			{
 				//模特总类
 				$model_total_hot_obj = POCO::singleton('pai_model_total_hot_report_class');
 				$post_data['data'] = $model_total_hot_obj->get_total_hot_list($date, $b_select_count = false,$location_id,'','details_price DESC,details_count DESC,user_id DESC','0,30','user_id,details_price');
 				//return $post_data;
 			}
 			//周消费
 			elseif($module == 'week_cash')
 			{
 				$model_seven_hot_obj = POCO::singleton('pai_model_seven_hot_report_class');
 				$post_data['data'] = $model_seven_hot_obj->get_seven_hot_list($date, $b_select_count = false,$location_id,'','details_price DESC,details_count DESC,user_id DESC','0,30','user_id,details_price');
 				//return $post_data;
 			}
 			//周订单榜
 			elseif ($module == 'week_order')
 			{
 				$model_seven_hot_obj = POCO::singleton('pai_model_seven_hot_report_class');
 				$post_data['data'] = $model_seven_hot_obj->get_seven_hot_list($date, $b_select_count = false,$location_id,'','details_count DESC,details_price DESC,user_id DESC','0,30','user_id,details_count');
 				//return $post_data;
 			}
 			//三天访问榜
 			elseif ($module == 'three_visit')
 			{
 				$model_three_visit_obj = POCO::singleton('pai_model_three_visit_report_class');
 				$post_data['data'] = $model_three_visit_obj->get_three_visit_list($date,false,$location_id,'','count_visit DESC,user_id DESC','0,30','user_id,count_visit');
 				//return $post_data;
 			}
 			/* else
 			{
 				return array();
 			} */
 		}
 		//摄影师获取角色为空时
 		elseif($role == 'cameraman' || $role == '')
 		{
 			//摄影师总榜
 			if($module == 'all')
 			{
 				$cameraman_total_hot_obj = POCO::singleton('pai_cameraman_total_hot_report_class');
 				$post_data['data'] = $cameraman_total_hot_obj->get_total_hot_list($date,false,$location_id,'','details_price DESC,details_count DESC,user_id DESC','0,30','user_id,details_price');
 				//return $list;
 			}
 			//周消费
 			if($module == 'week_cash')
 			{
 				$cameraman_seven_hot_obj = POCO::singleton('pai_cameraman_seven_hot_report_class');
 				$post_data['data'] = $cameraman_seven_hot_obj->get_seven_hot_list($date,false,$location_id,'','details_price DESC,details_count DESC,user_id DESC','0,30','user_id,details_price');
 				//return $list;
 			}
 			//周订单榜
 			elseif ($module == 'week_order')
 			{
 				$cameraman_seven_hot_obj = POCO::singleton('pai_cameraman_seven_hot_report_class');
 				$post_data['data'] = $cameraman_seven_hot_obj->get_seven_hot_list($date,false,$location_id,'','details_count DESC,details_price DESC,user_id DESC','0,30','user_id,details_count');
 				//return $list;
 			}
 			//好评榜
 			elseif ($module == 'month_praise')
 			{
 				$cameraman_month_hot_obj = POCO::singleton('pai_cameraman_month_hot_report_class');
 				$post_data['data'] = $cameraman_month_hot_obj->get_month_hot_list($date,false,$location_id,'','score DESC,user_id DESC','0,30','user_id,score');
 				//return $list;
 			}
 		}
 		return $post_data;
 	}
 	
 	
 	
 }
 
 