<?php

//引入类
//include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
//include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

function add_event_date($date_data)
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	
	$add_ret = $date_obj->add_event_date ( $date_data );
	return $add_ret;

}

/**
 * 约拍提交处理  modify hai 20140911
 * @param array $date_data 
 * array( 
 * 'from_date_id'=>'', //摄影师ID 调用时以$yue_login_id 赋值
 * 'to_date_id'=>'',   //模特ID
 * 'date_status'=>'',  //状态     传入wait
 * 'date_time'=>'',    //约拍时间
 * 'date_type'=>'',    //拍摄类型
 * 'date_style'=>'',   //拍摄风格
 * 'date_hour'=>'',    //拍摄时长
 * 'date_price'=>'',   //出价
 * 'date_address'=>''  //地址
 *
 *)   
 * @param int   $user_balance 用户余额  用于判断用户是否停留页面太长时间没提交  而用户余额变动后再提交
 * @param int   $is_available_balance   0为第三方支付 1为余额支付   如果余额不够支付将需要继续跳转到第三方继续支付
 * @param string $third_code   第三方支付的标识 现暂时支持微信和支付宝钱包 alipay_purse、tenpay_wxapp 当用户使用余额全额支付时可为空
 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * 返回值 status_code 为是否错误  status_code -1 参数错误 -2用户余额有变动   -3添加到约拍表时产生错误  -4为生成第三方请求参数产生错误。
 * 1为余额支付成功   2为生成请求参数成功，待跳转到第三方。
 * message返回的消息 cur_balance 返回用户当前真实余额[当status_code==2 或余额支付成功才有此key] request_data 第三方发起请求的字符串[需要发起请求时候才返回]
 *
 */
