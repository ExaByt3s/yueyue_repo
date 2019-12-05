<?php

/**
 * 活动回顾发布
 *
 * 2015-10-30
 *
 * author  星星
 *
 */
include_once 'common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
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
    echo "<script>alert('该商品类型不能添加回顾');window.location.href='./normal_certificate_basic.php';</script>";
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






//获取活动回顾内容
$review_content = $pai_mall_goods_obj->get_activity_review($goods_id);
if(empty($review_content))
{
    $action_name = "发布";
}
else
{
    $action_name = "编辑";
}

$introduce = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$review_content["content"]);


$hide_system_msg = 1;

$page_title = $action_name."该活动回顾";
//相关赋值操作

$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'activity_review.tpl.htm');

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

$tpl->assign("page_title",$page_title);
$tpl->assign("activity_type_name",$activity_type_name);
$tpl->assign("activity_title",$activity_title);
$tpl->assign("introduce",$introduce);
$tpl->assign("hide_system_msg",$hide_system_msg);
$tpl->assign("goods_id",$goods_id);

$tpl->assign("system_msg",$system_msg);

$tpl->output();

?>