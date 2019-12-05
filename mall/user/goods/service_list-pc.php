<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'/goods/service_list.tpl.htm');

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

// 
$tab = trim($_INPUT['tab']);

$return_query = $_INPUT['return_query'];

$tpl->assign('title',$main_title);

$page = $_INPUT['page'];
$page = $page ? $page : 1 ;

// 分页使用的page_count
$page_count = 9; 


$limit_start = ($page - 1)*$page_count;


$limit = "{$limit_start},{$page_count}";

$ret = get_api_result('customer/goods_list.php',array(
    'user_id' => $yue_login_id,
    'limit' => $limit,
    'return_query' =>urlencode($return_query)
    ));

/**********分页处理**********/
$page_obj = new show_page ();
$page_obj->file = "service_list.php?";
$total_count = $ret['data']['total'];
$show_count = 9 ;
$page_obj->setvar (array( 'return_query' => $return_query ,'title' => $_INPUT['title'],'type_id' => $type_id));
$page_obj->set ( $show_count, $total_count );
$ret = get_api_result('customer/goods_list.php',array(
    'user_id' => $yue_login_id,
    'limit' => $page_obj->limit (), 
    'return_query' =>urlencode($return_query)
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



// if ( $yue_login_id == 100049 ) 
// {
//     // *********************** 列表组件 start ***********************
//     //列表模板需要传递的参数，有什么传什么，没有为空
//     $params_array =  array(
//         'url' =>  'customer/goods_list.php' ,
//         'return_query' => urlencode($return_query) ,
//         'title' =>  $_INPUT['title'] ,
//         'type_id' => $type_id
//     );

//     // 载入列表模板
//     include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/common/list_type.php');
//     $list_type = list_type($params_array, 1 ,9); // $params_array 列表数据，$tpl_num 模板 一页显示多少个
   
//     $tpl->assign('list_type', $list_type);
//     // *********************** 列表组件 END ***********************

// }


$output_arr['input_data']['data'] = array
(

    0 => array(
        'text' => '',
        'place_holder' => '关键字',
        'default_text' => '',
        'default_url' => '',
        'type_id' => $type_id ,
        'search_type' => 'goods',
        'show' => true
    ),
    1 => array(
        'text' => '',
        'place_holder' => '商品ID/商家名称',
        'default_text' => '',
        'default_url' => '',
        'type_id' =>  $type_id,
        'search_type' => 'seller',
    )

);
$output_arr['input_data']['search_type'] = "goods";

$tpl->assign('page_data',mall_output_format_data($output_arr));




if ($_INPUT['print']) 
{
    print_r($ret);
}



// 关键词配置
$keywords_key ='';
$description_key ='';


$tpl->assign('keywords_key', $keywords_key);
$tpl->assign('description_key', $description_key);

$tpl->assign('type_id_title', $MALL_COLUMN_CONFIG[$type_id]['key_nav']);
$tpl->assign('type_id', $type_id);


$tpl->assign('data_list', $ret['data']);

?>