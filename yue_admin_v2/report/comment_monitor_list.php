<?php
/**
 * @desc:   评价监控
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/1
 * @Time:   11:37
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once('common.inc.php');
check_auth($yue_login_id,'comment_monitor');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$mall_comment_log_obj = POCO::singleton( 'pai_mall_comment_log_class' );//评价类
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$user_obj = POCO::singleton( 'pai_user_class' );
$page_obj = new show_page();
$show_count = 30;
$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT.'comment_monitor_list.tpl.htm');

$act = trim($_INPUT['act']);
$month = trim($_INPUT['month']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$type_id = intval($type_id);

$intval_score = 3;//少于等于3的
$intval_num = 0;//超过3次的用户


$where_str = '';
$setParam = array();
//商品品类选择
$type_list = $type_obj->get_type_cate(2);

if($intval_score >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "overall_score <={$intval_score}";
}
if($intval_num >=0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "overall_score <={$intval_score}";
}


//月份处理
if(!preg_match("/\d\d\d\d-\d\d/", $month))
{
    $month = date('Y-m',time()-24*3600);
}
if(strlen($month) >0)
{
    $date = $month.'-01';
    $setParam['month'] = $month;
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m')='".mysql_escape_string($month)."'";
}

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
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') <='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if($type_id >0)
{
    $setParam['type_id'] = $type_id;
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
}

if($act == 'export')//导出数据
{
    $list = $mall_comment_log_obj->get_comment_for_buyer_list(false,$date,$type_id,$where_str,"GROUP BY seller_user_id HAVING COUNT(seller_user_id)>{$intval_num}",'add_time DESC',"0,99999999","COUNT(*) AS bad_num,seller_user_id");
     $data = array();
     foreach($list as $key=>$val)
     {
         $data[$key]['seller_user_id'] = $val['seller_user_id'];
         $ret = $user_obj->get_user_info($val['seller_user_id']);
         $data[$key]['seller_user_name'] = $ret['nickname'];
         $data[$key]['seller_user_phone'] = $ret['cellphone'];
         $data[$key]['bad_num'] = $val['bad_num'];
         unset($ret);
     }
    $fileName = '评价监控列表';
    $headArr  = array("商家ID","商家昵称","手机","差评条数");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

$page_obj->setvar($setParam);
$total_count = $mall_comment_log_obj->get_comment_for_buyer_list(true,$date,$type_id,$where_str,'','','',"DISTINCT(seller_user_id)");
$page_obj->set($show_count,$total_count);
$list = $mall_comment_log_obj->get_comment_for_buyer_list(false,$date,$type_id,$where_str,"GROUP BY seller_user_id HAVING COUNT(seller_user_id)>{$intval_num}",'add_time DESC',$page_obj->limit(),"COUNT(*) AS bad_num,seller_user_id");
if(!is_array($list)) $list = array();

foreach($list as &$v)
{
    $ret = $user_obj->get_user_info($v['seller_user_id']);
    $v['seller_user_name'] = $ret['nickname'];
    $v['seller_user_phone'] = $ret['cellphone'];
    $v['month'] = $month;
}


$tpl->assign($setParam);
$tpl->assign('type_list',$type_list);
$tpl->assign('list',$list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();