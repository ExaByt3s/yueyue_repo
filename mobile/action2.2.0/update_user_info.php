<?php

/**
 * 更新用户数据
 * zy 2014.9.10
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/**
 * 流程处理
 */
if(empty($yue_login_id))
{
    die('no login');
}


//$update_info_obj = POCO::singleton('pai_user_class');


$update_info_obj = POCO::singleton('pai_cameraman_card_class');


$update_data['nickname'] = iconv("UTF-8", "gbk//TRANSLIT", $_INPUT['nickname']);;
//$update_data['pic_arr'] = explode(',',$_INPUT['pic_arr']);
$update_data['pic_arr'] = $_REQUEST['pic_arr'];
$update_data['intro'] = iconv("UTF-8", "gbk//TRANSLIT", $_INPUT['intro']);;
$update_data['cover_img'] = $_INPUT['cover_img'];
$update_data['location_id'] = intval($_INPUT['location_id']);
//$update_data['sex'] = (string)$_INPUT['sex'];
//$update_data['birthday'] = $_INPUT['birthday'];
//$update_data['attendance'] = $_INPUT['attendance'];
//$update_data['phone'] = (int)$_INPUT['phone'];
//$update_data['user_level'] = (int)$_INPUT['user_level'];
//$update_data['organizer_level'] = (int)$_INPUT['organizer_level'];
//$update_data['model_level'] = (int)$_INPUT['model_level'];
//$update_data['cameraman_level'] = (int)$_INPUT['cameraman_level'];
//$update_data['last_login_time'] = (int)$_INPUT['last_login_time'];
//$update_data['last_login_location'] = (int)$_INPUT['last_login_location'];


//$ret = $update_info_obj->update_user($update_data, $yue_login_id);

//$update_data = poco_iconv_arr($update_data,'UTF-8', 'GBK');

$ret = $update_info_obj->update_cameraman_card($update_data, $yue_login_id);


$output_arr['code'] = $ret?1:0;
$output_arr['msg'] = $ret ? '更新成功' : '更新失败';
//$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');


mobile_output($output_arr,false);



?>