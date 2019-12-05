<?php
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/../../yue_res_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$head_html = include_once($file_dir. '/../webcontrol/head.php');
$topic_id = intval($_GET['topic_id']);

$topic_obj = POCO::singleton('pai_topic_class');
$ret = $topic_obj->get_topic_info($topic_id);
$ret['content'] .= $ret['content_v2'];

$tpl = new SmartTemplate("index.tpl.html");

$tpl ->assign('topic_id',$topic_id);
$tpl ->assign('head_html',$head_html);
$tpl ->assign('content',$ret['content']);
$tpl ->assign('title',$ret['title']);
$tpl->output();
?>
