<?php
include_once 'common.inc.php';
set_time_limit(0);
ignore_user_abort(true);
ini_set('memory_limit', '256M');
$obj = POCO::singleton('pai_mall_statistical_class');
$re = $obj->do_goods_id_view_numbers_update();
pai_log_class::add_log(serialize($re), $re['max_url'], 'mall_goods_statistical');
$file = "/disk/data/htdocs232/poco/pai/logs/".date('Ym')."/".date('d')."_mall_goods_pv.log";
$fp=fopen($file, 'a');
$mess=$re['log'];
if($mess!="")
{
	fwrite($fp,$mess);
}
fclose($fp);

