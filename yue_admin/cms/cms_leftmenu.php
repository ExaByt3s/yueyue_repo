<?php
/** 
 * 导航
 */

/**
 * 模板
 */
define("G_YUE_CMS_CHECK_ADMIN",1);
include_once("cms_common.inc.php");
$tpl = new SmartTemplate("cms_leftmenu.tpl.htm");

/**
 * common
 */
$cms_db_obj = POCO::singleton ( 'cms_db_class' );

/**
 * 取频道数据
 */

//取频道
$channel_info = $cms_db_obj->get_cms_list("channel_tbl");
foreach ($channel_info as $k=>$v)	$option_channel[$v["channel_id"]] = $v["channel_name"];
$tpl->assign("option_channel", $option_channel);

//取排行榜
$option_rank = $cms_db_obj->get_cms_list("rank_tbl","","*","sort_order");
$tpl->assign("option_rank", $option_rank);


$tpl->output();
?>