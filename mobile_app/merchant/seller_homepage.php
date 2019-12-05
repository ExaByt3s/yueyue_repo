<?php
/**
 * 商家 首页
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-30
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID

$cms_obj = new cms_system_class();
// 公告栏
$board = interface_get_seller_issue_record(1010, '0,6', $cms_obj);
// 通知栏 (头条)
$notice = interface_get_seller_issue_record(1011, '0,6', $cms_obj);

$promotion_url = 'http://yp.yueus.com/mall/seller/sales/list.php';  // 促销中心
$order_url = 'http://yp.yueus.com/mall/seller/trade/list.php';  // 接单中心
if (version_compare($version, '1.3', '>')) { // for test
    $promotion_url = 'http://yp.yueus.com/mall/seller/test/sales/list.php';  // 促销中心
    $order_url = 'http://yp.yueus.com/mall/seller/test/trade/list.php';  // 接单中心
}

// 便捷操作
$panels = array(
    array(
        'title' => '服务发布',
        'url' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250040&type=inner_app',
        'default' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095950_175742_10002_15019.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095413_400420_10002_14994.png?88x88_130',
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095933_877741_10002_15015.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151015/20151015142736_429527_10002_13375.png?88x88_130',
        ),
    ),
    array(
        'title' => '服务管理',
        'url' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250006&type=inner_app',
        'default' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100013_928997_10002_15021.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095629_738360_10002_15001.png?88x88_130',
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100002_688626_10002_15020.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095446_638166_10002_14995.png?88x88_130',
        ),
    ),
    array(
        'title' => '订单管理',
        'url' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250002&type=inner_app',
        'default' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100037_34320_10002_15023.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095720_102264_10002_15004.png?88x88_130',
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100025_825870_10002_15022.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095714_159379_10002_15003.png?88x88_130',
        ),
    ),
    array(
        'title' => '促销中心',
        'url' => 'yueseller://goto?type=inner_web&url=' . urlencode($promotion_url) . '&wifi_url=' . urlencode($promotion_url) . '&showtitle=1',
        'default' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100102_333748_10002_15025.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095803_405447_10002_15009.png?88x88_130',
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100050_924902_10002_15024.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095745_672892_10002_15008.png?88x88_130',
        ),
    ),
    array(
        'title' => '接单中心',
        'url' => 'yueseller://goto?type=inner_web&url=' . urlencode($order_url) . '&wifi_url=' . urlencode($order_url) . '&showtitle=1',
        'default' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100128_359034_10002_15027.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095833_169300_10002_15011.png?88x88_130',
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100115_672317_10002_15026.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095818_182509_10002_15010.png?88x88_130',
        ),
    ),
    array(
        'title' => '客服',
        'url' => 'yueseller://goto?user_id=' . $user_id . '&receiver_id=10001&receiver_name=' .
            urlencode(mb_convert_encoding('约约商家客服', 'utf8', 'gbk')) . '&pid=1250025&type=inner_app',
        'default' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100157_856921_10002_15037.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095858_491430_10002_15014.png?88x88_130',
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20151016/20151016100145_885995_10002_15028.png?132x132_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20151016/20151016095845_82654_10002_15013.png?88x88_130',
        ),
    ),
);

$home_info = array(
    'user' => array(
        'user_id' => $user_id,
        'name' => get_seller_nickname_by_user_id($user_id),
        'avatar' => get_seller_user_icon($user_id, 165, TRUE), // $profile['avatar'],
        'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250004&type=inner_app',
    ),
    'board' => $board,  // 公告栏
    'notice' => $notice,  // 通知栏
    'panels' => $panels, // 便捷操作
);
$options['data'] = $home_info;
return $cp->output($options);