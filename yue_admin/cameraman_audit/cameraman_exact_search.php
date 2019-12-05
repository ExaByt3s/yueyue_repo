<?php 

/**
 * 摄影师精准筛选版本2
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月27日
 * @version 2
 */

    include_once 'common.inc.php';
    include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
    include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php");
    include_once ("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
	
	$cameraman_add_v2_obj  = POCO::singleton('pai_cameraman_add_v2_class');
	
	$user_obj = POCO::singleton('pai_user_class');
	
	//摄影师打标签类
	$cameraman_add_user_label = POCO::singleton('pai_cameraman_add_user_label_class');
	
	//分页类
	$page_obj   = new show_page ();
	$show_count = 20;
	
    $tpl = new SmartTemplate("cameraman_exact_search.tpl.htm");
    
    
    $act            = trim($_INPUT['act']);
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
    
    //条件
    $where_str = '';
    $setParam  = array();
    
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
    
    $total_count = $cameraman_add_v2_obj->get_search_list(true,$label_id,$f_start_time,$f_end_time, $where_str);
    $page_obj->setvar($setParam);
    $page_obj->set($show_count,$total_count);
    
    $list     = $cameraman_add_v2_obj->get_search_list(false,$label_id,$f_start_time,$f_end_time, $where_str,$order_sort,$page_obj->limit());
    
    if(!is_array($list)) $list = array();
    
    $sql_tmp_str = '';
    foreach ($list as $key=>$val)
    {
    	//echo $val['login_sum'];
    	if($val['login_sum']==0) $list[$key]['login_name'] = '沉默';
    	if($val['login_sum']>0 && $val['login_sum'] <= 5) $list[$key]['login_name'] = '活跃';
    	if($val['login_sum']>5) $list[$key]['login_name'] = '积极';
    	//消费水平
    	if($val['consumption_level']>=0 && $val['consumption_level']<=100 ) $list[$key]['consumption_name'] = '低价';
    	if($val['consumption_level']>=101 && $val['consumption_level']<=200 ) $list[$key]['consumption_name'] = '较低';
    	if($val['consumption_level']>=201 && $val['consumption_level']<=400 ) $list[$key]['consumption_name'] = '适中';
    	if($val['consumption_level']>=401 && $val['consumption_level']<=600 ) $list[$key]['consumption_name'] = '较高';
    	if($val['consumption_level']>600) $list[$key]['consumption_name'] = '高价';
    	if($key !=0) $sql_tmp_str .= ',';
    	$sql_tmp_str .= $val['user_id'];
    }
    
   /*  if(strlen($sql_tmp_str) >0)
    {
    	$sql_nickname_str = "user_id IN ($sql_tmp_str)";
    	$user_ret = $user_obj->get_user_list(false,$sql_nickname_str,'user_id DESC',"0,{$show_count}",'user_id,nickname,cellphone,location_id');
    	//print_r($user_ret);
    	if(is_array($user_ret)) $list = combine_arr($list, $user_ret, 'user_id');
    } */
    
    
    foreach ($list as $key=>$vall)
    {
    	$list[$key]['location_name'] = get_poco_location_name_by_location_id($vall['location_id']);
    	$list[$key]['label_name']    = $cameraman_add_user_label->get_info_by_user_id($vall['user_id']);
    	$list[$key]['goods_style']    = str_replace('|', ',', $vall['goods_style']);
    }
    //print_r($list);
    
    
    
    //城市选择
    $province_list = change_assoc_arr($arr_locate_a);
    foreach ($province_list as $key => $vo)
    {
    	if ($vo['c_id'] == $province)
    	{
    		$province_list[$key]['selected_prov'] = "selected='true'";
    	}
    }
    if ($province>0)
    {
    	$city_list = ${'arr_locate_b'.$province};
    	$city_list = change_assoc_arr($city_list);
    	foreach ($city_list as $c_key => $vo)
    	{
    		if ($vo['c_id'] == $location_id)
    		{
    			$city_list[$c_key]['selected_city'] = "selected='true'";
    		}
    	}
    } 
    
    //print_r($list);
   	$tpl->assign($setParam);
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign('list', $list);
    $tpl->assign('total_count', $total_count);
    $tpl->assign('province_list',$province_list);
    $tpl->assign('city_list',$city_list);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();


 ?>