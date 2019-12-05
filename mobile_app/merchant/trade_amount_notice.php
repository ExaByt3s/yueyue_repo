<?php

/**
 * 交易数量 提醒
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-11-20
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
$options['data'] = array('message' => '接口已弃用');
return $cp->output($options);

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID

$mall_order_obj = POCO::singleton('pai_mall_order_class');
$num_result = $mall_order_obj->get_order_number_for_seller($user_id);
if ($location_id == 'test') {
    $options['data'] = $num_result;
    return $cp->output($options);
}
$options['data'] = $num_result;
return $cp->output($options);