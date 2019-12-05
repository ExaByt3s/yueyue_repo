<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'/seller/index.tpl.htm');

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

$seller_user_id = trim($_INPUT['seller_user_id']);





// 列表
$ret = get_api_result('customer/sell_services_list.php',array(
    'seller_user_id' => $seller_user_id
    ), true); 



// 分页使用的page_count
$page_count = 6; 


$limit_start = ($page - 1)*$page_count;


$limit = "{$limit_start},{$page_count}";


/**********分页处理**********/
$page_obj = new show_page ();
$total_count = $ret['data']['total'];
$show_count = 6 ;

$page_obj->sethash('go_place');

$page_obj->setvar (array( 'seller_user_id' => $seller_user_id));
$page_obj->set ( $show_count, $total_count );
$ret = get_api_result('customer/sell_services_list.php',array(
        'seller_user_id' => $seller_user_id,
        'limit' => $page_obj->limit (), 
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


// 列表图片格式转换
foreach ($ret['data']['list'] as $k => $val) 
{
    $ret['data']['list'][$k]['images'] = yueyue_resize_act_img_url($val['images'], $size = '320');
}

if ($_INPUT['print']) 
{
    print_r($ret);
}


// 更多介绍
$ret_info = get_api_result('customer/trade_seller_detail.php',array(
    'user_id' => $yue_login_id,
    'seller_user_id'=> $seller_user_id 
    ), true); 



$tpl->assign('seller_user_id',$seller_user_id);
$tpl->assign('service_status',$service_status);
$tpl->assign('goods_type',$goods_type);
$tpl->assign('stars_width',$stars_width);
$tpl->assign('name',$ret_data["name"]);
$tpl->assign($ret_data);
$tpl->assign('list',$ret['data']['list']);
$tpl->assign('more_introduce',$ret_info['data']['introduce']);

?>