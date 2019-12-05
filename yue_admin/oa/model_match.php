<?php

include_once 'common.inc.php';
include_once 'top.php';
//地区引用
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$order_id = ( int ) $_INPUT ['order_id'];
$complete_recommend = ( int ) $_INPUT ['complete_recommend'];
$confirm_order = ( int ) $_INPUT ['confirm_order'];
$shoot_confirm = ( int ) $_INPUT ['shoot_confirm'];
$cancel = ( int ) $_INPUT ['cancel'];
$cancel_reason = $_INPUT ['cancel_reason'];


$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$oa_model_list_obj = POCO::singleton ( 'pai_model_oa_model_list_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
$task_obj = POCO::singleton('pai_task_questionnaire_class');

$order_info = $model_oa_order_obj->get_order_info ( $order_id );

$question = $task_obj->show_questionnaire_data($order_info['request_id']);


$tpl = new SmartTemplate ( "model_match.tpl.htm" );


$source = $order_info['source'];

if($complete_recommend==1)
{
	$check_add = $oa_model_list_obj->check_add_model($order_id);
	
	if(!$check_add)
	{
		echo "<script>alert('还没有添加商家');</script>";
		exit;
	}

	$ret = $model_oa_order_obj->order_complete_recommend($order_id);
	if($ret==1)
	{
		if($source==4)
		{
			echo "<script>alert('已完成推荐');parent.location.href='list.php?list_status=doing&requirement=1&order_status=complete_recommend';</script>";
		}
		else
		{
			echo "<script>alert('已完成推荐');parent.location.href='list.php?list_status=doing';</script>";
		}
		exit;
	}elseif($ret==-1)
	{
		echo "<script>alert('当前订单状态不是已下单状态，请刷新页面');</script>";
		exit;
	}
	else
	{
		echo "<script>alert('推荐失败，请联系管理员');</script>";
		exit;
	}
}

if($confirm_order==1)
{
	$ret = $model_oa_order_obj->order_confirm_order($order_id);
	if($ret==1)
	{
		echo "<script>alert('打回成功');parent.location.href='list.php?list_status=doing';</script>";
		exit;
	}
	else
	{
		echo "<script>alert('打回失败，请联系管理员');</script>";
		exit;
	}
}

if($shoot_confirm==1)
{

	$check_select = $oa_model_list_obj->check_select_model($order_id);
	
	if(!$check_select)
	{
		echo "<script>alert('还没选择合适的商家');</script>";
		exit;
	}
	
	$ret = $model_oa_order_obj->order_shoot_confirm($order_id);
	if($ret==1)
	{
		echo "<script>alert('确认成功');parent.location.href='list.php?list_status=doing';</script>";
		exit;
	}
	elseif ($ret==-1)
	{
		
	}
	else
	{
		echo "<script>alert('确认失败，请联系管理员');</script>";
		exit;
	}
}

if($cancel==1)
{
	$ret = $model_oa_order_obj->order_cancel($order_id,$cancel_reason);
	if($ret==1)
	{
		echo "<script>alert('取消成功');parent.location.href='list.php?list_status=cancel';</script>";
		exit;
	}
	else
	{
		echo "<script>alert('取消失败，请联系管理员');</script>";
		exit;
	}
}



$order_info['city_name'] = get_poco_location_name_by_location_id ( $order_info ['location_id']);

$order_info['date_remark'] = str_replace("\n","<br />",$order_info['date_remark']);

$model_list = $oa_model_list_obj->get_model_list(false, 'order_id='.$order_id, 'id DESC', '0,5000');
foreach($model_list as $k=>$val)
{
	$model_list[$k]['cellphone'] = $user_obj->get_phone_by_user_id($val['user_id']);
	$model_list[$k]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
	$model_list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);
}

$tpl->assign ( "question",$question );
$tpl->assign ( 'order', $order_info );
$tpl->assign ( 'order_id', $order_id );
$tpl->assign ( 'model_list', $model_list );
$tpl->assign ( 'oa_role', $oa_role );

$tpl->output ();

?>