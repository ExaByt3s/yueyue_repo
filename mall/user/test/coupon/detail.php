<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 17 July, 2015
 * @package default
 */

/**
 * Define �Ż�ȯ����
 */
include_once 'config.php';

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'coupon/detail.tpl.html');

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$coupon_obj = POCO::singleton('pai_coupon_class');

$sn = $_INPUT['sn'];


$ret = $coupon_obj -> get_coupon_detail_by_sn($sn,$yue_login_id);

$ret['scope_module_type_name'] = '��'.$ret['scope_module_type_name'].'��';
$ret['scope_order_total_amount'] = $ret['scope_order_total_amount']*1;
$ret['face_value'] = $ret['face_value']*1;
$ret['coin'] = '��';

if($ret['scope_module_type'] == 'yuepai')
{
	$ret['scope_module_txt'] = '����Լ�ģ� ʹ���Żݣ�';
	$ret['scope_module_btn'] = 'hot'; //��ҳ
}
else if($ret['scope_module_type'] == 'waipai')
{
	$ret['scope_module_txt'] = '�������ģ� ʹ���Żݣ�';
	$ret['scope_module_btn'] = 'act'; //����
}
else if($ret['scope_module_type'] == 'task_request')
{
	$ret['scope_module_txt'] = '����ʹ���Ż�ȯ��';
	$ret['scope_module_btn'] = 'hot';
	
	//��ʱ������ȻTT�Ż�ȯ����תȥ������ҳ
	$ret['scope_module_type'] = 'yuepai';
}
else if($ret['scope_module_type'] == '')
{
	$ret['scope_module_txt'] = '����ʹ���Ż�ȯ��';
	$ret['scope_module_btn'] = 'hot';

	//��ʱ������Ȼͨ��ȯ����תȥ������ҳ
	$ret['scope_module_type'] = 'yuepai';
}
else
{
	$ret['scope_module_txt'] = '����ʹ���Ż�ȯ��';
	$ret['scope_module_btn'] = 'hot';
}

$parma = mall_output_format_data($ret);
$tpl->assign('data', $parma);


$tpl->output();	
?>