<?php

/**
 * 编辑 用户信息 ( 初始化 )
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-7-16
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID

$seller_obj = POCO::singleton('pai_user_class');
$user_result = $seller_obj->get_user_info($user_id);  // 获取用户信息
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
$user_info = array(
    'user_id' => $user_result['user_id'], // 用户名
    'avatar' => get_user_icon($user_id, 165, TRUE), // 头像
    'nickname' => $user_result['nickname'], // 用户名
    'location_id' => $user_result['location_id'],
    'intro' => $user_result['remark'], // 简介
    'is_display_record' => $user_result['is_display_record'], // 是否显示记录
);
// 获取 图集
$pic_obj = POCO::singleton('pai_pic_class');
$pic_list = $pic_obj->get_user_pic($user_id);
//$pic_list = $pic_obj->get_user_pic($user_id);
foreach ($pic_list as $value) {
    $img_url = $value['img'];
    $showcase = array(
        'thumb' => yueyue_resize_act_img_url($img_url, '440'), // 缩略图
        'original' => yueyue_resize_act_img_url($img_url), // 原图
    );
    $user_info['showcase'][] = $showcase;
}
// 上传配置
$upload_config = array(
    'post_icon' => 'http://sendmedia-w.yueus.com:8078/icon.cgi',
    'post_icon_wifi' => 'http://sendmedia-w-wifi.yueus.com:8078/icon.cgi',
    'icon_size' => '640',
    'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_pic_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
    'pic_size' => '640',
    'pic_num' => '15',
);


$options['data'] = array_merge($user_info, $upload_config);
return $cp->output($options);
