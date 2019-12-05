<?php
/** 
 * 
 * 构造二维码返回页
 * 
 * author 星星
 * 
 * 
 * 2015-3-30
 * 
 * 
 */

include_once("./party_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$activity_code_obj = POCO::singleton('pai_activity_code_class');

//获取encode了的分享链接
$text = urldecode(trim($_INPUT['text']));

if(empty($text))
{
    $text = "http://www.yueus.com";
}

//构造二维码图片
$img = $activity_code_obj->get_qrcode_img($text);
header('Content-type: image/png');
echo file_get_contents($img);
?>