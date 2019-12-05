<?php
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
global $yue_login_id;
/*
 * 提交报价
 * @param int $quote_id
 * @return int
 */
$request_id = (int)$_INPUT['request_id'];
$user_id = $yue_login_id；
$price = $_INPUT['price'];
$content = $_INPUT['content'];
$more_info = $_INPUT['more_info'];

$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$ret = $task_quotes_obj->submit_quotes($request_id, $user_id, $price, $content, $more_info);

echo $ret;

?>