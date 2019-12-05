<?php

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
global $yue_login_id;
/*
 * 删除一条需求
 * @param int $lead_id
 * @return int
 */
$lead_id = (int)$_INPUT['lead_id'];
$task_lead_obj = POCO::singleton('pai_task_lead_class');
$ret = $task_lead_obj->delete_user_lead($lead_id,$yue_login_id);

echo $ret;


?>