<?php

/**
 * 商品 详细解释页
 *
 * @since 2015-6-25
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
//require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$seller_user_id = $client_data['data']['param']['seller_user_id'];  // 商家ID
$request_platform = $client_data['data']['param']['request_platform'];   // 请求平台 ( for 兼容web 2015-7-29 )

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->show_seller_data_for_temp($seller_user_id);  // 获取用户信息
if (empty($user_result)) {
    $options['data'] = array(
        'result' => 0,
        'message' => '没有该商家详情预览!',
        'introduce' => '',
    );
    return $cp->output($options);
}
$introduce = $user_result['seller_data']['introduce'];
//$introduce = interface_content_to_ubb($introduce);

$options['data'] = array(
    'result' => 1,
    'message' => 'Success!',
    'introduce' => $introduce,
);
return $cp->output($options);
