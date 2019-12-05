<?php
/*
 *
 * //商品编辑模块
 *
 *
 *
 *
 */



//结构特殊处理
$diet_menu_arr = explode("|",$page_data['system_data']["077e29b11be80ab57e1a2ecabb7da330"]['value']);
/*foreach($diet_menu_arr as $key => $value)
{
    $data_mark = ((int)$key)+1;
    $diet_menu_arr_new[$key]["data_value"] = $value;
    $diet_menu_arr_new[$key]["data_mark"] = $data_mark;

}
$diet_menu_arr_len = count($diet_menu_arr);
$diet_menu_arr_new[0]["div_count"] = $diet_menu_arr_len+1;

$tpl->assign("diet_menu_arr_new",$diet_menu_arr_new);*/

/*****2015-9-25兼容数据处理***********/
$diet_menu_value = "";
$diet_menu_len = count($diet_menu_arr);
$diet_menu_split = "\n";
foreach($diet_menu_arr as $key => $value)
{
    if($key==($diet_menu_len-1))
    {
        $diet_menu_split = "";
    }
    $diet_menu_value .= $value.$diet_menu_split;
}
$page_data['system_data']["077e29b11be80ab57e1a2ecabb7da330"]['value'] = $diet_menu_value;
/*****2015-9-25兼容数据处理***********/




//编辑时候特殊处理
if($action=="edit")
{
    //处理美食达人地区问题
    $page_data['default_data']['province']['value'] = substr($page_data['default_data']['location_id']['value'],0,6);
}




//处理DIY结构
$tmp_price_list = $page_data['prices_data'];

//约美食的规格处理
function mall_meishi_replace($old_str)
{
    $preg_str = "/(.*?)（([0-9].*?)人）$/";
    preg_match_all($preg_str,$old_str,$match_str);
    $name_v1 = $match_str[1][0];
    $name_v2 = $match_str[2][0];
    if(empty($match_str[1][0]) && empty($match_str[2][0]))
    {
        $name_v1 = $old_str;
        $name_v2 = "";
    }
    $name_arr = array($name_v1,$name_v2);
    return $name_arr;

}

$i=0;
foreach($tmp_price_list as $key => $value)
{
    $tmp_name_arr = mall_meishi_replace($value['name']);
    //print_r($tmp_name_arr);
    $value['name_v1'] = $tmp_name_arr[0];
    $value['name_v2'] = $tmp_name_arr[1];


    //初始化拆分
    $new_price_list[$i] = $value;
    $new_price_list[$i]['data_mark'] = $i+1;
    $i++;
}
$diy_count = count($new_price_list);
$diy_count = (int)$diy_count+1;





$tpl->assign("diy_time",time()."0");//多增一位，跟其他场次ID值一致
$tpl->assign("diy_count",$diy_count);


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
?>