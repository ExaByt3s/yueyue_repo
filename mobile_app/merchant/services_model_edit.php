<?php

/**
 * ������Ʒ�༭(�ύ)�ӿ�
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-8-25
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = $client_data['data']['param']['goods_id'];   // ��ƷID
$post_data = $client_data['data']['param']['post_json_data'];  // �ύ������
if (empty($user_id) || empty($post_data)) {
    $result = array('result' => 0, 'message' => 'û�и��û�');
    $options['data'] = $result;
    return $cp->output($options);
}
$type_id = 31; // ģ��
// ��������
$introduce_str = '';
$content = $post_data['content'];
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
if (empty($post_data['titles'])) {
    $result = array('result' => -1, 'message' => '����Ϊ��');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($introduce_str)) {
    $result = array('result' => -1, 'message' => 'ͼ������Ϊ��');
    $options['data'] = $result;
    return $cp->output($options);
}
// ��������
$default_data = array(
    'titles' => $post_data['titles'],
    'content' => $introduce_str, // ����
);
// �ֲ�ͼƬ
$img = array();
foreach ($post_data['img_list'] as $value) {
    $img_val = $value['img'];
    if (empty($img_val) || !filter_var($img_val, FILTER_VALIDATE_URL)) {
        continue;
    }
    $img_val = yueyue_resize_act_img_url($img_val);  // תΪԭͼ����
    $img[] = array('img_url' => $img_val);
}
// �۸�
$special_ids = array();
foreach ($post_data['prices_list'] as $value) {
    $id = $value['id'];
    if (!is_numeric($id)) {
        // ������ , ����: �Ա��� ���ļ���key
        $special_ids[$id] = $value;
        continue;
    }
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
// ��������
$style = $post_data['style'];
if ($style == '72b32a1f754ba1c09b3695e0cb6cde7f') {
    // �Ա� û����������, �� ���ļ���
    $system_data = array(
        'd9d4f495e875a2e075a1a4a6e1b9770f' => $style, // ���
        '16a5cdae362b8d27a1d8f8c7b78b4330' => intval($special_ids['16a5cdae362b8d27a1d8f8c7b78b4330']), // ���ļ���
    );
} else {
    $system_data = array(
        'd9d4f495e875a2e075a1a4a6e1b9770f' => $style, // ���
        '66f041e16a60928b05a7e228a89c3799' => intval($post_data['limit_num']), // ��������
    );
}
// ��ȡ ����ID
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info = $mall_obj->get_seller_info($user_id, 2);
$store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
// ��װ����
$op_data = array(
    'store_id' => $store_id,
    'type_id' => $type_id,
    'default_data' => $default_data,
    'img' => $img,
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
    $res['goods_id'] = $res['result'];
}
$options['data'] = $res;
return $cp->output($options);
