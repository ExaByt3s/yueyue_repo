<?php 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$code_obj = POCO::singleton('pai_activity_code_class');

$url = urldecode($_INPUT['url']);

if(!$url) exit;

$img_url = $code_obj->get_qrcode_img($url);

$file = file_get_contents($img_url);

header('Content-type: image/png');

echo $file;


?>