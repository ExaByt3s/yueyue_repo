<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 21 July, 2015
 * @package default
 */

/**
 * Define 活动支付
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

$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/pay.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部公共样式和js引入
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$event_id = intval($_INPUT['event_id']);
$enroll_id = intval($_INPUT['enroll_id']);
$table_id = intval($_INPUT['table_id']);
$phone = intval($_INPUT['tel']);
$enroll_num = intval($_INPUT['enroll_num']);


// 直接支付流程
if(!$enroll_id)
{
	$event_info = get_event_by_event_id($event_id);
	$budget = $event_info['budget'];
	
	$detail_list['event_id'] = $event_id;
	$detail_list['phone'] = $phone;
	$detail_list['table_id'] = $table_id;
	
	$detail_list['total_budget'] = $enroll_num*$budget;
	$detail_list['enroll_num'] = $enroll_num;
	$detail_list['event_title'] = $event_info['title'];
	
	$table_info = $event_info['table_info'];
	$table_text  = '';
	
	foreach($table_info as $key => $val)
	{
		if($table_id == $val['id'])
		{
			$table_text = "第".($key+1)."场 ".date("m.d H:i",$val['begin_time'])."-".date("m.d H:i",$val['end_time']);

			break;
		}
	}
	
	$detail_list['table_text'] = $table_text;

	// 支付总价
	$detail_info['total_amount'] = $enroll_num*$budget;
	
	// 传递参数
	$table_arr[] = array(
			'enroll_num' => $enroll_num,
			'table_id' => $table_id
		);

	$page_params = array(
		'event_id' => $event_id,
		'phone' => $phone,
		'table_arr' => $table_arr,
		'table_id' => $table_id,
		'enroll_id' => 0,
		'has_join' => $enroll_id?1:0
	);
	
	if($_INPUT['print'] == 1)
	{
		print_r($table_info);
		die();
	}
	
}
// 跑继续支付流程
else
{
	$enroll_detail = get_enroll_detail_info($enroll_id);
	
	$budget = $event_info['budget'];
	
	$detail_list['event_id'] = $enroll_detail['event_id'];
	$detail_list['phone'] = $enroll_detail['phone'];
	$detail_list['table_id'] = $enroll_detail['table_id'];
	
	$detail_list['enroll_num'] = $enroll_detail['enroll_num'];
	$detail_list['event_title'] = $enroll_detail['event_title'];
	
	$table_info = $enroll_detail['table_arr'];
	
	$detail_list['table_text'] = $enroll_detail['table_info'];
	
	// 支付总价
	$detail_list['total_budget'] = $enroll_detail['total_budget'];
	$detail_info['total_amount'] = $enroll_detail['total_budget'];
	
	$table_arr = array();

	$page_params = array(
		'event_id' => $event_id,
		'phone' => $enroll_detail['phone'],
		'table_id' => $enroll_detail['table_id'],
		'enroll_id' => $enroll_id,
		'table_arr' => $table_arr,
		'has_join' => $enroll_id?1:0,
		'enroll_id_arr' => array($enroll_id)
	);
	
	if($_INPUT['print'] == 1)
	{
		print_r($detail_list);
		die();
	}
	
}

//获取个人信息
$obj = POCO::singleton('pai_user_class');
$user_info = $obj->get_user_info_by_user_id($yue_login_id);

$detail_info['user_id'] = $user_info['user_id'];
$detail_info['available_balance'] =  $user_info['available_balance']; //$ret['available_balance'];
$detail_info['bail_available_balance'] = $user_info['bail_available_balance'];
$detail_info['balance'] = $user_info['balance'];  

/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false; 

$detail_info['is_weixin'] = $__is_weixin;
$detail_info['is_yueyue_app'] = $__is_yueyue_app;   

if(!$__is_yueyue_app && !$__is_weixin )
{
	$detail_info['is_weixin'] = false;
	$detail_info['is_yueyue_app'] = false;	
	$detail_info['is_zfb_wap'] = true;    
}


// 订单详情列表
$detail_info['detail_list'] = $detail_list;  


$detail_info = mall_output_format_data($detail_info);
$page_params = mall_output_format_data($page_params);

// 当前支付页面回链
$cur_page_url = urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);

$tpl->assign('cur_page_url', $cur_page_url);

$tpl->assign('page_data',$detail_info);
$tpl->assign('page_params',$page_params);
$tpl->output();
?>