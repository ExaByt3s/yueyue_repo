<?php
/** /* 
 * 获取用户的绑定激活码
 * 
 * author 星星
 * 
 * 2015-1-14
 */



include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$ajax_status = 1;


$yue_phone = (int)$_INPUT['yue_phone'];

if($ajax_status>0)
{
    //拿验证码代码
    //发送校验码
    $pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
    $ret = $pai_sms_obj->send_phone_reg_verify_code ($yue_phone);
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