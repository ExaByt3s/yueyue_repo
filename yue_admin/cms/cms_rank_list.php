<?php
/**
 * 添加频道
 */

/**
 * common
 */
define("G_YUE_CMS_CHECK_ADMIN",1);
include_once("cms_common.inc.php");

/**
 * 模板
 */
$tpl = new SmartTemplate("cms_rank_list.tpl.htm");


$channel_id = $_INPUT["channel_id"]*1;

$cms_db_obj = POCO::singleton ( 'cms_db_class' );

/**
 * 取频道数据
 */
$channel_info = $cms_db_obj->get_cms_list("channel_tbl");//取频道
foreach ($channel_info as $k=>$v) $option_channel[$v["channel_id"]] = $v["channel_name"];

$tpl->assign("option_channel", $option_channel);

/**
 * 取单频道榜单
 */
$channel_id > 0 && $where = 'channel_id = ' . $channel_id;
$rank_info = $cms_db_obj->get_cms_list("rank_tbl", $where, "*" ,"channel_id, sort_order");//取榜单
foreach ($rank_info as $k=>$v) $rank_info[$k]["channel_name"] = $option_channel[$v["channel_id"]];

$tpl->assign("rank_info", $rank_info);
$tpl->assign("ch_id", $channel_id);

$tpl->output();
?>