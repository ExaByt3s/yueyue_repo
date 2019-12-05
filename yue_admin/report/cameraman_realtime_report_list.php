<?php

/**
 * ��Ӱʦʵʱ����
 * @authors xiao xiao (xiaojm@yueyue.com)
 * @date    2015��4��13��
 * @version 1
 */
 include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
 ini_set('memory_limit', '256M');
 include ('common.inc.php');
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 $cameraman_realtime_obj = POCO::singleton('pai_cameraman_realtime_report_class');
 $user_obj           = POCO::singleton('pai_user_class');
 $page_obj   = new show_page ();
 $show_count = 30;
 
 $tpl = new SmartTemplate("cameraman_realtime_report_list.tpl.htm");
 
 $act     = trim($_INPUT['act']);
 $user_id = intval($_INPUT['user_id']);
 
 $where_str = '';
 $setParam  = array();
 
 if($user_id > 0) $setParam['user_id'] = $user_id;
 
 $total_count = $cameraman_realtime_obj->get_cameraman_realtime_list(true,$user_id, $where_str);
 
 $page_obj->setvar($setParam);
 
 $page_obj->set($show_count, $total_count );
 
 //�Ƿ񵼳��ж�
 if ($act == 'export')
 {
 	//if($total_count > 5000) $total_count = 5000;
 	$list = $cameraman_realtime_obj->get_cameraman_realtime_list(false,$user_id, $where_str, "0,{$total_count}",'details_count DESC,date_count DESC,user_id DESC');
 }
 else 
 {
 	$list = $cameraman_realtime_obj->get_cameraman_realtime_list(false,$user_id, $where_str, $page_obj->limit(),'details_count DESC,date_count DESC,user_id DESC');
 }
 
 $where_tmp_str = '';
 foreach ($list as $key=> $val)
 {
 	if($key != 0) $where_tmp_str .= ',';
 	$where_tmp_str .= $val['user_id'];
 }
 
 //ģ���ǳ�
 if(strlen($where_tmp_str) > 0)
 {
 	//�ǳ�
 	$where_in_str = "user_id IN ({$where_tmp_str})";
 	$user_ret = $user_obj->get_user_list(false, $where_in_str, 'user_id DESC', "0,{$show_count}",'user_id,nickname');
 	if(is_array($user_ret)) $list = combine_arr($list, $user_ret, 'user_id');
 }
 
 if(!is_array($list)) $list = array();
 
 //��������
 if($act == 'export')
 {
 	$data = array();
 	foreach ($list as $key => $vo)
 	{
 		$data [$key] ['user_id'] = $vo ['user_id'];
 		$data [$key] ['nickname'] = $vo ['nickname'];
 		$data [$key] ['visitUV'] = $vo ['visitUV'];
 		$data [$key] ['chatUV'] = $vo ['chatUV'];
 		$data [$key] ['date_count'] = $vo ['date_count'];
 		$data [$key] ['details_count'] = $vo ['details_count'];
 		$pv_pref = sprintf('%.2f', $vo['details_count']/$vo['chatUV']);
 		if($pv_pref > 1) $pv_pref = 1;
 		$data [$key] ['pv_pref'] = ($pv_pref *100).'%';
 		$data [$key] ['last_login_time'] = date('Y-m-d H:i:s', $vo['last_login_time']);
 	}
 	$fileName = "��Ӱʦ����ʵʱ����";
 	$title    = "��Ӱʦ����ʵʱ����";
 	$headArr  = array('��ӰʦID','�ǳ�','�鿴ģ��UV','˽��UV','ģ���µ���','ģ�سɵ���','UV����ת����','����¼ʱ��');
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
 
 //��������
 
 
 //print_r($list);exit;
 $tpl->assign($setParam);
 $tpl->assign('total_count', $total_count);
 $tpl->assign('list', $list);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 
 $tpl->output();
 
 ?>
 
 
 
 