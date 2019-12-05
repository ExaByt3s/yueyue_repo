<?php

/**
 * 模特商家编辑(提交)接口
 * 
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-8-25
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$post_data = $client_data['data']['param']['post_json_data'];  // 提交的数据
$profile_id = $post_data['profile_id'];
if (empty($user_id) || empty($profile_id)) {
    $result = array('result' => 0, 'message' => '没有该用户');
    $options['data'] = $result;
    return $cp->output($options);
}
$name = $post_data['name'];
if (empty($name)) {
    $result = array('result' => -2, 'message' => '昵称不合法');
    $options['data'] = $result;
    return $cp->output($options);
}
$name_len = mb_strlen($name,'utf-8');  // 字符串长度
if($name_len > 50){
    $name = mb_substr($name,0,50,'utf-8');  // 截取前50个字符
}
if(empty($post_data['cover'])){
   $result = array('result' => -1, 'message' => '背景图为空');
    $options['data'] = $result;
    return $cp->output($options); 
}
$data = array(
    'name' => $name,
    'location_id' => $post_data['location_id'],
    'cover' => $post_data['cover'],
    'att' => array(
        array('key' => 'm_height', 'data' => $post_data['height'],),
        array('key' => 'm_weight', 'data' => $post_data['weight'],),
        array('key' => 'm_cups', 'data' => $post_data['cup_size'],),
        array('key' => 'm_cup', 'data' => $post_data['cup'],),
        array('key' => 'm_bwh', 'data' => $post_data['chest'] . '-' . $post_data['waist'] . '-' . $post_data['hip'],),
        array('key' => 'm_level', 'data' => $post_data['level_require'],),
        array('key' => 'm_experience', 'data' => $post_data['experience'],),
    ),
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
