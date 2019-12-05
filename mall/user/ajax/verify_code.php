<?php
/**
 * 活动码验证
 */

include_once('../common.inc.php');

$output_arr['data'] = array();

if(empty($yue_login_id))
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = "非法登录";
	die();
}

$code = str_replace(' ','',$_INPUT['code']);

$code = (int)$code;

$code_obj = POCO::singleton('pai_activity_code_class');


$ret = $code_obj->verify_code($yue_login_id,$code);

$output_arr['code'] = $ret>0 ? 1 : 0;

if($ret==1)
{
	$code_info = $code_obj->get_available_code_info($code);
	$event_id = $code_info["event_id"];

	$return_url = "./sign.php?event_id=".$event_id;

	$output_arr['data']['return_url'] = $return_url;
	$output_arr['msg'] = "活动码验证成功";
}elseif($ret==-1){
	$output_arr['msg'] = "活动码无效或已被使用";
}elseif($ret==-2){
	$output_arr['msg'] = "你不是活动发起人，不能使用该活动码";
}elseif($ret==-3){
	$output_arr['msg'] = "活动已结束";
}elseif($ret==-4){
	$output_arr['msg'] = "活动码输入错误过多，请稍后再试";
}


mall_mobile_output($output_arr,false); 

?>