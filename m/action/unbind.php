<?php
/**
 * hudw 2014.9.16
 * ���
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$user_id = $_INPUT['user_id'];


/*if($user_id != $yue_login_id)
{
	$output_arr['code'] = 0;
	$output_arr['msg'] = '�Ƿ��˳�';
	mobile_output($output_arr,false);
	exit();
}*/

$nfc_obj = POCO::singleton('pai_nfc_class');
$nfc_obj->mobile_logout($yue_login_id);

$pai_user_obj = POCO::singleton('pai_user_class');
$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');

$bind_weixin_obj->delete_user($yue_login_id);

$ret = $pai_user_obj->logout();

//�˳���Ȩ
setcookie('yueus_openid', '', null, '/', '.yueus.com');
setcookie('yueus_code', '', null, '/', '.yueus.com');
setcookie('yueus_scope', '', null, '/', '.yueus.com');
setcookie('yueus_url2', '', null, '/', '.yueus.com'); //����Ȩ��δ�󶨡�δ��¼����url2���ݸ�ǰ�ˣ��Ա�ǰ��ע�ᡢ��¼����ת

$output_arr['code'] = $ret;
$output_arr['msg'] = '�˳���¼';


mobile_output($output_arr,false);

?>