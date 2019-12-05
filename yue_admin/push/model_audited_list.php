<?php

/*
 * 推送控制器
 *
*/
include_once 'common.inc.php';
include_once 'include/common_function.php';
$tpl = new SmartTemplate("model_audited_list.tpl.htm");
$page_obj = new show_page ();
$user_obj = POCO::singleton ( 'pai_user_class' );
$user_add_obj = POCO::singleton ( 'pai_model_add_class' );
$model_audit_obj = POCO::singleton('pai_model_audit_class');
$hot_model_obj = POCO::singleton('pai_hot_model_class');
$model_card_obj = POCO::singleton('pai_model_card_class');
$model_style_v2_obj = POCO::singleton('pai_model_style_v2_class');
//获取支付宝账号
$user_account_obj = POCO::singleton('pai_bind_account_class');
//订单或者交易
$order_org_obj  = POCO::singleton('pai_order_org_class');
//好评类
$comment_score_obj = POCO::singleton('pai_comment_score_rank_class');
//状态审核一直是审核通过的
$is_approval      = 1;
$phone           = (int)$_INPUT['phone'] ? (int)$_INPUT['phone'] : '';
$user_id         = (int)$_INPUT['user_id'] ? (int)$_INPUT['user_id'] : '';
$min_height      = (int)$_INPUT['min_height'] ? (int)$_INPUT['min_height'] : '';
$max_height      = (int)$_INPUT['max_height'] ? (int)$_INPUT['max_height'] : '';
$style           = $_INPUT['style'] ? $_INPUT['style'] : '';
$min_price       = (int)$_INPUT['min_price'] ? (int)$_INPUT['min_price'] : '';
$max_price       = (int)$_INPUT['max_price'] ? (int)$_INPUT['max_price'] : '';
$hour            = $_INPUT['hour'] ? $_INPUT['hour'] : 2;
$cup             = $_INPUT['cup'] ? $_INPUT['cup'] : '';
$min_num         = (int)$_INPUT['min_num'] ? (int)$_INPUT['min_num'] : '';
$max_num         = (int)$_INPUT['max_num'] ? (int)$_INPUT['max_num'] : '';
$min_trade_price = (int)$_INPUT['min_trade_price'] ? (int)$_INPUT['min_trade_price'] : '';
$max_trade_price = (int)$_INPUT['max_trade_price'] ? (int)$_INPUT['max_trade_price'] : '';
if($_INPUT['act'] == 'approval')
{
    $user_id = $_INPUT['user_id'] ? $_INPUT['user_id'] : 0;
    if (empty($user_id)) 
    {
        echo "<script>alert('非法操作');location.href='model_audit_list.php'</script>";
        exit;
    }
    if($model_audit_obj->update_model(array("is_approval"=>$is_approval,"audit_time"=>time(),"audit_user_id"=>$yue_login_id), $user_id))
    {
        echo "<script>alert('操作成功');location.href='{$_SERVER['HTTP_REFERER']}'</script>";
        exit;
    }
    
}

if($_INPUT['act'] == 'approval')
{
    $user_id = $_INPUT['user_id'];
    $model_audit_obj->update_model(array("is_approval"=>$is_approval,"audit_time"=>time()), $user_id);

    $user_info = $user_obj->get_user_info($user_id);
    $location_id = $user_info["location_id"];

    //推送到热门模特
    //$hot_model_obj->add_model(array("user_id"=>$user_id,"location_id"=>$location_id));
    echo "<script>alert('审核成功');</script>";
    exit;
}



$where  = "1 AND is_approval={$is_approval}";

if($phone)
{
    $where .= " AND cellphone={$phone}";
}

if($user_id)
{
    $where .= " AND user_id={$user_id}";
}
//身材部分查询
$where_model_card_str = "1";
//身高
if ($min_height && $max_height) 
{
    $where_model_card_str .=" AND height BETWEEN {$min_height} AND {$max_height}";
}

