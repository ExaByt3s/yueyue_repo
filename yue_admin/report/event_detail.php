<?php

/**
 * ��������
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��5��18��
 * @version 1.0
 */
ignore_user_abort(true);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������//������
//���ú���
include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
include('common.inc.php');
$event_order_obj = POCO::singleton('pai_event_order_report_class');
$model_audit_obj = POCO::singleton('pai_model_audit_class');
$user_obj        = POCO::singleton('pai_user_class');
$coupon_obj = POCO::singleton( 'pai_coupon_class' );//�Ż�ȯ��
$tpl = new SmartTemplate("event_detail.tpl.htm");

$page_obj = new show_page();

$show_count = 30;
$act = trim($_INPUT['act']);

$enroll_id     = intval($_INPUT['enroll_id']);//������
$date_id       = intval($_INPUT['date_id']);//Լ��ID
$event_id = intval($_INPUT['event_id']);//����ID
$start_time    = trim($_INPUT['start_time']);
$end_time      = trim($_INPUT['end_time']);
$event_status = intval($_INPUT['event_status']); //ǿ��ȡ��
$type = isset($_INPUT['type'])? intval($_INPUT['type']) :-1;


$date = date('Y-m-d',time());
/* $where_str = "complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m-%d') != '".mysql_escape_string($date)."'
              AND ((event_status='2' OR (event_status='3' AND type='have_part_refunded') AND date_id>0)
              OR (event_status='2' AND enroll_id>0 AND enroll_status=1)) AND pay_status=1";*/
/*$where_str = "complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m-%d') != '".mysql_escape_string($date)."'
              AND ((event_status='2' OR (event_status='3' AND (type='have_part_refunded' OR type='have_all_refunded')) AND date_id>0)
              OR (event_status='2' AND date_id=0)) AND pay_status=1";*/
$where_str = "complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m-%d') != '".mysql_escape_string($date)."'
              AND pay_status=1";
$setParam  = array();

