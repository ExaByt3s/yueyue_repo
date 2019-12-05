<?php 
/**
 * 任务，生意机会提醒
 * @author Henry
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

echo "停止发送 " . date("Y-m-d H:i:s");
die();

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$cur_time = time();
$hour_minute = date('H:i', $cur_time);

$list = array();
if( $hour_minute=='12:00' )
{
	$begin_time = strtotime( date('Y-m-d 18:00:00', $cur_time-24*3600) );
	$end_time = strtotime( date('Y-m-d 11:59:59', $cur_time) );
	
	$task_lead_obj = POCO::singleton('pai_task_lead_class');
	$list_a = $task_lead_obj->get_user_id_and_count_list_for_remind($begin_time, $end_time);
	foreach($list_a as $info_a)
	{
		$user_id_tmp = intval($info_a['user_id']);
		$list[$user_id_tmp]['count_a'] = intval($info_a['count']);
	}
	
	$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
	$list_b = $task_quotes_obj->get_user_id_and_count_list_for_remind();
	foreach($list_b as $info_b)
	{
		$user_id_tmp = intval($info_b['user_id']);
		$list[$user_id_tmp]['count_b'] = intval($info_b['count']);
	}
}
elseif( $hour_minute=='18:00' )
{
	$begin_time = strtotime( date('Y-m-d 12:00:00', $cur_time) );
	$end_time = strtotime( date('Y-m-d 17:59:59', $cur_time) );
	
	$task_lead_obj = POCO::singleton('pai_task_lead_class');
	$list_a = $task_lead_obj->get_user_id_and_count_list_for_remind($begin_time, $end_time);
	foreach($list_a as $info_a)
	{
		$user_id_tmp = intval($info_a['user_id']);
		$list[$user_id_tmp]['count_a'] = intval($info_a['count']);
	}
	
	$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
	$list_b = $task_quotes_obj->get_user_id_and_count_list_for_remind();
	foreach($list_b as $info_b)
	{
		$user_id_tmp = intval($info_b['user_id']);
		$list[$user_id_tmp]['count_b'] = intval($info_b['count']);
	}
}
$seller_user_id_arr = array();
foreach($list as $seller_user_id=>$info)
{
	$count_a = intval($info['count_a']);
	$count_b = intval($info['count_b']);
	
	$pai_sms_obj = POCO::singleton('pai_sms_class');
	$task_seller_obj = POCO::singleton('pai_task_seller_class');
	$seller_cellphone = $task_seller_obj->get_seller_cellphone($seller_user_id); //获取卖家手机号码
	if( $count_a>0 && $count_b>0 )
	{
		$sms_data = array(
			'lead_count' => $count_a,
			'url' => 'task.yueus.com/m/list.php',
		);
		$pai_sms_obj->send_sms($seller_cellphone, 'G_PAI_TASK_LEAD_REMIND_SELLER_C', $sms_data);
	}
	elseif( $count_a>0 )
	{
		$sms_data = array(
			'lead_count' => $count_a,
			'url' => 'task.yueus.com/m/list.php',
		);
		$pai_sms_obj->send_sms($seller_cellphone, 'G_PAI_TASK_LEAD_REMIND_SELLER', $sms_data);
	}
	elseif( $count_b>0 )
	{
		$sms_data = array(
			'url' => 'task.yueus.com/m/process.php',
		);
		$pai_sms_obj->send_sms($seller_cellphone, 'G_PAI_TASK_LEAD_REMIND_SELLER_B', $sms_data);
	}
	
	$seller_user_id_arr[] = $seller_user_id;
}

echo "生意机会提醒： " . date("Y-m-d H:i:s");
if( !empty($seller_user_id_arr) )
{
	echo " seller_user_ids=" . implode(',', $seller_user_id_arr);
}
