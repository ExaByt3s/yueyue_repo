<?php
/**
 * @desc:   服务商回复列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/20
 * @Time:   9:56
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'seller_reply_message');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$sendserver_seller_reply_obj = POCO::singleton( 'pai_sendserver_seller_reply_class' );//信息数据
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$page_obj = new show_page();
$show_count = 30;

$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'seller_reply_message_list.tpl.htm' );

$act = trim($_INPUT['act']);
$month = trim($_INPUT['month']) ?trim($_INPUT['month']) : date('Y-m',time()-24*3600);

$start_date = trim($_INPUT['start_date']) ? trim($_INPUT['start_date']) : date('Y-m-d',time()-2*24*3600);
$end_date = trim($_INPUT['end_date']) ? trim($_INPUT['end_date']) : date('Y-m-d',time()-2*24*3600);
$type = isset($_INPUT['type']) ? intval($_INPUT['type']) : 1 ;
$type_id = intval($_INPUT['type_id']);

$receive_id = intval($_INPUT['receive_id']);

$sort = trim($_INPUT['sort']);//排序


$where_str = '1';
$setParam = array();

if(strlen($month) >0)
{
    $setParam['month'] = $month;
    $date = $month.'-01';
}

if(strlen($start_date) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(date_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(strlen($end_date) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(date_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if($receive_id >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "receive_id = {$receive_id}";
    $setParam['receive_id'] = $receive_id;
}
if($type>0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    if($type == 1) {//只记用户信息
        $where_str .= "sender_id>=100000";
    }else{//只记系统信息
        $where_str .= "sender_id<100000";
    }
    $setParam['type'] = $type;
}
$type_list = $type_obj->get_type_cate(2);
if($type_id >0)
{
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
    $setParam['type_id'] = $type_id;
}

$order_by = 'id DESC';

switch($sort)//回复排序选择
{
    case 'five_scale_asc': //5分钟内回复率正序
        $tmp_order = "five_scale ASC,five_sum ASC";
        break;
    case 'five_count_desc': //5分钟内回复数倒序
        $tmp_order = "five_sum DESC,five_scale DESC";
        break;
    case 'five_count_asc': //5分钟内回复数正序
        $tmp_order = "five_sum ASC,five_scale ASC";
        break;
    case 'no_scale_desc':  //无回复率倒序
        $tmp_order = "no_reply_scale DESC,no_reply_sum DESC";
        break;
    case 'no_scale_asc':  //无回复率正序
        $tmp_order = "no_reply_scale ASC,no_reply_sum ASC";
        break;
    case 'no_count_desc':  //无回复率倒序
        $tmp_order = "no_reply_sum DESC,no_reply_scale DESC";
        break;
    case 'no_count_asc': //无回复率正序
        $tmp_order = "no_reply_sum ASC,no_reply_scale ASC";
        break;
        default:
            $tmp_order = "five_scale DESC,five_sum DESC";
            break;

}

if(strlen($sort) >0)
{
    $setParam['sort'] = $sort;
}
if(strlen($tmp_order) >0)
{
    if(strlen($order_by) >0) $order_by = ','.$order_by;
    $order_by = $tmp_order.$order_by;
}
/*if(strlen($sort) >0)
{
    if(strlen($order_by) >0) $order_by = ','.$order_by;
    if($sort =='desc') {
        $order_by = "no_reply_scale DESC,no_reply_sum DESC".$order_by;
    }
    else {
        $order_by = "no_reply_scale ASC,no_reply_sum ASC".$order_by;
    }
    $setParam['sort'] = $sort;
}
else{
    if(strlen($order_by) >0) $order_by = ','.$order_by;
    $order_by = "five_scale DESC,five_sum DESC".$order_by;
}*/

