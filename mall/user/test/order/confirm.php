<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 6 July, 2015
 * @package default
 */

/**
 * Define 确认下单
 */

include_once 'config.php';
$pc_wap = 'wap/';

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}


// 读取页面
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'order/confirm.tpl.htm');



// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

// 用户类
$user_obj = POCO::singleton ( 'pai_user_class' );

/******** 接收参数，用于PC跳转过来 ********/ 
$from_pc = intval($_INPUT['from_pc']);
$goods_id = intval($_INPUT['goods_id']);
$stage_id = trim($_INPUT['stage_id']);
if($from_pc == 1)
{
	$prices_type_id = intval($_INPUT['price_type_id']);
	$tpl->assign('prices_type_id',$prices_type_id);
}

/******** 接收参数，用于初始化表单 ********/ 
$direct_order_id = intval($_INPUT['direct_order_id']);
if($direct_order_id)
{
	$mall_direct_obj = POCO::singleton ( 'pai_mall_direct_order_class');
	$direct_data = $mall_direct_obj->get_config_info($direct_order_id);
	
	if($goods_id == $direct_data['goods_id'])
	{
		$service_location_id = intval($direct_data['location_id']);
	
		$prices_type_id = intval($direct_data['price_type']);
		$service_time = date('Y-m-d H:i',$direct_data['service_time']);
		$service_address = trim($direct_data['address']);
		$service_people = intval($direct_data['num']);
		$description = trim($direct_data['message']);
		$quantity = intval($direct_data['num']);
		$is_auto_accept = intval($direct_data['is_auto_accept']);
		$is_auto_sign = intval($direct_data['is_auto_sign']);
	
		$tpl->assign('direct_order_id',$direct_order_id);
		$tpl->assign('is_auto_accept',$is_auto_accept);
		$tpl->assign('is_auto_sign',$is_auto_sign);
	
		if($service_location_id)
		{
			$city_name = get_poco_location_name_by_location_id ( $service_location_id );
	
			$tpl->assign('service_location_id',$service_location_id);
			$tpl->assign('city_name',$city_name);
		}
		if($prices_type_id)
		{
			$tpl->assign('prices_type_id',$prices_type_id);
		}
		if($service_time)
		{
			$tpl->assign('service_time',$service_time);
		}
		if($service_address)
		{
			$tpl->assign('service_address',$service_address);
		}
		if($service_people)
		{
			$tpl->assign('service_people',$service_people);
		}
		if($description)
		{
			$tpl->assign('description',$description);
		}
		if($quantity)
		{
			$tpl->assign('quantity',$quantity);
		}
	}
	

	
}


/******** 接收参数，用于初始化表单 ********/ 
	
	


$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');	

// 商品详情
$goods_info = $task_goods_obj->get_goods_info($goods_id);
$type_id = intval($goods_info['goods_data']['type_id']);
$seller_user_id = intval($goods_info['goods_data']['user_id']);
$service_cellphone = $user_obj->get_phone_by_user_id($yue_login_id); 
 
// 判断是否为活动服务
$goods_type = $type_id == 42 ? 'activity' : 'normal';
$title = $goods_type == 'normal' ? '订单确认页' : '活动报名';

$type_name_list = $type_obj -> get_type_attribute_cate(0);
foreach($type_name_list as $val)
{
	$type_name[$val['id']] = $val;
}
$prices_list_de = $goods_info['goods_prices_list'];

// 规格
$type_arr = array();

$has_type = true;
$shoud_show_price_type_id = true;

// ================== 促销按钮icon构造 ==================
// hudw 2015.10.20
$type_target = 'goods';
$show_param_info = array(
 	'channel_module' => 'mall_order', //必填，固定即mall_order
 	'org_user_id' => $yue_login_id,
 	'location_id' => empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'],
 	'seller_user_id' => $seller_user_id,
 	'mall_type_id' => $type_id,
	'channel_gid' => $goods_id, //商品id必填
);

