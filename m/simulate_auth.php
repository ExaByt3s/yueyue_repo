<?php 
/*
 * ģ����Ȩ
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$user_obj = POCO::singleton('pai_user_class');
$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');


$openid = $_INPUT['open_id'];

//����Ƿ�󶨹������󶨹����Զ���¼
$bind_info = $bind_weixin_obj->get_bind_info_by_open_id($openid);
if($bind_info)
{
	$user_id = $bind_info['user_id'];
	$user_obj->load_member($user_id);
}


$url = 'http://yp.yueus.com/m/wx';
echo "<script type=\"text/javascript\">location.href='{$url}';</script>";
?>