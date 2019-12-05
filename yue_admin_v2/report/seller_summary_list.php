<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/16
 * @Time:   11:23
 * version: 1.0
 */

include_once('common.inc.php');
check_auth($yue_login_id,'seller_summary_list');//Ȩ�޿���
include_once(YUE_ADMIN_V2_PATH."common/Excel_v2.class.php");//������
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_seller_summary_class.inc.php');

$seller_summary_obj = new pai_seller_summary_class();
$page_obj = new show_page();
$show_count = 30;
$type_obj = POCO::singleton('pai_mall_goods_type_class');//��ƷƷ��
$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'seller_summary_list.tpl.htm' );

$act = trim($_INPUT['act']);
$type_id = (int)$type_id;
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

//��ʼ������
$where_str = '';
$setParam = array();
$type_list = $type_obj->get_type_cate(2); //��ƷƷ��ѡ��


//��������
if($type_id >=0)
{
    //��ƷƷ��ѡ��
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
    $setParam['type_id'] = $type_id;
}

if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))//��ʼʱ��
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "date_time >= '".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))//����ʱ��
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "date_time <= '".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

if($act == 'export')//��������
{
    $data = array();
    $list = $seller_summary_obj->get_seller_summary_list(false,$type_id,$where_str,'date_time DESC,id DESC',"date_time,seller_total,seller_add_date_count,seller_login_count,seller_contact_count,trade_seller_count,be_evaluated_seller_count");
    if(!is_array($list)) $list = array();
    foreach($list as $key=>$v)
    {
        $data[$key]['date_time'] = $v['date_time'];
        $data[$key]['seller_total'] = $v['seller_total'];
        $data[$key]['seller_add_date_count'] = $v['seller_add_date_count'];
        $data[$key]['seller_login_count'] = $v['seller_login_count'];
        $data[$key]['seller_contact_count'] = $v['seller_contact_count'];
        $data[$key]['trade_seller_count'] = $v['trade_seller_count'];
        $data[$key]['be_evaluated_seller_count'] = $v['be_evaluated_seller_count'];
    }
    unset($list);
    $fileName = '�̼��ܼ��б�';
    $headArr  = array("����","��֤�̼�����","���������̼���","���յ�½�̼���","���ջ����̼���","���ս����̼���","���ձ��������̼���");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

$page_obj->setvar($setParam);

$total_count = $seller_summary_obj->get_seller_summary_list(true,$type_id,$where_str);
$page_obj->set($show_count,$total_count);

$list = $seller_summary_obj->get_seller_summary_list(false,$type_id,$where_str,'date_time DESC,id DESC',$page_obj->limit());

if(!is_array($list)) $list = array();

$tpl->assign('type_list', $type_list);
$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();

