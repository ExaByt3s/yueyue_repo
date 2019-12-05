<?php
include_once("../../poco_app_common.inc.php");
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
if($pai_user_obj->check_role($user_id) == 'model')
{
  $sql_str = "SELECT COUNT(DISTINCTROW(from_date_id)) AS c, MAX(add_time) AS add_time FROM `event_db`.`event_date_tbl` WHERE to_date_id = $user_id AND date_status = 'confirm'";
}else{
  $sql_str = "SELECT COUNT(DISTINCTROW(to_date_id)) AS c, MAX(add_time) AS add_time FROM `event_db`.`event_date_tbl` WHERE from_date_id = $user_id AND date_status = 'confirm'";  
}
$result = db_simple_getdata($sql_str, TRUE);  
if($result['c'])
{
    $date_num = (int)$result['c'];
    $data_add_time = $result['add_time'];
}else{
    $date_num = 0;
}  

$sql_str = "SELECT COUNT(DISTINCTROW(be_follow_user_id)) AS c, MAX(add_time) AS add_time FROM pai_db.pai_user_follow_tbl WHERE follow_user_id = $user_id";
$result = db_simple_getdata($sql_str, TRUE, 101);
if($result['c'])
{
    $follow_num = (int)$result['c'];
    $follow_add_time = $result['add_time'];
}else{
    $follow_num = 0;
}    

$data['date_total']     = $date_num;
$data['follow_total']   = $follow_num;     
if($data_add_time > $follow_add_time)
{
    $data['last_time'] = $data_add_time;
}else{
    $data['last_time'] = $follow_add_time;
}      

$data['last_time'] = strtotime(date('Y-m-d H:i:00'));  

$options['data'] = $data;

$cp->output($options);
?>