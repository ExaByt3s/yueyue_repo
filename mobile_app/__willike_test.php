<?php
//
//// 查询所有数据
//$slt_sql = 'SELECT A.* FROM poco_communication_oauth_tbl AS A , 
//(SELECT user_id,COUNT(*) AS cum FROM poco_communication_oauth_tbl GROUP BY user_id HAVING cum > 1) AS B 
//WHERE A.user_id >7007 ORDER BY A.id DESC;';
//
////$dbh = new PDO('mysql:host=127.0.0.1;port=3306;', 'chenwb', 'cHen3=fgs@');
//$dbh = new PDO('mysql:host=14.29.52.10;port=3306;dbname=m_service_db;', 'chenwb', 'cHen3=fgs@');
//$dbh->setAttribute(PDO::ATTR_PERSISTENT, true);  // 设置数据库连接为持久连接
//$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);  //
////$dbh->exec('SET CHARACTER SET UTF8'); // 设置 编码格式
//// user_id  access_token  expire_time  refresh_token  app_id add_time update_time
//$sth = $dbh->prepare($slt_sql);
//$exec_res = $sth->execute();
//$sth->setFetchMode(PDO::FETCH_ASSOC);  // 设置数据返回类型
//$rows = $sth->fetchAll();
//$err_ids = ',';
//$opt_ids = ',';
//$count_opt = 0;
//$count_err = 0;
//$k = 0;
//foreach ($rows as $val) {
//    $k ++;
//    $user_id = $val['user_id'];
//    if (strpos($opt_ids, ',' . $user_id . ',') !== FALSE) {
//        // 已经处理过
//        continue;
//    }
//    $app_id = $val['app_id'];
//    $app_id_new = $app_id == 3 ? 4 : 3;
//    $ist_sql = 'UPDATE m_service_db.poco_communication_oauth_tbl SET access_token="' . $val['access_token'] .
//            '",expire_time=' . $val['expire_time'] . ',refresh_token="' . $val['refresh_token'] .
//            '",update_time=' . $val['update_time'] . ' WHERE user_id=' . $user_id . ' AND app_id=' . $app_id_new . ';';
////    $exec_res = $sth->execute();
////    $sth = $dbh->prepare($ist_sql);
////    $affected_rows = $sth->rowCount(); // 操作影响的行数
////    unset($ist_sql);
////    unset($exec_res);
////    if ($affected_rows < 1) {
////        // 操作失败
////        $err_ids .= $user_id . '-' . $app_id . ',';
////        $count_err ++;
////        continue;
////    }
//    $opt_ids .= $user_id . ',';
//    $count_opt ++;
//    echo $ist_sql . PHP_EOL;
//}
////
////echo '<br>-----------------OK[' . $count_opt . ']-----------------<br/>';
////echo $opt_ids;
////echo '<br>-----------------FAIL[' . $count_err . ']-----------------<br/>';
////echo $err_ids;
//exit;

//$client = new GearmanClient();
//$client->addServer('113.107.204.233', 9830);
//$json = array(
//    'pocoid' => 'yuebuyer/117452',
//);
//$version_json = $client->do('get_client_info', json_encode($json)); // 获取历史记录
//var_dump($version_json);
//exit;
//$client = new GearmanClient();
//$client->addServer('113.107.204.236', 13200);
//
//// 聊天记录
//$json = array(
//    'type' => 'history',
//    'mode' => 'cross',
//    'send_user_id' => 'yuebuyer/100048',
//    'to_user_id' => 'yueseller/100331',
//    'start_time' => 0,
//    'end_time' => time(),
//    'count' => 30
//);
//
//$history_json = $client->do('chatlog', json_encode($json)); // 获取历史记录
////$history_json = trim($history_json);
//$rst = json_decode($history_json, true);
//
//var_dump($history_json, $rst);
//exit;

/**
 * 接口测试 - 陈伟标
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-6-17
 */
$path = filter_input(INPUT_POST, 'path');   // 文件路径
if (empty($path)) {
    $raw_data_str = $HTTP_RAW_POST_DATA;   // 支持 raw 数据
    if (empty($raw_data_str)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 400 Bad Request');
        exit();
    }
    $raw_data_arr = json_decode($raw_data_str, TRUE);
    if (empty($raw_data_arr)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 406 Not Acceptable');
        exit();
    }
    $path = $raw_data_arr['path'];
    if (empty($path)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 401 unauthorized');
        exit();
    }
//    if ($raw_data_arr['request_user'] != 'willike') {
//        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 406 Not Acceptable');
//        exit('pls use you own test account!');
//    }
    $param = $raw_data_arr;
}
if (strpos($path, 'mall/') === FALSE && strpos($path, 'ssl/') === FALSE) {
    // 手机 api 接口
    $dir = str_replace('\\', '/', dirname(__FILE__));  // 当前文件夹路径
    $file = $dir . '/' . (strpos($path, '.php') ? $path : $path . '.php');
    if (!file_exists($file)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 404 not found');
        exit();
    }
    $base_url = 'http://yp-new-t.yueus.com/mobile_app';
    $url = str_replace($dir, $base_url, $file);  // 拼装 url
} else {
    // 通用接口 ( 登陆,注册等 )
    $path = trim(trim($path), '.');
    $url = 'http://yp-new-t.yueus.com' . '/' . (strpos($path, '.php') ? $path : $path . '.php');
//    $url = 'https://ypays.yueus.com' . '/' . (strpos($path, '.php') ? $path : $path . '.php');
}
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 410 gone');
    exit();
}
foreach ($_POST as $key => $value) {
    if ($key == 'path') {
        continue;
    }
    // 处理内容中的json
    if (strpos($value, '{"') === 0) {
        $slen = strlen($value);
        $kpos = strpos($value, '}');
        if ($kpos == $slen || $kpos == ($slen - 1)) {
            $value = json_decode($value, true);
        }
    }
    $param[$key] = $value;   // 组装参数
}
isset($param['user_id']) || $param['user_id'] = 117452;  // 添加用户ID
// 传递参数
$post_data = array(
    'version' => '3.3.0',
    'os_type' => 'api',
    'ctime' => time(),
    'app_name' => 'poco_yuepai_api',
    'sign_code' => substr(md5('poco_' . json_encode($param) . '_app'), 5, -8),
    'is_enc' => 0,
    'param' => $param,
);
//var_dump(json_encode($post_data));exit;
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
//    curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

exit;