$promotion_obj = POCO::singleton('pai_promotion_class');
// ================== 促销按钮icon构造 ==================


// ================== 规格构造按钮 ==================
if($prices_list_de)
{
	// 普通服务的时候
	if($goods_type == 'normal')
	{
		$price_tag_type = '类型';

		foreach($prices_list_de as $key_de => $val_de)
		{

			$selected = ($prices_type_id == $val_de['id'])?1:0;
			
			if(empty($prices_type_id))
			{
				$temp_arr = array(
					'price' => $val_de['value'],
					'prices_type_id' => $val_de['id'],
					'price_name' => $val_de['value'].'/'.$val_de['name_val'],
					'selected' => $selected,
					'goods_prices' => $val_de['value']
				);

				// 只有价格大于0才能有规格
				// hudw 2015.9.28
				if($val_de['value']>0)
				{
					$type_arr[] = $temp_arr;
				}
			
				
			}
			else
			{
				if($selected)
				{
					$temp_arr = array(
						'price' => $val_de['value'],
						'prices_type_id' => $val_de['id'],
						'price_name' => $val_de['value'].'/'.$val_de['name_val'],
						'selected' => $selected,
						'goods_prices' => $val_de['value']
					);
			
					$type_arr[] = $temp_arr;
				
					break;
				}
				
			}

	    }
	}
	else
	{
		// 活动服务的场次和规格按钮的构造
		$price_tag_type = '活动报名：';
		$stage_arr = array();
		$activity_price_id = '';


		foreach($prices_list_de as $key_de => $val_de)
		{		
			
			if($val_de['stock_num'] == 0 || $val_de['time_e'] < time())
			{
				
				// 报名已经满了 或者 场次已经过期了
				unset($prices_list_de[$key_de]);  
				continue;
			}


			if(empty($stage_id))
			{
				$stage_id = $val_de['id'];
			}

			if($_INPUT['db'] == 1)
			{
				print_r($val_de);
				
			}


			$selected = ($stage_id == $val_de['id'])?1:0;
			
			// 对应每一项场次的所有规格
			$prices_list_data = $val_de['prices_list_data'];
			if(!empty($prices_list_data))
			{
				foreach ($prices_list_data as $p_key => $p_value) 
				{
					// 此变量用于显示对应的场次规格
					$show = $selected;
					$type_btn_selected = $p_key == 0 ? true : false;

					if($val_de['id'] == $stage_id  && !$activity_price_id)
					{
						$activity_price_id = $p_value['id'];
					}

					$temp_arr = array(
						'price' => $p_value['prices'],
						'prices_type_id' => $p_value['id'],
						'price_name' => $p_value['prices'].'/'.$p_value['name'],
						'selected' => $type_btn_selected,
						'stage_id' => $val_de['id'],
						'show' => $show,
						'goods_prices' => $p_value['prices']
					);

					// 只有价格大于0才能有规格
					// hudw 2015.9.28
					if($p_value['prices']>0)
					{
						$type_arr[] = $temp_arr;						
					}
				}			
			}
			
			$time  = date("Y.m.d H:i",$val_de['time_s']).' 至 '.date("Y.m.d H:i",$val_de['time_e']);

			$temp_stage_arr = array(
				'stage_name' => $val_de['name_val'].' '.$time,
				'stage_time' => $time,
				'stage_id' => $val_de['id'],
				'selected' => $selected
			);

			$stage_arr[] = $temp_stage_arr;

	    }

	    $temp_prices_list_de = array_values($prices_list_de);


			

	    // 用于显示更多场次按钮
		$show_more_stage = count($prices_list_de) > 3 ? true : false;
	}
	
}
else
{
	$price_tag_type = '价格';
	
	$temp_arr = array(
		'price' => $goods_info['goods_data']['prices'],
		'price_name' => $goods_info['goods_data']['prices'],
		'prices_type_id' => 0,
		'selected' => 1,
		'goods_prices' => $goods_info['goods_data']['prices']
	);
	
	$type_arr[] = $temp_arr;
	
	$shoud_show_price_type_id = true;
			
}

