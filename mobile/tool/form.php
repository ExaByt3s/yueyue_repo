<?php 
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$tpl = new SmartTemplate ( "form.tpl.htm" );

$id = (int)$_INPUT['id'];

$date_config_obj = POCO::singleton ( 'pai_model_date_config_class' );

$config_info = $date_config_obj->get_config_info ( $id );

$config_info ['city_name'] = get_poco_location_name_by_location_id ( $config_info ['location_id'] );

if($config_info['date_time'])
{
	$config_info['date_time'] = date("Y-m-d H:i",$config_info['date_time']);
}
else
{
	$config_info['date_time'] = "";
}

if(!$config_info['location_id'])
{
	$config_info['location_id'] = 101029001;
}

$tpl->assign ( $config_info );

$tpl->output ();

?>