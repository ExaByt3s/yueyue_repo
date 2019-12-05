<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//取得模板对象
$tpl = $my_app_pai->getView('xingxing_test.tpl.htm');
$header_html = $my_app_pai->webControl('fatherday_topic_header', array(), true);
$tpl->assign('header_html', $header_html);

$tpl->output();
?>