// 促销详情数组
$promotion_details_arr = array();

if($_INPUT['sa'] == 1)
{
	print_r($stage_arr);
}

/*

参与促销使用字段：

限时低价：type_name
开始时间：start_time_str
结束时间：end_time_str
促销价：cal_goods_prices

*/

// 特殊处理星星bug
// 循环重新整合规格按钮
// hudw 2015.10.20

$promotion_arr_idx = 0;
foreach ($type_arr as $key => $value)
{
	$type_arr[$key]['price_name'] = preg_replace('/\|@\|/', '', $type_arr[$key]['price_name']);
	$type_arr[$key]['price_name'] = '￥'.$type_arr[$key]['price_name'];

	$promotion_detail = $promotion_obj->get_promotion_list_for_show_single($yue_login_id, $type_target, $show_param_info, 
	array(
		'prices_type_id' => $value['prices_type_id'],
		'goods_prices'   => $value['goods_prices'],
		'stage_id' => $value['stage_id']
	), true);




	// 根据promotion_id 来判断按钮是否具备促销功能
	if(!empty($promotion_detail))
	{
		
		$type_arr[$key]['promotion_id'] = $promotion_detail[0]['promotion_id'];
		$type_arr[$key]['remain_quantity'] = $promotion_detail[0]['remain_quantity'];

		$promotion_details_arr[$promotion_arr_idx]['promotion_id'] = $promotion_detail[0]['promotion_id'];
		$promotion_details_arr[$promotion_arr_idx]['prices_type_id'] = $type_arr[$key]['prices_type_id'];
		$promotion_details_arr[$promotion_arr_idx]['type_name'] = $promotion_detail[0]['type_name'];
		$promotion_details_arr[$promotion_arr_idx]['content'] = $promotion_detail[0]['start_time_str'].'-'.$promotion_detail[0]['end_time_str'].' '.$promotion_detail[0]['promotion_desc'];
		$promotion_details_arr[$promotion_arr_idx]['cal_goods_prices'] = '促销价：'.$promotion_detail[0]['cal_goods_prices'];
		$promotion_details_arr[$promotion_arr_idx]['remain_quantity'] = $promotion_detail[0]['remain_quantity'];

		$promotion_arr_idx ++ ;
	}




}

$show_promotion_details = (count($promotion_details_arr) == 1 && count($type_arr) == 1);

if($show_promotion_details)
{
	$tpl->assign('promotion_id',$promotion_details_arr[0]['promotion_id']);
	$tpl->assign('remain_quantity',$promotion_details_arr[0]['remain_quantity']);

}


$tpl->assign('show_promotion_details',$show_promotion_details);
$tpl->assign('promotion_details_arr',$promotion_details_arr);
$tpl->assign('price_tag_type',$price_tag_type);
$tpl->assign('shoud_show_price_type_id',$shoud_show_price_type_id);

// 活动促销，重新整合
if($goods_type == 'activity')
{
	foreach ($promotion_details_arr as $key => $value) 
	{
		if($value['prices_type_id'] == $activity_price_id)
		{
			$promotion_details_arr[$key]['selected'] = true;

			$show_promotion_details = 1;

			$promotion_id = $value['promotion_id'];

			$remain_quantity = $value['remain_quantity'];

			break;
		}
	} 


	$tpl->assign('promotion_id',$promotion_id);
	$tpl->assign('remain_quantity',$remain_quantity);
	$tpl->assign('show_promotion_details',$show_promotion_details);
	$tpl->assign('promotion_details_arr',$promotion_details_arr);


}

// 以下判断为根据不同类型的服务输出不同的组件

// 输出数量组件
$tpl->assign('has_quantity_com',true);

