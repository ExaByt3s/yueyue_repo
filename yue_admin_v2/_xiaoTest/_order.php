<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/8
 * @Time:   10:39
 * version: 1.0
 */

include_once('common.inc.php');

$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//订单类
//商品品类选择
$type_list = $type_obj->get_type_cate(2);
$end_sign_time = '2015-10-01';
$where_str .= "FROM_UNIXTIME(sign_time,'%Y-%m-%d') <= '".mysql_escape_string($end_sign_time)."'";

echo "<table>";
foreach($type_list as $v)
{
    //金额
    $sql_str ="SELECT SUM(total_amount) AS total_price FROM mall_db.mall_order_tbl WHERE status=8 AND {$where_str} AND type_id={$v['id']}";
    $ret = db_simple_getdata($sql_str,true,101);

    //商家数量

    /*$sql_str ="SELECT COUNT(s.user_id) AS total_user_id from mall_db.mall_seller_tbl as s left join mall_db.mall_seller_profile_tbl as p on s.user_id=p.user_id WHERE s.`status`=1 AND FIND_IN_SET(p.type_id,{$v['id']})";
    $ret = db_simple_getdata($sql_str,true,101);*/

    //在卖过商品的商家
    /*$sql_str ="SELECT count(DISTINCT(seller_user_id)) AS total_seller_user FROM mall_db.mall_order_tbl WHERE status=8 AND type_id={$v['id']} AND {$where_str}";
    $ret = db_simple_getdata($sql_str,true,101);*/

    //在线的商品总数
    /*$sql_str ="SELECT count(*) as total_goods FROM mall_db.mall_goods_tbl WHERE status=1 AND is_show=1 AND type_id={$v['id']}";
    $ret = db_simple_getdata($sql_str,true,101);*/

    //卖出的商品总数

    /*$sql_str = "SELECT COUNT(DISTINCT
(goods_id)) AS total_goods_id from mall_db.mall_order_tbl as o left join mall_db.mall_order_detail_tbl as p on o.order_id=p.order_id WHERE status=8 AND {$where_str} AND o.type_id={$v['id']}";
    $ret = db_simple_getdata($sql_str,true,101);*/
    print_r($ret);
    //$total_count = $mall_order_obj->get_order_full_list($v['id'],8,true,$where_str);
    echo "<tr>";
    echo "<td>{$v['name']}</td>";
    echo "<td>{$ret['total_goods_id']}</td>";
    echo "</tr>";
}
echo "</table>";
