<?php
set_time_limit(0);

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql = "select * from test.test_seller_user where status=0";
$arr = db_simple_getdata($sql,false,101);

$seller_obj = POCO::singleton ( 'pai_mall_certificate_basic_class' );
$supplier_obj = POCO::singleton ( 'pai_mall_supplier_class' );

foreach($arr as $val)
{
    $user_id = $val['user_id'];
    $ret=$seller_obj->batch_open_service($user_id,$val['basic_type'],$data=array());
    print_r($ret);

    $ret=$seller_obj->batch_update_seller_type_id($user_id,array($val['type_id']));
    print_r($ret);


    if($val['type_id']==41)
    {
        $ret = $supplier_obj->add_supplier($user_id);
        print_r($ret);
    }

    $sql = "update test.test_seller_user set status=1 where id=".$val['id'];
    db_simple_getdata($sql,false,101);
}













?>