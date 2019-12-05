<?php

/**
 * 服务类型
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-6-29
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // 当前地理位置ID
$user_id = $client_data['data']['param']['user_id'];   // 当前用户ID

// status 1 通过,2不通过,0未审核,-2没有相关纪录,-3商家状态失效
$certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');
$services_res = $certificate_service_obj->get_service_status_by_user_id($user_id, true);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$services_res' => $services_res,
    );
    return $cp->output($options);
}
// 品类图标
$type_ids_ = interface_type_list();  // 获取品类列表
// 页面PID
$pids = array(
    31 => 1250028, // 模特服务
    5 => 1250034,    // 摄影培训
    12 => 1250035,    // 影棚租赁
    3 => 1250036,    // 化妆服务
    40 => 1250037,    // 摄影服务
    41 => 1250038,  // 美食达人
    43 => 1250039, // 其它服务
);
$services_list = array();
foreach ($services_res as $services) {
    $type_id = $services['type_id'];  // 品类ID
    if ($type_id == 41) {
        // 美食暂不能编辑
        continue;
    }
    $status = $services['status'];
    if ($status == 1 || $status == 0) {
        // 通过 和 审核中
        $type = $type_ids_[$type_id];
        if (empty($type)) {
            continue;
        }
        $type['url'] = 'yueseller://goto?type=inner_app&pid=' . $pids[$type_id] . '&&operate=add&user_id=' . $user_id . '&type_id=' . $type_id;
        $services_list[] = $type;
    }
}

$options['data'] = array(
    'title' => '选择服务类别',
    'list' => $services_list,
);
return $cp->output($options);