<?php

/**
 * 优惠券 弹窗控制接口
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-9-6
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$request_times = intval($client_data['data']['param']['request_times']);  // 请求次数
$os_version = $client_data['data']['param']['os_version'];  // 系统版本
// ios7以下：itms-apps://ax.itunes.apple.com/WebObjects/MZStore.woa/wa/viewContentsUserReviews?type=Purple+Software&id= 935185009
// ios7以上：https://itunes.apple.com/us/app/yue-yue-zui-gao-xiao-she-ying/id935185009?l=zh&ls=1&mt=8

$urls = array(
    '6' => 'itms-apps://ax.itunes.apple.com/WebObjects/MZStore.woa/wa/viewContentsUserReviews?type=Purple+Software&id= 935185009',
    '7' => 'https://itunes.apple.com/us/app/yue-yue-zui-gao-xiao-she-ying/id935185009?l=zh&ls=1&mt=8',
);
$url = version_compare($os_version, '7.0.0', '>=') ? $urls['7'] : $urls['6'];

$msg = array(
    // 已登录
    'listed' => array(
        'title' => '约约周年庆，点评有好礼~',
        'desc' => '当用户点击“立即评价”后，即跳转AppStore对应页面，并下发20元优惠券给用户。',
    ),
    // 未登录
    'unlisted' => array(
        'title' => '约约周年庆，点评有好礼~',
        'desc' => '为庆祝约约1周年，进入AppStore为约约提供好评，即可获得20元优惠券！' . "\n\r" . '你尚未登录，登陆后进行评价，即可领取优惠券',),
);

{
    $pop_data = array(
        'pop' => 0, // 是否弹窗
        'msg' => $msg,
        'url' => $url,
    );
}

$options['data'] = $pop_data;
return $cp->output($options);
