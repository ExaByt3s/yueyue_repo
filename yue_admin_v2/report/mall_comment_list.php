<?php
/**
 * @desc:   评价列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/1
 * @Time:   9:26
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once('common.inc.php');
check_auth($yue_login_id,'mall_comment_list');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$mall_comment_log_obj = POCO::singleton( 'pai_mall_comment_log_class' );//评价类
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$user_obj = POCO::singleton( 'pai_user_class' );
$page_obj = new show_page();
$show_count = 30;

$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT.'mall_comment_list.tpl.htm');


$act = trim($_INPUT['act']);
$month = trim($_INPUT['month']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$seller_user_id = intval($_INPUT['seller_user_id']);
$type_id = intval($_INPUT['type_id']);
$status = intval($_INPUT['status']);//差评还是好评

$where_str = '';
$setParam = array();

$intval_score = 3;
//商品品类选择
$type_list = $type_obj->get_type_cate(2);

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
if($seller_user_id >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "seller_user_id = {$seller_user_id}";
    $setParam['seller_user_id'] = $seller_user_id;
}
if($type_id >0)
{
    $setParam['type_id'] = $type_id;
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
}
if($status >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    if($status == 1) //好评
    {
       $where_str .= "overall_score>{$intval_score}";
    }
    else //差评
    {
        $where_str .= "overall_score<={$intval_score}";
    }
    $setParam['status'] = $status;
}

if($act == 'export')//导出数据
{
    $list = $mall_comment_log_obj->get_comment_for_buyer_list(false,$date,$type_id,$where_str,'','add_time DESC',$page_obj->limit(),"*");
    $data = array();
    foreach($list as $key=>$val)
    {
        $data[$key]['add_time'] = date('Y-m-d',$val['add_time']);
        if($val['is_anonymous'] == 0)
        {
            $data[$key]['buyer_user_id'] = $val['seller_user_id'];
            $buyer_ret = $user_obj->get_user_info($val['buyer_user_id']);
            $data[$key]['buyer_user_name'] = $buyer_ret['nickname'];
            $data[$key]['buyer_user_phone'] = $buyer_ret['cellphone'];
            unset($buyer_ret);
        }
        else
        {
            $data[$key]['buyer_user_id'] = '匿名';
            $data[$key]['buyer_user_name'] = '匿名';
            $data[$key]['buyer_user_phone'] = '匿名';
        }
        $ret = $user_obj->get_user_info($val['seller_user_id']);
        //print_r($ret);
        $data[$key]['seller_user_id'] = $val['seller_user_id'];
        $data[$key]['seller_user_name'] = $ret['nickname'];
        $data[$key]['seller_user_phone'] = $ret['cellphone'];
        $data[$key]['overall_score'] = $val['overall_score'];
        $data[$key]['comment'] = $val['comment'];
        unset($ret);
    }
    unset($list);
    $fileName = '评价列表';
    $headArr  = array("日期","消费者ID","消费者昵称","消费者手机号","商家ID","商家昵称","商家手机号","总体评分","内容");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

$page_obj->setvar($setParam);
$total_count = $mall_comment_log_obj->get_comment_for_buyer_list(true,$date,$type_id,$where_str);
$page_obj->set($show_count,$total_count);
$list = $mall_comment_log_obj->get_comment_for_buyer_list(false,$date,$type_id,$where_str,'','add_time DESC',$page_obj->limit(),"*");

if(!is_array($list)) $list = array();
foreach($list as &$v)
{
    if($v['is_anonymous'] == 0)
    {
        $buyer_ret = $user_obj->get_user_info($v['buyer_user_id']);
        $v['buyer_user_name'] = $buyer_ret['nickname'];
        $v['buyer_user_phone'] = $buyer_ret['cellphone'];
        unset($buyer_ret);
    }
    $ret = $user_obj->get_user_info($v['seller_user_id']);
    //print_r($ret);
    $v['seller_user_name'] = $ret['nickname'];
    $v['seller_user_phone'] = $ret['cellphone'];
    unset($ret);
}

$tpl->assign($setParam);
$tpl->assign('type_list',$type_list);
$tpl->assign('list',$list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();