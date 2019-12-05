<?php
/*
 * 我的父亲节专题订单列表页
 *
 *  author 星星
 *
 * 2015-6-3
 */

include_once('/disk/data/htdocs232/poco/pai/poco_pai_common.inc.php');

//约摄对象
$pai_yueshe_topic_obj   = POCO::singleton('pai_yueshe_topic_class');

$tpl = $my_app_pai->getView('my_order_list.tpl.htm');

if(empty($yue_login_id))
{
    header("location:./fatherday_login.php");
    exit();
}

$my_order_list = $pai_yueshe_topic_obj->get_my_order_list($yue_login_id);



?>