<?php
include_once 'common.inc.php';
set_time_limit(0);
ini_set('memory_limit', '256M');

$obj = POCO::singleton('pai_mall_goods_class');

//
//$type_id = 12;
//$goods_id = 2117840;
//$profile_id = 205829;
//$obj->batch_insert_or_update_goods_type_id_tbl(null,$type_id);
//exit('ok');

$arys = array(40,12,5,31,3,41);
//$arys = array(5,40);
foreach($arys as $k => $v)
{
    $type_id = $v;
    $obj->batch_insert_or_update_goods_type_id_tbl(null,$type_id);
}

exit('ok');
