<?php 
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$page_arr = include_once ('/disk/data/htdocs232/poco/pai/mall/user/page_url_config.inc.php');

$tpl = new SmartTemplate ( "topic_btn.tpl.htm" );

$url = urldecode($_INPUT['url']);

$tpl->assign("url",$url);

$tpl->output ();

?>