<?php

include_once 'common.inc.php';
include_once 'top.php';
//地区引用
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$order_id = ( int ) $_INPUT ['order_id'];
$complete_recommend = ( int ) $_INPUT ['complete_recommend'];



$model_oa_enroll_obj = POCO::singleton ( 'pai_model_oa_enroll_class' );
$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$oa_model_list_obj = POCO::singleton ( 'pai_model_oa_model_list_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
$task_obj = POCO::singleton('pai_task_questionnaire_class');

if($_INPUT['close_order']==1)
{

	$ret = $model_oa_order_obj->order_close($order_id);
		
	if($ret==1)
	{
		echo "<script>alert('结单成功');parent.location.href='list.php?list_status=done&requirement=1';</script>";
		exit;
	}
	else 
	{
		echo "<script>alert('结单失败');</script>";
	}
	exit;
}


$order_info = $model_oa_order_obj->get_order_info ( $order_id );

$order_info['city_name'] = get_poco_location_name_by_location_id ( $order_info ['location_id']);

$order_info['date_remark'] = htmlspecialchars_decode($order_info['date_remark']);

$question = $task_obj->show_questionnaire_data($order_info['request_id']);

$model_list = $oa_model_list_obj->get_model_list(false, "order_id={$order_id}", 'id DESC', '0,5000');
foreach($model_list as $k=>$val)
{
	$model_list[$k]['cellphone'] = $user_obj->get_phone_by_user_id($val['user_id']);
	$model_list[$k]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
	$model_list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);
}

$enroll_list = $model_oa_enroll_obj->get_model_list(false,"order_id={$order_id}", 'id DESC', '0,1000');
foreach($enroll_list as $k=>$val)
{
	$enroll_list[$k]['cellphone'] = $user_obj->get_phone_by_user_id($val['user_id']);
	$enroll_list[$k]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
	$enroll_list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);
}

if($order_info['request_id'])
{
    $tpl = new SmartTemplate ( "mall_order_view.tpl.htm" );
}
else
{
    $tpl = new SmartTemplate ( "order_view.tpl.htm" );
}

$tpl->assign ( "question",$question );
$tpl->assign ( 'order', $order_info );
$tpl->assign ( 'order_id', $order_id );
$tpl->assign ( 'oa_role', $oa_role );
$tpl->assign ( 'model_list', $model_list );
$tpl->assign ( 'enroll_list', $enroll_list );

$tpl->output ();

?>