<?php
/*
 *
 * //商品编辑模块
 *
 *
 *
 *
 */




//处理规格价格
//$new_price_list = price_list_contruct($page_data,"d395771085aab05244a4fb8fd91bf4ee");


//特殊处理地区location_id
$page_data["default_data"]["location_id"]['province'] = substr($page_data["default_data"]['location_id']['value'],0,6);


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


//配置页面模板显示内容,配置样式
$config_page_arr = array(
    array("（兴趣教学/手艺指导）","10px","1"),
    array("（原创设计/手工作品）","210px","2"),
    array("（技能服务/商务合作）","410px","3")

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