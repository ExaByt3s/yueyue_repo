<?php

/**
 * �ղ� �̼�/��Ʒ �б�
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-8-26
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$target_type = $client_data['data']['param']['target_type'];  // Ŀ������ ( seller/goods )
$type_id = intval($client_data['data']['param']['type_id']);  // ����ID
$order_by = trim($client_data['data']['param']['sort_by']);  // ���� add_timeΪ������ʱ�䣬user_last_update_timeΪ���û�����ʱ��
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

$follow_obj = POCO::singleton('pai_mall_follow_user_class');
$list = array();
$update_orderby_ = '';
switch ($target_type) {
    case 'trade':  // ����
    case 'seller':
        // �̼��ղ�
        $classify_name_arr = array(
            31 => 'ģ��',
            5 => '��ѵ',
            3 => '��ױ',
            12 => '��ҵ����',
            40 => '��Ӱ',
            41 => '��ʳ',
            43 => '����',
            99 => '�',
        );
        $follow_type = ($target_type == 'trade') ? 'deal' : 'collect';
        $list_res = $follow_obj->get_follow_by_user_id($follow_type, FALSE, $user_id, $type_id, $order_by, $limit_str);
        foreach ($list_res as $val) {
            $type_id_arr = explode(',', $val['type_id_str']);  // ��֤�����б�
            $type_id_str = '';
            foreach ($type_id_arr as $type_id) {
                $type_id_str .= $classify_name_arr[$type_id] . '��';
            }
            $score = $val['comment_score'];
            // ��ת�û�����
            $link = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $val['user_id'] . '&pid=1220103&type=inner_app';
            $list[] = array(
                'seller_id' => $val['user_id'],
                'name' => $val['nickname'],
                'cover' => $val['user_icon'], // yueyue_resize_act_img_url($val['cover'], 640),
                'services' => '��֤����' . trim($type_id_str, '��'),
                'location' => $val['city_name'],
                'goods_num' => '��������: ' . $val['onsale_num'] . '��',
                'score' => $score <= 0 ? '5' : strval($score),
                'is_close' => strval($val['is_close']),  // 1 �ر�, 0δ�ر�
                'link' => $val['is_close'] == 1 ? '' : $link,
            );
        }
        $update_orderby_ = 'user_last_update_time';
        break;
    case 'goods':
        $list_res = $follow_obj->get_follow_goods_by_user_id(FALSE, $user_id, $type_id, $order_by, $limit_str);
        foreach ($list_res as $val) {
            $goods_status = intval($val['status']);  // ����/�״̬
            $is_show = $val['is_show'];
            $goods_type_id = $val['type_id'];
            $url = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $val['goods_id'] . '&pid=1220102&type=inner_app';
            if ($goods_type_id == 42) {
                $url = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $val['goods_id'] . '&pid=1220152&type=inner_app';
            }
            $url = $is_show == 2 ? '' : $url;
            $price_str = sprintf('%.2f', $val['prices']); // �۸�
            $format = $val['format'];  // ���
            $prices_unit = $val['prices_unit'];
            $unit = (empty($format) ? '' : '/' . $format) . (empty($prices_unit) ? '' : $prices_unit); // ��λ
            $is_finish = $goods_type_id == 42 ? interface_activity_is_finish($val['e_time']) : false;
            $list[] = array(
                'goods_id' => $val['goods_id'],
                'type_id' => $goods_type_id,
                'titles' => $val['goods_name'],
                'images' => yueyue_resize_act_img_url($val['image'], 640),
                'prices' => '��' . $price_str . $unit,
                'rate' => '��' . $price_str,
                'unit' => $unit,
                'is_show' => strval($val['is_show']),
                'score' => intval($val['average_score']), // ����
                'is_finish' => $is_finish == true ? 1 : 0,  // �Ƿ����(�)
                'status' => $goods_status,
                'link' => $url,
            );
        }
        $update_orderby_ = 'goods_last_update_time';
        break;
    default:
        break;
}
if ($location_id == 'test') {
    $options['data'] = array(
        '$limit_str' => $limit_str,
        '$type_id' => $type_id,
        '$list_res' => $list_res,
        '$list' => $list,
    );
    return $cp->output($options);
}
// ����
$classify = array(
    array('title' => '����Ʒ��', 'value' => '0'),
    array('title' => 'Լģ��', 'value' => '31'),
    array('title' => 'Լ��Ӱ', 'value' => '40'),
    array('title' => 'Լ��ʳ', 'value' => '41'),
    array('title' => 'Լ��ѵ', 'value' => '5'),
    array('title' => 'Լ��ױ', 'value' => '3'),
    array('title' => 'Լ�', 'value' => '42'),
    array('title' => 'Լ��Ȥ', 'value' => '43'),
    array('title' => '��ҵ����', 'value' => '12'),
);
$options['data'] = array(
    'list' => $list,
    'search' => array(
        'type_id' => $classify,
        'sort_by' => array(
            // Ϊ������ʱ��
            array('title' => 'Ĭ������', 'value' => 'add_time'),
            array('title' => '���չ�עʱ��', 'value' => 'add_time'),
            array('title' => '�����������', 'value' => $update_orderby_),  // ���û�����ʱ��
        ),
    ),
);
return $cp->output($options);
