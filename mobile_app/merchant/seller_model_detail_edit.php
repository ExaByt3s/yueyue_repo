<?php

/**
 * 模特商家编辑(详情)提交接口
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-8-25
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$profile_id = $client_data['data']['param']['profile_id'];
$introduce = $client_data['data']['param']['introduce'];  // 提交的数据
$version = $client_data['data']['version']; // 版本
if (version_compare($version, '1.2', '>')) {
    $options['data'] = array(
        'version' => $version,
        'msg' => '该接口已停止使用'
    );
    return $cp->output($options);
}
if (empty($user_id) || empty($profile_id)) {
    $result = array('result' => 0, 'message' => '没有该用户');
    $options['data'] = $result;
    return $cp->output($options);
}
$name = $post_data['name'];
if (empty($introduce) || !is_array($introduce)) {
    $result = array('result' => -2, 'message' => '介绍不能为空');
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
    $result = array('result' => -2, 'message' => '数据格式错误');
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
