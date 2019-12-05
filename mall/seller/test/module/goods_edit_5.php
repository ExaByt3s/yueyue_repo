<?php
/*
 *
 * //商品编辑模块
 *
 *
 *
 *
 */


//配置最小日期，当前日
$now_date = date("Y-m-d",time());

//处理分类子分类
$return_data = children_type_contruct($page_data,"4f6ffe13a5d75b2d6a3923922b3922e5",$action);
$teacher_detail_data_arr = $return_data[0];//对应分类的单选
$show_J_child_title = $return_data[1];

//处理标签子分类
$return_data = children_type_contruct($page_data,"f5f8590cd58a54e94377e6ae2eded4d9",$action);
$teacher_detail_data_arr_label = $return_data[0];//对应分类的单选
$show_J_child_title_label = $return_data[1];


//编辑时候特殊处理
if($action=="edit")
{

    //培训时间时间特殊处理
    $change_time = strtotime($page_data['system_data']['072b030ba126b2f4b2374f342be9ed44']['value']);
    $page_data['system_data']['072b030ba126b2f4b2374f342be9ed44']['value'] = date("Y-m-d",$change_time);

    //报名截止时间特殊处理
    $join_end_time = $page_data['system_data']['bbf94b34eb32268ada57a3be5062fe7d']['value'];
    $explode_arr = explode(" ",$join_end_time);

    $join_end_time_ymd = $explode_arr[0];
    $join_end_time_hour = $explode_arr[1];


}


//招生范围配置数组处理
$location_id_config = pai_mall_load_config('location_id');
if(!empty($page_data['default_data']['location_id']['value']))
{
    //使用英文逗号拆分
    $select_loc_arr = explode(",",$page_data['default_data']['location_id']['value']);
}
else
{
    //如果没有数据默认选中全国
    $select_loc_arr = array(0);
    $page_data['default_data']['location_id']['value'] = 0;
}

foreach($location_id_config as $key => $value)
{
    //判断地区配置数组的地区ID值是否在选中的数组里
    if(in_array($value["location_id"],$select_loc_arr))
    {
        $location_id_config[$key]["is_select"] = 1;
    }
    else
    {
        $location_id_config[$key]["is_select"] = 0;
    }

}





//print_r($explode_arr);
$tpl->assign("location_id_config",$location_id_config);
$tpl->assign("join_end_time_ymd",$join_end_time_ymd);
$tpl->assign("join_end_time_hour",$join_end_time_hour);


$tpl->assign("now_date",$now_date);
$tpl->assign("teacher_detail_data_arr",$teacher_detail_data_arr);
$tpl->assign("teacher_detail_data_arr_label",$teacher_detail_data_arr_label);
$tpl->assign("show_J_child_title_label",$show_J_child_title_label);



//处理时间
//  24小时时间重组
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

//特殊处理开课时间
if($page_data['system_data']["072b030ba126b2f4b2374f342be9ed44"]["value"] == "1970-01-01")
{
    $page_data['system_data']["072b030ba126b2f4b2374f342be9ed44"]["value"] = "";//未填时间时候的初始化处理
}


if($yue_login_id==109650)
{
    //print_r($page_data);
    //print_r($location_id_config);
}

if($yue_login_id==100004)
{
    //print_r($page_data);
    /*print_r($teacher_detail_data_arr);
    print_r($show_J_child_title);
    print_r($teacher_detail_data_arr_label);
    print_r($show_J_child_title_label);*/
}

/*$cookie_name = "mall_pai_goods_edit_".$type_id;
if(isset($_COOKIE[$cookie_name]))
{
    $hide_system_msg = 1;
}*/

?>