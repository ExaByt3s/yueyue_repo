<?php

 
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);
$b_id = $_INPUT['b_id'];
//include_once($file_dir.'/./task_common.inc.php');
// 权限文件
//include_once($file_dir.'/./task_for_normal_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top_nav.php');
include_once($file_dir. '/./webcontrol/footer.php');
$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');


$tpl = $my_app_pai->getView('front.tpl.htm');

$tpl->assign('time', time());  //随机数

// 公共样式和js引入
$global_top = _get_wbc_head();
$tpl->assign('global_top', $global_top);

$global_nav = _get_wbc_top_nav(array('cur_page'=>'lead_list'));
$tpl->assign('global_nav', $global_nav);

// 底部
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);

$topic_obj = POCO::singleton('pai_home_page_topic_class');
$data = $topic_obj->get_big_category(0);
$tpl->assign('topic_data', $data);

$app_ver_str = file_get_contents('http://yp.yueus.com/download/version.txt');
$tpl->assign('app_ver', $app_ver_str);

$tpl->output();
 ?>