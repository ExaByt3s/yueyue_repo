<?php

/**
 * 保存模特卡
 * zy 2014.9.15
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/**
 * 流程处理
 */
if(empty($yue_login_id))
{
    die('no login');
}


$update_model_card_obj = POCO::singleton('pai_model_card_class');

//var_dump($_REQUEST['pic_arr']);
//var_dump($_INPUT['model_type']);
//var_dump($_INPUT['model_style_arr']);
//var_dump($_INPUT['model_price_arr']);



$update_data['nickname'] = mb_convert_encoding(trim($_INPUT['nickname']),'gbk','utf-8');
$update_data['sex'] = mb_convert_encoding((string)$_INPUT['sex'],'gbk','utf-8');
$update_data['birthday'] = $_INPUT['birthday'];
$update_data['location_id'] = $_INPUT['location_id'];
$update_data['height'] = (int)$_INPUT['height'];
$update_data['weight'] = (int)$_INPUT['weight'];
$update_data['cup'] = (string)$_INPUT['cup'];
$update_data['chest'] = (int)$_INPUT['chest'];
$update_data['waist'] = (int)$_INPUT['waist'];
$update_data['hip'] = (int)$_INPUT['hip'];
$update_data['limit_num'] = (int)$_INPUT['limit_num'];
$update_data['cameraman_require'] = $_INPUT['cameraman_require'] ? $_INPUT['cameraman_require'] : 100;
$update_data['level_require'] = (int)$_INPUT['level_require'];
$update_data['pic_arr'] = $_REQUEST['pic_arr'];
$update_data['model_type_arr'] = poco_iconv_arr($_REQUEST['model_type'],'UTF-8', 'GBK');
$update_data['model_style_arr'] = poco_iconv_arr($_REQUEST['model_style_arr'],'UTF-8', 'GBK');
$update_data['model_price_arr'] = poco_iconv_arr($_REQUEST['model_price_arr'],'UTF-8', 'GBK');


$update_data ['new_model_style_arr']= poco_iconv_arr($_REQUEST['new_model_style_arr'],'UTF-8', 'GBK');


 //print_r($update_data);

$ret = $update_model_card_obj->update_model_card($update_data, $yue_login_id);




$output_arr['code'] = $ret?1:0;
$output_arr['msg'] = $ret ? '保存成功' : '保存失败';
/*$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'utf-8','gbk');*/


mobile_output($output_arr,false);