<?php

/**
 * 接口测试 - 陈伟标
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-21
 */
include 'test/api_rest.php';
//$path = 'customer/buyer_user_edit.php';
////$path = 'event/get_hot_ad.php';
////$path = 'customer/buyer_user.php';
//$param = array(
//    'user_id' => '117452',
//    '_debug' => 'test',
//);
$path = filter_input(INPUT_POST, 'path');   // 接口路径
$param['location_id'] = empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'];
$param['request_platform'] = 'web';
foreach ($_POST as $key => $value) {
    if ($key == 'path') {
        continue;
    }
    $param[$key] = $value;   // 组装参数
}
$result = get_api_result($path, $param, FALSE, TRUE, TRUE);
echo $result;
exit;

header('Content-type: application/json; charset=utf-8');   // 定义文件格式
$dir = str_replace('\\', '/', dirname(dirname(dirname(__FILE__)))) . '/mobile_app/';   // 接口基础路径

$path = filter_input(INPUT_POST, 'path');   // 接口路径
if (empty($path)) {
    $raw_data_str = $HTTP_RAW_POST_DATA;   // 支持 raw 数据
    if (empty($raw_data_str)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 401 unauthorized');
        exit();
    }
    $raw_data_arr = json_decode($raw_data_str, TRUE);
    if (empty($raw_data_arr)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 401 unauthorized');
        exit();
    }
    $path = $raw_data_arr['path'];
    if (empty($path)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 401 unauthorized');
        exit();
    }
    $param = $raw_data_arr;
}
$path = trim(trim($path), '.');
$base_url = (strpos($path, 'mall/') === 0) ? 'https://ypays.yueus.com' : 'http://yp.yueus.com/mobile_app';
$url = $base_url . '/' . (strpos($path, '.php') ? $path : $path . '.php');
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 410 gone');
    exit();
}
// 设置 location_id
$param['location_id'] = empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'];
$param['request_platform'] = 'web';
foreach ($_POST as $key => $value) {
    if ($key == 'path') {
        continue;
    }
    $param[$key] = $value;   // 组装参数
}
// 传递参数
$post_data = array(
    'version' => '1.0.0',
    'os_type' => 'web',
    'ctime' => time(),
    'app_name' => 'poco_yuepai_android',
    'sign_code' => substr(md5('poco_' . json_encode($param) . '_app'), 5, -8),
    'is_enc' => 0,
    'param' => $param,
);
$cov_data = iconv('GBK', 'UTF-8', json_encode($post_data));  // 数据转UTF8编码
header('Content-type: application/json; charset=utf-8');   // 定义文件格式
echo test_api_curl($url, $cov_data);  // 直接输出结果

/**
 * cURL 获取数据
 * 
 * @param string $url 链接
 * @param array $post_data POST数据
 * @return string 
 */
function test_api_curl($url, $post_data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

exit;
