<?php

/**
 * �̳Ƕ����б� v2��
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015/7/14
 * @Time:   11:46
 * @version 2.0
 */
//���ú���
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
include('common.inc.php');
check_auth($yue_login_id,'mall_order_list');//Ȩ�޿���
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");//����
$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//������
$type_obj = POCO::singleton('pai_mall_goods_type_class');//��ƷƷ��
$pai_organization_obj = POCO::singleton("pai_organization_class");//������
$mall_comment_obj = POCO::singleton( 'pai_mall_comment_class' );//�̳�����
$mall_certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');//�����������
$user_obj = POCO::singleton('pai_user_class');//�û���
$coupon_obj = POCO::singleton( 'pai_coupon_class' );//�Ż�ȯ��


$page_obj = new show_page();
$show_count = 30;
$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT."mall_order_list.tpl.htm");

$act = trim($_INPUT['act']);
$order_id = intval($_INPUT['order_id']);//����ID
$order_sn = intval($_INPUT['order_sn']);//�������
$type_id  = intval($_INPUT['type_id']);//����ID

$start_sign_time = trim($_INPUT['start_sign_time']);//ǩ����ʼʱ��
$end_sign_time   = trim($_INPUT['end_sign_time']);//ǩ������ʱ��
$start_close_time= trim($_INPUT['start_close_time']);//�رտ�ʼʱ��
$end_close_time  = trim($_INPUT['end_close_time']);//�رս���ʱ��
$status = isset($_INPUT['status']) ? intval($_INPUT['status']) : -1;//����״̬(7�ѹرգ�8�����)

$referer = trim($_INPUT['referer']); //��ȡ��Դ

$seller_user_id = intval($_INPUT['seller_user_id']);//�̼�ID


$key=999;
//��ƷƷ��ѡ��
$type_list = $type_obj->get_type_cate(2);
$type_list[$key]['id'] = 20;
$type_list[$key]['name'] = '�渶';
foreach($type_list as $k => &$v)
{
    $v['selected'] = $type_id==$v['id'] ? true : false;
}

$where_str = '';
$setParam  = array();

//�ඩ��ID
$order_list = trim($_INPUT['order_list']);
if(strlen($order_list)>0)
{
    if (strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "order_id IN ({$order_list})";
    $setParam['order_list'] = $order_list;
}

if($order_id >0)
{
    if (strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "order_id = {$order_id}";
    $setParam['order_id'] = $order_id;
}
if($order_sn >0)
{
    if (strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "order_sn = {$order_sn}";
    $setParam['order_sn'] = $order_sn;
}
if($type_id >0)
{
    $setParam['type_id'] = $type_id;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_sign_time) || preg_match("/\d\d\d\d\d\d\d\d/", $start_sign_time))
{
    $start_sign_time = date('Y-m-d',strtotime($start_sign_time));
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(sign_time,'%Y-%m-%d') >= '".mysql_escape_string($start_sign_time)."'";
    $setParam['start_sign_time'] = $start_sign_time;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_sign_time) || preg_match("/\d\d\d\d\d\d\d\d/", $end_sign_time))
{
    $end_sign_time = date('Y-m-d',strtotime($end_sign_time));
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(sign_time,'%Y-%m-%d') <= '".mysql_escape_string($end_sign_time)."'";
    $setParam['end_sign_time'] = $end_sign_time;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_close_time) || preg_match("/\d\d\d\d\d\d\d\d/", $start_close_time))
{
    $start_close_time = date('Y-m-d',strtotime($start_close_time));
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(close_time,'%Y-%m-%d') >= '".mysql_escape_string($start_close_time)."'";
    $setParam['start_close_time'] = $start_close_time;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_close_time) || preg_match("/\d\d\d\d\d\d\d\d/", $end_close_time))
{
    $end_close_time = date('Y-m-d',strtotime($end_close_time));
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(close_time,'%Y-%m-%d') <= '".mysql_escape_string($end_close_time)."'";
    $setParam['end_close_time'] = $end_close_time;
}
if(strlen($referer)>0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "referer = '".mysql_escape_string($referer)."'";
    $setParam['referer'] = $referer;
}
if($seller_user_id>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "seller_user_id = {$seller_user_id}";
    $setParam['seller_user_id'] = $seller_user_id;
}
/*if($status>=0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "status ={$status}";

}*/
$setParam['status'] = $status;

