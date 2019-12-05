<?php

/**
 * ��Ʒ �б�ҳ
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$version = $client_data['data']['version'];
$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$seller_user_id = $client_data['data']['param']['seller_user_id'];  // �̼��û�ID
$type_id = (int)$client_data['data']['param']['type_id'];  // ����ID
// on_sell ����  off_sell �¼�
$service_status = $client_data['data']['param']['service_status'];   // ����״̬
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
    $rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)

    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);
    $lstart = ($page - 1) * $rows;  // ��ʼλ��
    $lcount = $rows;
    $limit_str = $lstart . ',' . $lcount;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lstart = $lstart < 0 ? 0 : $lstart;
    $lcount = $lcount > 100 ? 100 : ($lcount < 1 ? 1 : $lcount);
    $limit_str = $lstart . ',' . $lcount;
}
if (empty($seller_user_id)) {
    $options['data'] = array(
        'total' => 0,
        'list' => array(),
        'share' => array(),
    );
    return $cp->output($options);
}
switch ($service_status) {
    case 'on_sell':
        $is_show = 1;  // �ϼ�
        $action_type = 5;
        break;
    case 'off_sell':
        $is_show = 2; // �¼�
        $action_type = 3;
        break;
    default:
        $is_show = 1;  // Ĭ���ϼ�
        $action_type = 5;
        break;
}

$task_goods_obj = POCO::singleton('pai_mall_goods_class');
// status״̬ 0δ���,1ͨ��,2δͨ��,3ɾ��;show��/�¼� 1�ϼ�,2�¼�;type_id��Ʒ����,keyword�����ؼ���
$data = array(
//    'status' => 1,
    'type_id' => $type_id,
);
empty($is_show) || $data['show'] = $is_show;
empty($keyword) || $data['keyword'] = $keyword;
// �
$event_data = array(
    'action_type' => $action_type,
);
$goods_total = $task_goods_obj->user_goods_list($seller_user_id, $data, true);  // ��������
$goods_list = array();
if ($goods_total > 0 && $goods_total >= $lstart) {
    $goods_list = $task_goods_obj->user_goods_list($seller_user_id, $data, false, 'goods_id DESC', $limit_str, '*');
    if ($location_id == 'test') { //for debug
        $options['data'] = array(
            'limit_str' => $limit_str,
            '$goods_list' => $goods_list,
        );
        return $cp->output($options);
    }
}
if (version_compare($version, '3.3', '>')) {
    $leave = $lstart + $lcount - $goods_total; // �б�ȱ��
    // ���� �����
    $event_total = $task_goods_obj->user_goods_list($seller_user_id, $event_data, true);
    $goods_total += $event_total;
    // ��ȡ��б�
    if ($leave <= 0 || $event_total <= 0) {  // �����
        $event_list = array();
    } else if ($leave <= $lcount) {
        $limit_str = 0 . ',' . $leave;
        $event_list = $task_goods_obj->user_goods_list($seller_user_id, $event_data, false, 'goods_id DESC', $limit_str, '*');
    } else {
        $limit_str = ($leave - $lcount) . ',' . $lcount;
        $event_list = $task_goods_obj->user_goods_list($seller_user_id, $event_data, false, 'goods_id DESC', $limit_str, '*');
    }
    if ($location_id == 'test1') { //for debug
        $options['data'] = array(
            'limit_str' => $limit_str,
            '$event_list' => $event_list,
        );
        return $cp->output($options);
    }
    if (!empty($event_list)) {
        $goods_list = array_merge($goods_list, $event_list);
    }
}
$promotion_obj = POCO::singleton('pai_promotion_class');  // ����
$service_list = array();
foreach ($goods_list as $goods) {
    $goods_id = $goods['goods_id'];
    $goods_type_id = $goods['type_id'];
    $prices_list = unserialize($goods['prices_list']);
    $price_str = sprintf('%.2f', $goods['prices']);
    $pro_prices_list = $price_str;
    $unit = '';
    if (!empty($prices_list)) {
        $tmp = 0;
        $pro_prices_list = array();
        foreach ($prices_list as $k => $price) {
            if ($price <= 0) {
                continue;
            }
            $pro_prices_list[] = array(
                'prices_type_id' => $k, //����
                'goods_prices' => $price, //����
            );
            if ($tmp > 0 && $price > $tmp) {
                continue;
            }
            $tmp = $price;
        }
        if ($tmp > 0) {
            $price_str = sprintf('%.2f', $tmp);
            $unit = ' ��';
        }
    }
    $is_show = $goods['is_show'];  // ���¼�
    $statis_result = $task_goods_obj->get_goods_statistical($goods_id);
    if ($location_id == 'test2') { //for debug
        $options['data'] = array(
            '$goods_id' => $goods_id,
            '$statis_result' => $statis_result,
        );
        $cp->output($options);
        exit;
    }
    $link = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&goods_id=' . $goods_id . '&pid=1220102&type=inner_app'; // ��ת��������
    if ($goods_type_id == 42) {
        // �����
        $link = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_user_id . '&goods_id=' . $goods_id . '&pid=1220152&type=inner_app';
    }
    $service_info = array(
        'goods_id' => $goods_id, // ��ƷID
        'titles' => preg_replace('/&#\d+;/', '', $goods['titles']), // ��������
        'type_id' => $goods_type_id, // �������
        'is_show' => $is_show, // ����״̬
        'images' => yueyue_resize_act_img_url($goods['images'], '640'), // ͼƬչʾ
        'prices' => '��' . $price_str . $unit,
        'rate' => '��' . $price_str,
        'unit' => $unit,
        'link' => $link,
        'buy_num' => '����' . $statis_result['bill_pay_num'] . '�˹���',
//        'abate' => '��ʡ��100',
//        'notice' => '��ʱ�ͼ�',
//        'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
    );
    if ($goods_type_id == 42) {
        // �Ƿ����
        $is_finish = interface_activity_is_finish($goods['e_time']);
        $service_info['is_finish'] = $is_finish ? 1 : 0;
    }
    if (!empty($pro_prices_list) && version_compare($version, '3.2', '>')) {
        $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '��ʡ: ' . $promotion['abate'];
            // ��� ������Ϣ
            $service_info = array_merge($service_info, $promotion);
        }
    }
    $service_list[] = $service_info;
}
// ����
$share = array();
if (version_compare($version, '3.2', '>')) {
    $share_img = $service_list[0]['images'];
    $title = '�����б�';
    $share = $task_goods_obj->get_goods_list_share_text($seller_user_id, $type_id, $title, $share_img);
}

$options['data'] = array(
    'total' => intval($goods_total),
    'list' => $service_list,
    'share' => $share,
); //
return $cp->output($options);
