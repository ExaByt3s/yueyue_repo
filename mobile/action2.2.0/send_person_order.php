<?php
ignore_user_abort(true);
/**
 * 发送邀请结果
 * hdw 2014.9.9
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */
$date_id = intval($_INPUT['date_id']) ;

$status = trim($_INPUT['status']);

$user_id = intval($_INPUT['user_id']);

if(!intval($_INPUT['cellphone']))
{
	
	$output_arr['msg'] = mb_convert_encoding('请输入手机号码', 'gbk','utf-8');

	$output_arr['code'] = 0;

	die();

}


$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

$nickname =  mb_convert_encoding('未登录用户', 'gbk','utf-8');
if($yue_login_id)
{
	$obj       = POCO::singleton('pai_user_class');

	$user_info = $obj->get_user_info_by_user_id($yue_login_id);	

	$nickname = $user_info['nickname'];
}



$insert_data['cameraman_phone'] = intval($_INPUT['cellphone']);
$insert_data['cameraman_nickname'] = $nickname;
$insert_data['style'] = mb_convert_encoding($_INPUT['style'], 'gbk','utf-8');
$insert_data['location_id'] = $_INPUT['location_id'];
$insert_data['date_time'] = $_INPUT['date_time'];
$insert_data['hour'] = mb_convert_encoding($_INPUT['hour'], 'gbk','utf-8');
$insert_data['model_num'] = mb_convert_encoding($_INPUT['model_num'], 'gbk','utf-8');
$insert_data['budget'] = $_INPUT['budget'];
$insert_data['looks_require'] = mb_convert_encoding($_INPUT['looks_require'], 'gbk','utf-8');
$insert_data['source'] = 2;

/*
 * 检查在等待审核状态是否重复提交
 */
$is_duplicate  = $model_oa_order_obj ->check_duplicate_submit(intval($_INPUT['cellphone']));

if($is_duplicate)
{
	$output_arr['msg'] = mb_convert_encoding('你已提交过订单', 'gbk','utf-8');

	$output_arr['code'] = 0;

	mobile_output($output_arr,false);

	exit();
}

$ret = $model_oa_order_obj->add_order($insert_data);

$ret =1;

if($ret==1){
	$msg = "你已提交成功";
}elseif($ret==-1){
	$msg = "操作人身份异常"; 
}elseif($ret==-2){ 
	$msg = "操作状态异常";
}elseif($ret==-3){
	$msg = "支付状态异常";
}

$output_arr['msg'] = mb_convert_encoding($msg, 'gbk','utf-8');

$output_arr['code'] = $ret>0 ? 1 : 0;

$output_arr['params'] = $insert_data;


mobile_output($output_arr,false);

?>