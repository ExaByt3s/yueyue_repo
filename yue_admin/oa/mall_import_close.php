<?php

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$model_list_obj = POCO::singleton ( 'pai_model_oa_model_list_class' );
$order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$import_obj = POCO::singleton ( 'pai_oa_mall_import_order_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );


$order_list = $order_obj->get_order_list ( false, "order_status='close' and status=0", 'order_id ASC', '0,10' );

foreach($order_list as $val)
{
	$order_id = $val['order_id'];
	$model_list = $model_list_obj->get_model_list(false,"order_id={$order_id} and status=1","id DESC","0,1000");
	$count_model = count($model_list);
	foreach ($model_list as $model_val)
	{
		$style_arr = explode(",",$val['style']);
		
		$insert_data['model_nickname'] = get_user_nickname_by_user_id($model_val ['user_id'] );
		$insert_data['model_phone'] = $user_obj->get_phone_by_user_id($model_val ['user_id']);
		$insert_data['cameraman_nickname'] = $val['cameraman_nickname'];
		$insert_data['cameraman_phone'] = $val['cameraman_phone'];
		
		//只有一个合适的模特时候钱就用应付金额  ,多个的时候就用模特的拍摄价格
		if($count_model>1)
		{
			$insert_data['price'] = $model_val['price'];
		}
		else
		{
			$insert_data['price'] = $val['payable_amount'];
		}
		$insert_data['service_address'] = $val['date_address'];
		$insert_data['service_location_id'] = $val['location_id'];
		$insert_data['service_time'] = $val['date_time'];
		$insert_data['service_id'] = $val['service_id'];
		$insert_data['seller_overall_score'] = $model_val ['model_overall_score'];
		$insert_data['seller_match_score'] = $model_val ['match_score'];
		$insert_data['seller_quality_score'] = $model_val ['quality_score'];
		$insert_data['seller_comment'] = $model_val ['model_comment'];
		$insert_data['buyer_overall_score'] = $model_val ['cameraman_overall_score'];
		$insert_data['buyer_comment'] = $model_val ['cameraman_comment'];
		$insert_data['oa_order_id'] = $order_id;
		$insert_data['running_number'] = $val['running_number'];
		$insert_data['pay_type'] = $val['pay_type'];
		$insert_data['pay_time'] = $val['pay_time'];
        $insert_data['type_id'] = $model_val['type_id'];
		$insert_data['buyer_realname'] = $val['cameraman_realname'];
		$insert_data['pay_account'] = $val['pay_account'];
		$insert_data['payment_remark'] = $val['payment_remark'];
		
		$import_obj->add_order($insert_data);
		
		unset($insert_data);
	}
	
	$order_obj->update_order(array("status"=>1), $order_id);
}



?>