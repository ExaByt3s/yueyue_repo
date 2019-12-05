<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$url="http://www.yueus.com";
$img=pai_activity_code_class::get_qrcode_img($url);

echo "<img src='{$img}' />";

?>