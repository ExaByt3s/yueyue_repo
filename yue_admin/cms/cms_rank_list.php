<?php
/**
 * ���Ƶ��
 */

/**
 * common
 */
define("G_YUE_CMS_CHECK_ADMIN",1);
include_once("cms_common.inc.php");

/**
 * ģ��
 */
$tpl = new SmartTemplate("cms_rank_list.tpl.htm");


$channel_id = $_INPUT["channel_id"]*1;

$cms_db_obj = POCO::singleton ( 'cms_db_class' );

/**
 * ȡƵ������
 */
$channel_info = $cms_db_obj->get_cms_list("channel_tbl");//ȡƵ��
foreach ($channel_info as $k=>$v) $option_channel[$v["channel_id"]] = $v["channel_name"];

$tpl->assign("option_channel", $option_channel);

/**
 * ȡ��Ƶ����
 */
$channel_id > 0 && $where = 'channel_id = ' . $channel_id;
$rank_info = $cms_db_obj->get_cms_list("rank_tbl", $where, "*" ,"channel_id, sort_order");//ȡ��
foreach ($rank_info as $k=>$v) $rank_info[$k]["channel_name"] = $option_channel[$v["channel_id"]];

$tpl->assign("rank_info", $rank_info);
$tpl->assign("ch_id", $channel_id);

$tpl->output();
?>