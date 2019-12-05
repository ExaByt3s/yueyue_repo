<?php
/**
 * @desc:   消费者对商家的评价
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/21
 * @Time:   14:12
 * version: 1.0
 */
exit('暂时先不做');
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'mall_comment_seller');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$page_obj = new show_page();
$show_count = 30;

$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT."mall_comment_seller_list.tpl.htm");

$act = trim($_INPUT['act']);

$type_id = intval($_INPUT['type_id']);
$start_time = trim($_INPUT['start_time']);
$end_time = trim($_INPUT['start_time']);
$sort = trim($_INPUT['sort']);//排序



