<?php

/**
 * ģ���̼ұ༭(����)�ύ�ӿ�
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-8-25
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$profile_id = $client_data['data']['param']['profile_id'];
$introduce = $client_data['data']['param']['introduce'];  // �ύ������
$version = $client_data['data']['version']; // �汾
if (version_compare($version, '1.2', '>')) {
    $options['data'] = array(
        'version' => $version,
        'msg' => '�ýӿ���ֹͣʹ��'
    );
    return $cp->output($options);
}
if (empty($user_id) || empty($profile_id)) {
    $result = array('result' => 0, 'message' => 'û�и��û�');
    $options['data'] = $result;
    return $cp->output($options);
}
$name = $post_data['name'];
if (empty($introduce) || !is_array($introduce)) {
    $result = array('result' => -2, 'message' => '���ܲ���Ϊ��');
    $options['data'] = $result;
    return $cp->output($options);
}
$introduce_str = '';
foreach ($introduce as $value) {
    $type = strval($value['type']);
    $val = trim($value['content']);
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
    $result = array('result' => -2, 'message' => '���ݸ�ʽ����');
    $options['data'] = $result;
    return $cp->output($options);
}
$data = array(
    'introduce' => $introduce_str,
);
$seller_obj = POCO::singleton('pai_mall_seller_class');
$res = $seller_obj->user_update_seller_profile($profile_id, $data, $user_id);
if ($location_id == 'test') {
    $options['data'] = array(
        'profile_id' => $profile_id,
        'data' => $data,
        'user_id' => $user_id,
        'res' => $res
    );
    return $cp->output($options);
}
$options['data'] = $res;
return $cp->output($options);
