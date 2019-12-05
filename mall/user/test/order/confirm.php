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
 * Define ȷ���µ�
 */

include_once 'config.php';
$pc_wap = 'wap/';

// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if(intval($check_arr['switch']) == 1)
{
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
	header("Location:{$url}");
	die();
}


// ��ȡҳ��
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'order/confirm.tpl.htm');



// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

// �û���
$user_obj = POCO::singleton ( 'pai_user_class' );

/******** ���ղ���������PC��ת���� ********/ 
$from_pc = intval($_INPUT['from_pc']);
$goods_id = intval($_INPUT['goods_id']);
$stage_id = trim($_INPUT['stage_id']);
if($from_pc == 1)
{
	$prices_type_id = intval($_INPUT['price_type_id']);
	$tpl->assign('prices_type_id',$prices_type_id);
}

/******** ���ղ��������ڳ�ʼ���� ********/ 
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


/******** ���ղ��������ڳ�ʼ���� ********/ 
	
	


$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');	

// ��Ʒ����
$goods_info = $task_goods_obj->get_goods_info($goods_id);
$type_id = intval($goods_info['goods_data']['type_id']);
$seller_user_id = intval($goods_info['goods_data']['user_id']);
$service_cellphone = $user_obj->get_phone_by_user_id($yue_login_id); 
 
// �ж��Ƿ�Ϊ�����
$goods_type = $type_id == 42 ? 'activity' : 'normal';
$title = $goods_type == 'normal' ? '����ȷ��ҳ' : '�����';

$type_name_list = $type_obj -> get_type_attribute_cate(0);
foreach($type_name_list as $val)
{
	$type_name[$val['id']] = $val;
}
$prices_list_de = $goods_info['goods_prices_list'];

// ���
$type_arr = array();

$has_type = true;
$shoud_show_price_type_id = true;

// ================== ������ťicon���� ==================
// hudw 2015.10.20
$type_target = 'goods';
$show_param_info = array(
 	'channel_module' => 'mall_order', //����̶���mall_order
 	'org_user_id' => $yue_login_id,
 	'location_id' => empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'],
 	'seller_user_id' => $seller_user_id,
 	'mall_type_id' => $type_id,
	'channel_gid' => $goods_id, //��Ʒid����
);

$promotion_obj = POCO::singleton('pai_promotion_class');
// ================== ������ťicon���� ==================


