<?php
/**
 * @desc:   发送列表页
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/7
 * @Time:   11:12
 * version: 1.0
 */
include('common.inc.php');
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");//地区文件
$send_message_log_v2_obj = POCO::singleton( 'pai_send_message_log_v2_class' ); //发送商家和买家的类
$user_obj = POCO::singleton('pai_user_class');
$page_obj = new show_page();
$show_total = 30;

$tpl = new SmartTemplate("send_mall_message_list.tpl.htm");

$role = trim($_INPUT['role']);
$add_id = intval($_INPUT['add_id']);
$type = trim($_INPUT['type']);
$status = isset($_INPUT['status']) ? intval($_INPUT['status']):-1;

$where_str = '';
$setParam = array();

if(strlen($role) >0) $setParam['role'] = $role;
if($add_id >0) $setParam['add_id'] = $add_id;
if(strlen($type) >0) $setParam['type'] = $type;
if($status >=0) $setParam['status'] = $status;

$page_obj->setvar($setParam);
$total_count = $send_message_log_v2_obj->get_info_list(true,$role,$add_id,$type,$status,$where_str);
$page_obj->set($show_total,$total_count);

$list = $send_message_log_v2_obj->get_info_list(false,$role,$add_id,$type,$status,$where_str,'add_time DESC,id DESC', $page_obj->limit(), $fields = '*');
if(!is_array($list)) $list = array();

foreach($list as &$v)
{
    $v['city'] =  get_poco_location_name_by_location_id ($v['location_id']);
    $v['send_name']  = get_user_nickname_by_user_id($v['add_id']);
    $v['desc']       = poco_cutstr($v['content'], 20, '....');
    $v['card_text1'] = poco_cutstr($v['card_text1'], 20, '....');
}

$tpl->assign($setParam);
$tpl->assign('total_count',$total_count);
$tpl->assign('list',$list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();