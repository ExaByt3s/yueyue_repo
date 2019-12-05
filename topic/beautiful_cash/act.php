<?php 
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');


$height = (int)$_INPUT['height'];
$weight = (int)$_INPUT['weight'];
$bwh = $_INPUT['bwh'];
$cup = $_INPUT['cup'];
$style = iconv("utf-8","gbk",$_INPUT['style']);


$bwh_arr = explode("-",$bwh);

$bust = (int)$bwh_arr[0];
$waist = (int)$bwh_arr[1];
$hip = (int)$bwh_arr[2];

if($height==0) $height=1;
if($weight==0) $weight=1;
if($bust==0) $bust=1;
if($waist==0) $waist=1;
if($hip==0) $hip=1;

$beauty_cash_obj = POCO::singleton ( 'pai_beauty_cash_class' );
$weixin_pub_obj = POCO::singleton ( 'pai_weixin_pub_class' );

$price = $beauty_cash_obj->cal_price($height,$weight,$bust,$waist,$hip);

$percent = $beauty_cash_obj->get_beauty_rank_percent($price);

$weixin_info = $weixin_pub_obj->get_weixin_user($_COOKIE['yueus_openid']);

$insert_data['open_id'] = $_COOKIE['yueus_openid'];
$insert_data['icon'] = $weixin_info['headimgurl'];
$insert_data['nickname'] = $weixin_info['nickname'];
$insert_data['height'] = $height;
$insert_data['weight'] = $weight;
$insert_data['bust'] = $bust;
$insert_data['waist'] = $waist;
$insert_data['hip'] = $hip;
$insert_data['cup'] = $cup;
$insert_data['price'] = $price;
$insert_data['percent'] = $percent;
$insert_data['style'] = $style;
$insert_data['add_time'] = time();

$id = $beauty_cash_obj->add_info($insert_data);

setcookie("BEAUTY_CASH",1,time()+10);

$json['code'] = 1;
$json['id'] = (int)$id;
echo json_encode($json);

?>