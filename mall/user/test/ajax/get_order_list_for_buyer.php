<?php
/**
 * 
 *
 * @author hudingwen
 * @version 1.0
 * @copyright , 30 June, 2015
 * @package default
 */

/**
 * 订单列表
 */

include_once('../common.inc.php');

// 没有登录的处理
if(empty($yue_login_id))
{
	$output_arr['code'] = -1;
	$output_arr['msg']  = '尚未登录,非法操作';
	$output_arr['data'] = array();
	exit();
}

// 订单类
$mall_order_obj = POCO::singleton('pai_mall_order_class');

$type_id = intval($_INPUT['type_id']);
$status = intval($_INPUT['status']);
$page = intval($_INPUT['page']);

/**** 分页处理 START ****/
if(empty($page))
{
	$page = 1;
}
// 分页使用的page_count
$page_count = 11;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}
$limit = "{$limit_start},{$page_count}";

/**** 分页处理 END   ****/

/**
* @param int $user_id 买家用户ID
* @param int $type_id 商品品类ID
* @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
* @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
* @param string $order_by 排序
* @param string $limit 一次查询条数
* @param string $fields 查询字段
* @return array	
 */
$ret = $mall_order_obj->get_order_list_for_buyer($yue_login_id, $type_id, $status, $b_select_count=false, $order_by='', $limit, $fields='*');

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = $page_count -1;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	// 删除最后一条数据
	array_pop($ret);
}

if($ret)
{
	$output_arr['code'] = 1;
	$output_arr['msg']  = 'Success';
}
else
{
	$output_arr['code'] = 0;
	$output_arr['msg']  = 'Error';
}

$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);

?>