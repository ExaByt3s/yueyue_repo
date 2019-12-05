<?php
/** 
 * 
 * tt
 * hudw
 * 2015-4-11
 * common
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//дљЫЭЩњвтПЈ
if( $yue_login_id>0 )
{
	$task_coin_obj = POCO::singleton('pai_task_coin_class');
	$task_coin_obj->submit_give('SELLER_LOGIN_TODAY', $yue_login_id, strtotime(date('Y-m-d 00:00:00')));
}

?>