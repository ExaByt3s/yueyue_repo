<?php

/**
 * 获取 商品分类 列表 [ 未用接口 ]
 * 
 * @since 2015-6-24
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];

$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$type_result = $task_goods_obj->user_goods_type_list($user_id);  // 商家获取商品类型列表
if ($location_id == 'test') {
    $options['data'] = $type_result;
    return $cp->output($options);
}

$type_list = array();
foreach ($type_result as $value) {
    $type_list[] = array(
        'type_id' => $value['id'],
        'name' => $value['name'],
        'link' => 'yueyue://goto?user_id=' . $user_id . '&type_id=' . $value['id'] . '&type=inner_app',
    );
}

$options['data']['list'] = $type_list;
return $cp->output($options);
