<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$access_token   = $client_data['data']['param']['access_token'];
$to_user_id     = $client_data['data']['param']['to_user_id'];

$data['is_pass'] = 'yes';
$options['data'] = $data;

$cp->output($options);
exit();

$data['is_pass'] = 'yes';

$pai_obj    = POCO::singleton ( 'pai_user_class' );
$date_obj   = POCO::singleton ( 'event_date_class' );
if($pai_obj->check_role($user_id) == 'cameraman' && $pai_obj->check_role($to_user_id) == 'model')
{
    $payment_obj = POCO::singleton ( 'pai_model_card_class' );
    if(!$payment_obj->check_cameraman_level_require($user_id, $to_user_id))
    {
        $check_date = $date_obj->check_cameraman_is_date ( $user_id, $to_user_id );
        if(!$check_date)
        {
            $data['is_pass'] = 'no';
            $model_level_require = $payment_obj->get_model_level_require($to_user_id);
            $data['tips'] = "这位模特需要达到V{$model_level_require}认证级别才能约拍，赶紧来升级你的信用等级吧。";
        }

    }
}


/**
   $payment_obj = POCO::singleton ( 'pai_model_card_class' );
   if($payment_obj->check_cameraman_level_require($user_id, $to_user_id))
   {
        $data['is_pass'] = 'yes';
   }

   if($payment_obj->check_cameraman_level_require($to_user_id, $user_id))
   {
        $data['is_pass'] = 'yes';
   }
**/

//print_r($data_array);
$options['data'] = $data;

$cp->output($options);
?>