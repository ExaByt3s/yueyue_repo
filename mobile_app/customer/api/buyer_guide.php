<?php

/**
 * 买家首页 引导页
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-7-1
 * @version 1.0 Beta
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once("../../protocol_common.inc.php");

$cp = new poco_communication_protocol_class();
$client_data = $cp->get_input(array('be_check_token' => false));
$url_test = false;  // 是否测试链接
$user_id = $client_data['data']['param']['user_id'];
//$access_token = $client_data['data']['param']['access_token'];
//$ret = $cp->check_access_token_is_matching($user_id, $access_token);
//if (!$ret) {
//    $cp->output(array('data' => array()));
//    exit;
//}
$pocket_url = 'http://yp.yueus.com/mall/seller/test/pocket.php';   // 钱包URL
$user = array(
    'user_id' => $user_id,
    'name' => get_user_nickname_by_user_id($user_id),
    'avatar' => get_user_icon($user_id, 86, TRUE),
    'edit' => array(
        'title' => '更新资料',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=1220110&type=inner_app',
    ),
    'detail' => array(
        array('title' => '签到券', 'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=1220092&type=inner_app'),
        array('title' => '订单', 'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=1220105&type=inner_app'),
    ),
    'recharge' => array(
        'title' => '充值',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=&type=inner_app'
    ),
    'wallet' => array(
        'title' => '钱包',
        'request' => 'yueyue://goto?type=inner_web&url=' . url_switch($pocket_url, $url_test) . '&wifi_url= ' . url_switch($pocket_url, $url_test),
        'remain' => '0'
    ),
    'coupons' => array(
        'title' => '优惠券',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=&type=inner_app'
    ),
    'credit' => array(
        'title' => '信用等级',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=&type=inner_app'
    ),
    'sharing' => array(
        'title' => '分享我的摄影师卡',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=1220025&type=inner_app'
    ),
    'setting' => array(
        'title' => '设置',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=&type=inner_app'
    ),
);

$options['data'] = $user;
$cp->output($options);

/**
 * 链接 切换开关
 * 
 * @param string $url 链接
 * @param boolean $test 是否是测试
 * @param string $test_str url 测试附加字段
 * @return string 
 */
function url_switch($url, $test = TRUE, $test_str = 'test') {
    $url = strpos($url, '://') ? $url : urldecode($url);  // 是否转编译的URL
    if (stripos($url, 'http://') === FALSE) {
        return $url;
    }
    $test_str = empty($test_str) ? 'test' : $test_str;
    if ($test === TRUE) { // 测试链接
        $is_test = strpos($url, '/' . trim($test_str, '/') . '/') ? TRUE : FALSE;
        $return_url = ($is_test === TRUE) ? $url : str_replace('yueus.com/', 'yueus.com/' . $test_str . '/', $url);
    } else {
        $return_url = str_replace('yueus.com/' . $test_str . '/', 'yueus.com/', $url);
    }
    return urlencode($return_url);
}