//罩杯1
if ($cup) 
{
    $where_model_card_str .=" AND cup = '{$cup}'";
}
//var_dump($where_model_card_str);
if ($where_model_card_str != "1") 
{
    $card_user_id = $model_card_obj->get_model_card_list($b_select_count = false, $where_model_card_str, $order_by = 'user_id DESC', $limit = '0,1000', $fields = 'user_id');
   if (!empty($card_user_id)) 
   {
       $card_user_id = array_change_by_val($card_user_id, 'user_id');
   }
   else
   {
      $card_user_id = array();
   }
}


$where_style_str = "1";
//风格
if ($style) 
{
    $where_style_str .= " AND style like '%{$style}%'";
}

//价格
if ($min_price && $max_price && $hour) 
{
    if ($hour == 2) 
    {
        $where_style_str .= " AND hour='2' AND price BETWEEN {$min_price} AND {$max_price}";
    }
    else if ($hour == 4) 
    {
        $where_style_str .= " AND hour='4' AND price BETWEEN {$min_price} AND {$max_price}";
    }
    else
    {
        $where_style_str .= " AND continue_price BETWEEN {$min_price} AND {$max_price}";
    }
}

//风格判断
if ($where_style_str != "1") 
{
   $style_user_id = $model_style_v2_obj->get_model_style_list($b_select_count = false, $where_style_str, $order_by = 'id DESC', $limit = '0,1000', $fields = 'user_id');
   if (!empty($style_user_id)) 
   {
       $style_user_id = array_change_by_val($style_user_id, 'user_id');
   }
   else
   {
      $style_user_id = array();
   }
   //print_r(array_change_by_val($style_user_id, 'user_id'));exit;
}
//$model_style_v2_obj
 
$where_trade_str ="1";
//交易次数
if ($min_trade && $max_trade) 
{
   $where_trade_str = "SELECT  count(date_id) AS trade_count,to_date_id AS user_id FROM event_db.event_date_tbl WHERE date_status = 'confirm' GROUP BY to_date_id HAVING trade_count BETWEEN {$min_trade} AND {$max_trade}";
   //$where_trade_str = "SELECT  count(date_id) AS trad_count,to_date_id AS user_id FROM event_db.event_date_tbl WHERE trad_count BETWEEN {$min_trade} AND {$max_trade} GROUP BY to_date_id HAVING ";
   $trade_list_id = $order_org_obj->get_user_id_where_str($where_trade_str);
   if (!empty($trade_list_id)) 
   {
       $trade_list_id = array_change_by_val($trade_list_id, 'user_id');
   }
   else
   {
      $trade_list_id = array();
   }
}

