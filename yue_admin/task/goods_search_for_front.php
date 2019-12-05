<?php
ini_set('memory_limit','512M');

include_once 'common.inc.php';
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$service_obj = POCO::singleton("pai_mall_certificate_service_class");
$task_seller_obj = POCO::singleton('pai_mall_seller_class');
$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$status_name = pai_mall_load_config('goods_status');
$show_status = pai_mall_load_config("goods_show");

switch($action)
{
	case "export":
        exit('export');
	break;
	default:
        define('G_MALL_PROJECT_USER_ROOT',"http://yp.yueus.com/mall/user/test");
		$type_id = (int)$_INPUT['type_id'];
        if(empty($type_id))
        {
            $type_id = 31;
        }
        $obj = POCO::singleton('pai_mall_goods_type_attribute_class');
        //删除缓存
        $obj->del_property_for_search_get_data();
        $rs = $obj->property_for_search_get_data($type_id,true);
        
        $p = $_INPUT['p'] ? (int)$_INPUT['p'] : 1;
		$_INPUT ['user_id'] = (int)$_INPUT ['user_id'];
		$user_id = $_INPUT ['user_id']?$_INPUT ['user_id']:"";
        
        $type_list = $task_goods_type_obj->get_type_cate(2);
        $type_list_name = array();
		foreach($type_list as $key => $val)
		{
			$type_list[$key]['show'] = true;
			$type_list_name[$val['id']] = $val;
		}
        
        $detail_data = array();
        $third_data = array();
        if( ! empty($_INPUT['detail']) && $_INPUT['for_page'] == 1 )
        {
            $_INPUT['detail'] = urldecode($_INPUT['detail']);
            $detail_ll = explode('_',$_INPUT['detail']);
            
            if( ! empty($detail_ll) )
            {
                foreach($detail_ll as $k => $v)
                {
                    $detail_parent_child_ll = explode(',',$v);
                    if( ! empty($detail_parent_child_ll) )
                    {
                        $detail_data[$detail_parent_child_ll['0']] = $detail_parent_child_ll['1'];
                    }
                } 
            } 
           
           $_INPUT['detail'] = $detail_data;
        }
        
        if( ! empty($_INPUT['third']) && $_INPUT['for_page'] == 1 )
        {
            $_INPUT['third'] = urldecode($_INPUT['third']);
            $third_ll = explode('_',$_INPUT['third']);
            if( ! empty($third_ll) )
            {
                foreach($third_ll as $k => $v)
                {
                    $third_parent_child_ll = explode(',',$v);
                    if( ! empty($third_parent_child_ll) )
                    {
                        $third_data[$third_parent_child_ll['0']] = $third_parent_child_ll['1'];
                    }
                } 
            } 
           
           $_INPUT['third'] = $third_data;
        }
        
        $select_input = $_INPUT;
        
		$show_count = 20;
		$offect = ($p-1)*$show_count;
		$limit = "{$offect},{$show_count}";
		
		if($_set_debug)
		{
			$_INPUT['debug'] = true;
		}
        
        $price_list = array();
        $goods_list = $task_goods_obj->search_goods_list_by_fulltext($_INPUT,$limit);
	    
		$list = $goods_list['data'];
		$page_obj = new show_page ();
		unset($_INPUT['p']);
		unset($_INPUT['IP_ADDRESS']);
		unset($_INPUT['IP_ADDRESS1']);
		unset($_INPUT['request_method']);
		unset($_INPUT['s']);
		unset($_INPUT['page']);
		unset($_INPUT['action']);
		foreach($_INPUT as $key => $val)
		{
			if($val === "")
			{
				unset($_INPUT[$key]);
			}
		}
		//exit;
		$_INPUT['s_action'] = "goods";
		$post_data = mall_query_str($_INPUT);
        
        
        //分页的数据处理
        $detail = $third = '';
        if( ! empty($_INPUT['detail']) )
        {
            foreach($_INPUT['detail'] as $k => $v)
            {
                $detail .= "$k,$v". '_';
            }
            
            $detail = substr($detail, 0, -1);
            $_INPUT['detail'] = $detail;
            
            unset($detail);
            $_INPUT['for_page'] = 1;
            
        }
        
        if( ! empty($_INPUT['third']))
        {
            foreach($_INPUT['third'] as $k => $v)
            {
                $third .= "$k,$v". '_';
            }
            
            $third = substr($third, 0, -1);
            $_INPUT['third'] = $third;
            unset($third);
        }
        
		$page_obj->setvar($_INPUT);
		$page_obj->set($show_count, $goods_list['total'] );		
		$profile_data = array();
		$service_obj = POCO::singleton("pai_mall_certificate_service_class");
		foreach($list as $key => $val)
		{
            $list[$key]['user_name'] = $val['user_id']."</br>"."(".get_user_nickname_by_user_id($val['user_id']).")";
			$list[$key]['type_name'] = $type_list_name[$val['type_id']]['name'];
			$list[$key]['status_name'] = isset($val['goods_status'])?$status_name[$val['goods_status']]:$status_name[$val['status']];
			!isset($list[$key]['goods_status'])?$list[$key]['goods_status'] = $val['status']:"";
			$list[$key]['score'] = $val['review_times']?sprintf('%.2f',$val['total_overall_score']/$val['review_times']):0;
			$list[$key]['show_name'] = "已".$show_status[$val['is_show']];
			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$list[$key]['onsale_time'] = $val['onsale_time']>0?date("Y-m-d H:i:s",$val['onsale_time']):"未上线";
			$list[$key]['audit_time'] = $val['audit_time']>0?date("Y-m-d H:i:s",$val['audit_time']):"未审核";
			$location_id = explode(',',trim($val['location_id'],','));
			$location_name = '';
			foreach($location_id as $val_de)
			{
				$location_name .= ($location_name?"<br>":"").($val_de?get_poco_location_name_by_location_id($val_de):($val_de==0?"全国":""));
			}
			$list[$key]['location_name'] = $location_name;
			!$profile_data[$val['profile_id']]?$profile_data[$val['profile_id']] = $task_seller_obj->get_seller_profile_for_search($val['profile_id']):"";
			$list[$key]['profile_data'] = $profile_data[$val['profile_id']];
			$goods_data = $task_goods_obj->get_goods_info_for_search($val['goods_id']);
			$list[$key]['seller_status'] = $task_seller_obj->get_seller_status($val['seller_id']);
			$service_status = $service_obj->get_service_open_or_not($val['user_id'],$val['type_id']);
			if(!$service_status )
			{
				$list[$key]['seller_status'] = 0;
			}
			$list[$key]['belong_user'] = $goods_data['goods_data']['belong_user']?get_user_nickname_by_user_id($goods_data['goods_data']['belong_user']):"无";
			$list[$key]['goods_att'] = $goods_data['goods_att'];			
		}
        
        $page = $page_obj->output(1);
        
        include_once (TASK_TEMPLATES_ROOT."goods_search_for_front_tpl.php");
	break;
}

?>