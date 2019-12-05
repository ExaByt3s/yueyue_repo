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

$share_img = $output_arr['data']['share_text']['img'];

if(preg_match('/image17-c/',$share_img))
{
	$output_arr['data']['share_text']['img'] = str_replace('image17-c','image17',$share_img);
}

mobile_output($output_arr,false);

?>