<?php
/**
 * Ӧ�ÿ��Ӧ��ʵ��
 */

//����Ӧ�ù����ļ�
require_once('poco_app_common.inc.php');

$item_info = POCO::execute(array('item.get_item_info', 7734111), array(135369060));
$gps_info = POCO::execute('item.get_gps_info_by_full_path', array($item_info['full_item_pathname']));


var_dump($gps_info);

exit;

//���û�Աmemberģ���API
$user_info = POCO::execute(array('member.get_user_info_by_user_id'), array($login_id));
//ȡ��Ӧ����Ŀ������Ϣ
$ini_info = $my_app_demo->ini('/');
//ȡ��ģ�����
$tpl = $my_app_demo->getView('demo.tpl.htm');
//ȡ��Ӧ�ò�������Ψһʵ��
$app_obj = POCO::singleton('app_register_class');
$app_info = $app_obj->get_app_info('debug');
//ȡ��POCOĬ��ͷβ
$header_html = $my_app_demo->webControl('Header', array('app_name'=>$my_app_demo->ini('app_config/APP_NAME'), 'app_url'=>$my_app_demo->ini('app_config/APP_URL')));
$footer_html = $my_app_demo->webControl('Footer', array(), true);
$tpl->assign('app_info', $app_info);
$tpl->assign('user_info', $user_info);
$tpl->assign('ini_info', $ini_info);
$tpl->assign('header_html', $header_html);
$tpl->assign('footer_html', $footer_html);
$tpl->output();

?>