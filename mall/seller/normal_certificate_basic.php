<?php

/**
 * ������֤ҳ�棬��֤���ѡ���ҳ��
 *
 *
 * 2015-6-18
 *
 *
 *  author    ����
 *
 *
 */

include_once 'common.inc.php';
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'normal_certificate_basic.tpl.htm');


//echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if(empty($yue_login_id))
{
    $r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $r_url = urlencode($r_url);
    echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=".$r_url."';</script>";
    exit;
    //echo "not login error";
    //exit();
}

//У���û���ɫ,���̼ұ���
$user_id = $yue_login_id;
/*
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_name=$seller_info['seller_data']['name'];*/

//��ȡ������֤��Ϣ��֤��Ϣ
$mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
$user_basic_status_list = $mall_basic_check_obj->get_person_status_by_user_id($user_id);
$end_basic_certificate = 0;//�����Ƿ���̼ҷ���ť
if($user_basic_status_list['status']==0)
{

    $basic_certificate = "checking";//�����
    $end_basic_certificate = "1";//�����Ƿ���̼ҷ���ť
}
else if($user_basic_status_list['status']==1)
{
    $basic_certificate = "pass";//ͨ�������
    $end_basic_certificate = "1";//�����Ƿ���̼ҷ���ť
}
else if($user_basic_status_list['status']==2)
{
    if($user_basic_status_list['basic_type']=="person")
    {
        $basic_certificate = "recheck_person";//��Ҫ�����
    }
    else
    {
        $basic_certificate = "recheck_company";//��Ҫ��˾
    }
    $end_basic_certificate = "1";//�����Ƿ���̼ҷ���ť
}
else
{
    $basic_certificate = "no_record";//û�м�¼
}

//print_r($user_basic_status_list);


//����û��ķ�����֤��Ϣ
$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id,true);


/*if($yue_login_id==100004 || $yue_login_id==101615)
{
    print_r($user_status_list);
}*/




//����һ������
$type_name_array = array("model"=>array(31,"ģ�ط���"),
    "cameror"=>array(40,"��Ӱ����"),
    "studio"=>array(12,"Ӱ������"),
    "teacher"=>array(5,"��Ӱ��ѵ"),
    "dresser"=>array(3,"��ױ����"),
    "diet"=>array(41,"��ʳ����"),
    "other"=>array(43,"��������")
);

$status_array = array("-2"=>"no_record","0"=>"checking","1"=>"pass","2"=>"recheck","-3"=>"no_power");
//$type_id_array = explode(',',$seller_info['seller_data']['profile'][0]['type_id']);
//ƥ�����ǰ�˵ķ�����֤������״̬
$service_end_status_array = array();
foreach($user_status_list as $key => $value)
{
    $type_list[$key]['id'] = $type_name_array[$value['service_type']][0];
    $end_status_array[] = $status_array[$value['status']];


}

$can_publish_service = false;
if(in_array("no_power",$end_status_array))
{
    $service_end_status = "no_power";
}
else if(in_array("pass",$end_status_array))
{
    $service_end_status = "pass";
    $can_publish_service = true;
}
else if(in_array("checking",$end_status_array))
{
    $service_end_status = "checking";
    $can_publish_service = true;
}
else if(in_array("recheck",$end_status_array))
{
    $service_end_status = "recheck";
}
else
{
    $service_end_status = "no_record";
}

//ϵͳ��Ϣ
$system_msg = "��ϵͳ֪ͨ�����ʵ���ͷ�����֤����Ҳ��ܿ��������ƷŶ~";

//������Ϣ
$hide_gudie = 0;
if(isset($_COOKIE["normal_certificate_basic_guide"]))
{
    $hide_gudie = 1;
}
//ϵͳ��Ϣ��ʾ
$hide_system_msg = 1;//Ĭ������ʾ��ʾ
/*if(isset($_COOKIE["normal_certificate_basic_system_msg"]))
{
    $hide_system_msg = 1;
}*/

//���ݻ���״̬��������֤״̬��ϵͳ��Ϣ
$basic_certificate_arr = array("recheck_person","recheck_company","no_record");
$service_end_status_arr = array("no_record","recheck");
if(in_array($basic_certificate,$hide_system_msg_arr))
{
    $hide_system_msg = 0;
}
else
{
    //�жϷ�����֤���Ȩ��
    if(in_array($service_end_status,$service_end_status_arr))
    {
        $hide_system_msg = 0;
    }
}


// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// ������
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// �ײ�
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// ͷ��bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);


// �ײ�
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);

$page_title = "������֤ҳ";



$tpl->assign("page_title",$page_title);

//$tpl->assign("basic_type",$user_basic_status_list['basic_type']);//��֤����
$tpl->assign("basic_certificate",$basic_certificate);
$tpl->assign("service_end_status",$service_end_status);
$tpl->assign("can_publish_service",$can_publish_service);
$tpl->assign("system_msg",$system_msg);
$tpl->assign("hide_gudie",$hide_gudie);
$tpl->assign("hide_system_msg",$hide_system_msg);
$tpl->assign("end_basic_certificate",$end_basic_certificate);



$tpl->output();

?>