<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



$user_id = $_INPUT['user_id'];

$user_obj = POCO::singleton('pai_user_class');

$user_info = $user_obj->get_user_info($user_id);

if(!$user_info)
{
	die('���޴���');
}else
{
	$user_obj->load_member($user_id);
	echo $user_id;
	echo "��¼�ɹ� �ǳƣ�".$user_info["nickname"];
}


?>