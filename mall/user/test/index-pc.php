<?php


// 注意： 此常量为首页使用 请勿拷贝
    define("opacity",1);
// 注意： 此常量为首页使用 end

$task_templates_root = TASK_TEMPLATES_ROOT;

// 新版首页的变量
// hudw 2015.9.7
if(isset($index_template_root))
{
	$task_templates_root = $index_template_root.'/templates/default/';
}

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView($task_templates_root.$pc_wap.'/index.tpl.htm');

// 头部css相关
include_once($task_templates_root.$pc_wap. '/webcontrol/head.php');
// 头部bar
include_once($task_templates_root.$pc_wap. '/webcontrol/global-top-bar.php');
// 底部
include_once($task_templates_root.$pc_wap. '/webcontrol/footer.php');
// 下载区域
include_once($task_templates_root.$pc_wap. '/webcontrol/down-app-area.php');

$pc_global_top = _get_wbc_head();
$global_top_bar = _get_wbc_global_top_bar();
$footer = _get_wbc_footer();
$down_app_area = _get_wbc_down_app_area();


$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_ROOT);
// ================== pc版头部通用 end ==================

// 栏目配置
$location_id = $_COOKIE['yue_location_id'] ;

$obj = POCO::singleton('pai_home_page_topic_class');
$arr = $obj->get_pc_home_category_3_1_0($location_id);

foreach ( $arr as $k => $val ) 
{
	
    $arr[$k]['url'] = mall_yueyue_app_to_http($val['url']);
	
}

/**
foreach ( $arr  as $k => $val ) 
{
    if ($val['str'] == '约活动') 
    {
        $arr[$k]['is_hide'] = 1;
    }
}
**/

// 关键词配置
$title_key = '约约--最高效的时间电商平台';
$keywords_key ='约约，模特，摄影师，约拍，约摄，服务，时间电商，技能服务';
$description_key ='约约是最高效的时间电商O2O平台，通过时间电商平台，每个用户都能利用自己的零碎时间，提供摄影、技能等相关的服务来创造价值。';


// 榜单banner
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$top_banner = new cms_system_class ();
$top_banner_ret = $top_banner->get_record_list_by_issue_id($b_select_conut=false, 36907 , $limit="0,3", $order_by="place_number ASC", $freeze=null, $where_str="");

$type_obj = POCO::singleton('pai_mall_goods_type_class');
$type_data = $type_obj->get_type_cate(0);

$tpl->assign('type_data',mall_output_format_data($type_data));
$tpl->assign('title_key', $title_key);
$tpl->assign('keywords_key', $keywords_key);
$tpl->assign('description_key', $description_key);

$tpl->assign('get_big_category', $get_big_category );
$tpl->assign('resa', $ret);
$tpl->assign('roll', $roll);

$tpl->assign('arr', $arr);
$tpl->assign('top_banner_ret', $top_banner_ret);

$output_arr['input_data']['data'] = array
(

    0 => array(
        'text' => '',
        'place_holder' => '关键字',
        'default_text' => '',
        'default_url' => '',
        'type_id' => '',
        'search_type' => 'goods',
        'show' => true
    ),
    1 => array(
        'text' => '',
        'place_holder' => '商品ID/商家名称',
        'default_text' => '',
        'default_url' => '',
        'type_id' => '31',
        'search_type' => 'seller',
    )

);
$output_arr['input_data']['search_type'] = "goods";




// 首页热门推荐

$hot_tuijian  = $ret_index_v2['data']['module_list'][0]['exhibit'];

$tpl->assign('hot_tuijian',$hot_tuijian);

$tpl->assign('page_data',mall_output_format_data($output_arr));

?>