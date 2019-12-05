<?php
/**
 * @desc:   �����б�v2
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/10
 * @Time:   9:24
 * version: 2.0
 */
include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
include('common.inc.php');
ini_set('memory_limit', '256M');
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
$user_obj      = POCO::singleton ( 'pai_user_class' );//�û���
$user_icon_obj = POCO::singleton('pai_user_icon_class');//�û�ͼƬ
$model_relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' ); //����������
$mall_order_obj = POCO::singleton ( 'pai_mall_order_class' ); //������
$mall_obj = POCO::singleton('pai_mall_seller_class'); //������
$tpl  = new SmartTemplate("order_org_list_v2.tpl.htm");


$act           = trim($_INPUT['act']);
$nick_name     = trim($_INPUT['nick_name']);
$cellphone     = intval($_INPUT['cellphone']);
$min_date_time = trim($_INPUT['min_date_time']);
$max_date_time = trim($_INPUT['max_date_time']);
$status        = intval($_INPUT['status']);
$org_id        = intval($_INPUT['org_id']);//����ID

if ($org_id <1)
{
    echo "<script type='text/javascript'>window.alert('�Ƿ�����');location.href='org_list_v2.php';</script>";
    exit;
}

$user_arr = array(); //�û�����
//��ѯ�����µĵ���
$where_org_str   = "org_id={$org_id}";
$user_arr  = $model_relate_org_obj->get_model_org_list_by_org_id(false,$where_org_str, '0,99999999', 'id DESC','user_id');
if(!is_array($user_arr)) $user_arr = array();
//��ʼ������
$where_str = "org_user_id={$org_id}";
$setParam = array(); //����
$where_user_str = ''; //�û�����
$user_tmp_str = '';   //user_id��ʱ����
$order_list = array();
$total_price = 0;

$setParam['org_id'] = $org_id;
foreach($user_arr as $key=>$val)
{
    if($key !=0) $user_tmp_str .= ',';
    $user_tmp_str .= $val['user_id'];
}
if(strlen($nick_name) >0)
{
    if(strlen($where_user_str) >0) $where_user_str .= ' AND ';
    $where_user_str .= "nickname LIKE '%".mysql_escape_string($nick_name)."%'";
    $setParam['nick_name'] = $nick_name;
}
if($cellphone >0)
{
    if(strlen($where_user_str) >0) $where_user_str .= ' AND ';
    $where_user_str .= "cellphone='{$cellphone}'";
    $setParam['cellphone'] = $cellphone;
}
if(strlen($user_tmp_str) >0)
{
    if(strlen($where_user_str) >0) $where_user_str .= ' AND ';
    $where_user_str .= "user_id IN ({$user_tmp_str})";
    $user_arr = $user_obj->get_user_list(false,$where_user_str,'user_id DESC','0,99999999','user_id');
    if(!is_array($user_arr)) $user_arr = array();
}
if($status >0)//״̬����
{
    $setParam['status'] = $status;
    $sql_status_str = '';
    foreach($user_arr as $key=>$statusVal)
    {
        if($key !=0) $sql_status_str .= ',';
        $sql_status_str .= $statusVal['user_id'];
    }
    if(strlen($sql_status_str) >0)
    {
        $sql_str = "user_id IN({$sql_status_str}) AND status={$status}";
        $user_arr = $mall_obj->get_seller_store_list(false, $sql_str,'store_id DESC','0,99999999','DISTINCT(user_id) as user_id');
        if(!is_array($user_arr)) $user_arr = array();
    }
    else
    {
        $user_arr = array();
    }
}
//��������һ�½���ʱ���״̬
if(strlen($min_date_time) >0)
{
    $setParam['min_date_time'] = $min_date_time;
}
if(strlen($max_date_time) >0)
{
    $setParam['max_date_time'] = $max_date_time;
}

if(is_array($user_arr) && !empty($user_arr))
{
    $user_str = '';
    foreach($user_arr as $key=>$vo)
    {
        if($key !=0) $user_str .= ',';
        $user_str .= $vo['user_id'];
    }
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "seller_user_id IN ({$user_str})";
    //����ʱ�䴦��
    if(strlen($min_date_time) >0)
    {
        if(strlen($where_str) >0) $where_str .= ' AND ';
        $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') >= '".mysql_escape_string($min_date_time)."'";
    }
    if(strlen($max_date_time) >0)
    {
        if(strlen($where_str) >0) $where_str .= ' AND ';
        $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') <= '".mysql_escape_string($max_date_time)."'";
    }
    $user_list = $mall_order_obj->get_order_list(0, -1, false, $where_str, 'add_time DESC,order_id DESC','0,99999999','DISTINCT(seller_user_id) seller_user_id');
    if(!is_array($user_list)) $user_list = array();
    $tmp_str = '';
    foreach ($user_list as $key_val => $vall)
    {
        $order_list[$key_val]['seller_user_id']   = $vall['seller_user_id'];
        $order_list[$key_val]['org_id']    = $org_id;
        $order_list[$key_val]['icon']      = $user_icon_obj->get_user_icon($vall['seller_user_id'], 32);
        $order_list[$key_val]['thumb']     = $user_icon_obj->get_user_icon($vall['seller_user_id'], 100);
        $order_list[$key_val]['nickname']  = get_user_nickname_by_user_id($vall['seller_user_id']);
        $order_list[$key_val]['cellphone'] = $user_obj->get_phone_by_user_id($vall['seller_user_id']);
        //ʵ�ʽ��
        $tmp_str = "seller_user_id = {$vall['seller_user_id']} AND org_user_id={$org_id}";
        $ret     = $mall_order_obj->get_order_list(0, -1, false, $tmp_str, 'add_time DESC,order_id DESC','0,1','sum(total_amount) as true_budget');
        $order_list[$key_val]['true_budget'] = sprintf('%.2f',$ret[0]['true_budget']);
        //�ܽ��״���
        $order_list[$key_val]['total_count']    = $mall_order_obj->get_order_list(0, -1, true, $tmp_str);
        //�ɹ�����
        $order_list[$key_val]['success_count']  = $mall_order_obj->get_order_list(0, 8, true, $tmp_str);
        $seller_info = $mall_obj->get_seller_info($vall['seller_user_id'],2);
        $order_list[$key_val]['status'] = intval($seller_info['seller_data']['company'][0]['store'][0]['status']);//����״̬
        //�ܽ��
        $total_price += sprintf('%.2f',$order_list[$key_val]['true_budget']);

    }

}
//��������
if ($act == 'export')
{
    if (empty($order_list) || !is_array($order_list))
    {
        echo "<script type='text/javascript'>window.alert('�������ݲ���Ϊ��');history.back();</script>";
        exit;
    }
    $data = array();
    foreach ($order_list as $key => $vo)
    {
        unset($vo['seller_user_id']);
        unset($vo['org_id']);
        unset($vo['icon']);
        unset($vo['thumb']);
        if($vo['status'] ==1)
        {
            $vo['status'] = '�ϼ�';
        }
        else
        {
            $vo['status'] = '�¼�';
        }
        $data[$key] = $vo;
    }
    $fileName = '���������';
    $title = '��������';
    $headArr = array("�ǳ�","�ֻ���","���׽��","���״���","�ɹ�������","״̬");
    getExcel($fileName,$title,$headArr,$data);
    exit;

}
$tpl->assign('total_price',$total_price);
$tpl->assign($setParam);
$tpl->assign('list', $order_list);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();