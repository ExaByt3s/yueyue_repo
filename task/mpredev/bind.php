<?php

 
/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/../task_common.inc.php');
// Ȩ���ļ�
include_once($file_dir.'/../task_for_normal_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top_nav.php');
include_once($file_dir. '/./webcontrol/footer.php');
$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');


$tpl = $my_app_pai->getView('bind.tpl.htm');

$tpl->assign('time', time());  //�����

// ������ʽ��js����
$global_top = _get_wbc_head();
$tpl->assign('global_top', $global_top);

$global_nav = _get_wbc_top_nav(array('cur_page'=>'lead_list'));
$tpl->assign('global_nav', $global_nav);

// �ײ�
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);

$obj = POCO::singleton('pai_user_class');

$ret = $obj->get_user_info_by_user_id($yue_login_id);
$tpl->assign('info', $ret);

$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$bind_status = $pai_bind_account_obj->get_bind_status($yue_login_id,'alipay_account');
				//-1 δ�� 0 ����� 1����� 2��˲�ͨ��
				switch ($bind_status['status']) {
					case '-1':
					case '2':
						$status_resault['code'] = -1;
						$status_resault['msg']  = 'δ��';
						break;
					case '0':
						$status_resault['code'] = -2;
						$status_resault['msg']  = '�����';
						$status_resault['account'] =$bind_status['third_account'];
						break;
					case '1':
						$status_resault['code'] = 1;
						$status_resault['msg']  = '�Ѱ�';
						$status_resault['account'] =$bind_status['third_account'];
						break;
					// modify by hudw 2015.5.25
					/**
					case '2':
						$status_resault['code'] = -3;
						$status_resault['msg']  = '��˲�ͨ��';
						break;
					**/
					default:
						break;
				}
$tpl->assign('status_resault', $status_resault);

$tpl->output();
 ?>