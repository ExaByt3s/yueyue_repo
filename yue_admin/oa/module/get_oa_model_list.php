<?php 
include_once '../common.inc.php';


$order_id = ( int ) $_INPUT ['order_id'];

$tpl = new SmartTemplate ( "get_oa_model_list.tpl.html" );

$oa_model_list_obj = POCO::singleton ( 'pai_model_oa_model_list_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );

$model_list = $oa_model_list_obj->get_model_list(false, 'order_id='.$order_id, 'id DESC', '0,5000');
foreach($model_list as $k=>$val)
{
	$model_list[$k]['cellphone'] = $user_obj->get_phone_by_user_id($val['user_id']);
	$model_list[$k]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
	$model_list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);
}

$tpl->assign ( 'model_list', $model_list );
$tpl->assign ( 'order_id', $order_id );
$tpl->assign ( 'oa_role', $oa_role );

$tpl->output ();
?>