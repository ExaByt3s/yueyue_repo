<?php

/**
 * 保存模特卡
 * zy 2014.9.15
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/model/cache.fuc.php');

// 用于处理机构问题
$model_id = intval($_INPUT['model_id'])?intval($_INPUT['model_id']) : $yue_login_id;

$pai_model_obj = POCO::singleton('pai_model_relate_org_class');

/**
 * 流程处理
 */
if(empty($yue_login_id))
{
    die('no login');
}

// 判断机构是否拥有编辑模特权限
$can_edit = $pai_model_obj-> get_org_model_audit_by_user_id($model_id,$yue_login_id);

if(!$can_edit)
{
	
	echo "<script>alert('非法操作！')</script>";

	die();
}


$update_model_card_obj = POCO::singleton('pai_model_card_class');

$update_data['user_icon'] = $_INPUT['user_icon'];
$update_data['nickname'] = mb_convert_encoding(trim($_INPUT['nickname']),'gbk','utf-8');
$update_data['sex'] = mb_convert_encoding((string)$_INPUT['sex'],'gbk','utf-8');
$update_data['location_id'] = $_INPUT['location_id'];
$update_data['height'] = (int)$_INPUT['height'];
$update_data['weight'] = (int)$_INPUT['weight'];
$update_data['cup'] = (string)$_INPUT['cup'];
//$update_data['cup_v2'] = (string)$_INPUT['cup_v2'];
$update_data['chest'] = (int)$_INPUT['chest'];
$update_data['chest_inch'] = (int)$_INPUT['chest_inch'];
$update_data['waist'] = (int)$_INPUT['waist'];
$update_data['hip'] = (int)$_INPUT['hip'];
$update_data['limit_num'] = (int)$_INPUT['limit_num'];
$update_data['cameraman_require'] = $_INPUT['cameraman_require'] ? $_INPUT['cameraman_require'] : 100;
$update_data['level_require'] = (int)$_INPUT['level_require'];
$update_data['pic_arr'] = $_REQUEST['pic_arr'];
$update_data['intro'] = mb_convert_encoding(trim($_INPUT['intro']), 'GBK','UTF-8');
$update_data['cover_img'] = trim($_INPUT['cover_img']);


$update_data ['new_model_style_arr']= poco_iconv_arr($_REQUEST['new_model_style_arr'],'UTF-8', 'GBK');


//print_r($update_data);
//dump($update_data);
//die();

if($_INPUT['is_preview'])
{
	$update_data['city_name'] = $_INPUT['city_name'];
	$ret = set_pc_model_cache($model_id,$update_data);
}
else
{
	$ret = $update_model_card_obj->update_model_card($update_data, $model_id);
}




$output_arr['code'] = $ret?1:0;
$output_arr['msg'] = $ret ? '保存成功' : '保存失败';
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
/*$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'utf-8','gbk');*/


mobile_output($output_arr,false);