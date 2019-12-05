<?php

/**
 * ��Ʒ ��ϸ����ҳ
 *
 * @since 2015-6-25
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
require_once(dirname(dirname(__FILE__)) . '/protocol_interface.func.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // �û�ID
$seller_user_id = $client_data['data']['param']['seller_user_id'];  // �̼�ID
$request_platform = $client_data['data']['param']['request_platform'];   // ����ƽ̨ ( for ����web 2015-7-29 )

$seller_obj = POCO::singleton('pai_mall_seller_class');
$user_result = $seller_obj->get_seller_info($seller_user_id, 2);  // ��ȡ�û���Ϣ
$introduce = $user_result['seller_data']['profile'][0]['introduce'];
if ($location_id == 'test' || $request_platform == 'web') {
    $options['data'] = array('introduce' => $introduce);
    return $cp->output($options);
}
$contents = interface_content_to_ubb($introduce);
$options['data'] = array('introduce' => $contents);
return $cp->output($options);