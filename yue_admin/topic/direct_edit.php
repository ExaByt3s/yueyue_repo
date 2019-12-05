<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate ( "direct_edit.tpl.htm" );

$act = $_INPUT ['act'] ? $_INPUT ['act'] : 'add';

$type_id = ( int ) $_INPUT ['type_id'];
$id = ( int ) $_INPUT ['id'];
$title = $_INPUT ['title'];
$goods_id = (int)$_INPUT ['goods_id'];
$location_id = $_INPUT ['location_id'];
$is_auto_accept = $_INPUT ['is_auto_accept'];
$is_auto_sign = $_INPUT ['is_auto_sign'];
$limit_times = (int)$_INPUT ['limit_times'];
$num = (int)$_INPUT ['num'];
$message = $_INPUT ['message'];
$address = $_INPUT ['address'];
$service_time = $_INPUT ['service_time'];
$price_type = (int)$_INPUT ['price_type'];

$mall_direct_obj = POCO::singleton ( 'pai_mall_direct_order_class' );
$mall_goods_obj = POCO::singleton ( 'pai_mall_goods_class' );


$type_name_arr = array("3"=>"化妆服务","12"=>"影棚租赁","5"=>"摄影培训","31"=>"模特服务","40"=>"摄影服务","43"=>"约有趣");

switch ($act)
{
	case 'add' :
		if ($_POST ['act'])
		{

			
			if (! $goods_id)
			{
				js_pop_msg ( "商品ID不能为空" );
				exit ();
			}
			
			if (! $location_id)
			{
				js_pop_msg ( "省市不能为空" );
				exit ();
			}
			
			
			$goods_info = $mall_goods_obj->get_goods_info($goods_id);
		
			if($goods_info['goods_data']['type_id']!=$type_id)
			{
				js_pop_msg ( "该商品ID不在分类".$type_name_arr[$type_id]."里面" );
				exit ();
			}
			
			$insert_data ['title'] = $title;
			$insert_data ['type_id'] = $type_id;
			$insert_data ['goods_id'] = $goods_id;
			$insert_data ['location_id'] = $location_id;
			$insert_data ['is_auto_accept'] = $is_auto_accept;
			$insert_data ['is_auto_sign'] = $is_auto_sign;
			$insert_data ['limit_times'] = $limit_times;
			$insert_data ['num'] = $num;
			$insert_data ['message'] = $message;
			$insert_data ['address'] = $address;
			$insert_data ['service_time'] = strtotime($service_time);
			$insert_data ['price_type'] = $price_type;
			$insert_data ['add_time'] = time ();
			
			$mall_direct_obj->add_config ( $insert_data );
			
			echo "<script>alert('添加成功');parent.location.href='direct_list.php';</script>";
		}
		break;
	
	case 'edit' :
		$topic_info = $mall_direct_obj->get_config_info ( $id );
		$topic_info['province_id'] = substr($topic_info['location_id'],0,6);
		$topic_info['service_time'] = date('Y-m-d H:i',$topic_info['service_time']);
		if ($_POST ['act'])
		{

			
			if (! $goods_id)
			{
				js_pop_msg ( "商品ID不能为空" );
				exit ();
			}
			
			if (! $location_id)
			{
				js_pop_msg ( "省市不能为空" );
				exit ();
			}
			

			$goods_info = $mall_goods_obj->get_goods_info($goods_id);
			
			if($goods_info['goods_data']['type_id']!=$type_id)
			{
				js_pop_msg ( "该商品ID不在分类".$type_name_arr[$type_id]."里面" );
				exit ();
			}
			
			$update_data ['title'] = $title;
			$update_data ['type_id'] = $type_id;
			$update_data ['goods_id'] = $goods_id;
			$update_data ['location_id'] = $location_id;
			$update_data ['is_auto_accept'] = $is_auto_accept;
			$update_data ['is_auto_sign'] = $is_auto_sign;
			$update_data ['limit_times'] = $limit_times;
			$update_data ['num'] = $num;
			$update_data ['message'] = $message;
			$update_data ['address'] = $address;
			$update_data ['service_time'] = strtotime($service_time);
			$update_data ['price_type'] = $price_type;
			
			$mall_direct_obj->update_config ( $update_data, $id );
			echo "<script>alert('更新成功');parent.location.href='direct_list.php';</script>";
		}
		break;
}

$tpl->assign ( 'type_name', $type_name_arr[$type_id] );
$tpl->assign ( 'type_id', $type_id );
$tpl->assign ( $topic_info );
$tpl->assign ( 'act', $act );
$tpl->assign ( 'MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER );
$tpl->output ();

?>