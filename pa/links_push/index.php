<?php
/**
 * @desc:   引导页
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/17
 * @Time:   9:26
 * version: 1.0
 */
include_once('common.inc.php');
include_once('top.php');
include_once(YUE_PA_CLASS_ROOT.'pai_url_qrcode_class.inc.php');

$pai_url_qrcode_obj = POCO::singleton( 'pai_url_qrcode_class' );
$pai_pa_dt_register_obj = POCO::singleton( 'pai_pa_dt_register_class' );//地推注册表

$page_obj = new show_page();
$show_total = 20;
//if($yue_login_id == 100293) $show_total = 2;

$tpl = new SmartTemplate( TEMPLATES_ROOT.'index.tpl.htm' );

//参数接收
$act = trim($_INPUT['act']);
$user_id = (int)$_INPUT['user_id'];
$url = trim($_INPUT['url']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

//删除
if($act == 'del')
{
    $id = (int)$_INPUT['id'];
    $return_url = $_SERVER['HTTP_REFERER'];
    if($id <1) js_pop_msg_v2('非法操作',true);
    $ret = $pai_url_qrcode_obj->del_info_by_id($id);
    if($ret) js_pop_msg_v2('删除成功',false,$return_url);
    js_pop_msg_v2('删除失败');
}
elseif($act == 'update')//更新数据
{
    $id = (int)$_INPUT['id'];
    $apply_for_name = trim($_INPUT['apply_for_name']);
    if($id <1) die("非法操作");
    $apply_for_name = urldecode(iconv("UTF-8","GBK//IGNORE",$apply_for_name));
    $pai_url_qrcode_obj->update_info($id,array('apply_for_name'=>$apply_for_name));
    exit;
}



//参数拼凑
$where_str = '';
$setParam = array();

if($user_id >0) $setParam['user_id'] = $user_id;
if(strlen($url)>0)  $setParam['url'] = $url;
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

$page_obj->setvar($setParam);
$total_count = $pai_url_qrcode_obj->get_url_qrcode_list(true,$user_id,$url,$where_str);
$page_obj->set($show_total,$total_count);
$list = $pai_url_qrcode_obj->get_url_qrcode_list(false,$user_id,$url,$where_str,"add_time DESC,id DESC",$page_obj->limit());
if(!is_array($list)) $list = array();
foreach($list as &$v)
{
    $v['p_num'] = $pai_pa_dt_register_obj->get_pa_dt_register_list(true,$v['puid']);
}


$tpl->assign('list',$list);
$tpl->assign($setParam);
$tpl->assign('page',$page_obj->output(true));
$tpl->assign('YUE_ADMIN_V2_ADMIN_TEST_HEADER',$_YUE_ADMIN_V2_ADMIN_TEST_HEADER);
$tpl->output();
