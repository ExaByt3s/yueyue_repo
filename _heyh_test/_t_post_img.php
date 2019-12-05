<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$img_url = 'http://yue-icon.yueus.com/10/100008.jpg';

$img_socurs = file_get_contents($img_url);



$url = 'http://sendmedia-wifi.yueus.com:8078/icon.cgi';

$param = array('poco_id' =>100008, 'opus' => $img_socurs);

$ch = curl_init();
// 设置选项，包括URL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER,  false);
curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

// 执行并获取HTML文档内容
$output = curl_exec($ch);

// 释放curl句柄
curl_close($ch);

header("Content-type: text/html; charset=utf-8");

// 打印获得的数据
print_r($output);
?>