<?php
/*
    ��¼ҳ��
*/
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$account = trim(intval($_INPUT['account']));
$password = trim($_INPUT['password']);
$is_new_user = trim($_INPUT['is_new_user']);

$return_arr = array();
$user_id = 0;

$pai_user_obj = POCO::singleton('pai_user_class');	

// ���ڱ���Ƿ��ǽ�ɫ����
$return_arr['is_role_error'] = false;

//������
if( strlen($account)<1 || strlen($password)<1 )
{
	$return_arr['result'] = 500;
	$return_arr['msg'] = '�ʺŻ�����Ϊ��';
	mobile_output($return_arr, false);
	exit();
}


//��¼��֤
$user_id = $pai_user_obj->user_login($account, $password);


if(!$user_id){

	$return_arr['result'] = 500;
	$return_arr['msg'] = '�˺Ż��������';
	mobile_output($return_arr, false);
	exit();
	
}

$is_go_to_edit = false;



$return_arr['result'] = 200;
$return_arr['uid'] = $user_id;
$return_arr['user_id'] = $user_id;
$return_arr['access_token'] = $user_id;
$return_arr['access_token_secret'] = $user_id;
$return_arr['msg'] = '��¼�ɹ�';
$return_arr['is_go_to_edit'] = $is_go_to_edit;

//ע��token
// ����ͨѶЭ��
require_once('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
// ��ȡ�ͻ��˵�����
$cp = new yue_protocol_system();
$app_name = 'poco_yuepai_android';
// ��ȡ�û�����Ȩ��Ϣ
$access_info = $cp->get_access_info($user_id, $app_name);
$return_arr['app_access_token'] = $access_info['access_token'];
$return_arr['app_expire_time'] = $access_info['expire_time'];

$return_arr['user_info'] = $pai_user_obj->get_user_info_by_user_id($user_id);

mobile_output($return_arr,false);

