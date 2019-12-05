<?php
include_once 'common.inc.php';
$mall_basic_check_obj = POCO::singleton('pai_mall_goods_class');

foreach($get_db_data as $k => $v)
{
    $rs = $mall_basic_check_obj->batch_open_service($v['user_id'],$v['type_id']);
    if($rs['status'] != 1)
    {
        echo $rs['msg'];
        echo "</br>";
    }
}


exit('ok');




?>