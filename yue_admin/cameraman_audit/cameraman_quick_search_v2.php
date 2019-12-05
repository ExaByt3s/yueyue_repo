<?php 
	
/**
 * 摄影师快速筛选版本2
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
	
	//分页
	$page_obj = new show_page ();
	$show_count = 20;
		
    $tpl = new SmartTemplate("cameraman_quick_search_v2.tpl.htm");
    
    $act       = trim($_INPUT['act']);
    $name      = trim($_INPUT['name']);
    $cellphone = trim($_INPUT['cellphone']);
    $user_id   = trim($_INPUT['user_id']);
    
    //排序条件
    $sort      = trim($_INPUT['sort']);
    
    //条件
    $where_str = '';
    $setParam  = array();
    
    if (strlen($name) >0) 
    {
    	if(strlen($where_str) >0) $where_str .= ' AND ';
        $where_str .= "C.name like '%".mysql_escape_string($name)."%'";
        $setParam['name'] = $name;
    }
    if (strlen($cellphone) >0) 
    {
       if(strlen($where_str)>0) $where_str .= ' AND ';
       $where_str .= "P.cellphone = '".mysql_escape_string($cellphone)."'";
       $setParam['cellphone'] = $cellphone;
    }
    if($user_id >0)
    {
    	if(strlen($where_str)>0) $where_str .= ' AND ';
    	$where_str .= "C.user_id = {$user_id}";
    	$setParam['user_id'] = $user_id;
    }
    
    
    //默认排序
    $order_sort = 'user_id DESC';
    if(strlen($sort)>0)
    {
    	if($sort == 'add_time_asc') $order_sort = 'C.add_time ASC,C.user_id DESC';
    	elseif($sort == 'add_time_desc') $order_sort = 'C.add_time DESC,C.user_id DESC';
    	$setParam['sort'] = $sort;
    }
    
    
    $total_count = $cameraman_add_v2_obj->get_list(true, $where_str);
    $page_obj->setvar($setParam);
    $page_obj->set($show_count,$total_count);
    $list = $cameraman_add_v2_obj->get_list(false, $where_str,$order_sort,$page_obj->limit());
	
    if(!is_array($list)) $list = array();
    
    $sql_tmp_str = '';
    foreach ($list as $key=>$val)
    {
    	//$list[$key]['location_name'] = get_poco_location_name_by_location_id($val['location_id']);
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
    /* if(strlen($sql_tmp_str) >0)
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
    
    $tpl->assign('list', $list);
    $tpl->assign($setParam);
    $tpl->assign('total_count', $total_count);
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();

 ?>