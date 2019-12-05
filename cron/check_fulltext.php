<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/7/6
 * Time: 9:03
 */
include_once ("../poco_app_common.inc.php");
$fulltext_content = file_get_contents('http://www.yueus.com/cron/check_event.php');

//var_dump($fulltext_content);

$date_time = date('Y-m-d H:i:s', time());

if(is_not_json($fulltext_content))
{
    $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 10001, 'HTTP', 'None',0,'ERROR', 0, '全文异常', '{$fulltext_content}')";
}else{
    $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 10001, 'HTTP', 'None',0,'OK', 0, '全文正常', '{$fulltext_content}')";
}
//echo $sql_str;

db_simple_getdata($sql_str, TRUE, 6);

function is_not_json($str){
    return is_null(json_decode($str));
}

?>