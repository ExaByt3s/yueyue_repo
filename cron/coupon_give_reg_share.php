<?php 
/**
 * 发放优惠券
 * @author Henry
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//echo '暂停发放优惠券' . date("Y-m-d H:i:s");
//exit();

set_time_limit(600);

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
$queue_list = $coupon_give_obj->get_queue_list(0, false, '', 'id DESC', '0,1000');
foreach($queue_list as $queue_info)
{
	$coupon_give_obj->give_coupon_by_queue_info($queue_info);
}

/*
//2015年7月10日 至 2015年8月9日，重庆50元约拍专属商品券
//地区：重庆；角色：摄影师；注册时间：2015年7月10日 至 2015年8月9日；
$sql = "SELECT user_id FROM `pai_db`.`pai_user_tbl` WHERE location_id='101004001' AND role='cameraman' AND add_time>=1436457600 AND add_time<=1439135999 AND user_id NOT IN (SELECT user_id FROM `pai_coupon_db`.`coupon_give_queue_tbl` WHERE give_code='Y2015M07D10_CHONGQING_NEW_USER')";
$user_list = db_simple_getdata($sql, false, 101);
foreach($user_list as $user_info)
{
	$cellphone = '';
	$user_id = intval($user_info['user_id']);
	$ref_id = 0;
	$coupon_give_obj->submit_queue('Y2015M07D10_CHONGQING_NEW_USER', $cellphone, $user_id, $ref_id);
}
*/

echo '发放优惠券' . date("Y-m-d H:i:s");
