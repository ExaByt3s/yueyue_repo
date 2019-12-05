<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/8/19
 * Time: 11:18
 */
exit();
include_once "../poco_app_common.inc.php";

$sql_str = "SELECT * FROM pai_log_db.check_user_version_tbl WHERE is_send=0";
$result = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    if(check_send_msg_num($val['user_id']) > 5) conncontinue;
    $pai_sms_obj = POCO::singleton('pai_sms_class');
    $tel = (int)$val['user_tel'];
    if($tel == 15813331110) $tel = 15813311352;
    if($tel)
    {
        $sms_data = array(
            'datetime' => date('H:i:s', time()),
        );
        if($pai_sms_obj->send_sms($tel, 'G_PAI_MALL_SELLER_CHAT', $sms_data))
        {
            $sql_str = "UPDATE pai_log_db.check_user_version_tbl SET is_send=1 WHERE id= $val[id]";
        }else{
            $sql_str = "UPDATE pai_log_db.check_user_version_tbl SET is_send=2 WHERE id= $val[id]";
        }
        db_simple_getdata($sql_str, TRUE, 101);
    }
}

function check_send_msg_num($user_id)
{
    $sql_str = "SELECT COUNT(*) AS C FROM pai_log_db.check_user_version_tbl WHERE user_id=$user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    return $result['C'];
}