$total_count = $mall_order_obj->get_order_full_list($type_id,$status,true,$where_str);
if($act == 'export') //��������
{
    $time_start = microtime_float();
    $list = $mall_order_obj->get_order_full_list($type_id,$status,false, $where_str,'sign_time DESC,close_time DESC,order_id DESC',"0,{$total_count}", $fields='*');
    if(!is_array($list)) $list = array();
    $data = array();
    foreach($list as $key=>$val)
    {
        $ref_order_price= '0.00';
        $data[$key]['order_id']    = $val['order_id'];
        $data[$key]['order_sn']    = $val['order_sn'];
        $data[$key]['seller_name'] = $val['seller_name'];
        $data[$key]['seller_user_id'] = $val['seller_user_id'];
        $data[$key]['seller_phone'] = $user_obj->get_phone_by_user_id($val['seller_user_id']);
        //�̼ҵȼ�
        $data[$key]['seller_rating_level'] = get_rating_level_by_seller_user_id($val['seller_user_id'],$val['type_id']);
        $data[$key]['buyer_name']  = $val['buyer_name'];
        $data[$key]['buyer_user_id']  = $val['buyer_user_id'];
        $data[$key]['buyer_phone']  = $user_obj->get_phone_by_user_id($val['buyer_user_id']);
        $data[$key]['order_num'] = $mall_order_obj->get_order_list(0, 8, TRUE,"buyer_user_id={$val['buyer_user_id']}");//���״���
        $data[$key]['type_name']   = $val['type_name'];
        if($val['type_id'] == 42)//����⴦��
        {
            $data[$key]['cat_type_name'] = get_little_cate_by_goods_id($val['activity_list'][0]['activity_id']);
            $data[$key]['goods_id']  = $val['activity_list'][0]['activity_id'];
            $data[$key]['goods_name']  = $val['activity_list'][0]['activity_name'];
            $service_time = date('Y-m-d H:i:s',$val['activity_list'][0]['service_time']);
            $service_address = $val['activity_list'][0]['service_address'];
            $seller_comment_ret = $mall_comment_obj->get_buyer_comment_info($val['order_id'],$val['activity_list'][0]['activity_id']);
            $buyer_comment_ret  = $mall_comment_obj->get_seller_comment_info($val['order_id'],$val['activity_list'][0]['activity_id']);
        } else
        {
            $data[$key]['cat_type_name'] = get_little_cate_by_goods_id($val['detail_list'][0]['goods_id']);
            $data[$key]['goods_id']  = $val['detail_list'][0]['goods_id'];
            $data[$key]['goods_name']  = $val['detail_list'][0]['goods_name'];
            $service_time = date('Y-m-d H:i:s',$val['detail_list'][0]['service_time']);
            $service_address = $val['detail_list'][0]['service_address'];
            $seller_comment_ret = $mall_comment_obj->get_buyer_comment_info($val['order_id'],$val['detail_list'][0]['goods_id']);
            $buyer_comment_ret  = $mall_comment_obj->get_seller_comment_info($val['order_id'],$val['detail_list'][0]['goods_id']);
        }

        $data[$key]['total_amount']= $val['total_amount'];
        $data[$key]['use_coupon_str'] = $val['is_use_coupon'] == 1 ? '��':'��';
        $data[$key]['discount_amount']= $val['discount_amount'];
        if($val['status'] == 8)
        {
            $ref_order_price = $coupon_obj->sum_ref_order_cash_amount_by_oid('mall_order', $val['order_id']);//��ɵ���ͬ�۸�
        }
        $data[$key]['ref_order_price'] = $ref_order_price;
        $data[$key]['pay_time_str'] = $val['pay_time_str'];
        $data[$key]['sign_time'] = $val['sign_time'] != 0 ? date('Y-m-d H:i:s',$val['sign_time']) :'--';
        $data[$key]['service_time_str'] = $service_time;
        $data[$key]['service_address'] = $service_address;
        $data[$key]['status_str2'] = $val['status_str2'];
        $data[$key]['org_user_id'] = $val['org_user_id'] == 0 ? '��' : $pai_organization_obj->get_org_name_by_user_id($val['org_user_id']);
        $audit_name = $mall_certificate_service_obj->get_user_option_name($val['type_id'],$val['seller_user_id']);
        $data[$key]['audit_name'] = strlen($audit_name) >0 ? $audit_name : '��';
        $data[$key]['referer'] = $val['referer'];
        $data[$key]['buyer_comment']  = trim($buyer_comment_ret['comment']) == '' ? '����':$buyer_comment_ret['comment'];
        $data[$key]['seller_comment']  = trim($seller_comment_ret['comment']) == '' ? '����':$seller_comment_ret['comment'];


    }
    unset($list);
    $fileName = '��������';
    //$title    = '���������б�';
    $headArr  = array("����ID","�������","�̼��ǳ�","�̼�ID","�̼��ֻ���","�̼�����","����ǳ�","���ID","����ֻ���","��ҽ��״���","��ƷƷ��","��ƷС����","��ƷID","��Ʒ��","���׶�","�Ƿ�ʹ���Ż�ȯ","�Ż�ȯ���","�Ѳ������","����ʱ��","ǩ��ʱ��","����ʱ��","����ص�","����״̬","�̼һ�������","�̼������Ա","��Դ","��Ҷ��̼�����","�̼Ҷ��������");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
    /*$time_end = microtime_float();
    $time = $time_end - $time_start;
    echo $time;
    exit;*/
}

