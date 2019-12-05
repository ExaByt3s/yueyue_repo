<?php
include_once("protocol_input.inc.php");

// 获取客户端的数据
$cp = new yue_protocol_system();
// 获取用户的授权信息
//$client_data = $cp->get_input();

$url = 'http://www.yueus.com/mobile_app/event/get_hot_list.php';
$ch = curl_init();

/**
$query['tag'] = '日系';
$query['price'] = '10-50';
$query['hour'] = '4';
$query['order'] = '';
$query['key'] = '美女';
**/

// 请求参数
$param = array('location_id' =>101029001, 'query'=>'score_list');

// 请求的参数
$post_data = array(
  'version'   => '1.0.1',
  'os_type'   => 'ios',
  'ctime'     => time(),
  'app_name'  => 'poco_yuepai_android',
  'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
  'is_enc'    => 0,
  'param'     => $param,
);

$post_data = json_encode($post_data);
$post_data = iconv('GBK', 'UTF-8', $post_data);

// 设置选项，包括URL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER,  false);
curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));

// 执行并获取HTML文档内容
$output = curl_exec($ch);

// 释放curl句柄
curl_close($ch);

header("Content-type: text/html; charset=utf-8");

// 打印获得的数据
print_r($output);
?>