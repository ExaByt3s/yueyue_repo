<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$id = (int)$_INPUT['id'];

$tpl = $my_app_pai->getView('intro.tpl.htm');

$header_html = $my_app_pai->webControl('yueshe_wx_topic_header', array(), true);
$tpl->assign("header_html",$header_html);

$footer_html = $my_app_pai->webControl('yueshe_wx_topic_footer', array("id"=>$id), true);
$tpl->assign("footer_html",$footer_html);


$tpl->assign("id",$id);

$tpl->output();
 ?>