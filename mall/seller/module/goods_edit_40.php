<?php
/*
 *
 * //��Ʒ�༭ģ��
 *
 *
 *
 *
 */




//��������ӷ���
$return_data = children_type_contruct($page_data,"8613985ec49eb8f757ae6439e879bb2a",$action);
$photo_detail_data_arr = $return_data[0];//��Ӧ����ĵ�ѡ
$show_J_child_title = $return_data[1];


$tpl->assign("photo_detail_data_arr",$photo_detail_data_arr);

$new_price_list = price_list_contruct($page_data,"eddea82ad2755b24c4e168c5fc2ebd40");
//����ҳ��ṹ�����������
foreach($new_price_list as $key => $value)
{
    if($value['key']=="06eb61b839a0cefee4967c67ccb099dc")
    {
        $new_price_list[$key]["html_name"] = "������ۣ����:";
        $new_price_list[$key]["intro"] = "�����ײͲ�������װ����ױ���������׷�����Ӵ�����۷����ü۸�������ÿͻ�������Ĵ�������ʵ���ɣ�";
        $new_price_list[$key]["data_role"] = "J_creative";
        $new_price_list[$key]["valid_rule"] = "ap1-20";
    }
    else if($value['key']=="9dfcd5e558dfa04aaf37f137a1d9d3e5")
    {
        $new_price_list[$key]["html_name"] = "������񣨱��:";
        $new_price_list[$key]["intro"] = "�����ײͽ���������������ʵ���ӻ��������׷����뻯ױ������ȣ����ÿͻ����跳���Ա�����ͻ�ױʦ�ȼ���Ͷ�뵽�����У�";
        $new_price_list[$key]["data_role"] = "J_standard";
        $new_price_list[$key]["valid_rule"] = "ap1-20";
    }
    else if($value['key']=="950a4152c2b4aa3ad78bdd6b366cc179")
    {
        $new_price_list[$key]["html_name"] = "�������ѡ�:";
        $new_price_list[$key]["intro"] = "���ײͽ����ṩ���ͻ��������ֵҵ������ᡢ������Ʒ�ȣ����ÿͻ��������ڲ�ͬ����Ӱ����";
        $new_price_list[$key]["data_role"] = "J_enjoyabel";
        $new_price_list[$key]["valid_rule"] = "ap0-20";
    }
}



//print_r($page_data);
//exit();
/*if($yue_login_id==100004)
{
    //print_r($photo_detail_data_arr);
    //echo $show_J_child_title;
    print_r($page_data);
}*/


//ϵͳ��Ϣ
$system_msg = "���ġ�".$page_title."����֤��δͨ����ˣ����ڱ༭����ֻ���ȷ���ֿ�Ŷ";
//�������״̬��ʾ��ʾ��
$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id,true);

//ϵͳ��Ϣ��ʾ
$hide_system_msg = 1;

foreach($user_status_list as $k => $v)
{
    if($v['type_id']==$type_id)
    {
        if($v['status']==0)
        {
            $hide_system_msg = 0;
        }
    }
}


/*$cookie_name = "mall_pai_goods_edit_".$type_id;
if(isset($_COOKIE[$cookie_name]))
{
    $hide_system_msg = 1;
}*/

?>