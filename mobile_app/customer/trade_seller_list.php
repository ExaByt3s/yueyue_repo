<?php

/**
 * �̼� �б�
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-9-7
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$return_query = $client_data['data']['param']['return_query'];   // ��ѯ����
$title = $client_data['data']['param']['title'];   // ����
$title = empty($title) ? '�̼��б�' : $title;
$pid = $client_data['data']['param']['page_id'];   // PID
$version = $client_data['data']['version']; // �汾��
$img_size = $client_data['data']['param']['img_size'];   // ͼƬ����
$size = ($img_size == 'small') ? 260 : 640;  // ͼƬ�ߴ�
$type_id = $client_data['data']['param']['type_id'];  // �����ID
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
    $rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ($rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $lstart . ',' . $lcount;
}
$data = array();
if (!empty($return_query)) {
    $en_ret = mb_convert_encoding(urldecode($return_query), 'gbk', 'utf-8');
    parse_str($en_ret, $data);   // ����������
}
$seller = array();
$total = 0; // ����
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$default_cover = $mall_seller_obj->_seller_cover;  // Ĭ�ϱ���
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
if ($data['yueyue_static_cms_id']) {
    // ��ȡ������
    $cms_obj = new cms_system_class();
    $record_list = $cms_obj->get_last_issue_record_list(false, $limit_str, 'place_number DESC', $data['yueyue_static_cms_id']);
    // ��������
    $total = $cms_obj->get_last_issue_record_list(TRUE, $limit_str, 'place_number DESC', $data['yueyue_static_cms_id']);
    foreach ($record_list as $value) {
        if (empty($value['link_url']) && $data['cms_type'] == 'mall') {
            // �����̼�
            $search_data = array(
                'keywords' => $value['user_id'],
            );
            $result_list = $mall_seller_obj->user_search_seller_list($search_data, '0,1');
            $search_result = $result_list['data'][0];
            if (empty($search_result)) {
                continue;
            }
            if ($location_id == 'test1') {
                $options['data'] = array(
                    '$search_data' => $search_data,
                    '$result_list' => $result_list,
                    'param' => $client_data['data']['param'],
                    '$search_data' => $search_data,
                );
                return $cp->output($options);
            }
            $seller_id = $search_result['user_id'];
            $buy_num = $search_result['bill_finish_num']; // ���״���
            $score = $search_result['total_average_score'] <= 0 ? 5 : sprintf('%.2f', $search_result['total_average_score']);
            $cover = empty($search_result['cover']) ? $default_cover : $search_result['cover'];
            $seller[] = array(
                'user_id' => $seller_id,
                'name' => $search_result['name'],
                'cover' => yueyue_resize_act_img_url($cover, $size), // ����ͼ
                'type_id' => trim($search_result['type_id'], ','), // ����
                'score' => strval($score), // ����
                'buy_num' => $buy_num,
                'trade_num' => '���״�����' . $buy_num . '��',
                'goods_num' => '�ṩ������Ŀ ' . $search_result['onsale_num'] . '��',
                'link' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app', // �̼���ҳ
            );
        }
    }
} else if (isset($data['s_action']) && $data['s_action'] == 'seller') {
    // ���� �̼��б� ( ȫ������ )
    // type_id%3D31%26city%3D101029%26location_id%3D101029001%26total_times_s%3D1%26total_times_e%3D10%26total_money_s%3D1%26total_money_e%3D10%26status%3D1%26m_height%3D160%2C170%26s_action%3Dseller
    $search_result = $mall_seller_obj->user_search_seller_list($data, $limit_str);
    if ($location_id == 'test') {
        $options['data'] = array(
            '$data' => $data,
            '$search_result' => $search_result,
        );
        return $cp->output($options);
    }
    $total = $search_result['total'];
    // ��װ���ݷ���
    $goods = array();
    foreach ($search_result['data'] as $value) {
        $seller_id = $value['user_id'];
        $score = $search_result['total_average_score'] <= 0 ? 5 : sprintf('%.2f', $search_result['total_average_score']);
        $buy_num = $value['bill_finish_num'];  // ���״���
        $cover = empty($value['cover']) ? $default_cover : $value['cover'];
        $seller[] = array(
            'user_id' => $seller_id,
            'name' => $search_result['name'],
            'cover' => yueyue_resize_act_img_url($cover, $size), // ����ͼ
            'type_id' => trim($search_result['type_id'], ','), // ����
            'score' => strval($score), // ����
            'buy_num' => $buy_num,
            'trade_num' => '���״�����' . $buy_num . '��',
            'goods_num' => '�ṩ������Ŀ ' . intval($search_result['onsale_num']) . '��',
            'link' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app', // �̼���ҳ
        );
    }
}
// ����
$share = array();
if (version_compare($version, '3.2', '>')) {
    // ����
    $share_img = $seller[0]['images'];
    $share = $task_goods_obj->get_list_share_text($pid, $return_query, $title, $share_img);
}
$goods_list = array(
    'title' => $title,
    'list' => $seller,
    'total' => intval($total),
    'share' => $share,
    'tt_link' => 'yueyue://goto?type=inner_app&pid=1220080', // TT˽�˶���
);
$options['data'] = $goods_list;
return $cp->output($options);
