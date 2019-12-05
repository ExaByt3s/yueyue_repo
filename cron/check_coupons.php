<?php
/**
 * Created by PhpStorm.
 * User: 何耀华
 * Date: 2015/10/10
 * Time: 15:15
 */

include_once ("../poco_app_common.inc.php");

cron_check_coupons_residual_amount();

function cron_check_coupons_residual_amount()
{
    $sql_str = "SELECT balance FROM `ecpay_db`.`pai_account_actual_tbl` WHERE channel_rid=20003";
    $result = db_simple_getdata($sql_str, TRUE, 101);

    $date_time = date('Y-m-d H:i:s', time());

    if($result['balance'] > 10000)
    {
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 10003, 'HTTP', 'None',0,'OK', 0, '优惠券资金充足 {$result[balance]}', '剩余金额：{$result[balance]}')";
    }else{
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 10003, 'HTTP', 'None',0,'ERROR', 0, '优惠券资金不足 {$result[balance]}', '剩余金额：{$result[balance]}')";
    }

    db_simple_getdata($sql_str, TRUE, 6);
}