<?php 

/* 
 * 模特审核内容页
 *
*/

	include_once 'common.inc.php';
    include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
    include_once 'include/locate_file.php';
    include_once 'include/common_function.php';
    //查看权限
    check_authority_by_list('exit_type',$authority_list,'model', 'is_select');
    $user_obj  = POCO::singleton('pai_user_class');
    //账号绑定
    $payment_obj = POCO::singleton ( 'pai_payment_class' );
    $model_add_obj  = POCO::singleton('pai_model_add_class');
	$model_card_obj  = POCO::singleton('pai_model_card_class');
    //获取支付宝账号
    $user_account_obj = POCO::singleton('pai_bind_account_class');
    $tpl = new SmartTemplate("model_detail.tpl.htm");
    $uid = $_INPUT['uid'] ? $_INPUT['uid'] : "";
    $uid = (int)$uid;
    if (empty($uid)) 
    {
    	echo "<script type='text/javascript'>window.alert('您传过来的数据有误!');location.href='model_quick_search.php';</script>";	
   		exit;
    }
    //获取昵称
    $nickname       = $user_obj->get_user_nickname_by_user_id($uid);
    $inputer_name   = $user_obj->get_user_nickname_by_user_id($yue_login_id);
    $inputer_time = date('Y-m-d H:i:s');
    //die($nickname);
    $model_data   = $model_add_obj->get_model_info($uid);
    if (!empty($model_data) && is_array($model_data)) 
    {
        if ($model_data['img_url']) 
        {
            $img_url = $model_data['img_url'];
        }
        
    }
    if (isset($model_data['inputer_name']) && !empty($model_data['inputer_name'])) 
    {
         $inputer_name = $model_data['inputer_name'];
         //$inputer_time =
         $inputer_time = !empty($model_data['inputer_time']) ? $model_data['inputer_time'] : $inputer_time ;
    }
    $img_url = $img_url ? $img_url : 'resources/images/admin_upload_thumb.png';
    $other_data   = $model_add_obj->get_model_other($uid);
    $follow_data  = $model_add_obj->get_model_follow($uid);
    //更新数据
    if (!empty($follow_data) && is_array($follow_data)) 
    {
        foreach ($follow_data as $key => $vo) 
        {
            switch ($vo['result']) {
                case '0':
                    # code...
                    $follow_data[$key]['result'] = '已解决';
                    break;
                case '1':
                    $follow_data[$key]['result'] = '未解决';
                    break;
                case '2':
                    $follow_data[$key]['result'] = '跟进中';
                    break;
                default:
                    $follow_data[$key]['result'] = '已解决';
                    break;
            }
        }
        # code...
    }
    $prof_data    = $model_add_obj->get_model_profession($uid);
    if (!empty($prof_data) && is_array($prof_data)) 
    {
        switch ($prof_data['p_state']) 
        {
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
        # code...
    }
    $score_data   = $model_add_obj->get_model_score($uid);
    if (!empty($score_data)) 
    {
       $total_score = $score_data['join_score']+$score_data['appearance_score']+$score_data['honor_score']+$score_data['makeup_score']+$score_data['expressiveness_score']+$score_data['height_score']+$score_data['weight_score']+$score_data['cup_score']+$score_data['bwh_score'];
    }
    else
    {
        $total_score = 0;
    }
    $tpl->assign('total_score', $total_score);
    //身材信息
    $stature_data = $model_add_obj->get_model_stature($uid);
    if (!empty($stature_data) && is_array($stature_data)) 
    {
        //性别处理
        switch ($stature_data['sex']) {
            case '1':
                $p_sex_1 = "selected='true'";
                $tpl->assign('p_sex_1', $p_sex_1);
                break;
        }
        //罩杯处理
        switch ($stature_data['cup_id']) 
        {
            case 1:
                $p_cupid_1 = "selected='true'";
                $tpl->assign('p_cupid_1', $p_cupid_1);
                break;
            case 2:
                $p_cupid_2 = "selected='true'";
                $tpl->assign('p_cupid_2', $p_cupid_2);
                break;
            case 3:
                $p_cupid_3 = "selected='true'";
                $tpl->assign('p_cupid_3', $p_cupid_3);
                break;
            case 4:
                $p_cupid_4 = "selected='true'";
                $tpl->assign('p_cupid_4', $p_cupid_4);
                break;
            case 5:
                $p_cupid_5 = "selected='true'";
                $tpl->assign('p_cupid_5', $p_cupid_5);
                break;
            case 6:
                $p_cupid_6 = "selected='true'";
                $tpl->assign('p_cupid_6', $p_cupid_6);
                break;
        }
        //罩杯2
        switch ($stature_data['cup_a']) 
        {
            case 1:
                $p_cupa_1 = "selected='true'";
                $tpl->assign('p_cupa_1', $p_cupa_1);
                break;
            case 2:
                $p_cupa_2 = "selected='true'";
                $tpl->assign('p_cupa_2', $p_cupa_2);
                break;
            case 3:
                $p_cupa_3 = "selected='true'";
                $tpl->assign('p_cupa_3', $p_cupa_3);
                break;
            case 4:
                $p_cupa_4 = "selected='true'";
                $tpl->assign('p_cupa_4', $p_cupa_4);
                break;
            case 5:
                $p_cupa_5 = "selected='true'";
                $tpl->assign('p_cupa_5', $p_cupa_5);
                break;
            case 6:
                $p_cupa_6 = "selected='true'";
                $tpl->assign('p_cupa_6', $p_cupa_6);
                break;
        }
        # code...
    }
    //获取电话号码
    $cellphone = $user_obj->get_phone_by_user_id($uid);
   //print_r($cellphone);exit;
    //获取模特卡数据
    $model_card = $model_card_obj->get_model_card_info($uid);
    //print_r($model_card);exit;
    //print_r($model_card);exit;
    //print_r($model_card);exit;
    if (!empty($model_card) && is_array($model_card)) 
    {
        $model_card_data['m_chest'] = (int)$model_card['chest'];
        $model_card_data['m_chest_inch'] = (int)$model_card['chest_inch'];
        $model_card_data['m_hip']     = (int)$model_card['hip'];
        $model_card_data['m_waist'] = (int)$model_card['waist'];
        //$model_card_data['chest_inch'] = $model_card['chest_inch'];
        $model_card_data['m_cup'] = $model_card['cup'];
        $model_card_data['m_height'] = $model_card['height'];
        $model_card_data['m_weight'] = $model_card['weight'];
    }
    //print_r($model_card);exit;
    //账户绑定内容提取
    $account_info = $payment_obj->get_user_account_info ( $uid );
    //获取模特风格
    $model_list1 = $model_add_obj->list_style($uid);
    //$model_list2 = $model_style_obj->get_model_style_by_user_id($uid);
    $str = '';
    if (!empty($model_list1) && is_array($model_list1)) 
    {
        foreach ($model_list1 as $key => $vo) 
        {
        $style_name = '';
        switch ($vo['style']) 
        {
            case 0:
                $style_name = '欧美';
                break;
            case 1:
                $style_name = '情绪';
                break;
            case 2:
                $style_name = '清新';
                break;
            case 3:
                $style_name = '复古';
                break;
            case 4:
                $style_name = '韩系';
                break;
            case 5:
                $style_name = '日系';
                break;
            case 6:
                $style_name = '性感';
                break;
            case 7:
                $style_name = '街拍';
                break;
            case 8:
                $style_name = '胶片';
                break;
            case 9:
                $style_name = '商业';
                break;
            
            default:
                $style_name = '';
                break;
        }
            if ($key != 0) 
            {
                $str .= ',';
            }
            $str .= $style_name;
        }
        //die($str);
        # code...
    }
    /*elseif (empty($model_list1) && !empty($model_list2)) 
    {
        foreach ($model_list2 as $key => $vo) 
        {
            if ($key != 0) 
            {
                $str .= ',';
            }
            $str .= $vo['style'];
        }
        # code...
    }*/
    //获取
    //省和接口
    $location_id_info = get_poco_location_name_by_location_id ($model_data['location_id'], true, true);
    //print_r(substr($location_id_info['level_1']['id'], 0 , 6));exit;
    //省
    $province_list = change_assoc_arr($arr_locate_a);
    if (isset($location_id_info['level_1']) && is_array($location_id_info['level_1'])) 
    {
        $prov_id = substr($location_id_info['level_1']['id'], 0 , 6);
        foreach ($province_list as $key => $vo) 
        {
         if (isset($location_id_info['level_1']) && $vo['c_id'] == $prov_id) 
         {
            $province_list[$key]['selected_prov'] = "selected='true'";
          }
        }
        $city_list = ${'arr_locate_b'.$prov_id};
        if (!empty($city_list) && is_array($city_list) ) 
        {
          $city_list = change_assoc_arr($city_list);
          foreach ($city_list as $c_key => $vo) 
          {
           if ($vo['c_id'] == $location_id_info['level_1']['id']) 
           {
              $city_list[$c_key]['selected_city'] = "selected='true'";
           }
         }
        }
    }
    //获取app基本信息
    $app_user = $user_obj->get_user_info($uid);
    //print_r($app_user);exit;
    $app_user['city'] = get_poco_location_name_by_location_id($app_user['location_id']);
    //获取支付宝账号
    $user_alipay = $user_account_obj->get_alipay_account_by_user_id($uid);
    $tpl->assign('user_alipay', $user_alipay);
    //城市
    //die($model_data['province']);
    //print_r($list);exit;
    //样片链接
    $p_url = $model_add_obj->find_purl_info($uid);
    $enter = $model_add_obj->find_enter_info($uid);
    $join  = $model_add_obj->find_join_info($uid);
    $label = $model_add_obj->find_label_info($uid);
    $tpl->assign($p_url);
    $tpl->assign($enter);
    $tpl->assign($join);
    $tpl->assign($label);
    $tpl->assign('str', $str);
    $tpl->assign('model_list', $model_list);
    $tpl->assign($account_info);
    //print_r($account_info);exit;
    //die($inputer_name);
    $tpl->assign('province_list', $province_list);
    $tpl->assign('city_list', $city_list);
    $tpl->assign('inputer_name_true', $inputer_name);
    $tpl->assign('nickname', $nickname);
    $tpl->assign('cellphone', $cellphone);
    $tpl->assign($model_card_data);
    $tpl->assign($model_data);
    $tpl->assign($other_data);
    $tpl->assign('list', $follow_data);
    $tpl->assign($prof_data);
    $tpl->assign($score_data);
    $tpl->assign($stature_data);
    $tpl->assign('img_url', $img_url);
    //$tpl->assign('p_state_1', 'selected');
    $tpl->assign('user_id', $uid);
    $tpl->assign($app_user);
    $tpl->assign('inputer_time', $inputer_time);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();



 ?>