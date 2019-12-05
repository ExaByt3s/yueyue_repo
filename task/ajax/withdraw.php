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
function out_put( $data,$code,$msg ){

	$output_arr['data'] = $data;
	$output_arr['code'] = $code;
	$output_arr['msg']  = $msg;
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
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$phone = $task_seller_obj->get_seller_cellphone($yue_login_id);
		if( empty($phone) )
			out_put( '', 0, '���Ȱ��̼��ֻ�' );
		$ret = $pai_sms_obj->send_withdraw_verify_code($phone);
		$ret?out_put( '',1,'�ɹ����Ͷ�����֤��' ):out_put( '',0,'���ʹ���' );
	break;
	case 'money':
		
		out_put( '',0,'�뵽APP��ȥ����' );
		
		$pai_payment_obj 	  = POCO::singleton('pai_payment_class');
		$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
		$sms_code 	  	 = intval($_INPUT['sms_code']);
		$is_money   	 = 1;//intval($_INPUT['is_money']);
		$withdraw_type   = $is_money==1?'withdraw':'bail';
		$amount 		 = $_INPUT['amount'] * 1;
		
		//����ǰ̨Ҫ���ύ���ֵ  				
		$third_type      = $_INPUT['third_type'];
		$third_type 	 = 'alipay';

		$bind_info 	     = $pai_bind_account_obj->get_info_by_user_id($yue_login_id,'alipay_account');
		if( empty($bind_info)  )
			die('�û�δ��');

		$real_name   	 = $bind_info['real_name'];
		$third_account   = $bind_info['third_account'];
		if( $amount<$pai_payment_obj->get_min_seller_withdraw_amount() || $amount>$pai_payment_obj->get_max_seller_withdraw_amount() ){

			out_put( '',0,'���ֽ��ֻ����10-50000֮��');
			exit();

		}
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$phone = $task_seller_obj->get_seller_cellphone($yue_login_id);
		$phone==''&& out_put( '',0,'���Ȱ��̼��ֻ�' );

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
		if( $ret_today>=10 )
		{
			out_put( '',0,'ԼԼΪ�������˺Ű�ȫ��ÿ��ֻ������ʮ�Σ�����������ϵ4000-82-9003' );
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