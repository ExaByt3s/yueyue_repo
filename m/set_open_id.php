<?php 
$open_id = $_GET['open_id'];

if($open_id)
{
	setcookie("yueus_openid",$open_id,time()+3600,"/","yueus.com");
	echo '��Ȩ�ɹ�';
}
else
{
	setcookie("yueus_openid","",time()-3600,"/","yueus.com");
	echo '�����Ȩ�ɹ�';
}
?>