<?php

/**
 * �ղ� �̼�/��Ʒ ����
 *
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-8-26
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$target_id = $client_data['data']['param']['target_id'];  // Ŀ�� ID  ( �̼�ID/��ƷID )
$target_type = $client_data['data']['param']['target_type'];  // Ŀ������ ( seller/goods )
$operate = $client_data['data']['param']['operate'];  // �������� ( follow/unfollow )
if (empty($user_id)){
	$result = array(
        'result' => 0,
        'message' => '���ȵ�¼',
    );
    $options['data'] = $result;
    return $cp->output($options);
}
if (empty($target_id) || empty($target_type) || empty($operate)) {
    $result = array(
        'result' => 0,
        'message' => '����������',
    );
    $options['data'] = $result;
    return $cp->output($options);
}
$follow_obj = POCO::singleton('pai_mall_follow_user_class');
switch ($operate) {
    case 'follow':
        switch ($target_type) {
            case 'seller':
                $result = $follow_obj->add_user_follow($user_id, $target_id);
                break;
            case 'goods':
                $result = $follow_obj->add_goods_follow($user_id, $target_id);
                break;
            default:
                $result = array();
                break;
        }
        break;
    case 'unfollow':
        switch ($target_type) {
            case 'seller':
                $result = $follow_obj->cancel_user_follow($user_id, $target_id);
                break;
            case 'goods':
                $result = $follow_obj->cancel_goods_follow($user_id, $target_id);
                break;
            default:
                $result = array();
                break;
        }
        if ($result['result'] == 1) {
            $result['message'] = '��ȡ���ղ�';
        }
        break;
    default:
        $result = array();
        break;
}
if (isset($result['message'])) {
    $result['message'] = str_replace('��ע', '�ղ�', $result['message']);
}
$options['data'] = $result;
return $cp->output($options);