$page_obj->setvar($setParam);

$page_obj->set($show_count,$total_count);

$list = $mall_order_obj->get_order_full_list($type_id,$status,false, $where_str,'sign_time DESC,close_time DESC,order_id DESC',$page_obj->limit(), $fields='*');


if(!is_array($list)) $list = array();
foreach($list as &$v)
{
    if($v['type_id'] == 42)
    {
         $v['activity_list'][0]['service_address_cut'] = poco_cutstr($v['activity_list'][0]['service_address'], 20, '....');
    }else
    {
        $v['detail_list'][0]['service_address_cut'] = poco_cutstr($v['detail_list'][0]['service_address'], 20, '....');
    }
    $v['org_user_name'] = $v['org_user_id'] == 0 ? '��' : $pai_organization_obj->get_org_name_by_user_id($v['org_user_id']);
    $v['audit_name'] = $mall_certificate_service_obj->get_user_option_name($v['type_id'],$v['seller_user_id']);
}
if($yue_login_id == 100293)
{

    //print_r($list);
}

function get_little_cate_by_goods_id($goods_id)
{
    $mall_goods_obj = POCO::singleton('pai_mall_goods_class');//��Ʒ��
    $goods_id = intval($goods_id);
    if($goods_id <1) return '--';
    $goods_info = $mall_goods_obj->get_goods_info($goods_id);
    if(is_array($goods_info) && !empty($goods_info))
    {
        $system_data = $goods_info['system_data'];
        if(!is_array($system_data)) $system_data = array();
        foreach($system_data as $system_next)
        {
            foreach($system_next['child_data'] as $cate_type)
            {
                if($cate_type['key'] == $system_next['value']) return $cate_type['name'];

            }
        }
    }
    return '--';
}

//��ȡ������Դ
function referer($referer='')
{
    $referer = trim($referer);
    $sql_str ="SELECT referer FROM mall_db.mall_order_tbl WHERE referer !='' GROUP BY referer";
    $ret = db_simple_getdata($sql_str,false,101);
    if(!is_array($ret)) $ret = array();
    if(strlen($referer) >0)
    {
        foreach($ret as &$v)
        {
            $v['selected'] = $referer==$v['referer'] ? true : false;
        }
    }
    return $ret;
}

//��ȡ�û�ABCD�ȼ�
function get_rating_level_by_seller_user_id($seller_user_id,$type_id)
{
    $mall_obj = POCO::singleton('pai_mall_seller_class');
    $rating_result =pai_mall_load_config('seller_rating');//���صȼ��ļ�
    $seller_user_id = (int)$seller_user_id;
    $type_id = (int)$type_id;
    if($seller_user_id <1 || $type_id <1) return '--';
    $result = $mall_obj->get_seller_rating($seller_user_id);
    if(strlen($result) <1) return '--';
    $result = explode(",",$result);
    if(!is_array($result) || empty($result)) return '--';
    $rating_level = 0;
    foreach($result as $key=>$v)
    {
        list($type_val,$rating_level_val) = explode('-',$v);
        if($type_id == $type_val)
        {
            $rating_level = $rating_level_val;
            break;
        }
    }
    if($type_id <1 || $rating_level <0) return '--';
    return $rating_result[$type_id][$rating_level]['text'];
}


$tpl->assign('referer_list',referer($referer));

$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign('type_list', $type_list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
