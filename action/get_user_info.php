<?php

/**
 * 获取用户信息
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if($yue_login_id)
{
	$obj       = POCO::singleton('pai_user_class');

	$ret = $obj->get_user_nickname_by_user_id($yue_login_id);

	$output_arr['data'] = array('login' => 1,'nickname'=>$ret );
	$output_arr['code'] = 1;
	$output_arr['msg'] = 'sucess'; 

	mobile_output($output_arr,false);
}
else
{
	$output_arr['data'] = array('login' => 0,'nickname'=>'' );
	$output_arr['code'] = -1;
	$output_arr['msg'] = 'logout'; 

	mobile_output($output_arr,false);
}



?>