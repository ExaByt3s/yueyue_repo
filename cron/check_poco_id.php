<?php
/**
 * Created by PhpStorm.
 * User: ºÎÒ«»ª
 * Date: 2015/10/10
 * Time: 18:03
 */
include_once ("../poco_app_common.inc.php");

cron_check_poco_id_number();

function cron_check_poco_id_number()
{
    $sql_str = "SELECT COUNT(*) AS c FROM pai_db.pai_temp_poco_id_tbl WHERE STATUS=0";
    echo $sql_str;

    $result = db_simple_getdata($sql_str, TRUE, 101);

    $msg = 'POCOIDÊ£Óà³ä×ã£¬' . $result['c'] . '¸ö';
    $date_time = date('Y-m-d H:i:s', time());

    if($result['c'] > 5000)
    {
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 10004, 'HTTP', 'None',0,'OK', 0, '{$msg}', 'Ê£ÓàIDÊý£º{$result[c]}')";
    }else{
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 10004, 'HTTP', 'None',0,'ERROR', 0, '{$msg}', 'Ê£ÓàIDÊý£º{$result[c]}')";
    }
    echo $sql_str;
    db_simple_getdata($sql_str, TRUE, 6);
}