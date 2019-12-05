<?php

/**
 * 编辑 用户信息 ( 提交 )
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-7-16
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];  // 用户ID
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$post_data = $client_data['data']['param']['post_json_data'];
if (empty($user_id) || empty($post_data)) {
    $result = array('result' => -1, 'message' => '没有更新内容');
    $options['data'] = $result;
    $cp->output($options);
    exit;
}
$nickname = $post_data['nickname'];  // 用户名
$location = $post_data['location_id'];
$intro = $post_data['intro'];  // 简介
$is_display_record = $post_data['is_display_record'];  // 是否显示记录

if ($nickname) {
    $update_data['nickname'] = $nickname;
}
if ($location) {
    $update_data['location_id'] = $location;
}
if ($intro) {
    $update_data['remark'] = $intro;
}
$update_data['is_display_record'] = intval($is_display_record); // 是否显示记录
if (!empty($post_data['showcase'])) {  // 图集
    $update_data['pic_arr'] = $post_data['showcase'];
}
if ($location_id == 'test') {
    $options['data'] = array(
        'post_json_data' => $post_data,
        'update_data' => $update_data,
    );
    return $cp->output($options);
}
$obj = POCO::singleton('pai_user_class');
$res = $obj->update_mall_user_info($update_data, $user_id);
$result = array('result' => 0, 'message' => '更新失败');
if ($res) {
    $result = array('result' => 1, 'message' => '更新成功', 'res' => $res);
}
$options['data'] = $result;
return $cp->output($options);
