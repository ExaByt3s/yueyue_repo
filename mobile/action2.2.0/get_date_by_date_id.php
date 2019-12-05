<?php

/**
 * 获取约拍单消息
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


if(!$yue_login_id)
{
	$output_arr['code'] = 500;
	$output_arr['msg'] = '没有权限，请先登录';
	mobile_output($output_arr,false);
	exit;
}

/**
 * 页面接收参数
 */
$date_id = intval($_INPUT['date_id']);

$ret = get_date_by_date_id($date_id);

if(!in_array($yue_login_id,array($ret['from_date_id'],$ret['to_date_id'])))
{
	$output_arr['code'] = 404;
	$output_arr['msg'] = '不存在该订单';
	mobile_output($output_arr,false);
	exit;
}

if(!$ret['date_address'])
{	
	$ret['date_address'] = mb_convert_encoding('暂无', 'gbk','utf-8');		
}

if(!$ret['date_type'])
{
	$ret['date_type'] = mb_convert_encoding('暂无', 'gbk','utf-8');		
}

$output_arr['code'] = count($ret)?1:0;
$output_arr['data'] = $ret;
$output_arr['msg'] = 'get date info';

if($yue_login_id == 100029)
{
	//sleep(11);	
}

mobile_output($output_arr,false);

?>