<?php
ignore_user_abort(true);
set_time_limit(0);
//define('G_PAI_ECPAY_DEV', 1);
define ( 'YUE_OA_IMPORT_ORDER', 1 );

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_obj = POCO::singleton ( 'pai_user_class' );
$import_order_obj = POCO::singleton ( 'pai_oa_mall_import_order_class' );
$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
$ecpay_pai_withdraw_obj = POCO::singleton ( 'ecpay_pai_withdraw_class' );
$mall_comment_obj = POCO::singleton ( 'pai_mall_comment_class' );
$mall_order_obj = POCO::singleton ( 'pai_mall_order_class' );
$relate_org_obj = POCO::singleton('pai_model_relate_org_class');
$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

$order_list = $import_order_obj->get_order_list ( false, 'status=0', 'id ASC', '0,1' );

if (! $order_list)
{
	die ( "没任务了" );
}
 

$order_info = $order_list [0];
$id = $order_info ['id'];
$oa_order_id = $order_info ['oa_order_id'];


//检查财务应付金额是否与给商家金额一致
$oa_order_info = $model_oa_order_obj->get_order_info($oa_order_id);
$payable_amount = $oa_order_info['payable_amount'];

$import_seller_list = $import_order_obj->get_order_list ( false, "oa_order_id={$oa_order_id}", 'id ASC', '0,1000','sum(price) as sum' );

if($import_seller_list[0]['sum']!=$payable_amount)
{
    //echo "<script>alert('商家应付金额与财务应付金额不符，请重新修改');</script>";
    $import_order_obj->update_order ( array ("log" => "商家应付金额与财务应付金额不符，请重新修改" ), $id );
    exit;
}


//正在执行的更新状态为2，
$import_order_obj->update_order ( array ("status" => 2 ), $id );

$check_model_phone = $user_obj->check_cellphone_exist ( $order_info ['model_phone'] );

if (! $check_model_phone)
{
	$model_reg_data ['nickname'] = $order_info ['model_nickname'];
	$model_reg_data ['cellphone'] = $order_info ['model_phone'];
	$model_reg_data ['pwd'] = 'yueus123456';
	$model_reg_data ['role'] = "cameraman";
	
	$seller_user_id = $user_obj->create_mall_account ( $model_reg_data, $err_msg );
	$log .= "创建商家成功:" . $seller_user_id . "\n";

}
else
{
	$seller_user_id = $user_obj->get_user_id_by_phone ( $order_info ['model_phone'] );
}
 
$check_cameraman_phone = $user_obj->check_cellphone_exist ( $order_info ['cameraman_phone'] );

if (! $check_cameraman_phone)
{
	$cameraman_reg_data ['nickname'] = $order_info ['cameraman_nickname'];
	$cameraman_reg_data ['cellphone'] = $order_info ['cameraman_phone'];
	$cameraman_reg_data ['pwd'] = 'yueus123456';
	$cameraman_reg_data ['role'] = "cameraman";
	
	$buyer_user_id = $user_obj->create_mall_account ( $cameraman_reg_data, $err_msg );
	$log .= "创建消费者成功:" . $buyer_user_id . "\n";

}
else
{
	$buyer_user_id = $user_obj->get_user_id_by_phone ( $order_info ['cameraman_phone'] );
}


$org_info = $relate_org_obj->get_org_info_by_user_id($seller_user_id);
$org_user_id = intval($org_info['org_id']);

if($org_user_id && $order_info ['withdraw']==1)
{
	$log .= "不允许机构商家线下提现:" . $org_user_id . "\n";
	$import_order_obj->update_order ( array ("log" => $log ), $id );
	exit;
}

if($order_info['type_id'])
{
    $goods_type_arr = array("31"=>52,"12"=>54,"5"=>56,"3"=>51,"40"=>53);
    $goods_id = $goods_type_arr[$order_info['type_id']];
}
else
{
    $goods_type_arr = array("0"=>52,"1"=>54,"2"=>56,"3"=>51,"7"=>52);
    $goods_id = $goods_type_arr[$order_info['service_id']];
}


 $detail_list = array( array(
  	'goods_id' => $goods_id, //商品ID
  	'prices_type_id' => '',
  	'service_time' => strtotime($order_info['service_time']), //服务时间
  	'service_location_id' => $order_info['service_location_id'],
  	'service_address' => $order_info['service_address'],
   'service_people' => 1,
   'prices' => $order_info['price'], //单价，特殊服务必填，正常服务忽略
  	'quantity' => 1, //数量
  ));
  
 $more_info = array( 
  	'seller_user_id' => $seller_user_id, //卖家用户ID，特殊服务必填，正常服务忽略
  	'description' => '', //描述、备注
   'is_auto_accept' => 1, //是否自动接受，下单、支付、接受不发送通知
   'is_auto_sign' => 1, //是否自动签到，签到、评价不发送通知
   'referer' => 'oa', //订单来源，app weixin pc wap oa
  );
	  
