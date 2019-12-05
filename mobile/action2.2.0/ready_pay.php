<?php

/**
 * 准备支付
 * 
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$yue_login_id)
{
	die('no login');
}


$output_arr['code'] = 0;
$output_arr['message'] = "抱歉，亲，模特邀约功能已暂停使用";
mobile_output($output_arr,false);
exit();


// 修复Android 1.0.6 支付穿透问题
sleep(1);

$data['from_date_id']   = $yue_login_id; //摄影师ID
$data['to_date_id']     = (int)$_INPUT['model_id'];  //模特ID
$data['date_status']    = 'wait';  //状态
$data['date_time']      = strtotime(trim($_INPUT['date']));  //约拍时间
$data['date_style']     = mb_convert_encoding(trim($_INPUT['style']),'gbk','utf-8'); //拍摄风格
$data['date_hour']      = 1;  //拍摄时长
$data['hour']           = $_INPUT['hour'];  //拍摄时长
$data['date_price']     = $_INPUT['price'];  //出价
$data['limit_num']     = (int)$_INPUT['limit_num'];  //人数限制
$data['date_address']   = iconv("UTF-8", "gbk//TRANSLIT", $_INPUT['address']); 
$data['source']    = 'app';
$data['direct_confirm_id']   =  (int)$_INPUT['direct_confirm_id'];

$ret = add_date_op($data);

if($ret['status_code']==1)
{
	$output_arr['code'] 	= $ret['status_code'];
	$output_arr['message'] 	= $ret['message'];
	$output_arr['date_id']  = $ret['date_id'];
}
else
{
	$output_arr['code'] 	= $ret['status_code'];
	$output_arr['message'] 	= $ret['message'];
	
}

mobile_output($output_arr,false);

?>