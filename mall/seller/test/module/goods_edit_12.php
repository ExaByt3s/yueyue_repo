<?php
/*
 *
 * //商品编辑模块
 *
 *
 *
 *
 */


//添加服务的时候，初始化认证时候的地区信息
if($action=="add")
{

    $location_res = $mall_service_check_obj->get_loation_id_and_place($yue_login_id);
    $certificate_location_id = $location_res["location_id"];
    $certificate_studio_place = $location_res["studio_place"];
    //特殊处理地区location_id
    $page_data["default_data"]["location_id"]['province'] = substr($certificate_location_id,0,6);//初始化地区
    $page_data["default_data"]["location_id"]['value'] = $certificate_location_id;//初始化地区
    $page_data["system_data"]["320722549d1751cf3f247855f937b982"]["value"] = $certificate_studio_place;//初始化地址
}
else
{
    //特殊处理地区location_id
    $page_data["default_data"]["location_id"]['province'] = substr($page_data["default_data"]['location_id']['value'],0,6);
}



//处理规格价格
$new_price_list = price_list_contruct($page_data,"1c383cd30b7c298ab50293adfecb7b18");


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


?>