<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];

$data['mid'] = '122LB08002';
$data['nickname']       = get_user_nickname_by_user_id($user_id);
$data['icon']           = get_user_icon($user_id, 86, TRUE);
$data['user_id']        = $user_id;

$obj = POCO::singleton('pai_user_class');
//   $info = $obj->get_user_info($user_id);
$info =  $obj->get_user_info_by_user_id($user_id);

$data['role']           = $obj->check_role($user_id);
$data['location_id']    = $info['location_id'];
$data['city_name']      = $info['city_name'];
$data['user_level']     = $info['user_level'];

$data['yuepai_url']     = 'http://yp.yueus.com/mobile/app?from_app=1&role=' . $data['role'] . '#mine/consider';
$data['code_url']       = 'http://yp.yueus.com/mobile/app?from_app=1#act/security';
//$data['credit_url']     = 'http://yp.yueus.com/mobile/app?from_app=1#mine/authentication_list';
$data['setup_url']      = 'http://yp.yueus.com/mobile/app?from_app=1#account/setup';
$data['bill_url']       = 'http://yp.yueus.com/mobile/app?from_app=1#mine/money/bill';

$data['yuepai_url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider';
$data['code_url_wifi']       = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#act/security';
//$data['credit_url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/authentication_list';
$data['setup_url_wifi']      = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#account/setup';
$data['bill_url_wifi']       = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/money/bill';

if($data['role'] == 'cameraman')
{
    $data['credit_url']         = 'http://yp.yueus.com/mobile/app?from_app=1#mine/authentication_list';
    $data['credit_url_wifi']    = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/authentication_list';
    $data['home_url']           = 'http://yp.yueus.com/mobile/app?from_app=1#zone/' . $user_id . '/cameraman';
    $data['home_url_wifi']      = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/' . $user_id . '/cameraman';
    $data['recharge_url']       = 'yueyue://goto?type=inner_web&url=http%3A%2F%2Fyp.yueus.com%2Fmobile%2Fm2%2Frecharge%2Findex.php&wifi_url=http%3A%2F%2Fyp.yueus.com%2Fmobile%2Fm2%2Frecharge%2Findex.php&showtitle=1';
    //if(version_compare($client_data['data']['version'], '2.1.20', '>'))  $data['recharge_url']       = 'yueyue://goto?type=inner_web&url=http%3A%2F%2Fyp.yueus.com%2Fmobile%2Fm2predev%2Frecharge%2Findex.php&wifi_url=http%3A%2F%2Fyp.yueus.com%2Fmobile%2Fm2predev%2Frecharge%2Findex.php&showtitle=1';
    $data['credit_url']       = "yueyue://goto?type=inner_web&url=" . urlencode($data['credit_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['credit_url']));

    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) unset($data['recharge_url']);
    //$data['home_url']         = "yueyue://goto?type=inner_app&pid=1220026&mid=122RO02001&user_id=" . $user_id;

    unset($data['credit_url_wifi']);
    //unset($data['home_url_wifi']);


    if(version_compare($client_data['data']['version'], '2.1.0', '>=')) {
        $cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );
        $share_text = $cameraman_card_obj->get_share_text($user_id);
        $data['share']  = $share_text;
    }
}else{
    $data['mywork_url']         = '';
    $data['mywork_url_wifi']    = '';
    $data['honor_url']          = '';
    $data['honor_url_wifi']     = '';
    $data['home_url']           = 'http://yp.yueus.com/mobile/app?from_app=1#model_date/model_card/edit_all';
    $data['home_url_wifi']      = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_date/model_card/edit_all';

    if(version_compare($client_data['data']['version'], '1.0.6', '>=')) {

        //$data['home_url']       = "yueyue://goto?type=inner_web&url=" . urlencode($data['home_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['home_url']));

        //unset($data['home_url_wifi']);
    }

    $model_card_obj = POCO::singleton('pai_model_card_class');
    $model_card_info = $model_card_obj->get_share_text($user_id);

    $data['share']  = $model_card_info;

}

$data['pw_url']             = 'http://yp.yueus.com/mobile/app?from_app=1#account/setup/bind/enter_pw/form_setup';
$data['pw_url_wifi']        = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#account/setup/bind/enter_pw/form_setup';

$data['alipay_url']         = 'http://yp.yueus.com/mobile/app?from_app=1#mine/money/bind_alipay';
$data['alipay_url_wifi']    = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/money/bind_alipay';

if(version_compare($client_data['data']['version'], '2.1.20', '>'))
{
    $data['alipay_url']         = 'http://yp.yueus.com/mobile/m2/mine/bind_alipay/';
    $data['alipay_url_wifi']    = 'http://yp-wifi.yueus.com/mobile/m2/mine/bind_alipay/';
}



$data['event_url']          = 'http://yp.yueus.com/mobile/app?from_app=1#mine/status';
$data['event_url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/status';

if(version_compare($client_data['data']['version'], '2.1.20', '>'))
{
    $data['bill_url']         = 'http://yp.yueus.com/mobile/m2/mine/bill/?type=trade';
    $data['bill_url_wifi']    = 'http://yp-wifi.yueus.com/mobile/m2/mine/bill/?type=trade';
}


    $data['yuepai_url'] = "yueyue://goto?type=inner_web&url=" . urlencode($data['yuepai_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['yuepai_url']));
    $data['code_url']   = "yueyue://goto?type=inner_web&url=" . urlencode($data['code_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['code_url']));
    $data['setup_url']  = "yueyue://goto?type=inner_web&url=" . urlencode($data['setup_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['setup_url']));
    $data['bill_url']   = "yueyue://goto?type=inner_web&url=" . urlencode($data['bill_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['bill_url']));


if(version_compare($client_data['data']['version'], '2.1.20', '>'))
{
    $data['bill_url'] .= '&showtitle=2';
}



    unset($data['yuepai_url_wifi']);
    unset($data['code_url_wifi']);
    unset($data['setup_url_wifi']);
    unset($data['bill_url_wifi']);

    $data['pw_url']     = "yueyue://goto?type=inner_web&url=" . urlencode($data['pw_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['pw_url']));
    $data['alipay_url']= "yueyue://goto?type=inner_web&url=" . urlencode($data['alipay_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['alipay_url']));
    $data['event_url'] = "yueyue://goto?type=inner_web&url=" . urlencode($data['event_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $data['event_url']));

    if(version_compare($client_data['data']['version'], '2.1.20', '>'))
    {
        $data['alipay_url'] .= '&showtitle=2';
    }

    unset($data['pw_url_wifi']);
    unset($data['alipay_url_wifi']);
    unset($data['event_url_wifi']);

    $waipai_num = count_waipai_order_num($user_id);
    if($waipai_num)  $data['waipai_num'] = (string)$waipai_num;

    $yuepai_num = count_yuepai_order_num($user_id);
    if($yuepai_num) $data['yuepai_num'] = (string)$yuepai_num;

    $code_num = count_act_ticket($user_id);
    if($code_num) $data['code_num'] = (string)$code_num;

    $coupon_url = 'http://yp.yueus.com/mobile/app?from_app=1#coupon/list/available';
    $data['coupon_url'] = "yueyue://goto?type=inner_web&url=" . urlencode($coupon_url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $coupon_url));;
    $coupon_obj = POCO::singleton('pai_coupon_class');
    $coupon_num = $coupon_obj->get_user_coupon_list_by_tab('available', $user_id, true);
    if($coupon_num) $data['coupon_num'] = (string)$coupon_num;

    $data['user_id']        = $user_id;

    $task_request_obj = POCO::singleton('pai_task_request_class');


if(version_compare($client_data['data']['version'], '2.1.20', '>')) {
    if($task_request_obj->get_request_is_have($user_id))
    {
        $data['tt_url'] = "yueyue://goto?type=inner_app&pid=1220079";
    }else{
        $data['tt_url'] = "yueyue://goto?type=inner_app&pid=1220080";
    }
}

if(version_compare($client_data['data']['version'], '2.1.20', '>')) {
    $data['code_url'] = "yueyue://goto?type=inner_app&pid=1220092";
}

$options['data'] = $data;

$cp->output($options);
?>