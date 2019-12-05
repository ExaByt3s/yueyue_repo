<?php
/**
 * ���¹����ǣ�У���첽�ύ�����������Ƿ���ȷ
 * update by 2015-11-17 ��ʯ��
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//���session_id
$yue_session_id = trim($_COOKIE['yue_session_id']);
if( strlen($yue_session_id)<1 )
{
	$res_arr = array("send_status" => "-1");
    echo json_encode($res_arr);
    exit();
}

//��һ�� ����tokenֵ ����֤�Ƿ���ȷ�ż���ִ�У����¹���Ϊ ��֤hashֵ ������ȵĻ�������json Ϊ "send_status" => "-2" ���˳��ű�
include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');
$validation_code_obj = POCO::singleton('validation_code_class');
$token_ajax = trim($_INPUT['token']);
if( strlen($token_ajax)<1 || $validation_code_obj->get_hash()!=$token_ajax )
{
    $res_arr = array("send_status" => "-2");
    echo json_encode($res_arr);
    exit();
}

//�ڶ��� ����action��ֵ 9de4a97425678c5b1288aa70c1669a64  = md5('register')���ж�action����ƥ��ƥ��md5ֵ  # ��������ֻ���������ȷ
$action = trim($_INPUT['action']);
$yue_phone = (int)$_INPUT['phone_num'];
if( !in_array($action, array(md5("register"), md5("change_pwd"))) || !preg_match("/^1\d{10}$/", $yue_phone) )
{
    $res_arr = array("send_status" => "-3");
    echo json_encode($res_arr);
    exit();
}

//���� ����У����
require_once dirname(dirname(__FILE__)) . '/lib/class.geetestlib.php';
$GtSdk = new GeetestLib();

//ȡ��StartCaptchaServlet.php�������cache���ֵ����� Ϊ 1 ʱ ��ʾ��������������
$cache_key = 'G_YUEYUE_GEETEST_GTSERVER_' . $yue_session_id;
if( POCO::getCache($cache_key)==1 )
{
	$value_str = trim($_POST['value']);
	$value_arr = json_decode($value_str, true);
	$geetest_challenge = trim($value_arr['geetest_challenge']);
	$geetest_validate = trim($value_arr['geetest_validate']);
	$geetest_seccode = trim($value_arr['geetest_seccode']);
	if( strlen($geetest_challenge)<1 || strlen($geetest_validate)<1 || strlen($geetest_seccode)<1 )
	{
		$res_arr = array("send_status" => "-4");
		echo json_encode($res_arr);
		exit();
	}
	
    # У������ $_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode']
    $rst = $GtSdk->validate($geetest_challenge, $geetest_validate, $geetest_seccode);
    if( $rst!==true )
    {
    	$res_arr = array("send_status" => "-5");
    	echo json_encode($res_arr);
    	exit();
    }

}
else
{
    /**
     * ��������������Ǽ��ͷ�����û����ͨ�ŵ����
     */
	$value_str = trim($_POST['value']);
	$value_arr = json_decode($value_str, true);
	$geetest_validate = trim($value_arr['geetest_validate']);
	if( strlen($geetest_validate)<1 )
	{
		$res_arr = array("send_status" => "-6");
		echo json_encode($res_arr);
		exit();
	}

    if( !$GtSdk->get_answer($geetest_validate) )
    {
    	$res_arr = array("send_status" => "-7");
    	echo json_encode($res_arr);
    	exit();
    }
}

//������֤�ɹ��� - �ж���ע�ỹ���޸������֧
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
//�����ݱ�ʾ���ͳɹ� ���� 1 ��ʾ���ͳɹ� 2 ��ʾ�з��ͣ���û�гɹ�
$res_arr = array("send_status" => "1");
echo json_encode($res_arr);
