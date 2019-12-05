<?php

 
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');


$tpl = $my_app_pai->getView('pub.tpl.htm');

$tpl->assign('time', time());  //随机数

$task_questionnaire_obj = POCO::singleton('pai_task_questionnaire_class');
$json_arr = $task_questionnaire_obj -> get_questionnaire_version_list(4);
$json_arr = yue_iconv_arr_to_json($json_arr,'GBK','UTF-8');
//$json_arr = poco_iconv_arr($json_arr, 'GBK','UTF-8');



$arr = array('province'=>$area_config['province'],'city'=>$area_config['city']);
$output_arr['data'] = $area_config['sort_area'];

$output_arr = yue_iconv_arr_to_json($output_arr,'GBK','UTF-8');

$tpl->assign('output_arr', $output_arr);



$tpl->assign('json_arr', $json_arr);

$tpl->output();

 ?>