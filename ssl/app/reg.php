<?php
/*
 * ע��
 */
require('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
// ��ȡ�ͻ��˵�����
$cp = new yue_protocol_system();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input ( array ('be_check_token' => false ) );

$phone = $client_data ['data'] ['param'] ['phone'];
$pwd = $client_data ['data'] ['param'] ['pwd'];
$verify_code = $client_data ['data'] ['param'] ['verify_code'];
$role = $client_data ['data'] ['param'] ['role'];
$app_name = $client_data ['data'] ['param'] ['app_name'];
$app_name = empty($app_name) ? $client_data ['data'] ['app_name'] : $app_name;
$app_name = $app_name ? $app_name : 'poco_yuepai_android';


//��LOG  ���� http://yp.yueus.com/logs/201501/28_ssl_reg.txt
//$log_arr['phone'] = $phone;
//$log_arr['client_data'] = $client_data;
//pai_log_class::add_log($log_arr, 'ssl_reg_ready', 'ssl_reg');

if (! $phone)
{
	yue_app_out_put ( 'phone_is_null',0 );
}

if (! $pwd)
{
	yue_app_out_put ( 'pwd_is_null',0 );
}

if (! preg_match ( "/^.{6,31}$/", $pwd ))
{
	yue_app_out_put ( 'pwd_is_error',0 );
}

if (! in_array ( $role, array ('model', 'cameraman' ) ) || empty ( $role ))
{
	yue_app_out_put ( 'role_choose_error',0 );
}

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
$check_phone_ret = $pai_user_obj->check_phone_format ( $phone );

if (! $check_phone_ret)
{
	yue_app_out_put ( 'phone_format_error',0 );
}

if (strlen ( $verify_code ) < 1)
{
	yue_app_out_put ( 'verify_code_error' ,0);
}


$user_info = $pai_user_obj->get_user_by_phone ( $phone );
//����û��Ƿ���ģ�ؿ⵼���
if ($user_info ['pwd_hash'] == 'poco_model_db')
{
	$is_model_db = true;
}

if(!$is_model_db)
{
	//���˾Ͳ����ٰ� 
	$check_phone_ret = $pai_user_obj->check_cellphone_exist ( $phone );
	if ($check_phone_ret)
	{
		yue_app_out_put ( 'reg_success', 1 );
	}
	
}

//�����֤��
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
$ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $verify_code, 0, false );
if (! $ret)
{
	yue_app_out_put ( 'verify_code_check_error',0 );
}

//ģ�ؿ��û����⴦��
if($is_model_db)
{
	$ret = $pai_user_obj->update_pwd_by_phone ( $phone, $pwd );
	if ($ret)
	{
		$user_id = $pai_user_obj->get_user_id_by_phone($phone);
		//���û���¼
		$pai_user_obj->load_member ( $user_id );
		

		//ģ�ؿ�ע���Ҫ������Դ�ͼ���ʱ��
		$update_data['add_time'] = time();
		$update_data['reg_from'] = 'app';
		$update_data['role'] = $role;
		$pai_user_obj->update_user_by_phone($update_data, $phone);
		
	}
}
else
{

	if($role == 'model')
	{
		$name = "ģ��_";
	}
	elseif($role == 'cameraman')
	{
		$name = "��Ӱʦ_";
	}
	
	//ע���ʺ�
	$user_info_arr ['nickname'] = $name . substr ( $phone, - 4 );
	$user_info_arr ['cellphone'] = $phone;
	$user_info_arr ['role'] = $role;
	$user_info_arr ['pwd'] = $pwd;
	$user_info_arr ['reg_from'] = "app";
	$user_id = $pai_user_obj->create_account ( $user_info_arr, $err_msg );
	
	if ($user_id < 1)
	{
		yue_app_out_put ( 'reg_error' ,0);
	}
	else
	{
		
		if ($role == 'model')
		{
			$model_card_obj = POCO::singleton ( 'pai_model_card_class' );
			$insert_model ["user_id"] = $user_id;
			$model_card_obj->add_model_card ( $insert_model );
		}
		else
		{
			$cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );
			$insert_cameraman ["user_id"] = $user_id;
			$cameraman_card_obj->add_cameraman_card ( $insert_cameraman );
		}
		
		//���û���¼
		$pai_user_obj->load_member ( $user_id );
		
		$pai_user_obj->add_bind_phone_log ( $user_id, $phone, 'BIND_PHONE' );
		
	}

}

//����֤��
$pai_sms_obj->check_phone_reg_verify_code ( $phone, $verify_code );

$log_arr2['user_id'] = $user_id;
$log_arr2['phone'] = $phone;
$log_arr2['client_data'] = $client_data;
pai_log_class::add_log($log_arr2, 'ssl_reg_success', 'ssl_reg');

yue_app_out_put ( 'reg_success', 1 );


?>