// ================== ����찴ť ==================
if($prices_list_de)
{
	// ��ͨ�����ʱ��
	if($goods_type == 'normal')
	{
		$price_tag_type = '����';

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

				// ֻ�м۸����0�����й��
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
		// �����ĳ��κ͹��ť�Ĺ���
		$price_tag_type = '�������';
		$stage_arr = array();
		$activity_price_id = '';


		foreach($prices_list_de as $key_de => $val_de)
		{		
			
			if($val_de['stock_num'] == 0 || $val_de['time_e'] < time())
			{
				
				// �����Ѿ����� ���� �����Ѿ�������
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
			
			// ��Ӧÿһ��ε����й��
			$prices_list_data = $val_de['prices_list_data'];
			if(!empty($prices_list_data))
			{
				foreach ($prices_list_data as $p_key => $p_value) 
				{
					// �˱���������ʾ��Ӧ�ĳ��ι��
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

					// ֻ�м۸����0�����й��
					// hudw 2015.9.28
					if($p_value['prices']>0)
					{
						$type_arr[] = $temp_arr;						
					}
				}			
			}
			
			$time  = date("Y.m.d H:i",$val_de['time_s']).' �� '.date("Y.m.d H:i",$val_de['time_e']);

			$temp_stage_arr = array(
				'stage_name' => $val_de['name_val'].' '.$time,
				'stage_time' => $time,
				'stage_id' => $val_de['id'],
				'selected' => $selected
			);

			$stage_arr[] = $temp_stage_arr;

	    }

	    $temp_prices_list_de = array_values($prices_list_de);


			

	    // ������ʾ���ೡ�ΰ�ť
		$show_more_stage = count($prices_list_de) > 3 ? true : false;
	}
	
}
else
{
	$price_tag_type = '�۸�';
	
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

// ������������
$promotion_details_arr = array();

if($_INPUT['sa'] == 1)
{
	print_r($stage_arr);
}

/*

�������ʹ���ֶΣ�

��ʱ�ͼۣ�type_name
��ʼʱ�䣺start_time_str
����ʱ�䣺end_time_str
�����ۣ�cal_goods_prices

*/

// ���⴦������bug
// ѭ���������Ϲ��ť
// hudw 2015.10.20

$promotion_arr_idx = 0;
foreach ($type_arr as $key => $value)
{
	$type_arr[$key]['price_name'] = preg_replace('/\|@\|/', '', $type_arr[$key]['price_name']);
	$type_arr[$key]['price_name'] = '��'.$type_arr[$key]['price_name'];

	$promotion_detail = $promotion_obj->get_promotion_list_for_show_single($yue_login_id, $type_target, $show_param_info, 
	array(
		'prices_type_id' => $value['prices_type_id'],
		'goods_prices'   => $value['goods_prices'],
		'stage_id' => $value['stage_id']
	), true);




	// ����promotion_id ���жϰ�ť�Ƿ�߱���������
	if(!empty($promotion_detail))
	{
		
		$type_arr[$key]['promotion_id'] = $promotion_detail[0]['promotion_id'];
		$type_arr[$key]['remain_quantity'] = $promotion_detail[0]['remain_quantity'];

		$promotion_details_arr[$promotion_arr_idx]['promotion_id'] = $promotion_detail[0]['promotion_id'];
		$promotion_details_arr[$promotion_arr_idx]['prices_type_id'] = $type_arr[$key]['prices_type_id'];
		$promotion_details_arr[$promotion_arr_idx]['type_name'] = $promotion_detail[0]['type_name'];
		$promotion_details_arr[$promotion_arr_idx]['content'] = $promotion_detail[0]['start_time_str'].'-'.$promotion_detail[0]['end_time_str'].' '.$promotion_detail[0]['promotion_desc'];
		$promotion_details_arr[$promotion_arr_idx]['cal_goods_prices'] = '�����ۣ�'.$promotion_detail[0]['cal_goods_prices'];
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

// ���������������
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

// �����ж�Ϊ���ݲ�ͬ���͵ķ��������ͬ�����

// ����������
$tpl->assign('has_quantity_com',true);

// ���ʱ�����
$tpl->assign('has_time_com',true);

// ����ص����
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
	// ������
	$buy_num = 999999;
}

// �����Զ��嶩���ģ�Ĭ��ѡ�б�������
if(!$direct_order_id)
{
	if($goods_type == 'normal')
	{
		// Ĭ��ѡ�е�һ���۸񣬲���ֻ��һ���۸��ʱ��
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

	// Ĭ��ѡ�з������ڵĵ�����Ӱ�����޺���ʳ
	$service_location_arr = array(12,41,43);
	if(in_array($type_id,$service_location_arr))
	{
		$service_location_id = $goods_info['goods_data']['location_id'];
		$city_name = get_poco_location_name_by_location_id ( $service_location_id );
	}
 
	switch ($type_id) 
	{
		case 5:
			// ������Ӱ��ѵ����ص�
			$service_address = $goods_info['goods_att'][65];
			$tpl->assign('service_address',$service_address);
			break;
		case 12:
			// ����Ӱ�����ص�
			$service_address = $goods_info['goods_att'][320];
			$tpl->assign('service_address',$service_address);
			break;
		case 40:
			// ������Ӱ����ص�
			$service_address = $goods_info['goods_att'][93];
			$tpl->assign('service_address',$service_address);
			break;
		case 41:
			// ������ʳ����ص�
			$service_address = $goods_info['goods_att'][259];
			$tpl->assign('service_address',$service_address);
			break;
		case 43:
			// ����Լ��Ȥ����ص�
			$service_address = $goods_info['goods_att'][370];
			$tpl->assign('service_address',$service_address);
			break;
		
		default:
			break;
	}
	
	// ���ڿؼ�Ĭ�Ͽ���
	$date_control_disable = false;
	// �̶��γ̵�ʱ�䲢���п���ʱ�䣬���ɱ༭ʱ��
	// �ǹ̶��γ̵�ʱ�䲢���п���ʱ�䣬�ɱ༭ʱ��
	// ���������˺ö���ˣ������²ۣ�
	// hudw 2015.9.25
	$goods_info['goods_att'][317] = trim(str_replace('<br>', '', $goods_info['goods_att'][317]));

	

	if('[�̶���ѵ�γ�]' == $goods_info['goods_att'][317])
	{
		if($goods_info['goods_att'][60] && $type_id == 5)
		{
			$service_time = stristr($goods_info['goods_att'][60],' ')?$goods_info['goods_att'][60]:$goods_info['goods_att'][60].' '.$goods_info['goods_att'][360];

			// ��ֹ����ʱ��
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
	if('[�ǹ̶���ѵ�γ�]' == $goods_info['goods_att'][317])
	{
		if($goods_info['goods_att'][60] )
		{
			$service_time = stristr($goods_info['goods_att'][60],' ')?$goods_info['goods_att'][60]:$goods_info['goods_att'][60].' '.$goods_info['goods_att'][360];
			
			// ��ֹ����ʱ��
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

// �û��ֻ���
if($service_cellphone)
{
	$tpl->assign('service_cellphone',$service_cellphone);
}

if($_INPUT['print'] == 1)
{
	print_r($goods_info);
}

// ���ڲ��ʹ�÷�Χ
$now_year = date("Y");
$now_month = date("m");
$next_year = $now_year+1;
$next_month = $now_month-6;

$tpl->assign('now_range', $now_year.'-'.$now_month);
$tpl->assign('next_range', $next_year.'-'.$next_month);

// ��ȡ������������
$type_name = $task_goods_obj->get_goods_typename_for_type_id($type_id);

/**
 * ָ��ʱ���µ� ����ʹ��
 * hudw 2015.10.29
 */
// =====================
$ditui_goods_id_arr = array(
	//253 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'�����³�'),
	2124647 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029002', 'address'=>'���ﹺ�﹫԰�ưɽ�'),
	2122759 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124012 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124060 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124183 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124416 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124924 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124945 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124936 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124689 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2123840 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124335 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124990 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2123469 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2125024 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2125028 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2125029 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124042 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124519 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2125037 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2123694 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2123693 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2123690 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2123676 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124745 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2125002 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124027 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2124646 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2123874 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2125091 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
	2123873 => array('time'=>'2015-12-1 23:59', 'location_id'=>'101029001', 'address'=>'����'),
    
    2117851 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2117959 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2117969 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2117970 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2117971 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118034 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118037 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2119097 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2119098 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2119103 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2122001 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2122828 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2125848 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118039 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118040 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118052 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118074 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118077 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118086 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118342 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118935 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118940 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2118948 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2125791 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2121858 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2121874 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2121877 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2121878 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2121879 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2121880 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    2123374 => array('time'=>'2015-12-31 23:59', 'location_id'=>'101029002', 'address'=>'�㶫����'),
    
    2125398 => array('time'=>'2015-11-21 23:00', 'location_id'=>'101029001', 'address'=>'����'),
    2128670 => array('time'=>'2015-11-21 23:00', 'location_id'=>'101029001', 'address'=>'����'),
	
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