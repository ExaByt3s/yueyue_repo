<?php

/**
 * 二维码 接口
 *
 * @author heyaohua
 * @since 2015-7-2
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
$user_id = $client_data['data']['param']['user_id'];
$code_data = urldecode($client_data['data']['param']['code_data']);

$code_data = str_replace('&amp;', '&', $code_data);
$jump_url = code_to_yueseller($code_data, $user_id);


pai_log_class::add_log($code_data, 'code_data', 'code_processing');
pai_log_class::add_log($jump_url, 'jump_url', 'code_processing');
//pai_log_class::add_log($jump_url, 'code_processing', 'code_processing');


if ($jump_url['order_type']=='activity') {
    $redirect_url = 'yueseller://goto?type=inner_app&pid=1250043&trade_type=completed&activity_id=' . $jump_url['activity_id'] .
        '&stage_id=' . $jump_url['stage_id'] . '&order_sn=' . $jump_url['order_sn'];
}
else
{
    $redirect_url = 'yueseller://goto?type=inner_app&pid=1250022&order_sn=' . $jump_url['order_sn'];
}
if (is_array($jump_url)) {
    if ($jump_url['result'] == 1) {
        if ($jump_url['event']) {
            $options['data']['code'] = '0';
            $options['data']['msg'] = '此签到码无效，请重新操作';
        } else {
            $options['data']['code'] = '1';
            $options['data']['msg'] = '正常';
            $options['data']['url'] = $redirect_url; // 'yueseller://goto?type=inner_app&pid=1250022&order_sn=' . $jump_url['order_sn'];
        }
    } else {
        $options['data']['code'] = '0';
        $options['data']['msg'] = $jump_url['message'];
        //$options['data']['msg'] = '错误';
    }
} else {
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

/**
 * 生成 二维码
 *
 * @param string $url 链接 URL
 * @param int $user_id 用户ID
 * @return array|string
 */
function code_to_yueseller($url, $user_id) {
    $url_array = parse_url($url);
    $return_url = '';
    if ($url_array['scheme'] == 'http' || $url_array['scheme'] == 'https') {
        switch ($url_array['host']) {
            case 'yp.yueus.com':
            case 'yp-wifi.yueus.com':
                $jump_type = 'inner_web';
                break;
            case 'www.yueus.com':
                $jump_type = 'special';
                break;
            default:
                $jump_type = 'outside_web';
        }
        if ($jump_type == 'inner_web' && $url_array['path'] != '/mobile/app') {
            $showtitle = '&showtitle=1';
        }
        if ($url_array['path'] == '/mobile/action/check_qrcode.php') {
            //echo "进入";
            $qrcode_obj = POCO::singleton('pai_activity_code_class');
            $return_array = $qrcode_obj->verify_qrcode_url($user_id, $url, 'merchant');
            return $return_array;
        }
        //二维码解析
        if ($jump_type == 'special') {
            return '';
            $path_arr = explode("/", $url_array['path']);
            if ($path_arr[1] == 'mall') {
                $user_id = (int)$path_arr[2];
                if ($user_id) {
                    $return_url = 'yueseller://goto?type=inner_app&pid=1250004&user_id=' . $user_id;
                }
            }
            if ($path_arr[1] == 'goods') {
                $goods_id = (int)$path_arr[2];
                if ($goods_id) {
                    $return_url = 'yueseller://goto?type=inner_app&pid=1250007&goods_id=' . $goods_id;
                }
            }
        }
        $return_url = 'yueseller://goto?type=' . $jump_type . '&url=' . urlencode($url) . '&wifi_url=' . urlencode($url);
    }
    return $return_url;
}
