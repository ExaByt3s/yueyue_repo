<?php

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
global $yue_login_id;
/*
 * 收藏一条订单
 * @param int $quote_id
 * @return int
 */

$quotes_id = (int)$_INPUT['quotes_id'];
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
// echo $yue_login_id.'=='.$quotes_id;
$ret = $task_quotes_obj->check_user_auth($yue_login_id,$quotes_id);//验证操作权限
if(!$ret)
{
	echo 0;
	exit;
}
$ret = $task_quotes_obj->update_quotes_archive($quotes_id, array("is_archive"=>1));

echo $ret;


?>