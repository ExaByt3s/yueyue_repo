<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/7/14
 * Time: 13:54
 */
require_once('../poco_app_common.inc.php');
include_once(G_YUEYUE_ROOT_PATH . "/system_service/sms_service/poco_app_common.inc.php");

$sms_obj = POCO::singleton('class_sms_v2');
$remain = $sms_obj->get_remain(11);

//var_dump($remain);
$log_time = date('Y-m-d H:i:s');
$task_id = 10002;
$mon_type = 'HTTP';

$log_message = "短信剩余量：" . $remain;
$log_content = "短信剩余量：" . $remain;

if($remain > 5000)
{
    $sql_str = "INSERT INTO monitor_service_for_yueyue_db.mon_log_tbl(log_time, task_id, mon_type, ok_status, log_message, log_content)
                VALUES ('{$log_time}', '{$task_id}', '{$mon_type}', 'OK', '{$log_message}', '{$log_content}')";
}else{
    $sql_str = "INSERT INTO monitor_service_for_yueyue_db.mon_log_tbl(log_time, task_id, mon_type, ok_status, log_message, log_content)
                VALUES ('{$log_time}', '{$task_id}', '{$mon_type}', 'ERR', '{$log_message}', '{$log_content}')";
}
db_simple_getdata($sql_str, TRUE, 6);

check_slave_server();
function check_slave_server()
{
    $log_time = date('Y-m-d H:i:s');
    $task_id = 10005;
    $mon_type = 'HTTP';

    $sql_str = "SELECT ABS(UNIX_TIMESTAMP(NOW())-update_time) AS NUM_1 ,DATE_FORMAT(FROM_UNIXTIME(update_time),'%Y-%m-%d %T')  AS wdate FROM mall_db.`mall_goods_updata_log_tbl` ORDER BY update_time DESC LIMIT 0, 1";
    $result = db_simple_getdata($sql_str, FALSE, 102);

    if($result['NUM_1'] < 600)
    {
        $log_message = "同步正常";
        $log_content = "同步正常";
        $sql_str = "INSERT INTO monitor_service_for_yueyue_db.mon_log_tbl(log_time, task_id, mon_type, ok_status, log_message, log_content)
                VALUES ('{$log_time}', '{$task_id}', '{$mon_type}', 'OK', '{$log_message}', '{$log_content}')";
    }else{
        $log_message = "同步异常";
        $log_content = "同步异常";
        $sql_str = "INSERT INTO monitor_service_for_yueyue_db.mon_log_tbl(log_time, task_id, mon_type, ok_status, log_message, log_content)
                VALUES ('{$log_time}', '{$task_id}', '{$mon_type}', 'ERR', '{$log_message}', '{$log_content}')";
    }
    db_simple_getdata($sql_str, TRUE, 6);
}
