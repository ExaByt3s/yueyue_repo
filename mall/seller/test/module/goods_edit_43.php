<?php
/*
 *
 * //��Ʒ�༭ģ��
 *
 *
 *
 *
 */




//������۸�
//$new_price_list = price_list_contruct($page_data,"d395771085aab05244a4fb8fd91bf4ee");


//���⴦�����location_id
$page_data["default_data"]["location_id"]['province'] = substr($page_data["default_data"]['location_id']['value'],0,6);


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


//����ҳ��ģ����ʾ����,������ʽ
$config_page_arr = array(
    array("����Ȥ��ѧ/����ָ����","10px","1"),
    array("��ԭ�����/�ֹ���Ʒ��","210px","2"),
    array("�����ܷ���/���������","410px","3")

);

foreach($page_data["system_data"]["a8abb4bb284b5b27aa7cb790dc20f80b"]['child_data'] as $key => $value)
{
    $page_data["system_data"]["a8abb4bb284b5b27aa7cb790dc20f80b"]['child_data'][$key]["type_text"] = $config_page_arr[$key][0];
    $page_data["system_data"]["a8abb4bb284b5b27aa7cb790dc20f80b"]['child_data'][$key]["left"] = $config_page_arr[$key][1];
    $page_data["system_data"]["a8abb4bb284b5b27aa7cb790dc20f80b"]['child_data'][$key]["data_mark"] = $config_page_arr[$key][2];
}


/*$cookie_name = "mall_pai_goods_edit_".$type_id;
if(isset($_COOKIE[$cookie_name]))
{
    $hide_system_msg = 1;
}*/

if($yue_login_id==109650)
{
    //print_r($page_data);
}
if($yue_login_id==100004)
{
    //print_r($page_data);
}


?>