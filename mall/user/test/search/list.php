<?php

include_once 'config.php';
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'search/list.tpl.htm');



// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);



// 接收参数
$keyword = urldecode(iconv("UTF-8","GBK",trim($_INPUT['keyword'])));

$type = $_INPUT['type'];
$type_id = $_INPUT['type_id'] ? $_INPUT['type_id'] : '' ;



switch ($type) 
{
    case 'seller':
        $type_name = '商家列表';
    break;
    
    case 'goods':
        $type_name = '服务列表';
    break;
}
$tpl->assign('type_name',$type_name);


if (empty($keyword )) 
{
    echo "请输入搜索关键字！";
    return ;
}

// print_r($keyword);


// $ret = get_api_result('customer/search_sellers.php',array(
//     'user_id' => $yue_login_id,
//     'keyword'=> $keyword
// ), true); 

$page_params = array(
    'keyword' => $keyword,
    'type' => $type ,
    'type_id' => $type_id
);


$page_params = mall_output_format_data($page_params);

$tpl->assign('page_params',$page_params);
// $tpl->assign('ret',$ret);






$tpl->output();
?>