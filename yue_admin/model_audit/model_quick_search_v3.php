<?php 
/*
 *模特快速查询
 *
*/
    include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
    ini_set('memory_limit', '256M');
    include_once 'common.inc.php';
    include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
    include_once 'include/locate_file.php';
	include_once 'include/common_function.php';
   /* if ($yue_login_id != 100293) 
    {
      echo "程序正在开发中。。。";
       exit;
    }*/
	$page_obj = new show_page ();
	$show_count = 20;
    $model_add_obj  = POCO::singleton('pai_model_add_class');
	$user_obj       = POCO::singleton('pai_user_class');
    $model_audit_obj = POCO::singleton('pai_model_audit_class');
    //机构表
    $organization_obj  = POCO::singleton('pai_organization_class');
    $org_relate_obj    = POCO::singleton('pai_model_relate_org_class');
    //模特卡
    $model_card_obj  = POCO::singleton('pai_model_card_class');
    
    
    $tpl = new SmartTemplate("model_quick_search.tpl.htm");
    
    $recycle = 0;
    $act = $_INPUT['act'] ? $_INPUT['act'] : 'list';
    
    $sex = isset($_INPUT['sex']) ? intval($_INPUT['sex']) : -1;
    
    $start_shoe_size =  intval($_INPUT['start_shoe_size']);
    $end_shoe_size   =  intval($_INPUT['end_shoe_size']);
    $start_join_time =  trim($_INPUT['start_join_time']);
    $end_join_time   =  trim($_INPUT['end_join_time']);
    $min_height      =  intval($_INPUT['min_height']);
    $max_height      =  intval($_INPUT['max_height']);
    $min_age         =  intval($_INPUT['min_age']);
    $max_age         =  intval($_INPUT['max_age']);
    
    $min_price       =  floatval($_INPUT['min_price']);
    $max_price       =  floatval($_INPUT['max_price']);
    
    $hour            =  trim($_INPUT['hour']);
    $min_weight      =  intval($_INPUT['min_weight']);
    $max_weight      =  intval($_INPUT['max_weight']);
    
    $p_state           = intval($_INPUT['p_state']);
    $start_follow_time = trim($_INPUT['start_follow_time']);
    $end_follow_time   = trim($_INPUT['end_follow_time']);
    $cup_id            = intval($_INPUT['cup_id']);
    $cup_a             = intval($_INPUT['cup_a']);
    
    $style             = isset($_INPUT['style']) ? intval($_INPUT['style']) : -1;
    
    $join                = trim($_INPUT['join']);
    $enter               = trim($_INPUT['enter']);
    $province            = intval($_INPUT['province']);
    $location_id         = intval($_INPUT['location_id']);
    
    $p_enter_school_time = trim($_INPUT['p_enter_school_time']);
    
    $inputer_id          = intval($_INPUT['inputer_id']);
    
    $start_inputer_time  = trim($_INPUT['start_inputer_time']);
    $end_inputer_time    = trim($_INPUT['end_inputer_time']);
    $p_school            = trim($_INPUT['p_school']);
    $p_specialty         = trim($_INPUT['p_specialty']);
    
    $min_score           = intval($_INPUT['min_score']);
    $max_score           = intval($_INPUT['max_score']);
    
    $label               = trim($_INPUT['label']);
    
    $information_sources = trim($_INPUT['information_sources']);
    $sort                = trim($_INPUT['sort']);
    $is_exist            = isset($_INPUT['is_exist']) ? intval($_INPUT['is_exist']) : -1;
    //是否审核通过
    $is_status       = isset($_INPUT['is_status']) ? intval($_INPUT['is_status']) : -1;
    //机构id
    $org_user_id     = isset($_INPUT['org_user_id']) ? intval($_INPUT['org_user_id']) : -1;
    $other           = $_INPUT['other'] ? $_INPUT['other'] : '';//所有匹配
    $where_str_list = "recycle = {$recycle}";
    $inputer_list   = $model_add_obj->get_inputer_list_v2(false,'inputer_id !=0 ','0,99999999','uid DESC','DISTINCT(inputer_id)');
    $label_list     = $model_add_obj->get_label_list('label <> ""','0,99999999', 'uid DESC', 'DISTINCT(label)');
    //获取活动报名
    $join_list    = $model_add_obj->get_join_list('activity_join <> ""','0,99999999', 'uid DESC', 'DISTINCT(activity_join)');
    //获取活动入围
    $enter_list    = $model_add_obj->get_enter_list('activity_enter <> ""','0,99999999', 'uid DESC', 'DISTINCT(activity_enter)');
    //机构数据
    $org_list = $organization_obj->get_org_list('','','','0,99999999','user_id,nick_name');
    //放入回车站
    if ($act == 'recy') 
    {
        $retUrl = $_SERVER['HTTP_REFERER'];
        $uid = isset($_INPUT['uid']) ? intval($_INPUT['uid']) : 0;
        $recycle = $_INPUT['recycle'] ? intval($_INPUT['recycle']) : 0;
        $reason = $_INPUT['reason'] ? $_INPUT['reason'] : '';
        $info = $model_add_obj->update_model(array('recycle' => $recycle, 'reason' => $reason),$uid);
        echo "<script>window.alert('操作成功成功!');location.href='{$retUrl}';</script>";
        exit;
    }

    //主查询条件
    $where_str_list = "1 AND recycle = {$recycle}";
    //不存在的uid
    $tmp_user_id = 110;
    //查询语句开始
    $uid = array();
    //临时查询条件
    $where_str = "1";
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
    }
    //价格风格
    $style_where_str = "1";
    if ($style != -1) 
    {
        $style_where_str .= " AND style={$style}";
    }
    if(!empty($min_price) && !empty($max_price) && !empty($hour)) 
    {
        $style_where_str .= " AND {$hour} BETWEEN {$min_price} AND {$max_price} ";
    }
    //var_dump($style_where_str);exit;
    if ($style_where_str != '1') 
    {
        $style_uid = $model_add_obj->search_style($style_where_str,'', 'uid DESC', 'DISTINCT(uid)');
        if (empty($style_uid)) 
        {
            $where_str_list .= " AND uid = {$tmp_user_id}";
        }
    }
    //活动报名
    if (!empty($join)) 
    {
        $j_where_str = "activity_join = '{$join}'";
        $j_uid = $model_add_obj->get_join_list($j_where_str,'', 'uid DESC', 'DISTINCT(uid)');
        if (empty($j_uid)) 
        {
            $where_str_list .= " AND uid = {$tmp_user_id}";
        } 
    }
    //活动入围
    if (!empty($enter)) 
    {
        $e_where_str = "activity_enter = '{$enter}'";
        $e_uid = $model_add_obj->get_enter_list($e_where_str,'', 'uid DESC', 'DISTINCT(uid)');
        if (empty($e_uid)) 
        {
            $where_str_list .= " AND uid = {$tmp_user_id}";
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
    //省
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
    if (!empty($inputer_id)) 
    {
        $where_str .= " AND inputer_id = {$inputer_id} ";
    }
    //录入时间
    if (!empty($start_inputer_time) && !empty($end_inputer_time)) 
    {
        $start_temp_inputer = strtotime($start_inputer_time);
        $end_temp_inputer   = strtotime($end_inputer_time)+24*3600;
        $where_str .= " AND UNIX_TIMESTAMP(inputer_time) BETWEEN {$start_temp_inputer} AND {$end_temp_inputer}";
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
        $l_where_str = "label = '{$label}'";
        $l_uid = $model_add_obj->get_label_list($l_where_str,'0,99999999', 'uid DESC', 'DISTINCT(uid)');
        if (empty($l_uid)) 
        {
            $where_str_list .= " AND uid = {$tmp_user_id}";
        } 
    }
    //跟进时间
    if (!empty($start_follow_time) && !empty($end_follow_time)) 
    {
            //执行获取uid
        $start_temp_follow = strtotime($start_follow_time);
        $end_temp_follow   = strtotime($end_follow_time)+24*3600;
        $f_where_str = " UNIX_TIMESTAMP(follow_time) BETWEEN {$start_temp_follow} AND {$end_temp_follow} ";
        $uid = $model_add_obj->get_follow_uid($f_where_str,'0,99999999', 'uid DESC', 'DISTINCT(uid)');
        if (empty($uid)) 
        {
            $where_str_list .= " AND uid = {$tmp_user_id}";
        }        
    }
    //机构id
    if ($org_user_id != -1) 
    {
        $org_where_str = "org_id = {$org_user_id}";
        $org_uid       = $org_relate_obj->get_model_org_list_by_org_id($b_select_count = false,$org_where_str, $limit = '0,99999999', $order_by = 'id DESC',  $fields = 'user_id');
        //print_r($org_uid);
        if (empty($org_uid)) 
        {
            $where_str_list .= " AND uid = {$tmp_user_id}";
        }      
        # code...
    }
    $uiid = $model_add_obj->get_search_uid($where_str,'0,99999999', 'uid DESC', 'DISTINCT(uid)' );
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
        //机构id处理
        if (!empty($org_uid)) 
        {
            $org_uid = array_change_by_val($org_uid, 'user_id');
            //print_r($uiid);
            $uiid = array_intersect($uiid, $org_uid);
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
        //是否审核通过
        if ($is_status != -1) 
        {
            if (!empty($uiid) && is_array($uiid)) 
            {
                foreach ($uiid as $status_key => $vo) 
                {
                    $info = $model_audit_obj->get_id_by_user_id_status($vo, $is_status);
                    if (!$info) 
                    {
                        unset($uiid[$status_key]);
                    }
                }
            }
        }      
    }    
    elseif(empty($uiid))
    {
        $where_str_list .= " AND uid = {$tmp_user_id}";
    }
    //得到结果
    if (empty($uiid) || !is_array($uiid)) 
    {
        $where_str_list .= " AND uid = {$tmp_user_id}";
    }
    else
    {
        $get_user_id = implode(',', $uiid);
        $where_str_list .= " AND uid in ($get_user_id) ";
    }

    if ($act == 'list') 
    {
        $total_count = 0;
        $page_obj->setvar ();
        $page_obj->set ( $show_count, $total_count );
        $list  = array();
    }
    elseif ($act == 'search') 
    {
        $total_count = $model_add_obj->get_model_list(true, $where_str_list);
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
                    'inputer_id' => $inputer_id, 'p_school' => $p_school,
                    'start_inputer_time' => $start_inputer_time, 'end_inputer_time' => $end_inputer_time,
                    'p_specialty' => $p_specialty,'min_score' => $min_score,
                    'max_score' => $max_score,'label' => $label,
                    'is_exist'  => $is_exist,'is_status' => $is_status, 
                    'information_sources' => $information_sources,
                    'org_user_id' => $org_user_id,
                    'act'  => $act,
                    'sort' => $sort
                ));
        $page_obj->set ( $show_count, $total_count );
        $list = $model_add_obj->get_model_list(false, $where_str_list, change_sort($sort), $page_obj->limit(), $fields = '*');
    }
    elseif ($act == 'export') 
    {
       $list = $model_add_obj->get_model_list(false, $where_str_list, change_sort($sort), '0,10000', $fields = '*');
       $data = array();
       foreach ($list as $key => $vo) 
       {
             $prof_data  = $model_add_obj->get_model_profession($vo['uid']);
             //风格及风格价格
             $style_data = $model_add_obj->list_style($vo['uid']);
             //其他信息
             $other_data = $model_add_obj->get_model_other($vo['uid']);
             //身材信息
             $stature_data       = $model_add_obj->get_model_stature($vo['uid']);
             //价格风格
             $style_data = $model_add_obj->list_style($vo['uid']);
             //模特卡
             $model_card_data = $model_card_obj->get_model_card_by_user_id($vo['uid']);
             //print_r($model_card_data);exit;
             $data[$key]['id']          = $key+1;
             $data[$key]['user_id']     = $vo['uid'];
             $data[$key]['name']        = $vo['name'];
             $data[$key]['nickname']    = $vo['nick_name'];
             $data[$key]['app_name']    = $vo['app_name'];
             $data[$key]['phone']       = $vo['phone'];
             $data[$key]['weixin_id']   = $vo['weixin_id'];
             $data[$key]['weixin_name'] = $vo['weixin_name'];
             /*$data[$key]['discuz_name'] = $vo['discuz_name'];
             $data[$key]['poco_name']   = $vo['poco_name'];*/
             $data[$key]['qq']          = $vo['qq'];
             $data[$key]['email']       = $vo['email'];
             $price_str = '';
             $style_str = '';
             if (is_array($style_data)) 
             {
               foreach ($style_data as $style_key => $style_val) 
               {
                 if($style_key != 0 && $style_val['style'])
                 {
                   $price_str .= ',';
                   $style_str .= ',';
                 }
                 //风格
                 $style_str .= change_style_val($style_val['style']);
                 $price_str .= isset($style_val['twoh_price']) ? $style_val['twoh_price'].'/2小时,' : '';
                 $price_str .= isset($style_val['fourh_price']) ? $style_val['fourh_price'].'/4小时,' : '';
                 $price_str .= isset($style_val['addh_price']) ? $style_val['addh_price'].'/加时' : '';
               }
               # code...
             }
             $data[$key]['style']       = $style_str;
             $data[$key]['price']       = $price_str;
             //$data[$key]['poco_id']     = $vo['poco_id'];
             /*$data[$key]['state']       = change_text_by_state($prof_data['p_state']);
             $data[$key]['p_school']    = $prof_data['p_school'];*/
             $data[$key]['sex']          = $stature_data['sex'] == 0 ? '女' : '男';
             $age                        = $stature_data['age'] ? date('Y', time()) - substr($stature_data['age'], 0, 4) : '';
             if ($age < 0 || $age > 100) 
             {
                $data[$key]['age'] = '';
             }
             else
             {
                $data[$key]['age'] = $age;
             }
             $data[$key]['height'] = $stature_data['height'];
             $data[$key]['weight'] = $stature_data['weight'];
             $data[$key]['cup']    = change_into_text_by_cup_id($stature_data['cup_id']).get_cup_text_by_cup_a_id($stature_data['cup_a']);
             $data[$key]['chest']  = $stature_data['chest'].'/'.$stature_data['waist'].'/'.$stature_data['chest_inch'];
             $data[$key]['intro']  = $model_card_data['intro'];
             $data[$key]['information_sources'] = $other_data['information_sources'];
             $data[$key]['city']         = get_poco_location_name_by_location_id ($vo['location_id']);
             $data[$key]['inputer_name'] = get_user_nickname_by_user_id($data['inputer_id']);
             $data[$key]['inputer_time'] = $vo['inputer_time'];
             $data[$key]['alipay_info']  = $other_data['alipay_info'];
             
        }
        $fileName = "yueyue_excel";
           //$headArr = array("序号","姓名","昵称","微信名称","论坛名称","POCO用户名","APP昵称","手机号码","微信","QQ","邮箱","POCOID","职业状态","学校名称","来源", "常驻城市", "录入者", "录入时间", "支付宝账号", "性别", "年龄","身高", "体重", "罩杯", "三围" );
          $headArr = array("序号","模特ID","姓名","昵称","APP昵称","手机号码","微信","微信名称","QQ","邮箱","拍摄风格","价格","性别", "年龄","身高", "体重", "罩杯", "三围","备注档期","来源", "常驻城市", "录入者", "录入时间", "支付宝账号");
           $title = "模特库数据";
           getExcel($fileName,$title,$headArr,$data);
           exit;
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
        if ($vo['inputer_id'] == $inputer_id) 
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
    //审核
    //入库处理
    if ($is_status == 0) 
    {
        $tpl->assign('is_status_e', "selected='true'");
    }
    elseif ($is_status == 1) 
    {
        $tpl->assign('is_status_1', "selected='true'");
    }
    elseif ($is_status == 2) 
    {
        $tpl->assign('is_status_2', "selected='true'");
    }

    //机构数据
    $tpl->assign('org_list', $org_list);
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
    $tpl->assign('inputer_id', $inputer_id);
    $tpl->assign('start_inputer_time', $start_inputer_time);
    $tpl->assign('end_inputer_time', $end_inputer_time);
    $tpl->assign('p_school', $p_school);
    //die($p_specialty);
    $tpl->assign('p_specialty', $p_specialty);
    $tpl->assign('information_sources', $information_sources);
    $tpl->assign('min_score', $min_score);
    $tpl->assign('max_score', $max_score);
    $tpl->assign('org_user_id', $org_user_id);
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign("list", $list);
    $tpl->assign("total_count", $total_count);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();

 ?>