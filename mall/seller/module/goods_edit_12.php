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
$new_price_list = price_list_contruct($page_data,"1c383cd30b7c298ab50293adfecb7b18");



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


/*$cookie_name = "mall_pai_goods_edit_".$type_id;
if(isset($_COOKIE[$cookie_name]))
{
    $hide_system_msg = 1;
}*/


?>