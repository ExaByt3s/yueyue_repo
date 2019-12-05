<?php


/**
 * 录入者报表
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-04-01 15:37:26
 * @version 1
 */
 ignore_user_abort(true);
 ini_set('memory_limit', '256M');
 set_time_limit(3600);
 include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
 include('common.inc.php');
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 $model_audit_obj = POCO::singleton('pai_model_audit_class');
 $model_add_obj  = POCO::singleton('pai_model_add_class');
 //管理者类
 $inputer_report_obj = POCO::singleton('pai_inputer_report_class');
 //用户类
 $user_obj    = POCO::singleton('pai_user_class');


 //分页类
 $page_obj   = new show_page();
 $show_count = 30;
 //模板类
 $tpl = new SmartTemplate('model_inputer_report_v3.tpl.htm');
 $inputer_list   = $model_add_obj->get_inputer_list_v2(false,'inputer_id !=0 ','0,99999999','uid DESC','DISTINCT(inputer_id)' );
 $act = trim($_INPUT['act']);
 $inputer_id = trim($_INPUT['inputer_id']);
 $date = $_INPUT['date'] ? trim($_INPUT['date']) : date('Y-m-d', time()-24*3600);

 $setParam  = array();
 $where_str = '';

 //条件
 if(strlen($inputer_id) > 0)
 {
 	$where_str .= "inputer_id = {$inputer_id}";
 	//echo $where_str;
 	$setParam['inputer_id'] = $inputer_id;
 	//获取选择
 	if(!is_array($inputer_list)) $inputer_list  = array();
 	foreach ($inputer_list as $inpKey => $inpVal) 
 	{
 		if($inpVal['inputer_id'] == $inputer_id) $inputer_list[$inpKey]['selected'] = "selected='true'";
 		# code...
 	}
 }
 if(strlen($date) > 0 ) $setParam['date'] = $date;
 //查询数据
 $total_count = $inputer_report_obj->get_inpuer_report_report_list_v2($date,true, $inputer_id);

 //导出数据
 if($act == 'export')
 {
 	$list = $inputer_report_obj->get_inpuer_report_report_list_v2($date,false, $inputer_id,'detail_price DESC,detail_num DESC', "0,{$total_count}", $fields = '*');
 	if(!is_array($list)) $list = array();
 	//查询昵称
 	$where_nickname_tmp_str = '';
 	$where_inputer_tmp_str  = '';
 	foreach ($list as $key => $vo) 
 	{
 		if($key != 0)
 		{
 			$where_nickname_tmp_str .= ','; 
 			$where_inputer_tmp_str  .= ',';
 		}
 		$where_nickname_tmp_str .= "{$vo['user_id']}";
 		$where_inputer_tmp_str  .= "{$vo['inputer_id']}";
 	}
 	if(strlen($where_nickname_tmp_str) > 0) 
 	{
 		$where_nickname_in_str = "user_id in({$where_nickname_tmp_str})";
 		$user_list = $user_obj->get_user_list(false, $where_nickname_in_str,'user_id DESC',"0,{$total_count}", 'nickname,user_id,add_time as register_time');
 		//print_r($user_list);exit;
 		if(is_array($user_list)) $list = combine_arr($list, $user_list, 'user_id');
 		
 	}
 	if(strlen($where_inputer_tmp_str) > 0)
 	{
 		$where_inputer_name_in_str = "user_id in({$where_inputer_tmp_str})";
 		$inputer_name_list = $user_obj->get_user_list(false, $where_inputer_name_in_str,'user_id DESC',"0,{$total_count}", 'nickname as inputer_name,user_id as inputer_id');
 		//print_r($inputer_name_list);
 		//exit;
 		if(is_array($inputer_name_list)) $list = combine_arr2($list, $inputer_name_list, 'inputer_id');
 	}
 	//print_r($list);exit;
 	$data = array();
 	foreach ($list as $key => $vall) 
 	{
		$data[$key]['user_id']      = $vall['user_id'];
		$data[$key]['nickname']     = $vall['nickname'];
		$data[$key]['login_num']    = $vall['login_num'];
	 	$reply_qur = 0;
	 	if($vall['replay_count'] == 0)
	 	{
	 		$reply_qur = 0;
	 	}
	 	else
	 	{
	 	  $reply_qur = sprintf('%.2f',($vall['replay_count']/$vall['mes_count'])*100);
	 	}
	 	$data[$key]['reply_qur']    = $reply_qur.'%';
		$data[$key]['detail_price'] = $vall['detail_price'];
		$data[$key]['detail_num']   = $vall['detail_num'];
		$data[$key]['cancel_num']   = $vall['cancel_num'];
		$data[$key]['inputer_name'] = $vall['inputer_name'];
        $row  = $model_audit_obj->get_model_info($vall['user_id']);
        $data[$key]['register_time'] = date("Y-m-d H:i", $vall['register_time']);
        $audit_time = intval($row['audit_time']);
        $data[$key]['audit_time']     = strlen($audit_time) >1 ? date("Y-m-d H:i", $row['audit_time']) : '';
        $data[$key]['reason']        = $row['reason'];
 	}
 	$fileName = "管理模特报表";
 	$title    = "管理模特列表";
 	$headArr  = array('用户ID','用户昵称','登录频率','回复率','交易金额','交易数','拒绝次数','录入者','注册时间','操作时间','备注');
 	getExcel($fileName,$title,$headArr,$data);
 	exit;
 }


 $page_obj->setvar($setParam);
 $page_obj->set($show_count, $total_count);
 $list        = $inputer_report_obj->get_inpuer_report_report_list_v2($date,false, $inputer_id,'detail_price DESC,detail_num DESC', $page_obj->limit());
 //print_r($list);exit;
 if(!is_array($list)) $list = array();
 
 //录入者昵称
 $where_tmp_str = '';
 //查询昵称
 $where_nickname_tmp_str = '';
 foreach ($list as $key => $vo) 
 {
 	$reply_qur = 0;
 	if($vo['replay_count'] == 0)
 	{
 		$reply_qur = 0;
 	}
 	else
 	{
 	  $reply_qur = sprintf('%.2f',($vo['replay_count']/$vo['mes_count'])*100);
 	}
 	$list[$key]['reply_qur'] = $reply_qur;
 	if($key != 0){$where_nickname_tmp_str .= ',';$where_tmp_str .= ',';} 
 	$where_nickname_tmp_str .= $vo['user_id'];
 	$where_tmp_str .= $vo['inputer_id'];
 }
 //echo $where_nickname_tmp_str;
 if(strlen($where_nickname_tmp_str) > 0) 
 {
 	$where_nickname_in_str = "user_id in({$where_nickname_tmp_str})";
 	$user_list = $user_obj->get_user_list(false, $where_nickname_in_str,'user_id DESC',"0,{$show_count}", 'nickname,user_id');
 	if(is_array($user_list)) $list = combine_arr($list, $user_list, 'user_id');
 }
 if(strlen($where_tmp_str) > 0)
 {
 	$where_in_str .= "user_id IN ({$where_tmp_str})";
 	$inputer_ret =  $user_obj->get_user_list(false, $where_in_str, $order_by = 'user_id DESC', '0,,{$show_count}', 'nickname as inputer_name,user_id as inputer_id');
 	if(is_array($inputer_ret)) $list = combine_arr2($list, $inputer_ret, 'inputer_id');
 }
 $tpl->assign('total_count',$total_count);
 $tpl->assign('inputer_list',$inputer_list);
 $tpl->assign('list',$list);
 $tpl->assign($setParam);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();

 ?>