// 输出时间组件
$tpl->assign('has_time_com',true);

// 输出地点组件
if($type_id == 31 || $type_id == 3 || $type_id == 40)
{
	$tpl->assign('has_address_com',true);
}



$buy_num = $goods_info['goods_data']['buy_num'];

if($type_id == 31)
{
	//$gtn = $task_goods_obj->get_goods_type_name($goods_info,array(58));
	//$buy_num = $gtn[58];
	
	$buy_num = 0;
}

if($buy_num == 0)
{
	// 无限制
	$buy_num = 999999;
}

// 不是自定义订单的，默认选中表单的设置
if(!$direct_order_id)
{
	if($goods_type == 'normal')
	{
		// 默认选中第一个价格，并且只有一个价格的时候
		if(count($type_arr) == 1)
		{
			$type_arr[0]['selected'] = 1;
			$tpl->assign('prices_type_id',$type_arr[0]['prices_type_id']);
		}
	}
	elseif($goods_type == 'activity')
	{
		$tpl->assign('prices_type_id',$activity_price_id);
	}
	

	$tpl->assign('type_arr_length',count($type_arr));

	// 默认选中服务所在的地区，影棚租赁和美食
	$service_location_arr = array(12,41,43);
	if(in_array($type_id,$service_location_arr))
	{
		$service_location_id = $goods_info['goods_data']['location_id'];
		$city_name = get_poco_location_name_by_location_id ( $service_location_id );
	}
 
	switch ($type_id) 
	{
		case 5:
			// 增加摄影培训服务地点
			$service_address = $goods_info['goods_att'][65];
			$tpl->assign('service_address',$service_address);
			break;
		case 12:
			// 增加影棚服务地点
			$service_address = $goods_info['goods_att'][320];
			$tpl->assign('service_address',$service_address);
			break;
		case 40:
			// 增加摄影服务地点
			$service_address = $goods_info['goods_att'][93];
			$tpl->assign('service_address',$service_address);
			break;
		case 41:
			// 增加美食服务地点
			$service_address = $goods_info['goods_att'][259];
			$tpl->assign('service_address',$service_address);
			break;
		case 43:
			// 增加约有趣服务地点
			$service_address = $goods_info['goods_att'][370];
			$tpl->assign('service_address',$service_address);
			break;
		
		default:
			break;
	}
	
	// 日期控件默认开启
	$date_control_disable = false;
	// 固定课程的时间并且有开课时间，不可编辑时间
	// 非固定课程的时间并且有开课时间，可编辑时间
	// 这个需求改了好多次了，无力吐槽！
	// hudw 2015.9.25
	$goods_info['goods_att'][317] = trim(str_replace('<br>', '', $goods_info['goods_att'][317]));

	

	if('[固定培训课程]' == $goods_info['goods_att'][317])
	{
		if($goods_info['goods_att'][60] && $type_id == 5)
		{
			$service_time = stristr($goods_info['goods_att'][60],' ')?$goods_info['goods_att'][60]:$goods_info['goods_att'][60].' '.$goods_info['goods_att'][360];

			// 截止报名时间
			$end_service_time = stristr($goods_info['goods_att'][403],' ')?$goods_info['goods_att'][403]:$goods_info['goods_att'][403].' '.$goods_info['goods_att'][360];

			if($end_service_time == trim('0:00'))
			{
				$end_service_time = '';
			}

 			$tpl->assign('service_time',$service_time);
 			$tpl->assign('end_service_time',$end_service_time);

			$date_control_disable = true;
			
		}
	
		
	}
	if('[非固定培训课程]' == $goods_info['goods_att'][317])
	{
		if($goods_info['goods_att'][60] )
		{
			$service_time = stristr($goods_info['goods_att'][60],' ')?$goods_info['goods_att'][60]:$goods_info['goods_att'][60].' '.$goods_info['goods_att'][360];
			
			// 截止报名时间
			$end_service_time = stristr($goods_info['goods_att'][403],' ')?$goods_info['goods_att'][403]:$goods_info['goods_att'][403].' '.$goods_info['goods_att'][360];

			if($end_service_time == trim('0:00'))
			{
				$end_service_time = '';
			}

 			$tpl->assign('service_time',$service_time);
 			$tpl->assign('end_service_time',$end_service_time);

			
		}
		
	}
	

	$tpl->assign('date_control_disable',$date_control_disable);
	$tpl->assign('service_location_id',$service_location_id);
	$tpl->assign('city_name',$city_name);
}

