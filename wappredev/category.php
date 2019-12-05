<?php

 
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

//include_once($file_dir.'/./task_common.inc.php');
// 权限文件
//include_once($file_dir.'/./task_for_normal_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top_nav.php');
include_once($file_dir. '/./webcontrol/footer.php');
$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');


$tpl = $my_app_pai->getView('category.tpl.htm');

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
$head_pic = $topic_obj->get_banner_list($b_id);
$issue_pic = $topic_obj->get_small_category($b_id);
$details_pic_info = $topic_obj->get_small_category_3_goods($b_id);
foreach ($details_pic_info as $k => $val) {
	$rank_count = $val['star'];
    for ($i=0; $i < $val['star'] ; $i++) { 
        $details_pic_info[$k]['starts_list'][$i]['yes_start'] = 1 ;
		$rank_count = $i + 1;
    }
	if($rank_count == 0){
		for($j=0; $j < 5;$j++){
			$details_pic_info[$k]['starts_list'][$j]['no_start'] = 1;
		}
	}
		else{
			for ($j=$rank_count; $j < 5;$j++){
		$details_pic_info[$k]['starts_list'][$j]['no_start'] = 1;
		
	}
	}	
}
//$details_pic_info['starts_list'] = $starts;
$tpl->assign('topic_data', $data);
$tpl->assign('head_pic', $head_pic);
$tpl->assign('issue_pic', $issue_pic);
$tpl->assign('details_pic_info', $details_pic_info);




$tpl->output();
 ?>