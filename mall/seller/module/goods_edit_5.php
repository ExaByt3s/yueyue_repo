<?php
/*
 *
 * //��Ʒ�༭ģ��
 *
 *
 *
 *
 */


//������С���ڣ���ǰ��
$now_date = date("Y-m-d",time());

//��������ӷ���
$return_data = children_type_contruct($page_data,"9fc3d7152ba9336a670e36d0ed79bc43",$action);
$teacher_detail_data_arr = $return_data[0];//��Ӧ����ĵ�ѡ
$show_J_child_title = $return_data[1];

//�༭ʱ�����⴦��
if($action=="edit")
{

    //Ӱ��ʱ�����⴦��
    $change_time = strtotime($page_data['system_data']['072b030ba126b2f4b2374f342be9ed44']['value']);
    $page_data['system_data']['072b030ba126b2f4b2374f342be9ed44']['value'] = date("Y-m-d",$change_time);
}

$tpl->assign("now_date",$now_date);
$tpl->assign("teacher_detail_data_arr",$teacher_detail_data_arr);

//����ʱ��
//  24Сʱʱ������
$hours_time_arr = array();
$time_split_array = array("00","15","30","45");
for ($i=0; $i < 24 ; $i++)
{
    foreach($time_split_array as $k => $v)
    {
        $tmp_value = $i.":".$v;
        array_push( $hours_time_arr,array('hours' => $tmp_value));
    }

}


$tpl->assign("hours_time_arr",$hours_time_arr);



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

//���⴦����ʱ��
if($page_data['system_data']["072b030ba126b2f4b2374f342be9ed44"]["value"] == "1970-01-01")
{
    $page_data['system_data']["072b030ba126b2f4b2374f342be9ed44"]["value"] = "";//δ��ʱ��ʱ��ĳ�ʼ������
}


/*if($yue_login_id==100004)
{
    print_r($page_data);
}*/

/*$cookie_name = "mall_pai_goods_edit_".$type_id;
if(isset($_COOKIE[$cookie_name]))
{
    $hide_system_msg = 1;
}*/

?>