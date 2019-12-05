<?php

/**
 * Ʒ����ҳ
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-8-28
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$type_id = $client_data['data']['param']['type_id'];  // �����ID
$debug = $client_data['data']['param']['debug'];  // ����
$version = $client_data['data']['version'];   // �汾
$location_id = empty($location_id) ? 101029001 : $location_id;
// ��������
$classify_name_arr = array(
    31 => 'Լģ��',
    5 => 'Լ��ѵ',
    3 => 'Լ��ױ',
    12 => '��ҵ����',
    40 => 'Լ��Ӱ',
    41 => 'Լ��ʳ',
    42 => 'Լ�',
    43 => 'Լ��Ȥ',
    99 => 'Լ�',
);
$classify_title = isset($classify_name_arr[$type_id]) ? $classify_name_arr[$type_id] : '����';
// �ֲ�ͼƬ
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
    $img = empty($val['app_img_v2']) ? $val['app_img'] : $val['app_img_v2'];  // 2015-9-10 ���ֶ�
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
// �����б�
$cids_arr = array(
    //����
    '101029001' => array(
        3 => 597, // ��ױ
        5 => 595, // ��ѵ
        12 => 599, // ��ҵ����
        31 => 589, // ģ��
        40 => 603, // ��Ӱ
        41 => 604, // ��ʳ
        42 => 601, // �
        43 => 605, // ����
        99 => 601, // �
    ),
    //�人
    '101019001' => array(),
    //����
    '101001001' => array(
        3 => 714, // ��ױ
        5 => 713, // ��ѵ
        12 => 715, // ��ҵ����
        31 => 712, // ģ��
        40 => 717, // ��Ӱ
        41 => 718, // ��ʳ
        42 => 716, // �
        43 => 719, // ����
        99 => 716, // �
    ),
    //�Ϻ�
    '101003001' => array(
        3 => 730, // ��ױ
        5 => 729, // ��ѵ
        12 => 731, // ��ҵ����
        31 => 728, // ģ��
        40 => 733, // ��Ӱ
        41 => 734, // ��ʳ
        42 => 732, // �
        43 => 735, // ����
        99 => 732, // �
    ),
    //�ɶ�
    '101022001' => array(
        3 => 762, // ��ױ
        5 => 761, // ��ѵ
        12 => 763, // ��ҵ����
        31 => 760, // ģ��
        40 => 765, // ��Ӱ
        41 => 766, // ��ʳ
        42 => 764, // �
        43 => 767, // ����
        99 => 764, // �
    ),
    //����
    '101004001' => array(
        3 => 746, // ��ױ
        5 => 745, // ��ѵ
        12 => 747, // ��ҵ����
        31 => 744, // ģ��
        40 => 749, // ��Ӱ
        41 => 750, // ��ʳ
        42 => 748, // �
        43 => 751, // ����
        99 => 748, // �
    ),
    //����
    '101015001' => array(
        3 => 794, // ��ױ
        5 => 793, // ��ѵ
        12 => 795, // ��ҵ����
        31 => 792, // ģ��
        40 => 797, // ��Ӱ
        41 => 798, // ��ʳ
        42 => 796, // �
        43 => 799, // ����
        99 => 796, // �
    ),
    //�½�
    '101024001' => array(),

    //����
    '101029002' => array(
        3 => 597, // ��ױ
        5 => 595, // ��ѵ
        12 => 599, // ��ҵ����
        31 => 876, // ģ��
        40 => 881, // ��Ӱ
        41 => 604, // ��ʳ
        42 => 601, // �
        43 => 883, // ����
        99 => 601, // �
    ),

    '100000000' => array(
        3 => 1070, // ��ױ
        5 => 1071, // ��ѵ
        12 => 1072, // ��ҵ����
        31 => 1073, // ģ��
        40 => 1074, // ��Ӱ
        41 => 1075, // ��ʳ
        42 => 1077, // �
        43 => 1076, // ����
        99 => 1077, // �
    ),
);
$cids = isset($cids_arr[$location_id]) ? $cids_arr[$location_id] : $cids_arr['101029001']; // 2015-9-21
//ƻ����˰�
if ($version == '3.2.10') {
//    $cids = array(
//        3 => 682, // ��ױ
//        5 => 683, // ��ѵ
//        12 => 684, // ��ҵ����
//        31 => 685, // ģ��
//        40 => 686, // ��Ӱ
//        41 => 687, // ��ʳ
//        42 => 689, // �
//        43 => 688, // ����
//        99 => 689, // �
//    );
}

$cms_obj = new cms_system_class();
$cid = isset($cids[$type_id]) ? $cids[$type_id] : $cids[31];
$category_result = $cms_obj->get_last_issue_record_list(false, '0,5', 'place_number ASC', $cid);  // ���������
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
    list($dsmall, $dbig) = explode('|', $cg['content']);   // ����ǰ
//    list($hsmall, $hbig) = explode('|', $cg['remark']);  // ������
    $url = $cg['link_url'];
    $url = preg_replace('/\s+/', '+', $url);  // ��ֹ��Ϊ�ո�Ϊת������´���
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $link_type = $cg['link_type'];
        if ($url && $link_type == 'inner_web') {
            $url = "yueyue://goto?type=inner_web&url=" . urlencode($url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $url));
        } elseif ($link_type == 'inner_app') {
            $scheme = parse_url($url, PHP_URL_SCHEME);  // ��ȡЭ��ͷ
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
// ģ���б�
$rank_event_v3_obj = POCO::singleton('pai_rank_event_v3_class');
//$version_n = version_compare($version, '3.2.10', '=') ? '��˰�' : '';
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
    $module_type = $value['module_type'];  // type_4 ģ��, type_1 �Ƽ�, type_3 ����
    if ($module_type == 'type_4') {
        // ģ���б�
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
        // �Ƽ��б�
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
        // ���ڻع�
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
    unset($exhibit); // ����������
}

// ��ǩ����
switch ($location_id) {
    //����
    case 101001001:
        $sids = array(
            3 => 722, // ��ױ
            5 => 721, // ��ѵ
            12 => 723, // ��ҵ����
            31 => 720, // ģ��
            40 => 724, // ��Ӱ
            41 => 726, // ��ʳ
            42 => 725, // �
            43 => 727, // ����
            99 => 725, // �
        );
        break;

    //�Ϻ�
    case 101003001:
        $sids = array(
            3 => 738, // ��ױ
            5 => 737, // ��ѵ
            12 => 739, // ��ҵ����
            31 => 736, // ģ��
            40 => 740, // ��Ӱ
            41 => 742, // ��ʳ
            42 => 741, // �
            43 => 743, // ����
            99 => 741, // �
        );
        break;

    //����
    case 101004001:
        $sids = array(
            3 => 754, // ��ױ
            5 => 753, // ��ѵ
            12 => 755, // ��ҵ����
            31 => 752, // ģ��
            40 => 756, // ��Ӱ
            41 => 758, // ��ʳ
            42 => 757, // �
            43 => 759, // ����
            99 => 757, // �
        );
        break;

    //�ɶ�
    case 101022001:
        $sids = array(
            3 => 770, // ��ױ
            5 => 769, // ��ѵ
            12 => 771, // ��ҵ����
            31 => 768, // ģ��
            40 => 772, // ��Ӱ
            41 => 774, // ��ʳ
            42 => 773, // �
            43 => 775, // ����
            99 => 773, // �
        );
        break;

    //����
    case 101015001:
        $sids = array(
            3 => 802, // ��ױ
            5 => 801, // ��ѵ
            12 => 803, // ��ҵ����
            31 => 800, // ģ��
            40 => 804, // ��Ӱ
            41 => 806, // ��ʳ
            42 => 805, // �
            43 => 807, // ����
            99 => 805, // �
        );
        break;

    case 101029002:
        $sids = array(
            3 => 611, // ��ױ
            5 => 610, // ��ѵ
            12 => 612, // ��ҵ����
            31 => 884, // ģ��
            40 => 888, // ��Ӱ
            41 => 615, // ��ʳ
            42 => 613, // �
            43 => 616, // ����
            99 => 613, // �
        );
        break;

    default:
        $sids = array(
            3 => 611, // ��ױ
            5 => 610, // ��ѵ
            12 => 612, // ��ҵ����
            31 => 609, // ģ��
            40 => 618, // ��Ӱ
            41 => 615, // ��ʳ
            42 => 613, // �
            43 => 616, // ����
            99 => 613, // �
        );
        break;
}
if (version_compare($version, '3.2.10', '=')) {
//    $sids = array(
//        3 => 611, // ��ױ
//        5 => 610, // ��ѵ
//        12 => 612, // ��ҵ����
//        31 => 609, // ģ��
//        40 => 618, // ��Ӱ
//        41 => 615, // ��ʳ
//        42 => 613, // �
//        43 => 616, // ����
//        99 => 613, // �
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
        $scheme = parse_url($url, PHP_URL_SCHEME);  // ��ȡЭ��ͷ
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
        'title' => '�б����',
        'url' => 'yueyue://goto?type=inner_app&pid=1220156&return_query=type_id%3D42%26s_action%3Dgoods&title=%E9%81%87%E8%A7%81%E6%9C%80%E7%BE%8E%E7%9A%84%E8%87%AA%E5%B7%B1&type_id=42',
    );
    $sitems[] = array(
        'title' => '��ǩ����',
        'url' => 'yueyue://goto?type=inner_app&pid=1220153&return_query=type_id%3D42%26detail%5B270%5D%3D377%26is_official%3D0%26status%3D10%26is_show%3D0%26edit_status%3D0&title=%E6%A0%87%E7%AD%BE%E6%B5%8B%E8%AF%95&type_id=42',
    );
    $sitems[] = array(
        'title' => '��ǩ����',
        'url' => 'yueyue://goto?type=inner_app&pid=1220153&return_query=type_id%3D42%26detail%5B270%5D%3D0%26is_official%3D-1%26status%3D10%26is_show%3D0%26edit_status%3D0&type_id=42',
    );
}
$style_list = array(
    'title' => $classify_title . '����',
    'items' => $sitems,
);
// TT˽�˶���
$ind_url = array(
    'lan' => 'http://yp.yueus.com/mall/user/person_order/index.php?type_id=' . $type_id,
    'wifi' => 'http://yp-wifi.yueus.com/mall/user/person_order/index.php?type_id=' . $type_id,
);
//$doyen = 'http://x.eqxiu.com/s/uRQpIZac?eqrcode=1&from=singlemessage&isappinstalled=0';  // ����
$doyen = 'http://u.eqxiu.com/s/Ij24tQuY?eqrcode=1&from=timeline&isappinstalled=0';  // ���� 2015-09-11
$individual = array(
    0 => array(
        'title' => '˽�˶���',
        'desc' => '�������涼û���ҵ�������Ҫ�ķ���û��ϵ����˽�˶���,��������һ��һר������ɣ�',
        'ico' => 'http://image16-d.poco.cn/yueyue/cms/20150909/14862015090913540084694318_640.png',
        'link' => 'yueyue://goto?type=inner_web&url=' . urlencode($ind_url['lan']) . '&wifi_url=' . urlencode($ind_url['wifi']) . '&showtitle=1', // �����б�
    ),
    // Լ����
    43 => array(
        'title' => '��ҲҪ��Ϊ����',
        'desc' => 'ֻҪ����һ��֮�������������Ϊ���ˣ��ṩ��ķ��񣬽��Լ���ʱ����֣��������ǲ����Ķ��ˡ������˽���࣡',
        'ico' => 'http://image16-d.poco.cn/yueyue/cms/20150909/1965201509091354207543133_640.png',
        'link' => 'yueyue://goto?type=outside_web&url=' . urlencode($doyen) . '&wifi_url=' . urlencode($doyen), // �����б�
    ),
);
$search_url = 'yueyue://goto?type=inner_app&type_id=' . $type_id . '&pid=1220124';
if ($type_id == 99 || $type_id == 42) {
    // �����
//	$search_url = 'yueyue://goto?type=inner_app&pid=1220076&title=%E5%85%A8%E9%83%A8%E6%B4%BB%E5%8A%A8';
    $search_url = 'yueyue://goto?type=inner_app&pid=1220098&search_type=waipai&url=' . urlencode('yueyue://goto?type=inner_app&pid=1220076&key=default');
    if (version_compare($version, '3.3', '>')) {
        $search_url = 'yueyue://goto?type=inner_app&pid=1220124&type_id=42';
    }
}
if (version_compare($version, '3.3', '>')) {
    $search_services = 'yueyue://goto?type=inner_app&pid=1220126&keyword=&type_id=' . $type_id;  // ��������
    if ($type_id == 99 || $type_id == 42) { // ����
        $search_services = 'yueyue://goto?type=inner_app&pid=1220153&keyword=&type_id=42';  // �����
    }
    $search_sellers = 'yueyue://goto?type=inner_app&pid=1220125&keyword=&type_id=' . $type_id;  // �����̼�
    $search_url = $search_url . '&search_services=' . urlencode($search_services) . '&search_sellers=' . urlencode($search_sellers);
}

$return_list = array(
    'title' => $classify_title,
    'banner_list' => $banner_list, // �ֲ�
    'category_list' => $category_list, // ����
    'module_list' => $module_list, // ģ��
    'style_list' => $style_list, // �����ǩ
    'recommend_list' => $recommend_list, // �Ƽ��б�
    'review_list' => $review_list, // �ֲ�
    'individual' => isset($individual[$type_id]) ? $individual[$type_id] : $individual[0], // ˽�˶���
    'search_url' => $search_url, // ��ѯURL
);
if (version_compare($version, '3.2.10', '=')) {
//    unset($return_list['style_list']);  // ��˰治����ǩ
}
$options['data'] = $return_list;
return $cp->output($options);
