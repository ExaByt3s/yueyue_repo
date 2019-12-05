<?php
/**
 * 报价列表
 * @author Henry
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//获取参数
$user_id = intval($client_data['data']['param']['user_id']);
$request_id = intval($client_data['data']['param']['request_id']);

//初始化对象
$task_request_obj = POCO::singleton('pai_task_request_class');
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$task_service_obj = POCO::singleton('pai_task_service_class');
$task_profile_obj = POCO::singleton('pai_task_profile_class');
$task_message_obj = POCO::singleton('pai_task_message_class');

//获取需求信息
$request_info = $task_request_obj->get_request_detail_info_by_id($request_id);
$service_id = trim($request_info['service_id']);

//获取服务信息
$task_service_obj = POCO::singleton('pai_task_service_class');
$service_info = $task_service_obj->get_service_info($service_id);

//整理报价单数据
$quotes_list_tmp = $task_quotes_obj->get_quotes_detail_list_for_valid($request_id);
$page = 0;
$hired_quotes_info = array();
$hired_profile_info = array();
$quotes_count = count($quotes_list_tmp);
$quotes_list = array();
foreach($quotes_list_tmp as $quotes_info_tmp)
{
	$quotes_id_tmp = trim($quotes_info_tmp['quotes_id']);
	$user_id_tmp = trim($quotes_info_tmp['user_id']);
	$profile_id_tmp = intval($quotes_info_tmp['profile_id']);
	$content_tmp = trim($quotes_info_tmp['content']);
	$status_tmp = trim($quotes_info_tmp['status']);
	$price_tmp = trim($quotes_info_tmp['price']);
	
	//状态颜色
	$quotes_is_gray_tmp = $task_quotes_obj->get_quotes_is_gray($status_tmp, $request_info['status_code']);
	
	//商家资料
	$profile_info_tmp = $task_profile_obj->get_profile_info_by_id($profile_id_tmp);
	$rank_tmp = floor($profile_info_tmp['rank']) . '';
	$reviews_str_tmp = "{$profile_info_tmp['reviews']}次交易";
	
	//获取提醒数字
	$remind_num_tmp = $task_quotes_obj->get_quotes_remind_num($user_id, $quotes_id_tmp);
	$remind_num_tmp = trim($remind_num_tmp);
	
	//价格
	$price_str_tmp = '￥' . ((ceil($price_tmp)==$price_tmp)?$price_tmp*1:$price_tmp);
	
	//获取最近留言信息
	$message_info_tmp = $task_message_obj->get_message_info_lately_by_quotes_id($quotes_id_tmp);
	if( !empty($message_info_tmp) )
	{
		$content_tmp = trim(htmlspecialchars_decode($message_info_tmp['message_content']));
	}
	
	if( $status_tmp==1 )
	{
		$hired_quotes_info = $quotes_info_tmp;
		$hired_profile_info = $profile_info_tmp;
	}
	
	$quotes_list[] = array(
		'quotes_id' => $quotes_id_tmp,
		'user_id' => $user_id_tmp,
		'user_nickname' => $quotes_info_tmp['user_nickname'],
		'user_icon' => $quotes_info_tmp['user_icon'],
		'is_vip' => $quotes_info_tmp['is_vip'],
		'remind_num' => $remind_num_tmp,
		'rank' => $rank_tmp,
		'reviews_str' => $reviews_str_tmp,
		'price_str' => $price_str_tmp,
		'content' => $content_tmp,
		'status' => $status_tmp,
		'is_gray' => $quotes_is_gray_tmp,
	);
}

$tip_title = '';
$tip_content = '';
$btn_pay = '';
$btn_pay_url = '';
$status_code = trim($request_info['status_code']);
if( $status_code=='started' ) //待雇佣，待过期，无报价
{
	$tip_title = $service_info['profession_name'] . "会在24小时内联系你\r\n请你耐心等待";
	$tip_content = "把你的需求告诉我们\r\n约约很愿意帮助你完成它";
}
elseif( $status_code=='introduced' ) //待雇佣，待过期，有报价
{
	$cur_time = time();
	$expire_time = intval($request_info['expire_time']);
	$hour_tmp = ceil(($expire_time-$cur_time)/3600);
	if( $hour_tmp<1 ) $hour_tmp = 1;
	$tip_title = '';
	$tip_content = "我们会在{$hour_tmp}小时内将符合你需求的摄影师推送给你";
}
elseif( $status_code=='closed' ) //待雇佣，已过期，无报价
{
	$tip_title = "抱歉，暂时还没有符合你需求的服务供应者\r\n如需请电4000-82-9003";
	//$tip_content = "把你的需求告诉我们\r\n约约很愿意帮助你完成它";
}
elseif( $status_code=='quoted' ) //待雇佣，已过期，有报价
{
	//无
}
elseif( $status_code=='canceled' ) //已取消
{
	$canceled_time_str = date('Y.m.d', $request_info['canceled_time']);
	$tip_title = '';
	$tip_content = "你在{$canceled_time_str}取消了该订单";
}
elseif( $status_code=='hired' ) //已雇佣，待支付
{
	$pay_amount_tmp = trim($hired_quotes_info['pay_amount']);
	$pay_amount_tmp = '￥' . ((ceil($pay_amount_tmp)==$pay_amount_tmp)?$pay_amount_tmp*1:$pay_amount_tmp);;
	$btn_pay = "支付服务金{$pay_amount_tmp}";
	if( $client_data['data']['version']=='88.8.8'  ) //商业版、测试版
	{
		$pay_url_tmp = "http://yp.yueus.com/mobile/m2predev/pay_tt/pay.php?ver={$client_data['data']['version']}&quotes_id={$hired_quotes_info['quotes_id']}";
	}
	else
	{
		$pay_url_tmp = "http://yp.yueus.com/mobile/m2/pay_tt/pay.php?ver={$client_data['data']['version']}&quotes_id={$hired_quotes_info['quotes_id']}";
	}
	$pay_url_tmp = urlencode($pay_url_tmp);
	$btn_pay_url = "yueyue://goto?type=inner_web&showtitle=1&url={$pay_url_tmp}&wifi_url={$pay_url_tmp}";
	
	//日志 http://yp.yueus.com/logs/201502/03_pay_tt.txt
	pai_log_class::add_log(array('data'=>$client_data['data'], 'pay_url_tmp'=>$pay_url_tmp), 'get_quotes_list_hired', 'pay_tt');
}
elseif( $status_code=='paid' ) //已雇佣，已支付，待评价
{
	//无
	
	//日志 http://yp.yueus.com/logs/201502/03_pay_tt.txt
	pai_log_class::add_log(array(), 'get_quotes_list_paid', 'pay_tt');
}
elseif( $status_code=='reviewed' ) //已雇佣，已支付，已评价
{
	$tip_title = '';
	$tip_content = "感谢你的信任，欢迎下次再约";
}

$data = array();
$data['title'] = $request_info['title'];
$data['status_color'] = $request_info['status_color'];
$data['status_code'] = $request_info['status_code'];
$data['quotes_count'] = trim($quotes_count);
$data['quotes_list'] = $quotes_list;
$data['tip_title'] = $tip_title;
$data['tip_content'] = $tip_content;
$data['btn_hire'] = "谁最合适？";
$data['btn_cancel'] = "都不合适？";
$data['btn_pay'] = $btn_pay;
$data['btn_pay_url'] = $btn_pay_url;
$data['btn_review'] = "去写个评价吧";
$data['mid'] = '122OD04003'; //模板ID

$options['data'] = $data;
$cp->output($options);
