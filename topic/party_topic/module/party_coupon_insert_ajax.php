<?php
/** 
 * 
 * 优惠劵入库处理
 * 
 * author 星星
 * 
 * 
 * 2015-3-4
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$obj = POCO::singleton ( 'pai_event_coupon_class' );
$data['cellphone'] = $_INPUT['cellphone'];
$data['event_id'] = $_INPUT['event_id'];

$ret=$obj->add_share_coupon($data);
?>