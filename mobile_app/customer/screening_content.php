<?php

/**
 * 搜索列表 筛选排序 条件 (弃用)
 *
 * @author 何耀华
 * @editor willike <chenwb@yueus.com>
 * @since 2015/10/9
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');
$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$type_id = (int)$client_data['data']['param']['type_id']; //分类ID
$options['data'] = array(
    'msg' => '该接口已弃用',
);
return $cp->output($options);
//$result = interface_get_search_screening($type_id);
//$options['data'] = $result;
//return $cp->output($options);