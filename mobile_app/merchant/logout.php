<?php

/**
 * �˳���¼
 * 
 * @author heyaohua
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);  // �ݲ���֤token ( 2015-9-11 )
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];

$role = 'yueseller';
$obj = POCO::singleton('pai_nfc_class');
//$obj->mobile_logout($user_id, $role);

$data['tips'] = '�˳��ɹ�!';
$options['data'] = $data;

$cp->output($options);
