<?php 

	include_once 'common.inc.php';
    check_authority(array('cameraman'));
	//二维数组转化为一维数组
    function change_arr($arr)
    {
        $tmp_arr = array();
        if (!empty($arr) && is_array($arr)) 
        {
            foreach ($arr as $key => $vo) 
            {
                $tmp_arr[] = $vo['uid'];
            }
        }
        return $tmp_arr;
    }
    //排序
    function select_sort($sort)
    {
        $sort_tmp;
        switch ($sort) {
            case 'uid_desc':
                $sort_tmp = "uid DESC"; 
                break;
            case 'ptime_desc':
                $sort_tmp = "inputer_time DESC";
                break;
            case 'ptime_asc':
                $sort_tmp = "inputer_time ASC";
                break;
            default:
                $sort_tmp = "uid DESC";
                break;
        }
        return $sort_tmp;
    }
	$page_obj = new show_page ();
	$show_count = 20;
	$cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
    $tpl = new SmartTemplate("cameraman_quick_search.tpl.htm");
    $act = $_INPUT['act'] ? $_INPUT['act'] : '';
    $sex = $_INPUT['sex'] ? $_INPUT['sex'] : 0;
    $is_studio = isset($_GET['is_studio']) ? intval($_GET['is_studio']) : -1;
    $min_age   = $_INPUT['min_age'] ?  intval($_INPUT['min_age']) : '';
    $max_age   = $_INPUT['max_age'] ?  intval($_INPUT['max_age']) : '';
    $city      = isset($_GET['city']) ? intval($_GET['city']) : -1;
    $p_state   = isset($_GET['p_state']) ? intval($_GET['p_state']) : -1;
    $join_age  = $_INPUT['join_age'] ? intval($_INPUT['join_age']) : '';
    $style     = isset($_GET['style']) ? $_GET['style'] : -1;
    $min_price = $_INPUT['min_price'] ? intval($_INPUT['min_price']) : '';
    $max_price = $_INPUT['max_price'] ? intval($_INPUT['max_price']) : '';
    $is_fview  = isset($_GET['is_fview']) ? intval($_GET['is_fview']) : -1;
    $min_time  = $_INPUT['min_time'] ? $_INPUT['min_time'] : '';
    $max_time  = $_INPUT['max_time'] ? $_INPUT['max_time'] : '';
    $start_follow_time  = $_INPUT['start_follow_time'] ? $_INPUT['start_follow_time'] : '';
    $end_follow_time    = $_INPUT['end_follow_time'] ? $_INPUT['end_follow_time'] : '';
    $sort               = $_INPUT['sort'] ? $_INPUT['sort'] : 'uid_desc';
    $where_str = "";
    switch ($act) {
    	case 'search':
    		 $where_str_list = "";
    		  if (!empty($sex)) 
    		  {
    		  	$where_str_list .= "sex = {$sex}";
    		  }
    		  //工作室
    		  if ($is_studio != -1) 
    		  {
    		  	if (!empty($where_str_list)) 
    		  	{
    		  		$where_str_list .=" AND ";
    		  		# code...
    		  	}
    		  	$where_str_list .= " is_studio = {$is_studio} ";
    		  }
    		  //年龄
    		  if (!empty($min_age) && !empty($max_age)) 
    		  {
    		  	if (!empty($where_str_list)) 
    		  	{
    		  		$where_str_list .= " AND ";
    		  	}
    		  	$where_str_list .= " (YEAR(NOW()) - left(birthday,4)) BETWEEN {$min_age} AND {$max_age} ";
    		  }
    		  //城市条件
    		  if ($city != -1) 
    		  {
    		  	if (!empty($where_str_list)) 
    		  	{
    		  		$where_str_list .= " AND ";
    		  	}
    		  	$where_str_list .=" city = {$city} ";
    		  }
    		 //职业
    		  if ($p_state != -1) 
    		  {
    		  	if (!empty($where_str_list)) 
    		  	{
    		  		$where_str_list .= " AND ";
    		  	}
    		  	$where_str_list .=" p_state = {$p_state} ";
    		  }
    		//摄龄
    		 if (!empty($join_age)) 
    		 {
    		 	if (!empty($where_str_list)) 
    		 	{
    		 		$where_str_list .= " AND ";
    		 	}
    		 	$where_str_list .= " join_age = {$join_age} ";
    		 }
    		//风格
    		if ($style != -1) 
    		{
    			$style_where_str = "style = {$style}";
    			$style_uid = $cameraman_add_obj->cameraman_search_style($style_where_str,'', 'uid DESC', 'DISTINCT(uid)');
    			//echo '风格';print_r($style_uid);
    			if (empty($style_uid)) 
                {
                    $list = array();
                    break;
                }
    		}
    		//拍摄花费
    		if (!empty($min_price) && !empty($max_price)) 
    		{
    			/*if (!empty($where_str_list)) 
    			{
    				$where_str_list .= " AND ";
    			}
    			$where_str_list .= " month_take BETWEEN {$min_price} AND {$max_price} ";*/
    		}
    		//远景
    		if ($is_fview != -1) 
    		{
    			if (!empty($where_str_list)) 
    			{
    				$where_str_list .= " AND ";
    			}
    			$where_str_list .= " is_fview = {$is_fview}";
    		}
    		//拍摄次数
    		if (!empty($min_time) && !empty($max_time)) 
    		{
    			/*if (!empty($where_str_list)) 
    			{
    				$where_str_list .= " AND ";
    			}
    			$where_str_list .= " attend_total BETWEEN {$min_time} AND {$max_time} ";*/
    			# code...
    		}
    		//跟进时间
    		if (!empty($start_follow_time) && !empty($end_follow_time)) 
    		{
    			$start_temp_follow = strtotime($start_follow_time);
                $end_temp_follow   = strtotime($end_follow_time);
    			$f_where_str = " UNIX_TIMESTAMP(DATE_FORMAT(follow_time, '%Y-%m-%d')) BETWEEN {$start_temp_follow} AND {$end_temp_follow} ";
    			$follow_uid = $cameraman_add_obj->get_follow_cameraman_uid($f_where_str,'', 'uid DESC', 'DISTINCT(uid)');
                if (empty($follow_uid) || !is_array($follow_uid)) 
                {
                    $list = array();
                    break;
                    # code...
                }
    		}
    		//echo($where_str_list);
    		$uid = $cameraman_add_obj->get_cameraman_search_uid($where_str_list,'', 'uid DESC', 'DISTINCT(uid)' );
    		//print_r($uid);
    		if (!empty($uid) && is_array($uid)) 
            {
               $uid = change_arr($uid);
               //交集风格uid
               if (!empty($style_uid) && is_array($style_uid)) 
               {
                   $style_uid = change_arr($style_uid);
                   $uid = array_intersect($uid, $style_uid);
               }
               //交集跟进uid
               if (!empty($follow_uid) && is_array($follow_uid)) 
               {
                    $follow_uid = change_arr($follow_uid);
                    $uid = array_intersect($uid, $follow_uid);
               }
            }
            elseif(empty($uiid))
            {
                $list = array();
                break;
            }
            //得到总的uid
            if (empty($uid) || !is_array($uid)) 
            {
            	$list  = array();
            	break;
            	# code...
            }
            else
            {
            	foreach ($uid as $key => $id) 
            	{
            		 if (!empty($where_str)) 
                     {
                        $where_str .= " OR ";
                     }
                    $where_str .= " uid = {$id} ";
            	}
            }
            //print_r($where_str);//exit;
            $total_count = $cameraman_add_obj->get_cameraman_list(true, $where_str);
              //print_r($total_count);exit;
            $page_obj->setvar 
            (
              array
              (
              		'sex'=> $sex, 'is_studio'=> $is_studio,
                    'min_age' => $min_age, 'max_age' => $max_age,
                    'city'    => $city, 'p_state' => $p_state,
                    'join_age' => $join_age, 'style' => $style,
                    'min_price'=> $min_price, 'max_price' => $max_price,
                    'is_fview'=> $is_fview, 'min_time' => $min_time,
                    'start_follow_time' => $start_follow_time,
                    'end_follow_time'   => $end_follow_time
               )
            );
            $page_obj->set ( $show_count, $total_count );
            $list = $cameraman_add_obj->get_cameraman_list(false, $where_str,  select_sort($sort), $page_obj->limit(), $fields = '*');
    		break;
    	
    	default:
    		$total_count = $cameraman_add_obj->get_cameraman_list(true, $where_str);
              //print_r($total_count);exit;
            $page_obj->setvar ();
            $page_obj->set ( $show_count, $total_count );
            //die(select_sort($sort));
            $list = $cameraman_add_obj->get_cameraman_list(false, $where_str,  select_sort($sort), $page_obj->limit(), $fields = '*');
    		break;
    }

    //性别处理
    if ($sex == 1) 
    {
    	$sex_1 = "selected='true'";
    	$tpl->assign('sex_1', $sex_1);
    }
    elseif ($sex == 2) 
    {
    	$sex_2 = "selected='true'";
    	$tpl->assign('sex_2', $sex_2);
    }
    //工作室处理
    if ($is_studio == 0) 
    {
    	$is_studio_s = "selected='true'";
    	$tpl->assign('is_studio_s', $is_studio_s);
    }
    elseif ($is_studio == 1) 
    {
    	$is_studio_1 = "selected='true'";
    	$tpl->assign('is_studio_1', $is_studio_1);
    }
    //城市处理
    switch ($city) {
    	case 0:
    		$city_c = "selected='true'";
    		$tpl->assign('city_c', $city_c);
    		break;
    	case 1:
    		$city_1 = "selected='true'";
    		$tpl->assign('city_1', $city_1);
    		break;
    	case 2:
    		$city_2 = "selected='true'";
    		$tpl->assign('city_2', $city_2);
    		break;
    	case 3:
    		$city_3 = "selected='true'";
    		$tpl->assign('city_3', $city_3);
    		break;
    	case 4:
    		$city_4 = "selected='true'";
    		$tpl->assign('city_4', $city_4);
    		break;
    	case 5:
    		$city_5 = "selected='true'";
    		$tpl->assign('city_5', $city_5);
    		break;
    }
    //职业处理
    switch ($p_state) {
    	case 0:
    		$p_state_s = "selected='true'";
    		$tpl->assign('p_state_s', $p_state_s);
    		break;
    	case 1:
    		$p_state_1 = "selected='true'";
    		$tpl->assign('p_state_1', $p_state_1);
    		break;
    	case 2:
    		$p_state_2 = "selected='true'";
    		$tpl->assign('p_state_2', $p_state_2);
    		break;
    }
    //风格处理
    switch ($style) {
    	case 0:
    		$style_s = "selected='true'";
    		$tpl->assign('style_s', $style_s);
    		break;
    	case 1:
    		$style_1 = "selected='true'";
    		$tpl->assign('style_1', $style_1);
    		break;
    	case 2:
    		$style_2 = "selected='true'";
    		$tpl->assign('style_2', $style_2);
    		break;
    	case 3:
    		$style_3 = "selected='true'";
    		$tpl->assign('style_3', $style_3);
    		break;
    	case 4:
    		$style_4 = "selected='true'";
    		$tpl->assign('style_4', $style_4);
    		break;
    	case 5:
    		$style_5 = "selected='true'";
    		$tpl->assign('style_5', $style_5);
    		break;
    	case 6:
    		$style_6 = "selected='true'";
    		$tpl->assign('style_6', $style_6);
    		break;
    	case 7:
    		$style_7 = "selected='true'";
    		$tpl->assign('style_7', $style_7);
    		break;
    	case 8:
    		$style_8 = "selected='true'";
    		$tpl->assign('style_8', $style_8);
    		break;
    	case 9:
    		$style_9 = "selected='true'";
    		$tpl->assign('style_9', $style_9);
    		break;
    }
    //远景处理
    if ($is_fview == 0) 
    {
    	$is_fview_f = "selected='true'";
    	$tpl->assign('is_fview_f', $is_fview_f);
    }
    elseif ($is_fview == 1) 
    {
    	$is_fview_1 = "selected='true'";
    	$tpl->assign('is_fview_1', $is_fview_1);
    }
    
    /*列表显示处理*/
    if (!empty($list) && is_array($list)) 
    {
       foreach ($list as $key_city => $vo) 
       {
          //常驻城市名称获取
          $mycity = '';
          //echo $vo_city['city'];
          switch ($vo['city']) 
          {
            case 0:
                  $mycity = '广州';
                  break;
            case 1:
                  $mycity = '上海';
                  break;
            case 2:
                  $mycity = '北京';
                  break;
            case 3:
                  $mycity = '成都';
                  break;
            case 4:
                  $mycity = '武汉';
                  break;
            case 5:
                  $mycity = '深圳';
                  break;
          }
           $list[$key_city]['city'] = $mycity;
           //拍摄风格处理
           $style_list = $cameraman_add_obj->cameraman_list_style($vo['uid']);
           $style ="";
           foreach ($style_list as $style_key => $vstyle) 
           {
               switch ($vstyle['style']) 
               {
                   case 0:
                       $mystyle = '欧美';
                       break;
                   case 1:
                       $mystyle = '情绪';
                       break;
                   case 2:
                       $mystyle = '清新';
                       break;
                   case 3:
                       $mystyle = '复古';
                       break;
                  case 4:
                       $mystyle = '韩系';
                       break;
                  case 5:
                       $mystyle = '日系';
                       break;
                  case 6:
                       $mystyle = '性感';
                       break;
                  case 7:
                       $mystyle = '街拍';
                       break;
                  case 8:
                       $mystyle = '胶片';
                       break;
                  case 9:
                       $mystyle = '商业';
                       break;
               }
               if ($style_key != 0) 
               {
                   $style .=',';
               }
               $style .=$mystyle;
           }
           $list[$key_city]['style'] = $style;
       }
      
    }
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
    

    $tpl->assign('min_age', $min_age);
    $tpl->assign('max_age', $max_age);
    $tpl->assign('join_age', $join_age);
    $tpl->assign('min_price', $min_price);
    $tpl->assign('max_price', $max_price);
    $tpl->assign('min_time', $min_time);
    $tpl->assign('max_time', $max_time);
    $tpl->assign('start_follow_time', $start_follow_time);
    $tpl->assign('end_follow_time', $end_follow_time);
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign('list', $list);
    $tpl->assign('total_count', $total_count);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();


 ?>