<?php

/**
 * �����б� ɸѡ���� ���� (����)
 *
 * @author ��ҫ��
 * @editor willike <chenwb@yueus.com>
 * @since 2015/10/9
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');
$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$type_id = (int)$client_data['data']['param']['type_id']; //����ID
$options['data'] = array(
    'msg' => '�ýӿ�������',
);
return $cp->output($options);
//$result = interface_get_search_screening($type_id);
//$options['data'] = $result;
//return $cp->output($options);