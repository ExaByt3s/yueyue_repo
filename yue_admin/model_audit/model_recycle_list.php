<?php 
/*
 * 模特库回收站
 *
*/

	include_once 'common.inc.php';
    include_once 'include/common_function.php';
	  
    $page_obj = new show_page ();
	$show_count = 20;
	
	$model_add_obj  = POCO::singleton('pai_model_add_class');
    $tpl  = new SmartTemplate("model_recycle_list.tpl.htm");
    $recycle   = 1;
    //echo $nick_name;
    $sort      = $_INPUT['sort'] ? $_INPUT['sort'] : 'ptime_desc';
    //判断
    $where_str = "1 AND recycle = {$recycle}";
    //判断是否存在 地区管理员
    if(strlen($authority_list[0]['location_id']) >0)
    {
    	$where_str .= " AND location_id IN ({$authority_list[0]['location_id']})";
    	//$setParam['aut_location'] = true;
    }
    
    
    $total_count = $model_add_obj->get_model_list(true, $where_str);
              //print_r($total_count);exit;
    $page_obj->setvar ();
    $page_obj->set ( $show_count, $total_count );
    $list = $model_add_obj->get_model_list(false, $where_str, change_sort($sort), $page_obj->limit(), $fields = '*');

    //排序
    switch ($sort) {
        case 'uid_desc':
            $sort_1 = "selected='true'";
            $tpl->assign('sort_1', $sort_1);
            break;
        case 'ptime_asc':
            $sort_2 = "selected='true'";
            $tpl->assign('sort_2', $sort_2);
            break;
        case 'ptime_desc':
            $sort_3 = "selected='true'";
            $tpl->assign('sort_3', $sort_3);
            break;
        default:
            # code...
            break;
    }
    //常驻城市名称获取
    if (!empty($list) && is_array($list)) 
    {
       foreach ($list as $key_city => $vo) 
       {
          $key_list = get_poco_location_name_by_location_id($vo['location_id']);
           //var_dump($key_list);
           $city_name = '';
           if (!empty($key_list)) 
           {
               $city_name = $key_list;
           }
           $list[$key_city]['city'] = $city_name;
       }
      
    }
    $tpl->assign('list', $list);
    $tpl->assign('total_count', $total_count);
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();

 ?>