<?php
/**
 * 以下功能是，校验异步提交上来的数据是否正确
 * update by 2015-11-17 黄石汉
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//检查session_id
$yue_session_id = trim($_COOKIE['yue_session_id']);
if( strlen($yue_session_id)<1 )
{
	$res_arr = array("send_status" => "-1");
    echo json_encode($res_arr);
    exit();
}

//第一步 接收token值 并验证是否正确才继续执行，以下功能为 验证hash值 如果不等的话，返回json 为 "send_status" => "-2" 并退出脚本
include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');
$validation_code_obj = POCO::singleton('validation_code_class');
$token_ajax = trim($_INPUT['token']);
if( strlen($token_ajax)<1 || $validation_code_obj->get_hash()!=$token_ajax )
{
    $res_arr = array("send_status" => "-2");
    echo json_encode($res_arr);
    exit();
}

//第二步 接收action的值 9de4a97425678c5b1288aa70c1669a64  = md5('register')，判断action数据匹不匹配md5值  # 正则检验手机号正不正确
$action = trim($_INPUT['action']);
$yue_phone = (int)$_INPUT['phone_num'];
if( !in_array($action, array(md5("register"), md5("change_pwd"))) || !preg_match("/^1\d{10}$/", $yue_phone) )
{
    $res_arr = array("send_status" => "-3");
    echo json_encode($res_arr);
    exit();
}

//加载 极客校验类
require_once dirname(dirname(__FILE__)) . '/lib/class.geetestlib.php';
$GtSdk = new GeetestLib();

//取出StartCaptchaServlet.php这里存入cache里的值，如果 为 1 时 表示服务器是正常的
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
	
    # 校验数据 $_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode']
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
     * 以下这种情况就是极客服务器没正常通信的情况
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

//数据验证成功后 - 判断是注册还是修改密码分支
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
//有数据表示发送成功 返回 1 表示发送成功 2 表示有发送，但没有成功
//有数据表示发送成功 返回 1 表示发送成功 2 表示有发送，但没有成功
$res_arr = array("send_status" => "1");
echo json_encode($res_arr);
