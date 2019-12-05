<?php

/**
 * 商品 列表页
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
// on_sell 在售  off_sell 下架
$service_status = $client_data['data']['param']['service_status'];   // 服务状态
$type_id = $client_data['data']['param']['type_id'];   // 分类ID
$type_id = intval($type_id); // 某个品类/全部  0 表示全部
$keyword = $client_data['data']['param']['keyword'];   // 搜索关键词
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

$status = 1;  // (服务)通过
$is_show = 0;  // 全部
$action_type = 1;  // (活动)进行中
switch ($service_status) {
    case 'on_sell':
        $is_show = 1;  // 上架
        $action_type = 5;
        break;
    case 'off_sell':
        $is_show = 2; // 下架
        $action_type = 3;
        break;
    case 'send':
        $is_show = 1; // 上架 2015-9-2
        $action_type = 1;
        break;
    case 'under_review':  // 审核
        $is_show = 0;
        $status = 6;  // 未审核 + 未通过
        $action_type = 4;
        break;
    case 'under_way':  // 进行中
        $action_type = 1;
        break;
    case 'finish_off' :  // 已结束
        $action_type = 2;
        break;
    default:
        $status = 1;
        $is_show = 0;  // 全部
        $action_type = 1;
        break;
}
$task_goods_obj = POCO::singleton('pai_mall_goods_class');

if ($type_id == 42) {  // 活动
    // 活动: 1进行中 2已结束 3已下架 4审核中
    $data = array(
        'action_type' => $action_type,
    );
} else {
    // status状态 0未审核,1通过,2未通过,3删除; show上/下架 1上架,2下架;type_id商品类型,keyword搜索关键字
    $data = array(
        'status' => $status,
        'type_id' => $type_id,
    );
    empty($is_show) || $data['show'] = $is_show;
    empty($keyword) || $data['keyword'] = $keyword;
}

$goods_list = $task_goods_obj->user_goods_list($user_id, $data, false, 'goods_id DESC', $limit_str, '*');
if ($location_id == 'test') { //for debug
    $options['data'] = array(
        '$user_id' => $user_id,
        '$data' => $data,
        '$limit_str' => $limit_str,
        '$goods_list' => $goods_list,
    );
    return $cp->output($options);
}
// 类型 编辑页面PID
$edit_pids = array(
    31 => 1250028, // 模特服务
    5 => 1250034,    // 摄影培训
    12 => 1250035,    // 影棚租赁
    3 => 1250036,    // 化妆服务
    40 => 1250037,    // 摄影服务
    41 => 1250038,  // 美食达人
    43 => 1250039, // 其它服务
);
// 服务状态
$status_arr = array(
    0 => 'review',
    1 => 'pass',
    2 => 'rejected',
    3 => 'remove',
);
$promotion_obj = POCO::singleton('pai_promotion_class');  // 促销
$task_log_obj = POCO::singleton('pai_task_admin_log_class');
$service_list = array();
foreach ($goods_list as $goods) {
    $goods_id = $goods['goods_id'];
    $type_id = $goods['type_id'];
    $goods_status = $goods['status']; // 状态 0 未审核 1 通过 2未通过 3删除
    if ($type_id == 42) {
        // 获取场次信息
        $showing_result = $task_goods_obj->user_get_goods_info($goods_id, $user_id);
        $prices_data_list = $showing_result['data']['prices_data_list'];  // 价格列表
        $exhibit = interface_get_goods_showing($prices_data_list);
        if (empty($exhibit['showing_exhibit'])) {  // 全部结束
            $showing_info = $exhibit['all_exhibit'][0];
        } else {
            $showing_info = $exhibit['showing_exhibit'][0];
        }
        $stage_id = $showing_info['stage_id'];  // 场次ID

        $goods_status = $goods['edit_status'] == 1 ? 0 : $goods_status;  // 审核状态
        $activity_result = $task_goods_obj->get_goods_id_activity_info($goods_id);
        $price_str = sprintf('%.2f', $activity_result['min_price']) . '-' . sprintf('%.2f', $activity_result['max_price']);
        $ing_show = intval($activity_result['ing_show']);  // 正在进行中的活动数
        $is_finish = $ing_show > 0 ? 0 : 1;  // 是否结束
        $service_info = array(
            'goods_id' => $goods_id, // 商品ID
            'titles' => preg_replace('/&#\d+;/', '', $goods['titles']), // 服务名称
            'type_id' => $type_id, // 服务分类
            'is_show' => $goods['is_show'], // 服务状态
            'images' => yueyue_resize_act_img_url($goods['images'], 640), // 图片展示
            'prices' => '￥' . $price_str,
            'link' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250044&type=inner_app', // 跳转服务详情
            'total_showing' => '共' . intval($activity_result['total_show']) . '场',
            'showing_num' => $ing_show,
            'showing_desc' => '场进行中',
            'is_finish' => $is_finish,  // 0 可参与 1 结束
            'status_str' => $status_arr[$goods_status],
            'notice' => '测试',
            'buy_num' => '已参加：' . intval($activity_result['has_join_num']) . '人', // ($goods['stock_num_total'] - $goods['stock_num']),
            'roster_link' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&stage_id=' . $stage_id . '&pid=1250045&type=inner_app', // 名单URL
            'action' => activity_action($goods_status, $is_finish, $goods['is_show']),
        );
        $service_list[] = $service_info;
        continue;
    }
    $prices_list = unserialize($goods['prices_list']);
    if (!empty($prices_list) && $service_status != 'send') {
        $min = $max = 0;
        $pro_prices_list = array();
        foreach ($prices_list as $k => $price) {
            if ($price <= 0) {
                continue;
            }
            $pro_prices_list[] = array(  // for 促销
                'prices_type_id' => $k, //必填
                'goods_prices' => $price, //必填
            );
            $min = ($min > 0 && $min < $price) ? $min : $price;
            $max = ($max > 0 && $max > $price) ? $max : $price;
        }
        if ($min == $max) {
            $price_str = sprintf('%.2f', $min);
        } else {
            $price_str = sprintf('%.2f', $min) . '-' . sprintf('%.2f', $max);
        }
    } else {
        $price_str = sprintf('%.2f', $goods['prices']);
        $pro_prices_list = $price_str;
    }
    $score = interface_reckon_average_score($goods['total_overall_score'], $goods['review_times']);  // 用户评分
    $is_show = $goods['is_show'];
    // 按钮
    $edit_action = array('title' => '编辑', 'request' => 'edit');
    $sell_status = array('title' => $is_show == 1 ? '下架' : '上架', 'request' => $is_show == 1 ? 'off_sell' : 'on_sell',);
    $action = array($sell_status);
    // 服务编辑 链接
    $edit_url = 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&operate=edit&pid=' . $edit_pids[$type_id] . '&type=inner_app';
    if ($type_id != 41 && version_compare($version, '1.2', '>')) {  //
        // 1.2 版本, 除了美食,所有都有编辑功能
        $action = array($edit_action, $sell_status);
    } elseif ($type_id == 31 && version_compare($version, '1.1', '>')) {
        // 模特服务 有编辑
        $action = array($edit_action, $sell_status);
    } else {
        $edit_url = '';
    }
    $notice = '';
    if ($goods_status == 2) {
        $reject_data = array('type_id' => 2007, 'action_type' => 2, 'action_id' => $goods_id);
        $log_result = $task_log_obj->get_log_by_type_last($reject_data);  // 拒绝原因
        if ($location_id == 'test2') { //for debug
            $options['data'] = array(
                '$goods_id' => $goods_id,
                '$log_result' => $log_result,
            );
            $cp->output($options);
            exit;
        }
        $notice = trim(preg_replace('/\d+:/', '', $log_result['note']));
        $action = array(array('title' => '原因', 'request' => 'notice'));
        if ($type_id == 31) {
            $action = array($edit_action, array('title' => '原因', 'request' => 'notice'));  // 审核中模特 有编辑
        } else if ($type_id != 41 && version_compare($version, '1.2', '>')) {  //
            // 1.2 版本
            $action = array($edit_action, array('title' => '原因', 'request' => 'notice'));
        } else {
            $edit_url = '';
        }
        $notice = empty($notice) ? '审核未通过' : $notice;
    } elseif ($goods_status == 0) {
        // 审核中 模特服务有编辑
        if ($type_id == 31) {
            $action = array($edit_action);  // 审核中模特 有编辑
        } else if ($type_id != 41 && version_compare($version, '1.2', '>')) {  //
            // 1.2 版本,除约美食 其他都有编辑
            $action = array($edit_action);
        } else {
            $action = array();
            $edit_url = '';
        }
        $notice = '审核中';
    }
    $statis_result = $task_goods_obj->get_goods_statistical($goods_id);
    if ($location_id == 'test1') { //for debug
        $options['data'] = array(
            '$goods_id' => $goods_id,
            '$statis_result' => $statis_result,
        );
        $cp->output($options);
        exit;
    }
    $service_info = array(
        'goods_id' => $goods_id, // 商品ID
        'titles' => preg_replace('/&#\d+;/', '', $goods['titles']), // 服务名称
        'type_id' => $type_id, // 服务分类
        'is_show' => $is_show, // 服务状态
        'images' => yueyue_resize_act_img_url($goods['images'], 640), // 图片展示
        'prices' => '￥' . $price_str,
        'link' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1250007&type=inner_app', // 跳转服务详情
        'edit_url' => $edit_url,
        'buy_num' => '已有' . $statis_result['bill_pay_num'] . '人购买',
        'score' => strval($score),
        'status_str' => $status_arr[$goods_status],
        'notice' => $notice,
        'action' => $action,
    );
    if (!empty($pro_prices_list) && version_compare($version, '1.2', '>')) {
        $promotion = interface_get_goods_promotion($user_id, $goods_id, $pro_prices_list, $promotion_obj);
        if (!empty($promotion)) {
            $promotion['abate'] = '立省: ' . $promotion['abate'];
            $promotion['anotice'] = $promotion['notice'];
            unset($promotion['notice']);
            // 添加 促销信息
            $service_info = array_merge($service_info, $promotion);
        }
    }
    $service_list[] = $service_info;
}

/**
 * 生成 活动管理 按钮
 *
 * @param int $status 状态 0 未审核 1 通过 2未通过 3删除
 * @param int $is_finish 是否结束
 * @param int $is_show 1 上架 2 下架
 * @return array
 */
function activity_action($status, $is_finish = 0, $is_show = 0) {
    // 按钮文案
    $action_arr = array(
        '000' => '上架.on_sell|名单.roster',
        '200' => '原因.reason|名单.roster',
        '100' => '下架.off_sell|名单.roster',
    );
    if ($status == 2) {
        $action = $action_arr['200'];
    } elseif ($is_show == 2) {
        $action = $action_arr['000'];
    } else {
        $action = $action_arr['100'];
    }
    $btn = explode('|', $action);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
        );
    }
    return $arr;
}

$options['data']['list'] = $service_list; //
return $cp->output($options);
