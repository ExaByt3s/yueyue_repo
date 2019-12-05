<?php

//测试高德地图API

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//取得模板对象
$tpl = $my_app_pai->getView('publish_gaodemap.tpl.htm');


$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$header_html = $my_app_pai->webControl('PartyHeader', array(), true);
$footer_html = $my_app_pai->webControl('PartyFooter', array(), true);


$tpl->assign("act","add");
$tpl->assign("rand",time());
$tpl->assign('global_header_html', $global_header_html);
$tpl->assign('header_html', $header_html);
$tpl->assign('footer_html', $footer_html);
$tpl->output();
?>