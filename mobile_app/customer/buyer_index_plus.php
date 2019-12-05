<?php

/**
 * 买家首页 v3.1.0
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-8-26
 */
defined('YUE_INPUT_CHECK_TOKEN') || define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$debug = $client_data['data']['param']['debug'];  // 调试
$version = $client_data['data']['version'];  // 版本
$location_id = empty($location_id) ? 101029001 : $location_id;
// 大分类 按钮
$cms_obj = new cms_system_class();
$ico_ids = array(
    '101029001' => 587, //广州
    '101001001' => 703, //北京
    '101003001' => 705, //上海
    '101022001' => 708, //成都
    '101004001' => 707, //重庆
    '101015001' => 710, //西安
    '101029002' => 875, //深圳
);
if (version_compare($version, '3.2', '>')) {  // for test
    $ico_ids['101029001'] = '996';
}
if ($version == '3.2.10') {
//    $ico_ids = array('101029001' => '996');
}
$ico_id = isset($ico_ids[$location_id]) ? $ico_ids[$location_id] : $ico_ids['101029001'];
$ico_result = $cms_obj->get_last_issue_record_list(false, '0,8', 'place_number ASC', $ico_id);  // 不多于八个
if ($debug == 'test') {
    $options['data'] = $ico_result;
    return $cp->output($options);
}
$ico_list = array();
foreach ($ico_result as $icos) {
    list($dsmall, $dbig) = explode('|', $icos['content']);   // 触发前
    list($hsmall, $hbig) = explode('|', $icos['remark']);  // 触发后
    $cid = $icos['user_id'];  // 分类
    $url = $icos['link_url'];
    parse_str($url, $i_data);
//    $text_col = strpos($i_data['url'], '/topic/index.php?topic_id=253') !== false ? '0xffbababa' : '0xff333333';
    $text_col = '0xff333333';
    $is_open = 1;
    if (strpos($i_data['url'], '/topic/index.php?topic_id=') !== false ||
        strpos($url, '/topic/index.php?topic_id=') !== false
    ) {
        $text_col = '0xffbababa';
        $is_open = 0;
    }
    $ico_list[$cid][] = array(
        'title' => $icos['title'],
        'dmid' => '', // 记录标识
        'text_col' => $text_col,  // 0xff333333 开通服务, 0xffbababa 未开通
        'is_open' => $is_open,
        'url' => $url,
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
// 分类ID 1就是技能  2就是商业 3就是娱乐 4就是生活
$cids = array(
    1 => '技能',
    2 => '商业',
    3 => '娱乐',
    4 => '生活',
);
// 分类列表
$category_list = array();
foreach ($cids as $k => $v) {
    $category_list[] = array(
        'title' => $v,
        'items' => isset($ico_list[$k]) ? $ico_list[$k] : array(),
    );
}

$rank_ids = array(
    'default' => array(
        '101029001' => 354, //广州
        '101019001' => 355, //武汉
        '101001001' => 356, //北京
        '101003001' => 357, //上海
        '101022001' => 359, //成都
        '101004001' => 358, //重庆
        '101015001' => 354, //西安
        '101024001' => 362, //新疆
        '101029002' => 922, //深圳
    ),
);
// 图片轮播
$carousel_list = array();
$rank_id = isset($rank_ids['default'][$location_id]) ? $rank_ids['default'][$location_id] : $rank_ids['default']['101029001'];
//苹果审核版
if ($version == '3.2.10') {
//    $rank_id = 287;
}
$record_result = $cms_obj->get_last_issue_record_list(false, '0,6', 'place_number DESC', $rank_id);
foreach ($record_result as $val) {
    $url = filter_var($val['link_url'], FILTER_VALIDATE_URL) ? $val['link_url'] : '';
    if (!empty($url)) {
        if ($val['link_type'] == 'inner_web') {
            $url = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
        } elseif ($val['link_type'] == 'inner_app') {
            $scheme = parse_url($url, PHP_URL_SCHEME);  // 获取协议头
            if ($scheme != 'yueyue') {
                $url = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $val['user_id'];
            }
        }
    }
    $carousel_list[] = array(
        'dmid' => 'ad-' . $rank_id,
        'title' => '',
        'img' => $val['img_url'],
        'url' => $url,
    );
}
// 获取首页数据
$rank_event_v3_obj = POCO::singleton('pai_rank_event_v3_class');

pai_log_class::add_log($client_data, 'buyer_index_plus', 'buyer_index_plus');
//$version_n = version_compare($version, '3.2.10', '=') ? '审核版' : '';
$rank_list = $rank_event_v3_obj->get_cms_rank_by_location_id('index', null, $location_id, $version_n);
if ($debug == 'test1') {
    $options['data'] = array(
        '$location_id' => $location_id,
        '$rank_list' => $rank_list,
    );
    return $cp->output($options);
}
$module_list = $recommend_list = $billboard = array();
foreach ($rank_list as $value) {
    $module_type = $value['module_type'];  // type_1 模块, type_2 推荐, type_3 榜单
    if ($module_type == 'type_1') {
        $exhibit = $banner = array();
        foreach ($value['general_info'] as $exh) {
            $url = filter_var($exh['curl'], FILTER_VALIDATE_URL) ? $exh['curl'] : '';
            $exhibit[] = array('title' => $exh['title'], 'img' => $exh['img_url'], 'desc' => $exh['content'], 'url' => $url,);
        }
        foreach ($value['banner_info'] as $bn) {
            $url = filter_var($bn['curl'], FILTER_VALIDATE_URL) ? $bn['curl'] : '';
            $banner[] = array('title' => $bn['title'], 'img' => $bn['img_url'], 'desc' => $bn['content'], 'url' => $url,);
        }
        if (empty($exhibit) && empty($banner)) {
            continue;
        }
        // 模块列表
        $module_list[] = array(
            'title' => $value['title'],
            'more' => empty($value['curl']) ? '' : $value['curl'], // 更多
            'exhibit' => $exhibit,
            'banner' => $banner,
        );
        unset($exhibit, $banner);
    } elseif ($module_type == 'type_2') {
        // 推荐列表
        foreach ($value['rank_info'] as $rec) {
            $url = filter_var($rec['curl'], FILTER_VALIDATE_URL) ? $rec['curl'] : '';
            $recommend_list[] = array('title' => $rec['title'], 'desc' => $rec['content'], 'img' => $rec['img_url'], 'url' => $url,);
        }
    } elseif ($module_type == 'type_3') {
        // 榜单列表
        $blist = array();
        foreach ($value['rank_info'] as $bres) {
            $url = filter_var($bres['curl'], FILTER_VALIDATE_URL) ? $bres['curl'] : '';
            $blist[] = array('title' => $bres['title'], 'desc' => $bres['content'], 'img' => $bres['img_url'], 'url' => $url,);
        }
        $billboard = array(
            'title' => $value['title'],
            'list' => $blist,
        );
        unset($blist);
    }
}

// TT私人定制
$ind_url = array(
    'lan' => 'http://yp.yueus.com/mall/user/person_order/index.php?type_id=0',
    'wifi' => 'http://yp-wifi.yueus.com/mall/user/person_order/index.php?type_id=0',
);
$individual = array(
    'title' => '私人订制',
    'desc' => '神马？上面都没有找到你所需要的服务？没关系，来私人订制，立即体验一对一专属服务吧！',
    'ico' => 'http://image16-d.poco.cn/yueyue/cms/20150909/14862015090913540084694318_640.png',
    'link' => 'yueyue://goto?type=inner_web&url=' . urlencode($ind_url['lan']) . '&wifi_url=' . urlencode($ind_url['wifi']) . '&showtitle=1',
);

// 搜索URL
$search_url = 'yueyue://goto?type=inner_app&type_id=&pid=1220151';

$options['data'] = array(
    'category_list' => $category_list, // 分类
    'carousel_list' => $carousel_list, // 轮播
    'module_list' => $module_list, // 模块
    'recommend_list' => $recommend_list, // 推荐列表
    'billboard' => empty($billboard) ? array('title' => '', 'list' => array()) : $billboard, // 榜单
    'individual' => $individual, // 私人订制
    'search_url' => $search_url, // 查询URL
);
return $cp->output($options);