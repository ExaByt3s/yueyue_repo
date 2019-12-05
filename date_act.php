<?php

//������
//include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
//include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

function add_event_date($date_data)
{
	$date_obj = POCO::singleton ( 'event_date_class' );
	
	$add_ret = $date_obj->add_event_date ( $date_data );
	return $add_ret;

}

/**
 * Լ���ύ����  modify hai 20140911
 * @param array $date_data 
 * array( 
 * 'from_date_id'=>'', //��ӰʦID ����ʱ��$yue_login_id ��ֵ
 * 'to_date_id'=>'',   //ģ��ID
 * 'date_status'=>'',  //״̬     ����wait
 * 'date_time'=>'',    //Լ��ʱ��
 * 'date_type'=>'',    //��������
 * 'date_style'=>'',   //������
 * 'date_hour'=>'',    //����ʱ��
 * 'date_price'=>'',   //����
 * 'date_address'=>''  //��ַ
 *
 *)   
 * @param int   $user_balance �û����  �����ж��û��Ƿ�ͣ��ҳ��̫��ʱ��û�ύ  ���û����䶯�����ύ
 * @param int   $is_available_balance   0Ϊ������֧�� 1Ϊ���֧��   �������֧������Ҫ������ת������������֧��
 * @param string $third_code   ������֧���ı�ʶ ����ʱ֧��΢�ź�֧����Ǯ�� alipay_purse��tenpay_wxapp ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
 * @return array array( 'status_code'=>0,'message'=>'','cur_balance'=>'','request_data'=>$request_data)
 * ����ֵ status_code Ϊ�Ƿ����  status_code -1 �������� -2�û�����б䶯   -3��ӵ�Լ�ı�ʱ��������  -4Ϊ���ɵ��������������������
 * 1Ϊ���֧���ɹ�   2Ϊ������������ɹ�������ת����������
 * message���ص���Ϣ cur_balance �����û���ǰ��ʵ���[��status_code==2 �����֧���ɹ����д�key] request_data ����������������ַ���[��Ҫ��������ʱ��ŷ���]
 *
 */
