<?php
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$date_config_obj = POCO::singleton ( 'pai_model_date_config_class' );

$id = (int)$_INPUT['id'];

$available = $date_config_obj->check_available($id);

if($available)
{
	$code = 1;
	$msg = '可用';
}
else
{
	$code = 0;
	$msg = '已抢完';
}

$json_arr['code'] = $code;
$json_arr['msg'] = iconv('gbk','utf-8',$msg);

echo json_encode($json_arr);

?>