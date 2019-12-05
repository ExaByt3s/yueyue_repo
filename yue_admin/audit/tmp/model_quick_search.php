<?php 


    include_once 'common.inc.php';
    include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
    include_once 'include/locate_file.php';
	include_once 'include/common_function.php';
    //查看权限
    check_authority_by_list('exit_type',$authority_list,'model', 'is_select');
	$page_obj = new show_page ();
	$show_count = 20;
    $model_add_obj  = POCO::singleton('pai_model_add_class');
	$user_obj       = POCO::singleton('pai_user_class');
    $tpl = new SmartTemplate("model_quick_search.tpl.htm");
    $recycle = 0;
    $act = $_INPUT['act'] ? $_INPUT['act'] : '';
    $sex = isset($_INPUT['sex']) ? intval($_INPUT['sex']) : -1;
    $start_shoe_size =  $_INPUT['start_shoe_size'] ? $_INPUT['start_shoe_size'] : '';
    $end_shoe_size   =  $_INPUT['end_shoe_size'] ? $_INPUT['end_shoe_size'] : '';
    $start_join_time =  $_INPUT['start_join_time'] ? $_INPUT['start_join_time'] : '';
    $end_join_time   =  $_INPUT['end_join_time'] ? $_INPUT['end_join_time'] : '';
    $min_height      =  $_INPUT['min_height'] ? $_INPUT['min_height'] : '';
    $max_height      =  $_INPUT['max_height'] ? $_INPUT['max_height'] : '';
    $min_age         =  $_INPUT['min_age'] ? $_INPUT['min_age'] : '';
    $max_age         =  $_INPUT['max_age'] ? $_INPUT['max_age'] : '';
    $min_price       =  $_INPUT['min_price'] ? $_INPUT['min_price'] : '';
    $max_price       =  $_INPUT['max_price'] ? $_INPUT['max_price'] : '';
    $hour            =  $_INPUT['hour'] ? $_INPUT['hour'] : 'twoh_price';
    $min_weight      =  $_INPUT['min_weight'] ? $_INPUT['min_weight']: '';
    $max_weight      =  $_INPUT['max_weight'] ? $_INPUT['max_weight']: '';
    $p_state         =  $_INPUT['p_state'] ?  intval($_INPUT['p_state']) : 0;
    $start_follow_time = $_INPUT['start_follow_time'] ? $_INPUT['start_follow_time'] : '';
    $end_follow_time = $_INPUT['end_follow_time'] ? $_INPUT['end_follow_time'] : '';
    $cup_id          = $_INPUT['cup_id'] ? intval($_INPUT['cup_id']) : 0;
    $cup_a           = $_INPUT['cup_a'] ?  intval($_INPUT['cup_a']) : 0 ;
    $style           = isset($_INPUT['style']) ? intval($_INPUT['style']) : -1;
    $join            = $_INPUT['join'] ? $_INPUT['join'] : '';
    $enter           = $_INPUT['enter'] ? $_INPUT['enter'] : '';
    $grade           = $_INPUT['grade'] ? $_INPUT['grade'] : '';
    $province        = $_INPUT['province']  ? (int)$_INPUT['province']  : 0;
    $location_id     = $_INPUT['location_id']  ? (int)$_INPUT['location_id']  : 0;
    $p_enter_school_time = $_INPUT['p_enter_school_time'] ? $_INPUT['p_enter_school_time'] : '';
    $inputer_name        = $_INPUT['inputer_name'] ? $_INPUT['inputer_name'] : '';
    $start_inputer_time  = $_INPUT['start_inputer_time'] ? $_INPUT['start_inputer_time'] : '';
    $end_inputer_time    = $_INPUT['end_inputer_time'] ? $_INPUT['end_inputer_time'] : '';
    $p_school         = $_INPUT['p_school'] ? $_INPUT['p_school'] : '';
    $p_specialty      = $_INPUT['p_specialty'] ? $_INPUT['p_specialty'] : '';
    $min_score        = $_INPUT['min_score'] ? $_INPUT['min_score'] : '';
    $max_score        = $_INPUT['max_score'] ? $_INPUT['max_score'] : '';
    $label            = $_INPUT['label'] ? $_INPUT['label'] : '';
    $information_sources = $_INPUT['information_sources'] ? $_INPUT['information_sources'] : '';
    $sort             = $_INPUT['sort'] ? $_INPUT['sort'] : 'ptime_desc';
    $is_exist         = isset($_INPUT['is_exist']) ? intval($_INPUT['is_exist']) : -1;
    $other           = $_INPUT['other'] ? $_INPUT['other'] : '';//所有匹配
    $where_str_list = "recycle = {$recycle}";
    $inputer_list   = $model_add_obj->get_search_uid('inputer_name <> ""','', 'uid DESC', 'DISTINCT(inputer_name)');
    $label_list     = $model_add_obj->get_label_list('label <> ""','', 'uid DESC', 'DISTINCT(label)');
    //获取活动报名
    $join_list    = $model_add_obj->get_join_list('activity_join <> ""','', 'uid DESC', 'DISTINCT(activity_join)');
    //获取活动入围
    $enter_list    = $model_add_obj->get_enter_list('activity_enter <> ""','', 'uid DESC', 'DISTINCT(activity_enter)');
    switch ($act) {
        case 'search':
            $where_str = "1";
            $uid = array();
            if ($sex != -1) 
            {
               $where_str .= " AND sex = {$sex} ";
            }
            //码数
            if(!empty($start_shoe_size) && !empty($end_shoe_size))
            {
                $where_str .= " AND shoe_size BETWEEN {$start_shoe_size} AND {$end_shoe_size} ";
            }
            //加入时间
            if (!empty($start_join_time) && !empty($end_join_time)) 
            {
                $where_str .= " AND (left(p_join_time,4)) BETWEEN {$start_join_time} AND {$end_join_time} ";
            }
            //身高
            if (!empty($min_height) && !empty($max_height)) 
            {
                $where_str .= " AND height BETWEEN {$min_height} AND {$max_height} ";
            }
            //年龄
            if (!empty($min_age) && !empty($max_age)) 
            {
                $where_str .= " AND (YEAR(NOW()) - left(age,4)) BETWEEN {$min_age} AND {$max_age} ";
                # code...
            }
            //价格风格
            $style_where_str = '';
            if ($style != -1) 
            {
               $style_where_str = "style={$style}";
                # code...
            }
            if (!empty($min_price) && !empty($max_price) && !empty($hour)) 
            {
                if (!empty($style_where_str)) 
                {
                    $style_where_str .= " AND ";
                    # code...
                }
                $style_where_str .= "{$hour} BETWEEN {$min_price} AND {$max_price} ";
            }
            if (!empty($style_where_str)) 
            {
               $style_uid = $model_add_obj->search_style($style_where_str,'', 'uid DESC', 'DISTINCT(uid)');
                if (empty($style_uid)) 
                {
                    $list = array();
                    break;
                }
            }
            //活动报名
            if (!empty($join)) 
            {
                $j_where_str = "activity_join = '{$join}'";
                $j_uid = $model_add_obj->get_join_list($j_where_str,'', 'uid DESC', 'DISTINCT(uid)');
                if (empty($j_uid)) 
                {
                    $list = array();
                    break;
                } 
            }
            //活动入围
            if (!empty($enter)) 
            {
                $e_where_str = "activity_enter = '{$enter}'";
                $e_uid = $model_add_obj->get_enter_list($e_where_str,'', 'uid DESC', 'DISTINCT(uid)');
                if (empty($e_uid)) 
                {
                    $list = array();
                    break;
                } 
            }
            //身高
            if (!empty($min_weight) && !empty($max_weight)) 
            {
               $where_str .= " AND weight BETWEEN {$min_weight} AND {$max_weight} ";
            }
            //状态
            if (!empty($p_state)) 
            {
                $where_str .= " AND  state = {$p_state} ";
            }
            //罩杯
            if (!empty($cup_id)) 
            {
                $where_str .= " AND  cup_id = '{$cup_id}' ";
            }
            if (!empty($cup_a)) 
            {
                $where_str .= " AND cup_a = '{$cup_a}' ";
            }
            if (!empty($province)) 
            {
                if (!empty($location_id)) 
                {
                     $where_str .= " AND location_id = {$location_id}";
                }
                else
                {
                   $where_str .= " AND left(location_id,6) = {$province}"; 
                }
            }
            //进学校年份
            if (!empty($p_enter_school_time)) 
            {
                $where_str .= " AND p_enter_school_time = {$p_enter_school_time} ";
            }
            //录入者
            if (!empty($inputer_name)) 
            {
                $where_str .= " AND inputer_name = '{$inputer_name}' ";
            }
            //录入时间
            if (!empty($start_inputer_time) && !empty($end_inputer_time)) 
            {
                $start_temp_inputer = strtotime($start_inputer_time);
                $end_temp_inputer   = strtotime($end_inputer_time);
                $where_str .= " AND UNIX_TIMESTAMP(DATE_FORMAT(inputer_time, '%Y-%m-%d')) BETWEEN {$start_temp_inputer} AND {$end_temp_inputer}";
            }
            //学校
            if (!empty($p_school)) 
            {   
                $where_str .= " AND p_school like '%{$p_school}%'";
            }
            //专业
            if (!empty($p_specialty)) 
            {
                $where_str .= " AND p_specialty like '%{$p_specialty}%'";
            }
            //积分
            if (!empty($min_score) && !empty($max_score)) 
            {
                $where_str .= " AND total_score BETWEEN {$min_score} AND {$max_score} ";
            }
            //来源
            if (!empty($information_sources)) 
            {
                $where_str .= " AND information_sources like '%{$information_sources}%'";
            }
            //标签
            if (!empty($label)) 
            {
               /*if (!empty($where_str)) 
                {
                    # code...
                    $where_str .=" AND ";
                }
                $where_str .= " label = '{$label}' ";*/
                $l_where_str = "label = '{$label}'";
                $l_uid = $model_add_obj->get_label_list($l_where_str,'', 'uid DESC', 'DISTINCT(uid)');
                if (empty($l_uid)) 
                {
                    $list = array();
                    break;
                } 
            }
            //跟进时间
            if (!empty($start_follow_time) && !empty($end_follow_time)) 
            {
                //执行获取uid
                $start_temp_follow = strtotime($start_follow_time);
                $end_temp_follow   = strtotime($end_follow_time);
                $f_where_str = " UNIX_TIMESTAMP(DATE_FORMAT(follow_time, '%Y-%m-%d')) BETWEEN {$start_temp_follow} AND {$end_temp_follow} ";
                $uid = $model_add_obj->get_follow_uid($f_where_str,'', 'uid DESC', 'DISTINCT(uid)');
                if (empty($uid)) 
                {
                    $list = array();
                    break;
                }
                
            }
            $uiid = $model_add_obj->get_search_uid($where_str,'', 'uid DESC', 'DISTINCT(uid)' );
            if (!empty($uiid)) 
            {
               $uiid = array_change_by_val($uiid, 'uid');
               if (!empty($style_uid)) 
               {
                   $style_uid = array_change_by_val($style_uid, 'uid');
                   $uiid = array_intersect($uiid, $style_uid);
               }
               if (!empty($j_uid)) 
               {
                    $j_uid = array_change_by_val($j_uid, 'uid');
                    $uiid = array_intersect($uiid, $j_uid);
                   # code...
               }
               if (!empty($e_uid)) 
               {
                   $e_uid = array_change_by_val($e_uid, 'uid');
                   $uiid  = array_intersect($uiid, $e_uid);
               }
               if (!empty($l_uid)) 
               {
                    $l_uid = array_change_by_val($l_uid, 'uid');
                    $uiid = array_intersect($uiid, $l_uid);
               }
               if (!empty($uid)) 
               {
                    $uid = array_change_by_val($uid, 'uid');
                    $uiid = array_intersect($uiid, $uid);
               }
               if ($is_exist != -1) 
               {
                  if ($is_exist == 1) 
                  {
                     if (!empty($uiid) && is_array($uiid)) 
                      {
                       foreach ($uiid as $app_key=>$val) 
                       {
                           $row = $user_obj->get_user_info($val);
                           if (is_md5($row['pwd_hash'])) 
                           {
                              unset($uiid[$app_key]);    
                               //($is_exist == -1) ? unset($uiid[$app_key]): ''; 
                           }
                       }
                      }
                  }
                  elseif ($is_exist == 0) 
                  {
                      if (!empty($uiid) && is_array($uiid)) 
                      {
                       foreach ($uiid as $app_key=>$val) 
                       {
                           $row = $user_obj->get_user_info($val);
                           if (!is_md5($row['pwd_hash'])) 
                           {
                              unset($uiid[$app_key]);     
                           }
                       }
                      }
                  }
                   
               }
              
            }
            elseif(empty($uiid))
            {
                $list = array();
                break;
            }
            //得到结果
            if (empty($uiid) || !is_array($uiid)) 
            {
                $list = array();
                break;
            }
            else
            {
              $get_user_id = implode(',', $uiid);
              $where_str_list .= " AND uid in ($get_user_id) ";
            }
            //print_r($where_str_list);exit;
            //die('kkdk');
            $total_count = $model_add_obj->get_model_list(true, $where_str_list);
              //print_r($total_count);exit;
            $page_obj->setvar (
                array(
                    'sex'=> $sex, 'start_shoe_size'=> $start_shoe_size,
                    'end_shoe_size'=> $end_shoe_size, 'style' => $style,
                    'min_height' => $min_height, 'max_height' => $max_height,
                    'min_age'   => $min_age, 'max_age' => $max_age,
                    'min_price'  => $min_price, 'max_price'=> $max_price,
                    'min_weight' => $min_weight, 'max_weight' => $max_weight,
                    'p_state'    => $p_state, 'start_follow_time' => $start_follow_time,
                    'end_follow_time' => $end_follow_time, 'cup_id' => $cup_id,
                    'cup_a' => $cup_a, 'join_time' => $join_time,
                    'province' => $province,'location_id' => $location_id, 'p_enter_school_time' => $p_enter_school_time,
                    'inputer_name' => $inputer_name, 'p_school' => $p_school,
                    'p_specialty' => $p_specialty,'min_score' => $min_score,
                    'max_score' => $max_score,
                    'is_exist'  => $is_exist,
                    'information_sources' => $information_sources,
                    'act'  => $act,
                    'sort' => $sort
                ));
            $page_obj->set ( $show_count, $total_count );
            $list = $model_add_obj->get_model_list(false, $where_str_list, select_sort($sort), $page_obj->limit(), $fields = '*');
            //print_r($list);exit;
            break;
        case 'recy':
            $retUrl = $_SERVER['HTTP_REFERER'];
            $uid = isset($_INPUT['uid']) ? intval($_INPUT['uid']) : 0;
            $recycle = $_INPUT['recycle'] ? intval($_INPUT['recycle']) : 0;
            //var_dump($recycle);
            //print_r($_INPUT);exit;
            $info = $model_add_obj->update_model(array('recycle' => $recycle),$uid);
            echo "<script>window.alert('操作成功成功!');location.href='{$retUrl}';</script>";
            exit;
            break;
        default:
            //$total_count = $model_add_obj->get_model_list(true, $where_str_list);
              //print_r($total_count);exit;
            $total_count = 0;
            $page_obj->setvar ();
            $page_obj->set ( $show_count, $total_count );
            //$list = $model_add_obj->get_model_list(false, $where_str_list, select_sort($sort), $page_obj->limit(), $fields = '*');
            $list  = array();
            break;
    }

    //性别
    switch ($sex) 
    {
        case 0:
            $sex_s = "selected='true'";
            $tpl->assign('sex_s', $sex_s);
            break;
        case 1:
            $sex_1 = "selected='true'";
            $tpl->assign('sex_1', $sex_1);
            break;
        default:
    }
   //职业判断
    //echo $p_state;
    switch ($p_state) {
        case 1:
            $p_state_1 = "selected='true'";
            $tpl->assign('p_state_1', $p_state_1);
            break;
        case 2:
            $p_state_2 = "selected='true'";
            $tpl->assign('p_state_2', $p_state_2);
            break;
        case 3:
            $p_state_3 = "selected='true'";
            $tpl->assign('p_state_3', $p_state_3);
            break;
    }
    /*echo($cup_id);
    echo($cup_a);*/
    //罩杯
    switch ($cup_id) {
        case 1:
            $cup_id_1 = "selected='true'";
            $tpl->assign('cup_id_1', $cup_id_1);
            break;
        case 2:
            $cup_id_2 = "selected='true'";
            $tpl->assign('cup_id_2', $cup_id_2);
            break;
        case 3:
            $cup_id_3 = "selected='true'";
            $tpl->assign('cup_id_3', $cup_id_3);
            break;
        case 4:
            $cup_id_4 = "selected='true'";
            $tpl->assign('cup_id_4', $cup_id_4);
            break;
        case 5:
            $cup_id_5 = "selected='true'";
            $tpl->assign('cup_id_5', $cup_id_5);
            break;
        case 6:
            $cup_id_6 = "selected='true'";
            $tpl->assign('cup_id_6', $cup_id_6);
            break;
    }
    //罩杯单位
    switch ($cup_a) {
        case 1:
            $cup_a_1 = "selected='true'";
            $tpl->assign('cup_a_1', $cup_a_1);
            break;
        case 2:
            $cup_a_2 = "selected='true'";
            $tpl->assign('cup_a_2', $cup_a_2);
            break;
        case 3:
            $cup_a_3 = "selected='true'";
            $tpl->assign('cup_a_3', $cup_a_3);
            break;
        case 4:
            $cup_a_4 = "selected='true'";
            $tpl->assign('cup_a_4', $cup_a_4);
            break;
        case 5:
            $cup_a_5 = "selected='true'";
            $tpl->assign('cup_a_5', $cup_a_5);
            break;
        case 6:
            $cup_a_6 = "selected='true'";
            $tpl->assign('cup_a_6', $cup_a_6);
            break;
        
        default:
            # code...
            break;
    }
    //拍摄风格
    switch ($style) 
    {
        case 0:
            $style_0 = "selected='true'";
            $tpl->assign('style_0', $style_0);
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
        default:
            # code...
            break;
    }

    //活动报名被选择
    if (!empty($join_list) && is_array($join_list)) 
    {
    foreach ($join_list as $join_key => $vo) 
    {
        if ($vo['activity_join'] == $join) 
        {
            $join_list[$join_key]['join_select'] = "selected='true'";
        }
    }
    }
    //活动入围被选择
    if (!empty($enter_list) && is_array($enter_list)) 
    {
    foreach ($enter_list as $enter_key => $vo) 
    {
        if ($vo['activity_enter'] == $enter)
        {
            $enter_list[$enter_key]['enter_select'] = "selected='true'";
        }
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
    //录入者被选择
    if (!empty($inputer_list) && is_array($inputer_list)) 
    {
     foreach ($inputer_list as $input_key => $vo) 
     {
        if ($vo['inputer_name'] == $inputer_name) 
        {
            $inputer_list[$input_key]['inputer_select'] = "selected='true'";
        }
     }
    }
    //print_r($inputer_list);exit;
    //标签被选择
    if (!empty($label_list) && is_array($label_list)) 
    {
    foreach ($label_list as $label_key => $vo) 
    {
        if ($vo['label'] == $label) 
        {
            $label_list[$label_key]['label_select'] = "selected='true'";
        }
    }
    }
    //常驻城市名称获取
    if (!empty($list) && is_array($list)) 
    {
       foreach ($list as $key_city => $vo) 
       {
           $key_list = get_poco_location_name_by_location_id($vo['location_id']);
           $city_name = '';
           if (!empty($key_list)) 
           {
               $city_name = $key_list;
           }
           $list[$key_city]['city'] = $city_name;
       }
      
    }
    //print_r($list);
    //城市列表
    //die($province);
    //省
    $province_list = change_assoc_arr($arr_locate_a);
    foreach ($province_list as $key => $vo) 
    {
        if ($vo['c_id'] == $province) 
        {
            $province_list[$key]['selected_prov'] = "selected='true'";
        }
        # code...
    }
    //城市
    if ($province) 
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
    //价格小时
    if ($hour == 'fourh_price') 
    {
        $tpl->assign('fourh_true', "selected='true'");
    }
    elseif ($hour == 'addh_price') {
       $tpl->assign('add_true', "selected='true'");
    }
    //入库处理
    if ($is_exist == 0) 
    {
        $tpl->assign('is_exist_e', "selected='true'");
    }
    elseif ($is_exist == 1) 
    {
        $tpl->assign('is_exist_1', "selected='true'");
    }
    $tpl->assign('province_list', $province_list);
    $tpl->assign('city_list', $city_list);
    $tpl->assign('inputer_list', $inputer_list);
    $tpl->assign('label_list', $label_list);
    $tpl->assign('join_list', $join_list);
    $tpl->assign('enter_list', $enter_list);
    $tpl->assign('start_shoe_size', $start_shoe_size);
    $tpl->assign('end_shoe_size', $end_shoe_size);
    $tpl->assign('start_join_time', $start_join_time);
    $tpl->assign('end_join_time', $end_join_time);
    $tpl->assign('min_height', $min_height);
    $tpl->assign('max_height', $max_height);
    $tpl->assign('min_age', $min_age);
    $tpl->assign('max_age', $max_age);
    $tpl->assign('min_price', $min_price);
    $tpl->assign('max_price', $max_price);
    $tpl->assign('min_weight', $min_weight);
    $tpl->assign('max_weight', $max_weight);
    $tpl->assign('start_follow_time', $start_follow_time);
    $tpl->assign('end_follow_time', $end_follow_time);
    $tpl->assign('province', $province);
    $tpl->assign('city', $city);
    $tpl->assign('p_enter_school_time', $p_enter_school_time);
    $tpl->assign('inputer_name', $inputer_name);
    $tpl->assign('start_inputer_time', $start_inputer_time);
    $tpl->assign('end_inputer_time', $end_inputer_time);
    $tpl->assign('p_school', $p_school);
    //die($p_specialty);
    $tpl->assign('p_specialty', $p_specialty);
    $tpl->assign('information_sources', $information_sources);
    $tpl->assign('min_score', $min_score);
    $tpl->assign('max_score', $max_score);
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign("list", $list);
    $tpl->assign("total_count", $total_count);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();

 ?>