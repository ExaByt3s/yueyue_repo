<?php

/**
 * �˳���¼
 * 
 * @author heyaohua
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once("../../protocol_common.inc.php");

// ��ȡ�ͻ��˵�����
$cp = new poco_communication_protocol_class();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input();

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];

$role = 'yuebuyer';
$obj = POCO::singleton('pai_nfc_class');
$obj->mobile_logout($user_id, $role);

$data['tips'] = '�˳��ɹ�!';
$options['data'] = $data;

$cp->output($options);
