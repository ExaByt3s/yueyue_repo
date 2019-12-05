<?php
/**
 * hudw 2014.8.22
 * 模特拍摄风格
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_id = $_INPUT['user_id'];

$pai_obj = POCO::singleton('pai_model_card_class');
/*
 * 根据用户ID获取模特卡数据
 * @param int $user_id
 * return array
 */

$style['style']='【私拍第一季】1折价50元';
$style['price']= 50;

$ret = $pai_obj ->get_model_card_by_user_id($user_id);		

if($user_id==100000)
{
	array_unshift($ret['model_style'],$style);
}

$obj = $ret;

$output_arr['data']['model_style'] = $obj['model_style'];
$output_arr['data']['cameraman_require'] = $obj['cameraman_require'];

mobile_output($output_arr,false);

?>