<?php

/**
 * 商品 详细解释页
 *
 * @since 2015-6-25
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // 获取用户信息
$introduce = $user_result['seller_data']['profile'][0]['introduce'];
if ($location_id == 'test') {
    $options['data'] = $introduce;
    return $cp->output($options);
}
$contents = interface_content_replace_pics($introduce, 640);  // 替换图片大小
$contents = interface_content_to_ubb($contents); // 转ubb
$options['data'] = array('introduce' => $contents);
return $cp->output($options);
