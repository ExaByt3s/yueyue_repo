<?php
/*
 活动支付成功的文案
*/
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
 
$help_txt = include_once('/disk/data/htdocs232/poco/pai/mobile/config/help_text.conf.php');

$pai_config_obj = POCO::singleton ( 'pai_config_class' );

$waipai_arr = $pai_config_obj->big_waipai_event_id_arr();


// for 大外拍
if(in_array($event_id,$waipai_arr))
{
	$msg = $help_txt['text']['big_act_txt'];
}
else
{
	$msg = $help_txt['text']['normal_act_txt'];
}



$output_arr['msg'] = $msg;


mobile_output($output_arr,false);

?>