<?php
/**
 * ��ױ ��ʼ���ӿ�
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-28
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = $client_data['data']['param']['goods_id'];   // ��Ʒ���
$operate = $client_data['data']['param']['operate'];   // ��������  edit/add
$type_id = 3; // ��ױ
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
    $prices_data = $goods_info['prices_data']['37a749d808e46495a8da1e5352d03cae']['child_data'];
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
interface_init_goods_property_patch($title);
$init_list['titles'] = interface_init_goods_property_patch($title, 'titles');
// ����λ��
$goods_location = $goods_info['goods_data']['location_id'];
$init_list['location_id'] = interface_init_goods_property_patch($goods_location, 'location_id');
// �޸Ļ�ױƷ��ʾ
$init_list['7cbbc409ec990f19c78c75bd1e06f215']['hint'] = '�����뻯ױƷ��ѡ�';
$key_map = array(
    'common' => array(
        'a3f390d88e4c41f2747bfa2f1b5f87db' => 'type', // ױ������
        'titles' => 'titles', // �������� default_data[titles]
        'location_id' => 'location_id',  // ����λ��
        '7cbbc409ec990f19c78c75bd1e06f215' => 'toiletry', // ��ױƷ
        'e2c420d928d4bf8ce0ff2ec19b371514' => 'time', // ��ʱ
    )
);
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
$goods_init_info['options'] = array(
    'first' => $first_options_init,
    'second' => $second_options,
);
// �۸�
$goods_init_info['standerd_title'] = '���&�۸�';
$goods_init_info['standerd'] = interface_init_goods_property_patch($prices_data, 'standerd');
// ��Ʒ����
$contents = interface_content_to_ubb($goods_info['default_data']['content']['value']);
// �ϴ�����
$upload_config = interface_services_upload_config($images, $contents, $type_id);
$goods_init_info['upload'] = $upload_config;

$options['data'] = $goods_init_info;
return $cp->output($options);