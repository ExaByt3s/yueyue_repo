<?php

/**
 * ���� ����
 * 
 * @since 2015-6-29
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$url_test = false;  // �Ƿ��������
// ��ȡ�ͻ��˵�����
$cp = new poco_communication_protocol_class();
// ��ȡ�û�����Ȩ��Ϣ
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];   // ��ǰ����λ��ID
$user_id = $client_data['data']['param']['user_id'];   // ��ǰ�û�ID

$type_obj = POCO::singleton('pai_mall_goods_type_class');
$type_result = $type_obj->get_type_cate(2);   // ��ȡ��Ʒ����

if ($location_id == 'test') {
    $options['data']['list'] = $type_result;
    $cp->output($options);
    exit;
}
$code_arr = array(12 => 'studio_rent', 2 => 'life_service', 3 => 'makeup', 5 => 'shooting_training',
    31 => 'model_service', 40 => 'photo_service');
$ico_arr = array(
    31 => 'http://image17-c.poco.cn/yueyue/cms/20150710/4810201507101813076653044.png', // ģ��Լ��
    12 => 'http://image17-c.poco.cn/yueyue/cms/20150710/27512015071018133169504499.png', // Ӱ������
    5 => 'http://image17-c.poco.cn/yueyue/cms/20150710/55652015071018135068618082.png', // ��Ӱ��ѵ
    40 => 'http://image17-c.poco.cn/yueyue/cms/20150710/22142015071018144041408929.png', // ��Ӱ����
    3 => 'http://image17-c.poco.cn/yueyue/cms/20150710/80182015071018153952515487.png', // ��ױ����
    55 => 'http://image17-c.poco.cn/yueyue/cms/20150710/7019201507101815592416957.png', // ���Ļ
);

$type_list = array();
foreach ($type_result as $value) {
    $id = $value['id'];
    $url = 'http://yp.yueus.com/mall/user/test/order/list.php?type_id=' . $id . '&status=0';   // �����б�
    $type_list[] = array(
        'id' => $id,
        'name' => $value['name'],
        'link' => 'yueyue://goto?user_id=' . $user_id . '&url=' . url_switch($url, $url_test) . '&wifi_url=' . url_switch($url, $url_test) . '&type=inner_web&showtitle=1', // ������Ʒ�б�
        'ico' => $ico_arr[$id],
    );
}

$options['data']['list'] = $type_list;
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
