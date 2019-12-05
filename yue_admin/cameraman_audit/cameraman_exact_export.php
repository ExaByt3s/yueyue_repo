<?php
/**
 * 数据导出
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-05-04 10:37:31
 * @version 1
 */
 //include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
 include_once 'common.inc.php';
ini_set('memory_limit', '256M');
 //引入常用函数
 include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
 include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
 
 $cameraman_add_v2_obj  = POCO::singleton('pai_cameraman_add_v2_class');
 $user_obj = POCO::singleton('pai_user_class');
 
 //摄影师打标签类
 $cameraman_add_user_label = POCO::singleton('pai_cameraman_add_user_label_class');
 
 
 $tpl = new SmartTemplate("cameraman_exact_export.tpl.htm");
 
 $act       = trim($_INPUT['act']);
 
 //原本有的
 $sex            = trim($_INPUT['sex']);
 $p_status       = trim($_INPUT['p_status']);
 $start_reg_time = trim($_INPUT['start_reg_time']);
 $end_reg_time   = trim($_INPUT['end_reg_time']);
 $province       = intval($_INPUT['province']);
 $location_id    = intval($_INPUT['location_id']);
 $join_age       = intval($_INPUT['join_age']);
 $f_start_time   = trim($_INPUT['f_start_time']);
 $f_end_time     = trim($_INPUT['f_end_time']);
 //风格
 $goods_style    = trim($_INPUT['goods_style']);
 $is_fview       = intval($_INPUT['is_fview']);
 
 //标签
 $label_id       = trim($_INPUT['label_id']);
 
 $label_name     = trim($_INPUT['label_name']);
 
 $pp_start_price = floatval($_INPUT['pp_start_price']);
 $pp_end_price   = floatval($_INPUT['pp_end_price']);
 
 //平均消费金额
 $avg_start_price = floatval($_INPUT['avg_start_price']);
 $avg_end_price   = floatval($_INPUT['avg_end_price']);
 
 $login_sum = intval($_INPUT['login_sum']);
 //$end_login_sum   = intval($_INPUT['end_login_sum']);
 
 $last_start_login_time = trim($_INPUT['last_start_login_time']);
 $last_end_login_time   = trim($_INPUT['last_end_login_time']);
 
 //上月交易次数
 $pp_start_num = intval($_INPUT['pp_start_num']);
 $pp_end_num   = intval($_INPUT['pp_end_num']);
 
 //消费区间
 $consumption_level    = trim($_INPUT['consumption_level']);
 
 //空闲时间
 $photo_time   = trim($_INPUT['photo_time']);

 
 //排序
 $sort         = trim($_INPUT['sort']);
 
 //导出格式
 $layout    = trim($_INPUT['layout']);
 $role      = trim($_INPUT['role']);
 $start_num = intval($_INPUT['start_num']);
 $end_num   = intval($_INPUT['end_num']);
 //条件
 $where_str = '';
 //快速筛选
 if ($act == 'quick_search')
 {
 	$where_str = '';
    if(strlen($sex)>0)
    {
    	if(strlen($where_str) >0) $where_str .= ' AND ';
    	$where_str .= "C.sex ='".mysql_escape_string($sex)."'";
    	$setParam['sex'] = $sex;
    }
    if(strlen($p_status) >0)
    {
    	if (strlen($where_str) >0) $where_str .= ' AND ';
    	$where_str .= "C.p_status = '".mysql_escape_string($p_status)."'";
    	$setParam['p_status'] = $p_status;
    }
    if (strlen($start_reg_time) >0)
    {
    	if(strlen($where_str) >0) $where_str .= ' AND ';
    	$where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(C.register_time),'%Y-%m-%d') >= '".mysql_escape_string($start_reg_time)."'";
    	$setParam['start_reg_time'] = $start_reg_time;
    }
    if (strlen($end_reg_time) > 0)
    {
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(C.register_time),'%Y-%m-%d') <= '".mysql_escape_string($end_reg_time)."'";
    	$setParam['end_reg_time'] = $end_reg_time;
    }
    if($province >0)
    {
    	if(strlen($where_str) >0) $where_str .= ' AND ';
    	if($location_id >0)
    	{
    		$where_str .= "P.location_id = {$location_id}";
    		$setParam['location_id']   = $location_id;
    	}
    	else
    	{
    		$where_str .= "left(P.location_id,6) = {$province}";
    	}
    	$setParam['province'] = $province;
    }
    if($join_age>0)
    {
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "(DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(C.join_time)),'%Y')+0) = {$join_age}";
    	$setParam['join_age'] = $join_age;
    }
    
    //$f_start_time和 $f_end_time 使用连表查
    if (strlen($f_start_time)>0)
    {
    	$setParam['f_start_time'] = $f_start_time;
    }
    
    if(strlen($f_end_time)>0)
    {
    	$setParam['f_end_time'] = $f_end_time;
    }
    
    if(strlen($goods_style)>0)
    {
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "C.goods_style LIKE '%".mysql_escape_string($goods_style)."%'";
    	$setParam['goods_style'] = $goods_style;
    }
    if($is_fview>0)
    {
    	if (strlen($where_str)>0)$where_str .= ' AND ';
    	$where_str .= "C.is_fview = {$is_fview}";
    	$setParam['is_fview'] = $is_fview;
    }
    
    //标签连表查$label
    if (strlen($label_id)>0)
    {
    	$setParam['label_id'] = $label_id;
    }
    if(strlen($label_name))
    {
    	$setParam['label_name'] = $label_name;
    }
    
    if ($pp_start_price >0)
    {
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "C.prev_month_price >= {$pp_start_price}";
    	$setParam['pp_start_price'] = $pp_start_price;
    }
    
    if ($pp_end_price >0)
    {
    	if(strlen($where_str) >0) $where_str .= ' AND ';
    	$where_str .= "C.prev_month_price <= {$pp_end_price}";
    	$setParam['pp_end_price'] = $pp_end_price;
    }
    
    if ($avg_start_price >0)
    {
    	if(strlen($where_str) >0) $where_str .= ' AND ';
    	$where_str .= "C.avg_month_price >= {$avg_start_price}";
    	$setParam['avg_start_price'] = $avg_start_price;
    }
    if($avg_end_price >0)
    {
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "C.avg_month_price <= {$avg_end_price}";
    	$setParam['avg_end_price'] = $avg_end_price;
    }
    //待定
    if ($login_sum >0)
    {
    	if(strlen($where_str) >0)$where_str .= ' AND ';
    	//$where_str .= "login_sum >= {$start_login_sum}";
    	if($login_sum ==1) $where_str .= "C.login_sum =0";
    	if($login_sum ==2) $where_str .= "C.login_sum >=1 AND C.login_sum<=5";
    	if($login_sum ==3) $where_str .= "C.login_sum >5";
    	$setParam['login_sum'] = $login_sum;
    }
    if (strlen($last_start_login_time)>0)
    {
    	if(strlen($where_str) >0) $where_str .= ' AND ';
    	$where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(C.last_login_time),'%Y-%m-%d')>='".mysql_escape_string($last_start_login_time)."'";
    	$setParam['last_start_login_time'] = $last_start_login_time;
    }
    if(strlen($last_end_login_time) >0)
    {
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(C.last_login_time),'%Y-%m-%d')<='".mysql_escape_string($last_end_login_time)."'";
    	$setParam['last_end_login_time'] = $last_end_login_time;
    }
    if ($pp_start_num>0)
    {
    	//echo $pp_start_num;
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "C.prev_month_num >= {$pp_start_num}";
    	$setParam['pp_start_num'] = $pp_start_num;
    }
    if($pp_end_num >0)
    {
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "C.prev_month_num <= {$pp_end_num}";
    	$setParam['pp_end_num'] = $pp_end_num;
    }
    //待定
    if (strlen($consumption_level) >0)
    {
    	if (strlen($where_str) >0) $where_str .= ' AND ';
    	if($consumption_level == 1) $where_str .= "C.consumption_level >= 0 AND C.consumption_level <= 100";
    	if($consumption_level == 2) $where_str .= "C.consumption_level >= 101 AND C.consumption_level <= 200";
    	if($consumption_level == 3) $where_str .= "C.consumption_level >= 201 AND C.consumption_level <= 400";
    	if($consumption_level == 4) $where_str .= "C.consumption_level >= 401 AND C.consumption_level <= 600";
    	if($consumption_level == 5) $where_str .= "C.consumption_level > 600";
    	$setParam['consumption_level'] = $consumption_level;
    }
    
    //空闲时间
    if(strlen($photo_time)>0)
    {
    	if(strlen($where_str)) $where_str .= ' AND ';
    	$where_str .= "C.photo_time='".mysql_escape_string($photo_time)."'";
    	$setParam['photo_time'] = $photo_time;
    }
    
    
    //默认排序
    $order_sort = 'C.user_id DESC';
    if(strlen($sort)>0)
    {
    	if($sort == 'add_time_asc') $order_sort = 'C.add_time ASC,C.user_id DESC';
    	elseif($sort == 'add_time_desc') $order_sort = 'C.add_time DESC,C.user_id DESC';
    	$setParam['sort'] = $sort;
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
 	$list  = $cameraman_add_v2_obj->get_search_list(false,$label_id,$f_start_time,$f_end_time, $where_str,$order_sort,$limit);
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
 		$title    = "市场部excel";
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
 		$title    = "运营部excel";
 		$headArr = array('用户ID','APP昵称','姓名', '手机号码', '微信', '拍摄风格','拍摄风格','注册时间','最后登录时间','登录次数',' 消费次数','消费总金额','上月交易额','上月交易次数','平均消费金额','消费区间','活跃度','空闲时间','标签');
       /* $headArr = array('用户ID','APP昵称','姓名', '手机号码', '微信', '拍摄风格','注册时间','最后登录时间','登录次数',' 消费次数','消费总金额','上月交易额','上月交易次数','平均消费金额','消费区间','活跃度','空闲时间','标签');*/
 		//getExcel($fileName,$title,$headArr,$data);
        Excel_v2::start($headArr,$data,$fileName);
 		exit;
 	}
 }

 $setParam = array();
 
 if(strlen($sex)>0)
 {
 	$setParam['sex'] = $sex;
 }
 if(strlen($p_status) >0)
 {
 	$setParam['p_status'] = $p_status;
 }
 if (strlen($start_reg_time) >0)
 {
 	$setParam['start_reg_time'] = $start_reg_time;
 }
 if (strlen($end_reg_time) > 0)
 {
 	$setParam['end_reg_time'] = $end_reg_time;
 }
 if($province >0)
 {
 	if($location_id >0)
 	{
 		$setParam['location_id']   = $location_id;
 	}
 	$setParam['province'] = $province;
 }
 if($join_age>0)
 {
 	$setParam['join_age'] = $join_age;
 }
 
 //$f_start_time和 $f_end_time 使用连表查
 if (strlen($f_start_time)>0)
 {
 	$setParam['f_start_time'] = $f_start_time;
 }
 
 if(strlen($f_end_time)>0)
 {
 	$setParam['f_end_time'] = $f_end_time;
 }
 
 if(strlen($goods_style)>0)
 {
 	$setParam['goods_style'] = $goods_style;
 }
 if($is_fview>0)
 {
 	$setParam['is_fview'] = $is_fview;
 }
 
 //标签连表查$label
 if (strlen($label_id)>0)
 {
 	$setParam['label_id'] = $label_id;
 }
 if (strlen($pp_start_price) >0)
 {
 	$setParam['pp_start_price'] = $pp_start_price;
 }
 
 if (strlen($pp_end_price) >0)
 {
 	$setParam['pp_end_price'] = $pp_end_price;
 }
 
 if (strlen($avg_start_price) >0)
 {
 	$setParam['avg_start_price'] = $avg_start_price;
 }
 if(strlen($avg_end_price) >0)
 {
 	$setParam['avg_end_price'] = $avg_end_price;
 }
 //待定
 if ($login_sum >0)
 {
 	$setParam['login_sum'] = $login_sum;
 }
 if (strlen($last_start_login_time)>0)
 {
 	$setParam['last_start_login_time'] = $last_start_login_time;
 }
 if(strlen($last_end_login_time) >0)
 {
 	$setParam['last_end_login_time'] = $last_end_login_time;
 }
 if ($pp_start_num>0)
 {
 	$setParam['pp_start_num'] = $pp_start_num;
 }
 if($pp_end_num >0)
 {
 	$setParam['pp_end_num'] = $pp_end_num;
 }
 //待定
 if (strlen($consumption_level) >0)
 {
 	$setParam['consumption_level'] = $consumption_level;
 }
 
 //空闲时间
 if(strlen($photo_time)>0)
 {
 	$setParam['photo_time'] = $photo_time;
 }
 //默认排序
 if(strlen($sort)>0)
 {
 	$setParam['sort'] = $sort;
 }
 /* print_r($setParam);
 exit; */
 $tpl->assign($setParam);
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
 $tpl->output(); 

?>