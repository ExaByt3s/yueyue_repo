<?php
/**
 * 数据导出
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-05-04 10:37:31
 * @version 1
 */
 //include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
 include_once 'common.inc.php';
 //引入常用函数
 include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
 include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
 ini_set('memory_limit', '256M');
 $cameraman_add_v2_obj  = POCO::singleton('pai_cameraman_add_v2_class');
 $user_obj = POCO::singleton('pai_user_class');
 
 //摄影师打标签类
 $cameraman_add_user_label = POCO::singleton('pai_cameraman_add_user_label_class');
 
 $tpl = new SmartTemplate("cameraman_search_v2_export.tpl.htm");
 
 $act       = trim($_INPUT['act']);
 //导出格式
 $layout    = trim($_INPUT['layout']);
 $role      = trim($_INPUT['role']);
 
 $name      = trim($_INPUT['name']);
 $cellphone = trim($_INPUT['cellphone']);
 $user_id   = intval($_INPUT['user_id']);
 $start_num = intval($_INPUT['start_num']);
 $end_num   = intval($_INPUT['end_num']);
 //排序条件
 $sort      = trim($_INPUT['sort']);
 
 //条件
 $where_str = '';
 //快速筛选
 if ($act == 'quick_search')
 {
 	if (strlen($name) >0)
 	{
 		if(strlen($where_str) >0) $where_str .= ' AND ';
 		$where_str .= "C.name like '%".mysql_escape_string($name)."%'";
 	}
 	if (strlen($cellphone) >0)
 	{
 		if(strlen($where_str)>0) $where_str .= ' AND ';
 		$where_str .= "P.cellphone = '".mysql_escape_string($cellphone)."'";
 	}
 	if($user_id >0)
 	{
 		if(strlen($where_str)>0) $where_str .= ' AND ';
 		$where_str .= "C.user_id = {$user_id}";
 	}
 	$order_sort = 'user_id DESC';
 	if(strlen($sort)>0)
 	{
 		if($sort == 'add_time_asc') $order_sort = 'C.add_time ASC,C.user_id DESC';
 		elseif($sort == 'add_time_desc') $order_sort = 'C.add_time DESC,C.user_id DESC';
 	}
    $limit = "0,50000";
    if($start_num >0 && $end_num >0)
    {
        $limit = "{$start_num},{$end_num}";
    }elseif($start_num <1 && $end_num >0 )
    {
        $limit = "0,{$end_num}";
    }
    elseif($start_num >0 && $end_num <1)
    {
        $limit = "{$start_num},99999999";
    }
 	$list = $cameraman_add_v2_obj->get_list(false, $where_str,$order_sort,$limit);

 }

 //导出开始
 if($layout == 'txt')
 {
 	$user_str = implode(',', array_change_by_val($list, 'user_id'));
 	$filePath = "txt.txt";
 	file_put_contents($filePath, $user_str);
 	header("Content-type: application/octet-stream");
 	header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
 	header("Content-Length: ". filesize($filePath));
 	readfile($filePath);
 	exit;
 }
 elseif ($layout == 'url')
 {
 	$user_str = implode(',', array_change_by_val($list, 'user_id'));
 	$str = compressed($user_str);
 	$filePath = "url.txt";
 	file_put_contents($filePath, $str);
 	header("Content-type: application/octet-stream");
 	header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
 	header("Content-Length: ". filesize($filePath));
 	readfile($filePath);
 	exit;
 }
 elseif ($layout == 'excel')
 {
 	//市场部
 	if ($role == 'market')
 	{
 		//摄影师等级
 		$user_level_obj = POCO::singleton('pai_user_level_class');
 		$data = array();
 		foreach ($list as $key=>$val)
 		{
 			$data[$key]['user_id']  = $val['user_id'];
 			$data[$key]['nickname'] = $val['nickname'];
 			$data[$key]['cellphone'] = $val['cellphone'];
 			$data[$key]['level'] = $user_level_obj->get_user_level($val['user_id']);
 			$data[$key]['register_time'] = $val['register_time'];
 		}
 		$fileName = "市场部excel";
 		//$title    = "市场部excel";
 		$headArr = array('用户ID','APP昵称', '手机号码', '等级', '注册时间');
 		//getExcel($fileName,$title,$headArr,$data);
        Excel_v2::start($headArr,$data,$fileName);
 		exit;
 	}
 	//运营部的
 	if($role == 'operate')
 	{
 		$data = array();
 		foreach ($list as $key=>$val)
 		{
 			$data[$key]['user_id']          = $val['user_id'];
 			$data[$key]['nickname']         = $val['nickname'];
 			$data[$key]['name']             = $val['name'];
 			$data[$key]['cellphone']        = $val['cellphone'];
 			$data[$key]['weixin_name']      = $val['weixin_name'];
 			$data[$key]['goods_style']      = str_replace('|', ',', $val['goods_style']);
 			$data[$key]['register_time']    = $val['register_time'];
 			$data[$key]['last_login_time']  = $val['last_login_time'];
 			$data[$key]['login_sum']  = $val['login_sum'];
 			$data[$key]['total_sum']  = $val['total_sum'];
 			$data[$key]['total_price']  = $val['total_price'];
 			$data[$key]['prev_month_price'] = $val['prev_month_price'];
 			$data[$key]['prev_month_num']   = $val['prev_month_num'];
 			$data[$key]['avg_month_price']  = $val['avg_month_price'];
 			//消费水平
 			if($val['consumption_level']>=0 && $val['consumption_level']<=100 )  $data[$key]['consumption_name'] = '低价';
 			if($val['consumption_level']>=101 && $val['consumption_level']<=200 ) $data[$key]['consumption_name'] = '较低';
 			if($val['consumption_level']>=201 && $val['consumption_level']<=400 ) $data[$key]['consumption_name'] = '适中';
 			if($val['consumption_level']>=401 && $val['consumption_level']<=600 ) $data[$key]['consumption_name'] = '较高';
 			if($val['consumption_level']>600) $data[$key]['consumption_name'] = '高价';
 			//活跃度
 			if($val['login_sum']==0) $data[$key]['login_name'] = '沉默';
 			if($val['login_sum']>0 && $val['login_sum'] <= 5) $data[$key]['login_name'] = '活跃';
 			if($val['login_sum']>5) $data[$key]['login_name'] = '积极';
 			
 			$data[$key]['photo_time'] = $val['photo_time'];
 			
 			//标签
 			$data[$key]['label_name'] = $cameraman_add_user_label->get_info_by_user_id($val['user_id']);
 		}
 		$fileName = "运营部excel";
 		//$title    = "运营部excel";
 		$headArr = array('用户ID','APP昵称','姓名', '手机号码', '微信', '拍摄风格','注册时间','最后登录时间','登录次数',' 消费次数','消费总金额','上月交易额','上月交易次数','平均消费金额','消费区间','活跃度','空闲时间','标签');
 		//getExcel($fileName,$title,$headArr,$data);
        Excel_v2::start($headArr,$data,$fileName);
 		exit;
 	}
 }

 $setParam = array();
 
 if(strlen($name) >0)
 {
 	$setParam['name'] = $name;
 }
 if (strlen($cellphone)>0)
 {
 	$setParam['cellphone'] = $cellphone;
 }
 if ($user_id > 0)
 {
 	$setParam['user_id'] = $user_id;
 }
 if (trim($sort) >0)
 {
 	$setParam['sort'] = $sort;
 }
 
 $tpl->assign($setParam);
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
 $tpl->output(); 

?>