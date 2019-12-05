<?php

include_once 'common.inc.php';
include_once 'top.php';
include_once ('/disk/data/htdocs232/poco/pai/config/model_card_config.php');

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$oa_model_list_obj = POCO::singleton ( 'pai_model_oa_model_list_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
$task_obj = POCO::singleton('pai_task_questionnaire_class');

$cancel = $_INPUT['cancel'];
$confirm = $_INPUT['confirm'];
$wait_shoot = $_INPUT['wait_shoot'];
$wait_close = $_INPUT['wait_close'];
$order_id = $_INPUT['order_id'];
$cancel_reason = $_INPUT['cancel_reason'];

$order_info = $model_oa_order_obj->get_order_info($order_id);

$question = $task_obj->show_questionnaire_data($order_info['request_id']);

$source = $order_info['source'];

if($cancel)
{
	$ret = $model_oa_order_obj->order_cancel($order_id,$cancel_reason);
	if($ret==1)
	{
		$model_oa_order_obj->audit_not_pass($order_id);//审核不通过
		if($source==4)
		{
			echo "<script>alert('取消成功');parent.location.href='list.php?list_status=cancel&requirement=1';</script>";
		}
		else
		{
			echo "<script>alert('取消成功');parent.location.href='list.php?list_status=cancel';</script>";
		}
		exit;
	}
	elseif($ret==-1)
	{
		echo "<script>alert('该订单已结单，不能取消');</script>";
		exit;
	}
	else
	{
		echo "<script>alert('取消失败，请联系管理员');</script>";
		exit;
	}
}

if($confirm)
{
	$ret = $model_oa_order_obj->order_confirm_order($order_id);
	if($ret==1)
	{
		if($source==4)
		{
			echo "<script>alert('下单成功');parent.location.href='list.php?list_status=doing&requirement=1&order_status=confirm_order';</script>";
		}
		else
		{
			echo "<script>alert('下单成功');parent.location.href='list.php?list_status=doing';</script>";
		}
		exit;
	}
	else
	{
		echo "<script>alert('下单失败，请联系管理员');</script>";
		exit;
	}
}

if($wait_shoot)
{
	$ret = $model_oa_order_obj->order_wait_shoot($order_id);
	if($ret==1)
	{
		echo "<script>alert('通知成功');parent.location.href='list.php?list_status=doing';</script>";
		exit;
	}
	else
	{
		echo "<script>alert('通知失败，请联系管理员');</script>";
		exit;
	}
}

if($wait_close)
{
	$ret = $model_oa_order_obj->order_wait_close($order_id);
	if($ret==1)
	{
		echo "<script>alert('操作成功');parent.location.href='list.php?list_status=doing';</script>";
		exit;
	}
	else
	{
		echo "<script>alert('操作失败，请联系管理员');</script>";
		exit;
	}
}



$tpl = new SmartTemplate ( "order_edit.tpl.htm" );



$order_info['city_name'] = get_poco_location_name_by_location_id ( $order_info ['location_id'] );

$order_info['date_remark'] = str_replace("\n","<br />",$order_info['date_remark']);

$model_list = $oa_model_list_obj->get_model_list(false, 'order_id='.$order_id, 'id DESC', '0,5000');
foreach($model_list as $k=>$val)
{
	$model_list[$k]['cellphone'] = $user_obj->get_phone_by_user_id($val['user_id']);
	$model_list[$k]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
}

$order_info['model_list'] = $model_list;

$tpl->assign ( "question",$question );
$tpl->assign ( "order",$order_info );
$tpl->assign ( 'oa_role', $oa_role );

$tpl->output ();

?>