<?php

/**
 * �ҵ� (����ҳ)
 * 
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-6-26
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];

//�̼Ұ��û��汾��¼����ʱ������ʼ
if ($user_id) {
    $version = $client_data['data']['version'];
    $add_time = date('Y-m-d H:i:s');
    $sql_str = "INSERT IGNORE INTO test.user_version_tbl(user_id, `version`, add_time) VALUES ($user_id, '{$version}', '{$add_time}')";
    db_simple_getdata($sql_str, TRUE, 101);
}
//�̼Ұ��û��汾��¼����ʱ��������

// ��ȡ�û�����
$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // ��ȡ�û���Ϣ
$profile = $user_result['seller_data']['profile'][0];
$type_id = $profile['type_id'];

$user = array(
    'user_id' => $user_id,
    'name' => $profile['name'],
    'avatar' => $profile['avatar'],
    'edit' => array(
        'title' => '�༭',
        'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250023&type=inner_app',
    ),
    'detail' => array(
        'title' => '�鿴���Ͽ�',
        'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250004&type=inner_app',
    ),
    'service' => array(
        'title' => '�ҵķ���',
        'request' => 'yueseller://goto?user_id=' . $user_id . '&type_id=' . $type_id . '&pid=1250006&type=inner_app',
    ),
    'confirm' => array(
        'title' => '������֤',
        'request' => 'yueseller://goto?type=inner_web&url=' . url_switch('http://s.yueus.com/test/no_support_cetificate.php') .
        '&wifi_url=' . url_switch('http://s.yueus.com/test/no_support_cetificate.php') . '&showtitle=1',
    ),
    'appraise' => array(
        'title' => '�ҵ�����',
        'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250009&type=inner_app'
    ),
    'wallet' => array(
        'title' => '�ҵ�Ǯ��',
        'request' => 'yueseller://goto?type=inner_web&url=' . url_switch('http://s.yueus.com/test/pocket.php', FALSE) .
        '&wifi_url=' . url_switch('http://s.yueus.com/test/pocket.php', FALSE) . '&showtitle=1',
    ),
    'setting' => array('title' => '����', 'request' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250011&type=inner_app'),
);

$options['data'] = $user;
$cp->output($options);

/**
 * ���� �л�����
 * 
 * @param string $url ����
 * @param boolean $test �Ƿ��ǲ���
 * @param string $test_str url ���Ը����ֶ�
 * @return string 
 */
function url_switch($url, $test = TRUE, $test_str = 'test') {
    $url = strpos($url, '://') ? $url : urldecode($url);  // �Ƿ�ת�����URL
    if (stripos($url, 'http://') === FALSE) {
        return $url;
    }
    $test_str = empty($test_str) ? 'test' : $test_str;
    if ($test === TRUE) { // ��������
        $is_test = strpos($url, '/' . trim($test_str, '/') . '/') ? TRUE : FALSE;
        $return_url = ($is_test === TRUE) ? $url : str_replace('yueus.com/', 'yueus.com/' . $test_str . '/', $url);
    } else {
        $return_url = str_replace('yueus.com/' . $test_str . '/', 'yueus.com/', $url);
    }
    return urlencode($return_url);
}
