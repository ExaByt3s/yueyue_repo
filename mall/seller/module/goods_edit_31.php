<?php
/*
 *
 * //商品编辑模块
 *
 *
 *
 *
 */


//配置人数数组
/*$person_num_array = array(
    array("num"=>1),
    array("num"=>2),
    array("num"=>3),
    array("num"=>4),
    array("num"=>5)
);
//处理结构
$tpl->assign("person_num_array",$person_num_array);*/


//处理规格价格
$new_price_list = price_list_contruct($page_data,"d09bf41544a3365a46c9077ebb5e35c3");
//根据分类拆分价钱数组
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




//对风格类进行数据拆分处理
$shangye_array = array("9f61408e3afb633e50cdf1b20de6f466","d1f491a404d6854880943e5c3cd9ca25","9b8619251a19057cff70779273e95aa6");
$taobao_array = array("72b32a1f754ba1c09b3695e0cb6cde7f");
$style_name_arr = array(
    array("style_name"=>"约拍类"),
    array("style_name"=>"商业类"),
    array("style_name"=>"淘宝类")
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


//系统消息
$system_msg = "您的【".$page_title."】认证还未通过审核，现在编辑服务只能先放入仓库哦";
//根据审核状态显示提示层
$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id,true);

//系统消息显示
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