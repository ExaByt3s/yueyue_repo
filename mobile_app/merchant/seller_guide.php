<?php

/**
 * 我的 (引导页)
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-6-26
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];

//商家版用户版本记录，暂时处理，开始
if ($user_id) {
    $version = $client_data['data']['version'];
    $add_time = date('Y-m-d H:i:s');
    $sql_str = "INSERT IGNORE INTO test.user_version_tbl(user_id, `version`, add_time) VALUES ($user_id, '{$version}', '{$add_time}')";
    db_simple_getdata($sql_str, TRUE, 101);
}
//商家版用户版本记录，暂时处理，结束
// 获取用户数据
$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // 获取用户信息
$profile = $user_result['seller_data']['profile'][0];
$type_id = $profile['type_id'];

$cetificate_url = 'http://s.yueus.com/no_support_cetificate.php';  // 认证
$enclose_url = 'http://yp.yueus.com/mall/wallet/yue_pay/show_qrcode.php';  // 马上收款
$pocket_url = 'http://s.yueus.com/pocket.php';  // 我的钱包
$user = array(
    'user_id' => $user_id,
    'name' => $profile['name'],
    'avatar' => get_seller_user_icon($user_id, 165, TRUE), // $profile['avatar'],
    'edit' => array(
        'title' => '编辑',
        'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250023&type=inner_app',
    ),
    'detail' => array(
        'title' => '查看资料库',
        'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250004&type=inner_app',
    ),
    'service' => array(
        'title' => '我的服务',
        'request' => 'yueseller://goto?user_id=' . $user_id . '&type_id=' . $type_id . '&pid=1250006&type=inner_app',
    ),
    'confirm' => array(
        'title' => '服务认证',
        'request' => 'yueseller://goto?type=inner_web&url=' . urlencode($cetificate_url) .
            '&wifi_url=' . urlencode($cetificate_url) . '&showtitle=1',
    ),
    'appraise' => array(
        'title' => '我的评价',
        'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250009&type=inner_app'
    ),
    'wallet' => array(
        'title' => '我的钱包',
        'request' => 'yueseller://goto?type=inner_web&url=' . urlencode($pocket_url) .
            '&wifi_url=' . urlencode($pocket_url) . '&showtitle=1',
    ),
    'enclose' => array(
        'title' => '马上收款',
        'request' => 'yueseller://goto?type=inner_web&url=' . urlencode($enclose_url) .
            '&wifi_url=' . urlencode($enclose_url) . '&showtitle=1',
    ),
    'setting' => array('title' => '设置', 'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250011&type=inner_app'),
);
//if (version_compare($version, '1.3', '>') || in_array($user_id, array(133114, 127361, 103511, 204887, 100049, 100001, 130968, 214380, 118259))) {
//    $user['enclose'] = array(
//        'title' => '马上收款',
//        'request' => 'yueseller://goto?type=inner_web&url=' . urlencode($enclose_url) .
//            '&wifi_url=' . urlencode($enclose_url) . '&showtitle=1',
//    );
//}
$options['data'] = $user;
return $cp->output($options);
