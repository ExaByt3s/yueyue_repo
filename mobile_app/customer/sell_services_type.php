<?php

/**
 * 服务类型
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-6-29
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID

$type_obj = POCO::singleton('pai_mall_goods_type_class');
$type_result = $type_obj->get_type_cate(2);   // 获取商品分类
// 品类图标
$ico_arr = array(
    31 => 'http://image17-c.poco.cn/yueyue/cms/20150710/4810201507101813076653044.png', // 模特约拍
    12 => 'http://image17-c.poco.cn/yueyue/cms/20150710/27512015071018133169504499.png', // 影棚租赁
    5 => 'http://image17-c.poco.cn/yueyue/cms/20150710/55652015071018135068618082.png', // 摄影培训
    40 => 'http://image17-c.poco.cn/yueyue/cms/20150710/22142015071018144041408929.png', // 摄影服务
    3 => 'http://image17-c.poco.cn/yueyue/cms/20150710/80182015071018153952515487.png', // 化妆服务
    55 => 'http://image17-c.poco.cn/yueyue/cms/20150710/7019201507101815592416957.png', // 外拍活动
);
$type_list = array();
foreach ($type_result as $type) {
    $type_id = $type['id'];
    $type_list[] = array(
        'type_id' => $type_id,
        'title' => $type['name'],
        'image' => isset($ico_arr[$type_id]) ? $ico_arr[$type_id] : '',
//        'url' => 'yueyue://goto?type=inner_app&pid=1250028&&operate=add&user_id=' . $user_id . '&type_id=' . $type_id,
    );
}
$options['data'] = array(
    'title' => '选择服务类别',
    'list' => $type_list,
);
return $cp->output($options);