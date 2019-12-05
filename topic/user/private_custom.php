<?php
include_once("../../poco_app_common.inc.php");
include_once("pai_topic_common.inc.php");

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );


if($model_oa_order_obj->check_duplicate_submit($_INPUT['cameraman_phone']))
{
	echo 0;//重复提交记录
	exit;
}

$insert_data['cameraman_phone'] = $_INPUT['cameraman_phone'];

$insert_data['cameraman_nickname'] = iconv("UTF-8", "GBK",$_INPUT['cameraman_realname']);
$insert_data['cameraman_realname'] = iconv("UTF-8", "GBK",$_INPUT['cameraman_realname']);
$insert_data['home_page'] = iconv("UTF-8", "GBK",$_INPUT['home_page']);

$insert_data['style'] = $_INPUT['style'];


$insert_data['clothes_require'] = iconv("UTF-8", "GBK",$_INPUT['clothes_require']);
$insert_data['clothes_provide'] = iconv("UTF-8", "GBK",$_INPUT['clothes_provide']);
$insert_data['location_id'] = $_INPUT['location_id'];
$insert_data['date_address'] = iconv("UTF-8", "GBK",$_INPUT['date_address']);
$insert_data['date_time'] = $_INPUT['date_time'];
$insert_data['hour'] = iconv("UTF-8", "GBK",$_INPUT['hour']);
$insert_data['model_num'] = iconv("UTF-8", "GBK",$_INPUT['model_num']);
$insert_data['budget'] =  iconv("UTF-8", "GBK",$_INPUT['budget']);
$insert_data['bwh_require'] = iconv("UTF-8", "GBK",$_INPUT['bwh_require']);
$insert_data['height_require'] =  iconv("UTF-8", "GBK",$_INPUT['height_require']);
$insert_data['weight_require'] =  iconv("UTF-8", "GBK",$_INPUT['weight_require']);
$insert_data['looks_require'] = iconv("UTF-8", "GBK",$_INPUT['looks_require']);
$insert_data['date_remark'] = $_INPUT['date_remark'];
$insert_data['source'] = 3;
$insert_data['poco_id'] = $login_id;
$insert_data['style'] = iconv("UTF-8", "GBK",implode(",",$insert_data['style']));


$ret = $model_oa_order_obj->add_order($insert_data);


echo $ret;
?>

