<?php

/**
 * С���� �б�
 * 
 * @author heyaohua
 * @editor chenweibiao<chenwb@yueus.com>
 * @since 2015-6-23
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$type_id = $client_data['data']['param']['type_id'];  // ����ID
$query = (int) $client_data['data']['param']['query'];  // ��ID  ( main_id )
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
    $rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ( $rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    list($lstart, $lcount) = explode(',', $limit);
    $lcount = $lcount > 100 ? 100 : $lcount;
    $limit_str = $lstart . ',' . $lcount;
}
$rank_event_v3_obj = POCO::singleton('pai_rank_event_v3_class');
$event_result = $rank_event_v3_obj->get_cms_rank_info_list($query, $limit_str);
if ($location_id == 'test') {
    $options['data'] = array(
        '$query' => $query,
        '$limit_str' => $limit_str,
        '$event_result' => $event_result,
    );
    return $cp->output($options);
}
$category = array();
foreach ($event_result['list'] as $val) {
    $url = $val['curl'];
    $category[] = array(
        'title' => $val['title'],
        'desc' => $val['content'],
        'img' => $val['img_url'],
        'url' => $url . '&user_id=' . $user_id . '&type_id=' . $type_id,
    );
}
$title = $event_result['title'];
$search_url = 'yueyue://goto?type=inner_app&type_id=' . $type_id . '&pid=1220124';  // ������ ��ת
if ($query == 9948) {
    // ���� ��תURL
    $search_url = 'yueyue://goto?type=inner_app&pid=1220098&search_type=waipai&url=' . urlencode('yueyue://goto?type=inner_app&pid=1220076&key=default');
}
if (version_compare($version, '3.3', '>')) {
    $search_services = 'yueyue://goto?type=inner_app&pid=1220126&keyword=&type_id=' . $type_id;  // ��������
    if ($type_id == 99 || $type_id == 42 || $query == 9948) { // �
        $search_services = 'yueyue://goto?type=inner_app&pid=1220153&keyword=&type_id=42';  // �����
    }
    $search_url = 'yueyue://goto?type=inner_app&type_id=' . $type_id . '&pid=1220124';
    $search_sellers = 'yueyue://goto?type=inner_app&pid=1220125&keyword=&type_id=' . $type_id;  // �����̼�
    $search_url = $search_url . '&search_services=' . urlencode($search_services) . '&search_sellers=' . urlencode($search_sellers);
}
$classify_list = array(
    'title' => $title,
    'category_total' => $event_result['total_count'],  // �б�����
    'category_list' => $category, // �����б�
    'search_url' => $search_url,
);
$options['data'] = $classify_list;
return $cp->output($options);
