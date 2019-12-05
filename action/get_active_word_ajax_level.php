<?php
/**
 * ��ȡ�û��ĵȼ�
 *
 * author ��ʯ��
 *
 * 2015-10-21
 */
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');

//���� �ж��Ƿ�Ϊ AJAX ����
if( !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest' )
{
	$res_arr = array("ajax_status" => -1, "level" => -1);
	echo json_encode($res_arr);
	exit();
}

//���� ��֤������ ������֤ taken�����Ƿ���ȷ�������֤���ɹ�������json���˳��ű�
$validation_code_obj = POCO::singleton('validation_code_class');
$token_ajax = trim($_INPUT['token']);
if( strlen($token_ajax)<1 || $validation_code_obj->get_hash()!=$token_ajax )
{	
	$res_arr = array("ajax_status" => -1, "level" => -1);
	echo json_encode($res_arr);
	exit();
}

//����action��ֵ 9de4a97425678c5b1288aa70c1669a64  = md5('register')���ж�action����ƥ��ƥ��md5ֵ  # ��������ֻ���������ȷ
$action = trim($_INPUT['action']);
$yue_phone = (int)$_INPUT['phone_num'];
if( !in_array($action, array( md5("register"), md5("change_pwd"))) || !preg_match('/^1\d{10}$/', $yue_phone) )
{
	$res_arr = array("ajax_status" => -1, "level" => -1);
	echo json_encode($res_arr);
	exit();
}

//����ֻ��Ƿ���ע���ˣ����ע���˾ͷ�����Ϣ
$user_obj = POCO::singleton ( 'pai_user_class' );
if ( $action == md5("register") )
{
	$ret = $user_obj->check_cellphone_exist($yue_phone);
	if ( $ret )
	{
		$msg = mb_convert_encoding("���ֻ����Ѿ�ע���ԼԼ��", 'utf-8', 'gbk');
		$res_arr = array("ajax_status" => -2, "level" => -2, "msg" => $msg);
		echo json_encode($res_arr);
		exit();
	}
}

//����ֻ��Ƿ���ע���ˣ����ע���˾ͷ�����Ϣ
if ( $action == md5("change_pwd") )
{
	$ret = $user_obj->check_cellphone_exist($yue_phone);
	if ( !$ret )
	{
		$msg = mb_convert_encoding("���ֻ��Ż�û��ע��ԼԼ��", 'utf-8', 'gbk');
		$res_arr = array("ajax_status" => -2, "level" => -2, "msg" => $msg);
		echo json_encode($res_arr);
		exit();
	}
}


//����get_level()������֤�ȼ� - �˷������ж����Ƿ�wap��web pc���� ����ֵΪ0ʱ����ʾ΢������2Ϊwap��web pc����
$level = $validation_code_obj->get_level();
if( $level==1 )
{
	//��ʱ��0����
}
elseif( $level==2 )
{
	$res_arr = array("ajax_status" => 1, "level" => 2);
	echo json_encode($res_arr);
	exit();
}

//$levelΪ0�������
$pai_sms_obj = POCO::singleton('pai_sms_class');
if( $action==md5("register") )
{
	$ret = $pai_sms_obj->send_phone_reg_verify_code($yue_phone);
}
elseif( $action==md5("change_pwd") )
{
	$group_key = 'G_PAI_USER_PASSWORD_VERIFY';
	$ret = $pai_sms_obj->send_verify_code($yue_phone, $group_key, array());
}
else 
{
	$ret = false;
}
//�����ݱ�ʾ���ͳɹ� ���� 1 ��ʾ���ͳɹ� 2 ��ʾ�з��ͣ���û�гɹ�
if( $ret )
{
	$res_arr = array("ajax_status" => 1, "level" => 0 , 'send_status'=>1);
}
else
{
	$res_arr = array("ajax_status" => 1, "level" => 0 , 'send_status'=>2);
}
echo json_encode($res_arr);
