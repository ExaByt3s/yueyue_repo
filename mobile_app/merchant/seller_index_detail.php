<?php

/**
 * ��Ʒ ��ϸ����ҳ
 *
 * @since 2015-6-25
 * @author chenweibiao <chenwb@yueus.com>
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($user_id, 2);  // ��ȡ�û���Ϣ
$introduce = $user_result['seller_data']['profile'][0]['introduce'];
if ($location_id == 'test') {
    $options['data'] = $introduce;
    return $cp->output($options);
}
$contents = interface_content_replace_pics($introduce, 640);  // �滻ͼƬ��С
$contents = interface_content_to_ubb($contents); // תubb
$options['data'] = array('introduce' => $contents);
return $cp->output($options);
