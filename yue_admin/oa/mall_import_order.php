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
	die ( "û������" );
}
 

$order_info = $order_list [0];
$id = $order_info ['id'];
$oa_order_id = $order_info ['oa_order_id'];


//������Ӧ������Ƿ�����̼ҽ��һ��
$oa_order_info = $model_oa_order_obj->get_order_info($oa_order_id);
$payable_amount = $oa_order_info['payable_amount'];

$import_seller_list = $import_order_obj->get_order_list ( false, "oa_order_id={$oa_order_id}", 'id ASC', '0,1000','sum(price) as sum' );

if($import_seller_list[0]['sum']!=$payable_amount)
{
    //echo "<script>alert('�̼�Ӧ����������Ӧ���������������޸�');</script>";
    $import_order_obj->update_order ( array ("log" => "�̼�Ӧ����������Ӧ���������������޸�" ), $id );
    exit;
}


//����ִ�еĸ���״̬Ϊ2��
$import_order_obj->update_order ( array ("status" => 2 ), $id );

$check_model_phone = $user_obj->check_cellphone_exist ( $order_info ['model_phone'] );

if (! $check_model_phone)
{
	$model_reg_data ['nickname'] = $order_info ['model_nickname'];
	$model_reg_data ['cellphone'] = $order_info ['model_phone'];
	$model_reg_data ['pwd'] = 'yueus123456';
	$model_reg_data ['role'] = "cameraman";
	
	$seller_user_id = $user_obj->create_mall_account ( $model_reg_data, $err_msg );
	$log .= "�����̼ҳɹ�:" . $seller_user_id . "\n";

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
	$log .= "���������߳ɹ�:" . $buyer_user_id . "\n";

}
else
{
	$buyer_user_id = $user_obj->get_user_id_by_phone ( $order_info ['cameraman_phone'] );
}


$org_info = $relate_org_obj->get_org_info_by_user_id($seller_user_id);
$org_user_id = intval($org_info['org_id']);

if($org_user_id && $order_info ['withdraw']==1)
{
	$log .= "����������̼���������:" . $org_user_id . "\n";
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
  	'goods_id' => $goods_id, //��ƷID
  	'prices_type_id' => '',
  	'service_time' => strtotime($order_info['service_time']), //����ʱ��
  	'service_location_id' => $order_info['service_location_id'],
  	'service_address' => $order_info['service_address'],
   'service_people' => 1,
   'prices' => $order_info['price'], //���ۣ���������������������
  	'quantity' => 1, //����
  ));
  
 $more_info = array( 
  	'seller_user_id' => $seller_user_id, //�����û�ID����������������������
  	'description' => '', //��������ע
   'is_auto_accept' => 1, //�Ƿ��Զ����ܣ��µ���֧�������ܲ�����֪ͨ
   'is_auto_sign' => 1, //�Ƿ��Զ�ǩ����ǩ�������۲�����֪ͨ
   'referer' => 'oa', //������Դ��app weixin pc wap oa
  );
	  
$mall_order_ret = $mall_order_obj->submit_order($buyer_user_id, $detail_list, $more_info);

$mall_order_id = $mall_order_ret['order_id'];
if($mall_order_id)
{
    $log .= "�̳Ƕ���ID:" . $mall_order_id . "\n";
}
else
{
    $log .= "�̳Ƕ���ID:" . $mall_order_ret['message'] . "\n";
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
						"subject"=>"˽�˶���"
	);
	
	//��ֵ
	$pay_ret = $pai_payment_obj->manual_recharge ( $recharge_type, $buyer_user_id, $amount, $mall_order_id, $mall_order_id, 0, $more_info );
	$log .= "��ֵ״̬:" . $pay_ret['message'] . "\n";
	$import_order_obj->update_order ( array ("log" => $log ), $id );
	
	
	//��ֵ�ɹ�
	if ($pay_ret ['error'] == 0)
	{
		//֧��
		$payment_info = $pai_payment_obj->get_payment_info($pay_ret['payment_no']);
		$p_ret = $mall_order_obj->pay_order_by_payment_info($payment_info);
		$log .= "֧��״̬:" . $p_ret['message'] . "\n";
		$import_order_obj->update_order ( array ("log" => $log ), $id );
		
		//֧���ɹ�
		if($p_ret['result']==1)
		{
			//�����̼�
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
			$log .= "�����̼�:" . $seller_comment_ret['message'] . "\n";
			$import_order_obj->update_order ( array ("log" => $log ), $id );
			
			//����������
			$buyer_comment_data ['from_user_id'] = $seller_user_id;
			$buyer_comment_data ['to_user_id'] = $buyer_user_id;
			$buyer_comment_data ['order_id'] = $mall_order_id;
			$buyer_comment_data ['goods_id'] = $goods_id;
			$buyer_comment_data ['overall_score'] = $order_info ['buyer_overall_score'];
			$buyer_comment_data ['comment'] = $order_info ['buyer_comment'];
			$buyer_comment_ret = $mall_comment_obj->add_buyer_comment($buyer_comment_data);
			$log .= "����������:" . $buyer_comment_ret['message'] . "\n";
			$import_order_obj->update_order ( array ("log" => $log ), $id );
			
			if($order_info ['withdraw']==1)
			{
				$withdraw_id = $pai_payment_obj->submit_withdraw ( 'withdraw', $seller_user_id, $amount, "����֧��", 'manual', "OA����{$oa_order_id}" );
				$withdraw_ret = $ecpay_pai_withdraw_obj->check_apply ( $withdraw_id, "����֧�� OA����{$oa_order_id}" );
				$log .= "����״̬:" . $withdraw_ret;
			}
		}
		
		if ($id)
		{	
			//��ɵ������״̬Ϊ1
			$time = time();
			$import_order_obj->update_order ( array ("log" => $log, "status" => 1,"mall_order_id"=>$mall_order_id,"import_time"=>$time ), $id );
		}
	}

}



echo $log;

?>