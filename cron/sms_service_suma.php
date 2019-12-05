<?php
/**
 * 接收速码(16)上行信息
 * @author 黄石汉
 * @copyright 2014-12-31
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once(G_YUEYUE_ROOT_PATH . "/system_service/sms_service/poco_app_common.inc.php");

$class_sms_obj = POCO::singleton('class_sms_v2');

$up_list = $class_sms_obj->get_up_list(16);

pai_log_class::add_log(array('result' => $up_list), 'suma_notify', 'sms_suma_service');


if( !is_array($up_list) || empty($up_list) )
{
    exit();
}

$sms_service_receive_obj = POCO::singleton('sms_service_receive_class');

foreach ( $up_list as $v )
{
    $more_info = array(
        'msgid' => '',
        'receive_date' => $v[2],
    );
    $ret = $sms_service_receive_obj->submit_receive("16", $v[0], $v[1], $v[3], $more_info);
}

$date = date ( "Y-m-d H:i:s" );
echo '获取速码16通道回复信息' . $date;