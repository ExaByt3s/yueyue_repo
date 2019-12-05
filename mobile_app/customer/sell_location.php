<?php
/**
 * ��ȡ ����λ������
 *
 * @author willike<chenwb@yueus.com>
 * @since 2015-9-28
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$limit = $client_data['data']['param']['limit'];
$version = $client_data['data']['version'];  // �汾

$location = array(
    array('name' => '����', 'location_id' => '101029001'),
    array('name' => '����', 'location_id' => '101001001'),
    array('name' => '�Ϻ�', 'location_id' => '101003001'),
    array('name' => '�ɶ�', 'location_id' => '101022001'),
    array('name' => '����', 'location_id' => '101004001'),
    array('name' => '����', 'location_id' => '101015001'),
);
if (version_compare($version, '88.8.8', '=')) {
    $location[] = array('name' => '����', 'location_id' => '100000000');
}
if (version_compare($version, '2.2.0_r3', '=')) {
    $location = array(array('name' => '����', 'location_id' => '101029001'),);
}
$other = array(
    array(
        'title' => '�㶫����',
        'items' => array(
            array('name' => '����', 'location_id' => '101029001'),
            array('name' => '����', 'location_id' => '101029002'),
        ),
    ),
    array(
        'title' => '��������',
        'items' => array(
            array('name' => '�Ϻ�', 'location_id' => '101003001'),
        ),
    ),
);
if (version_compare($version, '3.2.10', '=')) { // ��˰�
//    $location = array(array('name' => '����', 'location_id' => '101029001'),);
//    $other = array(array(
//        'title' => '�㶫����',
//        'items' => array(
//            array('name' => '����', 'location_id' => '101029001'),
//        ),
//    ),);
}
$location_data = array(
    'service' => array(
        'title' => '�ѿ�ͨ�������',
        'list' => $location,
    ),
    'other' => array(
        'title' => '��������',
        'list' => $other,
    ),
);
$options['data'] = $location_data;
return $cp->output($options);
