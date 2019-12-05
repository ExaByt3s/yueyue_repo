<?php
/**
 * ���� ��ʼ���ӿ�
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-10-8
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = $client_data['data']['param']['goods_id'];   // ��Ʒ���
$operate = $client_data['data']['param']['operate'];   // ��������  edit/add
$type_id = 43; // ������Լ��Ȥ��
$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
if ($operate == 'edit') {
    if (empty($goods_id)) {
        $options['data'] = array(); //
        return $cp->output($options);
    }
    $goods_result = $mall_goods_obj->user_get_goods_info($goods_id, $user_id);
    if ($goods_result['result'] != 1) {
        return $cp->output(array('data' => array()));
    }
    $goods_info = $goods_result['data'];
    $prices_data = $goods_info['prices_data_list'];
} else if ($operate == 'add') {
    $goods_result = $mall_goods_obj->user_show_goods_data($type_id);
    $goods_info = $goods_result['data'];
    $prices_data = $goods_info['prices_data']['d395771085aab05244a4fb8fd91bf4ee']['child_data'];
} else {
    $options['data'] = array(); //
    return $cp->output($options);
}
if ($location_id == 'test') {
    $options['data'] = array(
        '$operate' => $operate,
        '$goods_result' => $goods_result,
    );
    return $cp->output($options);
}
if (empty($goods_info)) {
    $options['data'] = array(); //
    return $cp->output($options);
}
// ��ȡ����
$goods_property = interface_init_goods_property($goods_info['system_data']);
$init_list = $goods_property['init_list'];
$first_options = $goods_property['first_options'];  // һ��ѡ��
$second_options = $goods_property['second_options'];
// ����ͼ
$images = $goods_info['goods_data']['images'];
$init_list['goods_id'] = $goods_info['goods_data']['goods_id'];
// ����
$title = $goods_info['default_data']['titles']['value'];
$init_list['titles'] = interface_init_goods_property_patch($title, 'titles');
$init_list['prices'] = array(
    'type' => '4', //�ؼ�����
    'id' => 'default_data.prices', // ����ֵ
    'title' => '�۸�', // ��ʾ����
    'value' => intval($goods_info['default_data']['prices']['value']), // ��ʾֵ
    'option_value' => '', // ���ֵ
    'input_type' => '3', // �������� 1 ���� 2 ���� 3 С��
    'max_length' => 0,  // ����������
    'hint' => '',
    'unit' => 'Ԫ',
    'tips' => '����д�۸�',
);
// ����λ��
$goods_location = $goods_info['goods_data']['location_id'];
$init_list['location_id'] = interface_init_goods_property_patch($goods_location, 'location_id');
$key_map = array(
    'common' => array(
        '07cdfd23373b17c6b337251c22b7ea57' => 'type', // ����ؼ���
        'fb7b9ffa5462084c5f4e7e85a093e6d7' => 'other_tags', // ������ǩ
        'titles' => 'titles', // �������� default_data[titles]
        'a8abb4bb284b5b27aa7cb790dc20f80b' => 'classify',  // ��������
        'prices' => 'prices',  // �۸�
        'location_id' => 'location_id',  // ����λ��
        'd709f38ef758b5066ef31b18039b8ce5' => 'address',  //��ַ
        '1651cf0d2f737d7adeab84d339dbabd3' => 'refund',  // �˷ѹ���
        '0e01938fc48a2cfb5f2217fbfb00722d' => 'attention',  //ע������
    )
);
if (version_compare($version, '1.3', '>')) {
    $init_list['location_id']['type'] = 6;  // 6����λ��
    // �γ���
    $class_options = $init_list['550a141f12de6341fba65b0ad0433500']['options'];
    $init_list['d709f38ef758b5066ef31b18039b8ce5']['title'] = '�Ͽεص�';  // ��ַ
    $init_list['eed5af6add95a9a6f1252739b1ad8c24']['title'] = '�γ�����';
    $init_list['550a141f12de6341fba65b0ad0433500']['options'] = array_merge(
        array(
            $init_list['location_id'],  // ����λ��
            $init_list['d709f38ef758b5066ef31b18039b8ce5'],  // ��ַ
            $init_list['eed5af6add95a9a6f1252739b1ad8c24'],  // ����
        ), $class_options
    );
    // ��Ʒ��
    $goods_options = $init_list['67f7fb873eaf29526a11a9b7ac33bfac']['options'];
    $init_list['eed5af6add95a9a6f1252739b1ad8c24']['title'] = '��Ʒ����';
    $init_list['67f7fb873eaf29526a11a9b7ac33bfac']['options'] = array_merge(
        array(
            $init_list['eed5af6add95a9a6f1252739b1ad8c24'],  // ����
        ), $goods_options
    );
    // ������
    $services_options = $init_list['1a5b1e4daae265b790965a275b53ae50']['options'];
    $init_list['d709f38ef758b5066ef31b18039b8ce5']['title'] = '����ص�';  // ��ַ
    $init_list['eed5af6add95a9a6f1252739b1ad8c24']['title'] = '��������';
    $init_list['1a5b1e4daae265b790965a275b53ae50']['options'] = array_merge(
        array(
            $init_list['location_id'],  // ����λ��
            $init_list['d709f38ef758b5066ef31b18039b8ce5'],  // ��ַ
            $init_list['eed5af6add95a9a6f1252739b1ad8c24'],  // ����
        ), $services_options
    );
    // 1.3 �汾 ,�����ַ, ����
    unset($init_list['location_id'], $init_list['d709f38ef758b5066ef31b18039b8ce5'], $init_list['eed5af6add95a9a6f1252739b1ad8c24']);
}
// ������������ ( �����ǩ -> ����ؼ��� )
$init_list['07cdfd23373b17c6b337251c22b7ea57']['title'] = '����ؼ���';
$init_list['a8abb4bb284b5b27aa7cb790dc20f80b']['title'] = '��������';
$goods_init_info['goods_id'] = $goods_id;  // ��ʼ������
foreach ($key_map as $key => $map) {
    foreach ($map as $k => $v) {
        $value = $init_list[$k];
        if (empty($value)) {
            continue;
        }
        $goods_init_info[$key][$v] = $value;  // keyת����
        // ��װ ѡ��
        if (isset($first_options[$k])) {
            $first_options_init[$v . '_options'] = $first_options[$k];
        }
    }
}
if (version_compare($version, '1.3', '>')) {
    // �������������
    $property_keymap = array(
        '550a141f12de6341fba65b0ad0433500' => '15d4e891d784977cacbfcbb00c48f133',  // �γ�������
        '67f7fb873eaf29526a11a9b7ac33bfac' => 'c203d8a151612acf12457e4d67635a95',  // ��Ʒ������
        '1a5b1e4daae265b790965a275b53ae50' => '13f3cf8c531952d72e5847c4183e6910',  // ����������
    );
    $property_option = array();
    foreach ($property_keymap as $key => $value) {
        $property_option[] = array(
            'property_type' => $value,
            'property_list' => $init_list[$key]['options'],
        );
    }
    $goods_init_info['property'] = $property_option;
}
$goods_init_info['options'] = array(
    'first' => $first_options_init,
    'second' => $second_options,
);
if (version_compare($version, '1.3', '<')) {
    // �۸�
    $goods_init_info['standerd_title'] = '���&�۸�';
    $goods_init_info['standerd'] = interface_init_goods_property_patch($prices_data, 'standerd');
}
// ��Ʒ����
$contents = interface_content_to_ubb($goods_info['default_data']['content']['value']);
// �ϴ�����
$upload_config = interface_services_upload_config($images, $contents, $type_id);
$goods_init_info['upload'] = $upload_config;

$options['data'] = $goods_init_info;
return $cp->output($options);