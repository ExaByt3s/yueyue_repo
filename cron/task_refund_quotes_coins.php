<?php 
/**
 * ���񣬶�ʱ�˻����۵����⿨
 * @author Henry
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$refund_ret = $task_quotes_obj->refund_quotes_coins_by_timing();

echo "�˻��������⿨��{$refund_ret['success_num']}+{$refund_ret['fail_num']} " . date("Y-m-d H:i:s");
