<?php
/**
 * ����΢��(11)������Ϣ
 * @author ��ʯ��
 * @copyright 2014-12-31
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once(G_YUEYUE_ROOT_PATH . "/system_service/sms_service/poco_app_common.inc.php");

// ��ϢID
$msg_id = trim($_INPUT['MsgId']);
// �����ط��ź���
$up_num = trim($_INPUT['Up_YourNum']);
// �ͻ����е��ֻ�����
$up_service_num = trim($_INPUT['Up_UserTel']);
// �ͻ����еĶ�������
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


