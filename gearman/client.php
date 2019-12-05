<?php

//define('G_USE_NEW_MYSQL_DB_DRIVER', 1);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$pai_gearman_obj = POCO::singleton('pai_gearman_class');

$cmd_type = 'pai_mall_order_submit_after';
$send_rst = $pai_gearman_obj->send_cmd($cmd_type, array('order_sn'=>'94254705'));
var_dump($send_rst);

// $task_list = $pai_gearman_obj->get_task_all_list();
// var_dump($task_list);

// $task_list = $pai_gearman_obj->get_task_fail_list();
// var_dump($task_list);



/*
$the_query = "SELECT * FROM `mall_db`.`mall_order_detail_tbl` ORDER BY `order_detail_id` DESC LIMIT 1;";
$DB->query( $the_query, 0, 101);

$row=$DB->fetch_row();
var_dump($row);
*/
