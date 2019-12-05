<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("direct_list.tpl.htm");

$page_obj = new show_page ();

$mall_direct_obj = POCO::singleton ( 'pai_mall_direct_order_class' );

$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$activity_code_obj = POCO::singleton('pai_activity_code_class');


$type_id = $_INPUT['type_id'];
$act = $_INPUT['act'];
$id = $_INPUT['id'];

if($act=='del' && $id)
{
    $mall_direct_obj->del_config($id);
    echo "<script>alert('操作成功')</script>";
}

if($type_id)
{
	$where = "type_id=$type_id";
}
$type_name_arr = array("3"=>"化妆服务","12"=>"影棚租赁","5"=>"摄影培训","31"=>"模特服务","40"=>"摄影服务","43"=>"约有趣");

$show_count = 40;
$page_obj->setvar (array() );

$total_count=$mall_direct_obj->get_config_list(true,$where);

$page_obj->set ( $show_count, $total_count );


$list=$mall_direct_obj->get_config_list(false,$where, 'id DESC', $page_obj->limit());

foreach($list as $k=>$val){
	$list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);
 	$list[$k]['type_name'] = $type_name_arr[$val['type_id']];

    $goods_result = $task_goods_obj->get_goods_info_by_goods_id($val['goods_id']);

    $list[$k]['goods_name'] = $goods_result['data']['default_data']['titles']['value'];

    $url = "http://www.yueus.com/goods/".$val['goods_id'].'_'.$val['id'];
    $list[$k]['qrcode_img'] =$activity_code_obj->get_qrcode_img($url);

    $list[$k]['web_url'] = urlencode('http://yp.yueus.com/mall/user/order/confirm.php?goods_id='.$val['goods_id'].'&direct_order_id='.$val['id']);
    $list[$k]['app_url'] = urlencode('yueyue://goto?type=inner_app&pid=1220102&goods_id='.$val['goods_id'].'_'.$val['id']);
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('list', $list);
$tpl->assign('type_id', $type_id);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();
?>