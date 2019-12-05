<?php

include_once 'common.inc.php';
include_once 'top.php';

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$oa_model_list_obj = POCO::singleton ( 'pai_model_oa_model_list_class' );

$order_id = (int)$_INPUT['order_id'];
$cancel = (int)$_INPUT['cancel'];
$cancel_reason = $_INPUT['cancel_reason'];

if($cancel)
{
	$ret = $model_oa_order_obj->order_cancel($order_id,$cancel_reason);
	if($ret==1)
	{
		echo "<script>alert('取消成功');parent.location.href='list.php?list_status=cancel';</script>";
		exit;
	}
	else 
	{
		echo "<script>alert('取消失败');</script>";
	}
	exit;
}


$model_list = $oa_model_list_obj->get_model_list(false, "order_id={$order_id} and status=1", 'id DESC', '0,5000');
$order_info = $model_oa_order_obj->get_order_info($order_id);

foreach($model_list as $k=>$val)
{
    $model_list[$k]['model_nickname'] = get_user_nickname_by_user_id($val['user_id']);

    $model_list[$k]['cameraman_nickname'] = $order_info['cameraman_nickname'];

    $model_sum_price += $val['price'];
}





if($_POST['id'])
{
    if($model_sum_price!=$order_info['payable_amount'])
    {
        echo "<script>alert('商家应付金额与财务应付金额不符，请重新修改');</script>";
        exit;
    }

	$ids = $_INPUT['id'];
	
	$model_overall_score = $_INPUT['model_overall_score'];
	$model_expressive_score = $_INPUT['model_expressive_score'];
	$model_truth = $_INPUT['model_truth'];
	$model_time_sense = $_INPUT['model_time_sense'];
	$match_score = $_INPUT['match_score'];
	$manner_score = $_INPUT['manner_score'];
	$quality_score = $_INPUT['quality_score'];
	$model_comment = $_INPUT['model_comment'];
	
	$cameraman_overall_score = $_INPUT['cameraman_overall_score'];
	$cameraman_rp_score = $_INPUT['cameraman_rp_score'];
	$cameraman_time_sense = $_INPUT['cameraman_time_sense'];
	$cameraman_comment = $_INPUT['cameraman_comment'];
	
	foreach($ids as $k=>$id)
	{
		$update_data['model_overall_score'] = $model_overall_score[$k];
		$update_data['model_expressive_score'] = $model_expressive_score[$k];
		$update_data['model_truth'] = $model_truth[$k];
		$update_data['model_time_sense'] = $model_time_sense[$k];
		$update_data['match_score'] = $match_score[$k];
		$update_data['manner_score'] = $manner_score[$k];
		$update_data['quality_score'] = $quality_score[$k];
		
		if(!$model_comment[$k])
		{
			$model_comment[$k] = "合作愉快";
		}
		$update_data['model_comment'] = $model_comment[$k];
		
		$update_data['cameraman_overall_score'] = $cameraman_overall_score[$k];
		$update_data['cameraman_rp_score'] = $cameraman_rp_score[$k];
		$update_data['cameraman_time_sense'] = $cameraman_time_sense[$k];
		
		if(!$cameraman_comment[$k])
		{
			$cameraman_comment[$k] = "合作愉快";
		}
		$update_data['cameraman_comment'] = $cameraman_comment[$k];
		
		
		$oa_model_list_obj->update_model($update_data, $id);
		
	}
	
	$ret = $model_oa_order_obj->order_close($order_id);
	
	if($ret==1)
	{
		echo "<script>alert('结单成功');parent.location.href='list.php?list_status=done';</script>";
		exit;
	}
	else 
	{
		echo "<script>alert('结单失败');</script>";
	}
}

$tpl = new SmartTemplate ( "close_order.tpl.htm" );


$tpl->assign ( 'model_list', $model_list );
$tpl->assign ( 'order_id', $order_id );

$tpl->output ();

?>