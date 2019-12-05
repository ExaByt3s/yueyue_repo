<?php

/**
 * 获取专题列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$topic_obj = POCO::singleton('pai_topic_class');

/**
 * 页面接收参数
 */
$id = intval($_INPUT['id']) ;


$ret = $topic_obj->get_topic_info($id);
$ret['content'] .= $ret['content_v2'];
$output_arr['data'] = $ret;



mobile_output($output_arr,false);

?>