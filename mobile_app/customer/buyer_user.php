<?php

/**
 * ��� �û���ҳ
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-7-16
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$buyer_id = $client_data['data']['param']['buyer_id'];  // �����û� ID
$is_mine = (empty($buyer_id) || $user_id == $buyer_id) ? TRUE : FALSE;
$buyer_id = empty($buyer_id) ? $user_id : $buyer_id;

$seller_obj = POCO::singleton('pai_user_class');
$user_result = $seller_obj->get_user_info($buyer_id);  // ��ȡ�û���Ϣ
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
$introduce = interface_content_strip($user_result['remark'], 300);  // ���˽���
$is_display_record = intval($user_result['is_display_record']);  // �Ƿ���ʾ ���Ѽ�¼
// ��ȡ ���״��������ѽ��
$obj = POCO::singleton('pai_user_data_class');
$record_result = $obj->get_user_data_info($buyer_id);
if ($location_id == 'test1') {
    $options['data'] = $record_result;
    return $cp->output($options);
}
$score = $record_result['comment_score'];   // �û�����
$score = empty($score) ? 5 : $score;  // Ĭ��5��
$deal_times = $record_result['deal_times'];   // ���״���
$consume_ammount = $record_result['consume_ammount'];   // ���׽��
$name = $user_result['nickname'];  // �ǳ�
$user_info = array(
    'user_id' => $user_result['user_id'], // �û�ID
    'name' => $name,
    'avatar' => get_user_icon($user_result['user_id'], 165, $is_mine), // ͷ��
    'location' => get_poco_location_name_by_location_id($user_result['location_id']),
    'introduce' => trim($introduce),
    // ����ҳ��
    'chat' => array(
        'title' => '˽��TA',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&receiver_id=' . $user_result['user_id'] .
            '&receiver_name=' . urlencode(mb_convert_encoding($name, 'utf8', 'gbk')) . '&pid=1220021&type=inner_app',
    ),
    // ����
    'property' => array(
//        array('title' => '��Ա�ȼ�', 'value' => '13'),   // ��Ա�ȼ���ʱ���� 2015-7-22 @����
        array('title' => '���״���', 'value' => strval($deal_times)),
        array('title' => '���ѽ��', 'value' => strval($consume_ammount)),
    ),
    // ���Ѽ�¼
    'record_title' => '���Ѽ�¼',
    'is_display_record' => strval($is_display_record),
    'record' => array(),
    // ������Ϣ
    'business' => array(
        'title' => '����',
        'score' => strval($score > 5 ? 5 : ($score < 0 ? 0 : $score)),
        'merit' => array(
            'title' => '�ۺ�����',
            'request' => 'yueyue://goto?user_id=' . $user_id . '&buyer_id=' . $user_result['user_id'] . '&pid=1220075&type=inner_app',
        ),
    ),
    'showtitle' => '����ͼ��',
    // ��Ʒչʾ
    'showcase' => array(),
    // ����
//    'share' => array(),
);

// ��ȡ ͼ��
$pic_obj = POCO::singleton('pai_pic_class');
$pic_list = $pic_obj->get_user_pic($buyer_id);
if ($location_id == 'test2') {
    $options['data'] = $pic_list;
    return $cp->output($options);
}
foreach ($pic_list as $value) {
    $img_url = $value['img'];
    $showcase = array(
        'thumb' => yueyue_resize_act_img_url($img_url, '440'), // ����ͼ
        'original' => yueyue_resize_act_img_url($img_url), // ԭͼ
    );
    $user_info['showcase'][] = $showcase;
}
if ($is_display_record === 1) {
    // ��ȡ ���Ѽ�¼
    $mall_order_obj = POCO::singleton('pai_mall_order_class');   // ʵ�����̼ҽ��׶���
    $trade_data = $mall_order_obj->get_order_list_for_buyer($buyer_id, 0, 8, false, 'order_id DESC', '0,6', '*');
    if ($location_id == 'test3') {
        $options['data'] = $trade_data;
        return $cp->output($options);
    }
    $trade_list = array();
    foreach ($trade_data as $value) {
        $type_id = $value['type_id'];  // �����ID
        $order_sn = $value['order_sn'];
        if ($type_id == 20) {  // �渶
            $goods_id = '';
            $cover = yueyue_resize_act_img_url($value['seller_icon'], 64);
            // ��������
            $detail_url = 'http://yp.yueus.com/mall/user/test/order/detail.php?order_sn=' . $order_sn;
            $link = 'yueyue://goto?type=inner_web&url=' . urlencode($detail_url) . '&wifi_url=' . urlencode($detail_url) . '&showtitle=1';
        } else if ($type_id == 42) {  // �
            $goods_id = $value['activity_list'][0]['activity_id'];
            $cover = yueyue_resize_act_img_url($value['activity_list'][0]['activity_images'], 145);
            $pid = 1220152;  // �����
            $link = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=' . $pid . '&type=inner_app'; // ��ת�ҳ��
        } else {
            $goods_id = $value['detail_list'][0]['goods_id'];
            $cover = yueyue_resize_act_img_url($value['detail_list'][0]['goods_images'], 260);
            $pid = 1220102;
            $link = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=' . $pid . '&type=inner_app'; // ��ת��Ʒҳ��
        }
        $trade_info = array(
            'order_sn' => $order_sn, // ������
            'goods_id' => $goods_id, // ��ƷID
            'thumb' => $cover, // Ԥ��ͼ
            'link' => $link,
        );
        $user_info['record'][] = $trade_info;
    }
}

if ($is_mine === TRUE) {
    // �༭�û�
    $user_info['edit'] = array(
        'title' => '�༭',
        'request' => 'yueyue://goto?user_id=' . $user_id . '&pid=1220127&type=inner_app',
    );
    // ��ȡ�û�������Ϣ
    $user_info['share'] = $seller_obj->get_share_text($user_id);
}

$options['data'] = $user_info;
return $cp->output($options);

