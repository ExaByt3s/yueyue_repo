<?php
/** 
 * 
 * tt
 * 汤圆
 * 2015-4-11
 * 
 */
 
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */

define("DONT_CHECK_AUTH",1);

$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

// 权限文件
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');








include_once($file_dir. '/./webcontrol/footer.php');
 
$tpl = $my_app_pai->getView('customer_service.tpl.htm');


// 消费者与商家都能看到的页面，引入此文件
include_once($file_dir.'/./consumers_and_seller_require.php');



$tpl->assign('time', time());  //随机数

// 公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'customer_service'));
$tpl->assign('pc_global_nav', $pc_global_nav);


// 底部
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);





$tpl->output();
 ?>