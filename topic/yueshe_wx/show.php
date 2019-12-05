<?php
define('G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK', 1);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$id = (int)$_INPUT['id'];

$tpl = $my_app_pai->getView('show.tpl.htm');

$header_html = $my_app_pai->webControl('yueshe_wx_topic_header', array(), true);
$tpl->assign("header_html",$header_html);

$footer_html = $my_app_pai->webControl('yueshe_wx_topic_footer', array("id"=>$id), true);
$tpl->assign("footer_html",$footer_html);


$tpl->assign("id",$id);


$obj = new cms_system_class ();

$record_list = $obj->get_record_list_by_issue_id(false, $id, "0,10", "place_number ASC", $freeze=null, $where_str="");


$tpl->assign("record_list",$record_list);

$tpl->output();
 ?>