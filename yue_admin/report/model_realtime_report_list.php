<?php

/**
 * 模特实时报表
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月13日
 * @version 1
 */
 include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
 ini_set('memory_limit', '256M');
 include ('common.inc.php');
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 $model_realtime_obj = POCO::singleton('pai_model_realtime_report_class');
 $user_obj           = POCO::singleton('pai_user_class');
 $model_style_v2     = POCO::singleton ( 'pai_model_style_v2_class' );
 
 $page_obj   = new show_page ();
 $show_count = 30;
 
 $tpl = new SmartTemplate("model_realtime_report_list.tpl.htm");
 
 $act     = trim($_INPUT['act']);
 $user_id = intval($_INPUT['user_id']);
 
 $where_str = '';
 $setParam  = array();
 
 if($user_id > 0) $setParam['user_id'] = $user_id;
 
 $total_count = $model_realtime_obj->get_model_realtime_list(true,$user_id, $where_str);
 
 $page_obj->setvar($setParam);
 
 $page_obj->set($show_count, $total_count );
 
 //导出数据
 if($act == 'export')
 {
 	$list = $model_realtime_obj->get_model_realtime_list(false,$user_id, $where_str, "0,{$total_count}",'details_count DESC,date_count DESC,user_id DESC');
 }
 else 
 {
 	$list = $model_realtime_obj->get_model_realtime_list(false,$user_id, $where_str, $page_obj->limit(),'details_count DESC,date_count DESC,user_id DESC');
 }
 
 $where_tmp_str = '';
 foreach ($list as $key=> $val)
 {
 	if($key != 0) $where_tmp_str .= ',';
 	$where_tmp_str .= $val['user_id'];
 	$ret = $model_style_v2->get_model_style_by_user_id($val['user_id']);
 	if(is_array($ret) && !empty($ret)) $list[$key]['style'] = $ret[0]['style'];
 }
 
 //模特昵称
 if(strlen($where_tmp_str) > 0)
 {
 	//昵称
 	$where_in_str = "user_id IN ({$where_tmp_str})";
 	$user_ret = $user_obj->get_user_list(false, $where_in_str, 'user_id DESC', "0,{$show_count}",'user_id,nickname');
 	if(is_array($user_ret)) $list = combine_arr($list, $user_ret, 'user_id');
 	/* $style_ret = $model_style_v2->get_model_style_list(false, $where_in_str, 'id DESC', "0,{$show_count}", 'user_id,style');
 	if(is_array($style_ret)) $list = combine_arr($list, $style_ret, 'user_id'); */
 }
 
 if(!is_array($list)) $list = array();
  
 //数据导出
 if($act == 'export')
 {
 	//print_r($list);
 	foreach ($list as $key => $vo)
 	{
 		$data [$key] ['user_id'] = $vo ['user_id'];
		$data [$key] ['nickname'] = $vo ['nickname'];
		$data [$key] ['style'] = $vo ['style'];
		$data [$key] ['visitPV'] = $vo ['visitPV'];
		$data [$key] ['visitUV'] = $vo ['visitUV'];
		$data [$key] ['chatUV'] = $vo ['chatUV'];
		$data [$key] ['date_count'] = $vo ['date_count'];
		$data [$key] ['details_count'] = $vo ['details_count'];
		$pv_pref = sprintf('%.2f', $vo['details_count']/$vo['chatUV']);
		if($pv_pref > 1) $pv_pref = 1;
		$data [$key] ['pv_pref'] = ($pv_pref *100).'%';
		$data [$key] ['last_login_time'] = date('Y-m-d H:i:s', $vo['last_login_time']);
 	}
 	$fileName = "模特实时报表";
 	$title    = "模特实时报表";
 	$headArr  = array('模特ID','昵称','擅长','模特卡访问UV','模特卡访问PV','私聊UV','模特下单数','模特成单数','UV订单转化率','最后登录时间');
 	getExcel($fileName,$title,$headArr,$data);
 	exit;
 }
 
 foreach ($list as $key => $vo)
 {
 	$list[$key]['last_login_time'] = date('Y-m-d H:i:s', $vo['last_login_time']);
 	$pv_pref = sprintf('%.2f', $vo['details_count']/$vo['chatUV']);
 	if($pv_pref > 1) $pv_pref = 1;
 	$list[$key]['pv_pref']         = $pv_pref *100;
 }
 
 
 
 
 //print_r($list);exit;
 $tpl->assign($setParam);
 $tpl->assign('total_count', $total_count);
 $tpl->assign('list', $list);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 
 $tpl->output();
 
 ?>
 
 
 
 