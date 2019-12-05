<?php

/**
 * ��ʳ����(�ύ)�ӿ�
 *
 * @author willike
 * @since 2015/10/15
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$options['data'] = array('result' => 0, 'message' => '�ݲ�֧�ֱ༭');
return $cp->output($options);

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = intval($client_data['data']['param']['goods_id']);   // ��ƷID
$common = $client_data['data']['param']['common'];
$standerd = $client_data['data']['param']['standerd'];  // �۸�
$cover = $client_data['data']['param']['cover'];  // ����ͼ
$content = $client_data['data']['param']['content'];  // ͼ������
$guide = $client_data['data']['param']['guide'];  // ����ͼƬ
if (empty($user_id) || empty($common)) {
    $result = array('result' => 0, 'message' => '����δ����!');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($content)) {
    $result = array('result' => -1, 'message' => 'ͼ������Ϊ��');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($standerd)) {
    $result = array('result' => -1, 'message' => '���Ϊ��');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($guide)) {
    $result = array('result' => -3, 'message' => '����ͼΪ��');
    $options['data'] = $result;
    return $cp->output($options);
}
$imgs = array();
foreach ($cover as $img_val) {
    if (empty($img_val)) {
        continue;
    }
    $imgs[] = array('img_url' => $img_val);
}
if (empty($imgs)) {
    $result = array('result' => -2, 'message' => '����ͼΪ��');
    $options['data'] = $result;
    return $cp->output($options);
}
// ��������
$introduce_str = '';
foreach ($content as $value) {
    $type = strval($value['type']);
    $val = trim($value['content']);
    if (empty($val)) {
        continue;
    }
    switch ($type) {
        case '1' :
        case 'text':
            $text = '<p>' . str_replace(array('\r\n', '\n', '\n\r', "\r\n", "\n", "\n\r"), '<br>', $val) . '</p>';
            break;
        case '2':
        case 'image':
            $val = yueyue_resize_act_img_url($val);
            $text = '<img src="' . $val . '"/>';
            break;
        default:
            continue;
    }
    $introduce_str .= $text;
}
if (empty($introduce_str)) {
    $result = array('result' => -1, 'message' => 'ͼ������Ϊ��');
    $options['data'] = $result;
    return $cp->output($options);
}
// ����(����)����
$is_fill = true;
foreach ($common as $val) {
    $key = $val['id'];
    $value = trim($val['value']);
    $option_value = $val['option_value'];
    $sys_val = empty($option_value) ? $value : $option_value;
    if (empty($sys_val)) {
        $is_fill = false;
        break;
    }
    $system_data[$key] = $sys_val;
}
if ($is_fill === false) {
    $result = array('result' => 0, 'message' => '���ݲ�����');
    $options['data'] = $result;
    return $cp->output($options);
}
// ����ͼ
$system_data['e56954b4f6347e897f954495eab16a88'] = $guide;
// ���� default_data
$default_key = array('titles', 'location_id');
foreach ($default_key as $val) {
    $default_data[$val] = $system_data[$val];
    unset($system_data[$val]);
}
// ��������
$default_data['content'] = $introduce_str; // ����
// �۸�
$temp_i = 0;
foreach ($standerd as $value) {
    $key = $value['id'];
    if (empty($key)) {
        // û�� id ʱ,��ʾ����, ����һ��ID
        $key = time() . $temp_i;  // (9+1=10 λ��)
        $temp_i++;
    }
    $options = $value['options'];
    $o_val = array();
    foreach ($options as $v) {
        $o_val[$v['id']] = $v['value'];
    }
    $share_num = intval($o_val['share_num']);
    $prices = $o_val['price'];
    if (empty($prices) || !is_numeric($prices) || $prices <= 0 || $share_num < 1) {
        // �۸�Ϊ��, �۸������, �Ͳ�����С��1
        continue;
    }
    $stock_num = intval($o_val['stock_num']);
    $prices_diy[$key] = array(
        'name_v1' => $o_val['name'],
        'name_v2' => $share_num,
        'name' => $o_val['name'] . '��' . $share_num . '�ˣ�',
        'prices' => $prices,
        'stock_num' => $stock_num,
    );
}
if (empty($prices_diy)) {
    $result = array('result' => -2, 'message' => '�ײ�δ��д����');
    $options['data'] = $result;
    return $cp->output($options);
}
// ��ȡ ����ID
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $mall_obj->get_seller_info($user_id, 2);
$store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
$type_id = 41; // ��ʳ
// ��װ����
$op_data = array(
    'store_id' => $store_id,
    'type_id' => $type_id,
    'default_data' => $default_data,
    'img' => $imgs,
    'system_data' => $system_data,
    'prices_diy' => $prices_diy,
);

if ($location_id == 'test') {
    $options['data'] = array(
        '$op_data' => $op_data,
        'param' => $client_data['data']['param'],
    );
    return $cp->output($options);
}
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
if (empty($goods_id)) {
    //��������
    $res = $pai_mall_goods_obj->user_add_goods($op_data, $user_id);
} else {
    //��������
    $res = $pai_mall_goods_obj->user_update_goods($goods_id, $op_data, $user_id);
}
if (empty($res)) {
    $result = array('result' => 0, 'message' => '���ݱ���ʧ��', 'op_data' => $op_data);
    $options['data'] = $result;
    return $cp->output($options);
}
if ($res['result'] > 0) {
    $res['result'] = 1;
    $res['return_id'] = $res['result'];
}
$options['data'] = $res;
return $cp->output($options);
