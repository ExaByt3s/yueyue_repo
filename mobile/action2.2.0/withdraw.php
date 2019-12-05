<?php
/**
 * ���ֲ��ҷ�����
 * 2014.9.12 hudw
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
if(empty($yue_login_id))
{
	die('no login');
}
$type 		 = trim($_INPUT['type']);
$pai_sms_obj = POCO::singleton('pai_sms_class');
function out_put($data, $code, $msg, $more_info=array())
{
	if( !is_array($more_info) ) $more_info = array();
	$output_arr['data'] = $data;
	$output_arr['code'] = $code;
	$output_arr['msg']  = $msg;
	$output_arr = array_merge($more_info, $output_arr);
	mobile_output($output_arr,false);
	exit();
}
switch ($type) 
{
	case 'check_bind':
		$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
		$user_id    = $yue_login_id;
		$type       = 'alipay_account';
		$ret 		= $pai_bind_account_obj->get_bind_status($user_id,$type);
		//-1 δ�� 0 ����� 1����� 2��˲�ͨ��
		switch ($ret['status']) {
			case '-1':
				$output_arr['code'] = -1;
				$output_arr['msg']  = 'δ��֧�����˺�';
				$output_arr['data'] = array();	
				break;
			case '0':
				$output_arr['code'] = 0;
				$output_arr['msg']  = '�����';
				$output_arr['data'] = array('third_account'=>$ret['third_account']);	
				break;
			case '1':
				$output_arr['code'] = 1;
				$output_arr['msg']  = '�����';
				$output_arr['data'] = array('third_account'=>$ret['third_account']);	
				break;
			case '2':
				$output_arr['code'] = 2;
				$output_arr['msg']  = '��˲�ͨ��';
				$output_arr['data'] = array();					
				break;														
			default:
				break;
		}
		mobile_output($output_arr,false);
	break;
	case 'sms':
		$pai_user_obj = POCO::singleton('pai_user_class');
		$phone 		  = $pai_user_obj->get_phone_by_user_id($yue_login_id);
		if( empty($phone) )
		{
			out_put('', 0, '���Ȱ��ֻ�');
		}
		$ret = $pai_sms_obj->send_withdraw_verify_code($phone);
		if( !$ret )
		{
			out_put('', 0, '���ʹ���');
		}
		$phone_str = preg_replace('/(\d{3})(\d{4})(\d{4})/i', '$1****$3', $phone);
		$data = '��֤�뷢�͵�����½���ֻ��� <div>'.$phone_str.'</div></div>';
		out_put($data, 1, '�ɹ����Ͷ�����֤��', array());
	break;
	case 'money':
		$pai_payment_obj 	  = POCO::singleton('pai_payment_class');
		$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
		$sms_code 	  	 = intval($_INPUT['sms_code']);
		$pai_user_obj 	 = POCO::singleton('pai_user_class');
		$is_money   	 = intval($_INPUT['is_money']);
		$withdraw_type   = 'withdraw';//$is_money==1?'withdraw':'bail';
		$amount 		 = $_INPUT['amount'] * 1;
		
		//����ǰ̨Ҫ���ύ���ֵ  				
		$third_type      = $_INPUT['third_type'];
		$third_type 	 = 'alipay';

		$bind_info 	     = $pai_bind_account_obj->get_info_by_user_id($yue_login_id,'alipay_account');
		if( empty($bind_info)  )
			die('�û�δ��');

		$real_name   	 = $bind_info['real_name'];
		$third_account   = $bind_info['third_account'];
		
		//��ȡ�̼���Ϣ
		$seller_obj = POCO::singleton('pai_mall_seller_class');
		$seller_info = $seller_obj->get_seller_info($yue_login_id, 2);
		if( !empty($seller_info) && $seller_info['seller_data']['status']==1 ) //��Ч�̼�
		{
			$min_amount = $pai_payment_obj->get_min_seller_withdraw_amount();
			$max_amount = $pai_payment_obj->get_max_seller_withdraw_amount();
		}
		else
		{
			$min_amount = $pai_payment_obj->get_min_pay_amount();
			$max_amount = $pai_payment_obj->get_max_pay_amount();
		}
		if( $amount<$min_amount || $amount>$max_amount ){

			out_put( '',0,"���ֽ��ֻ����{$min_amount}-{$max_amount}֮��");
			exit();

		}
		$phone 	= $pai_user_obj->get_phone_by_user_id($yue_login_id);
		$phone==''&& out_put( '',0,'���Ȱ��ֻ�' );

		$ret = $pai_sms_obj->check_withdraw_verify_code($phone,$sms_code,0,false);
		if(!$ret)
			out_put( '',0,'��֤�����' );
		
		$today_time = strtotime(date('Y-m-d 00:00:00'));
		$search_arr = array(
			'user_id' => $yue_login_id,
			'min_add_time' => $today_time,
			'max_add_time' => $today_time,
		);
		$ret_today = $pai_payment_obj->get_withdraw_list_by_search($search_arr, true);
		if( $ret_today>0 )
		{
			out_put( '',0,'��ܰ��ʾ��ÿ�����һ������' );
		}
		
		$ret_last = $pai_payment_obj->submit_withdraw($withdraw_type, $yue_login_id, $amount,$real_name,$third_type, $third_account);
		if( $ret_last > 0 )
		{

			$sms_obj = POCO::singleton('pai_sms_class');
			$data 	 = array(

				 'datetime' => date('Y-m-d H:i'),
				 'amount'   => $amount,

			);
			$ret 	 = $sms_obj->send_withdraw_notice($phone,$data,$yue_login_id);
			$pai_sms_obj->del_withdraw_verify_code($phone);
			out_put( $ret, 1, '�ύ��������ɹ�');

		}
		else if($ret_last == 0)
		{
			out_put( '',0,'����ʧ��' );		
		}
		else if($ret_last == -1)
		{
			out_put( '',0,'���޿����ֵ����' );	
		}
		else if($ret_last == -2)
		{
			out_put( '',0,'��Ŀ���������' );
		}
	break;
}


?>