<?php
/**
 * 获取 地理位置数据
 *
 * @author willike<chenwb@yueus.com>
 * @since 2015-9-28
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$limit = $client_data['data']['param']['limit'];
$version = $client_data['data']['version'];  // 版本

$location = array(
    array('name' => '广州', 'location_id' => '101029001'),
    array('name' => '北京', 'location_id' => '101001001'),
    array('name' => '上海', 'location_id' => '101003001'),
    array('name' => '成都', 'location_id' => '101022001'),
    array('name' => '重庆', 'location_id' => '101004001'),
    array('name' => '西安', 'location_id' => '101015001'),
);
if (version_compare($version, '88.8.8', '=')) {
    $location[] = array('name' => '测试', 'location_id' => '100000000');
}
if (version_compare($version, '2.2.0_r3', '=')) {
    $location = array(array('name' => '广州', 'location_id' => '101029001'),);
}
$other = array(
    array(
        'title' => '广东区域',
        'items' => array(
            array('name' => '广州', 'location_id' => '101029001'),
            array('name' => '深圳', 'location_id' => '101029002'),
        ),
    ),
    array(
        'title' => '东部区域',
        'items' => array(
            array('name' => '上海', 'location_id' => '101003001'),
        ),
    ),
);
if (version_compare($version, '3.2.10', '=')) { // 审核版
//    $location = array(array('name' => '广州', 'location_id' => '101029001'),);
//    $other = array(array(
//        'title' => '广东区域',
//        'items' => array(
//            array('name' => '广州', 'location_id' => '101029001'),
//        ),
//    ),);
}
$location_data = array(
    'service' => array(
        'title' => '已开通服务城市',
        'list' => $location,
    ),
    'other' => array(
        'title' => '其他城市',
        'list' => $other,
    ),
);
$options['data'] = $location_data;
return $cp->output($options);
