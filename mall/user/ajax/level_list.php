<?php
ignore_user_abort(true);
/**
 * ���õȼ��б�
 */

include_once('../common.inc.php');

/**
 * ҳ����ղ���
 */


// û�е�¼�Ĵ���
if(empty($yue_login_id))
{
	$output_arr['code'] = -1;
	$output_arr['msg']  = '��δ��¼,�Ƿ�����';
	$output_arr['data'] = array();
	mobile_output($output_arr,false);
	exit();
}


$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

$level_list = $user_level_obj->level_list($yue_login_id);

$level_detail = $user_level_obj->level_detail($yue_login_id);

$output_arr['list'] = $level_list;

$output_arr['data'] = $level_detail;

mall_mobile_output($output_arr,false);

?>