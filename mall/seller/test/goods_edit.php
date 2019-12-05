<?php

/**
 * 商品服务操作页（添加或者编辑）
 *
 * 2015-6-17
 *
 * author  星星
 *
 */
include_once 'common.inc.php';


$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;
$mall_obj = POCO::singleton('pai_mall_seller_class');

$goods_id = (int)$_INPUT['goods_id'];
$type_id = (int)$_INPUT['type_id'];


if(!empty($goods_id))
{
    $action="edit";
}
else
{
    $action="add";
}


//查询类型判断模板
$type_id_array = array(3,5,12,31,40,41,42,43);
if($action=="add")
{
    $type_id = (int)$_INPUT['type_id'];

}
else if($action=="edit")
{

    //活动类型需要获取最新副本处理-2015-11-24
    $cache_info = $pai_mall_goods_obj->get_goods_info_by_goods_id($goods_id);//获取缓存
    $type_id = $cache_info['data']['goods_data']['type_id'];
    if($type_id==42)
    {
        $goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id,true);
    }
    else
    {
        $goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
    }
    //活动类型需要获取最新副本处理-2015-11-24

    //$type_id = $goods_info['data']['goods_data']['type_id'];
    //print_r($goods_info);

}

//服务认证判断
$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id,false,"goods");//添加后参数，其他分类通过，约活动也通过
foreach($user_status_list as $key => $value)
{
    if($value['can_add_goods']==1)
    {
        $can_edit_type_id_array[] = $value['type_id'];
    }
}
if(!in_array($type_id,$can_edit_type_id_array))
{
    //没有进行商家认证的
    echo "<script>alert('该服务认证不通过，没有权限编辑该类商品');window.location.href='./normal_certificate_basic.php';</script>";
    //header("location:./normal_certificate_basic.php");
    exit;
}





if(empty($type_id) || !in_array($type_id,$type_id_array))
{
    $type_id = 3;//默认
}

//获取配置的数据
$config_array = pai_mall_load_config('goods_status');


$pc_wap = 'pc/';
/*****2015-10-22编辑处理，切换模板***********/
if($action=="edit" && $type_id==41 && $goods_info['data']['goods_data']['is_show']=="1")
{
    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods_edit_'.$type_id.'_special.tpl.htm');
}
else
{
    //类型id选取不同模板
    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods_edit_'.$type_id.'.tpl.htm');
}
/*****2015-10-22编辑处理，切换模板***********/

//统一根据type_id控制页面名称(2015-10-27)
$page_title = $pai_mall_goods_obj->get_goods_typename_for_type_id($type_id);

if($action=="add")
{
    $type_info = $pai_mall_goods_obj->user_show_goods_data($type_id);
    //print_r($type_info);
    $page_data = $type_info['data'];

}
else if($action=="edit")
{
    //匹配对应数据,对数据进行处理
    $page_data = $goods_info['data'];

    //初始化商品封面图片
    $cover_img = "";
    $cover_img_count = count($page_data['goods_data']['img'])-1;
    foreach($page_data['goods_data']['img'] as $key => $value)
    {
        if($key<$cover_img_count)
        {
            $cover_img .= $value['img_url'].",";
        }
        else
        {

            $cover_img .= $value['img_url'];
        }
    }

    //价格取整2015-7-7
    /*if($page_data['default_data']['prices']['value'])
    {
        $page_data['default_data']['prices']['value'] = (int)$page_data['default_data']['prices']['value'];

    }*/



    $tpl->assign("cover_img",$cover_img);

    //处理编辑器的特殊字符
    $page_data['default_data']['content']['value'] = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$page_data['default_data']['content']['value']);


}


//根据类型选择不同的代码段
$include_once_url = "module/goods_edit_".$type_id.".php";
//不同分类的数据处理
if(file_exists($include_once_url))
{

    include_once $include_once_url;
    //echo "文件存在";
    //exit;
}
else
{
    echo "文件不存在";
    exit();
}




//处理结构的公共函数
/*
 * //处理符合分类数据结构规格价钱的函数
 *
 * @param array $source_data //源数据
 * @param array $type_key //匹配的key
 *
 */


function price_list_contruct($source_data,$type_key)
{
    foreach($source_data['prices_data'][$type_key]['child_data'] as $key => $value)
    {
        $new_price_list[$key]['id'] = $value['id'];
        $new_price_list[$key]['key'] = $value['key'];
        $new_price_list[$key]['name'] = $value['name'];
        $new_price_list[$key]['value'] = $source_data['prices_data_list'][$key]['value'];
        if($new_price_list[$key]['value']=="0")
        {
            $new_price_list[$key]['value'] = "";
        }
    }
    return $new_price_list;
}
/*
 * //处理符合分类数据结构子分类的函数
 *
 * @param array $source_data //源数据
 * @param array $type_key //匹配的key
 * @param array $action //数组原始操作 edit还是add
 *
 */
//
function children_type_contruct($source_data,$type_key,$action)
{
    //课程分类
    $show_J_child_title = 1;//默认显示子分类
    $has_choose = false;
    $target_detail_data_arr = array();

    $target_type_arr = $source_data['system_data'][$type_key]['child_data'];//类型分层数组
    $target_type_match_value = $source_data['system_data'][$type_key]['value'];//匹配的值

    //构造大分类跟子分类结构，显示关系
    $i=0;
    foreach($target_type_arr as $key => $value)
    {

        //主分类跟子分类匹配
        if(!empty($value['child_data']))
        {
            $target_detail_data_arr[$i]['child_data'] = $value['child_data'];
            //匹配对应关系
            $target_detail_data_arr[$i]['parent_id'] = $target_type_arr[$key]['id'];
            $target_detail_data_arr[$i]['parent_key'] = $target_type_arr[$key]['key'];

            if($action=="edit")
            {
                //匹配中并且有子分类
                if($target_type_match_value==$value["key"] && !empty($value['child_data']))
                {
                    $target_detail_data_arr[$i]['match'] = 1;//控制子分类的显示
                }
            }
            else if($action=="add")
            {
                //判断第一个大块有没有子分类
                if(!empty($target_type_arr[0]['child_data']))
                {
                    $target_detail_data_arr[0]['match'] = 1;
                }
                else
                {
                    $target_detail_data_arr[0]['match'] = 0;
                }


            }
            $i++;
        }

        //选中，但是没有子分类
        if($target_type_match_value==$value["key"] && empty($value['child_data']))
        {
            $show_J_child_title = 0;
        }

        //控制出子分类块
        if($target_type_match_value==$value["key"])
        {
            $has_choose = true;
        }

    }

    //是添加，判断默认勾选的是否有子分类
    if($action=="add")
    {
        if(empty($target_type_arr[0]["child_data"]))
        {
            $show_J_child_title = 0;
        }
    }
    //是编辑并且没有选中
    if($action=="edit" && !$has_choose)
    {
        $show_J_child_title = 0;
    }


    $return_array = array($target_detail_data_arr,$show_J_child_title);
    return $return_array;

}


//print_r($photo_detail_data_arr);
//echo $hide_J_child_title;

//共有赋值

$tpl->assign("default_data",$page_data['default_data']);
$tpl->assign("system_data",$page_data['system_data']);
$tpl->assign("show_J_child_title",$show_J_child_title);
$tpl->assign("new_price_list",$new_price_list);


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
$tpl->assign("type_id",$type_id);
$tpl->assign("user_id",$user_id);
$tpl->assign("page_title",$page_title);
$tpl->assign("action",$action);
$tpl->assign("hide_system_msg",$hide_system_msg);
$tpl->assign("system_msg",$system_msg);

$tpl->output();
?>