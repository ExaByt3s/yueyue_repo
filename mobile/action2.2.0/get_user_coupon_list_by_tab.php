<?php
/**
 * 获得搜索标签
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$coupon_obj = POCO::singleton('pai_coupon_class');

$tab = $_INPUT['tab'];
$user_id = $_INPUT['user_id'];

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


if( $tab=='used' )
{
	$order_by = 'used_time DESC,coupon_id desc';
}
elseif( $tab=='expired' )
{
	$order_by = 'end_time DESC,coupon_id desc';
}
else
{
	$order_by = 'give_time DESC,coupon_id desc';
}

$ret = $coupon_obj -> get_user_coupon_list_by_tab($tab, $yue_login_id, $b_select_count=false, $order_by, $limit);

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 10;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

foreach ($ret as $key => $value)
{
      $ret[$key]['scope_module_type_name'] = '「'.$ret[$key]['scope_module_type_name'].'」';
	  $ret[$key]['scope_order_total_amount'] = $ret[$key]['scope_order_total_amount']*1;
	  $ret[$key]['face_value'] = $ret[$key]['face_value']*1;
	  $ret[$key]['coin'] = '￥';
}



$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret;

mobile_output($output_arr,false);

?>