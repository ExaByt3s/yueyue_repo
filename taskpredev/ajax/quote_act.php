<?php
/** 
 * 
 * 报价
 * 
 * 2015-4-11
 * 
 */
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$request_id = (int)$_INPUT['request_id'];
$price = $_INPUT['price'];
$content = $_INPUT['content'];
$content = iconv('utf-8','gbk',$_INPUT['content']);

if(empty($price))
{
	$output_arr['code'] = -1;
	$output_arr['message'] = '报价不能为空';
	mobile_output($output_arr,false);
	exit();
}

if(empty($content))
{
	$output_arr['code'] = -1;
	$output_arr['message'] = '请输入内容';
	mobile_output($output_arr,false);
	exit();
}

if(empty($request_id))
{
	$output_arr['code'] = -1;
	$output_arr['message'] = '参数错误';
	mobile_output($output_arr,false);
	exit();
}

/**
 * 提交报价
 * @param int $request_id
 * @param int $user_id
 * @param double $price
 * @param string $content
 * @param array $more_info
 * @return array
 */
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$ret = $task_quotes_obj->submit_quotes($request_id, $yue_login_id, $price, $content, $more_info);

if($ret['result']==1)
{
	$pay_ret = $task_quotes_obj->pay_quotes_coins($ret['quotes_id']);
	
	if($pay_ret['result']==1)
	{
		$output_arr['code'] = 1;
		$output_arr['message'] = "提交成功";
		$output_arr['quotes_id'] = $ret['quotes_id'];
	}
	elseif($pay_ret['result']== -3 || $pay_ret['result']==-4)
	{
		$output_arr['code'] = 2;
		$output_arr['message'] = "你的生意卡不足，请进行充值";
		$output_arr['quotes_id'] = $ret['quotes_id'];
	}
	else
	{
		$output_arr['code'] = -3;
		$output_arr['message'] = $pay_ret['message'];
		$output_arr['quotes_id'] = $ret['quotes_id'];
	}
}
else
{
	$output_arr['code'] = -2;
	$output_arr['message'] = $ret['message'];
}

mobile_output($output_arr,false);
 ?>