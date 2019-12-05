<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$id = (int)$_INPUT['id'];
$phone = $_INPUT['phone'];

$tpl = $my_app_pai->getView('share.tpl.htm');

$header_html = $my_app_pai->webControl('yueshe_wx_topic_header', array(), true);
$tpl->assign("header_html",$header_html);


$footer_html = $my_app_pai->webControl('yueshe_wx_topic_footer', array("id"=>$id,"phone"=>$phone), true);
$tpl->assign("footer_html",$footer_html);



$obj = new cms_system_class ();

$record_list = $obj->get_record_list_by_issue_id(false, $id, "0,1", "place_number ASC", $freeze=null, $where_str="");
if($record_list[0]['remark'])
{
	$record_list[0]['share_img'] = str_replace("-c","",$record_list[0]['remark']);
}
else
{
	$record_list[0]['share_img'] = str_replace("-c","",$record_list[0]['img_url']);
}
$record = $record_list[0];

$tpl->assign("record",$record);
$tpl->assign("id",$id);
$tpl->assign("phone",$phone);


 
$tpl->output();
 ?>