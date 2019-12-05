<?php

/**
 * ��Ʒ �б�ҳ
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
// on_sell ����  off_sell �¼�
$service_status = $client_data['data']['param']['service_status'];   // ����״̬
$type_id = $client_data['data']['param']['type_id'];   // ����ID
$type_id = intval($type_id); // ĳ��Ʒ��/ȫ��  0 ��ʾȫ��
$keyword = $client_data['data']['param']['keyword'];   // �����ؼ���
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
    $rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)

    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);
    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $lstart . ',' . $lcount;
}

$status = 1;  // (����)ͨ��
$is_show = 0;  // ȫ��
$action_type = 1;  // (�)������
switch ($service_status) {
    case 'on_sell':
        $is_show = 1;  // �ϼ�
        break;
    case 'off_sell':
        $is_show = 2; // �¼�
        $action_type = 3;
        break;
    case 'send':
        $is_show = 1; // �ϼ� 2015-9-2
        break;
    case 'under_review':  // ���
        $is_show = 0;
        $status = 6;  // δ��� + δͨ��
        $action_type = 4;
        break;
    case 'under_way':  // ������
        $action_type = 1;
        break;
    case 'finish_off' :  // �ѽ���
        $action_type = 2;
        break;
    default:
        $status = 1;
        $is_show = 0;  // ȫ��
        $action_type = 1;
        break;
}
$task_goods_obj = POCO::singleton('pai_mall_goods_class');

if ($type_id == 42) {  // �
    // �: 1������ 2�ѽ��� 3���¼� 4�����
    $data = array(
        'action_type' => $action_type,
    );
} else {
    // status״̬ 0δ���,1ͨ��,2δͨ��,3ɾ��; show��/�¼� 1�ϼ�,2�¼�;type_id��Ʒ����,keyword�����ؼ���
    $data = array(
        'status' => $status,
        'type_id' => $type_id,
    );
    empty($is_show) || $data['show'] = $is_show;
    empty($keyword) || $data['keyword'] = $keyword;
}

$goods_list = $task_goods_obj->user_goods_list($user_id, $data, false, 'goods_id DESC', $limit_str, '*');
if ($location_id == 'test') { //for debug
    $options['data'] = array(
        '$user_id' => $user_id,
        '$data' => $data,
        '$limit_str' => $limit_str,
        '$goods_list' => $goods_list,
    );
    return $cp->output($options);
}
// ���� �༭ҳ��PID
$edit_pids = array(
    31 => 1250028,  // ģ�ط���
    5 => 1250034,   // ��Ӱ��ѵ
    12 => 1250035,  // Ӱ������
    3 => 1250036,   // ��ױ����
    40 => 1250037,  // ��Ӱ����
    41 => 1250038,  // ��ʳ����
    43 => 1250039,  // ��������
);
// ����״̬
$status_arr = array(
    0 => 'review',
    1 => 'pass',
    2 => 'rejected',
    3 => 'remove',
);
$promotion_obj = POCO::singleton('pai_promotion_class');  // ����
$task_log_obj = POCO::singleton('pai_task_admin_log_class');  // �������
$service_list = array();
foreach ($goods_list as $goods) {
    $goods_id = $goods['goods_id'];
    $type_id = $goods['type_id'];
    $goods_status = $goods['status']; // ״̬ 0 δ��� 1 ͨ�� 2δͨ�� 3ɾ��
    if ($type_id == 42) {
        $activity_result = $task_goods_obj->get_goods_id_activity_info($goods_id);
        $price_str = sprintf('%.2f', $activity_result['min_price']) . '-' . sprintf('%.2f', $activity_result['max_price']);
        $ing_show = intval($activity_result['ing_show']);  // ���ڽ����еĻ��
        $service_info = array(
            'goods_id' => $goods_id, // ��ƷID
            'titles' => preg_replace('/&#\d+;/', '', $goods['titles']), // ��������
            'type_id' => $type_id, // �������
            'is_show' => $goods['is_show'], // ����״̬
            'images' => yueyue_resize_act_img_url($goods['images'], 640), // ͼƬչʾ
            'prices' => '��' . $price_str,
            'link' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220152&type=inner_app', // ��ת��������
            'total_showing' => '��' . intval($activity_result['total_show']) . '��',
            'showing_num' => $ing_show,
            'showing_desc' => '��������',
            'is_finish' => $ing_show > 0 ? 0 : 1,  // 0 �ɲ��� 1 ����
            'status_str' => $status_arr[$goods_status],
            'notice' => '����',
            'buy_num' => '�Ѳμӣ�' . intval($activity_result['has_join_num']) . '��', // ($goods['stock_num_total'] - $goods['stock_num']),
        );
        $service_list[] = $service_info;
        continue;
    }
    $prices_list = unserialize($goods['prices_list']);
    if (!empty($prices_list) && $service_status != 'send') {
        $min = $max = 0;
        $pro_prices_list = array();
        foreach ($prices_list as $k => $price) {
            if ($price <= 0) {
                continue;
            }
            $pro_prices_list[] = array(  // for ����
                'prices_type_id' => $k, //����
                'goods_prices' => $price, //����
            );
            $min = ($min > 0 && $min < $price) ? $min : $price;
            $max = ($max > 0 && $max > $price) ? $max : $price;
        }
        if ($min == $max) {
            $price_str = sprintf('%.2f', $min);
        } else {
            $price_str = sprintf('%.2f', $min) . '-' . sprintf('%.2f', $max);
        }
    } else {
        $price_str = sprintf('%.2f', $goods['prices']);
        $pro_prices_list = $price_str;
    }
    $score = interface_reckon_average_score($goods['total_overall_score'], $goods['review_times']);  // �û�����
    $is_show = $goods['is_show'];
    // ��ť
    $edit_action = array('title' => '�༭', 'request' => 'edit');
    $sell_status = array('title' => $is_show == 1 ? '�¼�' : '�ϼ�', 'request' => $is_show == 1 ? 'off_sell' : 'on_sell',);
    $action = array($sell_status);
    // ����༭ ����
    $edit_url = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&operate=edit&pid=' . $edit_pids[$type_id] . '&type=inner_app';
    $notice = '';
    if ($goods_status == 2) {  // ״̬ 0 δ��� 1 ͨ�� 2δͨ�� 3ɾ��
        $reject_data = array('type_id' => 2007, 'action_type' => 2, 'action_id' => $goods_id);
        $log_result = $task_log_obj->get_log_by_type_last($reject_data);  // �ܾ�ԭ��
        $notice = trim(preg_replace('/\d+:/', '', $log_result['note']));
        $action = array(array('title' => 'ԭ��', 'request' => 'notice'));
        if ($type_id == 31) {
            $action = array($edit_action, array('title' => 'ԭ��', 'request' => 'notice'));  // �����ģ�� �б༭
        } else if ($type_id != 41 && version_compare($version, '1.2', '>')) {  //
            // 1.2 �汾
            $action = array($edit_action, array('title' => 'ԭ��', 'request' => 'notice'));
        } else {
            $edit_url = '';
        }
        $notice = empty($notice) ? '���δͨ��' : $notice;
    } elseif ($goods_status == 0) {
        // ����� ģ�ط����б༭
        if ($type_id == 31) {
            $action = array($edit_action);  // �����ģ�� �б༭
        } else if ($type_id != 41 && version_compare($version, '1.2', '>')) {  //
            // 1.2 �汾,��Լ��ʳ �������б༭
            $action = array($edit_action);
        } else {
            $action = array();
            $edit_url = '';
        }
        $notice = '�����';
    } else {
        if ($type_id != 41 && version_compare($version, '1.2', '>')) {  //
            // 1.2 �汾, ������ʳ,���ж��б༭����
            $action = array($edit_action, $sell_status);
        } elseif ($type_id == 31 && version_compare($version, '1.1', '>')) {
            // ģ�ط��� �б༭
            $action = array($edit_action, $sell_status);
        } else {
            $edit_url = '';
        }
    }
    $statis_result = $task_goods_obj->get_goods_statistical($goods_id);
    if ($location_id == 'test1') { //for debug
        $options['data'] = array(
            '$goods_id' => $goods_id,
            '$statis_result' => $statis_result,
        );
        $cp->output($options);
        exit;
    }
    $service_info = array(
        'goods_id' => $goods_id, // ��ƷID
        'titles' => preg_replace('/&#\d+;/', '', $goods['titles']), // ��������
        'type_id' => $type_id, // �������
        'is_show' => $is_show, // ����״̬
        'images' => yueyue_resize_act_img_url($goods['images'], 640), // ͼƬչʾ
        'prices' => '��' . $price_str,
        'link' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250007&type=inner_app', // ��ת��������
        'edit_url' => $edit_url,
        'buy_num' => '����' . $statis_result['bill_pay_num'] . '�˹���',
        'score' => strval($score),
        'status_str' => $status_arr[$goods_status],
        'notice' => $notice,
        'action' => $action,
    );
    if (!empty($pro_prices_list) && version_compare($version, '1.2', '>')) {
        $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '��ʡ: ' . $promotion['abate'];
            $promotion['anotice'] = $promotion['notice'];
            unset($promotion['notice']);
            // ��� ������Ϣ
            $service_info = array_merge($service_info, $promotion);
        }
    }
    $service_list[] = $service_info;
}
$options['data']['list'] = $service_list; //
return $cp->output($options);
