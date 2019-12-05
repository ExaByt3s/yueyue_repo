<?php
/** 
 * 
 * tt
 * лют╡
 * 2015-4-11
 * 
 */
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");
 

global $yue_login_id;

$request_id = (int)$_INPUT['request_id'];

$user_id = 100028 ;

// echo "1";

$task_request_obj = POCO::singleton('pai_task_request_class');
$submit_ret = $task_request_obj->change_request_status_del($user_id, $request_id);

mobile_output($submit_ret,false);


 ?>