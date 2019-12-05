<?php
$osver  = $_REQUEST['osver'];
$appver = $_REQUEST['appver'];
$iswifi = $_REQUEST['iswifi'];


$latest_version = '1.0.3';   //最新版本
$down_url       = 'http://app.yueus.com/'; //版本下载地址
$notice         = '系统更新提示：||1、约拍信息推送整合进了个人消息，更方便查询。||△  2、新增微信支付功能、聊天记录清空功能。||△ 3、拍摄价格单位，将调整为 x元/2小时 及 x元/4小时。'; //

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

/**
$array_result['result']     = "0000";         //默认成功返回
$array_result['update']     = 0;            //0、1，为1表示客户端需要提示更新
$array_result['version']    = '1.0';           //version：app_url对应的版本号，需确保与app_url对应的版本号一致，否则会一直提示更新
$array_result['app_url']    = 'http://y.poco.cn/a/';           //app_url：安卓直接填包的下载地址，ios填下载页面的链接
$array_result['detail']     = 'BUG修改';           //detail：更新提示信息
$array_result['detail']     = iconv('GBK', 'UTF-8', $array_result['detail']);
**/



echo json_encode($array_result);
?>
