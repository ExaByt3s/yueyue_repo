<?php
include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("topic_iframe.tpl.htm");
$topic_obj = POCO::singleton('pai_topic_class');

$config = include_once '/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php';
$cache_ver = $config['wx']['cache_ver'];

$id = $_INPUT['id'];

$topic_info = $topic_obj->get_topic_info($id);

$url = "http://yp.yueus.com/mall/user/topic/index.php?topic_id={$id}&online=1";

$img = pai_activity_code_class::get_qrcode_img($url);

$tpl->assign('img', $img);
$tpl->assign($topic_info);
$tpl->output();

?>
