<?php
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$code_data      = urldecode($client_data['data']['param']['code_data']);

$jump_url = code_to_yueyue($code_data, $user_id);

$options['data']['code'] = '0';
$options['data']['msg'] = $jump_url['message'];

if(is_array($jump_url))
{

    if($jump_url['result'] == 1)
    {
        if($jump_url['type'] == 'event')
        {
            $options['data']['code']    = '1';
            $options['data']['msg']     = '正常';
            $url                        = 'http://yp.yueus.com/mall/user/act/sign.php?event_id=' . $jump_url['event_id'];
            $wifi_url                   = 'http://yp-wifi.yueus.com/mall/user/act/sign.php?event_id=' . $jump_url['event_id'];
            $options['data']['url']     = 'yueyue://goto?type=inner_web&url=' . urlencode($url) . '&wifi_url=' . urlencode($wifi_url)  . '&showtitle=2';
        }

    }else{
        $options['data']['code'] = '0';
        $options['data']['msg'] = $jump_url['message'];
    }
}else{
    if ($jump_url) {
        $options['data']['code'] = '1';
        $options['data']['msg'] = '正常';
        $options['data']['url'] = $jump_url;
    } else {
        $options['data']['code'] = '0';
        $options['data']['msg'] = '非法URL';
    }
}

$cp->output($options);

function code_to_yueyue($url, $user_id)
{
    $url_array = parse_url($url);
    $return_url = '';

    if($url_array['scheme'] == 'http' || $url_array['scheme'] == 'https')
    {
        switch($url_array['host'])
        {
            case 'yp.yueus.com':
            case 'yp-wifi.yueus.com':
                $jump_type = 'inner_web';
                break;

            default:
                $jump_type = 'outside_web';
        }

        if($jump_type == 'inner_web' && $url_array['path'] != '/mobile/app') $showtitle = '&showtitle=1';


        if($url_array['path'] == '/mobile/action/check_qrcode.php')
        {
            //if($user_id == 100008) var_dump($url_array);

            $log_arr['$user_id']    = $user_id;
            $log_arr['url_log']     = $url_array;

            $qrcode_obj = POCO::singleton('pai_activity_code_class');
            $return_array = $qrcode_obj->verify_qrcode_url($user_id, $url, 'customer');
            return $return_array;
        }


        $return_url = 'yueyue://goto?type=' . $jump_type . '&url=' . urlencode($url) . '&wifi_url='  . urlencode($url) . $showtitle;


        if($jump_type == 'outside_web' && strpos($url_array['path'], 'share_card'))
        {
            $jump_type = 'inner_app';
            $data_array = explode('/', $url_array['path']);
            $return_url = 'yueyue://goto?type=' . $jump_type . '&pid=1220103&seller_user_id=' . $data_array[2];
            return $return_url;
        }

        if($jump_type == 'outside_web' && strpos($url_array['path'], 'cameraman'))
        {
            $jump_type = 'inner_app';
            $data_array = explode('/', $url_array['path']);
            $return_url = 'yueyue://goto?type=' . $jump_type . '&pid=1220123&user_id=' . $data_array[2];
            return $return_url;
        }

        if($jump_type == 'outside_web' && strpos($url_array['path'], 'mall'))
        {
            $jump_type = 'inner_app';
            $data_array = explode('/', $url_array['path']);
            $return_url = 'yueyue://goto?type=' . $jump_type . '&pid=1220103&seller_user_id=' . $data_array[2];
            return $return_url;
        }

        if($jump_type == 'outside_web' && strpos($url_array['path'], 'goods'))
        {
            $jump_type = 'inner_app';
            $data_array = explode('/', $url_array['path']);
            $return_url = 'yueyue://goto?type=' . $jump_type . '&pid=1220102&goods_id=' . $data_array[2];
            return $return_url;
        }

        if($jump_type == 'outside_web' && strpos($url_array['path'], 'event'))
        {
            $jump_type = 'inner_web';
            $data_array = explode('/', $url_array['path']);
            $jump_url = urlencode('http://yp.yueus.com/mall/user/act/detail.php?event_id='. $data_array[2]);
            $jump_url_wifi  = urlencode('http://yp-wifi.yueus.com/mall/user/act/detail.php?event_id='. $data_array[2]);
            //echo $jump_url;
            $return_url = 'yueyue://goto?type=' . $jump_type . '&url=' . $jump_url . '&wifi_url=' . $jump_url_wifi . '&showtitle=2';
            return $return_url;
        }


    }

    return $return_url;
}
?>