<?php 
/**
 * 任务，需求过期，通知用户处理报价
 * @author Henry
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$task_request_obj = POCO::singleton('pai_task_request_class');
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$task_service_obj = POCO::singleton('pai_task_service_class');

$date_prev = date('Y-m-d H:i', time()-60); //前一分钟
$max_quotes_num = $task_quotes_obj->get_max_quotes_num();

$where_str = "status=0 AND FROM_UNIXTIME(expire_time,'%Y-%m-%d %H:%i')='{$date_prev}'";
$request_detail_list = $task_request_obj->get_request_detail_list(0, 0, false, $where_str, 'request_id ASC', '0,99999999');
$request_id_arr = array();
foreach($request_detail_list as $request_detail_info)
{
	$request_id = intval($request_detail_info['request_id']);
	$service_id = intval($request_detail_info['service_id']);
	$buyer_user_id = intval($request_detail_info['user_id']);
	if( $request_detail_info['status_code']=='quoted' ) //待雇佣，已过期，有报价
	{
		$quotes_count = $task_quotes_obj->get_quotes_list_for_valid($request_id, true);
		if( $quotes_count>0 && $quotes_count<$max_quotes_num ) //这里仅处理未报满的，已报满的在事件触发那边处理了
		{
			//获取买家手机号码
			$pai_user_obj = POCO::singleton('pai_user_class');
			$buyer_cellphone = $pai_user_obj->get_phone_by_user_id($buyer_user_id);
			$sms_data = array();
			$pai_sms_obj = POCO::singleton('pai_sms_class');
			$pai_sms_obj->send_sms($buyer_cellphone, 'G_PAI_TASK_QUOTES_FINISH_BUYER', $sms_data);
			
			//获取服务信息
// 			$service_info = $task_service_obj->get_service_info($service_id);
// 			$content = "你在约约上发布的{$service_info['service_name']}需求已经有{$quotes_count}人发来了报价。快去看看吧";
// 			$url = '';
// 			send_message_for_10006($buyer_user_id, $content, $url);
			
			$request_id_arr[] = $request_id;
		}
	}
}

echo "需求过期，通知用户处理报价： " . date("Y-m-d H:i:s");
if( !empty($request_id_arr) )
{
	echo " request_ids=" . implode(',', $request_id_arr);
}