function add_event_date_op($date_data, $user_balance, $is_available_balance, $third_code, $redirect_url)
{
	
	$date_data ['from_date_id'] = intval ( $date_data ['from_date_id'] );
	if (empty ( $date_data ['from_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'from_date_id ��ʽ����' );
		return $ret;
	
	}
	$date_data ['to_date_id'] = intval ( $date_data ['to_date_id'] );
	if (empty ( $date_data ['to_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'to_date_id ��ʽ����' );
		return $ret;
	}
	/*
	$date_data['date_time'] = intval($date_data['date_time']);
    if(empty($date_data['date_time']))
    {
		$ret = array( 'status_code'=>-1,'message'=>'date_time ��ʽ����' );
		return $ret;
    }*/
	
	$date_data ['date_price'] = floatval ( $date_data ['date_price'] );
	
	$total_price = $date_data ['date_price'] * $date_data ['date_hour'];
	
	if (empty ( $date_data ['date_price'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'date_price ��ʽ����' );
		return $ret;
	
	}
	
	if ($total_price < 10 || $total_price > 10000)
	{
		$ret = array ('status_code' => - 1, 'message' => 'Լ�ļ۸�Ҫ��10-10000֮��' );
		return $ret;
	
	}
	
	$date_data ['date_hour'] = intval ( $date_data ['date_hour'] );
	if (empty ( $date_data ['date_hour'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'date_hour ��ʽ����' );
		return $ret;
	
	}
	if (! in_array ( $is_available_balance, array (0, 1 ) ))
	{
		
		$ret = array ('status_code' => - 1, 'message' => 'is_available_balance ��ʽ����' );
		return $ret;
	
	}
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$user_info = $user_obj->get_user_info_by_user_id ( $date_data ['from_date_id'] );
	
	$account_info = $payment_obj->get_user_account_info ( $date_data ['from_date_id'] );
	if ($account_info ['available_balance'] != $user_balance)
	{
		
		$ret = array ('status_code' => - 2, 'message' => '�û�����б䶯', 'user_info' => $user_info );
		return $ret;
	
	}
	$date_id = add_event_date ( $date_data );
	if ($date_id < 1)
	{
		
		$ret = array ('status_code' => - 3, 'message' => '��ӵ�Լ�Ĳ�������' );
		return $ret;
	
	}
	$account_info ['user_name'] = $user_info ['nickname'];
	$total_price = $date_data ['date_price'] * $date_data ['date_hour'];
	
	//�����֧��
	if ($is_available_balance)
	{
		
		if ($account_info ['available_balance'] < $total_price)
		{
			//���㣬��תȥ��ֵ
			$amount = $total_price - $account_info ['available_balance'];
			$redirect_third = 1;
			//$amount = bcsub($sum_cost, $available_balance, 2);
		

		}
		else
		{
			
			update_event_date_pay_status ( $date_id, $status = '1' );
			$ret = array ('status_code' => 1, 'message' => '���֧���ɹ���', 'user_info' => $user_info );
			return $ret;
		
		}
	
	}
	else
	{
		//ֱ����֧����֧��
		$amount = $total_price;
		$redirect_third = 1;
	
	}
	if ($redirect_third)
	{
		
		if (! in_array ( $third_code, array ('alipay_purse', 'tenpay_wxapp', 'tenpay_wxpub' ) ))
		{
			
			$ret = array ('status_code' => - 1, 'message' => 'third_code ֧����ʶ����' );
			return $ret;
		
		}
		$more_info ['channel_return'] = $redirect_url;
		$recharge_ret = $payment_obj->submit_recharge ( 'date', $date_data ['from_date_id'], $amount, $third_code, 0, '', $date_id, $more_info );
		if ($recharge_ret ['error'] === 0)
		{
			$payment_no = trim ( $recharge_ret ['payment_no'] );
			$request_data = trim ( $recharge_ret ['request_data'] );
			$ret = array ('status_code' => 2, 'message' => '���Լ�ĳɹ�,����ת��������֧����', 'request_data' => $request_data, 'payment_no' => $payment_no );
			return $ret;
		
		}
		else
		{
			$ret = array ('status_code' => - 4, 'message' => '��ת��������֧����������  ��ϸ��Ϣ��recharge_ret', 'recharge_ret' => $recharge_ret );
			return $ret;
		}
	
	}
}

function add_event_date_op_v2($date_data, $user_balance, $is_available_balance, $third_code, $redirect_url, $notify_url = '',$coupon_sn='')
{
	
	$date_data ['from_date_id'] = intval ( $date_data ['from_date_id'] );
	if (empty ( $date_data ['from_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'from_date_id ��ʽ����' );
		return $ret;
	
	}
	$date_data ['to_date_id'] = intval ( $date_data ['to_date_id'] );
	if (empty ( $date_data ['to_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'to_date_id ��ʽ����' );
		return $ret;
	}
	
	$date_data['date_time'] = intval($date_data['date_time']);
    if(empty($date_data['date_time']))
    {
		$ret = array( 'status_code'=>-1,'message'=>'��ѡ��Լ��ʱ��' );
		return $ret;
    }
	
	$date_data ['date_price'] = floatval ( $date_data ['date_price'] );
	$date_data ['date_hour'] = 1;
	if (empty ( $date_data ['date_price'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'date_price ��ʽ����' );
		return $ret;
	
	}
	
	if ( $date_data ['date_price'] > 10000)
	{
		$ret = array ('status_code' => - 1, 'message' => 'Լ�ļ۸�Ҫ��10000֮��' );
		return $ret;
	
	}
	
	if (! in_array ( $is_available_balance, array (0, 1 ) ))
	{
		
		$ret = array ('status_code' => - 1, 'message' => 'is_available_balance ��ʽ����' );
		return $ret;
	
	}
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
	$coupon_obj = POCO::singleton('pai_coupon_class');
	
	$user_info = $user_obj->get_user_info_by_user_id ( $date_data ['from_date_id'] );
	
	if($user_info['role']!='cameraman')
	{
		$ret = array ('status_code' => - 1, 'message' => 'ֻ����Ӱʦ����Լ��ģ��' );
		return $ret;
	}
	
	
	$account_info = $payment_obj->get_user_account_info ( $date_data ['from_date_id'] );
	if ($account_info ['available_balance'] != $user_balance)
	{
		
		$ret = array ('status_code' => - 2, 'message' => '�û�����б䶯', 'user_info' => $user_info );
		return $ret;
	
	}
	
	//���IOS�ո�����
	$data['date_address'] = str_replace("\u2006","",$data['date_address']);
	
	$org_info = $relate_org_obj->get_org_info_by_user_id($date_data['to_date_id']);
	
	$date_data['org_user_id'] = (int)$org_info['org_id'];
	
	$date_id = add_event_date ( $date_data );
	if ($date_id < 1)
	{
		
		$ret = array ('status_code' => - 3, 'message' => '��ӵ�Լ�Ĳ�������' );
		return $ret;
	
	}
	$account_info ['user_name'] = $user_info ['nickname'];
	$total_price = $date_data ['date_price'];
	
	
	//�Ż�ȯ����
	
	$channel_module = "yuepai";
	$coupon_obj->not_use_coupon_by_oid($channel_module, $date_id);
	
	if($coupon_sn)
	{
		$param_info = array(
		   'module_type'=>$channel_module, //ģ������ waipai yuepai topic
		   'order_total_amount'=>$date_data ['date_price'], //�����ܽ��
		   'model_user_id'=>$date_data ['to_date_id'], //ģ���û�ID��Լ�ġ�ר�⣩
		   'org_user_id'=>$date_data['org_user_id'], //�����û�ID
		   'mall_type_id' => 31, //����Ʒ��
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
			$ret = array ('status_code' => - 1, 'message' => '�Żݽ������' );
			return $ret;
		}
		
		$udpate_date['discount_price'] = $coupon_ret['used_amount'];
		$udpate_date['is_use_coupon'] = 1;
		update_event_date($udpate_date, $date_id);
	}
	
	//�����֧��
	if ($is_available_balance)
	{
		
		if ($account_info ['available_balance'] < $total_price)
		{
			//���㣬��תȥ��ֵ
			$amount = $total_price - $account_info ['available_balance'];
			$redirect_third = 1;
			//$amount = bcsub($sum_cost, $available_balance, 2);
		

		}
		else
		{
			
			update_event_date_pay_status ( $date_id, $status = '1');
			$ret = array ('status_code' => 1, 'message' => '���֧���ɹ���', 'user_info' => $user_info );
			return $ret;
		
		}
	
	}
	else
	{
		//ֱ����֧����֧��
		$amount = $total_price;
		$redirect_third = 1;
	
	}
	if ($redirect_third)
	{
		
		if (! in_array ( $third_code, array ('alipay_purse', 'tenpay_wxapp', 'tenpay_wxpub' ) ))
		{
			
			$ret = array ('status_code' => - 1, 'message' => 'third_code ֧����ʶ����' );
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
				
				$ret = array ('status_code' => - 1, 'message' => '΢���û�û��ԼԼ�˺�' );
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
			$ret = array ('status_code' => 2, 'message' => '���Լ�ĳɹ�,����ת��������֧����', 'request_data' => $request_data, 'payment_no' => $payment_no );
			return $ret;
		
		}
		else
		{
			$ret = array ('status_code' => - 4, 'message' => '��ת��������֧����������  ��ϸ��Ϣ��recharge_ret', 'recharge_ret' => $recharge_ret );
			return $ret;
		}
	
	}
}

/*
 * ���׼��Լ�ļ�¼
 */
function add_date_op($date_data)
{
	$date_data ['from_date_id'] = intval ( $date_data ['from_date_id'] );
	if (empty ( $date_data ['from_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'from_date_id ��ʽ����' );
		return $ret;
	
	}
	$date_data ['to_date_id'] = intval ( $date_data ['to_date_id'] );
	if (empty ( $date_data ['to_date_id'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'to_date_id ��ʽ����' );
		return $ret;
	}
	
	$date_data['date_time'] = intval($date_data['date_time']);
    if(empty($date_data['date_time']))
    {
		$ret = array( 'status_code'=>-1,'message'=>'��ѡ��Լ��ʱ��' );
		return $ret;
    }
	
	$date_data ['date_price'] = floatval ( $date_data ['date_price'] );
	$date_data ['date_hour'] = 1;
	if (empty ( $date_data ['date_price'] ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'date_price ��ʽ����' );
		return $ret;
	
	}
	
	if ( $date_data ['date_price'] > 30000)
	{
		$ret = array ('status_code' => - 1, 'message' => 'Լ�ļ۸�Ҫ��30000֮��' );
		return $ret;
	
	}
	
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
	$coupon_obj = POCO::singleton('pai_coupon_class');
	
	$user_info = $user_obj->get_user_info_by_user_id ( $date_data ['from_date_id'] );
	
	if($user_info['role']!='cameraman')
	{
		$ret = array ('status_code' => - 1, 'message' => 'ֻ����Ӱʦ����Լ��ģ��' );
		return $ret;
	}
	
	
	$org_info = $relate_org_obj->get_org_info_by_user_id($date_data['to_date_id']);
	
	$date_data['org_user_id'] = (int)$org_info['org_id'];

	$date_id = add_event_date ( $date_data );
	
	if ($date_id < 1)
	{
		$ret = array ('status_code' => - 1, 'message' => '���Լ�Ĵ���' );
		return $ret;
	
	}
	else 
	{
		$ret = array ('status_code' => 1, 'message' => '���Լ�ĳɹ�','date_id'=>$date_id );
		return $ret;
	}
}


function update_date_op($date_id, $user_balance, $is_available_balance, $third_code, $redirect_url, $notify_url = '',$coupon_sn='')
{
	$date_id = (int)$date_id;
	
	global $yue_login_id;
	

	if ($date_id < 1)
	{
		$ret = array ('status_code' => - 3, 'message' => 'date_id����' );
		return $ret;	
	}
	
	$date_info = get_date_info($date_id);
	
	if($date_info ['from_date_id']!=$yue_login_id)
	{
		$ret = array ('status_code' => - 1, 'message' => '�û�ID�쳣' );
		return $ret;
	}
	
	if (! in_array ( $is_available_balance, array (0, 1 ) ))
	{
		$ret = array ('status_code' => - 1, 'message' => 'is_available_balance ��ʽ����' );
		return $ret;
	}
	
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$coupon_obj = POCO::singleton('pai_coupon_class');
	$user_obj = POCO::singleton ( 'pai_user_class' );
	
	$user_info = $user_obj->get_user_info_by_user_id ( $date_info ['from_date_id'] );
	$account_info = $payment_obj->get_user_account_info ( $date_info ['from_date_id'] );
	if ($account_info ['available_balance'] != $user_balance)
	{
		$ret = array ('status_code' => - 2, 'message' => '�û�����б䶯', 'user_info' => $user_info );
		return $ret;	
	}
	
	
	$total_price = $date_info ['date_price'];
	
	//�Ż�ȯ����
	$channel_module = "yuepai";
	$coupon_obj->not_use_coupon_by_oid($channel_module, $date_id);
	
	//�ȸ���Ϊ0
	$c_udpate_date['discount_price'] = 0;
	$c_udpate_date['is_use_coupon'] = 0;
	update_event_date($c_udpate_date, $date_id);
	
	if($coupon_sn)
	{
		$param_info = array(
		   'module_type'=>$channel_module, //ģ������ waipai yuepai topic
		   'order_total_amount'=>$date_info ['date_price'], //�����ܽ��
		   'model_user_id'=>$date_info ['to_date_id'], //ģ���û�ID��Լ�ġ�ר�⣩
		   'org_user_id'=>$date_info['org_user_id'], //�����û�ID
		   'mall_type_id' => 31, //����Ʒ��
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
			$ret = array ('status_code' => - 1, 'message' => '�Żݽ������' );
			return $ret;
		}
		
		$udpate_date['discount_price'] = $coupon_ret['used_amount'];
		$udpate_date['is_use_coupon'] = 1;
		update_event_date($udpate_date, $date_id);
	}
	
	//�����֧��
	if ($is_available_balance)
	{
		
		if ($account_info ['available_balance'] < $total_price)
		{
			//���㣬��תȥ��ֵ
			$amount = $total_price - $account_info ['available_balance'];
			$redirect_third = 1;
			//$amount = bcsub($sum_cost, $available_balance, 2);
		

		}
		else
		{
			
			update_event_date_pay_status ( $date_id, $status = '1');
			$ret = array ('status_code' => 1, 'message' => '���֧���ɹ���', 'user_info' => $user_info );
			return $ret;
		
		}
	
	}
	else
	{
		//ֱ����֧����֧��
		$amount = $total_price;
		$redirect_third = 1;
	
	}
	if ($redirect_third)
	{
		
		if (! in_array ( $third_code, array ('alipay_purse', 'tenpay_wxapp', 'tenpay_wxpub' ) ))
		{
			
			$ret = array ('status_code' => - 1, 'message' => 'third_code ֧����ʶ����' );
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
				
				$ret = array ('status_code' => - 1, 'message' => '΢���û�û��ԼԼ�˺�' );
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
			$ret = array ('status_code' => 2, 'message' => '���Լ�ĳɹ�,����ת��������֧����', 'request_data' => $request_data, 'payment_no' => $payment_no );
			return $ret;
		
		}
		else
		{
			$ret = array ('status_code' => - 4, 'message' => '��ת��������֧����������  ��ϸ��Ϣ��recharge_ret', 'recharge_ret' => $recharge_ret );
			return $ret;
		}
	
	}
}

/*
 * ģ��Լ��״̬�б�
 * @param $model_user_id ģ��ID
 * @param $status ������consider  ��ͬ��confirm
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
		$list [$k] ['total_price_v2'] = $value ['date_price'] . "(" . $value ['hour'] . "Сʱ)";
		
		if ($status == 'confirm')
		{
			$event_info = $event_details_obj->get_event_by_event_id ( $value ['event_id'] );
			//�ж��Ƿ��ѽ���
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
			
			//��ʾlable״̬
			if ($check_canceling && $event_status == 0)
			{
				$lable = "����ȡ��";
				$event_ready = 1;
				$event_finish = 0;
				$event_cancel = 0;
			}
			elseif ($event_status == 3)
			{
				$lable = "��ȡ��";
				$event_ready = 0;
				$event_finish = 0;
				$event_cancel = 1;
			}
			elseif ($event_status == 2)
			{
				$lable = "�����";
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
			
			//��Ƿ�������һ��������ɨ��
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
			
			// ��������
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
			
			//ģ���Ƿ�������
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
				$lable = "�Ѿܾ�";
			}
			elseif ($value ['date_status'] == 'refund')
			{
				$lable = "���˿�";
			}
			else
			{
				$lable = "";
			}
			$list [$k] ['lable'] = $lable;
			$list [$k] ['is_end'] = 1;
		}
		
		$list [$k] ['role_text'] = "��Լ�ġ���Ӱʦ";
	
	}
	
	return $list;
}

/*
 * ��ӰʦԼ��״̬�б�
 * @param $cameraman_user_id ��ӰʦID
 * @param $status ������consider  ��ͬ��confirm
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
		$list [$k] ['total_price_v2'] = $value ['date_price'] . "(" . $value ['hour'] . "Сʱ)";
		
		if ($status == 'confirm')
		{
			$event_info = $event_details_obj->get_event_by_event_id ( $value ['event_id'] );
			//�ж��Ƿ��ѽ���
			if ($event_info ['event_status'] != 0)
			{
				$list [$k] ['is_end'] = 1;
			}
			else
			{
				$list [$k] ['is_end'] = 0;
			}
			
			//��Ƿ�������һ��������ɨ��
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
			
			//��ʾlable״̬
			if ($check_canceling && $event_status == 0)
			{
				$lable = "����ȡ��";
				$event_ready = 1;
				$event_finish = 0;
				$event_cancel = 0;
			}
			elseif ($event_status == 3)
			{
				$lable = "��ȡ��";
				$event_ready = 0;
				$event_finish = 0;
				$event_cancel = 1;
			}
			elseif ($event_status == 2)
			{
				$lable = "�����";
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
			
			// ��������
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
			
			// ��Ӱʦ�Ƿ�������Լ��
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
				$lable = "�Ѿܾ�";
			}
			elseif ($value ['date_status'] == 'refund')
			{
				$lable = "���˿�";
			}
			else
			{
				$lable = "";
			}
			
			$list [$k] ['event_status'] = 1;
			$list [$k] ['lable'] = $lable;
		}
		
		$list [$k] ['role_text'] = "ģ��";
	
	}
	
	return $list;
}

/*
 * ����date_idȡԼ����Ϣ
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
	//��ǰ�û���ɫ
	$role = $user_obj->check_role ( $yue_login_id );
	
	//$date_info ['date_id'] = date("Ymd",$date_info ['add_time']).$date_id;
	
	$date_info ['cameraman_nickname'] = get_user_nickname_by_user_id ( $date_info ['from_date_id'] );
	$date_info ['model_nickname'] = get_user_nickname_by_user_id ( $date_info ['to_date_id'] );
	
	$date_info ['date_time'] = date ( "Y-m-d H:i", $date_info ['date_time'] );
	
	$cameraman_user_info = $user_obj->get_user_info ( $date_info ['from_date_id'] );
	
	$date_info ['city_name'] = get_poco_location_name_by_location_id ( $cameraman_user_info ['location_id'] );
	
	$date_info ['total_price'] = $date_info ['date_price'] * $date_info ['date_hour'];
	$date_info ['total_price_v2'] = $date_info ['date_price'] . "(" . $date_info ['hour'] . "Сʱ)";
	
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
			$date_info ['show_code_button'] = 1; //���ȯ��ť
		}
		else
		{
			$date_info ['show_code_button'] = 0;
		}
		
		//��Ӱʦ�Ƿ�������
		$check_cameraman_comment = $model_comment_obj->is_comment_by_cameraman ( $date_id, $date_info ['from_date_id'] );
		
		if ($event_info ['event_status'] === '2' && ! $check_cameraman_comment)
		{
			$date_info ['show_comment_button'] = 1; //���۰�ť
		}
		else
		{
			$date_info ['show_comment_button'] = 0;
		}
		
		$date_info ['show_accept_button'] = 0; //ģ��������ܰ�ť
		$date_info ['show_reject_button'] = 0; //ģ�ؾܾ���ť
		$date_info ['show_scan_button'] = 0; //ɨ��ǩ����ť
		$date_info ['show_event_finsh_button'] = 0; //�ȷ�ϰ�ť
	

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
		
		//ģ���Ƿ�������
		$check_model_comment = $cameraman_comment_obj->is_comment_by_model ( $date_id, $date_info ['to_date_id'] );
		
		if ($event_info ['event_status'] === '2' && ! $check_model_comment)
		{
			$date_info ['show_comment_button'] = 1; //���۰�ť
		}
		else
		{
			$date_info ['show_comment_button'] = 0;
		}
		
		$date_info ['show_code_button'] = 0;
	
	}
	
	
	//�Ƿ�Լ������ȡ����
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
	
	//Լ������LOG
	$cancel_array = $log_list;//�����˿��¼
	
	$comment_array = $model_comment_obj->comment_log($date_id);//���ۼ�¼
	
	$reject_array = $date_obj->get_reject_info_by_date_id($date_id);//�ܾ�Լ�ļ�¼
	
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
	
	//ȡ����LOG �ɰ�
	$date_info ['cancel_log'] = $log_list;
	
	if ($date_info ['cancel_status'] == 1)
	{
		
		$cancel_type = $date_cancel_log_obj->get_last_cancel_log_type ( $date_id );
		
		if ($role == 'model')
		{
			if ($cancel_type == 'submit_application')
			{ //�ύ�˿�����ʱ����ʾͬ��Ͳ�ͬ�ⰴť
				$top_title = "�Է�����ȡ��<br />������12Сʱ�ڻظ��������Ĭ��ͬ��ȡ��Ŷ��";
				$date_info ['display_agree_button'] = 1;
				$date_info ['display_disagree_button'] = 1;
			}
			elseif ($cancel_type == 'application_disagree')
			{ //����״̬�ǲ�ͬ��ʱ��������ͬ��
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
				$top_title = "ȡ��Լ���ύ�ɹ�������ظ�";
			
			}
			elseif ($cancel_type == 'auto_agree')
			{
				$top_title = "���Զ��˿�";
			}
			elseif ($cancel_type == 'application_agree')
			{
				$top_title = "�Է��ѽ���ȡ��Լ��";
			}
			elseif ($cancel_type == 'application_disagree')
			{
				
				if ($date_info ['event_status'] == 0)
				{
					$check_code_scan = $code_obj->check_code_scan ( $date_info ['enroll_id'] );
					
					$top_title = "�Է��Ѿܾ�ȡ��Լ��";
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
				$top_title = "���˿�ɹ�";
			}
			elseif ($cancel_type == 'auto_agree')
			{
				$top_title = "���Զ��˿�";
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
			$date_info ['audit_status_text'] = "��Ӱʦ" . $date_info ['cameraman_nickname'] . "���õȼ�����������֤�У���ʱδ�ܴﵽ���ĵȼ�Ҫ�����϶Է�Լ�������ж��Ƿ�������롣";
		}
	}
	
	return $date_info;
}

/*
 * �ܾ�ԭ��
 * @param $date_id int
 * @param $cancel_reason string �ܾ�ԭ��
 * @param $remark string ����˵��
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
	$reason = "��".$cancel_reason."��".$remark;
	$address = $date_info ['date_address'];
	$model_nickname = get_user_nickname_by_user_id ( $model_user_id );
	
	//΢�ŷ���֪ͨ
	$user_id = $cameraman_user_id;
	$template_code = 'G_PAI_WEIXIN_DATE_REFUSED';
	$weixin_data = array ('datetime' => date ( "Y��n��j�� H:i", $date_time ), 'nickname' => $model_nickname,'address'=>$address,'reason'=>$reason );
	$to_url = 'http://app.yueus.com/';
	$weixin_pub_obj->message_template_send_by_user_id ( $user_id, $template_code, $weixin_data, $to_url );
	
	$data ['cancel_reason'] = $cancel_reason;
	$data ['status_remark'] = $remark;
	update_event_date ( $data, $date_id );
}

/*
 * �˿�ԭ��
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
		
		//��LOG  ���� http://yp.yueus.com/logs/201501/28_info.txt
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
			
			//ģ��ֱ��ȷ���ж�
			$direct_confirm_id = (int)$direct_confirm_id;
			if($direct_confirm_id)
			{
				$date_config = $date_config_obj->get_config_info($direct_confirm_id);
				
				//����Ƿ����
				$check_available = $date_config_obj->check_available($direct_confirm_id);
				
				//�ж�ֱ���µ���ģ�ء�ʱ���Ƿ��Ǻϣ���̨������жϣ�û��жϣ�
				
				
				//�ж�Լ��ʱ��
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
			$send_data ['card_text1'] = 'Լ��' . $model_nickname . ':' . $date_info ['hour'] . 'Сʱ  ' . $date_info ['date_style'];
			$send_data ['card_text2'] = '��' . sprintf ( "%.2f", $date_info ['date_price'] );
			$send_data ['card_title'] = '�ȴ�ģ�ؽ���';
			
			$to_send_data ['media_type'] = 'card';
			$to_send_data ['card_style'] = 1;
			$to_send_data ['card_text1'] = 'Լ����:' . $date_info ['hour'] . 'Сʱ  ' . $date_info ['date_style'];
			$to_send_data ['card_text2'] = '��' . sprintf ( "%.2f", $date_info ['date_price'] );
			$to_send_data ['card_title'] = '���ܻ�ܾ�';
			
			//΢��֪ͨ
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
				//ֱ��ȷ��
				update_event_date_status($date_id, 'confirm', $date_info ['to_date_id']);
				//����ʹ�ô���
				$date_config_obj->update_use_times($direct_confirm_id);
			}
		
			
			return true;
		}
	}
	
	return false;
}

/**
 * 
 * Լ��ȷ��
 * @param $date_id Լ�������
 * @param $date_status ����״̬
 * @param $user_id ���ݵ�ǰ��½���û�ID��������֤���û��Ƿ���Ȩ��ȥ����Լ��״̬���жϷ�ʽ��Լ���ı�Լ����ID�Ƿ����USERID
 * return -1����������쳣��-2����״̬�쳣��-3֧��״̬�쳣��1Լ�ĳɹ�
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
				$send_data ['card_text1'] = '��ͨ��' . $cameraman_nickname . '��Լ������,��׼ʱ���ֳ�ɨ��ǩ��';
				$send_data ['card_title'] = 'ɨ��ǩ��';
				
				$to_send_data ['media_type'] = 'card';
				$to_send_data ['card_style'] = 2;
				$to_send_data ['card_text1'] = '��ͨ�����Լ������,��׼ʱ���ֳ�ɨ��ǩ��';
				$to_send_data ['card_title'] = '��ʾǩ����';
				
				
				//΢��֪ͨ
				$user_id = $cameraman_user_id;
				$template_code = 'G_PAI_WEIXIN_DATE_ACCEPTED';
				$weixin_data = array ('datetime' => date ( "Y��n��j�� H:i", $date_info ['date_time'] ), 'address' => $date_info ['date_address'], 'nickname' => $model_nickname );
				$version_control = include ('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
				$cache_ver = trim ( $version_control ['wx'] ['cache_ver'] );
				$to_url = "http://yp.yueus.com/m/wx?{$cache_ver}#mine/consider_details_camera/{$date_id}";
				
			
				
				$new_date_info = get_date_info ( $date_id );
				$code_info = $code_obj->get_code_by_enroll_id ( $new_date_info ['enroll_id'] );
				$activity_code = $code_info [0] ['code'];
				
				$phone = $user_obj->get_phone_by_user_id ( $cameraman_user_id ); //��Ӱʦ�ֻ�����
				$data = array ('mt_nickname' => $model_nickname, //ģ���ǳ�
'activity_code' => activity_code_transfer ( $activity_code ) ); //������
				
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
			$send_data ['card_text1'] = '�Ѿܾ�' . $cameraman_nickname . '��Լ������';
			$send_data ['card_title'] = '�鿴Լ������';
			
			$to_send_data ['media_type'] = 'card';
			$to_send_data ['card_style'] = 2;
			$to_send_data ['card_text1'] = '�ܾ����Լ������';
			$to_send_data ['card_title'] = '�鿴Լ������';
			
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
 * �ύԼ��ȡ������
 * 
 * return ģ�ؽ��ܺ���Ӱʦ�˿�  1Ϊ�ύ�ɹ�,-1Ϊģ�ػ�δ��������,-2����ȡ����¼,-3Ϊ�ȯ�ѱ�ɨ��
 * return ģ�ػ�û����ǰ��Ӱʦ�˿� 1Ϊ�ɹ���-1Ϊ��������-2Ϊ�˿�ʧ�ܣ�-3Ϊ״̬�쳣
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
 * ģ�ػ�û����ǰ��Ӱʦ�˿�
 * return 1Ϊ�ɹ���-1Ϊ��������-2Ϊ�˿�ʧ�ܣ�-3Ϊ״̬�쳣
 */
function submit_date_refund($date_id, $reason, $remark)
{
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	$ret = $date_cancel_obj->submit_date_refund ( $date_id, $reason, $remark );
	return $ret;
}

/*
 * ����ͬ��״̬
 * 
 * return 1Ϊ�ɹ���-1Ϊ���ѻظ���Ӱʦȡ�����룬  -2Ϊϵͳ״̬�쳣
 */
function update_agree_status($date_id, $agree_status)
{
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	$ret = $date_cancel_obj->update_agree_status ( $date_id, $agree_status );
	return $ret;
}

/*
 * ǿ���˿�
 * 
 * return 1Ϊ�ɹ�,-1Ϊģ��ͬ��״̬�쳣��-2Ϊ�˿�ʧ�ܣ�����ϵ����Ա  ��-3Ϊϵͳ״̬�쳣
 */
function update_force_refund_status($date_id)
{
	$date_cancel_obj = POCO::singleton ( 'event_date_cancel_class' );
	$ret = $date_cancel_obj->update_force_refund_status ( $date_id );
	return $ret;
}

/*
 * ��Ӱʦ�ҵģ�Լ��������ʾ��
 */
function count_yuepai_order_num($user_id)
{
	$user_id = (int)$user_id;
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	
	$user_id = get_relate_poco_id($user_id);
	$enroll_list = get_enroll_list("user_id={$user_id} and pay_status=1 and remark='˽��Լ��'", false, '0,10000', 'enroll_id DESC',"event_id");
	
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