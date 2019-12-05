<?php 
/**
 * 发放生意卡
 * @author Henry
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//echo '暂停发放生意卡' . date("Y-m-d H:i:s");
//exit();

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$task_coin_obj = POCO::singleton('pai_task_coin_class');
$give_list = $task_coin_obj->get_give_list(0, false, '', 'give_id ASC', '0,1000');
foreach($give_list as $give_info)
{
	$task_coin_obj->give_by_info($give_info);
}

echo '发放生意卡' . date("Y-m-d H:i:s");
