<?php
/**
 *获取专题数据
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-03 13:48:50
 * @version 1
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
//频道
include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");
$id = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
$topic_obj = POCO::singleton('pai_topic_class');
$ret = $topic_obj->get_topic_info($id);
var_dump($ret);
?>