<?php
/**
 * �̼ұ༭
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015/10/16
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // ��ȡ�û���Ϣ
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
$profile = $user_result['seller_data']['profile'][0];
if (empty($profile)) {
    $result = array('result' => 0, 'message' => 'û�и��̼�');
    $options['data'] = $result;
    return $cp->output($options);
}
$type_id = $profile['type_id'];  // �û���֤   3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ
$location_id = $profile['location_id'];  // ����ID
$city = get_poco_location_name_by_location_id($location_id);  // ����
// ͼ������
$contents = interface_content_to_ubb($profile['introduce']);
// ��֤����
$property = $attr_list = $bwh_arr = array();
$att_data = $profile['att_data'];
foreach ($att_data as $val) {
    $key = $val['key'];
    if (in_array($key, array('ms_forwarding', 'p_order_income', 'p_team',
        'm_level', 'm_sex', 'yp_place', 'yp_background', 'yp_can_photo', 'yp_lighter',
        'yp_other_equitment', 'hz_order_way', 'hz_place', 't_way',))) {
        // ����Ҫ������
        continue;
    }
    $value = empty($val['value']) ? '' : $val['value'];
    if (in_array($key, array('m_height', 'm_weight', 'm_cups', 'm_cup', 'm_bwh'))) {
        // ģ������
        if ($key == 'm_bwh') {  // ��Χ���⴦��
            list($m_bwh_chest, $m_bwh_waist, $m_bwh_hip) = explode('-', $value);
            $bwh_arr['m_bwh_chest'] = $m_bwh_chest;
            $bwh_arr['m_bwh_waist'] = $m_bwh_waist;
            $bwh_arr['m_bwh_hip'] = $m_bwh_hip;
            continue;
        }
        $bwh_arr[$key] = $value;
        continue;
    }
    $hint = '�༭';
    $att_type = '4';
    $choice_type = '0'; // ѡ��
    $choice_num = 0;
    $input_type = 2;
    if (in_array($key, array('m_cup', 'hz_othergoodat', 'ot_otherlabel', 'ev_other'))) {
        $input_type = 1;
    }
    $options = array();
    $child_data = $val['child_data'];
    if (!empty($child_data)) {
        $hint = '��ѡ��';
        $att_type = '2';
        $choice_num = 1;
        $input_type = 0;
        $choice_type = '1'; // ��ѡ
        if (in_array($key, array('ot_label', 'hz_goodat'))) {
            $att_type = '5';  //��ǩѡ��
            $choice_type = '2';
            $choice_num = 3;
        }
        foreach ($child_data as $child) {
            $options[] = array(
                'value' => $child,  // ��ʾ����
                'option_value' => $child,  // ��ڵ�ֵ
            );
        }
    }
    $type_k = substr($key, 0, strpos($key, '_'));
    $attr_list[$type_k][] = array(
        'type' => $att_type, //�ؼ����ͣ�1����ҳѡ�� 2������ѡ��3����ҳ���룻4����ǰ���룻5����ǩѡ��
        'id' => $key,  // key
        'title' => $val['name'],  // ��ʾ�ı���
        'value' => str_replace(',', '-', $value),  // ��ʾ��ֵ
        'option_value' => $value, // ����ֵ
        'choice_type' => $choice_type,  // ѡ���, 1��ѡ,2��ѡ
        'choice_num' => $choice_num, // ���ѡ�м���
        'input_type' => $input_type,  // ����1,����2
        'options' => $options,
        'hint' => $hint,
        'unit' => '',
    );
}
$type_list_ = interface_type_list();  // Ʒ���б�
// Ʒ��ͼ�� ( 3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ,41 ��ʳ,43 �������� )
$style_keymap_ = array(
    'm' => 31,
    'yp' => 12,
    't' => 5,
    'p' => 40,
    'hz' => 3,
    'ms' => 41,
    'ot' => 43,
    'ev' => 42,
);
$type_id_arr = explode(',', $type_id);  // ��֤����
foreach ($style_keymap_ as $k => $show_type_id) {
    $v = $type_list_[$show_type_id];
//    $v['title'] = $v['title'] . '�̼�';
    if (!in_array($show_type_id, $type_id_arr)) {
        continue;
    }
    $att_info = isset($attr_list[$k]) ? $attr_list[$k] : array();
    if ($k == 'm') { // ģ��ר������
        $v['item']['bwh'] = $bwh_arr;
    }
    $v['item']['description'] = $att_info;
    $property[] = $v;
}
$user_info = array(
    'user_id' => $profile['user_id'], // �û�ID
    'profile_id' => $profile['seller_profile_id'], // ����ID
    'type_id' => $type_id,
    'common' => array(
        'name' => array(
            'type' => '4',
            'id' => 'name',  // key
            'title' => '�ǳ�',  // ��ʾ����
            'value' => $profile['name'],  // ��ʾ��ֵ
            'option_value' => '', // ����ֵ
            'input_type' => '1',  // ����1,����2
            'hint' => '�������ǳ�',
            'unit' => '',
        ),
        'location_id' => array(
            'type' => '2',
            'id' => 'location_id',
            'title' => '����',
            'value' => $city,
            'option_value' => $location_id, // ����ֵ
            'input_type' => '2',
            'hint' => '',
            'unit' => '',
        ),
        'introduce' => array(
            'type' => '3',  // �ؼ����ͣ�1����ҳѡ�� 2������ѡ��3����ҳ���룻4����ǰ����
            'id' => 'introduce',
            'title' => '���˽���',
            'value' => '',
            'option_value' => '', // ����ֵ
            'input_type' => '2',
            'hint' => '�༭����',
            'unit' => '',
        ),
    ),
    // ����
    'property' => $property,
);

// �ϴ�����
$upload_config = array(
    'avatar' => get_seller_user_icon($user_id, 165, TRUE), // $profile['avatar'], // ͷ��
    'post_icon' => 'http://sendmedia-w.yueus.com:8078/icon.cgi',
    'post_icon_wifi' => 'http://sendmedia-w-wifi.yueus.com:8078/icon.cgi',
    'icon_size' => 640,
    'cover' => yueyue_resize_act_img_url($profile['cover'], '640'), // ����ͼ
    'post_cover' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_cover_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
    'cover_size' => 640,
    'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_pic_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
    'pic_size' => 640,
    'pic_num' => 15,
    'contents' => $contents,
);

$options['data'] = array_merge($user_info, $upload_config);
return $cp->output($options);