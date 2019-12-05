<?php
$osver  = $_REQUEST['osver'];
$appver = $_REQUEST['appver'];
$iswifi = $_REQUEST['iswifi'];

$request_str = serialize($_REQUEST) ;
$sql_str = "INSERT INTO pai_log_db.update_log_tbl(request_str) VALUES ('{$request_str}')";
db_simple_getdata($sql_str, TRUE, 101);

$latest_version = '1.0.5';   //最新版本
$down_url       = 'http://y.poco.cn/a/y201502121619.apk'; //版本下载地址
$notice         = '系统更新提示：
1、约拍信息推送整合进了个人消息，更方便查询。
2、新增微信支付功能、聊天记录清空功能。
3、拍摄价格单位，将调整为 x元/2小时 及 x元/4小时。'; //

if($appver < $latest_version)
{
    $array_result['result']     = "0000";         
    $array_result['update']     = 1;            
    $array_result['version']    = $latest_version;
    $array_result['app_url']    = $down_url;
    $array_result['detail']     = iconv('GBK', 'UTF-8', $notice);    
}else{
    $array_result['result']     = "0000";         
    $array_result['update']     = 0;              
}
/**    $array_result['result']     = "0000";         
    $array_result['update']     = 0;    
**/
/**
$array_result['result']     = "0000";         //默认成功返回
$array_result['update']     = 0;            //0、1，为1表示客户端需要提示更新
$array_result['version']    = '1.0';           //version：app_url对应的版本号，需确保与app_url对应的版本号一致，否则会一直提示更新
$array_result['app_url']    = 'http://y.poco.cn/a/';           //app_url：安卓直接填包的下载地址，ios填下载页面的链接
$array_result['detail']     = 'BUG修改';           //detail：更新提示信息
$array_result['detail']     = iconv('GBK', 'UTF-8', $array_result['detail']);
**/

if($_REQUEST['uid'] == 100008)
{
    $latest_notice = '1，主页全新改版，摄影师可以发布自己需求了。
2，模特可以主动推荐自己，参与报名工作机会。
3，体验优化。';
    //版本低于最低版本，强制更新
    $array_result['result']     = "0000";
    $array_result['update']     = 2;
    $array_result['version']    = '1.1.0';
    $array_result['app_url']    = 'http://y.poco.cn/a';
    $array_result['detail']     = iconv('GBK', 'UTF-8', $latest_notice);
}

echo json_encode($array_result);
?>
