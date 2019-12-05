<?php


include_once('../common.inc.php');

/**
 * 页面接收参数
 */




// 接收参数
$page = intval($_INPUT['page']);

$option_time = $_INPUT['option_time'];





if(empty($page))
{
    $page = 1;
}

// 分页使用的page_count
$page_count = 10;

if($page > 1)
{
    $limit_start = ($page - 1)*($page_count - 1);
}
else
{
    $limit_start = ($page - 1)*$page_count;
}

$limit = "{$limit_start},{$page_count}";


// $time = strtotime(date("Y-m-d",time()));
// $where = "begin_time<={$time} and end_time>={$time}";


//约付
$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');


/**
 * 获取商家订单列表，根据TAB
 * @param int $user_id 商家用户ID
 * @param int $tab today week month all
 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
 * @param string $limit 一次查询条数
 * @param int $is_seller_comment [商家是否已评价]
 * @return array
 */
$ret = $order_payment_obj->get_order_list_by_tab_for_seller($yue_login_id, $option_time, false, $limit);




// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 9;



$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
    array_pop($ret);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret;

// 输出数据
mall_mobile_output($output_arr,false);



?>