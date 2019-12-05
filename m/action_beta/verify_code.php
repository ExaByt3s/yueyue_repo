<?php
/**
 * 活动码验证
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}

$code = intval($_INPUT['code']);


$code_obj = POCO::singleton('pai_activity_code_class');


$ret = $code_obj->verify_code($yue_login_id,$code);

$output_arr['code'] = $ret>0 ? 1 : 0;

if($ret==1){
	$output_arr['msg'] = "活动码验证成功";
}elseif($ret==-1){
	$output_arr['msg'] = "活动码无效或已被使用";
}elseif($ret==-2){
	$output_arr['msg'] = "你不是活动发起人，不能使用该活动码";
}elseif($ret==-3){
	$output_arr['msg'] = "活动已结束";
}
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false); 

?>