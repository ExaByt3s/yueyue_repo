<?php

/**
 * �༭ �û���Ϣ ( ��ʼ�� )
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-7-16
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID

$seller_obj = POCO::singleton('pai_user_class');
$user_result = $seller_obj->get_user_info($user_id);  // ��ȡ�û���Ϣ
if ($location_id == 'test') {
    $options['data'] = $user_result;
    return $cp->output($options);
}
$user_info = array(
    'user_id' => $user_result['user_id'], // �û���
    'avatar' => get_user_icon($user_id, 165, TRUE), // ͷ��
    'nickname' => $user_result['nickname'], // �û���
    'location_id' => $user_result['location_id'],
    'intro' => $user_result['remark'], // ���
    'is_display_record' => $user_result['is_display_record'], // �Ƿ���ʾ��¼
);
// ��ȡ ͼ��
$pic_obj = POCO::singleton('pai_pic_class');
$pic_list = $pic_obj->get_user_pic($user_id);
//$pic_list = $pic_obj->get_user_pic($user_id);
foreach ($pic_list as $value) {
    $img_url = $value['img'];
    $showcase = array(
        'thumb' => yueyue_resize_act_img_url($img_url, '440'), // ����ͼ
        'original' => yueyue_resize_act_img_url($img_url), // ԭͼ
    );
    $user_info['showcase'][] = $showcase;
}
// �ϴ�����
$upload_config = array(
    'post_icon' => 'http://sendmedia-w.yueus.com:8078/icon.cgi',
    'post_icon_wifi' => 'http://sendmedia-w-wifi.yueus.com:8078/icon.cgi',
    'icon_size' => '640',
    'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_pic_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
    'pic_size' => '640',
    'pic_num' => '15',
);


$options['data'] = array_merge($user_info, $upload_config);
return $cp->output($options);
