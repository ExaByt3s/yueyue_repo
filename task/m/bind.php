<?php

 
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/../task_common.inc.php');
// 权限文件
include_once($file_dir.'/../task_for_normal_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top_nav.php');
include_once($file_dir. '/./webcontrol/footer.php');
$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');


$tpl = $my_app_pai->getView('bind.tpl.htm');

$tpl->assign('time', time());  //随机数

// 公共样式和js引入
$global_top = _get_wbc_head();
$tpl->assign('global_top', $global_top);

$global_nav = _get_wbc_top_nav(array('cur_page'=>'lead_list'));
$tpl->assign('global_nav', $global_nav);

// 底部
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);

$obj = POCO::singleton('pai_user_class');

$ret = $obj->get_user_info_by_user_id($yue_login_id);
$tpl->assign('info', $ret);

$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$bind_status = $pai_bind_account_obj->get_bind_status($yue_login_id,'alipay_account');
				//-1 未绑定 0 待审核 1已审核 2审核不通过
				switch ($bind_status['status']) {
					case '-1':
					case '2':
						$status_resault['code'] = -1;
						$status_resault['msg']  = '未绑定';
						break;
					case '0':
						$status_resault['code'] = -2;
						$status_resault['msg']  = '待审核';
						$status_resault['account'] =$bind_status['third_account'];
						break;
					case '1':
						$status_resault['code'] = 1;
						$status_resault['msg']  = '已绑定';
						$status_resault['account'] =$bind_status['third_account'];
						break;
					// modify by hudw 2015.5.25
					/**
					case '2':
						$status_resault['code'] = -3;
						$status_resault['msg']  = '审核不通过';
						break;
					**/
					default:
						break;
				}
$tpl->assign('status_resault', $status_resault);

$tpl->output();
 ?>