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

if($user_id != $yue_login_id)
{
	//die('no login');
}

/**
 * 
 * 约拍确定
 * @param $date_id 约会表主键
 * @param $date_status 更新状态
 * @param $user_id 传递当前登陆的用户ID，进行验证该用户是否有权利去更改约会状态，判断方式：约会表的被约会人ID是否等于USERID
 * */
$ret = update_event_date_status($date_id,$status,$yue_login_id);



// 预留分支
switch ($status) 
{
	// 拒绝		
	case 'cancel':
		$cancel_reason = trim($_INPUT['cancel_reason']);
		$remark = trim($_INPUT['remark']);
		/*
		 * 拒绝原因
		 * @param $date_id int
		 * @param $cancel_reason string 拒绝原因
		 * @param $remark string 补充说明
		 */
		why_cancel_date($date_id,mb_convert_encoding($cancel_reason, 'gbk','utf-8'),mb_convert_encoding($remark, 'gbk','utf-8'));
		$output_arr['msg']  = $ret ?mb_convert_encoding('你已拒绝约拍邀请', 'gbk','utf-8') : mb_convert_encoding('告诉我们拒绝的原因，帮助我们改进吧。', 'gbk','utf-8');
		break;
}


if($ret==1){
	$msg = "操作成功";
}elseif($ret==-1){
	$msg = "操作人身份异常";
}elseif($ret==-2){
	$msg = "操作状态异常";
}elseif($ret==-3){
	$msg = "支付状态异常";
}

$output_arr['msg'] = mb_convert_encoding($msg, 'gbk','utf-8');

$output_arr['data'] = $ret>0 ? 1 : 0;


mobile_output($output_arr,false);

?>