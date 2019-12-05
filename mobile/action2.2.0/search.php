<?php
/**
 * 获得搜索标签
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$obj = POCO::singleton ( 'pai_search_tag_class' );

$ret = $obj->get_tag_list(false, '', 'id DESC', '0,500');

$output_arr['code'] = $ret?1:0;
$output_arr['msg'] = $ret ? '成功' : '失败';
$output_arr['list'] = $ret;


mobile_output($output_arr,false);

?>