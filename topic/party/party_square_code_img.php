<?php
/** 
 * 
 * �����ά�뷵��ҳ
 * 
 * author ����
 * 
 * 
 * 2015-3-30
 * 
 * 
 */

include_once("./party_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$activity_code_obj = POCO::singleton('pai_activity_code_class');

//��ȡencode�˵ķ�������
$text = urldecode(trim($_INPUT['text']));

if(empty($text))
{
    $text = "http://www.yueus.com";
}

//�����ά��ͼƬ
$img = $activity_code_obj->get_qrcode_img($text);
header('Content-type: image/png');
echo file_get_contents($img);
?>