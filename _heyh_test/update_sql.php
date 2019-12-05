<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/7/27
 * Time: 17:39
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
//exit();

$seller_obj = POCO::singleton('pai_mall_seller_class');

$sql_str = "SELECT belong_user, user_id FROM mall_db.mall_goods_tbl WHERE user_id>0 GROUP BY user_id ";
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{

    $audit_id = get_audit_for_user_id($val['user_id']);
    if($audit_id)
    {
        $sql_str = "UPDATE mall_db.mall_goods_tbl SET belong_user=$audit_id WHERE user_id=$val[user_id]";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 101);

        $seller_id = get_seller_id_for_user_id($val['user_id']);
        $type_id = 31;
        if($seller_id)
        {
            //$sql_str = "INSERT INTO mall_db.mall_seller_service_belong_tbl(seller_id, type_id, user_id)
            //            VALUES ('{$seller_id}', '{$type_id}', '{$audit_id}')";
            //echo $sql_str . "<BR>";
            //db_simple_getdata($sql_str, TRUE, 101);
            $seller_obj->add_seller_service_belong($seller_id,31,$audit_id);
        }
    }

}

function get_audit_for_user_id($user_id)
{
    $sql_str = "SELECT inputer_id FROM pai_user_library_db.model_info_tbl
                WHERE uid=$user_id";
    $rs = db_simple_getdata($sql_str, TRUE, 101);
    return $rs['inputer_id'];
}

function get_seller_id_for_user_id($user_id)
{
    $sql_str = "SELECT seller_id FROM mall_db.mall_seller_tbl WHERE user_id=$user_id";
    $rs = db_simple_getdata($sql_str, TRUE, 101);
    return $rs['seller_id'];
}