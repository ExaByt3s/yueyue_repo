<?php
/**
 * 服务列表
 * @author rong
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//获取参数
$user_id = intval($client_data['data']['param']['user_id']);
$limit = intval($client_data['data']['param']['limit']);

//初始化对象
$task_service_obj = POCO::singleton('pai_task_service_class');
$task_request_obj = POCO::singleton('pai_task_request_class');

//获取启用的服务列表

if($client_data['data']['version'] >= '2.2.0')
{
	$list = $task_service_obj->get_service_list(false, 'status=1 or service_id=7', 'sort ASC,service_id ASC', $limit,'service_id,service_name,service_desc,profession_name,cover_img');
}
else
{
	$list = $task_service_obj->get_service_list(false, 'status=1', 'sort ASC,service_id ASC', $limit,'service_id,service_name,service_desc,profession_name,cover_img');
}
	
foreach($list as $k=>$val)
{
	$is_submit_tmp = $task_request_obj->get_request_is_submit($user_id, $val['service_id']);
	$list[$k]['is_submit'] = $is_submit_tmp;
	
	$oa_url = "http://yp.yueus.com/mobile/app?from_app=1#demand";
	
	if($client_data['data']['version'] >= '3.0.0')
	{
		$list[$k]['url'] ="yueyue://goto?type=inner_app&pid=1220100&id=".$val['service_id'];
	}
	else
	{
		if($val['service_id']==7)
		{
			$list[$k]['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($oa_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $oa_url));
		}
		else
		{
			$list[$k]['url'] ="yueyue://goto?type=inner_app&pid=1220100&id=".$val['service_id'];
		}
	
	}
	
	
}



$data = array();
$data['service_list'] = $list;
$data['mid'] = '122LT08005'; //模板ID

$options['data'] = $data;
$cp->output($options);
