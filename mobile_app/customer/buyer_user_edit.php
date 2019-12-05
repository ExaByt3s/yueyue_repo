<?php

/**
 * �༭ �û���Ϣ ( �ύ )
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-7-16
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];  // �û�ID
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$post_data = $client_data['data']['param']['post_json_data'];
if (empty($user_id) || empty($post_data)) {
    $result = array('result' => -1, 'message' => 'û�и�������');
    $options['data'] = $result;
    $cp->output($options);
    exit;
}
$nickname = $post_data['nickname'];  // �û���
$location = $post_data['location_id'];
$intro = $post_data['intro'];  // ���
$is_display_record = $post_data['is_display_record'];  // �Ƿ���ʾ��¼

if ($nickname) {
    $update_data['nickname'] = $nickname;
}
if ($location) {
    $update_data['location_id'] = $location;
}
if ($intro) {
    $update_data['remark'] = $intro;
}
$update_data['is_display_record'] = intval($is_display_record); // �Ƿ���ʾ��¼
if (!empty($post_data['showcase'])) {  // ͼ��
    $update_data['pic_arr'] = $post_data['showcase'];
}
if ($location_id == 'test') {
    $options['data'] = array(
        'post_json_data' => $post_data,
        'update_data' => $update_data,
    );
    return $cp->output($options);
}
$obj = POCO::singleton('pai_user_class');
$res = $obj->update_mall_user_info($update_data, $user_id);
$result = array('result' => 0, 'message' => '����ʧ��');
if ($res) {
    $result = array('result' => 1, 'message' => '���³ɹ�', 'res' => $res);
}
$options['data'] = $result;
return $cp->output($options);
