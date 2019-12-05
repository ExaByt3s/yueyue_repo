<?php
/** /* 
 * 获取用户的绑定情况
 * 
 * author 星星
 * 
 * 2015-1-14
 */
/****防止攻击2015-11-20添加****/
$res_arr = array(
    "ajax_status"=>1
);

echo json_encode($res_arr);
die();
/****防止攻击2015-11-20添加****/
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$ajax_status = 1;
$action = trim($_INPUT['action']);


$yue_phone = (int)$_INPUT['phone_num'];
$action_array = array("register","change_pwd");
if(empty($yue_phone))
{
    $ajax_status = 0;
}
if(!in_array($action,$action_array))
{
    $ajax_status = 0;

}


if($ajax_status>0)
{
    //拿验证码代码
    //发送校验码
    $pai_sms_obj = POCO::singleton ( 'pai_sms_class' );

	//发现有攻击，简单防一下 2015-10-09
    if($action=="register" && !in_array($_SERVER['HTTP_USER_AGENT'], array('Mozilla/5.0', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)', 'Apache-HttpClient/UNAVAILABLE (java 1.4)'), true) && strpos($_SERVER['HTTP_USER_AGENT'], 'Dalvik/')===false)
    {
        $ret = $pai_sms_obj->send_phone_reg_verify_code ($yue_phone);
    }
    else if($action=="change_pwd" && !in_array($_SERVER['HTTP_USER_AGENT'], array('Mozilla/5.0', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)', 'Apache-HttpClient/UNAVAILABLE (java 1.4)'), true) && strpos($_SERVER['HTTP_USER_AGENT'], 'Dalvik/')===false)
    {
        $group_key = 'G_PAI_USER_PASSWORD_VERIFY';
        $ret = $pai_sms_obj->send_verify_code($yue_phone, $group_key, array());
    }



    if($ret)
    {
        $ajax_status = 1;
    }
    else
    {
        $ajax_status = 0;
    }



}



    /**
     * 发送注册校验码短信
     * @param string $phone
     * @param int $user_id
     * @return boolean
     */
    //public function send_phone_reg_verify_code($phone, $user_id=0)

//检查验证码
//$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
//$ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $verify_code, 0, false );


    /**
     * 校验注册校验码
     * @param string $phone
     * @param string $verify_code
     * @param int $user_id
     * @param boolean $b_del_verify_code
     * @return boolean
     */
    //public function check_phone_reg_verify_code($phone, $verify_code, $user_id=0, $b_del_verify_code=true)
//拿验证码代码


$res_arr = array(
"ajax_status"=>$ajax_status
);

echo json_encode($res_arr);
?>