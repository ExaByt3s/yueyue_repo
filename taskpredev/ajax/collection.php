<?php

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");
global $yue_login_id;
/*
 * 收藏一条订单
 * @param int $quote_id
 * @return int
 */

$quotes_id = (int)$_INPUT['quotes_id'];
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$ret = $task_quotes_obj->update_quotes_archive($quotes_id, array("is_archive"=>1));


if($ret == 1)
{
	$output_arr['code'] = 1;
	$output_arr['message'] = "收藏成功";
}
else
{
	$output_arr['code'] = 0;
	$output_arr['message'] = "收藏失败";
}

mobile_output($ret,false);
?>