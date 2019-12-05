<?php

/**
 * ����Э���ļ� ( ��� )
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-5
 * @description ����Э�鴦������(include), �� ���ݱ��� ����Ϊ $post_data
 */
// ���� �Ƿ�����Э��
if (!defined('YUE_INVOCATION_PROTOCOL')) {
    define('YUE_INVOCATION_PROTOCOL', TRUE); // ����Э��
}
// �����Ƿ� ��֤access_token
if (!defined('YUE_INPUT_CHECK_TOKEN')) {
    define('YUE_INPUT_CHECK_TOKEN', TRUE);
}
// �Ƿ���Ҫ����ȫ��DB��
if (!isset($DB) || empty($DB)) {
    global $DB;
}
// ������
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
// ����Э��
$yue_protocol_path = str_replace('\\', '/', dirname(dirname(__FILE__))) . '/protocol/';
require($yue_protocol_path . 'yue_protocol.inc.php');
// ��ȡ�ͻ��˵�����
$cp = new yue_protocol_system();
// �ж��Ƿ� ʹ��Э��
if (YUE_INVOCATION_PROTOCOL === FALSE) {
    // ʹ�� include ���� ( for web )
    if (empty($post_data)) {
        exit('POST data is empty!');
    }
    $client_data = $cp->get_input_process($post_data, YUE_INPUT_CHECK_TOKEN, FALSE);
} else {
    // �������� ( for APP )
    $client_data = $cp->get_input(array('be_check_token' => YUE_INPUT_CHECK_TOKEN));
}
require('protocol_methods.func.php');  // ���빫������
// ����yueyue�ײ㹫������
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
// ͳһ���� �û�ID 2015-9-23
//if(!is_numeric($client_data['data']['param']['user_id'])){
//    $client_data['data']['param']['user_id'] = '0';
//}
$user_id = $client_data['data']['param']['user_id'];
$version = $client_data['data']['version'];  // �汾��

// ���ͳ�� 2015-11-24
$tongji_type = filter_input(INPUT_SERVER, 'REQUEST_URI');
$tongji_query = 'query=' . serialize($client_data['data']);
yueyuetj_touch_log($tongji_type, $tongji_query);