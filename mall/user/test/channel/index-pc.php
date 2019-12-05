<?php

//****************** pc版头部通用 start ******************



$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'channel/index.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部bar
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');
// 底部
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// 下载区域
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/down-app-area.php');

$pc_global_top = _get_wbc_head();
$global_top_bar = _get_wbc_global_top_bar();
$footer = _get_wbc_footer();
$down_app_area = _get_wbc_down_app_area();

if(MALL_UA_IS_YUEYUE != 1)
{
	
    $pat = '/<a(.*?)href=[\'\"]?([^\'\" ]+).*?>/i';

    $ret['content'] = preg_replace_callback($pat,"mall_topic_page_replace_function",$ret['content']);
	
	
}



$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);
// ================== pc版头部通用 end ==================



$page = $_INPUT['page'];
$page = $page ? $page : 1 ;

// 分页使用的page_count
$page_count = 9; 

if($page > 1)
{
    $limit_start = ($page - 1)*($page_count - 1);
}
else
{
    $limit_start = ($page - 1)*$page_count;
}

$limit = "{$limit_start},{$page_count}";

$temp_ret = get_api_result('customer/classify_list_min.php',array(
        'query' => $query,
        'type_id' => $type_id,
        'user_id' => $yue_login_id
    )
);



/**********分页处理**********/
$page_obj = new show_page ();
$page_obj->file = "index.php?";
$total_count = $temp_ret['data']['category_total'];
$show_count = 9 ;
$page_obj->setvar (array( 'query' => $query ,'type_id' => $type_id ) );
$page_obj->set ( $show_count, $total_count );
$ret = get_api_result('customer/classify_list_min.php',array(
    'limit' => $page_obj->limit (), 
    'query' => $query,
    'type_id' => $type_id,
    'user_id' => $yue_login_id
    )
);
 
if ($show_count > $total_count) 
{
    $page_show = '';
}
else
{
    $page_show = $page_obj->output ( 1 ) ;
}
$tpl->assign ( "page", $page_show );
/**********分页处理 end **********/


if ($_INPUT['print']) 
{
    print_r($ret);
}

$output_arr['input_data'] = array
(
    'text' => '',
    'place_holder' => '关键字',    
    'type_id' => '',
    'search_type' => 'goods'

);

$tpl->assign('page_data',mall_output_format_data($output_arr));

$key_current = $MALL_COLUMN_CONFIG[$type_id] ;
$tpl->assign('key_current', $key_current);
$tpl->assign('ret', $ret['data']);


?>