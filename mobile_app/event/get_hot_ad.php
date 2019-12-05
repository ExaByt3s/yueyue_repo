<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$location_id = $client_data['data']['param']['location_id'];
$debug = $client_data['data']['param']['debug'];
$version = $client_data['data']['version'];

include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$cms_obj = new cms_system_class();

$rand_ids = array(
    // 未登录
    'unlisted' => array(
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
    // 登陆
    'listed' => array(
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
$rank_id = isset($rand_ids['unlisted'][$location_id]) ? $rand_ids['unlisted'][$location_id] : $rand_ids['unlisted']['101029001'];
//if (!empty($user_id)) {    // 不用分角色 2015-9-17
//    $pai_user_obj = POCO::singleton('pai_user_class');
//    if ($pai_user_obj->check_role($user_id) == 'model') {
//        $rank_id = isset($rand_ids['listed'][$location_id]) ? $rand_ids['listed'][$location_id] : $rand_ids['listed']['101029001'];
//    }
//}
//测试开发 开始
if (version_compare($client_data['data']['version'], '88.8.8', '>=')) {
    $rank_id = 117;
}
//测试开发 结束
$info = $cms_obj->get_last_issue_record_list(false, '0,6', 'place_number DESC', $rank_id);
if ($debug == 'test') {
    $options['data'] = array(
        '$version' => $version,
        '$location_id' => $location_id,
        '$rank_id' => $rank_id,
        '$info' => $info,
    );
    return $cp->output($options);
}
foreach ($info AS $key => $val) {
    $ad_list[$key]['ad_img'] = $val['img_url'];
    $ad_list[$key]['ad_url'] = $val['link_url'];
    $ad_list[$key]['vid'] = "";
    $ad_list[$key]['jid'] = "";
    $ad_list[$key]['dmid'] = "ad-" . $rank_id;

    if ($val['link_type'] == 'inner_web') {
        $ad_list[$key]['ad_url'] = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
        if (!version_compare($client_data['data']['version'], '3.0.0', '<')) {
            $ad_list[$key]['ad_url'] .= '&showtitle=2';
        }
    } elseif ($val['link_type'] == 'inner_app') {
        $array_url = parse_url($val['link_url']);
        if ($array_url['scheme'] == 'yueyue') {
            $ad_list[$key]['ad_url'] = $val['link_url'];
        } else {
            $ad_list[$key]['ad_url'] = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $val['user_id'];
        }
    }
}

//测试开发 开始
if (version_compare($client_data['data']['version'], '2.2.0_r3', '=')) {
    unset($ad_list);
    $ad_list[0]['ad_img'] = 'http://image17-c.poco.cn/yueyue/cms/20150609/24032015060910394314610237.jpg';
    $ad_list[0]['ad_url'] = 'http://yp.yueus.com/mobile/app?from_app=1#topic/245';
    $ad_list[0]['vid'] = "";
    $ad_list[0]['jid'] = "";
    $ad_list[0]['dmid'] = "apple";
    $ad_list[0]['ad_url'] = "yueyue://goto?type=inner_web&url=" . urlencode($ad_list[0]['ad_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $ad_list[0]['ad_url']));

    $ad_list[1]['ad_img'] = 'http://image17-c.poco.cn/yueyue/cms/20150605/30962015060511212110977755.jpg';
    $ad_list[1]['ad_url'] = 'http://yp.yueus.com/mobile/app?from_app=1#topic/279';
    $ad_list[1]['vid'] = "";
    $ad_list[1]['jid'] = "";
    $ad_list[1]['dmid'] = "apple";
    $ad_list[1]['ad_url'] = "yueyue://goto?type=inner_web&url=" . urlencode($ad_list[1]['ad_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $ad_list[1]['ad_url']));
}

$options['data'] = array(
    'mid' => '122PT01001',
    'list' => $ad_list,
);

return $cp->output($options);