$title = '服务商回复率列表';
if($act == 'export'){//导出数据
    $list = $sendserver_seller_reply_obj->get_info_list(false,$date,$type_id,$where_str,"GROUP BY receive_id","{$order_by}","0,99999999","receive_id,SUM(reply_five) AS five_sum,SUM(reply_ten) AS ten_sum,SUM(reply_twoten) AS twoten_sum,SUM(reply_threeten) AS threeten_sum,SUM(reply_onehour) AS onehour_sum,SUM(reply_tourhour) AS tourhour_sum,SUM(reply_tweehour) AS tweehour_sum,SUM(no_reply) AS no_reply_sum,SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour) AS reply_sum,SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour+no_reply) AS date_sum,(SUM(no_reply)/SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour+no_reply)) AS no_reply_scale,(SUM(reply_five)/SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour+no_reply)) AS five_scale");
    if(!is_array($list)) $list = array();
    $data = array();
    foreach($list as $key=>$val ) {
        $data[$key]['receive_id'] = $val['receive_id'];
        $five_scale = sprintf('%.2f', ($val['five_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['five_sum'] = $val['five_sum']."({$five_scale})";

        $ten_scale = sprintf('%.2f', ($val['ten_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['ten_sum'] = $val['ten_sum']."({$ten_scale})";

        $twoten_scale = sprintf('%.2f', ($val['twoten_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['twoten_sum'] = $val['twoten_sum']."({$twoten_scale})";

        $threeten_scale = sprintf('%.2f', ($val['threeten_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['threeten_sum'] = $val['threeten_sum']."({$threeten_scale})";

        $onehour_scale = sprintf('%.2f', ($val['onehour_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['onehour_sum'] = $val['onehour_sum']."({$onehour_scale})";

        $tourhour_scale = sprintf('%.2f', ($val['tourhour_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['tourhour_sum'] = $val['tourhour_sum']."({$tourhour_scale})";

        $tweehour_scale = sprintf('%.2f', ($val['tweehour_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['tweehour_sum'] = $val['tweehour_sum']."({$tweehour_scale})";

        $no_reply_scale = sprintf('%.2f', ($val['no_reply_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['no_reply_sum'] = $val['no_reply_sum']."({$no_reply_scale})";

        $reply_scale = sprintf('%.2f', ($val['reply_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['reply_sum'] = $val['reply_sum']."({$reply_scale})";
    }
    $headArr  = array("商家ID","	5分钟内回复","10分钟内回复","20分钟内回复","30分钟内回复","1小时内回复","12小时内回复","24小时内回复","无回复","回复百分比");
    Excel_v2::start($headArr,$data,$title);
    exit;
}

$page_obj->setvar($setParam);
$total_count = $sendserver_seller_reply_obj->get_info_list(true,$date,$type_id,$where_str,'','','',"DISTINCT(receive_id)");

$page_obj->set($show_count,$total_count);

$list = $sendserver_seller_reply_obj->get_info_list(false,$date,$type_id,$where_str,"GROUP BY receive_id","{$order_by}",$page_obj->limit(),"receive_id,SUM(reply_five) AS five_sum,SUM(reply_ten) AS ten_sum,SUM(reply_twoten) AS twoten_sum,SUM(reply_threeten) AS threeten_sum,SUM(reply_onehour) AS onehour_sum,SUM(reply_tourhour) AS tourhour_sum,SUM(reply_tweehour) AS tweehour_sum,SUM(no_reply) AS no_reply_sum,SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour) AS reply_sum,SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour+no_reply) AS date_sum,(SUM(no_reply)/SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour+no_reply)) AS no_reply_scale,(SUM(reply_five)/SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour+no_reply)) AS five_scale");

if(!is_array($list)) $list = array();

foreach($list as &$val ) {
    $val['five_scale'] = sprintf('%.2f', ($val['five_sum'] / $val['date_sum']) * 100);
    $val['ten_scale'] = sprintf('%.2f', ($val['ten_sum'] / $val['date_sum']) * 100);
    $val['twoten_scale'] = sprintf('%.2f', ($val['twoten_sum'] / $val['date_sum']) * 100);
    $val['threeten_scale'] = sprintf('%.2f', ($val['threeten_sum'] / $val['date_sum']) * 100);
    $val['onehour_scale'] = sprintf('%.2f', ($val['onehour_sum'] / $val['date_sum']) * 100);
    $val['tourhour_scale'] = sprintf('%.2f', ($val['tourhour_sum'] / $val['date_sum']) * 100);
    $val['tweehour_scale'] = sprintf('%.2f', ($val['tweehour_sum'] / $val['date_sum']) * 100);
    $val['no_reply_scale'] = sprintf('%.2f', ($val['no_reply_sum'] / $val['date_sum']) * 100);
    $val['reply_scale'] = sprintf('%.2f', ($val['reply_sum'] / $val['date_sum']) * 100);

    //日期
    $val['month'] = $month;
    $val['start_date'] = $start_date;
    $val['end_date'] = $end_date;
}

$tpl->assign($setParam);
$tpl->assign('total_count',$total_count);
$tpl->assign('title',$title);
$tpl->assign('list', $list);
$tpl->assign('type_list', $type_list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();