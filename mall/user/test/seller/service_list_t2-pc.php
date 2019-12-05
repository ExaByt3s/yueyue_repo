<?php


//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'/seller/service_list_t2.htm');

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


$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);
// ================== pc版头部通用 end ==================



$tab = trim($_INPUT['tab']);

$return_query = $_INPUT['return_query'];

$tpl->assign('title',$main_title);

$page = $_INPUT['p'] ;

$page = $page ? $page : 1 ;

// 分页使用的page_count
$page_count = 9; 


$limit_start = ($page - 1)*$page_count;


$limit = "{$limit_start},{$page_count}";


// 获取分页总数
// $ret = get_api_result('customer/goods_list.php',array(
//     'user_id' => $yue_login_id,
//     'limit' => $limit, 
//     'return_query' =>urlencode($return_query)
//     )
// );

// print_r($ret);


/**********分页处理**********/

$ret = get_api_result('customer/goods_list.php',array(
    'user_id' => $yue_login_id,
    'limit' => $limit, 
    'return_query' =>urlencode($return_query)
    )
);

$page_obj = new show_page ();
$page_obj->file = "service_list_t2.php?";
$total_count = $ret['data']['total'];
$show_count = 9 ;
$page_obj->setvar (array( 'return_query' => $return_query ,'title' => $_INPUT['title'],'type_id' => $type_id ));
$page_obj->set ( $show_count, $total_count );

// print_r($limit);
// print_r($ret);

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


// 关键词配置
$keywords_key ='';
$description_key ='';


$tpl->assign('keywords_key', $keywords_key);
$tpl->assign('description_key', $description_key);

if ($_INPUT['print']) 
{
    print_r($ret);
}




$tpl->assign('data_list', $ret['data']);
$tpl->assign('type_id_title', $MALL_COLUMN_CONFIG[$type_id]['key_nav']);
$tpl->assign('type_id', $type_id);
?>