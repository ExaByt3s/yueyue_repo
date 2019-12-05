<?php

$pc_wap = 'wap/';


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'home/collection_list.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$type_id = intval($_INPUT['type_id']);
$target_type = trim($_INPUT['target_type']);
$sort_by = trim($_INPUT['sort_by']);
$target_type = trim($_INPUT['target_type']);
// 输出页面参数
$page_params = mall_output_format_data(array
	(
        'user_id'=> $yue_login_id,
		'target_type'=> $target_type,
		'type_id' => $type_id,
		'sort_by' => $sort_by
	));

// 获取所有类型 模特、培训、摄影.....
$type_obj = POCO::singleton('pai_mall_goods_type_class');
$type_data_arr =array(
    0 => array(
    'id' => 0,
    'name' => "所有品类"
    ),
    2 => array(
    'id' => 31,
    'name' => "约模特"
    ),
    3 => array(
    'id' => 40,
    'name' => "约摄影"
    ),
    4 => array(
    'id' => 41,
    'name' => "约美食"
    ),
    5 => array(
    'id' => 5,
    'name' => "约培训"
    ),
    6 => array(
    'id' => 3,
    'name' => "约化妆"
    ),
    7 => array(
    'id' => 43,
    'name' => "约有趣"
    ),
    8 => array(
    'id' => 12,
    'name' => "商业定制"
    )
);



$last_updata = "goods_last_update_time";
if($target_type=="seller")
{
    $last_updata = "user_last_update_time";
}

$sort_data = array(
    0 => array(
        'name' => '默认排序',
        'sort_by' =>'add_time'
    ),
    1 => array(
        'name' => '按照关注时间',
        'sort_by' => 'add_time'
    ),
    2 => array(
        'name' => '按照最近更新',
        'sort_by' => $last_updata
        )
);

$filter_data = array(
    'type_data' => $type_data,
    'sort_data' => $sort_data
);
$page_filter_page_params = mall_output_format_data($filter_data);
//$sort_data = $page_filter_page_params['sort_data'];
//$type_data = $page_filter_page_params['type_data'];


$tpl->assign('seller_url','./collection_list.php?target_type=seller');
$tpl->assign('goods_url', './collection_list.php?target_type=goods');
$tpl->assign('page_params',$page_params);
$tpl->assign('target_type',$target_type);
$tpl->assign('type_id',$type_id);
$tpl->assign('sort_data',$sort_data);
$tpl->assign('type_data',$type_data_arr);
$tpl->assign('sort_by',$sort_by);
$tpl->assign('page_filter_page_params',$page_filter_page_params);


?>