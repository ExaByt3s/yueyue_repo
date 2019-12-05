<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');




$phone = $_INPUT['phone'];
$id    = $_INPUT['id'];

$pai_user_obj = POCO::singleton('pai_user_class');

if($phone)  $user_info = $pai_user_obj->get_user_list(false, 'cellphone='.$phone);
if($id)     $user_info = $pai_user_obj->get_user_list(false, 'user_id='.$id);

$user_id = (int)$user_info[0]['user_id'];
$role    = $user_info[0]['role'];

if(!$user_id)
{
	echo '查无此人';
}else 
{
	echo "ID：".$user_info[0]['user_id'].'<br />';
	echo "昵称：".$user_info[0]['nickname'].'<br />';
	echo "手机：".$user_info[0]['cellphone'].'<br />';
}






?>