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
$return_data = children_type_contruct($page_data,"39059724f73a9969845dfe4146c5660e",$action);
$activity_detail_data_arr = $return_data[0];//对应分类的单选
$show_J_child_title = $return_data[1];

$tpl->assign("activity_detail_data_arr",$activity_detail_data_arr);


//官方账号
$official_login_id_arr = $pai_mall_goods_obj->_is_official_user;
if(in_array($yue_login_id,$official_login_id_arr))//判断是否官方账号控制发布页的一些规则，1：控制场次人数
{
    $is_official_user = 1;
}
else
{
    $is_official_user = 0;
}
$tpl->assign("is_official_user",$is_official_user);



//编辑时候特殊处理地区
if($action=="edit")
{
    //处理地区问题
    $page_data['default_data']['province']['value'] = substr($page_data['default_data']['location_id']['value'],0,6);
    if($page_data['system_data']['00ec53c4682d36f5c4359f4ae7bd7ba1']['value'])
    {
        //活动地址地区
        $page_data['system_data']['province_2']['value'] = substr($page_data['system_data']['00ec53c4682d36f5c4359f4ae7bd7ba1']['value'],0,6);
    }
}


//控制顶栏蓝色banner显示
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




//处理领队DIY结构
$tmp_contact_list = $page_data['contact_data'];
//print_r($tmp_contact_list);
$i=0;
foreach($tmp_contact_list as $key => $value)
{

    //初始化拆分
    $contact_list[$i] = $value;
    $contact_list[$i]['data_mark'] = $i+1;
    $i++;
}

//处理场次结构
$tmp_price_list = $page_data['prices_data'];
$i=0;
foreach($tmp_price_list as $key => $value)
{

    //初始化拆分
    $new_price_list[$i] = $value;
    $new_price_list[$i]["time_s_str"] = $value["time_s"];
    $new_price_list[$i]["time_e_str"] = $value["time_e"];
    $new_price_list[$i]["time_s"] = date("Y-m-d H:i",$value["time_s"]);
    $new_price_list[$i]["time_e"] = date("Y-m-d H:i",$value["time_e"]);
    //根据状态跟人数
    if($new_price_list[$i]["status"]==1)
    {
        //进行中判断人数
        if($new_price_list[$i]["stock_num_total"]!=$new_price_list[$i]["stock_num"])
        {
            $new_price_list[$i]["join_status"] = 1;//表示有人报名了，只能修改人数
            $new_price_list[$i]["edit_attr"] = 'readonly="true"';//可编辑状态
            $new_price_list[$i]["edit_stock_num_attr"] = '';//可编辑人数状态
            $new_price_list[$i]["time_edit_attr"] = 0;//时间不能修改
            $new_price_list[$i]["prices_add_del_btn_show"] = 0;//价格添加删除按钮显示
            $new_price_list[$i]["section_del_btn_show"] = 0;//场次删除按钮显示
            $new_price_list[$i]["section_save_btn_show"] = 1;//场次保存按钮显示
            //测试
            $new_price_list[$i]["section_text"] = ":该场次有人报名了，只能修改人数，不能删除";
        }
        else
        {
            //没人报名
            //判断该场次是否已经过了开始时间
            if($value["time_s"]<time())//已经过了开始时间
            {
                $new_price_list[$i]["join_status"] = 1;//表示有人报名了，只能修改人数
                $new_price_list[$i]["edit_attr"] = 'readonly="true"';//可编辑状态
                $new_price_list[$i]["edit_stock_num_attr"] = '';//可编辑人数状态
                $new_price_list[$i]["time_edit_attr"] = 0;//时间不能修改
                $new_price_list[$i]["prices_add_del_btn_show"] = 0;//价格添加删除按钮显示
                $new_price_list[$i]["section_del_btn_show"] = 0;//场次删除按钮显示
                $new_price_list[$i]["section_save_btn_show"] = 1;//场次保存按钮显示
                //测试
                $new_price_list[$i]["section_text"] = ":该场次没人报名，已经过了开始时间，只能修改人数，不能删除";
            }
            else
            {
                $new_price_list[$i]["join_status"] = 0;//表示没有人报名了，全可以修改
                $new_price_list[$i]["edit_attr"] = '';//可编辑状态
                $new_price_list[$i]["edit_stock_num_attr"] = '';//可编辑人数状态
                $new_price_list[$i]["time_edit_attr"] = 1;//时间允许修改
                $new_price_list[$i]["prices_add_del_btn_show"] = 1;//价格添加删除按钮显示
                $new_price_list[$i]["section_del_btn_show"] = 1;//场次删除按钮显示
                $new_price_list[$i]["section_save_btn_show"] = 1;//场次保存按钮显示
                //测试
                $new_price_list[$i]["section_text"] = ":该场次没人报名，还没有过开始时间，可以都修改，删除";
            }


        }
    }
    else
    {
        $new_price_list[$i]["join_status"] = 2;//表示结束的场次，报名结束了，全不可以修改
        $new_price_list[$i]["edit_attr"] = 'readonly="true"';//可编辑状态
        $new_price_list[$i]["edit_stock_num_attr"] = 'readonly="true"';//可编辑人数状态
        $new_price_list[$i]["time_edit_attr"] = 0;//时间不能修改
        $new_price_list[$i]["prices_add_del_btn_show"] = 0;//价格添加删除按钮显示
        $new_price_list[$i]["section_del_btn_show"] = 0;//场次删除按钮显示
        $new_price_list[$i]["section_save_btn_show"] = 0;//场次保存按钮显示
        //测试
        $new_price_list[$i]["section_text"] = ":该场次已经结束，不能修改跟删除";
    }

    //测试显示当前报名人数
    $new_price_list[$i]["has_joined_num"] = (int)$value["stock_num_total"]-(int)$value["stock_num"];

    $the_lowest_prices = "";
    $j=0;
    foreach($new_price_list[$i]["prices_list_data"] as $k => $v)
    {
        $new_price_list[$i]["prices_list_data"][$k]["data_mark"] = $j+1;
        //根据上层编辑情况控制
        $new_price_list[$i]["prices_list_data"][$k]["edit_attr"] = $new_price_list[$i]["edit_attr"];
        //按钮显示
        $new_price_list[$i]["prices_list_data"][$k]["prices_add_del_btn_show"] = $new_price_list[$i]["prices_add_del_btn_show"];

        //循环获取该场次最低价
        if(empty($the_lowest_prices))
        {
            $the_lowest_prices = $v["prices"];
        }
        else
        {
            if($v["prices"]<$the_lowest_prices)
            {
                $the_lowest_prices = $v["prices"];
            }
        }
        $j++;
    }
    //赋值最低价到上层
    $new_price_list[$i]["the_lowest_prices"] = $the_lowest_prices;

    $section_diy_count = ((int)count($new_price_list[$i]["prices_list_data"]))+1;//获取价格块的计数值

    $new_price_list[$i]['section_diy_count'] = $section_diy_count;
    $new_price_list[$i]['data_mark'] = $i+1;
    $i++;

}
//页面初始化需要的值
$section_count = count($new_price_list);
$section_count = (int)$section_count+1;


