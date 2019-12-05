<?php

/**
 * 活动报名名单导出
 *
 * 2015-10-30
 *
 * author  星星
 *
 */
include_once 'common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$order_obj = POCO::singleton('pai_mall_order_class');
$user_id = $yue_login_id;


//获取活动ID
$goods_id = (int)$_INPUT["goods_id"];
if(empty($goods_id))
{
    //没有进行商家认证的
    echo "<script>alert('缺少商品ID');window.location.href='./normal_certificate_basic.php';</script>";
    exit;
}

$goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
$type_id = $goods_info['data']['goods_data']['type_id'];
$can_edit_review_arr = array(42);//可添加回顾的类型
if(!in_array($type_id,$can_edit_review_arr))
{
    //没有进行商家认证的
    echo "<script>alert('该商品类型没有报名名单');window.location.href='./normal_certificate_basic.php';</script>";
    exit;
}

$now_goods_user_id = $goods_info['data']['goods_data']["user_id"];
if($user_id!=$now_goods_user_id)
{
    //没有进行商家认证的
    echo "<script>alert('当前商品不属于此登录用户');window.location.href='./normal_certificate_basic.php';</script>";
    exit;
}


//print_r($goods_info);
//获取商品标题跟类型数据处理
$activity_title = $goods_info["data"]["default_data"]["titles"]["value"];
//获取商品类型名称
$activity_type_name = "";
$activity_type_arr = $goods_info["data"]["system_data"]["39059724f73a9969845dfe4146c5660e"]["child_data"];
$activity_type_value = $goods_info["data"]["system_data"]["39059724f73a9969845dfe4146c5660e"]["value"];
foreach($activity_type_arr as $k => $v)
{
    //找到选中的主类型
    if($v["key"]==$activity_type_value)
    {
        $activity_type_name = $v["name"];
        //判断是否有子分类
        if(!empty($v["child_data"]))
        {
            foreach($v["child_data"] as $key => $value)
            {
                if($value["is_select"]==1)
                {
                    $activity_type_name = $activity_type_name."-".$value["name"];
                }
            }

        }
    }
}


//循环出报名名单
$goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
$tmp_price_list = $goods_info['data']['prices_data'];
//print_r($goods_info);
//print_r($tmp_price_list);
$i=0;

foreach($tmp_price_list as $key => $value)
{
    $new_price_list[$i] = $value;
    //获取其对应报名名单
    $tmp_join_list = "";
    $tmp_join_list = $order_obj->get_order_list_of_paid_by_stage($goods_id, $value["type_id"],false,"","0,99999");
    //构造前置数字序号
    foreach($tmp_join_list as $k => $v)
    {
        $tmp_join_list[$k]["sequence_num"] = (int)$k+1;
    }
    $new_price_list[$i]["join_list"] = $tmp_join_list;
    $new_price_list[$i]["data_mark"] = $i+1;
    //已付款人数
    $section_info = $order_obj->sum_order_quantity_of_paid_by_stage($goods_id, $value["type_id"]);
    $new_price_list[$i]["paid_num"] = (int)$section_info["paid_num"];

    $i++;
}

//print_r($new_price_list);



//to do
//隐藏提示
$hide_system_msg = 1;

$page_title = "报名名单";
//相关赋值操作

$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'activity_join_list.tpl.htm');


// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// 顶部栏
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// 底部
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// 头部bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);


// 底部
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);

$tpl->assign("goods_id",$goods_id);
$tpl->assign("new_price_list",$new_price_list);
$tpl->assign("page_title",$page_title);
$tpl->assign("activity_type_name",$activity_type_name);
$tpl->assign("activity_title",$activity_title);
$tpl->assign("hide_system_msg",$hide_system_msg);
$tpl->assign("system_msg",$system_msg);
$tpl->output();

?>