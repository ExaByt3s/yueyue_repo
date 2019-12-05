<?php
//define('G_PAI_ECPAY_DEV', 1);
include_once 'common.inc.php';
include_once 'top.php';
//地区引用
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$order_id = ( int ) $_INPUT ['order_id'];
$complete_recommend = ( int ) $_INPUT ['complete_recommend'];

$tpl = new SmartTemplate ( "refund.tpl.htm" );
$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

if ($_POST && $order_id)
{
	$update_data ['refund_realname'] = $_INPUT ['refund_realname'];
	$update_data ['refund_type'] = $_INPUT ['refund_type'];
	$update_data ['refund_account'] = $_INPUT ['refund_account'];
	$update_data ['refund_running_number'] = $_INPUT ['refund_running_number'];
	$update_data ['refund_remark'] = $_INPUT ['refund_remark'];
	
	$model_oa_order_obj->update_order ( $update_data, $order_id );
	
	$ret = $model_oa_order_obj->order_refund ( $order_id );
	if ($ret == 1)
	{
		refund_oa($order_id);
		echo "<script>alert('退款成功');parent.location.href='list.php';</script>";
		exit ();
	}
	else
	{
		echo "<script>alert('退款失败，请联系管理员');</script>";
		exit ();
	}
}

$order_info = $model_oa_order_obj->get_order_info ( $order_id );

$order_info ['city_name'] = get_poco_location_name_by_location_id ( $order_info ['location_id'] );

$order_info ['total_price'] = $order_info ['hour'] * $order_info ['budget'];

$tpl->assign ( 'order', $order_info );
$tpl->assign ( 'order_id', $order_id );

$tpl->output ();

function refund_oa($order_id)
{
	$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
	$oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
	$user_obj = POCO::singleton ( 'pai_user_class' );
	
	$order_info = $oa_order_obj->get_order_info($order_id);
	
	$cellphone =  $order_info['cameraman_phone'];
	
	$user_id = $user_obj->get_user_id_by_phone($cellphone);
	
	if(!$user_id)
	{
		$user_info_arr ['pwd'] = "yueus123456";
		$user_info_arr ['cellphone'] = $cellphone;
		$user_info_arr ['nickname'] ="手机用户".substr($cellphone,-4);;
		$user_info_arr ['role'] = "cameraman";
		$user_id = $user_obj->create_account($user_info_arr, $err_msg);
	}
	
	$amount = $order_info['payable_amount'];
	
	$recharge_more_info = array ('third_code' => $order_info['pay_type'],
						"third_oid"=>$order_info ['running_number'],
						"ref_id"=>$order_id,
						"receive_time"=>$order_info ['pay_time'] ,
						"real_name"=>$order_info ['cameraman_realname'],
						"third_buyer"=>$order_info ['pay_account'],
						"remark"=>$order_info ['payment_remark'],
						"subject"=>"私人定制"
	);
	
	$pay_ret = $pai_payment_obj->manual_recharge ( 'recharge', $user_id, $amount, 0, '', $date_id, $recharge_more_info );
	
	$repay_more_info = array ('recharge_id' => $pay_ret['recharge_id'], //充值ID
						'third_code' => $order_info ['refund_type'], //支付方式 manual
						'third_oid' => $order_info ['refund_running_number'], //支付流水号
						'real_name' => $order_info ['refund_realname'], //真实姓名
						'third_buyer' => $order_info ['refund_account'], //账号
						'payment_no' => $pay_ret ['payment_no'],//充值支付号
						'remark' => $order_info ['refund_remark'] );//备注
	
	


	return $pai_payment_obj->manual_repay ( 'recharge', $user_id, $amount, $repay_more_info );
}

?>