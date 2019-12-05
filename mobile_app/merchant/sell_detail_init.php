<?php

/**
 * 模特商家编辑(初始化)接口
 *
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-8-25
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];  // 商品ID
$request_type = $client_data['data']['param']['request_type'];  // 请求类型
$version = $client_data['data']['version']; // 版本

$upload_config = array();
switch ($request_type) {
    case 'seller':  // 商家
        if (version_compare($version, '1.2', '>')) {
            $options['data'] = array(
                'version' => $version,
                'message' => '该接口已停止使用'
            );
            return $cp->output($options);
        }
        // 获取详情
        $seller_obj = POCO::singleton('pai_mall_seller_class');
        $user_result = $seller_obj->get_seller_info($user_id, 2);  // 获取用户信息
        $contents = trim($user_result['seller_data']['profile'][0]['introduce']);
        $profile_id = $user_result['seller_data']['profile'][0]['seller_profile_id'];
        // 上传配置
        $upload_config = array(
            'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
            'post_pic_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
            'pic_size' => '640',
//            'pic_num' => '15',
        );
        $detail = array('profile_id' => $profile_id);
        break;
    case 'goods':  // 商品
        if (empty($goods_id)) {
            $options['data'] = array(
                'result' => 0,
                'message' => '商品ID为空'
            );
            return $cp->output($options);
        }
        $task_goods_obj = POCO::singleton('pai_mall_goods_class');
        $goods_result = $task_goods_obj->user_get_goods_info($goods_id, $user_id);
        $goods = $goods_result['data'];
        $contents = trim($goods['default_data']['content']['value']);
        $contents = content_replace_pics($contents, 640);  // 替换图片大小
        break;
    default:
        $contents = array();
        break;
}

$detail['contents'] = interface_content_to_ubb($contents);

$options['data'] = array_merge($detail, $upload_config);
return $cp->output($options);
