<?php
/**
 * ��ѵ��ʼ��
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-24
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(dirname(__FILE__))) . '/protocol_input.inc.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$goods_id = $client_data['data']['param']['goods_id'];   // ��Ʒ���
$operate = $client_data['data']['param']['operate'];   // ��������  edit/add
$type_id = 5; // ��ѵ
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
} else if ($operate == 'add') {
    $goods_result = $mall_goods_obj->user_show_goods_data($type_id);
    $goods_info = $goods_result['data'];
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
$goods_property = interface_init_goods_property($goods_info['system_data']); // ��ȡ����
$init_list = $goods_property['init_list'];
$first_options = $goods_property['first_options'];
$second_options = $goods_property['second_options'];
// ����ͼ
$images = $goods_info['goods_data']['images'];
$init_list['goods_id'] = $goods_info['goods_data']['goods_id'];
// ����
$title = $goods_info['default_data']['titles']['value'];
$init_list['titles'] = interface_init_goods_property_patch($title, 'titles');
// ����λ��
$goods_location = $goods_info['goods_data']['location_id'];
$init_list['location_id'] = interface_init_goods_property_patch($goods_location, 'location_id', $type_id);
$stock_num = $goods_info['default_data']['stock_num']['value'];
$init_list['stock_num'] = array(
    'type' => '4',
    'id' => 'stock_num',
    'title' => '��������',
    'value' => empty($stock_num) ? '' : $stock_num,
    'input_type' => '2',
    'hint' => '',
    'unit' => '��',
    'tips' => '��������������',
);
$prices = $goods_info['default_data']['prices']['value'];
$init_list['prices'] = array(
    'type' => '4',
    'id' => 'prices',
    'title' => '�γ�ѧ��',
    'value' => empty($prices) ? '' : $prices,
    'input_type' => '3',
    'hint' => '',
    'unit' => 'Ԫ',
    'tips' => '������γ�ѧ��',
);
// �޸Ŀγ����� ��λ
$init_list['2a38a4a9316c49e5a833517c45d31070']['unit'] = '';
$key_map = array(
    'common' => array(
        '4f6ffe13a5d75b2d6a3923922b3922e5' => 'train_classify',  // ��ѵ����
        'f5f8590cd58a54e94377e6ae2eded4d9' => 'train_tags',  // ��ǩ
        '9fc3d7152ba9336a670e36d0ed79bc43' => 'train_type', // ��ѵ����
        'titles' => 'titles', // �������� default_data[titles]
        'location_id' => 'location_id',  // ������Χ
        'bbf94b34eb32268ada57a3be5062fe7d' => 'enter_deadline',  // ������ֹ����
        '47d1e990583c9c67424d369f3414728e' => 'lesson_type', // �γ����
        '093f65e080a295f8076b1c5722a46aa2' => 'lesson_target', // �γ�Ŀ��
        '5737c6ec2e0716f3d8a7a5c4e0de0d9a' => 'lesson_points', // �γ�����
        '735b90b4568125ed6c3f678819b6e058' => 'lesson_outline', // �γ̴��
        '5b8add2a5d98b1a652ea7fd72d942dac' => 'lesson_forms', // �γ���ʽ
        '44f683a84163b3523afe57c2e008bc8c' => 'lesson_give', // �ڿη�ʽ
        'fc490ca45c00b1249bbe3554a4fdf6fb,3295c76acbf4caaed33c36b1b5fc2cb1' => 'lesson_addr', // �Ͽε�ַ/������ϵ��ַ
        'fc490ca45c00b1249bbe3554a4fdf6fb' => 'lesson_addr',  // �Ͽε�ַ
        'stock_num' => 'lesson_recruit', // �������� default_data[stock_num]
        'prices' => 'lesson_fee', // �γ�ѧ�� default_data[prices]
        'c058f544c737782deacefa532d9add4c' => 'remark', // ��ע
    ),
    'choice' => array(
        '072b030ba126b2f4b2374f342be9ed44' => 'lesson_begins', // ��������
        'e7b24b112a44fdd9ee93bdf998c6ca0e,52720e003547c70561bf5e03b95aa99f' => 'lesson_time',  // ��ѵʱ��
        '2a38a4a9316c49e5a833517c45d31070' => 'lesson_period', // �γ�����
        '7647966b7343c29048673252e490f736' => 'lesson_quantity', // �γ�����
        'caf1a3dfb505ffed0d024130f58c5cfa' => 'lesson_duration', // ÿ��ʱʱ��
    ),
);
$init_list['fc490ca45c00b1249bbe3554a4fdf6fb']['title'] = '�Ͽε�ַ';
// ���� ��ѵʱ��
$start_time = $init_list['e7b24b112a44fdd9ee93bdf998c6ca0e']['value'];
$end_time_init_ = $init_list['52720e003547c70561bf5e03b95aa99f'];
$lesson_time_init_ = array(
    'id' => 'e7b24b112a44fdd9ee93bdf998c6ca0e,52720e003547c70561bf5e03b95aa99f',
    'title' => '��ѵʱ��',
    'value' => empty($start_time) ? '' : $start_time . '-' . $end_time_init_['value'],
);
$init_list['e7b24b112a44fdd9ee93bdf998c6ca0e,52720e003547c70561bf5e03b95aa99f'] = array_merge($end_time_init_, $lesson_time_init_);
if (version_compare($version, '1.3', '>')) {
    // 1.3 �汾, ���� (��)��ѵ����
    unset($init_list['9fc3d7152ba9336a670e36d0ed79bc43']);
}
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
if (version_compare($version, '1.3', '>')) {  // �Զ���ѡ���
    $tmp_second_options = array();
    foreach ($second_options as $second) {
        $id = $second['id'];
        $tmp_second_options[$id] = $second['items'];
    }
    $merge_keymap = array(
        'beed13602b9b0e6ecb5b568ff5058f07-39461a19e9eddfb385ea76b26521ea48' => array(
            '18d8042386b79e2c279fd162df0205c8',
        ),
        '0584ce565c824b7b7f50282d9a19945b' => array(),
        'dc912a253d1e9ba40e2c597ed2376640-e46de7e1bcaaced9a54f1e9d0d2f800d' => array(
            '69cb3ea317a32c4e6143e665fdb20b14',
        ),
    );
    $second_options = array();
    foreach ($merge_keymap as $merge_key => $merge_val) {
        $keys = explode('-', $merge_key);
        $id = $keys[0];
        $match_id = $keys[1];
        $second_items = array();
        foreach ($tmp_second_options[$id] as $items) {
            $option_value = $items['option_value'];
            if ($option_value == $match_id && !empty($merge_val)) {
                $items_options = array();
                foreach ($merge_val as $val) {
                    $items_options = array_merge($items_options, $tmp_second_options[$val]);
                }
                $items['options'] = $items_options;
            }
            $second_items[] = $items;
        }
        $second_options[] = array(
            'id' => $id,
            'items' => $second_items,
        );
    }
}

$goods_init_info['options'] = array(
    'first' => $first_options_init,
    'second' => $second_options,
);
// ��Ʒ����
$contents = interface_content_to_ubb($goods_info['default_data']['content']['value']);
// �ϴ�����
$upload_config = interface_services_upload_config($images, $contents, $type_id);
$goods_init_info['upload'] = $upload_config;

$options['data'] = $goods_init_info;
return $cp->output($options);