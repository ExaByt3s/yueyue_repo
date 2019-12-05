<?php
/** 
 * 
 * 报价
 * 
 * 2015-4-11
 * 
 */
 
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);

/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

$request_id = (int)$_INPUT['request_id'];
$price = $_INPUT['price'];
$content = $_INPUT['content'];

if(empty($price))
{
	js_pop_msg("报价不能为空");
}

if((int)$price < 1 )
{
	js_pop_msg("报价价格不能小于1，请重新输入！");
}

if((int)$price >= 1000000 )
{
    js_pop_msg("报价不能超过1000000，请重新输入！");
}


if(ceil($price) == $price )
{
	// echo "整数"; 
}
else
{
	js_pop_msg("报价必须为整数哦！");
}



if(empty($content))
{
	js_pop_msg("请输入原因");
}

if(empty($request_id))
{
	js_pop_msg("参数错误");
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
		js_pop_msg("提交成功",false,"./quote_success.php?quotes_id=".$ret['quotes_id']);
	}
	elseif($pay_ret['result']==-3 || $pay_ret['result']==-4)
	{
		js_pop_msg("你的生意卡不足，请进行充值",false,"./pay.php?quotes_id=".$ret['quotes_id']);
	}
	else
	{
		js_pop_msg($pay_ret['message']);
	}
}
else
{
	js_pop_msg($ret['message']);
}


 ?>