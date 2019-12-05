<?php 

/*
 * xiaoxiao
 * 交易列表 
 * 2015-1-13
*/
 include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
 include_once 'common.inc.php';
 //查看权限
 //check_authority_by_list($ret_type = 'exit_type',$authority_list, 'organization', 'is_select');
 include_once 'include/common_function.php';
 $user_obj      = POCO::singleton ( 'pai_user_class' );
 $user_icon_obj = POCO::singleton('pai_user_icon_class');
 $order_obj     = POCO::singleton ( 'pai_order_org_class' );
 $model_auditr_obj = POCO::singleton ( 'pai_model_audit_class' );
 //机构关联表
 $model_relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
 //活动状态
 //$event_details_obj = POCO::singleton ( 'event_details_class' );
 //状态
 //$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );
 $tpl = new SmartTemplate("order_org_list.tpl.htm");
 $act               = $_INPUT['act'] ? $_INPUT['act'] : 'search';
 $org_id            = $_INPUT['org_id'] ? intval($_INPUT['org_id']) : 0;
 $nick_name         = $_INPUT['nick_name'] ? $_INPUT['nick_name'] : '';
 $cellphone         = intval($_INPUT['cellphone']) ? intval($_INPUT['cellphone']) : '';
 $min_date_time     = $_INPUT['min_date_time']  ? $_INPUT['min_date_time'] : '';
 $max_date_time     = $_INPUT['max_date_time']  ? $_INPUT['max_date_time'] : '';
 $is_approval       = isset($_INPUT['is_approval']) ? intval($_INPUT['is_approval']) : -1;
 //var_dump($is_approval);
 if (empty($org_id)) 
 {
 	echo "<script type='text/javascript'>window.alert('非法操作');location.href='org_list.php';</script>";
 	exit;
 }
 $is_select = "selected='true'";
 $where_str = "re.org_id = {$org_id} AND au.user_id = re.user_id";
 if ($is_approval == 0) 
 {
 	$where_str .= " AND au.is_approval = 1";
 	$is_approval_1 = $is_select;
 }
 elseif ($is_approval == 1) 
 {
 	$where_str .= " AND au.is_approval <> 1";
 	$is_approval_2 = $is_select;
 }
 $total_count = $model_relate_org_obj->get_model_org_list_v2_by_org_id(true, $where_str);
 $list = $model_relate_org_obj->get_model_org_list_v2_by_org_id(false, $where_str, "0,{$total_count}", 'id DESC',$fields = 'user_id');
 //查询发布员所有user_id
 $user_id = array_change_by_val($list, 'user_id');
 //print_r($user_id);exit;
 //昵称处理
 if ($nick_name) 
 {
 	 //通过昵称查询user_id
 	 $app_uiser_id = $user_obj->get_user_id_by_nickname($nick_name);
 	 //var_dump($app_uiser_id);
 	 if (!empty($app_uiser_id) && is_array($app_uiser_id)) 
 	 {
 	 	$app_uiser_id = array_change_by_val($app_uiser_id, 'user_id');
 	 	$user_id = array_intersect($user_id, $app_uiser_id);
 	 }
 	 else
 	 {
 	 	$user_id = array();
 	 }
 }
 //电话号码
 if ($cellphone && preg_match ('/^1\d{10}$/isU',$cellphone )) 
 {
 	//交换user_id 搜索user_id 避免被修改
 	$get_user_id = $user_obj->get_user_id_by_phone($cellphone);
    if (in_array($get_user_id, $user_id)) 
    {
    	$user_id = array($get_user_id);
    }
    else
    {
    	$user_id = array();
    }   
 }
 //var_dump($user_id);//exit;
 //更进时间处理
 if ($min_date_time && $max_date_time) 
 {
 	$min_tmp_time = strtotime($min_date_time);
 	$max_tmp_time = strtotime($max_date_time) + 24*3600;
 	$where_data_where = "pay_time BETWEEN {$min_tmp_time} AND {$max_tmp_time} ";
 	$date_user_id = $order_obj->get_user_id_by_where_str($where_data_where, $limit = '0,99999999', $order_by = 'date_id DESC',  $fields = 'DISTINCT(to_date_id)');
    if (!empty($date_user_id) && is_array($date_user_id)) 
 	 {
 	 	$date_user_id = array_change_by_val($date_user_id, 'to_date_id');
 	 	$user_id = array_intersect($user_id, $date_user_id);
 	 	//var_dump($user_id);
 	 }
 	 else
 	 {
 	 	$user_id = array();
 	 }
 }
 //总数
//var_dump($user_id);
$list = array();
$total_count = 0;
if (!empty($user_id) && is_array($user_id)) 
{
	$key_val = 0;
	foreach ($user_id as $key => $vo) 
	{

		$list[$key_val]['user_id']   = $vo;
		//$list[$key_val]['user_id']   = $vo;
		$list[$key_val]['org_id']    = $org_id;
		$list[$key_val]['icon']      = $user_icon_obj->get_user_icon($vo, 32);
		$list[$key_val]['thumb']      = $user_icon_obj->get_user_icon($vo, 100);
		$list[$key_val]['nickname']  = get_user_nickname_by_user_id($vo);
		$list[$key_val]['cellphone'] = $user_obj->get_phone_by_user_id($vo);
		//实际金额
		$list[$key_val]['true_budget'] = $order_obj->get_user_count_budget_by_user_id($vo, $org_id);
		$total_count += sprintf('%.2f',$list[$key_val]['true_budget']);
		//总交易次数
		$list[$key_val]['total_count'] = $order_obj->get_user_total_count_by_user_id($vo, $org_id);
		//成功次数
		$list[$key_val]['success_count'] = $order_obj->get_user_success_count_by_user_id($vo, $org_id);
		$list[$key_val]['is_approval']   = $model_auditr_obj->get_status_by_user_id($vo);
		$key_val ++;
	}
}

 //导出数据
 if ($act == 'export') 
 {
 	if (empty($list) || !is_array($list)) 
 	{
 	   echo "<script type='text/javascript'>window.alert('导出数据不能为空');history.back();</script>";
 	   exit;
 	}
 	$data = array();
 	foreach ($list as $key => $vo) 
 	{
 		unset($vo['user_id']);
 		unset($vo['org_id']);
 		unset($vo['icon']);
 		unset($vo['thumb']);
 		$vo['is_approval'] = $vo['is_approval'] == 1 ? '上架' : '下架';
 		$data[$key] = $vo;
 	}
 	$fileName = '交易详情表';
 	$title = '交易详情';
 	$headArr = array("昵称","手机号","交易金额","交易次数","成功交易数","状态");
 	getExcel($fileName,$title,$headArr,$data);
 	exit;
 	
 }
 //var_dump($list);
 //print_r($list);exit;
 //var_dump($total_count);
 $tpl->assign('is_approval_1', $is_approval_1);
 $tpl->assign('is_approval_2', $is_approval_2);
 $tpl->assign('total_count', $total_count);
 $tpl->assign('nick_name', $nick_name);
 $tpl->assign('cellphone', $cellphone);
 $tpl->assign('act', $act);
 $tpl->assign('min_date_time', $min_date_time);
 $tpl->assign('max_date_time', $max_date_time);
 $tpl->assign('list', $list);
 $tpl->assign('org_id', $org_id);
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();




 ?>