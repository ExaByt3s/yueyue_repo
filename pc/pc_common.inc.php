<?php

/** 
 * pc 
 * 通用
 * 汤圆
 * 2015-6-5
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


if( $yue_login_id>0 )
{
	$task_coin_obj = POCO::singleton('pai_task_coin_class');
	$task_coin_obj->submit_give('SELLER_LOGIN_TODAY', $yue_login_id, strtotime(date('Y-m-d 00:00:00')));
}

?>