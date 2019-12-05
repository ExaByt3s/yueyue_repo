<?php

/**
 * 二维码 接口
 * 
 * @author heyaohua
 * @since 2015-7-2
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once("../../protocol_common.inc.php");

//上线需要删除
define('G_PAI_ECPAY_DEV', 1);

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input();

$user_id = $client_data['data']['param']['user_id'];
$code_data = urldecode($client_data['data']['param']['code_data']);

$jump_url = code_to_yueseller($code_data, $user_id);

if($jump_url['result'])
{
    if($jump_url['result'] == 1)
    {
        $options['data']['code'] = '1';
        $options['data']['msg'] = '正常';
        $options['data']['url'] = 'yueseller://goto?type=inner_app&pid=1250022&order_sn=' . $jump_url['order_sn'];;
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

/**
 * 生成 二维码
 * 
 * @param string $url 链接 URL
 * @return array 
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
            default:
                $jump_type = 'outside_web';
        }
        if ($jump_type == 'inner_web' && $url_array['path'] != '/mobile/app') {
            $showtitle = '&showtitle=1';
        }
        //print_r($url_array);
        if ($url_array['path'] == '/mobile/action/check_qrcode.php') {
            //echo "进入";
            $qrcode_obj = POCO::singleton('pai_activity_code_class');
            $return_array = $qrcode_obj->verify_qrcode_url($user_id, $url);
            return $return_array;
        }
        $return_url = 'yueseller://goto?type=' . $jump_type . '&url=' . urlencode($url) . '&wifi_url=' . urlencode($url) . $showtitle;

    }
    return $return_url;
}
