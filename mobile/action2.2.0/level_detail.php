<?php
ignore_user_abort(true);
/**
 * 信用等级详细
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */


if(!$yue_login_id)
{
	die('no login');
}



$user_level_obj = POCO::singleton ( 'pai_user_level_class' );

/*
 * 信用等级详细
 * 
 * 返回  id_status: have_audited为已审核 ，auditing为审核中， not_upload未上传
 *      balance_status: yes为已充值300，no为未充值
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