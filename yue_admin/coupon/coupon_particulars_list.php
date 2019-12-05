<?php

/**
 * 优惠券发送明细
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-30 14:55:51
 * @version 1
 */
 include_once ('common.inc.php');
 //后台常用函数
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 //优惠券类
 $coupon_obj = POCO::singleton('pai_coupon_class');
 $tpl = new SmartTemplate('coupon_particulars_list.tpl.htm');


 $act      = trim($_INPUT['act']);
 $batch_id = trim($_INPUT['batch_id']);
 //默认为这个月的明细
 $begin_time = $_INPUT['begin_time'] ? trim($_INPUT['begin_time']): date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')));
 $end_time   = $_INPUT['end_time']  ? trim($_INPUT['end_time']) : date('Y-m-d', mktime(0,0,0,date('m'),date('t'),date('Y')));

 $setParam['begin_time'] = $begin_time;
 $setParam['end_time'] = $end_time;

 if ($batch_id > 0) 
 {
 	$setParam['batch_id'] = $batch_id;
 }
 //$where_str  = '';
 /*$setParam   = array();
 if ($begin_time) 
 {
 	$setParam['begin_time'] = $begin_time;
 }
 if ($end_time) 
 {
 	$setParam['end_time']  = $end_time;
 }*/
 $list = $coupon_obj->get_stat_give_list(strtotime($begin_time)
, strtotime($end_time)+24*3600, $batch_id);

 if(!is_array($list)) $list = array();
 $where_in_str = '';
 foreach($list as $key=>$vo)
 {
 	if($key != 0) $where_in_str .= ",";
 	$where_in_str .= "{$vo['batch_id']}";
 }
 if(strlen($where_in_str) > 0)
 {
 	$where_tmp_str = "batch_id in ($where_in_str)";
 	$batch_list = $coupon_obj->get_batch_list(0, false, $where_tmp_str, '', '0,1000', 'batch_id,batch_name,scope_module_type_name,coupon_face_value');
 	if(is_array($batch_list)) $list = combine_arr2($list, $batch_list, 'batch_id');
 }

 $tpl->assign($setParam);
/* $tpl->assign('begin_time', $begin_time);
 $tpl->assign('end_time', $end_time);*/
 $tpl->assign('list', $list);
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();

 ?>