if($enroll_id >0)
{
    if (strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "enroll_id = {$enroll_id}";
    $setParam['enroll_id'] = $enroll_id;
}
if($date_id >0)
{
    if (strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "date_id = {$date_id}";
    $setParam['date_id'] = $date_id;
}
if($event_id >0)
{
    if (strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "event_id = {$event_id}";
    $setParam['event_id'] = $event_id;
}

if(strlen($start_time)>0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(complete_time,'%Y-%m-%d') >= '".mysql_escape_string($start_time)."'";
    $setParam['start_time'] = $start_time;
}
if(strlen($end_time)>0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(complete_time,'%Y-%m-%d') <= '".mysql_escape_string($end_time)."'";
    $setParam['end_time'] = $end_time;
}

if($event_status>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    if($event_status == 8)
    {
        $where_str .="((date_id=0 AND enroll_status =2) OR (date_id>0 AND event_status !='2' AND type !='have_part_refunded'))";
    }
    elseif($event_status == 3)
    {
        $where_str .="type ='have_part_refunded'";
    }else
    {
        $where_str .="event_status='{$event_status}' AND enroll_status !=2";
    }

    $setParam['event_status'] = $event_status;
}
if($type >=0)//����
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    if($type == 1)
    {
        $where_str .="date_id=0";
    }
    else
    {
        $where_str .="date_id>0";
    }
    $setParam['type'] = $type;
}

$total_count = $event_order_obj->get_list(true,$where_str);

//��������
if($act == 'export')
{
    //$time_start = microtime_float();
    /*if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "date_id>0";*/
    $list = $event_order_obj->get_list(false,$where_str,'complete_time DESC,id DESC', "0,{$total_count}");
    if(!is_array($list)) $list = array();
    $data = array();
    foreach($list as $key=>$val)
    {
        $refund_price = 0;
        $event_status = '';
        $ref_order_price = '0.00';
        $data[$key]['complete_time']    = date('Y-m-d H:i:s',$val['complete_time']);
        $data[$key]['id']               = $val['id'];
        $data[$key]['event_id']        = $val['event_id'];
        $data[$key]['enroll_id']        = $val['enroll_id'];
        $data[$key]['date_id']          = $val['date_id'];
        $data[$key]['model_name']       = get_user_nickname_by_user_id($val['to_date_id']);//$val['to_date_id'];//get_user_nickname_by_user_id($val['to_date_id']);;
        $data[$key]['to_date_id']       = $val['to_date_id'];
        $is_audit = '������Ʒ';
        if($val['date_id'] >0)
        {
            $is_approval = $model_audit_obj->get_status_by_user_id($val['to_date_id']);
            if($is_approval == 1)
            {
                $is_audit = 'ͨ��';
            }
            elseif($is_approval == 2)
            {
                $is_audit = '��ͨ��';
            }
            elseif($is_approval == 3)
            {
                $is_audit ='����';
            }
            else
            {
                $is_audit = '�����';
            }
        }
        $data[$key]['is_audit']         = $is_audit;
        $data[$key]['date_style']       = $val['date_style'];
        $user_ret = $user_obj->get_user_info($val['to_date_id']);
        $data[$key]['city']             =get_poco_location_name_by_location_id ($user_ret['location_id']);
        $data[$key]['date_address']     =$val['date_address'];
        $data[$key]['cameraman_name']   = get_user_nickname_by_user_id($val['from_date_id']);//$val['from_date_id'];//get_user_nickname_by_user_id($val['from_date_id']);
        $data[$key]['from_date_id']     = $val['from_date_id'];
        //���Ĵ���
        if(($val['date_id'] ==0 && $val['enroll_status'] !=1) || ($val['date_id']>0 && $val['event_status'] !='2' && $val['type'] !='have_part_refunded'))
        {
            $refund_price = '0.00';
            $event_status = 'ȫ���˿�';
        }
        //Լ�Ĵ���
        elseif($val['date_id'] >0 && $val['type'] =='have_part_refunded')
        {
            $refund_price = sprintf('%.2f',$val['budget']*$val['refund_rep']);
            $event_status = 'ǿ���˿�';
        }
        else
        {
            $refund_price = $val['budget'] * $val['enroll_num'];
            $event_status = '�Ѿ����';
            if($val['date_id'] >0)//Լ��
            {
                $ref_order_price = $coupon_obj->sum_ref_order_cash_amount_by_oid('yuepai', $val['date_id']);//��ɵ���ͬ�۸�
            }else{
                $ref_order_price = $coupon_obj->sum_ref_order_cash_amount_by_oid('waipai', $val['enroll_id']);//��ɵ���ͬ�۸�
            }

        }
        $data[$key]['is_use_coupon']    = $val['is_use_coupon'] == 1 ? '��': '��';
        $data[$key]['discount_price']  = $val['discount_price'];
        $data[$key]['ref_order_price'] = $ref_order_price;
        $data[$key]['price']            = $val['budget'] * $val['enroll_num'];
        //$data[$key]['refund_price']  = $refund_price;
        $data[$key]['event_status']  = $event_status;

        unset($user_ret);
        //unset($user_ret);
    }
    $fileName = '��������';
    $title    = '���������б�';
    $headArr  = array("����","���","����ID","������","Լ��ID","��Ʒ����","��ƷID","��Ʒ״̬","Լ�ķ��","����","Լ�ĵص�","������","������ID","�Ƿ�ʹ���Ż�ȯ","�Ż�ȯ���","�Ѳ������","�����ܽ��","����״̬");
    //getExcel($fileName,$title,$headArr,$data);
    Excel_v2::start($headArr,$data,$fileName);
    /* $time_end = microtime_float();
     $time = $time_end - $time_start;
     echo $time;*/
    exit;
}


$page_obj->setvar($setParam);

$page_obj->set($show_count,$total_count);

$list = $event_order_obj->get_list(false,$where_str,'complete_time DESC,id DESC', $page_obj->limit());

if(!is_array($list)) $list = array();

foreach ($list as $key=>$val)
{
    $refund_price = 0;
    $event_status = 2;
    $list[$key]['complete_time']    = date('Y-m-d H:i:s',$val['complete_time']);
    $list[$key]['price']            = $val['budget'] * $val['enroll_num'];

    //���Ĵ���
    if(($val['date_id'] ==0 && $val['enroll_status'] !=1) || ($val['date_id']>0 && $val['event_status'] !='2' && $val['type'] !='have_part_refunded'))
    {
        $refund_price = '0.00';
        $event_status = 8;
    }
    //Լ�Ĵ���
    elseif($val['date_id'] >0 && $val['type'] =='have_part_refunded')
    {
        $refund_price = sprintf('%.2f',$val['budget']*$val['refund_rep']);
        $event_status = 3;
    }

    $is_audit = '������Ʒ';
    if($val['date_id'] >0)
    {
        $is_approval = $model_audit_obj->get_status_by_user_id($val['to_date_id']);
        if($is_approval == 1)
        {
            $is_audit = 'ͨ��';
        }
        elseif($is_approval == 2)
        {
            $is_audit = '��ͨ��';
        }
        elseif($is_approval == 3)
        {
            $is_audit ='����';
        }
        else
        {
            $is_audit = '�����';
        }
    }
    $list[$key]['is_audit']         = $is_audit;
    $list[$key]['cameraman_name']   = get_user_nickname_by_user_id($val['from_date_id']);
    $list[$key]['model_name']       = get_user_nickname_by_user_id($val['to_date_id']);
    //$list[$key]['refund_price']     = $refund_price;
    $list[$key]['event_status']     = $event_status;

}


$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

?>