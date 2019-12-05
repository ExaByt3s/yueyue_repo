<?php 
$img = $_GET['img'];
$img = file_get_contents($img);
//使用图片头输出浏览器
header("Content-Type: image/jpeg;text/html; charset=utf-8");
echo $img;
exit; 

?>