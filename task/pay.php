<?php
/** 
 * 
 * tt
 * ��Բ
 * 2015-4-11
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');
// Ȩ���ļ�
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top_nav.php');
include_once($file_dir. '/./webcontrol/footer.php');
 
$tpl = $my_app_pai->getView('pay.tpl.htm');

$tpl->assign('time', time());  //�����

$quotes_id = (int)$_INPUT['quotes_id'];

// ������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'order_list'));
$tpl->assign('pc_global_nav', $pc_global_nav);

// �ײ�
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);

$tpl->assign('quotes_id', $quotes_id);

$tpl->output();
 ?>