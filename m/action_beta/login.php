<?php
/*
    ��¼ҳ��
*/
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$account = trim(intval($_INPUT['account']));
$password = trim($_INPUT['password']);
$is_new_user = trim($_INPUT['is_new_user']);
$redirect_url = trim($_INPUT['redirect_url']);

$yue_open_id = $_COOKIE['yueus_openid'];

$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
$pai_user_obj = POCO::singleton('pai_user_class');	
$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');


if(!$yue_open_id)
{
	$state_info = array('url' => $redirect_url);
	$url = $weixin_pub_obj->auth_get_authorize_url($state_info);
	
	$return_arr['result'] = 501;
	$return_arr['msg'] = '���ȵ�΢����Ȩ';
	$return_arr['auth_url'] = $url;
	mobile_output($return_arr, false);
	exit;
}

$return_arr = array();
$user_id = 0;


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

$check_open_id_bind = $bind_weixin_obj->get_bind_info_by_open_id($yue_open_id);



//��¼��֤
$user_id = $pai_user_obj->user_login($account, $password);


if(!$user_id){

	$return_arr['result'] = 500;
	$return_arr['msg'] = '�˺Ż��������';
	mobile_output($return_arr, false);
	exit();
	
}

//�жϽ�ɫ
$role = $pai_user_obj->check_role($user_id);

if($role!='cameraman'){

	$return_arr['result'] = 502;
	$return_arr['msg'] = '��Ӱʦ���ܵ�¼Ŷ';
	$pai_user_obj->logout();//�ǳ�
	mobile_output($return_arr, false);
	exit();
	
}

//���΢���Ƿ��а󶨹�ԼԼ
if($check_open_id_bind)
{
	
	if($check_open_id_bind['user_id']!=$user_id)
	{
		$return_arr['result'] = 500;
		$return_arr['msg'] = '��΢�ź��Ѱ󶨹�ԼԼ��';
		$pai_user_obj->logout();//�ǳ�
		mobile_output($return_arr, false);
		exit();
	}
}

$check_user_bind = $bind_weixin_obj->get_bind_info_by_user_id($user_id);


//���ԼԼ�Ƿ��а󶨹�΢��
if($check_user_bind)
{
	if($check_user_bind['open_id']!=$yue_open_id)
	{
		$return_arr['result'] = 500;
		$return_arr['msg'] = '��ԼԼ���Ѱ󶨹�΢����';
		$pai_user_obj->logout();//�ǳ�
		mobile_output($return_arr, false);
		exit();
	}
}

//�Ҳ����û�ID�����Ҳ���OPENID��ȥ��
if(!$check_user_bind && !$check_open_id_bind)
{
	$bind_data['user_id'] = $user_id;
	$bind_data['open_id'] = $yue_open_id;
	$ret = $bind_weixin_obj->add_bind($bind_data);
	if($ret)
	{
		$weixin_user_info = $weixin_pub_obj->get_weixin_user($yue_open_id);
		$cellphone = $pai_user_obj->get_phone_by_user_id($user_id);
		
		$template_code = 'G_PAI_WEIXIN_USER_BIND';
		$data = array('nickname'=>$weixin_user_info['nickname'], 'cellphone'=>$cellphone);
		$weixin_pub_obj->message_template_send_by_user_id($user_id, $template_code, $data);
	}
	
}

$is_go_to_edit = false;


$return_arr['result'] = 200;
$return_arr['uid'] = $user_id;
$return_arr['user_id'] = $user_id;
$return_arr['access_token'] = $user_id;
$return_arr['access_token_secret'] = $user_id;
$return_arr['msg'] = '��¼�ɹ�';
$return_arr['is_go_to_edit'] = $is_go_to_edit;

$return_arr['user_info'] = $pai_user_obj->get_user_info_by_user_id($user_id);

mobile_output($return_arr,false);

