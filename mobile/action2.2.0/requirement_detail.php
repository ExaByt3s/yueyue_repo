<?php

/**
 * 摄影师需求
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$model_enroll_obj = POCO::singleton ( 'pai_model_oa_enroll_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
$level_obj = POCO::singleton ( 'pai_user_level_class' );

/**
 * 页面接收参数
 */
$order_id = intval($_INPUT['order_id']) ;

$ret = $model_oa_order_obj->get_order_info($order_id);

$check_sign = $model_enroll_obj->check_repeat($yue_login_id,$ret['order_id']);

$role = $user_obj->check_role($yue_login_id);

if($check_sign || $role=='cameraman')
{
	$sign = 0;
}
else
{
	$sign = 1;
}

$user_info = $user_obj->get_user_by_phone($ret['cameraman_phone']);

$time = date("H:i",strtotime($ret['date_time']));

if($time=='10:00')
{
	$time_text = "上午";
}
elseif($time=='15:00')
{
	$time_text = "下午";
}
elseif($time=='18:00')
{
	$time_text = "晚上";
}
else
{
	$time_text = "下午";
}

$new_ret['time'] = date("n月d日",strtotime($ret['date_time']))." ".$time_text;
if((int)$ret['question_budget'])
{
    $new_ret['budget'] = '￥' . $ret['question_budget'];
}else{
    $new_ret['budget'] = $ret['question_budget'];
}

$new_ret['require_remark'] = $ret['require_remark'];
$new_ret['style'] = $ret['question_style'];
$new_ret['date_address'] = $ret['date_address']? $ret['date_address'] : "待定，私聊沟通";
$new_ret['clothes_require'] = $ret['clothes_require'] ? $ret['clothes_require'] : "待定，私聊沟通";
$new_ret['clothes_provide'] = $ret['clothes_provide'] ? $ret['clothes_provide'] : "待定，私聊沟通";
$new_ret['order_id'] = $ret['order_id'];
$new_ret['is_sign'] = $sign;
$new_ret['user_icon'] = get_user_icon($user_info['user_id']);
$new_ret['nickname'] = $user_info['nickname'];
$new_ret['user_id'] = $user_info['user_id'];
$new_ret['level'] = $level_obj->get_user_level($user_info['user_id']);
$new_ret ['city_name'] = get_poco_location_name_by_location_id ( $user_info ['location_id'] );


$output_arr['list'] = $new_ret;


mobile_output($output_arr,false);

?>