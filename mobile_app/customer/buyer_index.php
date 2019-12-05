<?php

/**
 * 买家首页
 * 
 * @author heyaohua
 * @editor chenweibiao<chenwb@yueus.com>
 */
defined('YUE_INPUT_CHECK_TOKEN') || define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$debug = $client_data['data']['param']['_debug'];  // 调试参数
$version = $client_data['data']['version'];  // 版本
// 分类ID
$yuepai_arr = array(
    101029001 => 312, //广州
    101019001 => 313, //武汉
    101001001 => 314, //北京
    101003001 => 315, //上海
    101004001 => 316, //重庆
    101022001 => 318, //成都
    101015001 => 320, //西安
    101024001 => 321, //新疆
    100000000 => 1069, //测试
);
// 大分类 按钮
$cms_obj = new cms_system_class();
$ico_key = isset($yuepai_arr[$location_id]) ? $yuepai_arr[$location_id] : 312;
$ico_result = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number ASC', $ico_key);
if ($debug == 'test') {
    $options['data'] = $ico_result;
    $cp->output($options);
    exit;
}
if ($user_id == 100008) {
    $ico_result[] = array('title' => '技术测试',
        'img_url' => 'http://image17-c.poco.cn/yueyue/cms/20150709/3073201507091455072853939_640.png',
        'link_url' => 'yueyue://goto?type=inner_app&pid=1220094&query=9424&type_id=100',
        'link_type' => 'inner_app',
        'content' => 'http://image17-c.poco.cn/yueyue/cms/20150722/82322015072214211385087553.png|http://image17-c.poco.cn/yueyue/cms/20150722/35212015072214213366536123.png',
        'remark' => 'http://image17-c.poco.cn/yueyue/cms/20150722/83182015072214220081125938.png|http://image17-c.poco.cn/yueyue/cms/20150722/49132015072214214321248775.png');
}
$ico_list = array();
foreach ($ico_result as $value) {
    list($dsmall, $dbig) = explode('|', $value['content']);   // 触发前
    list($hsmall, $hbig) = explode('|', $value['remark']);  // 触发后
    $ico_list[] = array(
        'str' => $value['title'],
        'dmid' => '', // 记录标识
        'url' => $value['link_url'],
        'default' => array(
            'big' => $dbig,
            'small' => $dsmall,
        ),
        'hover' => array(
            'big' => $hbig,
            'small' => $hsmall,
        )
    );
}
//$version_name = '';
//if (version_compare($version, '3.0.10') == 0) {
//    // 苹果审核专用
//    $version_name = '审核版';
//}
// 小分类列表
$rank_event_v2_obj = POCO::singleton('pai_rank_event_v2_class');
$event_result = $rank_event_v2_obj->get_rank_event_by_location_id('index', -1, $location_id, $version_name);
if ($debug == 'test1') {
    $options['data'] = $event_result;
    $cp->output($options);
    exit;
}
$exhibit_list = array();
foreach ($event_result AS $val) {
    $exhibit_list[] = array(
        'str' => $val['headtile'],
        's_title' => $val['subtitle'],
        'img' => $val['cover_url'],
        'url' => $val['curl'] . '&user_id=' . $user_id . '&type_id=' . $val['type_id'],
    );
}
// TT私人定制
$tt_link = 'yueyue://goto?type=inner_app&pid=1220080';  // 服务列表

$options['data'] = array(
    'mid' => '', // 模板ID
    'new_layout' => array(
        'button_list' => $ico_list,
        'banner_list' => $exhibit_list,
    ),
    'tt_link' => $tt_link,
);
return $cp->output($options);
