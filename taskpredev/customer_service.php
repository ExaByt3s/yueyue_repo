<?php
/** 
 * 
 * tt
 * ��Բ
 * 2015-4-11
 * 
 */
 
/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */

define("DONT_CHECK_AUTH",1);

$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

// Ȩ���ļ�
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');








include_once($file_dir. '/./webcontrol/footer.php');
 
$tpl = $my_app_pai->getView('customer_service.tpl.htm');


// ���������̼Ҷ��ܿ�����ҳ�棬������ļ�
include_once($file_dir.'/./consumers_and_seller_require.php');



$tpl->assign('time', time());  //�����

// ������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'customer_service'));
$tpl->assign('pc_global_nav', $pc_global_nav);


// �ײ�
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);





$tpl->output();
 ?>