<?php
ignore_user_abort(true);
/**
 * ���õȼ��б�
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * ҳ����ղ���
 */


if(!$yue_login_id)
{
	die('no login');
}



$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

$level_list = $user_level_obj->level_list($yue_login_id);

$output_arr['list'] = $level_list;


mobile_output($output_arr,false);

?>