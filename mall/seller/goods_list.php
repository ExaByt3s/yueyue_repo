<?php

/**
 * 商品列表页
 *
 * 2015-6-18
 *
 * author  星星
 *
 */
include_once 'common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
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





$show = $_INPUT['show'];
if(!isset($show))
{
    $show = 1;
}
else
{
    $show_array = array(0,1,2);
    if(!in_array($show,$show_array))
    {
        $show = 1;
    }
}




$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods_list.tpl.htm');
$show_count  = 5;	//每页显示数

//$data['status'] = 1;
if(!empty($show))
{
    if($show==1)
    {
        $data['status'] = 7;
    }
    else
    {
        $data['status'] = 1;
    }
    $data['show'] = $show;

}
else
{
    $data['status'] = 6;
}



//处理分页
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

$task_log_obj = POCO::singleton('pai_task_admin_log_class');//获取原因



foreach($goods_list as $key => $value)
{
    $goods_list[$key]['type_name'] = $type_name_array[$value['type_id']];
    $tmp_bill_array = $pai_mall_goods_obj->get_goods_statistical($value['goods_id']);
    $goods_list[$key]['bill_buy_num'] = $tmp_bill_array['bill_buy_num'];
    $goods_list[$key]['bill_not_pay_num'] = (int)$tmp_bill_array['bill_buy_num']-(int)$tmp_bill_array['bill_pay_num'];
    $goods_list[$key]['bill_pay_num'] = $tmp_bill_array['bill_pay_num'];

    //不通过查找原因
    if($value['status']==2)
    {
        $note_arr = $task_log_obj->get_log_by_type_last(array('type_id'=>2007,'action_type'=>2,'action_id'=>$value['goods_id']));
        if(empty($note_arr["note"]))
        {
            $note_arr["note"] = "填入的信息不符合规范";
        }

        $goods_list[$key]['reason'] = $note_arr["note"];



    }


    $prices_list_de = unserialize($value['prices_list']);
    if($prices_list_de)
    {
        if($value['type_id']==41)
        {
            $food_type_name = $pai_mall_goods_obj->get_goods_prices_list($value['goods_id']);
        }

        $i=0;
        foreach($prices_list_de as $key_de => $val_de)
        {
            $food_type_tmp_name = array();
            if($val_de>0)
            {
                if($value['type_id']==41)
                {
                    //美食服务特殊处理规格
                    $food_type_tmp_name = explode("|@|",$food_type_name[$i]['name']);
                    $goods_list[$key]['prices_list_de'] .= '"'.$food_type_tmp_name[0]."￥".$food_type_name[$i]['prices'].'"&nbsp;&nbsp;';
                }
                else
                {
                    $goods_list[$key]['prices_list_de'] .= '"'.$type_name[$key_de]['name']."￥".$val_de.'"&nbsp;&nbsp;';
                }

            }
            $i++;
        }

    }
    else
    {
        $goods_list[$key]['prices_list_de']='"￥'.$value['prices'].'"';
    }
}
/*if($yue_login_id==100004)
{
    print_r($goods_list);
}*/

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


$page_title = "商品列表页";

$tpl->assign("show",$show);
$tpl->assign("page_title",$page_title);
$tpl->assign("goods_list",$goods_list);
$tpl->output();

?>