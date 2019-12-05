<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$code_data      = urldecode($client_data['data']['param']['code_data']);


$log_arr['user_id'] = $user_id;
$log_arr['code_data'] = $code_data;
pai_log_class::add_log($log_arr, 'code', 'code');

$code_data = html_entity_decode($code_data);
$jump_url = code_to_yueyue($code_data);

if($jump_url)
{
    $options['data']['code'] = '1';
    $options['data']['msg'] = '正常';
    $options['data']['url']  = $jump_url;
}else{
    $options['data']['code'] = '0';
    $options['data']['msg'] = '非法URL';
}

$cp->output($options);

function code_to_yueyue($url)
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

        if(strpos($url_array['path'], 'check_qrcode.php'))
        {
            $url = str_replace('check_qrcode.php', 'check_qrcode_v2.php', $url);
            unset($showtitle);
        }


        $return_url = 'yueyue://goto?type=' . $jump_type . '&url=' . urlencode($url) . '&wifi_url='  . urlencode($url) . $showtitle;


        if($jump_type == 'outside_web' && strpos($url_array['path'], 'share_card'))
        {
            $jump_type = 'inner_app';
            $data_array = explode('/', $url_array['path']);
            $return_url = 'yueyue://goto?type=' . $jump_type . '&pid=1220025&user_id=' . $data_array[2];
            return $return_url;
        }

        if($jump_type == 'outside_web' && strpos($url_array['path'], 'cameraman'))
        {
            $jump_type = 'inner_app';
            $data_array = explode('/', $url_array['path']);
            $return_url = 'yueyue://goto?type=' . $jump_type . '&pid=1220025&user_id=' . $data_array[2];
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
    }

    return $return_url;
}
?>