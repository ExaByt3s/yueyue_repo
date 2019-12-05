<?php
/*
 * 退还优惠券
 * 
 * 
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$coupon_obj = POCO::singleton('pai_coupon_class');

$time = time()-1800;

$sql = "select date_id from event_db.event_date_tbl where date_status='wait' and pay_status=0 and is_use_coupon=1 and add_time<{$time};";
$date_arr = db_simple_getdata ( $sql );

foreach($date_arr as $val)
{
	$ret = $coupon_obj->not_use_coupon_by_oid("yuepai", $val['date_id']);
	
	if($ret['result']==1)
	{
		$sql = "update event_db.event_date_tbl set discount_price=0,is_use_coupon=0 where date_id=".$val['date_id'];
		db_simple_getdata ( $sql );
	}
}


$date = date ( "Y-m-d H:i:s" );
echo '退还优惠券成功' . $date;
?>