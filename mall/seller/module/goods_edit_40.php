<?php
/*
 *
 * //商品编辑模块
 *
 *
 *
 *
 */




//处理分类子分类
$return_data = children_type_contruct($page_data,"8613985ec49eb8f757ae6439e879bb2a",$action);
$photo_detail_data_arr = $return_data[0];//对应分类的单选
$show_J_child_title = $return_data[1];


$tpl->assign("photo_detail_data_arr",$photo_detail_data_arr);

$new_price_list = price_list_contruct($page_data,"eddea82ad2755b24c4e168c5fc2ebd40");
//根据页面结构进行特殊调整
foreach($new_price_list as $key => $value)
{
    if($value['key']=="06eb61b839a0cefee4967c67ccb099dc")
    {
        $new_price_list[$key]["html_name"] = "创意裸价（必填）:";
        $new_price_list[$key]["intro"] = "（此套餐不包含服装、化妆、相册等配套服务。添加创意裸价服务让价格更亲民，让客户感受你的创意和你的实力吧）";
        $new_price_list[$key]["data_role"] = "J_creative";
        $new_price_list[$key]["valid_rule"] = "ap1-20";
    }
    else if($value['key']=="9dfcd5e558dfa04aaf37f137a1d9d3e5")
    {
        $new_price_list[$key]["html_name"] = "标配服务（必填）:";
        $new_price_list[$key]["intro"] = "（此套餐建议根据拍摄需求适当添加基础的配套服务（入化妆、服务等），让客户无需烦恼自备服务和化妆师等即可投入到拍摄中）";
        $new_price_list[$key]["data_role"] = "J_standard";
        $new_price_list[$key]["valid_rule"] = "ap1-20";
    }
    else if($value['key']=="950a4152c2b4aa3ad78bdd6b366cc179")
    {
        $new_price_list[$key]["html_name"] = "尊享服务（选填）:";
        $new_price_list[$key]["intro"] = "此套餐建议提供给客户更多的增值业务（入相册、其他赠品等），让客户享受与众不同的摄影服务）";
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