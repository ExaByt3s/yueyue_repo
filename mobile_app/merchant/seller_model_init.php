<?php

/**
 * ģ���̼ұ༭(��ʼ��)�ӿ�
 * 
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-8-25
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // ��ȡ�û���Ϣ
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
//$user = $user_result['seller_data'];
$profile = $user_result['seller_data']['profile'][0];
$type_id = $profile['type_id'];  // �û���֤   3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ
if (strpos($type_id, '31') === FALSE) {
    $options['data'] = array('result' => -1, 'type_id' => $type_id, 'message' => 'û��ģ����֤');
    return $cp->output($options);
}
$profile_info = array();  // ���
foreach ($profile['default_data'] as $value) {
    $profile_info[$value['key']] = $value['value'];
}
// 3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ
$attr_arr = array(
    'm_level' => array(
        1 => 'V1�ֻ���֤',
        2 => 'V2ʵ����֤',
        3 => 'V3������֤',
    ),
    'm_experience' => array(
        '��ģ��',
        '1��',
        '2��',
        '3��',
        '3������'
    ),
    'm_height' => 'CM',
    'm_weight' => 'KG',
);
$attr_list = $bwh_arr = $attr_col = $attr_child = array();
$att_data = $profile['att_data'];
foreach ($att_data as $val) {
    $value = $val['value'];
    if ($value == '' || $value == NULL) {
        // ȥ�� һЩ��ֵ
        continue;
    }
    $vkey = $val['key'];
    if (isset($val['child_data']) && !empty($val['child_data'])) {
        $attr_child[$vkey] = $val['child_data'];
    }
    $attr_col[$vkey] = $value;
    if (in_array($vkey, array('m_height', 'm_weight', 'm_cups', 'm_cup', 'm_bwh'))) {
        $unit = isset($attr_arr[$vkey]) ? $attr_arr[$vkey] : '';
        $bwh_arr[$vkey] = $value . $unit;
        continue;
    }
    $type[] = array();
    list($key, $one) = explode('_', $vkey);
    $name = $val['name'];
    $attr_list[$key][] = array(
        'type' => 'label',
        'title' => $name,
        'value' => isset($attr_arr[$vkey]) ? $attr_arr[$vkey][$value] : $value,
    );
}
if ($location_id == 'test1') {
    $options['data'] = array(
        'attr_list' => $attr_list,
        'bwh_arr' => $bwh_arr,
        'attr_col' => $attr_col,
    );
    $cp->output($options);
    exit;
}
list($chest, $waist, $hip) = explode('-', $attr_col['m_bwh']);
$user_info = array(
    'result' => 1, // �ɹ�
    'user_id' => $profile['user_id'], // �û�ID
    'profile_id' => $profile['seller_profile_id'], // ����ID
    'cover' => yueyue_resize_act_img_url($profile['cover'], '640'), // ����ͼ
    'avatar' => get_seller_user_icon($user_id, 165, TRUE), // $profile['avatar'], // ͷ��
    'name' => $profile['name'],
    'location_id' => $profile_info['location_id'],
    'location' => get_poco_location_name_by_location_id($profile_info['location_id']),
    // ����
    'height' => strval(intval($attr_col['m_height'])), // ���
    'weight' => strval(intval($attr_col['m_weight'])), // ����
    'cup' => empty($attr_col['m_cup']) ? 'A' : $attr_col['m_cup'], // �ֱ�
    'cup_size' => strval(intval($attr_col['m_cups'])), // �ֱ���С (����)
//    'bwh' => $attr_col['m_bwh'],
    'chest' => strval(intval($chest)), // ��Χ
    'waist' => strval(intval($waist)), // ��Χ
    'hip' => strval(intval($hip)), // ��Χ
    'level_require' => intval($attr_col['m_level']) < 1 ? 1 : intval($attr_col['m_level']), // �ȼ�Ҫ��
    'level_options' => array(),
    'experience' => empty($attr_col['m_experience']) ? '��ģ��' : $attr_col['m_experience'], // ģ�ؾ���
    'experience_options' => empty($attr_child['m_experience']) ? $attr_arr['m_experience'] : $attr_child['m_experience'],
);
foreach ($attr_arr['m_level'] as $key => $value) {
    $user_info['level_options'][] = array(
        'name' => $value,
        'value' => $key,
        'desc' => $value,
    );
}

// �ϴ�����
$upload_config = array(
    'post_icon' => 'http://sendmedia-w.yueus.com:8078/icon.cgi',
    'post_icon_wifi' => 'http://sendmedia-w-wifi.yueus.com:8078/icon.cgi',
    'icon_size' => 640,
    'post_cover' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_cover_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
    'cover_size' => 640,
    'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_pic_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
    'pic_size' => 640,
    'pic_num' => 15,
);

$options['data'] = array_merge($user_info, $upload_config);
return $cp->output($options);
