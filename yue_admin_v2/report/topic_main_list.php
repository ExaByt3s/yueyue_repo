<?php
/**
 * @desc:   专题统计列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/11
 * @Time:   13:41
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'topic_main_list');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_topic_report_class.inc.php');
$topic_report_obj = new pai_topic_report_class();
$page_obj = new show_page();
$show_total = 20;

$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT."topic_main_list.tpl.htm");

$act = trim($_INPUT['act']);
$topic_id = (int)$_INPUT['topic_id'];
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

$where_str = '';
$setParam = array();

if($topic_id >0) $setParam['topic_id'] = $topic_id;
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "date_time >= '".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "date_time <= '".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

if($act == 'export')//导出数据
{
    $data = array();
    $list = $topic_report_obj->get_main_topic_list(false,$topic_id,$where_str,"date_time DESC,PV DESC,UV DESC,id DESC","0,99999999");
    if(!is_array($list)) $list = array();
    foreach($list as $key=>$val)
    {
        $buyer_user_UV = $topic_report_obj->get_order_user_uv_by_topic_id($val['topic_id'],$val['date_time']);
        $data[$key]['date_time'] = $val['date_time'];
        $data[$key]['topic_id'] = $val['topic_id'];
        $data[$key]['PV'] = $val['PV'];
        $data[$key]['UV'] = $val['UV'];
        $data[$key]['buyer_user_UV'] = (int)$buyer_user_UV;
        $data[$key]['buyer_scala'] = sprintf('%.2f',$buyer_user_UV/$val['UV']*100).'%';
    }
    unset($list);
    $fileName = '专题统计列表';
    $headArr  = array("日期","专题ID","浏览PV","浏览UV","消费人数UV","消费比例");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
    exit;
}

$page_obj->setvar($setParam);

$total_count = $topic_report_obj->get_main_topic_list(true,$topic_id,$where_str);
$page_obj->set($show_total,$total_count);
$list = $topic_report_obj->get_main_topic_list(false,$topic_id,$where_str,"date_time DESC,PV DESC,UV DESC,id DESC",$page_obj->limit());
if(!is_array($list)) $list = array();
foreach($list as &$v)
{
    $buyer_user_UV = $topic_report_obj->get_order_user_uv_by_topic_id($v['topic_id'],$v['date_time']);
    $v['buyer_user_UV'] = $buyer_user_UV;
    $v['buyer_scala'] = sprintf('%.2f',$buyer_user_UV/$v['UV']*100);
}


$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();


