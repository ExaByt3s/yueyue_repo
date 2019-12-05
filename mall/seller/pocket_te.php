<?php

/**
 * 商品服务操作页（添加或者编辑）
 *
 * 2015-6-29
 *
 * author  nolest
 *
 */

include_once 'common.inc.php';

$user_id = $yue_login_id;

$pc_wap = 'wap/';

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'pocket.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// 顶部栏
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// 底部
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// 头部bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);

// 底部
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);

$obj = POCO::singleton('pai_user_class');

$ret = $obj->get_user_info_by_user_id($yue_login_id);
$tpl->assign('info', $ret);

$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$bind_status = $pai_bind_account_obj->get_bind_status($yue_login_id,'alipay_account');
				//-1 未绑定 0 待审核 1已审核 2审核不通过
				switch ($bind_status['status']) {
					case '-1':
						$status_resault['code'] = -1;
						$status_resault['msg']  = '未绑定';
						break;
					case '0':
						$status_resault['code'] = -2;
						$status_resault['msg']  = '待审核';
						break;
					case '1':
						$status_resault['code'] = 1;
						$status_resault['msg']  = '已绑定';
						break;
					case '2':
						$status_resault['code'] = -3;
						$status_resault['msg']  = '审核不通过';
						break;
					default:
						break;
				}
$tpl->assign('status_resault', $status_resault);

dump($tpl);

$tpl->assign('num',123);
$tpl->output();

?>