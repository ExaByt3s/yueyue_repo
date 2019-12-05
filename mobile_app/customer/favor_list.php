<?php

/**
 * 收藏 商家/商品 列表
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-8-26
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$target_type = $client_data['data']['param']['target_type'];  // 目标类型 ( seller/goods )
$type_id = intval($client_data['data']['param']['type_id']);  // 类型ID
$order_by = trim($client_data['data']['param']['sort_by']);  // 排序 add_time为按更新时间，user_last_update_time为按用户更新时间
$limit = trim($client_data['data']['param']['limit']);  // 传值如: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // 第几页
    $rows = intval($client_data['data']['param']['rows']); // 每页限制条数(5-100之间)

    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);
    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $lstart . ',' . $lcount;
}

$follow_obj = POCO::singleton('pai_mall_follow_user_class');
$list = array();
$update_orderby_ = '';
switch ($target_type) {
    case 'trade':  // 交易
    case 'seller':
        // 商家收藏
        $classify_name_arr = array(
            31 => '模特',
            5 => '培训',
            3 => '化妆',
            12 => '商业定制',
            40 => '摄影',
            41 => '美食',
            43 => '达人',
            99 => '活动',
        );
        $follow_type = ($target_type == 'trade') ? 'deal' : 'collect';
        $list_res = $follow_obj->get_follow_by_user_id($follow_type, FALSE, $user_id, $type_id, $order_by, $limit_str);
        foreach ($list_res as $val) {
            $type_id_arr = explode(',', $val['type_id_str']);  // 认证服务列表
            $type_id_str = '';
            foreach ($type_id_arr as $type_id) {
                $type_id_str .= $classify_name_arr[$type_id] . '、';
            }
            $score = $val['comment_score'];
            // 跳转用户中心
            $link = 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $val['user_id'] . '&pid=1220103&type=inner_app';
            $list[] = array(
                'seller_id' => $val['user_id'],
                'name' => $val['nickname'],
                'cover' => $val['user_icon'], // yueyue_resize_act_img_url($val['cover'], 640),
                'services' => '认证服务：' . trim($type_id_str, '、'),
                'location' => $val['city_name'],
                'goods_num' => '服务数量: ' . $val['onsale_num'] . '个',
                'score' => $score <= 0 ? '5' : strval($score),
                'is_close' => strval($val['is_close']),  // 1 关闭, 0未关闭
                'link' => $val['is_close'] == 1 ? '' : $link,
            );
        }
        $update_orderby_ = 'user_last_update_time';
        break;
    case 'goods':
        $list_res = $follow_obj->get_follow_goods_by_user_id(FALSE, $user_id, $type_id, $order_by, $limit_str);
        foreach ($list_res as $val) {
            $goods_status = intval($val['status']);  // 服务/活动状态
            $is_show = $val['is_show'];
            $goods_type_id = $val['type_id'];
            $url = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $val['goods_id'] . '&pid=1220102&type=inner_app';
            if ($goods_type_id == 42) {
                $url = 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $val['goods_id'] . '&pid=1220152&type=inner_app';
            }
            $url = $is_show == 2 ? '' : $url;
            $price_str = sprintf('%.2f', $val['prices']); // 价格
            $format = $val['format'];  // 规格
            $prices_unit = $val['prices_unit'];
            $unit = (empty($format) ? '' : '/' . $format) . (empty($prices_unit) ? '' : $prices_unit); // 单位
            $is_finish = $goods_type_id == 42 ? interface_activity_is_finish($val['e_time']) : false;
            $list[] = array(
                'goods_id' => $val['goods_id'],
                'type_id' => $goods_type_id,
                'titles' => $val['goods_name'],
                'images' => yueyue_resize_act_img_url($val['image'], 640),
                'prices' => '￥' . $price_str . $unit,
                'rate' => '￥' . $price_str,
                'unit' => $unit,
                'is_show' => strval($val['is_show']),
                'score' => intval($val['average_score']), // 评分
                'is_finish' => $is_finish == true ? 1 : 0,  // 是否结束(活动)
                'status' => $goods_status,
                'link' => $url,
            );
        }
        $update_orderby_ = 'goods_last_update_time';
        break;
    default:
        break;
}
if ($location_id == 'test') {
    $options['data'] = array(
        '$limit_str' => $limit_str,
        '$type_id' => $type_id,
        '$list_res' => $list_res,
        '$list' => $list,
    );
    return $cp->output($options);
}
// 分类
$classify = array(
    array('title' => '所有品类', 'value' => '0'),
    array('title' => '约模特', 'value' => '31'),
    array('title' => '约摄影', 'value' => '40'),
    array('title' => '约美食', 'value' => '41'),
    array('title' => '约培训', 'value' => '5'),
    array('title' => '约化妆', 'value' => '3'),
    array('title' => '约活动', 'value' => '42'),
    array('title' => '约有趣', 'value' => '43'),
    array('title' => '商业定制', 'value' => '12'),
);
$options['data'] = array(
    'list' => $list,
    'search' => array(
        'type_id' => $classify,
        'sort_by' => array(
            // 为按更新时间
            array('title' => '默认排序', 'value' => 'add_time'),
            array('title' => '按照关注时间', 'value' => 'add_time'),
            array('title' => '按照最近更新', 'value' => $update_orderby_),  // 按用户更新时间
        ),
    ),
);
return $cp->output($options);
