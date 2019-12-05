<?php
/**
 * 接收微网(11)上行信息
 * @author 黄石汉
 * @copyright 2014-12-31
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once(G_YUEYUE_ROOT_PATH . "/system_service/sms_service/poco_app_common.inc.php");

// 信息ID
$msg_id = trim($_INPUT['MsgId']);
// 申请特服号号码
$up_num = trim($_INPUT['Up_YourNum']);
// 客户上行的手机号码
$up_service_num = trim($_INPUT['Up_UserTel']);
// 客户上行的短信内容
$up_msg = trim($_INPUT['Up_UserMsg']);

$sms_service_receive_obj = POCO::singleton('sms_service_receive_class');
if (ctype_digit($up_service_num))
{
    $more_info = array(
        'msgid' => $msg_id,
        'receive_date' => date('Y-m-d H:i:s', time()),
    );
    $ret = $sms_service_receive_obj->submit_receive("11", $up_service_num, $up_msg, $up_num, $more_info);
}


