<?php

/**
 * 品类首页
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-8-28
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$type_id = $client_data['data']['param']['type_id'];  // 大分类ID
$debug = $client_data['data']['param']['debug'];  // 调试
$version = $client_data['data']['version'];   // 版本
$location_id = empty($location_id) ? 101029001 : $location_id;
// 分类名称
$classify_name_arr = array(
    31 => '约模特',
    5 => '约培训',
    3 => '约化妆',
    12 => '商业定制',
    40 => '约摄影',
    41 => '约美食',
    42 => '约活动',
    43 => '约有趣',
    99 => '约活动',
);
$classify_title = isset($classify_name_arr[$type_id]) ? $classify_name_arr[$type_id] : '其他';
// 轮播图片
$pai_home_page_topic_obj = POCO::singleton('pai_home_page_topic_class');
$banner_result = $pai_home_page_topic_obj->get_banner_type_list($location_id, $type_id);
if ($debug == 'test') {
    $options['data'] = array(
        '$location_id' => $location_id,
        '$type_id' => $type_id,
        '$banner_result' => $banner_result,
    );
    return $cp->output($options);
}
$banner_list = array();
foreach ($banner_result as $val) {
    $title = $val['title'];
    $img = empty($val['app_img_v2']) ? $val['app_img'] : $val['app_img_v2'];  // 2015-9-10 新字段
    $url = $val['link_url'];
    if ($url && $val['link_type'] == 'inner_web') {
        $url = "yueyue://goto?type=inner_web&url=" . urlencode($url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $url));
    }
    $url = preg_replace('/\s+/', '+', $url);
    if ($version == '3.2.10') {
//        unset($url);
    }
    $banner_list[] = array('title' => $title, 'img' => yueyue_resize_act_img_url($img, 640), 'url' => $url);
}
// 分类列表
$cids_arr = array(
    //广州
    '101029001' => array(
        3 => 597, // 化妆
        5 => 595, // 培训
        12 => 599, // 商业定制
        31 => 589, // 模特
        40 => 603, // 摄影
        41 => 604, // 美食
        42 => 601, // 活动
        43 => 605, // 更多
        99 => 601, // 活动
    ),
    //武汉
    '101019001' => array(),
    //北京
    '101001001' => array(
        3 => 714, // 化妆
        5 => 713, // 培训
        12 => 715, // 商业定制
        31 => 712, // 模特
        40 => 717, // 摄影
        41 => 718, // 美食
        42 => 716, // 活动
        43 => 719, // 更多
        99 => 716, // 活动
    ),
    //上海
    '101003001' => array(
        3 => 730, // 化妆
        5 => 729, // 培训
        12 => 731, // 商业定制
        31 => 728, // 模特
        40 => 733, // 摄影
        41 => 734, // 美食
        42 => 732, // 活动
        43 => 735, // 更多
        99 => 732, // 活动
    ),
    //成都
    '101022001' => array(
        3 => 762, // 化妆
        5 => 761, // 培训
        12 => 763, // 商业定制
        31 => 760, // 模特
        40 => 765, // 摄影
        41 => 766, // 美食
        42 => 764, // 活动
        43 => 767, // 更多
        99 => 764, // 活动
    ),
    //重庆
    '101004001' => array(
        3 => 746, // 化妆
        5 => 745, // 培训
        12 => 747, // 商业定制
        31 => 744, // 模特
        40 => 749, // 摄影
        41 => 750, // 美食
        42 => 748, // 活动
        43 => 751, // 更多
        99 => 748, // 活动
    ),
    //西安
    '101015001' => array(
        3 => 794, // 化妆
        5 => 793, // 培训
        12 => 795, // 商业定制
        31 => 792, // 模特
        40 => 797, // 摄影
        41 => 798, // 美食
        42 => 796, // 活动
        43 => 799, // 更多
        99 => 796, // 活动
    ),
    //新疆
    '101024001' => array(),

    //深圳
    '101029002' => array(
        3 => 597, // 化妆
        5 => 595, // 培训
        12 => 599, // 商业定制
        31 => 876, // 模特
        40 => 881, // 摄影
        41 => 604, // 美食
        42 => 601, // 活动
        43 => 883, // 更多
        99 => 601, // 活动
    ),

    '100000000' => array(
        3 => 1070, // 化妆
        5 => 1071, // 培训
        12 => 1072, // 商业定制
        31 => 1073, // 模特
        40 => 1074, // 摄影
        41 => 1075, // 美食
        42 => 1077, // 活动
        43 => 1076, // 更多
        99 => 1077, // 活动
    ),
);
$cids = isset($cids_arr[$location_id]) ? $cids_arr[$location_id] : $cids_arr['101029001']; // 2015-9-21
//苹果审核版
if ($version == '3.2.10') {
//    $cids = array(
//        3 => 682, // 化妆
//        5 => 683, // 培训
//        12 => 684, // 商业定制
//        31 => 685, // 模特
//        40 => 686, // 摄影
//        41 => 687, // 美食
//        42 => 689, // 活动
//        43 => 688, // 更多
//        99 => 689, // 活动
//    );
}

$cms_obj = new cms_system_class();
$cid = isset($cids[$type_id]) ? $cids[$type_id] : $cids[31];
$category_result = $cms_obj->get_last_issue_record_list(false, '0,5', 'place_number ASC', $cid);  // 不多于五个
if ($debug == 'test1') {
    $options['data'] = array(
        '$type_id' => $type_id,
        '$cid' => $cid,
        '$category_result' => $category_result,
    );
    return $cp->output($options);
}
$category_list = array();
foreach ($category_result as $cg) {
    list($dsmall, $dbig) = explode('|', $cg['content']);   // 触发前
//    list($hsmall, $hbig) = explode('|', $cg['remark']);  // 触发后
    $url = $cg['link_url'];
    $url = preg_replace('/\s+/', '+', $url);  // 防止因为空格为转码而导致错误
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $link_type = $cg['link_type'];
        if ($url && $link_type == 'inner_web') {
            $url = "yueyue://goto?type=inner_web&url=" . urlencode($url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $url));
        } elseif ($link_type == 'inner_app') {
            $scheme = parse_url($url, PHP_URL_SCHEME);  // 获取协议头
            $url .= '&type_id=' . $type_id;
            if ($scheme != 'yueyue') {
                $url = '';
            }
        }
    } else {
        $url = '';
    }
    $category_list[] = array(
        'dmid' => 'ad-' . $cid . '-' . $cg['log_id'],
        'title' => $cg['title'],
        'img' => $dsmall,
        'img_big' => empty($dbig) ? $dsmall : $dbig,
        'url' => $url,
    );
}
// 模块列表
$rank_event_v3_obj = POCO::singleton('pai_rank_event_v3_class');
//$version_n = version_compare($version, '3.2.10', '=') ? '审核版' : '';
$rank_list = $rank_event_v3_obj->get_cms_rank_by_location_id('category_index', $type_id, $location_id, $version_n);
if ($debug == 'test1') {
    $options['data'] = array(
        '$location_id' => $location_id,
        '$type_id' => $type_id,
        '$rank_list' => $rank_list,
    );
    return $cp->output($options);
}
$module_list = $recommend_list = $review_list = array();
foreach ($rank_list as $value) {
    $module_type = $value['module_type'];  // type_4 模块, type_1 推荐, type_3 往期
    if ($module_type == 'type_4') {
        // 模块列表
        foreach ($value['rank_info'] as $exh) {
            $exhibit[] = array('title' => $exh['title'], 'img' => $exh['img_url'], 'url' => $exh['curl'],);
        }
        if (empty($exhibit)) {
            continue;
        }
        $module_list[] = array(
            'title' => $value['title'],
            'more' => empty($value['curl']) ? '' : $value['curl'],
            'exhibit' => $exhibit,
        );
    } elseif ($module_type == 'type_1') {
        // 推荐列表
        foreach ($value['rank_info'] as $rec) {
            $exhibit[] = array('title' => $rec['title'], 'desc' => $rec['content'], 'img' => $rec['img_url'], 'url' => $rec['curl'],);
        }
        if (empty($exhibit)) {
            continue;
        }
        $recommend_list = array(
            'title' => $value['title'],
            'more' => empty($value['curl']) ? '' : $value['curl'],
            'exhibit' => $exhibit,
        );
    } elseif ($module_type == 'type_3') {
        // 往期回顾
        foreach ($value['rank_info'] as $rev) {
            $exhibit[] = array('title' => $rev['title'], 'desc' => $rev['content'], 'img' => $rev['img_url'], 'url' => $rev['curl'],);
        }
        if (empty($exhibit)) {
            continue;
        }
        $review_list = array(
            'title' => $value['title'],
            'more' => empty($value['curl']) ? '' : $value['curl'],
            'exhibit' => $exhibit,
        );
    }
    unset($exhibit); // 脏数据问题
}

// 标签分类
switch ($location_id) {
    //北京
    case 101001001:
        $sids = array(
            3 => 722, // 化妆
            5 => 721, // 培训
            12 => 723, // 商业定制
            31 => 720, // 模特
            40 => 724, // 摄影
            41 => 726, // 美食
            42 => 725, // 活动
            43 => 727, // 更多
            99 => 725, // 活动
        );
        break;

    //上海
    case 101003001:
        $sids = array(
            3 => 738, // 化妆
            5 => 737, // 培训
            12 => 739, // 商业定制
            31 => 736, // 模特
            40 => 740, // 摄影
            41 => 742, // 美食
            42 => 741, // 活动
            43 => 743, // 更多
            99 => 741, // 活动
        );
        break;

    //重庆
    case 101004001:
        $sids = array(
            3 => 754, // 化妆
            5 => 753, // 培训
            12 => 755, // 商业定制
            31 => 752, // 模特
            40 => 756, // 摄影
            41 => 758, // 美食
            42 => 757, // 活动
            43 => 759, // 更多
            99 => 757, // 活动
        );
        break;

    //成都
    case 101022001:
        $sids = array(
            3 => 770, // 化妆
            5 => 769, // 培训
            12 => 771, // 商业定制
            31 => 768, // 模特
            40 => 772, // 摄影
            41 => 774, // 美食
            42 => 773, // 活动
            43 => 775, // 更多
            99 => 773, // 活动
        );
        break;

    //西安
    case 101015001:
        $sids = array(
            3 => 802, // 化妆
            5 => 801, // 培训
            12 => 803, // 商业定制
            31 => 800, // 模特
            40 => 804, // 摄影
            41 => 806, // 美食
            42 => 805, // 活动
            43 => 807, // 更多
            99 => 805, // 活动
        );
        break;

    case 101029002:
        $sids = array(
            3 => 611, // 化妆
            5 => 610, // 培训
            12 => 612, // 商业定制
            31 => 884, // 模特
            40 => 888, // 摄影
            41 => 615, // 美食
            42 => 613, // 活动
            43 => 616, // 更多
            99 => 613, // 活动
        );
        break;

    default:
        $sids = array(
            3 => 611, // 化妆
            5 => 610, // 培训
            12 => 612, // 商业定制
            31 => 609, // 模特
            40 => 618, // 摄影
            41 => 615, // 美食
            42 => 613, // 活动
            43 => 616, // 更多
            99 => 613, // 活动
        );
        break;
}
if (version_compare($version, '3.2.10', '=')) {
//    $sids = array(
//        3 => 611, // 化妆
//        5 => 610, // 培训
//        12 => 612, // 商业定制
//        31 => 609, // 模特
//        40 => 618, // 摄影
//        41 => 615, // 美食
//        42 => 613, // 活动
//        43 => 616, // 更多
//        99 => 613, // 活动
//    );
}

$sid = isset($sids[$type_id]) ? $sids[$type_id] : $sids[31];
$style_result = $cms_obj->get_last_issue_record_list(false, '0,20', 'place_number ASC', $sid);
if ($debug == 'test2') {
    $options['data'] = array(
        '$type_id' => $type_id,
        '$sid' => $sid,
        '$style_result' => $style_result,
    );
    return $cp->output($options);
}
$sitems = array();
foreach ($style_result as $sl) {
    $url = $sl['link_url'];
    $link_type = $sl['link_type'];
    if ($url && $link_type == 'inner_web') {
        $url = "yueyue://goto?type=inner_web&url=" . urlencode($url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $url));
    } elseif ($link_type == 'inner_app') {
        $scheme = parse_url($url, PHP_URL_SCHEME);  // 获取协议头
        if ($scheme != 'yueyue') {
            $url = '';
        } else {
            $url .= '&type_id=' . $type_id;
        }
    }
    if (empty($sl['title'])) {
        continue;
    }
    $sitems[] = array(
        'title' => $sl['title'],
        'url' => $url,
    );
}
if (version_compare($version, '3.3', '>') && ($type_id == 99 || $type_id == 42)) {
    $sitems[] = array(
        'title' => '列表测试',
        'url' => 'yueyue://goto?type=inner_app&pid=1220156&return_query=type_id%3D42%26s_action%3Dgoods&title=%E9%81%87%E8%A7%81%E6%9C%80%E7%BE%8E%E7%9A%84%E8%87%AA%E5%B7%B1&type_id=42',
    );
    $sitems[] = array(
        'title' => '标签测试',
        'url' => 'yueyue://goto?type=inner_app&pid=1220153&return_query=type_id%3D42%26detail%5B270%5D%3D377%26is_official%3D0%26status%3D10%26is_show%3D0%26edit_status%3D0&title=%E6%A0%87%E7%AD%BE%E6%B5%8B%E8%AF%95&type_id=42',
    );
    $sitems[] = array(
        'title' => '标签测试',
        'url' => 'yueyue://goto?type=inner_app&pid=1220153&return_query=type_id%3D42%26detail%5B270%5D%3D0%26is_official%3D-1%26status%3D10%26is_show%3D0%26edit_status%3D0&type_id=42',
    );
}
$style_list = array(
    'title' => $classify_title . '分类',
    'items' => $sitems,
);
// TT私人定制
$ind_url = array(
    'lan' => 'http://yp.yueus.com/mall/user/person_order/index.php?type_id=' . $type_id,
    'wifi' => 'http://yp-wifi.yueus.com/mall/user/person_order/index.php?type_id=' . $type_id,
);
//$doyen = 'http://x.eqxiu.com/s/uRQpIZac?eqrcode=1&from=singlemessage&isappinstalled=0';  // 达人
$doyen = 'http://u.eqxiu.com/s/Ij24tQuY?eqrcode=1&from=timeline&isappinstalled=0';  // 达人 2015-09-11
$individual = array(
    0 => array(
        'title' => '私人订制',
        'desc' => '神马？上面都没有找到你所需要的服务？没关系，来私人订制,立即体验一对一专属服务吧！',
        'ico' => 'http://image16-d.poco.cn/yueyue/cms/20150909/14862015090913540084694318_640.png',
        'link' => 'yueyue://goto?type=inner_web&url=' . urlencode($ind_url['lan']) . '&wifi_url=' . urlencode($ind_url['wifi']) . '&showtitle=1', // 服务列表
    ),
    // 约其他
    43 => array(
        'title' => '我也要成为达人',
        'desc' => '只要你有一技之长，就能申请成为达人，提供你的服务，将自己的时间变现！怎样，是不是心动了。摸我了解更多！',
        'ico' => 'http://image16-d.poco.cn/yueyue/cms/20150909/1965201509091354207543133_640.png',
        'link' => 'yueyue://goto?type=outside_web&url=' . urlencode($doyen) . '&wifi_url=' . urlencode($doyen), // 服务列表
    ),
);
$search_url = 'yueyue://goto?type=inner_app&type_id=' . $type_id . '&pid=1220124';
if ($type_id == 99 || $type_id == 42) {
    // 活动搜索
//	$search_url = 'yueyue://goto?type=inner_app&pid=1220076&title=%E5%85%A8%E9%83%A8%E6%B4%BB%E5%8A%A8';
    $search_url = 'yueyue://goto?type=inner_app&pid=1220098&search_type=waipai&url=' . urlencode('yueyue://goto?type=inner_app&pid=1220076&key=default');
    if (version_compare($version, '3.3', '>')) {
        $search_url = 'yueyue://goto?type=inner_app&pid=1220124&type_id=42';
    }
}
if (version_compare($version, '3.3', '>')) {
    $search_services = 'yueyue://goto?type=inner_app&pid=1220126&keyword=&type_id=' . $type_id;  // 搜索服务
    if ($type_id == 99 || $type_id == 42) { // 服务
        $search_services = 'yueyue://goto?type=inner_app&pid=1220153&keyword=&type_id=42';  // 搜索活动
    }
    $search_sellers = 'yueyue://goto?type=inner_app&pid=1220125&keyword=&type_id=' . $type_id;  // 搜索商家
    $search_url = $search_url . '&search_services=' . urlencode($search_services) . '&search_sellers=' . urlencode($search_sellers);
}

$return_list = array(
    'title' => $classify_title,
    'banner_list' => $banner_list, // 轮播
    'category_list' => $category_list, // 分类
    'module_list' => $module_list, // 模块
    'style_list' => $style_list, // 分类标签
    'recommend_list' => $recommend_list, // 推荐列表
    'review_list' => $review_list, // 轮播
    'individual' => isset($individual[$type_id]) ? $individual[$type_id] : $individual[0], // 私人订制
    'search_url' => $search_url, // 查询URL
);
if (version_compare($version, '3.2.10', '=')) {
//    unset($return_list['style_list']);  // 审核版不出标签
}
$options['data'] = $return_list;
return $cp->output($options);
