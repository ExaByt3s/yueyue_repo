<?php

/**
 * �༭����ѡ�񣬾�̬
 *
 *
 * 2015-6-17
 *
 *
 *  author    ����
 *
 *
 */

include_once 'common.inc.php';
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods_edit_choose.tpl.htm');
//У���û���ɫ,���̼ұ���
$user_id = $yue_login_id;



$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id,false,"goods");//��Ӻ��������������ͨ����Լ�Ҳͨ��



$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$config_type_list = $task_goods_type_obj->get_type_cate();
foreach($config_type_list as $key => $value)
{
    $tmp_type_name_array[$value["id"]] = $value["name"];
}
//����һ������
$type_name_array = array("model"=>array(31,$tmp_type_name_array["31"]),
    "cameror"=>array(40,$tmp_type_name_array["40"]),
    "studio"=>array(12,$tmp_type_name_array["12"]),
    "teacher"=>array(5,$tmp_type_name_array["5"]),
    "dresser"=>array(3,$tmp_type_name_array["3"]),
    "diet"=>array(41,$tmp_type_name_array["41"]),
    "other"=>array(43,$tmp_type_name_array["43"]),
    "activity"=>array(42,$tmp_type_name_array["42"])
);
$status_array = array("-2"=>"no_record","0"=>"checking","1"=>"pass","2"=>"recheck","-3"=>"no_power");
//$type_id_array = explode(',',$seller_info['seller_data']['profile'][0]['type_id']);



foreach($user_status_list as $key => $value)
{
    $type_list[$key]['name'] = $type_name_array[$value['service_type']][1];
    $type_list[$key]['id'] = $type_name_array[$value['service_type']][0];
    $type_list[$key]['show'] = $status_array[$value['status']];
    //���⴦������ʾ���,ͨ��������Ʒ�൫��δͨ���
    if($type_list[$key]['id']==42)
    {
        if($value['status']=="1" && $value['org_status']=="-2")
        {
            $type_list[$key]['show'] = "can_publish";
        }

        if($value['status']=="1" && $value['org_status']=="0")
        {
            $type_list[$key]['show'] = "checking";
        }

        if($value['status']=="1" && $value['org_status']=="2")
        {
            $type_list[$key]['show'] = "can_publish";
        }
    }
    //���⴦������ʾ���,ͨ��������Ʒ�൫��δͨ���


    if($type_list[$key]['show']=="checking" || $type_list[$key]['show']=="pass" || $type_list[$key]['show']=="can_publish")
    {
        $type_list[$key]['can_click'] = true;
    }
    else
    {
        $type_list[$key]['can_click'] = false;
    }

}

$no_data = true;
foreach($type_list as $k => $v)
{
    if($v['can_click'])
    {
        $no_data = false;
        break;
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




$page_title = "ѡ�����Ʒ��";
$tpl->assign("page_title",$page_title);
$tpl->assign("type_list",$type_list);
$tpl->assign("no_data",$no_data);
//echo $yue_login_id;
 //print_r($type_list);


$tpl->output();

?>