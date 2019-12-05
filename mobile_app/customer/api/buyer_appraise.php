<?php

/**
 * �ҵ� ����
 * 
 * @since 2015-6-19
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// ��ȡ�ͻ��˵�����
$cp = new poco_communication_protocol_class();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$page = intval($client_data['data']['param']['page']);  // �ڼ�ҳ
$rows = intval($client_data['data']['param']['rows']); // ÿҳ��������(5-100֮��)
$limit = trim($client_data['data']['param']['limit']);  // ��ֵ��: 0,20
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ( $rows > 100 ? 100 : $rows);

    $limit_str = ($page - 1) * $rows . ',' . $rows;
} else {
    $limit_str = $limit;
}

$api_obj = POCO::singleton('pai_mall_api_class');

// ��ȡ�����б�
$comment_list= $api_obj->api_packing_get_buyer_comment_list($user_id, false, '', 'comment_id DESC', $limit_str, '*');
if ($location_id == 'test') {
    $options['data']['list'] = $api_obj->api_get_buyer_comment_list($user_id, false, '', 'comment_id DESC', $limit_str, '*');
    $cp->output($options);
    exit;
}

$options['data']['list'] = $comment_list;

$cp->output($options);
