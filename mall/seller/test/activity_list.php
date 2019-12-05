<?php

/**
 * 商品列表页
 *
 * 2015-10-30
 *
 * author  星星
 *
 */
include_once 'common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$pai_mall_goods_api_obj = POCO::singleton('pai_mall_api_class');
$order_obj = POCO::singleton('pai_mall_order_class');
$page_obj =POCO::singleton('show_page');
$page_obj->show_last=1;
//$page_obj -> set_pares_url_to_dot_html(true);//静态
//$page_obj -> sethash('#list');
$user_id = $yue_login_id;
$pc_wap = 'pc/';

//判断是否通过了服务认证
if(!$seller_info['seller_data']['profile'][0]['type_id'])
{
    header("location:./normal_certificate_basic.php");
}


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'activity_list.tpl.htm');

//根据参数控制所示数据
$show = $_INPUT['show'];
if(!isset($show))
{
    $show = 1;
}
else
{
    $show_array = array(1,2,3,4);
    if(!in_array($show,$show_array))
    {
        $show = 1;
    }
}

//分页处理
$show_count  = 5;	//每页显示数
$data["action_type"] = $show;


//处理数据分页
$p = (int)$_INPUT['p'];
if($p<=0)
{
    $p = 1;
}
$limit = ($p-1)*$show_count;
$limit_str = "{$limit},{$show_count}";

$goods_list_count = $pai_mall_goods_obj->user_goods_list($user_id,$data,true, $order_by = 'goods_id DESC', $limit_str, $fields = '*');
$goods_list = $pai_mall_goods_obj->user_goods_list($user_id,$data,$b_select_count = false, $order_by = 'goods_id DESC', $limit_str, $fields = '*');


//交互处理，当前页，没有数据，拿第一页数据
if($p>1 && empty($goods_list))
{
    $p = 1;
    $limit = ($p-1)*$show_count;
    $limit_str = "{$limit},{$show_count}";
    $goods_list_count = $pai_mall_goods_obj->user_goods_list($user_id,$data,true, $order_by = 'goods_id DESC', $limit_str, $fields = '*');
    $goods_list = $pai_mall_goods_obj->user_goods_list($user_id,$data,$b_select_count = false, $order_by = 'goods_id DESC', $limit_str, $fields = '*');

}

/**********************************************分页代码**********************************************/


$page_obj->setvar(array('show'=>$show));
$page_obj->set($show_count, $goods_list_count);



//$limit_str  = $page_obj->limit();
//$page_select = str_replace('&nbsp;', '', $page_obj->output_pre10.$page_obj->output_pre.$page_obj->output_page.$page_obj->output_back.$page_obj->output_back10);
$page_select = str_replace('&nbsp;', '', $page_obj->output_pre.$page_obj->output_page.$page_obj->output_back);
$page_select = str_replace('>上一页<', '>&lt;<', $page_select);
$page_select = str_replace('>下一页<', '>&gt;<', $page_select);
$page_select = str_replace("<span class=\"dian-more color2\">···</span>","...",$page_select);



$tpl->assign("page_select",$page_select);	//分页



/**********************************************分页代码**********************************************/


//获取活动列表分类
$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
$type_name_list = $type_obj -> get_type_attribute_cate(0);
foreach($type_name_list as $val)
{
    $type_name[$val['id']] = $val;
}
$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$config_type_list = $task_goods_type_obj->get_type_cate();
foreach($config_type_list as $key => $value)
{
    $type_name_array[$value["id"]] = $value["name"];
}