// 用户手机号
if($service_cellphone)
{
	$tpl->assign('service_cellphone',$service_cellphone);
}

if($_INPUT['print'] == 1)
{
	print_r($goods_info);
}

// 日期插件使用范围
$now_year = date("Y");
$now_month = date("m");
$next_year = $now_year+1;
$next_month = $now_month-6;

$tpl->assign('now_range', $now_year.'-'.$now_month);
$tpl->assign('next_range', $next_year.'-'.$next_month);

// 获取服务类型名称
$type_name = $task_goods_obj->get_goods_typename_for_type_id($type_id);

/**
 * 指定时间下单 地推使用
 * hudw 2015.10.29
 */
// =====================
$ditui_goods_id_arr = array(
	//253 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'五羊新城'),
	2124647 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029002', 'address'=>'福田购物公园酒吧街'),
	2122759 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124012 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124060 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124183 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124416 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124924 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124945 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124936 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124689 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2123840 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124335 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124990 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2123469 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2125024 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2125028 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2125029 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124042 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124519 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2125037 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2123694 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2123693 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2123690 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2123676 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124745 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2125002 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124027 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2124646 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2123874 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2125091 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
	2123873 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'广州'),
    
    2117851 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2117959 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2117969 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2117970 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2117971 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118034 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118037 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2119097 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2119098 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2119103 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2122001 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2122828 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2125848 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118039 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118040 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118052 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118074 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118077 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118086 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118342 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118935 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118940 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2118948 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2125791 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2121858 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2121874 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2121877 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2121878 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2121879 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2121880 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    2123374 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'广东深圳'),
    
    2125398 => array('time'=>'2015-11-21 23:00', 'location_id'=>'101029001', 'address'=>'广州'),
    2128670 => array('time'=>'2015-11-21 23:00', 'location_id'=>'101029001', 'address'=>'广州'),
	
);
if(!empty($ditui_goods_id_arr[$goods_id]))
{
	$service_time = $ditui_goods_id_arr[$goods_id]['time'];
	if( !empty($service_time) ) $tpl->assign('service_time', $service_time);
	
	$service_location_id = $ditui_goods_id_arr[$goods_id]['location_id'];
	if( !empty($service_location_id) )
	{
		$city_name = get_poco_location_name_by_location_id($service_location_id);
		$tpl->assign('service_location_id', $service_location_id);
		$tpl->assign('city_name',$city_name);
	}
	
	$service_address = $ditui_goods_id_arr[$goods_id]['address'];
	if( !empty($service_address) ) $tpl->assign('service_address', $service_address);
}
// =====================

$tpl->assign('has_type',$has_type);
$tpl->assign('buy_num',$buy_num);
$tpl->assign('type_arr',$type_arr);
$tpl->assign('stage_arr',$stage_arr);
$tpl->assign('type_id',$type_id);
$tpl->assign('type_name',$type_name);
$tpl->assign('goods_id',$goods_id);
$tpl->assign('goods_info',$goods_info['goods_data']);
$tpl->assign('is_yueyue_app',MALL_UA_IS_YUEYUE);
$tpl->assign('goods_type',$goods_type);
$tpl->assign('show_more_stage',$show_more_stage);
$tpl->assign('title',$title);

$tpl->output();
	





?>