<?php

/**
 * �ҵ� ����
 *
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$buyer_id = intval($client_data['data']['param']['buyer_id']);  // ���ID
$goods_id = intval($client_data['data']['param']['goods_id']);  // ��ƷID
$page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
$rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    $limit_str = $limit;
}

$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
// ��ȡ�����б�
if (empty($buyer_id)) {
    $where = empty($goods_id) ? '' : 'goods_id=' . $goods_id;
    $comment_result = $mall_comment_obj->get_seller_comment_list($user_id, false, $where, 'add_time DESC', $limit_str, '*');
} else {
    $comment_result = $mall_comment_obj->get_buyer_comment_list($buyer_id, false, '', 'add_time DESC', $limit_str, '*');
}
if ($location_id == 'test') {
    $options['data'] = array(
        'user_id' => $user_id,
        'where' => $where,
        'list' => $comment_result,
    );
    return $cp->output($options);
}
$comment_list = array();
foreach ($comment_result as $value) {
    $from_user_id = $value['from_user_id'];
    $goods_id = $value['goods_id'];
    $type_id = $value['type_id'];
    $is_anonymous = $value['is_anonymous'];  // �Ƿ��������� 0Ϊ�� 1Ϊ��
    $order_id = $value['order_id'];
    $title = $type_id == 20 ? 'ֱ�Ӹ���' : '';
    if (!empty($goods_id)) {
        $goods_info = $task_goods_obj->user_get_goods_info($goods_id, $user_id);
        if ($location_id == 'test1') {
            $options['data'] = array(
                '$goods_id' => $goods_id,
                '$goods_info' => $goods_info,
            );
            $cp->output($options);
            exit;
        }
        $title = $goods_info['data']['goods_data']['titles'];
    }
    $score = $value['overall_score'];  // ����
    $score = empty($score) ? 5 : $score;
    if (empty($buyer_id)) {
        $name = $is_anonymous == 1 ? '����' : get_user_nickname_by_user_id($from_user_id);  // �û���
        // �̼� ( ���������ߵ����� )
        $avatar = $is_anonymous == 1 ? get_user_icon(10000) : get_user_icon($from_user_id);
    } else {
        $name = $is_anonymous == 1 ? '����' : get_seller_nickname_by_user_id($from_user_id);  // �̼���
        // ������ ( �����̼ҵ����� )
        $avatar = $is_anonymous == 1 ? get_seller_user_icon(10000) : get_seller_user_icon($from_user_id);
    }
    $comment_list[] = array(
        'type_id' => $type_id,
        'from_user_id' => $from_user_id,
        'avatar' => $avatar, // ͷ��
        'customer' => empty($name) ? '' : $name,
        'service_title' => ' ' . $title,
        'rating' => sprintf('%.1f', $score), // ����
        'comment' => $value['comment'], // ��������
        'add_time' => date('Y-m-d', $value['add_time']), // ����ʱ��
    );
    unset($goods_info);
}

$options['data']['list'] = $comment_list;
return $cp->output($options);