//处理活动列表，构造相关信息
foreach($goods_list as $key => $value)
{
    $goods_list[$key]['type_name'] = $type_name_array[$value['type_id']];
    $tmp_data_arr = $pai_mall_goods_api_obj->get_goods_id_activity_info($value["goods_id"]);//使用方法查阅列表活动相关的数据
    $goods_list[$key]['total_show'] = $tmp_data_arr["total_show"];
    $goods_list[$key]['ing_show'] = $tmp_data_arr["ing_show"];


    /*if($yue_login_id==100004)
    {
        print_r($tmp_data_arr);
    }*/
    //处理显示副本标题
    if($show==4)//如果是审核列表，特殊处理标题显示
    {
        if(!empty($tmp_data_arr["new_titles"]))
        {
            //判断新旧标题是否相等
            if($value["titles"]!=$tmp_data_arr["new_titles"])
            {
                $goods_list[$key]['show_title_status'] = 1;//不相等展示新旧标题
                $goods_list[$key]['new_titles'] = $tmp_data_arr["new_titles"];
                $goods_list[$key]['old_titles'] = $value["titles"];
            }
            else
            {
                $goods_list[$key]['show_title_status'] = 0;//相等不展示新旧标题
                $goods_list[$key]['new_titles'] = $value["titles"];
            }
        }
        else
        {
            $goods_list[$key]['show_title_status'] = 0;//相等不展示新旧标题
            $goods_list[$key]['new_titles'] = $value["titles"];
        }

    }
    else
    {
        $goods_list[$key]['show_title_status'] = 0;//相等不展示新旧标题
        $goods_list[$key]['new_titles'] = $value["titles"];
    }

    //活动已付款总人数
    $section_info = $order_obj->sum_order_quantity_of_paid_by_activity($value["goods_id"]);
    $goods_list[$key]['paid_num'] = (int)$section_info["paid_num"];


    if($tmp_data_arr["min_price"]==$tmp_data_arr["max_price"])
    {
        $goods_list[$key]['price_construct'] = "￥".$tmp_data_arr["min_price"];
    }
    else
    {
        $goods_list[$key]['price_construct'] = "￥".$tmp_data_arr["min_price"]."-".$tmp_data_arr["max_price"];
    }


    //根据组合情况控制功能按钮显示
        $cur_section_end_status = "";//0：表示全部未结束，1：有一场结束
        $cur_section_join_status = "";//0：未有人报名，1：有人报名
        $tmp_time_list = "";
        //循环场次
        $tmp_time_list = unserialize($value["time_list"]);
        //$goods_list[$key]['time_sel_list'] = $tmp_time_list;
        foreach($tmp_time_list as $k => $v)
        {
            if($v["time_e"]<time())
            {
                $cur_section_end_status = 1;
                break;
            }
            else
            {
                $cur_section_end_status = 0;
            }
        }


        //判断报名情况
        if($tmp_data_arr["ing_show_has_person"]==0)//正在进行的活动场次，是否有人报名中
        {
            $cur_section_join_status = 0;//没有
        }
        else
        {
            $cur_section_join_status = 1;//有
        }

        $goods_list[$key]['cur_section_end_status'] = $cur_section_end_status;
        $goods_list[$key]['cur_section_join_status'] = $cur_section_join_status;

    switch($show)
    {
        case 1:
            //进行中
            if($cur_section_end_status==0)//所有场次未结束
            {
                if($cur_section_join_status==0)//没人报名
                {
                    $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                    $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                    $goods_list[$key]['put_off_show'] = 1;//下架按钮显示
                    $goods_list[$key]['review_show'] = 0;//回顾按钮显示
                    $goods_list[$key]['join_list_show'] = 0;//报名名单按钮显示
                    $goods_list[$key]['test_text'] = "test:所有场次未结束，正在进行的场次--没人报名";//测试显示

                }
                else if($cur_section_join_status==1)//有人报名
                {
                    $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                    $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                    $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                    $goods_list[$key]['review_show'] = 0;//回顾按钮显示
                    $goods_list[$key]['join_list_show'] = 1;//报名名单按钮显示
                    $goods_list[$key]['test_text'] = "test:所有场次未结束，正在进行的场次--有人报名";//测试显示
                }

            }
            else if($cur_section_end_status==1)//有一场结束了
            {
                if($cur_section_join_status==0)//没人报名
                {
                    $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                    $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                    $goods_list[$key]['put_off_show'] = 1;//下架按钮显示
                    $goods_list[$key]['review_show'] = 1;//回顾按钮显示
                    $goods_list[$key]['join_list_show'] = 1;//报名名单按钮显示
                    $goods_list[$key]['test_text'] = "test:有一场以上结束了，正在进行的场次--没人报名";//测试显示
                }
                else if($cur_section_join_status==1)//有人报名
                {
                    $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                    $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                    $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                    $goods_list[$key]['review_show'] = 1;//回顾按钮显示
                    $goods_list[$key]['join_list_show'] = 1;//报名名单按钮显示
                    $goods_list[$key]['test_text'] = "test:有一场以上结束了，正在进行的场次--有人报名";//测试显示
                }
            }

            break;
        case 2:
                //已结束
                $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                $goods_list[$key]['put_off_show'] = 1;//下架按钮显示
                $goods_list[$key]['review_show'] = 1;//回顾按钮显示
                $goods_list[$key]['join_list_show'] = 1;//报名名单按钮显示
                $goods_list[$key]['test_text'] = "test:活动场次所有都结束了";//测试显示

            break;
        case 3:
            //已下架
            if($cur_section_end_status==0)//所有场次未结束
            {

                $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                $goods_list[$key]['put_on_show'] = 1;//上架按钮显示
                $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                $goods_list[$key]['review_show'] = 0;//回顾按钮显示
                $goods_list[$key]['join_list_show'] = 0;//报名名单按钮显示
                $goods_list[$key]['test_text'] = "test:所有场次未结束";//测试显示


            }
            else if($cur_section_end_status==1)//有一场结束了
            {

                $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                $goods_list[$key]['put_on_show'] = 1;//上架按钮显示
                $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                $goods_list[$key]['review_show'] = 1;//回顾按钮显示
                $goods_list[$key]['join_list_show'] = 1;//报名名单按钮显示
                $goods_list[$key]['test_text'] = "test:有一场以上结束了，正在进行的场次--没人报名";//测试显示

            }
            break;
        case 4:
            //待审核
            if($value["edit_status"]==0)//首次提交审核
            {
                if($cur_section_end_status==0)//所有场次未结束
                {

                    $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                    $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                    $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                    $goods_list[$key]['review_show'] = 0;//回顾按钮显示
                    $goods_list[$key]['join_list_show'] = 0;//报名名单按钮显示
                    $goods_list[$key]['test_text'] = "test:首次提交审核,所有场次未结束";//测试显示

                }
            }
            else
            {
                if($cur_section_end_status==0)//所有场次未结束
                {
                    if($cur_section_join_status==0)//没人报名
                    {
                        $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                        $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                        $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                        $goods_list[$key]['review_show'] = 0;//回顾按钮显示
                        $goods_list[$key]['join_list_show'] = 0;//报名名单按钮显示
                        $goods_list[$key]['test_text'] = "test:所有场次未结束，正在进行的场次--没人报名";//测试显示
                    }
                    else if($cur_section_join_status==1)//有人报名
                    {
                        $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                        $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                        $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                        $goods_list[$key]['review_show'] = 0;//回顾按钮显示
                        $goods_list[$key]['join_list_show'] = 1;//报名名单按钮显示
                        $goods_list[$key]['test_text'] = "test:所有场次未结束，正在进行的场次--有人报名";//测试显示
                    }

                }
                else if($cur_section_end_status==1)//有一场结束了
                {
                    if($cur_section_join_status==0)//没人报名
                    {
                        $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                        $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                        $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                        $goods_list[$key]['review_show'] = 1;//回顾按钮显示
                        $goods_list[$key]['join_list_show'] = 1;//报名名单按钮显示
                        $goods_list[$key]['test_text'] = "test:有一场以上结束了，正在进行的场次--有人报名";//测试显示
                    }
                    else if($cur_section_join_status==1)//有人报名
                    {
                        $goods_list[$key]['edit_show'] = 1;//编辑按钮显示
                        $goods_list[$key]['put_on_show'] = 0;//上架按钮显示
                        $goods_list[$key]['put_off_show'] = 0;//下架按钮显示
                        $goods_list[$key]['review_show'] = 1;//回顾按钮显示
                        $goods_list[$key]['join_list_show'] = 1;//报名名单按钮显示
                        $goods_list[$key]['test_text'] = "test:有一场以上结束了，正在进行的场次--有人报名";//测试显示
                    }
                }
            }
            break;
        default:
            break;
    }
    //根据组合情况控制功能按钮显示





}
if($yue_login_id==100004)
{
    //print_r($goods_list);
}

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


$page_title = "活动列表页";

$tpl->assign("show",$show);
$tpl->assign("page_title",$page_title);
$tpl->assign("goods_list",$goods_list);
$tpl->output();

?>