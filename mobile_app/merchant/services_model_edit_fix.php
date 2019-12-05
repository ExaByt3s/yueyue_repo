<?php

/**
 * ������Ʒ�༭(�ύ)�ӿ�
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-10-15
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
    $result = array('result' => -1, 'message' => '��༭ͼ������');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($standerd)) {
    $result = array('result' => -1, 'message' => '���Ϊ��');
    $options['data'] = $result;
    return $cp->output($options);
}
foreach ($cover as $img_val) {
    if (empty($img_val) || !filter_var($img_val, FILTER_VALIDATE_URL)) {
        continue;
    }
    $imgs[] = array('img_url' => $img_val);
}
if (empty($imgs)) {
    $result = array('result' => -2, 'message' => '���ϴ�����ͼ');
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
    $result = array('result' => -1, 'message' => '��༭ͼ������');
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
$default_key = array('titles');
foreach ($default_key as $val) {
    $default_data[$val] = $system_data[$val];
    unset($system_data[$val]);
}
// ��������
$default_data['content'] = $introduce_str; // ����
// �����۸��ϵ��
$style_keymap = array(
    '67c6a1e7ce56d3d6fa748ab6d9af3fd7' => 1, // ŷ��
    '642e92efb79421734881b53e1e1b18b6' => 1, // ����
    'f457c545a9ded88f18ecee47145a72c0' => 1, // ��ˮ
    'c0c7c76d30bd3dcaefc96f40275bdc0a' => 1, // ��װ
    '2838023a778dfaecdc212708f721b788' => 1, // �պ�
    '9a1158154dfa42caddbd0694a4e9bdc8' => 1, // ���ո���
    'a684eceee76fc522773286a895bc8436' => 1, // ����/�Ȼ���
    'b53b3a3d6ab90ce0268229151c9bde11' => 1, // ����
    '1afa34a7f984eeabdbb0a7d494132ee5' => 1, // ���
    '9f61408e3afb633e50cdf1b20de6f466' => 2, // ����
    'd1f491a404d6854880943e5c3cd9ca25' => 2, // ��չ
    '9b8619251a19057cff70779273e95aa6' => 2, // ����
    '72b32a1f754ba1c09b3695e0cb6cde7f' => 3, // �Ա�
);
$style = $system_data['d9d4f495e875a2e075a1a4a6e1b9770f'];
$standerd_key = isset($style_keymap[$style]) ? $style_keymap[$style] : 1;  // �۸�����
// �۸�
$prices_de = array();
foreach ($standerd as $val) {
    $id = $val['id'];
    $value = floatval($val['value']); // �۸�
    if ($value == '' || $value <= 0 || $value == '0.0') {
        continue;
    }
    if($id == '16a5cdae362b8d27a1d8f8c7b78b4330'){
        // ���ļ���
        $system_data[$id] = $value;
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
$type_id = 31; // ģ��
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