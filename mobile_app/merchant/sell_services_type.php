<?php

/**
 * ��������
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-6-29
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID

// status 1 ͨ��,2��ͨ��,0δ���,-2û����ؼ�¼,-3�̼�״̬ʧЧ
$certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');
$services_res = $certificate_service_obj->get_service_status_by_user_id($user_id, true);
if ($location_id == 'test') {
    $options['data'] = array(
        '$user_id' => $user_id,
        '$services_res' => $services_res,
    );
    return $cp->output($options);
}
// Ʒ��ͼ��
$type_ids_ = interface_type_list();  // ��ȡƷ���б�
// ҳ��PID
$pids = array(
    31 => 1250028, // ģ�ط���
    5 => 1250034,    // ��Ӱ��ѵ
    12 => 1250035,    // Ӱ������
    3 => 1250036,    // ��ױ����
    40 => 1250037,    // ��Ӱ����
    41 => 1250038,  // ��ʳ����
    43 => 1250039, // ��������
);
$services_list = array();
foreach ($services_res as $services) {
    $type_id = $services['type_id'];  // Ʒ��ID
    if ($type_id == 41) {
        // ��ʳ�ݲ��ܱ༭
        continue;
    }
    $status = $services['status'];
    if ($status == 1 || $status == 0) {
        // ͨ�� �� �����
        $type = $type_ids_[$type_id];
        if (empty($type)) {
            continue;
        }
        $type['url'] = 'yueseller://goto?type=inner_app&pid=' . $pids[$type_id] . '&&operate=add&user_id=' . $user_id . '&type_id=' . $type_id;
        $services_list[] = $type;
    }
}

$options['data'] = array(
    'title' => 'ѡ��������',
    'list' => $services_list,
);
return $cp->output($options);