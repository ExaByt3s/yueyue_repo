<?php

/**
 * 获取 商品分类 列表
 * 
 * @since 2015-6-24
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];

$api_obj = POCO::singleton('pai_mall_api_class');
$type_result = $api_obj->api_user_goods_type_list($user_id);  // 商家获取商品类型列表

$type_list = array();
foreach ($type_result as $value) {
    $type_list[] = array(
        'type_id' => $value['id'],
        'name' => $value['name'],
        'link' => 'yueyue://goto?user_id=' . $user_id . '&type_id=' . $value['id'] . '&type=inner_app',
    );
}

$options['data']['list'] = $type_list;
$cp->output($options);
