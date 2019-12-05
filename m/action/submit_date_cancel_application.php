<?php
ignore_user_abort ( true );
/**
 * 申请退款
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */
$date_id = intval ( $_INPUT ['date_id'] );
$reason = mb_convert_encoding ( trim ( $_INPUT ['reason'] ), 'gbk', 'utf-8' );
$remark = mb_convert_encoding ( trim ( $_INPUT ['remark'] ), 'gbk', 'utf-8' );

$type = $_INPUT ['type'];

if (! $yue_login_id)
{
	die ( 'no login' );
}


	
$ret_arr = submit_date_cancel_application ( $date_id, $reason, $remark );

$ret = $ret_arr['code'];

if($ret_arr['type']=='wait')
{
	if ($ret == 1)
	{
		$msg = "退款成功";
	} elseif ($ret == - 1)
	{
		$msg = "参数错误";
	} elseif ($ret == - 2)
	{
		$msg = "退款失败";
	}elseif ($ret == - 3)
	{
		$msg = "状态异常";
	}
}else
{
	if ($ret == 1)
	{
		$msg = "提交成功";
	} elseif ($ret == - 1)
	{
		$msg = "模特还未接受邀请";
	} elseif ($ret == - 2)
	{
		$msg = "已有取消记录";
	} elseif ($ret == - 3)
	{
		$msg = "活动券已被扫了";
	}
}

$output_arr ['msg'] = $msg;

$output_arr ['data'] = $ret;

mobile_output ( $output_arr, false );

?>