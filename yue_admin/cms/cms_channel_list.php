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
$tpl = new SmartTemplate("cms_channel_list.tpl.htm");

/**
 * ȡƵ������
 */

$cms_db_obj = POCO::singleton ( 'cms_db_class' );
$channel_list = $cms_db_obj->get_cms_list("channel_tbl");
$tpl->assign("channel_list", $channel_list);

$tpl->output();
?>