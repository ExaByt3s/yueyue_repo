<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 29 July, 2015
 * @package default
 */

/**
 * Define ֪ͨ��ʾҳ��
 */

include_once 'config.php';

$pc_wap = 'wap/';

$type = trim($_INPUT['type']);

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'notice/notice.tpl.html');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

switch($type)
{
	case 'order':
	$message = '�ύʧ��,�����ظ��ύ����';
	break;
	case 'pay':
	$message = '֧��ʧ��,�����ظ��ύ֧����Ϣ';
	break;
}


$tpl->assign('message',$message);
$tpl->assign('return_url',G_MALL_PROJECT_USER_ROOT.'/index.php');

$tpl->output();	
?>