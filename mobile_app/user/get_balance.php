<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];

//$data['nickname']       = get_user_nickname_by_user_id($user_id);
//$data['icon']           = get_user_icon($user_id, $size = 86);

$payment_obj = POCO::singleton ( 'pai_payment_class' );
$account_info = $payment_obj->get_user_account_info ( $user_id );
$data['balance'] = $account_info ['available_balance']; //╟о░№╙р╢ю

if(version_compare($client_data['data']['version'], '1.0.6', '>=')) {
    $data['balance'] = 'гд' . $account_info ['available_balance']; //╟о░№╙р╢ю
}

$options['data'] = $data;

$cp->output($options);
?>