if($yue_login_id==100004)
{
    //print_r($new_price_list);
}

//页面初始化地区选择需要的值
$big_checked_status = 0;//全国选中
$small_checked_status = 0;//地区选中
if(!empty($page_data["default_data"]["location_id"]["value"]))
{
    $small_checked_status = 1;
}
else
{
    $big_checked_status = 1;
}
//配置全国，地区数组
$locate_config_arr  = array(
    array("name"=>"全国","locate_value"=>0,"is_select"=>$big_checked_status),
    array("name"=>"地区选择","locate_value"=>1,"is_select"=>$small_checked_status),

);

//拆分经纬度
if(!empty($page_data["default_data"]["lat_lon"]["value"]))
{
    $lat_lon_arr = explode(",",$page_data["default_data"]["lat_lon"]["value"]);
    $lng = $lat_lon_arr[0];
    $lat = $lat_lon_arr[1];
    $tpl->assign("lng",$lng);
    $tpl->assign("lat",$lat);
}

//配置领队数组页面初始化值，计数使用
$diy_count = count($contact_list);
$diy_count = (int)$diy_count+1;
//配置选择时间最小日期，当前日
$now_date = date("Y-m-d",time());


/*******测试使用**********/
//测试系统消息显示
$hide_system_msg = 0;
//根据状态做文字显示
$cur_status = $page_data["goods_data"]["status"];//商品审核，0：未审核，1：通过，2，不通过
$cur_edit_status = $page_data["goods_data"]["edit_status"];//活动修改的审核，活动专有，0：第一次审核，审核中，1：有修改过，非第一次审，审核中，未通过，显示的不是最新提交内容，2：通过审核并更新到最新，3：审核不通过，显示的不是最新

if($cur_edit_status==0)
{
    $system_msg = "test：该活动处于第一次审核，审核中：edit_status：".$cur_edit_status;
}
else if($cur_edit_status==1)
{
    $system_msg = "test：该活动有修改过，非第一次审，审核中，还未通过，显示的不是最新提交内容：edit_status：".$cur_edit_status;
}
else if($cur_edit_status==2)
{
    $system_msg = "test：该活动处于通过审核并更新到最新内容：edit_status：".$cur_edit_status;
}
else if($cur_edit_status==3)
{
    $system_msg = "test：该活动处于审核不通过，之前提交审核修改的内容无效，显示的不是最新提交内容：edit_status：".$cur_edit_status;
}
/*******测试使用**********/




$tpl->assign("locate_config_arr",$locate_config_arr);
$tpl->assign("big_checked_status",$big_checked_status);
$tpl->assign("now_date",$now_date);
$tpl->assign("diy_time",time()."0");//多增一位，跟其他场次ID值一致
$tpl->assign("diy_time_md5",md5(time()."0"));//多增一位，跟其他场次ID值一致
$tpl->assign("diy_count",$diy_count);
$tpl->assign("contact_list",$contact_list);
$tpl->assign("section_count",$section_count);
$tpl->assign("system_msg",$system_msg);



?>