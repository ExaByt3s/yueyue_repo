<?php

/**
 * 接口测试 - ljl
 * 
 * @author ljl <chenwb@yueus.com>
 * @since 2015-6-17
 */
// 榜单信息
//include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
//$cms_obj = new cms_system_class();
//$key = 196;
//$list_info = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number DESC', $key);
//var_dump($list_info);
//exit;

$path = filter_input(INPUT_POST, 'path');   // 文件路径
if (empty($path)) {
    header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 401 unauthorized');
    exit();
}
$dir = str_replace('\\', '/', dirname(__FILE__));  // 当前文件夹路径
$file = $dir . '/' . (strpos($path, '.php') ? $path : $path . '.php');
if (!file_exists($file)) {
    header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 404 not found');
    exit();
}
foreach ($_POST as $key => $value) {
    if ($key == 'path') {
        continue;
    }
    $param[$key] = $value;   // 组装参数
}

$url = str_replace($dir, 'http://yp.yueus.com/mobile_app', $file);  // 拼装 url
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 410 gone');
    exit();
}
isset($param['user_id']) || $param['user_id'] = 100006;  // 添加用户ID
// 传递参数
$post_data = array(
    'version' => '2.3.0',
    'os_type' => 'android',
    'ctime' => time(),
    'app_name' => 'poco_yuepai_android',
    'sign_code' => substr(md5('poco_' . json_encode($param) . '_app'), 5, -8),
    'is_enc' => 0,
    'param' => $param,
);
$cov_data = iconv('GBK', 'UTF-8', json_encode($post_data));  // 数据转编码

header('Content-type: application/json; charset=utf-8');   // 定义文件格式
echo mobile_app_curl($url, $cov_data);  // 直接输出结果

/**
 * cURL 获取数据
 * 
 * @param string $url 链接
 * @param array $post_data POST数据
 * @return string 
 */
function mobile_app_curl($url, $post_data) {
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
