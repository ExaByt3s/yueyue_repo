<?php

/**
 * 热门搜索表情
 * 
 * @since 2015-7-17
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
//include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$type_id = intval($client_data['data']['param']['type_id']);   // 大分类ID
// 3 化妆,31 模特,40 摄影师,12 影棚,5 培训
if (in_array($type_id, array('3', '12','5'))) {
    $tags_arr = array(
        '三人', '室内', '室外',
    );
} else {
    $tags_arr = array(
        '真空', '内衣/比坚尼', '甜美', '糖水', '情绪', '日韩', '欧美', '古装', '文艺复古', '走秀', '淘宝', '礼仪/车展',
    );
}

$tags = array(
    'mid' => '122SE01001',
    'dmid' => 's001',
    'tag_num' => strval(count($tags)),
    'seller_tags' => array(),
    'service_tags' => array(),
);
$options['data'] = $tags;
return $cp->output($options);
