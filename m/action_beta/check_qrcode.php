<?php 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$code_obj = POCO::singleton('pai_activity_code_class');


$code = (int)$_INPUT['code'];
$event_id = (int)$_INPUT['event_id'];
$enroll_id = (int)$_INPUT['enroll_id'];
$hash = $_INPUT['hash'];

if(!$yue_login_id){
	die("请先登录");
}

$qrcode_hash = qrcode_hash($event_id,$enroll_id,$code);

if($qrcode_hash != $hash){
	$output_arr['code'] = 0;
	$output_arr['msg'] = 'HASH验证失败';
	$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
	mobile_output($output_arr,false);
	exit;
}


$ret = $code_obj->verify_code($yue_login_id,$code);


if($ret==1){
	$event_info = get_event_list('event_id='.$event_id);

	if($event_info[0]['type_icon']=='yuepai_app'){
		$type = 'mine/consider/confirm';
	}else{
		$type = "act/signin/".$event_id;
	}

	$output_arr['code'] = 1;
	$output_arr['msg'] = '验证成功';
	$output_arr['type'] = $type;

}else{
	$output_arr['code'] = 0;

	if($ret==-1){
		$output_arr['msg'] = '活动券无效或已被使用';
	}elseif($ret==-2){
		$output_arr['msg'] = '你不是活动发起人，不能使用该活动券';
	}elseif($ret==-3){
		$output_arr['msg'] = '活动已结束';
	}
}

$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false);

?>