<?php
define('TEST_SENDSERVER_SWITCH', 1);
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$push_obj = POCO::singleton('pai_information_push');

$send_data[media_type] = 'card';
$send_data[card_style] = '2';
$send_data[card_text1] = '��Ƭ��Ϣ������0001';
$send_data[card_text2] = '�İ�2';
$send_data[card_title] = '�鿴����';
$send_data[link_url]   = 'yueyue://goto?type=inner_app&url=' . urlencode('http://www.yueus.com') . '$wifi_url=' . urlencode('http://www.yueus.com');
var_dump($push_obj->message_sending_for_system(100008, $send_data));
unset($send_data);

$send_data[media_type] = 'card';
$send_data[card_style] = '1';
$send_data[card_text1] = '��Ƭ��Ϣ������0001';
$send_data[card_text2] = '$ 100';
$send_data[card_title] = '�鿴����';
$send_data[link_url]   = 'yueyue://goto?type=inner_app&url=' . urlencode('http://www.yueus.com') . '$wifi_url=' . urlencode('http://www.yueus.com');
var_dump($push_obj->message_sending_for_system(100008, $send_data));
unset($send_data);

$send_data[media_type] = 'text';
$send_data[content]    = '���ݣ�text���ͣ����ݲ��ԣ�����00001';
var_dump($push_obj->message_sending_for_system(100008, $send_data, 10000, 'yueseller'));
unset($send_data);

$send_data[media_type] = 'notify';
$send_data[content]    = '���ݣ�notify���ͣ� ���ݲ��ԣ�����00002';
$send_data[link_url]   = 'yueyue://goto?type=inner_app&url=' . urlencode('http://www.yueus.com') . '$wifi_url=' . urlencode('http://www.yueus.com');
var_dump($push_obj->message_sending_for_system(100008, $send_data, 10000, 'yueseller'));
unset($send_data);
exit();
$user_id = $_GET['user_id']?$_GET['user_id']:100008;
$buy_user_id = $_GET['buy_user_id']?$_GET['buy_user_id']:100008;

$send_data[media_type] = 'card';
$send_data[card_style] = '2';
$send_data[card_text1] = '�����ţ�XXXXXXXXXXXXXXX';
$send_data[card_text2] = '�İ�2';
$send_data[card_title] = '�鿴����';

$to_send_data[media_type] = 'card';
$to_send_data[card_style] = '2';
$to_send_data[card_text1] = '�����ţ�XXXXXXXXXXXXXXX';
$to_send_data[card_text2] = '�İ�5';
$to_send_data[card_title] = 'ȷ�ϻ�ȡ��';

$push_obj = POCO::singleton('pai_information_push');
var_dump($push_obj->card_send_for_order(100260, 'yueseller', 100008, 'yuebuyer', $send_data, $to_send_data, 10));

$send_data[media_type] = 'card';
$send_data[card_style] = '2';
$send_data[card_text1] = '�����ţ�XXXXXXXXXXXXXXX';
$send_data[card_text2] = '�İ�2';
$send_data[card_title] = '�鿴����';

$to_send_data[media_type] = 'card';
$to_send_data[card_style] = '2';
$to_send_data[card_text1] = '�����ţ�XXXXXXXXXXXXXXX';
$to_send_data[card_text2] = '�İ�5';
$to_send_data[card_title] = 'ȷ�ϻ�ȡ��';

$push_obj = POCO::singleton('pai_information_push');
var_dump($push_obj->card_send_for_order(100260, 'yueseller', 100008, 'yuebuyer', $send_data, $to_send_data, 10));
//var_dump($push_obj->card_send_for_order($user_id, 'yuebuyer', $buy_user_id, 'yueseller', $send_data, $send_data, 10));
?>