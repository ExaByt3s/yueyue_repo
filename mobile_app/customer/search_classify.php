<?php
/**
 * 检索 分类接口
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-24
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID

// 活动搜索
$event_url = 'yueyue://goto?type=inner_app&pid=1220098&search_type=waipai&url=' . urlencode('yueyue://goto?type=inner_app&pid=1220076&key=default');
if (version_compare($version, '3.3', '>')) {
    $event_url = 'yueyue://goto?type=inner_app&pid=1220124&type_id=42';
}

$type_list = array(
    array(
        'type_id' => 31,
        'title' => '约模特',
        'url' => 'yueyue://goto?type=inner_app&pid=1220124&type_id=31',
        'normal' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144034_942852_10002_16094.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141321_602711_10002_16026.png?240x70_130'
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144011_212998_10002_16093.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141129_907814_10002_16019.png?240x70_130'
        )
    ),
    array(
        'type_id' => 5,
        'title' => '约培训',
        'url' => 'yueyue://goto?type=inner_app&pid=1220124&type_id=5',
        'normal' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144109_108523_10002_16103.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141411_737114_10002_16028.png?240x70_130'
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144052_752163_10002_16096.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141351_911791_10002_16027.png?240x70_130'
        )
    ),
    array(
        'type_id' => 3,
        'title' => '约化妆',
        'url' => 'yueyue://goto?type=inner_app&pid=1220124&type_id=3',
        'normal' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144142_807652_10002_16105.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141502_134636_10002_16037.png?240x70_130'
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144125_945500_10002_16104.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141432_590428_10002_16029.png?240x70_130'
        )
    ),
    array(
        'type_id' => 12,
        'title' => '商业定制',
        'url' => 'yueyue://goto?type=inner_app&pid=1220124&type_id=12',
        'normal' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144217_629433_10002_16107.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141536_981435_10002_16044.png?240x70_130'
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144200_515969_10002_16106.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141520_670460_10002_16039.png?240x70_130'
        )
    ),
    array(
        'type_id' => 42,
        'title' => '约活动',
        'url' => $event_url,
        'normal' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144253_37194_10002_16109.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141619_537252_10002_16054.png?240x70_130'
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144236_707464_10002_16108.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141553_979125_10002_16045.png?240x70_130'
        )
    ),
    array(
        'type_id' => 40,
        'title' => '约摄影',
        'url' => 'yueyue://goto?type=inner_app&pid=1220124&type_id=40',
        'normal' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144324_809640_10002_16111.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141751_840030_10002_16056.png?240x70_130'
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144309_771341_10002_16110.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141727_637213_10002_16055.png?240x70_130'
        )
    ),
    array(
        'type_id' => 41,
        'title' => '约美食',
        'url' => 'yueyue://goto?type=inner_app&pid=1220124&type_id=41',
        'normal' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144401_119612_10002_16113.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141946_460660_10002_16065.png?240x70_130'
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144343_519465_10002_16112.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924141810_339870_10002_16057.png?240x70_130'
        )
    ),
    array(
        'type_id' => 43,
        'title' => '约有趣',
        'url' => 'yueyue://goto?type=inner_app&pid=1220124&type_id=43',
        'normal' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144435_517940_10002_16115.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924142114_136669_10002_16072.png?240x70_130'
        ),
        'press' => array(
            'big' => 'http://image19-d.yueus.com/yueyue/20150924/20150924144418_126618_10002_16114.png?360x105_130',
            'small' => 'http://image19-d.yueus.com/yueyue/20150924/20150924142049_581297_10002_16066.png?240x70_130'
        )
    )
);

if (version_compare($version, '3.3', '>')) {
    $search_services = 'yueyue://goto?type=inner_app&pid=1220126&keyword=';  // 搜索服务
    $search_sellers = 'yueyue://goto?type=inner_app&pid=1220125&keyword=';  // 搜索商家
    $new_list = array();
    foreach ($type_list as $list) {
        $type_id = $list['type_id'];
        if ($type_id == '42') {
            $search_services = 'yueyue://goto?type=inner_app&pid=1220153&keyword=';  // 搜索活动
        }
        $list['url'] = $list['url'] . '&search_services=' . urlencode($search_services . '&type_id=' . $type_id) .
            '&search_sellers=' . urlencode($search_sellers . '&type_id=' . $type_id);
        $new_list[] = $list;
    }
    $type_list = $new_list;
}

$options['data'] = array(
    'title' => '选择搜索品类',
    'list' => $type_list,
);
return $cp->output($options);