function add_event_date_op($date_data, $user_balance, $is_available_balance, $third_code, $redirect_url)
{
	
	$date_data ['from_date_id'] = intval ( $date_data ['from_date_id'] );
	if (empty ( $date_data ['from_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'from_date_id 格式错误' );
		return $ret;
	
	}
	$date_data ['to_date_id'] = intval ( $date_data ['to_date_id'] );
	if (empty ( $date_data ['to_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'to_date_id 格式错误' );
		return $ret;
	}
	/*
	$date_data['date_time'] = intval($date_data['date_time']);
    if(empty($date_data['date_time']))
    {
		$ret = array( 'status_code'=>-1,'message'=>'date_time 格式错误' );
		return $ret;
    }*/
	
	$date_data ['date_price'] = floatval ( $date_data ['date_price'] );
	
	$total_price = $date_data ['date_price'] * $date_data ['date_hour'];
	
	if (empty ( $date_data ['date_price'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'date_price 格式错误' );
		return $ret;
	
	}
	
	if ($total_price < 10 || $total_price > 10000)
	{
		$ret = array ('status_code' => - 1, 'message' => '约拍价格要在10-10000之内' );
		return $ret;
	
	}
	
	$date_data ['date_hour'] = intval ( $date_data ['date_hour'] );
	if (empty ( $date_data ['date_hour'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'date_hour 格式错误' );
		return $ret;
	
	}
	if (! in_array ( $is_available_balance, array (0, 1 ) ))
	{
		
		$ret = array ('status_code' => - 1, 'message' => 'is_available_balance 格式错误' );
		return $ret;
	
	}
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$user_info = $user_obj->get_user_info_by_user_id ( $date_data ['from_date_id'] );
	
	$account_info = $payment_obj->get_user_account_info ( $date_data ['from_date_id'] );
	if ($account_info ['available_balance'] != $user_balance)
	{
		
		$ret = array ('status_code' => - 2, 'message' => '用户余额有变动', 'user_info' => $user_info );
		return $ret;
	
	}
	$date_id = add_event_date ( $date_data );
	if ($date_id < 1)
	{
		
		$ret = array ('status_code' => - 3, 'message' => '添加到约拍产生错误' );
		return $ret;
	
	}
	$account_info ['user_name'] = $user_info ['nickname'];
	$total_price = $date_data ['date_price'] * $date_data ['date_hour'];
	
	//用余额支付
	if ($is_available_balance)
	{
		
		if ($account_info ['available_balance'] < $total_price)
		{
			//余额不足，跳转去充值
			$amount = $total_price - $account_info ['available_balance'];
			$redirect_third = 1;
			//$amount = bcsub($sum_cost, $available_balance, 2);
		

		}
		else
		{
			
			update_event_date_pay_status ( $date_id, $status = '1' );
			$ret = array ('status_code' => 1, 'message' => '余额支付成功！', 'user_info' => $user_info );
			return $ret;
		
		}
	
	}
	else
	{
		//直接用支付宝支付
		$amount = $total_price;
		$redirect_third = 1;
	
	}
	if ($redirect_third)
	{
		
		if (! in_array ( $third_code, array ('alipay_purse', 'tenpay_wxapp', 'tenpay_wxpub' ) ))
		{
			
			$ret = array ('status_code' => - 1, 'message' => 'third_code 支付标识错误！' );
			return $ret;
		
		}
		$more_info ['channel_return'] = $redirect_url;
		$recharge_ret = $payment_obj->submit_recharge ( 'date', $date_data ['from_date_id'], $amount, $third_code, 0, '', $date_id, $more_info );
		if ($recharge_ret ['error'] === 0)
		{
			$payment_no = trim ( $recharge_ret ['payment_no'] );
			$request_data = trim ( $recharge_ret ['request_data'] );
			$ret = array ('status_code' => 2, 'message' => '添加约拍成功,需跳转到第三方支付。', 'request_data' => $request_data, 'payment_no' => $payment_no );
			return $ret;
		
		}
		else
		{
			$ret = array ('status_code' => - 4, 'message' => '跳转到第三方支付产生错误  详细信息见recharge_ret', 'recharge_ret' => $recharge_ret );
			return $ret;
		}
	
	}
}

function add_event_date_op_v2($date_data, $user_balance, $is_available_balance, $third_code, $redirect_url, $notify_url = '',$coupon_sn='')
{
	
	$date_data ['from_date_id'] = intval ( $date_data ['from_date_id'] );
	if (empty ( $date_data ['from_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'from_date_id 格式错误' );
		return $ret;
	
	}
	$date_data ['to_date_id'] = intval ( $date_data ['to_date_id'] );
	if (empty ( $date_data ['to_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'to_date_id 格式错误' );
		return $ret;
	}
	
	$date_data['date_time'] = intval($date_data['date_time']);
    if(empty($date_data['date_time']))
    {
		$ret = array( 'status_code'=>-1,'message'=>'请选择约拍时间' );
		return $ret;
    }
	
	$date_data ['date_price'] = floatval ( $date_data ['date_price'] );
	$date_data ['date_hour'] = 1;
	if (empty ( $date_data ['date_price'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'date_price 格式错误' );
		return $ret;
	
	}
	
	if ( $date_data ['date_price'] > 10000)
	{
		$ret = array ('status_code' => - 1, 'message' => '约拍价格要在10000之内' );
		return $ret;
	
	}
	
	if (! in_array ( $is_available_balance, array (0, 1 ) ))
	{
		
		$ret = array ('status_code' => - 1, 'message' => 'is_available_balance 格式错误' );
		return $ret;
	
	}
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
	$coupon_obj = POCO::singleton('pai_coupon_class');
	
	$user_info = $user_obj->get_user_info_by_user_id ( $date_data ['from_date_id'] );
	
	if($user_info['role']!='cameraman')
	{
		$ret = array ('status_code' => - 1, 'message' => '只有摄影师才能约拍模特' );
		return $ret;
	}
	
	
	$account_info = $payment_obj->get_user_account_info ( $date_data ['from_date_id'] );
	if ($account_info ['available_balance'] != $user_balance)
	{
		
		$ret = array ('status_code' => - 2, 'message' => '用户余额有变动', 'user_info' => $user_info );
		return $ret;
	
	}
	
	//解决IOS空格乱码
	$data['date_address'] = str_replace("\u2006","",$data['date_address']);
	
	$org_info = $relate_org_obj->get_org_info_by_user_id($date_data['to_date_id']);
	
	$date_data['org_user_id'] = (int)$org_info['org_id'];
	
	$date_id = add_event_date ( $date_data );
	if ($date_id < 1)
	{
		
		$ret = array ('status_code' => - 3, 'message' => '添加到约拍产生错误' );
		return $ret;
	
	}
	$account_info ['user_name'] = $user_info ['nickname'];
	$total_price = $date_data ['date_price'];
	
	
	//优惠券处理
	
	$channel_module = "yuepai";
	$coupon_obj->not_use_coupon_by_oid($channel_module, $date_id);
	
	if($coupon_sn)
	{
		$param_info = array(
		   'module_type'=>$channel_module, //模块类型 waipai yuepai topic
		   'order_total_amount'=>$date_data ['date_price'], //订单总金额
		   'model_user_id'=>$date_data ['to_date_id'], //模特用户ID（约拍、专题）
		   'org_user_id'=>$date_data['org_user_id'], //机构用户ID
		   'mall_type_id' => 31, //服务品类
		 );
		$coupon_ret = $coupon_obj->use_coupon($date_data ['from_date_id'], 1, $coupon_sn, $channel_module, $date_id, $param_info);
		if($coupon_ret['result']!=1)
		{
			$ret = array ('status_code' => - 1, 'message' => $coupon_ret['message'] );
			return $ret;
		}
		
		$total_price = $date_data ['date_price']-$coupon_ret['used_amount'];
		if($total_price<=0)
		{
			$ret = array ('status_code' => - 1, 'message' => '优惠金额有误' );
			return $ret;
		}
		
		$udpate_date['discount_price'] = $coupon_ret['used_amount'];
		$udpate_date['is_use_coupon'] = 1;
		update_event_date($udpate_date, $date_id);
	}
	
	//用余额支付
	if ($is_available_balance)
	{
		
		if ($account_info ['available_balance'] < $total_price)
		{
			//余额不足，跳转去充值
			$amount = $total_price - $account_info ['available_balance'];
			$redirect_third = 1;
			//$amount = bcsub($sum_cost, $available_balance, 2);
		

		}
		else
		{
			
			update_event_date_pay_status ( $date_id, $status = '1');
			$ret = array ('status_code' => 1, 'message' => '余额支付成功！', 'user_info' => $user_info );
			return $ret;
		
		}
	
	}
	else
	{
		//直接用支付宝支付
		$amount = $total_price;
		$redirect_third = 1;
	
	}
	if ($redirect_third)
	{
		
		if (! in_array ( $third_code, array ('alipay_purse', 'tenpay_wxapp', 'tenpay_wxpub' ) ))
		{
			
			$ret = array ('status_code' => - 1, 'message' => 'third_code 支付标识错误' );
			return $ret;
		
		}
		$openid = '';
		if ($third_code == 'tenpay_wxpub')
		{
			
			$bind_weixin_obj = POCO::singleton ( 'pai_bind_weixin_class' );
			$bind_info = $bind_weixin_obj->get_bind_info_by_user_id ( $date_data ['from_date_id'] );
			$openid = $bind_info ['open_id'];
			if (empty ( $openid ))
			{
				
				$ret = array ('status_code' => - 1, 'message' => '微信用户没绑定约约账号' );
				return $ret;
			
			}
		
		}
		$more_info ['channel_return'] = $redirect_url;
		$more_info ['channel_notify'] = $notify_url;
		$more_info ['openid'] = $openid;
		$recharge_ret = $payment_obj->submit_recharge ( 'date', $date_data ['from_date_id'], $amount, $third_code, 0, '', $date_id, $more_info );
		if ($recharge_ret ['error'] === 0)
		{
			$payment_no = trim ( $recharge_ret ['payment_no'] );
			$request_data = trim ( $recharge_ret ['request_data'] );
			$ret = array ('status_code' => 2, 'message' => '添加约拍成功,需跳转到第三方支付。', 'request_data' => $request_data, 'payment_no' => $payment_no );
			return $ret;
		
		}
		else
		{
			$ret = array ('status_code' => - 4, 'message' => '跳转到第三方支付产生错误  详细信息见recharge_ret', 'recharge_ret' => $recharge_ret );
			return $ret;
		}
	
	}
}

/*
 * 添加准备约拍记录
 */
function add_date_op($date_data)
{
	$date_data ['from_date_id'] = intval ( $date_data ['from_date_id'] );
	if (empty ( $date_data ['from_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'from_date_id 格式错误' );
		return $ret;
	
	}
	$date_data ['to_date_id'] = intval ( $date_data ['to_date_id'] );
	if (empty ( $date_data ['to_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'to_date_id 格式错误' );
		return $ret;
	}
	
	$date_data['date_time'] = intval($date_data['date_time']);
    if(empty($date_data['date_time']))
    {
		$ret = array( 'status_code'=>-1,'message'=>'请选择约拍时间' );
		return $ret;
    }
	
	$date_data ['date_price'] = floatval ( $date_data ['date_price'] );
	$date_data ['date_hour'] = 1;
	if (empty ( $date_data ['date_price'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'date_price 格式错误' );
		return $ret;
	
	}
	
	if ( $date_data ['date_price'] > 30000)
	{
		$ret = array ('status_code' => - 1, 'message' => '约拍价格要在30000之内' );
		return $ret;
	
	}
	
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
	$coupon_obj = POCO::singleton('pai_coupon_class');
	
	$user_info = $user_obj->get_user_info_by_user_id ( $date_data ['from_date_id'] );
	
	if($user_info['role']!='cameraman')
	{
		$ret = array ('status_code' => - 1, 'message' => '只有摄影师才能约拍模特' );
		return $ret;
	}
	
	
	$org_info = $relate_org_obj->get_org_info_by_user_id($date_data['to_date_id']);
	
	$date_data['org_user_id'] = (int)$org_info['org_id'];

	$date_id = add_event_date ( $date_data );
	
	if ($date_id < 1)
	{
		$ret = array ('status_code' => - 1, 'message' => '添加约拍错误' );
		return $ret;
	
	}
	else 
	{
		$ret = array ('status_code' => 1, 'message' => '添加约拍成功','date_id'=>$date_id );
		return $ret;
	}
}


function update_date_op($date_id, $user_balance, $is_available_balance, $third_code, $redirect_url, $notify_url = '',$coupon_sn='')
{
	$date_id = (int)$date_id;
	
	global $yue_login_id;
	

	if ($date_id < 1)
	{
		$ret = array ('status_code' => - 3, 'message' => 'date_id错误' );
		return $ret;	
	}
	
	$date_info = get_date_info($date_id);
	
	if($date_info ['from_date_id']!=$yue_login_id)
	{
		$ret = array ('status_code' => - 1, 'message' => '用户ID异常' );
		return $ret;
	}
	
	if (! in_array ( $is_available_balance, array (0, 1 ) ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'is_available_balance 格式错误' );
		return $ret;
	}
	
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$coupon_obj = POCO::singleton('pai_coupon_class');
	$user_obj = POCO::singleton ( 'pai_user_class' );
	
	$user_info = $user_obj->get_user_info_by_user_id ( $date_info ['from_date_id'] );
	$account_info = $payment_obj->get_user_account_info ( $date_info ['from_date_id'] );
	if ($account_info ['available_balance'] != $user_balance)
	{
		$ret = array ('status_code' => - 2, 'message' => '用户余额有变动', 'user_info' => $user_info );
		return $ret;	
	}
	
	
	$total_price = $date_info ['date_price'];
	
	//优惠券处理
	$channel_module = "yuepai";
	$coupon_obj->not_use_coupon_by_oid($channel_module, $date_id);
	
	//先更新为0
	$c_udpate_date['discount_price'] = 0;
	$c_udpate_date['is_use_coupon'] = 0;
	update_event_date($c_udpate_date, $date_id);
	
	if($coupon_sn)
	{
		$param_info = array(
		   'module_type'=>$channel_module, //模块类型 waipai yuepai topic
		   'order_total_amount'=>$date_info ['date_price'], //订单总金额
		   'model_user_id'=>$date_info ['to_date_id'], //模特用户ID（约拍、专题）
		   'org_user_id'=>$date_info['org_user_id'], //机构用户ID
		   'mall_type_id' => 31, //服务品类
		 );
		$coupon_ret = $coupon_obj->use_coupon($date_info ['from_date_id'], 1, $coupon_sn, $channel_module, $date_id, $param_info);
		if($coupon_ret['result']!=1)
		{
			$ret = array ('status_code' => - 1, 'message' => $coupon_ret['message'] );
			return $ret;
		}
		
		$total_price = $date_info ['date_price']-$coupon_ret['used_amount'];
		if($total_price<=0)
		{
			$ret = array ('status_code' => - 1, 'message' => '优惠金额有误' );
			return $ret;
		}
		
		$udpate_date['discount_price'] = $coupon_ret['used_amount'];
		$udpate_date['is_use_coupon'] = 1;
		update_event_date($udpate_date, $date_id);
	}
	
	//用余额支付
	if ($is_available_balance)
	{
		
		if ($account_info ['available_balance'] < $total_price)
		{
			//余额不足，跳转去充值
			$amount = $total_price - $account_info ['available_balance'];
			$redirect_third = 1;
			//$amount = bcsub($sum_cost, $available_balance, 2);
		

		}
		else
		{
			
			update_event_date_pay_status ( $date_id, $status = '1');
			$ret = array ('status_code' => 1, 'message' => '余额支付成功！', 'user_info' => $user_info );
			return $ret;
		
		}
	
	}
	else
	{
		//直接用支付宝支付
		$amount = $total_price;
		$redirect_third = 1;
	
	}
	if ($redirect_third)
	{
		
		if (! in_array ( $third_code, array ('alipay_purse', 'tenpay_wxapp', 'tenpay_wxpub' ) ))
		{
			
			$ret = array ('status_code' => - 1, 'message' => 'third_code 支付标识错误' );
			return $ret;
		
		}
		$openid = '';
		if ($third_code == 'tenpay_wxpub')
		{
			
			$bind_weixin_obj = POCO::singleton ( 'pai_bind_weixin_class' );
			$bind_info = $bind_weixin_obj->get_bind_info_by_user_id ( $date_info ['from_date_id'] );
			$openid = $bind_info ['open_id'];
			if (empty ( $openid ))
			{
				
				$ret = array ('status_code' => - 1, 'message' => '微信用户没绑定约约账号' );
				return $ret;
			
			}
		
		}
		$more_info ['channel_return'] = $redirect_url;
		$more_info ['channel_notify'] = $notify_url;
		$more_info ['openid'] = $openid;
		$recharge_ret = $payment_obj->submit_recharge ( 'date', $date_info ['from_date_id'], $amount, $third_code, 0, '', $date_id, $more_info );
		if ($recharge_ret ['error'] === 0)
		{
			$payment_no = trim ( $recharge_ret ['payment_no'] );
			$request_data = trim ( $recharge_ret ['request_data'] );
			$ret = array ('status_code' => 2, 'message' => '添加约拍成功,需跳转到第三方支付。', 'request_data' => $request_data, 'payment_no' => $payment_no );
			return $ret;
		
		}
		else
		{
			$ret = array ('status_code' => - 4, 'message' => '跳转到第三方支付产生错误  详细信息见recharge_ret', 'recharge_ret' => $recharge_ret );
			return $ret;
		}
	
	}
}

/*
 * 模特约拍状态列表
 * @param $model_user_id 模特ID
 * @param $status 考虑中consider  已同意confirm
 * @param $b_select_count
 * @param $limit
 */
function get_model_status_date_list($model_user_id, $status = 'consider', $b_select_count = false, $limit = '0,10')
{
	$model_user_id = ( int ) $model_user_id;
	
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$cameraman_comment_obj = POCO::singleton ( 'pai_cameraman_comment_log_class' );
	$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	
	if ($status == 'consider')
	{
		$where_str = "to_date_id={$model_user_id} and date_status in ('wait','cancel','refund') and pay_status=1";
	}
	elseif ($status == 'confirm')
	{
		$where_str = "to_date_id={$model_user_id} and date_status in ('confirm','cancel_date') and pay_status=1";
	}
	
	$list = get_all_event_date ( $b_select_count, $where_str, 'date_id DESC', $limit, '*' );
	
	foreach ( $list as $k => $value )
	{
		$list [$k] ['icon'] = get_user_icon ( $value ['from_date_id'], 165 );
		$list [$k] ['nickname'] = get_user_nickname_by_user_id ( $value ['from_date_id'] );
		$list [$k] ['sum_price'] = $value ['date_price'] * $value ['date_hour'];
		$list [$k] ['date_time'] = date ( "Y-m-d H:i", $value ['date_time'] );
		
		$list [$k] ['total_price'] = $value ['date_price'] * $value ['date_hour'];
		$list [$k] ['total_price_v2'] = $value ['date_price'] . "(" . $value ['hour'] . "小时)";
		
		if ($status == 'confirm')
		{
			$event_info = $event_details_obj->get_event_by_event_id ( $value ['event_id'] );
			//判断是否活动已结束
			if ($event_info ['event_status'] != 0)
			{
				$list [$k] ['is_end'] = 1;
			}
			else
			{
				$list [$k] ['is_end'] = 0;
			}
			
			$check_canceling = $date_cancel_obj->is_canceling ( $value ['date_id'] );
			
			$event_status = $event_info ['event_status'];
			
			//显示lable状态
			if ($check_canceling && $event_status == 0)
			{
				$lable = "正在取消";
				$event_ready = 1;
				$event_finish = 0;
				$event_cancel = 0;
			}
			elseif ($event_status == 3)
			{
				$lable = "已取消";
				$event_ready = 0;
				$event_finish = 0;
				$event_cancel = 1;
			}
			elseif ($event_status == 2)
			{
				$lable = "已完成";
				$event_ready = 0;
				$event_finish = 1;
				$event_cancel = 0;
			}
			elseif ($event_status == 0)
			{
				$lable = "";
				$event_ready = 1;
				$event_finish = 0;
				$event_cancel = 0;
			}
			
			$list [$k] ['lable'] = $lable;
			$list [$k] ['event_ready'] = $event_ready;
			$list [$k] ['event_finish'] = $event_finish;
			$list [$k] ['event_cancel'] = $event_cancel;
			
			//活动是否有至少一个报名者扫码
			$check_scan = $activity_code_obj->check_event_code_scan ( $event_info ['event_id'] );
			
			if ($check_scan)
			{

				$list [$k] ['is_scan'] = 1;
				
			}
			else
			{
				$list [$k] ['is_scan'] = 0;
			}
			
			$cameraman_comment = $cameraman_comment_obj->get_cameraman_comment_by_date_id ( $value ['date_id'] );
			
			// 评分星星
			$has_star = intval ( $cameraman_comment ['overall_score'] );
			$miss_star = 5 - $has_star;
			
			for($i = 0; $i < 5; $i ++)
			{
				if ($has_star > 0)
				{
					$list [$k] ['stars_list'] [$i] ['is_red'] = 1;
					
					$has_star --;
				}
				else
				{
					$list [$k] ['stars_list'] [$i] ['is_red'] = 0;
					
					$miss_star --;
				}
			}
			
			//模特是否已评价
			$check_comment = $cameraman_comment_obj->is_comment_by_model ( $value ['date_id'], $model_user_id );
			
			if ($check_comment)
			{
				$list [$k] ['is_comment'] = 1;
			}
			else
			{
				$list [$k] ['is_comment'] = 0;
			}
		
		}
		else
		{
			if ($value ['date_status'] == 'wait' && $value ['pay_status'] == 1)
			{
				$lable = "NEW";
			}
			elseif ($value ['date_status'] == 'cancel')
			{
				$lable = "已拒绝";
			}
			elseif ($value ['date_status'] == 'refund')
			{
				$lable = "已退款";
			}
			else
			{
				$lable = "";
			}
			$list [$k] ['lable'] = $lable;
			$list [$k] ['is_end'] = 1;
		}
		
		$list [$k] ['role_text'] = "【约拍】摄影师";
	
	}
	
	return $list;
}

/*
 * 摄影师约拍状态列表
 * @param $cameraman_user_id 摄影师ID
 * @param $status 考虑中consider  已同意confirm
 * @param $b_select_count
 * @param $limit
 */
function get_cameraman_status_date_list($cameraman_user_id, $status = 'consider', $b_select_count = false, $limit = '0,10')
{
	$cameraman_user_id = ( int ) $cameraman_user_id;
	
	$model_comment_obj = POCO::singleton ( 'pai_model_comment_log_class' );
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	
	if ($status == 'consider')
	{
		$where_str = "from_date_id={$cameraman_user_id} and date_status in ('wait','cancel','refund') and pay_status=1";
	}
	elseif ($status == 'confirm')
	{
		$where_str = "from_date_id={$cameraman_user_id} and date_status in ('confirm','cancel_date') and pay_status=1";
	}
	
	$list = get_all_event_date ( $b_select_count, $where_str, 'date_id DESC', $limit, '*' );
	
	foreach ( $list as $k => $value )
	{
		$user_info = '';
		$list [$k] ['icon'] = get_user_icon ( $value ['to_date_id'], 165 );
		$list [$k] ['nickname'] = get_user_nickname_by_user_id ( $value ['to_date_id'] );
		$list [$k] ['date_time'] = date ( "Y-m-d H:i", $value ['date_time'] );
		
		$list [$k] ['total_price'] = $value ['date_price'] * $value ['date_hour'];
		$list [$k] ['total_price_v2'] = $value ['date_price'] . "(" . $value ['hour'] . "小时)";
		
		if ($status == 'confirm')
		{
			$event_info = $event_details_obj->get_event_by_event_id ( $value ['event_id'] );
			//判断是否活动已结束
			if ($event_info ['event_status'] != 0)
			{
				$list [$k] ['is_end'] = 1;
			}
			else
			{
				$list [$k] ['is_end'] = 0;
			}
			
			//活动是否有至少一个报名者扫码
			$check_scan = $activity_code_obj->check_event_code_scan ( $event_info ['event_id'] );
			if ($check_scan)
			{
				$list [$k] ['is_scan'] = 1;
			}
			else
			{
				$list [$k] ['is_scan'] = 0;
			}
			
			$check_canceling = $date_cancel_obj->is_canceling ( $value ['date_id'] );
			
			$event_status = ( int ) $event_info ['event_status'];
			
			//显示lable状态
			if ($check_canceling && $event_status == 0)
			{
				$lable = "正在取消";
				$event_ready = 1;
				$event_finish = 0;
				$event_cancel = 0;
			}
			elseif ($event_status == 3)
			{
				$lable = "已取消";
				$event_ready = 0;
				$event_finish = 0;
				$event_cancel = 1;
			}
			elseif ($event_status == 2)
			{
				$lable = "已完成";
				$event_ready = 0;
				$event_finish = 1;
				$event_cancel = 0;
			}
			elseif ($event_status == 0)
			{
				$lable = "";
				$event_ready = 1;
				$event_finish = 0;
				$event_cancel = 0;
			}
			
			$list [$k] ['lable'] = $lable;
			$list [$k] ['event_ready'] = $event_ready;
			$list [$k] ['event_finish'] = $event_finish;
			$list [$k] ['event_cancel'] = $event_cancel;
			
			$model_comment = $model_comment_obj->get_model_comment_by_date_id ( $value ['date_id'] );
			
			// 评分星星
			$has_star = intval ( $model_comment['overall_score'] );
			$miss_star = 5 - $has_star;
			
			for($i = 0; $i < 5; $i ++)
			{
				if ($has_star > 0)
				{
					$list [$k] ['stars_list'] [$i] ['is_red'] = 1;
					
					$has_star --;
				}
				else
				{
					$list [$k] ['stars_list'] [$i] ['is_red'] = 0;
					
					$miss_star --;
				}
			}
			
			// 摄影师是否已评价约拍
			$check_comment = $model_comment_obj->is_comment_by_cameraman ( $value ['date_id'], $cameraman_user_id );
			
			if ($check_comment)
			{
				$list [$k] ['is_comment'] = 1;
			}
			else
			{
				$list [$k] ['is_comment'] = 0;
			}
		
		}
		else
		{
			//$list[$k]['is_end'] = 1;
			if ($value ['date_status'] == 'cancel')
			{
				$lable = "已拒绝";
			}
			elseif ($value ['date_status'] == 'refund')
			{
				$lable = "已退款";
			}
			else
			{
				$lable = "";
			}
			
			$list [$k] ['event_status'] = 1;
			$list [$k] ['lable'] = $lable;
		}
		
		$list [$k] ['role_text'] = "模特";
	
	}
	
	return $list;
}

/*
 * 根据date_id取约拍信息
 * @param $date_id int
 */
function get_date_by_date_id($date_id)
{
	global $yue_login_id;
	
	$code_obj = POCO::singleton ( 'pai_activity_code_class' );
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$date_obj = POCO::singleton ( 'event_date_class' );
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	$date_cancel_log_obj = POCO::singleton ( 'event_date_cancel_log_class' );
	$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );
	$model_card_obj = POCO::singleton ( 'pai_model_card_class' );
	$model_comment_obj = POCO::singleton ( 'pai_model_comment_log_class' );
	$cameraman_comment_obj = POCO::singleton ( 'pai_cameraman_comment_log_class' );
	
	$date_arr = get_event_date ( "date_id", $date_id );
	$date_info = $date_arr [0];
	
	if ($date_info ['event_id'])
	{
		$event_info = get_event_by_event_id ( $date_info ['event_id'] );
		$date_info ['event_status'] = ( int ) $event_info ['event_status'];
	}
	//当前用户角色
	$role = $user_obj->check_role ( $yue_login_id );
	
	//$date_info ['date_id'] = date("Ymd",$date_info ['add_time']).$date_id;
	
	$date_info ['cameraman_nickname'] = get_user_nickname_by_user_id ( $date_info ['from_date_id'] );
	$date_info ['model_nickname'] = get_user_nickname_by_user_id ( $date_info ['to_date_id'] );
	
	$date_info ['date_time'] = date ( "Y-m-d H:i", $date_info ['date_time'] );
	
	$cameraman_user_info = $user_obj->get_user_info ( $date_info ['from_date_id'] );
	
	$date_info ['city_name'] = get_poco_location_name_by_location_id ( $cameraman_user_info ['location_id'] );
	
	$date_info ['total_price'] = $date_info ['date_price'] * $date_info ['date_hour'];
	$date_info ['total_price_v2'] = $date_info ['date_price'] . "(" . $date_info ['hour'] . "小时)";
	
	$date_info ['cameraman_user_icon'] = get_user_icon ( $date_info ['from_date_id'], 165 );
	$date_info ['model_user_icon'] = get_user_icon ( $date_info ['to_date_id'], 165 );
	
	if ($date_info ['event_id'] && $date_info ['enroll_id'])
	{
		$date_info ['qr_code'] = $code_obj->create_qr_code ( $date_info ['event_id'], $date_info ['enroll_id'] );
		
		$date_info ['code'] = $code_obj->get_code_by_enroll_id ( $date_info ['enroll_id'] );
	}

	
	if ($role == 'cameraman')
	{
		if ($date_info ['date_status'] == 'confirm' && $date_info ['pay_status'] == 1 && $event_info ['event_status'] === '0' && ! empty ( $date_info ['qr_code'] ))
		{
			$date_info ['show_code_button'] = 1; //出活动券按钮
		}
		else
		{
			$date_info ['show_code_button'] = 0;
		}
		
		//摄影师是否已评价
		$check_cameraman_comment = $model_comment_obj->is_comment_by_cameraman ( $date_id, $date_info ['from_date_id'] );
		
		if ($event_info ['event_status'] === '2' && ! $check_cameraman_comment)
		{
			$date_info ['show_comment_button'] = 1; //评论按钮
		}
		else
		{
			$date_info ['show_comment_button'] = 0;
		}
		
		$date_info ['show_accept_button'] = 0; //模特邀请接受按钮
		$date_info ['show_reject_button'] = 0; //模特拒绝按钮
		$date_info ['show_scan_button'] = 0; //扫描签到按钮
		$date_info ['show_event_finsh_button'] = 0; //活动确认按钮
	

	}
	elseif ($role == 'model')
	{
		
		if ($date_info ['date_status'] == 'wait' && $date_info ['pay_status'] == 1)
		{
			$date_info ['show_accept_button'] = 1;
			$date_info ['show_reject_button'] = 1;
		}
		else
		{
			$date_info ['show_accept_button'] = 0;
			$date_info ['show_reject_button'] = 0;
		}
		
		if ($date_info ['date_status'] == 'confirm' && $date_info ['pay_status'] == 1 && $event_info ['event_status'] === '0')
		{
			$check_code_scan = $code_obj->check_code_scan ( $date_info ['enroll_id'] );
			if ($check_code_scan)
			{
				$date_info ['show_scan_button'] = 0;
				
				$update_time = $code_obj->get_scan_update_time ( $date_info ['enroll_id'] );

				$date_info ['show_event_finsh_button'] = 1;
				
			}
			else
			{
				$date_info ['show_scan_button'] = 1;
				$date_info ['show_event_finsh_button'] = 0;
			}
		}
		else
		{
			$date_info ['show_scan_button'] = 0;
			$date_info ['show_event_finsh_button'] = 0;
		}
		
		//模特是否已评价
		$check_model_comment = $cameraman_comment_obj->is_comment_by_model ( $date_id, $date_info ['to_date_id'] );
		
		if ($event_info ['event_status'] === '2' && ! $check_model_comment)
		{
			$date_info ['show_comment_button'] = 1; //评论按钮
		}
		else
		{
			$date_info ['show_comment_button'] = 0;
		}
		
		$date_info ['show_code_button'] = 0;
	
	}
	
	
	//是否约拍正在取消中
	$cancel_status = $date_cancel_obj->is_canceling ( $date_id );
	if ($cancel_status)
	{
		$date_info ['cancel_status'] = 1;
	}
	else
	{
		$date_info ['cancel_status'] = 0;
	}
	
	if (($date_info ['show_code_button'] == 0 && $date_info ['show_comment_button'] == 0 && $date_info ['show_event_finsh_button'] == 0 && $date_info ['show_scan_button'] == 0 && $date_info ['show_accept_button'] == 0 && $date_info ['show_reject_button'] == 0) || $date_info ['cancel_status'] == 1)
	{
		$date_info ['show_button_status'] = 0;
	}
	else
	{
		$date_info ['show_button_status'] = 1;
	}
	
	if ($date_info ['date_status'] == 'wait' && $date_info ['pay_status'] == 1)
	{
		$date_info ['show_submit_refund_button'] = 1;
	}
	elseif ($date_info ['date_status'] == 'confirm')
	{
		if (! $event_info ['event_status'] && ! $cancel_status)
		{
			if ($date_info ['qr_code'])
			{
				$date_info ['show_submit_refund_button'] = 1;
			}
			else
			{
				$date_info ['show_submit_refund_button'] = 0;
			}
		}
	}
	else
	{
		$date_info ['show_submit_refund_button'] = 0;
	}
	
	$cameraman_nickname = $date_info ['cameraman_nickname'];
	
	$log_list = $date_cancel_log_obj->get_date_cancel_log_list ( $date_id );
	
	//约拍详情LOG
	$cancel_array = $log_list;//申请退款记录
	
	$comment_array = $model_comment_obj->comment_log($date_id);//评论记录
	
	$reject_array = $date_obj->get_reject_info_by_date_id($date_id);//拒绝约拍记录
	
	if(!$cancel_array)
	{
		$cancel_array = array();
	}
	if(!$comment_array)
	{
		$comment_array = array();
	}
	if(empty($reject_array[0]))
	{
		$reject_array = array();
	}
	
	$date_log = array_merge( $comment_array,$cancel_array,$reject_array);
	foreach($date_log as $k=>$val)
	{
		if($k==0)
		{
			$date_log[$k]['high_light'] = 1;
		}
		else
		{
			$date_log[$k]['high_light'] = 0;
		}
	}
	$date_info ['date_log'] = $date_log;
	
	//取消的LOG 旧版
	$date_info ['cancel_log'] = $log_list;
	
	if ($date_info ['cancel_status'] == 1)
	{
		
		$cancel_type = $date_cancel_log_obj->get_last_cancel_log_type ( $date_id );
		
		if ($role == 'model')
		{
			if ($cancel_type == 'submit_application')
			{ //提交退款申请时，显示同意和不同意按钮
				$top_title = "对方发起活动取消<br />请您在12小时内回复，否则就默认同意取消哦！";
				$date_info ['display_agree_button'] = 1;
				$date_info ['display_disagree_button'] = 1;
			}
			elseif ($cancel_type == 'application_disagree')
			{ //最后的状态是不同意时，可以再同意
				$date_info ['display_agree_button'] = 1;
				$date_info ['display_disagree_button'] = 0;
			}
			else
			{
				$date_info ['display_agree_button'] = 0;
				$date_info ['display_disagree_button'] = 0;
			}
			
			$date_info ['display_refund_button'] = 0;
		
		}
		elseif ($role == 'cameraman')
		{
			$date_info ['display_refund_button'] = 0;
			
			if ($cancel_type == 'submit_application')
			{
				$top_title = "取消约拍提交成功，静候回复";
			
			}
			elseif ($cancel_type == 'auto_agree')
			{
				$top_title = "已自动退款";
			}
			elseif ($cancel_type == 'application_agree')
			{
				$top_title = "对方已接受取消约拍";
			}
			elseif ($cancel_type == 'application_disagree')
			{
				
				if ($date_info ['event_status'] == 0)
				{
					$check_code_scan = $code_obj->check_code_scan ( $date_info ['enroll_id'] );
					
					$top_title = "对方已拒绝取消约拍";
					if ($check_code_scan)
					{
						$date_info ['display_refund_button'] = 0;
					}
					else
					{
						$date_info ['display_refund_button'] = 1;
					}
				
				}
				else
				{
					$top_title = "";
					$date_info ['display_refund_button'] = 0;
				}
			}
			elseif ($cancel_type == 'have_part_refunded')
			{
				$top_title = "已退款成功";
			}
			elseif ($cancel_type == 'auto_agree')
			{
				$top_title = "已自动退款";
			}
		}
		
		$date_info ['top_title'] = $top_title;
		
		if (! $date_info ['display_agree_button'] && ! $date_info ['display_disagree_button'] && ! $date_info ['display_refund_button'])
		{
			$date_info ['show_cancel_button_status'] = 0;
		}
		else
		{
			$date_info ['show_cancel_button_status'] = 1;
		}
	
	}
	
	$audit_info = $id_audit_obj->get_audit_info ( $date_info ['from_date_id'] );
	
	$date_info ['id_audit_status'] = $audit_info ['status'];
	
	$model_card_info = $model_card_obj->get_model_card_info ( $date_info ['to_date_id'] );
	$level_require = $model_card_info ['level_require'];
	
	if (($audit_info ['status'] == 0 || $audit_info ['status'] == 2) && $date_info['date_status']=='wait' && $role == 'model')
	{
		if ($level_require != 1)
		{
			$date_info ['audit_status_text'] = "摄影师" . $date_info ['cameraman_nickname'] . "信用等级正在升级认证中，暂时未能达到您的等级要求。请结合对方约拍请求判断是否接受邀请。";
		}
	}
	
	return $date_info;
}

/*
 * 拒绝原因
 * @param $date_id int
 * @param $cancel_reason string 拒绝原因
 * @param $remark string 补充说明
 */
function why_cancel_date($date_id, $cancel_reason, $remark)
{
	$date_id = ( int ) $date_id;
	if (empty ( $date_id ))
		return false;
		
	$date_info = get_date_info ( $date_id );
	
	$weixin_pub_obj = POCO::singleton ( 'pai_weixin_pub_class' );
	
	$cameraman_user_id = $date_info ['from_date_id'];
	$model_user_id = $date_info ['to_date_id'];
	$date_time = $date_info ['date_time'];
	$reason = "【".$cancel_reason."】".$remark;
	$address = $date_info ['date_address'];
	$model_nickname = get_user_nickname_by_user_id ( $model_user_id );
	
	//微信发送通知
	$user_id = $cameraman_user_id;
	$template_code = 'G_PAI_WEIXIN_DATE_REFUSED';
	$weixin_data = array ('datetime' => date ( "Y年n月j日 H:i", $date_time ), 'nickname' => $model_nickname,'address'=>$address,'reason'=>$reason );
	$to_url = 'http://app.yueus.com/';
	$weixin_pub_obj->message_template_send_by_user_id ( $user_id, $template_code, $weixin_data, $to_url );
	
	$data ['cancel_reason'] = $cancel_reason;
	$data ['status_remark'] = $remark;
	update_event_date ( $data, $date_id );
}

/*
 * 退款原因
 */
function why_refund_date($date_id, $reason, $remark)
{
	$date_id = ( int ) $date_id;
	if (empty ( $date_id ))
		return false;
	
	$data ['refund_reason'] = $reason;
	$data ['refund_remark'] = $remark;
	update_event_date ( $data, $date_id );
}

function get_event_date($field, $value)
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	return $date_obj->get_event_date ( $field, $value );
}

function get_date_info($date_id)
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	return $date_obj->get_date_info ( $date_id );
}

function update_event_date_pay_status($date_id, $status = '1')
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$msg_obj = POCO::singleton ( 'pai_information_push' );
	$weixin_pub_obj = POCO::singleton ( 'pai_weixin_pub_class' );
	$date_config_obj = POCO::singleton ( 'pai_model_date_config_class' );
	
	$date_info = get_event_date ( 'date_id', $date_id );
	$date_info = $date_info [0];
	
	if ($date_info ['pay_status'] == $status)
		return false;
	
	if ($status == 1)
	{
		if($date_info ['is_use_coupon'])
		{
			$total_price = $date_info ['date_price'] * $date_info ['date_hour'] - $date_info ['discount_price'];
		}
		else
		{
			$total_price = $date_info ['date_price'] * $date_info ['date_hour'];
		}
		
		$pay_info = $payment_obj->frozen_date ( $date_info ['from_date_id'], $total_price, $date_id );
		
		//加LOG  例子 http://yp.yueus.com/logs/201501/28_info.txt
		$log_arr['from_date_id'] = $date_info ['from_date_id'];
		$log_arr['total_price'] = $total_price;
		$log_arr['date_info'] = $date_info;
		$log_arr['date_id'] = $date_id;
		$log_arr['result'] = $pay_info;
		pai_log_class::add_log($log_arr, 'frozen_date', 'info');
		
		if ($pay_info)
		{
			$from_date_id = $date_info ['from_date_id'];
			$to_user_id = $date_info ['to_date_id'];
			
			$direct_confirm_id = $date_info['direct_confirm_id'];
			
			//模特直接确认判断
			$direct_confirm_id = (int)$direct_confirm_id;
			if($direct_confirm_id)
			{
				$date_config = $date_config_obj->get_config_info($direct_confirm_id);
				
				//检查是否可用
				$check_available = $date_config_obj->check_available($direct_confirm_id);
				
				//判断直接下单的模特、时间是否吻合（后台有填就判断，没填不判断）
				
				
				//判断约拍时间
				if($date_config['date_time'])
				{
					if($date_config['date_time']==$date_info ['date_time'])
					{
						$is_date_time = true;
					}
					else
					{
						$is_date_time = false;
					}
				}
				else
				{
					$is_date_time = true;
				}
				
	
				if($date_config['user_id']==$date_info ['to_date_id'] && $check_available && $is_date_time)
				{
					$direct_confirm = true;
				}
			}
			
			
			$model_nickname = get_user_nickname_by_user_id ( $to_user_id );
			
			$send_data ['media_type'] = 'card';
			$send_data ['card_style'] = 1;
			$send_data ['card_text1'] = '约了' . $model_nickname . ':' . $date_info ['hour'] . '小时  ' . $date_info ['date_style'];
			$send_data ['card_text2'] = '￥' . sprintf ( "%.2f", $date_info ['date_price'] );
			$send_data ['card_title'] = '等待模特接受';
			
			$to_send_data ['media_type'] = 'card';
			$to_send_data ['card_style'] = 1;
			$to_send_data ['card_text1'] = '约了你:' . $date_info ['hour'] . '小时  ' . $date_info ['date_style'];
			$to_send_data ['card_text2'] = '￥' . sprintf ( "%.2f", $date_info ['date_price'] );
			$to_send_data ['card_title'] = '接受或拒绝';
			
			//微信通知
			$template_code = 'G_PAI_WEIXIN_DATE_PAID';
			$weixin_data = array ('amount' => $date_info ['date_price'], 'nickname' => $model_nickname );
			$version_control = include ('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
			$cache_ver = trim ( $version_control ['wx'] ['cache_ver'] );
			$to_url = "http://yp.yueus.com/m/wx?{$cache_ver}#mine/consider_details_camera/{$date_id}";
			
			if (! defined ( 'YUE_OA_IMPORT_ORDER' ) && !$direct_confirm)
			{
				$msg_obj->send_msg_data ( $from_date_id, $to_user_id, $send_data, $to_send_data, $date_id );
				$weixin_pub_obj->message_template_send_by_user_id ( $from_date_id, $template_code, $weixin_data, $to_url );
			}
			
			$data ['pay_time'] = time();
			$data ['pay_status'] = $status;
			$date_obj->update_event_date ( $data, $date_id );
			
			if($direct_confirm)
			{
				//直接确认
				update_event_date_status($date_id, 'confirm', $date_info ['to_date_id']);
				//更新使用次数
				$date_config_obj->update_use_times($direct_confirm_id);
			}
		
			
			return true;
		}
	}
	
	return false;
}

/**
 * 
 * 约拍确定
 * @param $date_id 约会表主键
 * @param $date_status 更新状态
 * @param $user_id 传递当前登陆的用户ID，进行验证该用户是否有权利去更改约会状态，判断方式：约会表的被约会人ID是否等于USERID
 * return -1操作人身份异常，-2操作状态异常，-3支付状态异常，1约拍成功
 * */
function update_event_date_status($date_id, $date_status = 'confirm', $user_id)
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$code_obj = POCO::singleton ( 'pai_activity_code_class' );
	$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$msg_obj = POCO::singleton ( 'pai_information_push' );
	$coupon_obj = POCO::singleton('pai_coupon_class');
	$date_info = get_date_info ( $date_id );
	
	$weixin_pub_obj = POCO::singleton ( 'pai_weixin_pub_class' );
	
	$cameraman_user_id = $date_info ['from_date_id'];
	$model_user_id = $date_info ['to_date_id'];
	$date_time = $date_info ['date_time'];
	
	if ($user_id != $date_info ['to_date_id'])
	{
		return - 1;
	}
	
	if ($date_info ['date_status'] != 'wait')
	{
		return - 2;
	}
	
	$model_nickname = get_user_nickname_by_user_id ( $user_id );
	$cameraman_nickname = get_user_nickname_by_user_id ( $cameraman_user_id );
	
	if ($date_info ['pay_status'] == 1 && $date_status == 'confirm')
	{
		$pay_info = $payment_obj->accepted_date ( $date_id );
		//var_dump($pay_info);
		if ($pay_info)
		{
			$ret = $date_obj->update_event_date_status ( $date_id, $date_status );
			if ($ret)
			{
				
				$send_data ['media_type'] = 'card';
				$send_data ['card_style'] = 2;
				$send_data ['card_text1'] = '已通过' . $cameraman_nickname . '的约拍邀请,请准时到现场扫码签到';
				$send_data ['card_title'] = '扫码签到';
				
				$to_send_data ['media_type'] = 'card';
				$to_send_data ['card_style'] = 2;
				$to_send_data ['card_text1'] = '已通过你的约拍邀请,请准时到现场扫码签到';
				$to_send_data ['card_title'] = '出示签到码';
				
				
				//微信通知
				$user_id = $cameraman_user_id;
				$template_code = 'G_PAI_WEIXIN_DATE_ACCEPTED';
				$weixin_data = array ('datetime' => date ( "Y年n月j日 H:i", $date_info ['date_time'] ), 'address' => $date_info ['date_address'], 'nickname' => $model_nickname );
				$version_control = include ('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
				$cache_ver = trim ( $version_control ['wx'] ['cache_ver'] );
				$to_url = "http://yp.yueus.com/m/wx?{$cache_ver}#mine/consider_details_camera/{$date_id}";
				
			
				
				$new_date_info = get_date_info ( $date_id );
				$code_info = $code_obj->get_code_by_enroll_id ( $new_date_info ['enroll_id'] );
				$activity_code = $code_info [0] ['code'];
				
				$phone = $user_obj->get_phone_by_user_id ( $cameraman_user_id ); //摄影师手机号码
				$data = array ('mt_nickname' => $model_nickname, //模特昵称
'activity_code' => activity_code_transfer ( $activity_code ) ); //数字码
				
				if (! defined ( 'YUE_OA_IMPORT_ORDER' ))
				{
					$msg_obj->send_msg_data ( $model_user_id, $cameraman_user_id, $send_data, $to_send_data, $date_id );
					$weixin_pub_obj->message_template_send_by_user_id ( $user_id, $template_code, $weixin_data, $to_url );
					$pai_sms_obj->send_sms ( $phone, 'G_PAI_DATE_MT_AGREE', $data, $user_id );
				}

				
				
				return $ret;
			}
		}
	}
	elseif ($date_info ['pay_status'] == 1 && $date_status == 'cancel')
	{
		$pay_info = $payment_obj->refused_date ( $date_id );
		if ($pay_info)
		{
			
			$send_data ['media_type'] = 'card';
			$send_data ['card_style'] = 2;
			$send_data ['card_text1'] = '已拒绝' . $cameraman_nickname . '的约拍邀请';
			$send_data ['card_title'] = '查看约拍详情';
			
			$to_send_data ['media_type'] = 'card';
			$to_send_data ['card_style'] = 2;
			$to_send_data ['card_text1'] = '拒绝你的约拍邀请';
			$to_send_data ['card_title'] = '查看约拍详情';
			
			$msg_obj->send_msg_data ( $model_user_id, $cameraman_user_id, $send_data, $to_send_data, $date_id );
			
			$coupon_obj->refund_coupon_by_oid("yuepai", $date_id);
			
			return $date_obj->update_event_date_status ( $date_id, $date_status );
		}
	}
	else
	{
		return - 3;
	}

}

function update_event_date($data, $where)
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	return $date_obj->update_event_date ( $data, $where );
}

function get_all_event_date($b_select_count = false, $where_str = '', $order_by = 'date_id DESC', $limit = '0,10', $fields = '*')
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	return $date_obj->get_all_event_date ( $b_select_count, $where_str, $order_by, $limit, $fields );
}

function get_date_rank($location_id = 0, $limit = '0,6')
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	$ret = $date_obj->get_date_rank ( $location_id, $limit );
	foreach ( $ret as $k => $val )
	{
		$ret [$k] ['nickname'] = get_user_nickname_by_user_id ( $val ['user_id'] );
		$ret [$k] ['user_icon'] = get_user_icon ( $val ['user_id'], 165 );
	}
	return $ret;
}

/*
 * 提交约拍取消申请
 * 
 * return 模特接受后摄影师退款  1为提交成功,-1为模特还未接受邀请,-2已有取消记录,-3为活动券已被扫了
 * return 模特还没接受前摄影师退款 1为成功，-1为参数错误，-2为退款失败，-3为状态异常
 */
function submit_date_cancel_application($date_id, $reason, $remark = '')
{
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	
	$date_info = get_date_info ( $date_id );
	if ($date_info ['date_status'] == 'wait')
	{
		$ret = $date_cancel_obj->submit_date_refund ( $date_id, $reason, $remark );
	}
	else
	{
		$ret = $date_cancel_obj->submit_date_cancel_application ( $date_id, $reason, $remark );
	}
	$ret_array ['type'] = $date_info ['date_status'];
	$ret_array ['code'] = $ret;
	return $ret_array;
}

/*
 * 模特还没接受前摄影师退款
 * return 1为成功，-1为参数错误，-2为退款失败，-3为状态异常
 */
function submit_date_refund($date_id, $reason, $remark)
{
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	$ret = $date_cancel_obj->submit_date_refund ( $date_id, $reason, $remark );
	return $ret;
}

/*
 * 更新同意状态
 * 
 * return 1为成功，-1为你已回复摄影师取消申请，  -2为系统状态异常
 */
function update_agree_status($date_id, $agree_status)
{
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	$ret = $date_cancel_obj->update_agree_status ( $date_id, $agree_status );
	return $ret;
}

/*
 * 强制退款
 * 
 * return 1为成功,-1为模特同意状态异常，-2为退款失败，请联系管理员  ，-3为系统状态异常
 */
function update_force_refund_status($date_id)
{
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	$ret = $date_cancel_obj->update_force_refund_status ( $date_id );
	return $ret;
}

/*
 * 摄影师我的（约拍数字显示）
 */
function count_yuepai_order_num($user_id)
{
	$user_id = (int)$user_id;
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	
	$user_id = get_relate_poco_id($user_id);
	$enroll_list = get_enroll_list("user_id={$user_id} and pay_status=1 and remark='私人约拍'", false, '0,10000', 'enroll_id DESC',"event_id");
	
	foreach($enroll_list as $val)
	{
		$event_status = $event_details_obj->get_event_info_status($val['event_id']);
		
		if($event_status==='0')
		{
			$count++;
		}
	}
	$count = (int)$count;
	return $count;
}

?>