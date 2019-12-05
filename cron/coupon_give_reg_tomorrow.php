<?php 
/**
* @file coupon_give_reg_tomorrow.php
* @synopsis 新用户注册次日赠券，2015-11-11 至 2015-12-10 每天一次，14：00 
* @author wuhy@yueus.com
* @version null
* @date 2015-11-10
 */
ignore_user_abort(true);
set_time_limit(600);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//echo '暂停发放优惠券' . date("Y-m-d H:i:s");
//exit();

$op = trim($_INPUT['op']);
$cur_time = time();
if( $op!='run')
{
	die('op error!');
}
if( strtotime('2015-11-12 00:00:00')>=$cur_time && $cur_time<=strtotime('2015-12-11 23:59:59') )
{
	die('time expected!');
}

$coupon_give_obj = POCO::singleton('pai_coupon_give_class');

$yesterday = strtotime('yesterday', $cur_time);
$today = strtotime('today', $cur_time)-1;

$pai_user_obj = POCO::singleton('pai_user_class');
$user_list = $pai_user_obj->get_user_list(false, $where_str = "add_time>={$yesterday} AND add_time<={$today}", 'add_time ASC,user_id DESC', '0,99999999', '*');
foreach($user_list as $user_info)
{
	$user_id = intval($user_info['user_id']);
	$coupon_give_obj->submit_queue('Y2015M11D09_USER_REG_TOMORROW', '', $user_id, 0);
}

echo '注册送优惠券（次日14：00）' . date("Y-m-d H:i:s");