$mall_order_ret = $mall_order_obj->submit_order($buyer_user_id, $detail_list, $more_info);

$mall_order_id = $mall_order_ret['order_id'];
if($mall_order_id)
{
    $log .= "商城订单ID:" . $mall_order_id . "\n";
}
else
{
    $log .= "商城订单ID:" . $mall_order_ret['message'] . "\n";
}


$import_order_obj->update_order ( array ("log" => $log ), $id );

if($buyer_user_id==100000)
{
    exit;
}

if($mall_order_id)
{
	$recharge_type = 'mall_order';
	$amount = $order_info ['price'];
	$more_info = array ('third_code' => $order_info['pay_type'],
						"third_oid"=>$order_info ['running_number'],
						"ref_id"=>$oa_order_id,
						"receive_time"=>$order_info ['pay_time'],
						"real_name"=>$order_info ['buyer_realname'],
						"third_buyer"=>$order_info ['pay_account'],
						"remark"=>$order_info ['payment_remark'],
						"subject"=>"私人定制"
	);
	
	//充值
	$pay_ret = $pai_payment_obj->manual_recharge ( $recharge_type, $buyer_user_id, $amount, $mall_order_id, $mall_order_id, 0, $more_info );
	$log .= "充值状态:" . $pay_ret['message'] . "\n";
	$import_order_obj->update_order ( array ("log" => $log ), $id );
	
	
	//充值成功
	if ($pay_ret ['error'] == 0)
	{
		//支付
		$payment_info = $pai_payment_obj->get_payment_info($pay_ret['payment_no']);
		$p_ret = $mall_order_obj->pay_order_by_payment_info($payment_info);
		$log .= "支付状态:" . $p_ret['message'] . "\n";
		$import_order_obj->update_order ( array ("log" => $log ), $id );
		
		//支付成功
		if($p_ret['result']==1)
		{
			//评价商家
			$seller_comment_data ['from_user_id'] = $buyer_user_id;
			$seller_comment_data ['to_user_id'] = $seller_user_id;
			$seller_comment_data ['order_id'] = $mall_order_id;
			$seller_comment_data ['goods_id'] = $goods_id;
			$seller_comment_data ['overall_score'] = $order_info ['seller_overall_score'];
			$seller_comment_data ['match_score'] = $order_info ['seller_match_score'];
			$seller_comment_data ['manner_score'] = $order_info ['seller_manner_score'];
			$seller_comment_data ['quality_score'] = $order_info ['seller_quality_score'];
			$seller_comment_data ['comment'] = $order_info ['seller_comment'];
			$seller_comment_ret = $mall_comment_obj->add_seller_comment($seller_comment_data);
			$log .= "评价商家:" . $seller_comment_ret['message'] . "\n";
			$import_order_obj->update_order ( array ("log" => $log ), $id );
			
			//评价消费者
			$buyer_comment_data ['from_user_id'] = $seller_user_id;
			$buyer_comment_data ['to_user_id'] = $buyer_user_id;
			$buyer_comment_data ['order_id'] = $mall_order_id;
			$buyer_comment_data ['goods_id'] = $goods_id;
			$buyer_comment_data ['overall_score'] = $order_info ['buyer_overall_score'];
			$buyer_comment_data ['comment'] = $order_info ['buyer_comment'];
			$buyer_comment_ret = $mall_comment_obj->add_buyer_comment($buyer_comment_data);
			$log .= "评价消费者:" . $buyer_comment_ret['message'] . "\n";
			$import_order_obj->update_order ( array ("log" => $log ), $id );
			
			if($order_info ['withdraw']==1)
			{
				$withdraw_id = $pai_payment_obj->submit_withdraw ( 'withdraw', $seller_user_id, $amount, "线下支付", 'manual', "OA单号{$oa_order_id}" );
				$withdraw_ret = $ecpay_pai_withdraw_obj->check_apply ( $withdraw_id, "线下支付 OA单号{$oa_order_id}" );
				$log .= "提现状态:" . $withdraw_ret;
			}
		}
		
		if ($id)
		{	
			//完成导入更新状态为1
			$time = time();
			$import_order_obj->update_order ( array ("log" => $log, "status" => 1,"mall_order_id"=>$mall_order_id,"import_time"=>$time ), $id );
		}
	}

}



echo $log;

?>