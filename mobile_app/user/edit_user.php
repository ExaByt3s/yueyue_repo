<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_obj = POCO::singleton ( 'pai_user_class' );

$user_id = $client_data ['data'] ['param'] ['user_id'];
$access_token = $client_data ['data'] ['param'] ['access_token'];


$data ['icon'] = get_user_icon ( $user_id, 86, TRUE );

$user_info = $user_obj->get_user_info_by_user_id($user_id);

$check_role = $user_obj->check_role ( $user_id );

$data ['nickname'] = $user_info ['nickname'] ? $user_info ['nickname'] : "";
$data ['location_id'] = $user_info ['location_id'] ? $user_info ['location_id'] : "";
$data ['remark'] = $user_info ['intro'] ? $user_info ['intro'] : "";
$data ['cover_img'] = $user_info ['cover_img'] ? $user_info ['cover_img'] : "";
$data ['pic_arr'] = $user_info ['pic_arr'] ? $user_info ['pic_arr'] : array();
$data ['sex'] = $user_info ['sex'] ? $user_info ['sex'] : "";
$data ['user_id'] = $user_id;

if($check_role=='model')
{
	$data ['height'] = $user_info ['height']? $user_info ['height'] : "";
	$data ['weight'] = $user_info ['weight']? $user_info ['weight'] : "";
	$data ['cup'] = $user_info ['cup_word']? $user_info ['cup_word'] : "";
	$data ['chest_inch'] = $user_info ['chest_inch']? $user_info ['chest_inch'] : "";
	$data ['chest'] = $user_info ['chest']? $user_info ['chest'] : "";
	$data ['waist'] = $user_info ['waist']? $user_info ['waist'] : "";
	$data ['hip'] = $user_info ['hip']? $user_info ['hip'] : "";
	$data ['limit_num'] = $user_info ['limit_num']? $user_info ['limit_num'] : "";
	$data ['level_require'] = $user_info ['level_require']? $user_info ['level_require'] : "1";
	
	$model_style_combo = $user_info ['model_style_combo'];
	
	$data ['main_style'] = $model_style_combo['main'][0]? $model_style_combo['main'][0] : "";
	
	foreach($model_style_combo['other'] as $k=>$val)
	{
		$data ['other_style'][$k] = $val;
	}
}

$data['post_icon'] = 'http://sendmedia-w.yueus.com:8078/icon.cgi';
$data['post_icon_wifi'] = 'http://sendmedia-w-wifi.yueus.com:8078/icon.cgi';
$data['icon_size'] = '640';

$data['post_cover'] = 'http://sendmedia-w.yueus.com:8079/upload.cgi';
$data['post_cover_wifi'] = 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi';
$data['cover_size'] = '640';

$data['post_pic'] = 'http://sendmedia-w.yueus.com:8079/upload.cgi';
$data['post_pic_wifi'] = 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi';
$data['pic_size'] = '640';

$data['pic_num'] = '15';

$level_text[0]['name'] = "V1手机认证";
$level_text[0]['des'] = "V1:对方已获得手机认证";
$level_text[1]['name'] = "V2实名认证";
$level_text[1]['des'] = "V2:对方已获得手机认证+身份认证";
$level_text[2]['name'] = "V3达人认证";
$level_text[2]['des'] = "V3:对方已获得手机认证+身份认证+信用金￥300";

$data['level_text'] = $level_text;

include_once ('/disk/data/htdocs232/poco/pai/config/model_card_config.php');

$i=0;
foreach($model_style as $k=>$val)
{
	$model_style_arr[$i]['name'] = $val;
	$i++;
}
$data['style_text'] = $model_style_arr;

$options ['data'] = $data;

$cp->output ( $options );
?>