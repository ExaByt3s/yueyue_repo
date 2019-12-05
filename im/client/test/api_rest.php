<?php

/**
 * 获取 api 数据返回
 * 
 * @param string  $path 路径 ( 例如: customer/buyer_index )
 * @param array  $param 参数 ( 例如: array( 'user_id' => '117452') )
 * @param boolean $decode 是否转为数组
 * @param boolean $to_gbk 转编码gbk
 * @param boolean $is_login 是否登陆
 * @return array| string
 */
if(!function_exists("get_api_result"))
{
    function get_api_result($path, $param, $decode = TRUE, $to_gbk = TRUE, $is_login = TRUE)
    {
        if (empty($path) || empty($param)) {
            return FALSE;
        }
        // 设置 location_id
        $param['location_id'] = empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'];
        $param['request_platform'] = 'web';
        if (strpos($path, 'customer/') === 0 || strpos($path, 'merchant/') === 0) {
            // 手机 api 接口
            $dir = '/disk/data/htdocs232/poco/pai/mobile_app/';   // APP接口路径
            $file = $dir . (strpos($path, '.php') ? $path : $path . '.php');
            if (!file_exists($file)) {
                return FALSE;
            }
            // 传递参数
            $post_data = array(
                'version' => '3.2.0',
                'os_type' => 'web',
                'ctime' => time(),
                'app_name' => 'poco_yuepai_web',
                'sign_code' => substr(md5('poco_' . json_encode($param) . '_app'), 5, -8),
                'is_enc' => 0,
                'param' => $param,
            );
            define('YUE_INVOCATION_PROTOCOL', FALSE);  // 不引入协议
            if ($is_login === TRUE) {
                define('YUE_INPUT_CHECK_TOKEN', FALSE);  // 不使用验证access_token
            }
            $json = require($file);
        } else {
            // 登陆,注册 使用 curl
            $path = trim(trim($path), '.');
            $base_url = (strpos($path, 'mall/') === 0) ? 'https://ypays.yueus.com' : 'http://yp.yueus.com/mobile_app';
            $url = $base_url . '/' . (strpos($path, '.php') ? $path : $path . '.php');
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return FALSE;
            }
            // 传递参数
            $post_data = array(
                'version' => '3.2.0',
                'os_type' => 'web',
                'ctime' => time(),
                'app_name' => 'poco_yuepai_android',
                'sign_code' => substr(md5('poco_' . json_encode($param) . '_app'), 5, -8),
                'is_enc' => 0,
                'param' => $param,
            );
            $cov_data = iconv('GBK', 'UTF-8', json_encode($post_data));  // 数据转UTF8编码
            $json = api_curl($url, $cov_data);  // 直接输出结果
        }
        $arr = json_decode($json, TRUE);
        if ($decode === TRUE) {
            $arr = convert_result($arr, $to_gbk);
            return $arr;
        } else {
            $arr = convert_result($arr, FALSE);
            $json = json_encode($arr);
            return $json;
        }
    }
}
/**
 * 转编码
 * 
 * @param array $arr  转义数组
 * @param boolean $to_gbk  是否转编码
 * @return array
 */
if(!function_exists("convert_result"))
{
    function convert_result($arr, $to_gbk = TRUE)
    {
        if (empty($arr)) {
            return array();
        }
        $newarr = array();
        foreach ($arr as $key => $value) {
            if (empty($value)) {
                $newarr[$key] = $value;
                continue;
            }
            if (is_array($value)) {
                $newarr[$key] = convert_result($value, $to_gbk);
                continue;
            }
            if (strpos($value, 'yueyue://') === 0) {
                if ($to_gbk === TRUE) {
                    $value = mb_convert_encoding($value, 'gbk', 'utf8');
                }
                $newarr[$key] = mall_yueyue_app_to_http($value);
                continue;
            }
            if ($to_gbk === TRUE) {
                $newarr[$key] = mb_convert_encoding($value, 'gbk', 'utf8');
            } else {
                $newarr[$key] = $value;
            }
        }
        return $newarr;
    }
}

/**
 * cURL 获取数据
 * 
 * @param string $url 链接
 * @param array $post_data POST数据
 * @return string 
 */
if(!function_exists("api_curl"))
{
    function api_curl($url, $post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
//    curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

/**
 * 转换app页面 to wap页面
 * 
 * @param string $url 链接
 * @return string
 */
if(!function_exists("mall_yueyue_app_to_http"))
{
    function mall_yueyue_app_to_http($url)
    {
        $html_array = include('page_url_config.inc.php');
        $url_arr = parse_url($url);

        if ($_COOKIE['yue_member_id'] == 100001) {
            //print_r($url);
        }

        if ($url_arr['scheme'] === 'yueyue') {
            $parse_arr = array();
            parse_str($url_arr['query'], $parse_arr);
            $pid = $parse_arr['pid'];
            $type = $parse_arr['type'];

            // ======== 针对参数过长的处理 ========
            // hudw 2015.9.6
            $temp_parse_arr = array();
            parse_str($url_arr['query'], $temp_parse_arr);
            unset($temp_parse_arr['user_id']);
            unset($temp_parse_arr['inner_app']);
            unset($temp_parse_arr['inner_web']);
            unset($temp_parse_arr['type']);
            if($_GET['npid'] != 1)
            {
                unset($temp_parse_arr['pid']);
            }
            
            $temp_parse_str = http_build_query($temp_parse_arr);

            // ======== 针对参数过长的处理 ========

            if ($type === 'inner_app') {

                // 针对外拍列表特殊处理！！！
                // hudw 2015.8.7
                if ($pid == 1220076 ) {
                    $pid = '0000001';
                }

                // 针对兼容商家列表的处理!!!
                // hudw 2015.9.23
                if ($pid == 1220101) {
                    $return_query = urldecode($temp_parse_arr['return_query']);
                    if (preg_match('/cms_type=mall/', $return_query)) {
                        //$pid = 1220147;
                    }
                }

                // 重新整合url
                $temp_url_arr = parse_url($html_array[$pid]);
                parse_str($temp_url_arr['query'], $temp_query_arr_v2);
                $real_html_url = $temp_url_arr['scheme'] . '://' . '' . $temp_url_arr['host'] . $temp_url_arr['path'];
                $real_query_str = $temp_url_arr['query'] ? '&' . $temp_url_arr['query'] : '';

                return $real_html_url . '?' . $temp_parse_str . $real_query_str;

            } elseif ($type === 'inner_web') {
                $temp_url = urldecode($parse_arr['url']);
                $temp_url_arr = parse_url($temp_url);

                if (preg_match('/www.yueus.com/', $_SERVER['SCRIPT_URI'])) {
                    $temp_url_arr['host'] = str_replace('yp.yueus.com', 'www.yueus.com', $temp_url_arr['host']);


                }

                if (preg_match('/test/', $_SERVER['SCRIPT_URL'])) {
                    $temp_url_arr['path'] = str_replace('user/', 'user/test/', $temp_url_arr['path']);
                }
                return $temp_url_arr['scheme'] . '://' . $temp_url_arr['host'] . $temp_url_arr['path'] . ($temp_url_arr['query'] ? '?' . $temp_url_arr['query'] : '');
            }
        }

        return $url;
    }
}
