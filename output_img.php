<?php 
$img = $_GET['img'];
$img = file_get_contents($img);
//ʹ��ͼƬͷ��������
header("Content-Type: image/jpeg;text/html; charset=utf-8");
echo $img;
exit; 

?>