<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/10/28
 * Time: 9:33
 */

set_time_limit(3600);
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

//获取优惠券总数
$sql_str = "SELECT COUNT(*) FROM `pai_coupon_db`.`coupon_exchange_ref_coupon_tbl` ";
$result = db_simple_getdata($sql_str, FALSE, 101);
var_dump($result);
echo "<BR>-----------------------------------------------------<BR>";

//获取每天兑换优惠券数
$sql_str = "SELECT DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m-%d'), COUNT(*)
            FROM `pai_coupon_db`.`coupon_exchange_ref_coupon_tbl`
            GROUP BY DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m-%d')";
$result = db_simple_getdata($sql_str, FALSE, 101);
var_dump($result);
echo "<BR>-----------------------------------------------------<BR>";

//获取优惠券使用
$sql_str = "SELECT COUNT(*) AS C FROM `pai_coupon_db`.`coupon_ref_order_tbl` WHERE is_cash=1";
