<?php
/**
 * Ӱ�������ύ
 *
 * @author willike<chenwb@yueus.com>
 * @since 2015-9-29
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = intval($client_data['data']['param']['goods_id']);   // ��ƷID
$common = $client_data['data']['param']['common'];
$standerd = $client_data['data']['param']['standerd'];  // �۸�
$cover = $client_data['data']['param']['cover'];  // ����ͼ
$content = $client_data['data']['param']['content'];  // ͼ������
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
if(empty($standerd)){
    $result = array('result' => -1, 'message' => '���Ϊ��');
    $options['data'] = $result;
    return $cp->output($options);
}
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
// ��������
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
// ���� default_data
$default_key = array('titles','location_id');
foreach ($default_key as $val) {
    $default_data[$val] = $system_data[$val];
    unset($system_data[$val]);
}
// ��������
$default_data['content'] = $introduce_str; // ����
// �۸�
foreach ($standerd as $value) {
    $id = $value['id'];
    $value = floatval($value['value']); // �۸�
    if ($value == '' || $value <= 0 || $value == '0.0') {
        continue;
    }
    $prices_de[$id] = sprintf('%.2f', $value);
}
if (empty($prices_de)) {
    $result = array('result' => -2, 'message' => 'û����д�۸�');
    $options['data'] = $result;
    return $cp->output($options);
}
// ��ȡ ����ID
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $mall_obj->get_seller_info($user_id, 2);
$store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
$type_id = 12; // Ӱ������
// ��װ����
$op_data = array(
    'store_id' => $store_id,
    'type_id' => $type_id,
    'default_data' => $default_data,
    'img' => $imgs,
    'system_data' => $system_data,
    'prices_de' => $prices_de,
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