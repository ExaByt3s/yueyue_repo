<?php
include_once 'common.inc.php';
set_time_limit(0);
ignore_user_abort(true);
ini_set('memory_limit', '256M');
$obj = POCO::singleton('pai_mall_statistical_class');
$obj->uv_batch_insert_goods_uv_log();
pai_log_class::add_log(time(), 'uv_batch_insert_goods_uv_log', 'mall_goods_statistical');
exit('ok');