<?php
/**
 * 获得搜索标签
 */
include_once 'config.php';


$mall_order_obj = POCO::singleton('pai_mall_order_class');
$type_id = intval($_INPUT['type_id']);
$status = intval($_INPUT['status']);
$page = intval($_INPUT['page']);
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
$goods_type = ($type_id != 42) ? 'normal' : 'activity';

/**** 分页处理 END   ****/
$list =array();
$activity_code_obj = POCO::singleton('pai_activity_code_class');

if($goods_type == 'normal')
{
    /**
     * 获取买家服务订单列表
     * @param int $user_id 买家用户ID
     * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
     * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
     * @param string $order_by 排序
     * @param string $limit 一次查询条数
     * @param string $fields 查询字段
     * @param int $is_buyer_comment [买家是否已评价]
     * @return array
     */
    $ret = $mall_order_obj->get_order_list_by_detail_for_buyer($yue_login_id, $type_id, $status, false, 'order_id DESC', $limit,'*');

    foreach ($ret as $key => $value) 
    {
        $btn_action = btn_action($value['status'], $value['is_buyer_comment'],$value['order_sn'],$goods_type);
        $ret[$key]['btn_action'] = $btn_action; 
        
        $code_list = $ret[$key]['code_list'];
        
        foreach($code_list as $c_key => $c_value)
        {
            
            // 二维码图片转换 start 
            $ret[$key]['code_list'][$c_key]['qr_code_url_img'] = $activity_code_obj->get_qrcode_img($c_value['qr_code_url']);
            // 二维码图片转换 end  
            if($_INPUT['print'] == 1)
            {
                print_r($ret[$key]['code_list'][$c_key]['qr_code_url_img']);
            }
        }
        
    }

    
}
elseif ($goods_type == 'activity')
{
    /**
     * 获取买家活动订单列表
     * @param int $user_id 买家用户ID
     * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
     * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
     * @param string $order_by 排序
     * @param string $limit 一次查询条数
     * @param string $fields 查询字段
     * @param int $is_buyer_comment [买家是否已评价]
     * @return array
     */
    $ret = $mall_order_obj->get_order_list_by_activity_for_buyer($yue_login_id, $type_id, $status, false, 'order_id DESC', $limit, '*', -1);

    foreach ($ret as $key => $value) 
    {
        $btn_action = btn_action($value['status'], $value['is_buyer_comment'],$value['order_sn'],$goods_type);
        $ret[$key]['btn_action'] = $btn_action; 
        
        $code_list = $ret[$key]['code_list'];


        $ret[$key]['goods_name'] = $value['activity_name'];
        $ret[$key]['goods_id'] = $value['activity_id'];
        $ret[$key]['goods_images'] = $value['activity_images'];
        $ret[$key]['goods_id'] = $value['activity_id'];
        $ret[$key]['stage_id'] = $value['stage_id'];
        $ret[$key]['prices_type_id'] = $value['prices_type_id'];
        
        foreach($code_list as $c_key => $c_value)
        {
            
            // 二维码图片转换 start 
            $ret[$key]['code_list'][$c_key]['qr_code_url_img'] = $activity_code_obj->get_qrcode_img($c_value['qr_code_url']);
            // 二维码图片转换 end  
            if($_INPUT['print'] == 1)
            {
                print_r($ret[$key]['code_list'][$c_key]['qr_code_url_img']);
            }
        }
        
    }

  
}


// 设置按钮状态
function btn_action($status, $is_buyer_comment,$order_sn,$goods_type) {
    if ($is_buyer_comment == 1) {  // 商家已评价
        return array();
    }
    // 按钮文案
    $action_arr = array(
        0 => '关闭.close|支付.pay',
        1 => '取消订单.cancel',
        2 => '申请退款.refund|出示二维码.sign',
        7 => '删除订单.delete',
        8 => '评价订单.appraise'
    );
    $btn = explode('|', $action_arr[$status]);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
            'order_sn' =>$order_sn,
            'order_type' =>$goods_type
        );
    }
    return $arr;
}

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

$output_arr['list'] = $ret;
$output_arr['has_next_page'] = $has_next_page;
$output_arr['pay_url'] = '../pay/?order_sn=';


mall_mobile_output($output_arr,false);

?>