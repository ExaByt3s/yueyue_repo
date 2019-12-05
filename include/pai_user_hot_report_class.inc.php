<?php

/**
 * @desc ģ�غ���Ӱʦ���ϰ���Ϊ�м������ʹ��
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��28��
 * @version 1
 */
 class pai_user_hot_report_class extends POCO_TDG
 {
 	/**
 	 * ���캯��
 	 *
 	 */
 	public function __construct()
 	{
 		/* $this->setServerId ( 22 );
 		$this->setDBName ( 'yueyue_cameraman_hot_db' );
 		$this->setTableName ( 'yueyue_cameraman_seven_hot_tbl_20150415' ); */
 		
 	}
 	
 	/*
 	 * ��ȡ�Ȱ�����
 	 * @param $location_id [int]    ����ID     [101029001]
 	 * @param $role        [string] ��ɫ                    [model|cameraman|'']
 	 * @param $module      [string] ��ȡ��������    
 	 * [model(all[������],week_cash[������],week_order[�ܶ���],three_visit[�������ֵ]), 
 	 * cameraman(all[������],week_cash[������],week_order[�ܶ���],month_praise[�º���])]
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
 				//ģ������
 				$model_total_hot_obj = POCO::singleton('pai_model_total_hot_report_class');
 				$post_data['data'] = $model_total_hot_obj->get_total_hot_list($date, $b_select_count = false,$location_id,'','details_price DESC,details_count DESC,user_id DESC','0,30','user_id,details_price');
 				//return $post_data;
 			}
 			//������
 			elseif($module == 'week_cash')
 			{
 				$model_seven_hot_obj = POCO::singleton('pai_model_seven_hot_report_class');
 				$post_data['data'] = $model_seven_hot_obj->get_seven_hot_list($date, $b_select_count = false,$location_id,'','details_price DESC,details_count DESC,user_id DESC','0,30','user_id,details_price');
 				//return $post_data;
 			}
 			//�ܶ�����
 			elseif ($module == 'week_order')
 			{
 				$model_seven_hot_obj = POCO::singleton('pai_model_seven_hot_report_class');
 				$post_data['data'] = $model_seven_hot_obj->get_seven_hot_list($date, $b_select_count = false,$location_id,'','details_count DESC,details_price DESC,user_id DESC','0,30','user_id,details_count');
 				//return $post_data;
 			}
 			//������ʰ�
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
 		//��Ӱʦ��ȡ��ɫΪ��ʱ
 		elseif($role == 'cameraman' || $role == '')
 		{
 			//��Ӱʦ�ܰ�
 			if($module == 'all')
 			{
 				$cameraman_total_hot_obj = POCO::singleton('pai_cameraman_total_hot_report_class');
 				$post_data['data'] = $cameraman_total_hot_obj->get_total_hot_list($date,false,$location_id,'','details_price DESC,details_count DESC,user_id DESC','0,30','user_id,details_price');
 				//return $list;
 			}
 			//������
 			if($module == 'week_cash')
 			{
 				$cameraman_seven_hot_obj = POCO::singleton('pai_cameraman_seven_hot_report_class');
 				$post_data['data'] = $cameraman_seven_hot_obj->get_seven_hot_list($date,false,$location_id,'','details_price DESC,details_count DESC,user_id DESC','0,30','user_id,details_price');
 				//return $list;
 			}
 			//�ܶ�����
 			elseif ($module == 'week_order')
 			{
 				$cameraman_seven_hot_obj = POCO::singleton('pai_cameraman_seven_hot_report_class');
 				$post_data['data'] = $cameraman_seven_hot_obj->get_seven_hot_list($date,false,$location_id,'','details_count DESC,details_price DESC,user_id DESC','0,30','user_id,details_count');
 				//return $list;
 			}
 			//������
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
 
 