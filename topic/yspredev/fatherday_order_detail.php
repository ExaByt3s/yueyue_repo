<?php
/*
 * 我的父亲节专题订单详细页
 *
 *  author 星星
 *
 * 2015-6-3
 */

include_once('/disk/data/htdocs232/poco/pai/poco_pai_common.inc.php');

$id = (int)$_INPUT['id'];
//约摄对象
$pai_yueshe_topic_obj   = POCO::singleton('pai_yueshe_topic_class');

$tpl = $my_app_pai->getView('my_order_list.tpl.htm');



$order_info = $pai_yueshe_topic_obj->get_order_info($id);



?>