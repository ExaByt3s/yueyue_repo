<?php
/*
 *
 * //��Ʒ�༭ģ��
 *
 *
 *
 *
 */


//������������
/*$person_num_array = array(
    array("num"=>1),
    array("num"=>2),
    array("num"=>3),
    array("num"=>4),
    array("num"=>5)
);
//����ṹ
$tpl->assign("person_num_array",$person_num_array);*/


//������۸�
$new_price_list = price_list_contruct($page_data,"d09bf41544a3365a46c9077ebb5e35c3");
//���ݷ����ּ�Ǯ����
//print_r($new_price_list);
//exit;

$yuepai_price_array = array("fbd7939d674997cdb4692d34de8633c4","28dd2c7955ce926456240b2ff0100bde","c7e1249ffc03eb9ded908c236bd1996d",);
$shangye_price_array = array("918317b57931b6b7a7d29490fe5ec9f9","c7e1249ffc03eb9ded908c236bd1996d");
$taobao_price_array = array("48aedb8880cab8c45637abc7493ecddd","918317b57931b6b7a7d29490fe5ec9f9","c7e1249ffc03eb9ded908c236bd1996d");

foreach($new_price_list as $k => $v)
{
    if(in_array($v["key"],$yuepai_price_array))
    {
        $new_yuepai_price_array[] = $v;
    }

    if(in_array($v["key"],$shangye_price_array))
    {
        $new_shangye_price_array[] = $v;
    }

    if(in_array($v["key"],$taobao_price_array))
    {
        $new_taobao_price_array[] = $v;
    }
}

$tpl->assign("new_yuepai_price_array",$new_yuepai_price_array);
$tpl->assign("new_shangye_price_array",$new_shangye_price_array);
$tpl->assign("new_taobao_price_array",$new_taobao_price_array);




//�Է����������ݲ�ִ���
$shangye_array = array("9f61408e3afb633e50cdf1b20de6f466","d1f491a404d6854880943e5c3cd9ca25","9b8619251a19057cff70779273e95aa6");
$taobao_array = array("72b32a1f754ba1c09b3695e0cb6cde7f");
$style_name_arr = array(
    array("style_name"=>"Լ����"),
    array("style_name"=>"��ҵ��"),
    array("style_name"=>"�Ա���")
);

foreach($page_data["system_data"]["d9d4f495e875a2e075a1a4a6e1b9770f"]["child_data"] as $k => $v)
{
    if(in_array($v["key"],$shangye_array))
    {
        $shangye_type_array[] = $v;

    }
    else if(in_array($v["key"],$taobao_array))
    {
        $taobao_type_array[] = $v;

    }
    else
    {
        $yuepai_type_array[] = $v;

    }
}
foreach($yuepai_type_array as $key => $value)
{
    $yuepai_type_array[$key]["belong_type"] = "J_type_yuepai";
}
foreach($shangye_type_array as $key => $value)
{
    $shangye_type_array[$key]["belong_type"] = "J_type_shangye";
}
foreach($taobao_type_array as $key => $value)
{
    $taobao_type_array[$key]["belong_type"] = "J_type_taobao";
}

$style_name_arr[0]["radio_list"] = $yuepai_type_array;
$style_name_arr[1]["radio_list"] = $shangye_type_array;
$style_name_arr[2]["radio_list"] = $taobao_type_array;


$tpl->assign("style_name_arr",$style_name_arr);


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


//print_r($page_data);
//print_r($style_name_arr);
//print_r($new_yuepai_price_array);
//print_r($new_shangye_price_array);
//print_r($new_taobao_price_array);

//exit();






?>