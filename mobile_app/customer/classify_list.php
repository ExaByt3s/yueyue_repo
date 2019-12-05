<?php

/**
 * 小分类 列表
 *
 * @author heyaohua
 * @editor chenweibiao<chenwb@yueus.com>
 * @since 2015-6-23
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$type_id = $client_data['data']['param']['type_id'];  // 大分类ID
$query = (int)$client_data['data']['param']['query'];  // 榜单ID

$search_url = 'yueyue://goto?type=inner_app&type_id=' . $type_id . '&pid=1220124';  // 非外拍 跳转
if ($query == 9948) {
    // 外拍 跳转URL
    $search_url = 'yueyue://goto?type=inner_app&pid=1220098&search_type=waipai&url=' . urlencode('yueyue://goto?type=inner_app&pid=1220076&key=default');
}
$pai_home_page_topic_obj = POCO::singleton('pai_home_page_topic_class');
// Banner
//$banner_result = $pai_home_page_topic_obj->get_banner_list($query);
$banner_result = $pai_home_page_topic_obj->get_banner_type_list($location_id, $type_id);

if ($debug == 'test') {
    $options['data'] = array(
        'query' => $query,
        'list' => $banner_result,
    );
    return $cp->output($options);
}
$banner_list = array();
foreach ($banner_result as $val) {
    $top_banner['str'] = $val['title'];
    $top_banner['img'] = $val['app_img'];
    if ($val['link_url']) {
        if ($val['link_type'] != 'inner_web') {
            $top_banner['url'] = $val['link_url'];
        } else {
            $top_banner['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
        }
    } else {
        $top_banner['url'] = '';
    }
    $banner_list[] = $top_banner;
}
//$version_name = version_compare($version, '3.2.10', '=') ? '审核版' : '';
$type_id = empty($type_id) ? -1 : intval($type_id);
// 小分类列表
$rank_event_v2_obj = POCO::singleton('pai_rank_event_v2_class');
$event_result = $rank_event_v2_obj->get_rank_event_by_location_id('list', $type_id, $location_id, $version_name);
if ($debug == 'test1') {
    $options['data'] = $event_result;
    return $cp->output($options);
}
$category = array();
foreach ($event_result as $key => $val) {
    $url = $val['curl'];
    $category[] = array(
        'str' => $val['headtile'],
        'desc' => '这是一个描述!',
        'img' => $val['cover_url'],
        'url' => $url . '&user_id=' . $user_id . '&type_id=' . $val['type_id'],
    );
}
// 获取分类 信息
$str_result = $pai_home_page_topic_obj->get_category_text_by_type_id($type_id);
$title = $str_result['top1'];   // 标题
$category_title = $str_result['top2'];   // 分类标题
$category_content = $str_result['top3'];  // 分类描述
$topic_title = $str_result['top4'];   // 专题名称

if (version_compare($version, '3.3', '>')) {
    $search_services = 'yueyue://goto?type=inner_app&pid=1220126&keyword=&type_id=' . $type_id;  // 搜索服务
    if ($type_id == 99 || $type_id == 42 || $query == 9948) { // 活动
        $search_services = 'yueyue://goto?type=inner_app&pid=1220153&keyword=&type_id=42';  // 搜索活动
    }
    $search_url = 'yueyue://goto?type=inner_app&type_id=' . $type_id . '&pid=1220124';
    $search_sellers = 'yueyue://goto?type=inner_app&pid=1220125&keyword=&type_id=' . $type_id;  // 搜索商家
    $search_url = $search_url . '&search_services=' . urlencode($search_services) . '&search_sellers=' . urlencode($search_sellers);
}
$classify_list = array(
    'pid' => '',
    'mid' => '', // 模板ID
    'search_url' => $search_url,
    'title' => empty($title) ? '' : $title,
    'banner_list' => $banner_list,
    'category_title' => $category_title,
    'category_content' => $category_content,
    'category_list' => $category, // 分类列表
    'topic_title' => $topic_title,
    'tt_link' => 'yueyue://goto?type=inner_app&pid=1220080', // TT服务列表
);
$options['data'] = $classify_list;
return $cp->output($options);
