<?php
/**
 * 商家编辑提交
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015/10/16
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$profile_id = intval($client_data['data']['param']['profile_id']);   // 属性ID
$common = $client_data['data']['param']['common'];
$property = $client_data['data']['param']['property'];  // 属性
$cover = $client_data['data']['param']['cover'];  // 背景图
$avatar = $client_data['data']['param']['avatar'];  // 头像
$content = $client_data['data']['param']['content'];  // 图文详情
if (empty($user_id) || empty($profile_id) || empty($common)) {
    $result = array('result' => 0, 'message' => '数据未完整!');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($avatar) || empty($cover)) {
    $result = array('result' => -1, 'message' => '封面头像为空');
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($content)) {
    $result = array('result' => -2, 'message' => '图文详情为空');
    $options['data'] = $result;
    return $cp->output($options);
}
$avatar_img = $avatar; // $avatar[0];
if (empty($avatar_img) || !filter_var($avatar_img, FILTER_VALIDATE_URL)) {
    $result = array('result' => -1, 'message' => '头像为空');
    $options['data'] = $result;
    return $cp->output($options);
}
$cover_img = $cover[0];
if (empty($cover_img) || !filter_var($cover_img, FILTER_VALIDATE_URL)) {
    $result = array('result' => -1, 'message' => '背景图为空');
    $options['data'] = $result;
    return $cp->output($options);
}
// 处理详情
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
    $result = array('result' => -2, 'message' => '图文详情为空!');
    $options['data'] = $result;
    return $cp->output($options);
}
$user_data = array(
    'avatar' => $avatar_img,
    'cover' => $cover_img,
);
// 解析数据
foreach ($common as $val) {
    $key = $val['id'];
    $value = $val['value'];
    $option_value = $val['option_value'];
    $user_data[$key] = empty($option_value) ? $value : $option_value;
}
$user_data['introduce'] = $introduce_str; // 详情
// 处理 认证属性
$att = array();
foreach ($property as $pro) {
    $type_id = $pro['type_id'];
    $item = $pro['item'];
    if ($type_id == 31) {
        // 模特, 特殊处理
        $att['m_height'] = $item['bwh']['m_height'];
        $att['m_weight'] = $item['bwh']['m_weight'];
        $att['m_bwh'] = $item['bwh']['m_bwh_chest'] . '-' . $item['bwh']['m_bwh_waist'] . '-' . $item['bwh']['m_bwh_hip'];
        $att['m_cups'] = $item['bwh']['m_cups'];
        $att['m_cup'] = $item['bwh']['m_cup'];
    }
    foreach ($item['description'] as $des) {
        $d_id = $des['id'];
        $att[$d_id] = empty($des['option_value']) ? $des['value'] : $des['option_value'];
    }
}
//if (empty($att)) {
//    $result = array('result' => -3, 'message' => '商家属性为空!');
//    $options['data'] = $result;
//    return $cp->output($options);
//}
foreach ($att as $k => $v) {
    if (empty($v) || $v == 'null') {
        continue;
    }
    $user_data['att'][] = array(
        'key' => $k,
        'data' => $v,
    );
}
if ($location_id == 'test') {
    $result = array(
        '$profile_id' => $profile_id,
        '$user_data' => $user_data,
        '$user_id' => $user_id,
    );
    $options['data'] = $result;
    return $cp->output($options);
}
$seller_obj = POCO::singleton('pai_mall_seller_class');
$result = $seller_obj->user_update_seller_profile($profile_id, $user_data, $user_id);
$options['data'] = $result;
return $cp->output($options);
