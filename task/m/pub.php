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


$tpl = $my_app_pai->getView('pub.tpl.htm');

$tpl->assign('time', time());  //随机数

// 公共样式和js引入
$global_top = _get_wbc_head();
$tpl->assign('global_top', $global_top);

$global_nav = _get_wbc_top_nav(array('cur_page'=>'lead_list'));
$tpl->assign('global_nav', $global_nav);

// 底部
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);

$task_questionnaire_obj = POCO::singleton('pai_task_questionnaire_class');
$json_arr = $task_questionnaire_obj -> get_questionnaire_version_list(4);
$json_arr = yue_iconv_arr_to_json($json_arr,'GBK','UTF-8');
//$json_arr = poco_iconv_arr($json_arr, 'GBK','UTF-8');


$arr = array('province'=>$area_config['province'],'city'=>$area_config['city']);

$tpl ->assign("province",$arr['province']);
$tpl ->assign("city",$arr['city']);
$tpl ->assign("user_id",$yue_login_id);
$tpl->assign('json_arr', $json_arr);

$tpl->output();
 ?>