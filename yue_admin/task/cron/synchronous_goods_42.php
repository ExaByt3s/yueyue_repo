<?php
set_time_limit(0);
ini_set('memory_limit', '256M');
include_once 'common.inc.php';
$obj = POCO::singleton('pai_mall_goods_class');
$obj->synchronous_goods_42();
pai_log_class::add_log(time(), 'synchronous_goods_42', 'mall_goods_statistical');
exit('ok');