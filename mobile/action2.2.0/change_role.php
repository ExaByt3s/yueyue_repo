<?php 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$payment_obj = POCO::singleton('pai_payment_class');
$user_obj = POCO::singleton('pai_user_class');


if(!$yue_login_id){
	die("请先登录");
}

$check_payment = $payment_obj->check_user_used($yue_login_id);

if(!$check_payment){
	$ret = $user_obj->change_role($yue_login_id);
	if($ret==1){
		$output_arr['code'] = 1;
		$output_arr['msg'] = mb_convert_encoding('角色转换成功','gbk','utf-8');	
	}else{
		$output_arr['code'] = 0;
		$output_arr['msg'] = mb_convert_encoding('角色转换失败，请联系管理员','gbk','utf-8');	
	}
}else{
	$output_arr['code'] = 0;
	$output_arr['msg'] = '你已有交易记录，不能转换角色';
	$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
	
}

mobile_output($output_arr,false);

?>