$where_num_str = "1";
//好评
if ($min_num && $max_num) 
{
    $tmp_nin_num = $min_num/2;
    $max_tmp_num = $max_num/2;
    //var_dump($tmp_nin_num);
    $where_num_str = "num BETWEEN {$tmp_nin_num} AND {$max_tmp_num}";
    $num_user_id = $comment_score_obj->get_comment_rank_list($b_select_count = false, $where_num_str, $order_by = 'user_id DESC', $limit = '0,1000', $fields = 'user_id');
   if (!empty($num_user_id)) 
   {
       $num_user_id = array_change_by_val($num_user_id, 'user_id');
   }
   else
   {
      $num_user_id = array();
   }

}
$where_trade_price_str = "1";
//交易金额
if ($min_trade_price && $max_trade_price) 
{
    $where_trade_price_str = "SELECT  sum(date_price) AS count_price,to_date_id AS user_id FROM event_db.event_date_tbl WHERE date_status = 'confirm' GROUP BY to_date_id HAVING count_price BETWEEN {$min_trade_price} AND {$max_trade_price}";
    $price_user_id = $order_org_obj->get_user_id_where_str($where_trade_price_str);
    if (!empty($price_user_id)) 
    {
       $price_user_id = array_change_by_val($price_user_id, 'user_id');
    }
    else
    {
      $price_user_id = array();
    }
}
$show_count = 20;
$page_obj->setvar (array('phone' => $phone, 'user_id' => $user_id, 'min_height' => $min_height, 'max_height' => $max_height, 'style' => $style, 'min_price' => $min_price, 'max_price' => $max_price, 'hour' => $hour, 'cup' => $cup, 'min_num' => $min_num, 'max_num' => $max_num, 'min_trade_price' => $min_trade_price, 'max_trade_price' => $max_trade_price));
$list_user_id = $model_audit_obj->get_model_list(false, $where, 'audit_time DESC', '0,1000', 'user_id');
if (!empty($list_user_id)) 
{
    $list_user_id = array_change_by_val($list_user_id, 'user_id');
    if ($where_model_card_str != "1") 
    {
        //身材
        $list_user_id = array_intersect($list_user_id, $card_user_id);
    }
    //风格
    if ($where_style_str != "1") 
    {   
        //echo $where_style_str;
        //var_dump($style_user_id);
        $list_user_id = array_intersect($list_user_id, $style_user_id);
    }
    //交易次数
    if ($where_trade_str != "1") 
    {
        $list_user_id = array_intersect($list_user_id, $trade_list_id);
    }
    //好评
    if ($where_num_str != "1") 
    {
        $list_user_id = array_intersect($list_user_id, $num_user_id);
    }
    //交易金额
    if ($where_trade_price_str != "1") 
    {
        $list_user_id = array_intersect($list_user_id, $price_user_id);
    }
    if (!empty($list_user_id)) 
    {
       $user_id_str = implode(',',$list_user_id);
       $where_str = "user_id IN($user_id_str)";
       $total_count = $model_audit_obj->get_model_list(true,$where_str);
       $page_obj->set ( $show_count, $total_count );
       $list = $model_audit_obj->get_model_list(false, $where_str, 'audit_time DESC,add_time DESC', $page_obj->limit());
       foreach($list as $k=>$val)
       {
         $list[$k]['add_time'] = date("Y-m-d",$val['add_time']);    
         $list[$k]['nickname'] = get_user_nickname_by_user_id($val ['user_id'] );
         $list[$k]['user_thumb'] = str_replace("_86","",get_user_icon($val ['user_id']));
         $list[$k]['user_icon'] = str_replace("_86","_100",get_user_icon($val ['user_id'], 86));
         $list[$k]['audit_name'] = get_user_nickname_by_user_id($val['audit_user_id']);
         $list[$k]['audit_time'] = !empty($val['audit_time']) ? date("Y-m-d", $val['audit_time']) : '';
         $user_info = $user_obj->get_user_info($val ['user_id']);
         $cellphone = $user_info["cellphone"];
         $add_time = date("Y-m-d",$user_info["add_time"]);
         $list[$k]['is_complete'] = $model_card_obj->check_input_is_complete($val ['user_id']);
         $list[$k]['cellphone'] = $cellphone;
         $list[$k]['add_time'] = $add_time;
         $list[$k]['is_set']  = $user_add_obj->get_is_set_by_user_id($val['user_id']);
        }
    }
}

//print_r($list);
/*
*设置标题
*/
$title = "审核通过模特";
$tpl->assign('title', $title);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('total_count', $total_count);
$tpl->assign('list', $list);
$tpl->assign('phone', $phone);
$tpl->assign('user_id', $user_id);
$tpl->assign('min_height', $min_height);
$tpl->assign('max_height', $max_height);
$tpl->assign('min_price', $min_price);
$tpl->assign('max_price', $max_price);
$tpl->assign('min_trade', $min_trade);
$tpl->assign('max_trade', $max_trade);
$tpl->assign('min_num', $min_num);
$tpl->assign('max_num', $max_num);
$tpl->assign('min_trade_price', $min_trade_price);
$tpl->assign('max_trade_price', $max_trade_price);
$tpl->assign('style', $style);
$tpl->assign('hour', $hour);
$tpl->assign('cup', $cup);
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
?>