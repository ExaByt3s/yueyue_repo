<?php
exit;
include_once 'common.inc.php';


$goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$goods_property_obj = POCO::singleton('pai_mall_goods_type_attribute_class');

$type_list = $goods_type_obj->get_type_cate(2,'all');
// dump($type_list);
// die;
foreach($type_list as $k => $v)
{
	echo 'type_id'.':'.$v['id'].', '.' md5:'.md5($v['id']).", name:".$v['name']."</br>";
	$property_list = $goods_property_obj->get_type_attribute_cate(0,0,array(),$v['id']);
	//dump($property_list);
	foreach($property_list as $kp => $vp)
	{
		if($vp['level'] == 0)
		{
			$vp['level'] = 0.5;
		}
		echo str_repeat("&nbsp", $vp['level']*8).'property_id:'.$vp['id'].", md5:".md5($vp['id']).", name:".$vp['name']."</br>";
	}

}

//测试 日志类文件   pai_log_class  
                                               
 $obj=POCO::singleton(pai_mall_test_song_class);   

 $obj->do_update(2125605);

$task_goods_obj       = POCO::singleton('pai_mall_goods_class');
$goods_type  = POCO::singleton('pai_mall_goods_type_class');
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_statistical_obj = POCO::singleton('pai_mall_statistical_class');
$service_obj = POCO::singleton("pai_mall_certificate_service_class");
$_set_debug = false;  
switch ($action)
   {
   	case "add":
   			  
   	     break; 
   	case  "del":
         
         break;   		
   	case "edit";
         
   	     break;   
   	default:
   		$status = (int)$_INPUT ['status'];                        //商家的状态   0 -1  2   未审核  通过   不通过
   		$edit_status = (int)$_INPUT ['edit_status'];            
   		$show = (int)$_INPUT ['show'];
   		$type_id = (int)$_INPUT ['type_id'];
   		$keyword = $_INPUT['keyword'];
   		$orderby = $_INPUT['orderby']?$_INPUT['orderby']:0;
   		//$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
   		//$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
   		$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:'';
   		$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:'';
   		
   		$type_obj = POCO::singleton('pai_mall_goods_type_class');
   		$type_list = $type_obj->get_type_cate(2);
   		$search_type_list = $type_obj->get_first_cate();
   		
   		$type_list_name = array();
   		foreach($type_list as $key => $val)
   		{
   			$val['id']!=42?$type_list[$key]['show'] = true:"";
   			$type_list[$key]['selected'] = $val['id']==$type_id?true:false;
   			$type_list_name[$val['id']] = $val;
   		}
   		$show_status = pai_mall_load_config("goods_show");
   		foreach($show_status as $key => $val)
   		{
   			$show_list[] = array(
   					'key'=>$key,
   					'name'=>$val,
   					'selected'=>$key==$show?true:false,
   			);
   		}
   	  
   		$goods_status_list[]=array(
   				'key' => 10,
   				'name' => "全部",
   				'selected' => $status===10?true:false,
   		);
   		foreach($status_name as $key => $val)
   		{
   			$goods_status_list[] = array(
   					'key' => $key,
   					'name' => $val,
   					'selected' => $status===$key?true:false,
   			);
   		}
   		
   		$where = "type_id != '42'";
   		$where .= $status != 10?" AND status = '{$status}'":"";
   		$where .= $show?" AND is_show = '{$show}'":"";
   		$where .= $type_id?" AND type_id = '{$type_id}'":"";
   		$where .= $keyword?" AND (titles like '%".pai_mall_change_str_in($keyword)."%' OR user_id = '".(int)$keyword."' OR goods_id = '".(int)$keyword."')":"";
   		if($status!=0 and $begin_time and $end_time)
   		{
   			$begin_time_s = strtotime($begin_time);
   			$begin_time_s = $begin_time_s?$begin_time_s:0;
   			$end_time_s = strtotime($end_time);
   			$end_time_s = $end_time_s?$end_time_s+86400:0;
   			$where .= " AND audit_time >= {$begin_time_s} AND audit_time <= {$end_time_s}";
   		}
   		elseif($status==0 and $begin_time and $end_time)
   		{
   			$begin_time_s = strtotime($begin_time);
   			$begin_time_s = $begin_time_s?$begin_time_s:0;
   			$end_time_s = strtotime($end_time);
   			$end_time_s = $end_time_s?$end_time_s+86400:0;
   			$where .= " AND add_time >= {$begin_time_s} AND add_time <= {$end_time_s}";
   		}
   		$is_black = 10;
   		if( isset($_INPUT['is_black']) )
   		{
   			$is_black = $_INPUT['is_black'];
   			if($is_black != 10)
   			{
   				$where .= " AND is_black= '{$is_black}'";
   			}
   		}
   		//$post = mall_set_post_for_search($_POST);
   		//echo $post;
   		//print_r(mall_get_post_for_search($post));
   		$total_count = $task_goods_obj->get_goods_list(true, $where);
   		$page_obj = new show_page ();
   		$show_count = 20;
   		$page_obj->setvar ( array ("status" => $status,"begin_time" => $begin_time,"end_time" => $end_time,"show" => $show,"type_id" => $type_id,"keyword" => $keyword,'is_black'=>$is_black) );
   		$page_obj->set ( $show_count, $total_count );
   		$list = $task_goods_obj->get_goods_list(false, $where, "goods_id DESC", $page_obj->limit());
   		$task_seller_obj = POCO::singleton('pai_mall_seller_class');
   		$add_time_color = '';
   		
   		foreach($list as $key => $val)
   		{
   			$list[$key]['type_name'] = $type_list_name[$val['type_id']]['name'];
   			$list[$key]['status_name'] = $status_name[$val['status']];
   			$list[$key]['show_name'] = $show_status[$val['is_show']];
   		
   			if( $val['update_time'] > 0)
   			{
   				$compare_time = $val['update_time'];
   			}else if($val['add_time'] > 0)
   			{
   				$compare_time = $val['add_time'];
   			}
   		
   			$now = time();
   		
   			if( ($now-$compare_time) > 24*3600 && $val['status'] == 0 )
   			{
   				$add_time_color = 'rgb(245, 60, 60);';
   			}
   		
   			$list[$key]['add_time_color'] = $add_time_color;
   			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
   			$list[$key]['update_time'] = $val['update_time']>0?date("Y-m-d H:i:s",$val['update_time']):"未修改";
   			$list[$key]['audit_time'] = $val['audit_time']>0?date("Y-m-d H:i:s",$val['audit_time']):"未审核";
   			$location_id = explode(',',$val['location_id']);
   			$location_name = '';
   			foreach($location_id as $val_de)
   			{
   				$location_name .= ($location_name?"<br>":"").($val_de?get_poco_location_name_by_location_id($val_de):"全国");
   			}
   			$list[$key]['location_name'] = $location_name;
   			$list[$key]['seller_status'] = $task_seller_obj->get_seller_status($val['user_id'],2);
   		
   			////活动还在测试，所以拿状态时，传个false,默认是true代表线上
   			if($val['type_id'] == 42)
   			{
   				$service_status = $service_obj->check_user_activity_open_or_not($val['user_id']);
   			}else
   			{
   				$service_status = $service_obj->get_service_open_or_not($val['user_id'],$val['type_id'],false);
   			}
   				
   			if(!$service_status )
   			{
   				$list[$key]['seller_status'] = 0;
   			}
   			////
   			unset($add_time_color);
   		
   			$pai_risk_obj = POCO::singleton('pai_risk_class');
   			$seller_scalping_info = $pai_risk_obj->get_scalping_seller_info($val['user_id']);
   			if ($seller_scalping_info['add_by']=='system')
   			{
   				//系统
   				$list[$key]['is_flush'] = 1;
   				$list[$key]['scalping_titile'] = '刷单嫌疑(系统)';
   			} elseif($seller_scalping_info['add_by']=='manual')
   			{
   				//人工
   				$list[$key]['is_flush'] = 1;
   				$list[$key]['scalping_titile'] = '刷单嫌疑(人工)';
   			}
   		}
   		
   		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."song_test.tpl.htm" );
   		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
   		
   		$tpl->assign ( 'list', $list );
   		$tpl->assign ( 'orderby', $orderby );
   		
   		$tpl->assign('is_black',$is_black);
   		$tpl->assign ( 'status', $status );
   		$tpl->assign ( 'goods_status_list', $goods_status_list );
   		$tpl->assign ( 'show', $show );
   		$tpl->assign ( 'keyword', $keyword );
   		$tpl->assign ( 'type_id', $type_id );
   		//$tpl->assign ( 'user_id', $user_id );
   		$tpl->assign ( 'type_list', $type_list );
   		$tpl->assign( 'search_type_list' , json_encode($search_type_list));
   		$json_post_data = json_encode($_POST);
   		$tpl->assign('post', $json_post_data );
   		$tpl->assign ( 'show_list', $show_list );
   		$tpl->assign ( 'begin_time', $begin_time );
   		$tpl->assign ( 'end_time', $end_time );
   		$tpl->assign ( 'edit_status', $edit_status );
   		break;       
   }  

	//$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."song_test.tpl.htm" );
	
	
	
	$tpl->output ();

?>

