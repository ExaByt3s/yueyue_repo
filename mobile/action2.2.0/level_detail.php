<?php
ignore_user_abort(true);
/**
 * ���õȼ���ϸ
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

/*
 * ���õȼ���ϸ
 * 
 * ����  id_status: have_auditedΪ����� ��auditingΪ����У� not_uploadδ�ϴ�
 *      balance_status: yesΪ�ѳ�ֵ300��noΪδ��ֵ
 */

$level_detail = $user_level_obj->level_detail($yue_login_id);

if($yue_login_id == 100001 || $yue_login_id == 100028)
{
	//$level_detail['balance_status'] = 1;
	//$level_detail['is_check'] = 0;
	//$level_detail['up_load'] = 0;
}

$output_arr['list'] = $level_detail;


mobile_output($output_arr,false);

?>