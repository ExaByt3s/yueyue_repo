<?php
/** 
 * 
 * 峰会获取验证码异步文件
 * 
 * authro 星星
 * 
 * 
 * 2015-4-3
 */
    
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$ajax_status = 1;

$phone = (int)$_INPUT['phone'];
if(empty($phone))
{
    $ajax_status = 0;
}

if($ajax_status>0)
{
    //拿验证码代码
    //发送校验码
    $pai_sms_obj = POCO::singleton ( 'pai_sms_class' );
    

    //发送验证码
    $phone = $phone;
    $group_key = 'G_PAI_TOPIC_MEETING_VERIFY';
    $data = array();
    $ret = $pai_sms_obj->send_verify_code($phone, $group_key, $data);
    if($ret)
    {
        $ajax_status = 1;
    }
    else
    {
        $ajax_status = 0;
    }
    

}




$res_arr = array(
"ajax_status"=>$ajax_status
);

echo json_encode($res_arr);


?>