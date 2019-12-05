<?php 


	include_once 'common.inc.php';
  check_authority(array('cameraman'));
    $user_obj  = POCO::singleton('pai_user_class');
    //账号绑定
    $payment_obj = POCO::singleton ( 'pai_payment_class' );
    $cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
    $tpl = new SmartTemplate("cameraman_detail.tpl.htm");
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
    $cameraman_data   = $cameraman_add_obj->get_cameraman_info($uid);
    if (!empty($cameraman_data) && is_array($cameraman_data)) 
    {
        if ($cameraman_data['img_url']) 
        {
            $img_url = $cameraman_data['img_url'];
        }
        switch ($cameraman_data['city']) 
        {
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
        
        default:
            # code...
            break;
       }
        
    }
    if (isset($cameraman_data['inputer_name']) && !empty($cameraman_data['inputer_name'])) 
    {
         $inputer_name = $cameraman_data['inputer_name'];
    }
    $img_url = $img_url ? $img_url : 'resources/images/admin_upload_thumb.png';
    //个人信息
    $personal_data = $cameraman_add_obj->get_personal_info($uid);
    $other_data    = $cameraman_add_obj->get_cameraman_other($uid);
    $follow_total  = $cameraman_add_obj->get_cameraman_follow(true, $uid);
    $follow_data   = $cameraman_add_obj->get_cameraman_follow(false, $uid);
    //跟进数据
    if (!empty($follow_data) && is_array($follow_data)) 
    {
        foreach ($follow_data as $key => $vo) 
        {
            switch ($vo['result']) {
                case '0':
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

    if (!empty($personal_data) && is_array($personal_data)) 
    {
        //性别
        switch ($personal_data['sex']) {
            case 1:
                $sex_1 = "selected='true'";
                $tpl->assign('sex_1', $sex_1);
                break;
            case 2:
                $sex_2 = "selected='true'";
                $tpl->assign('sex_2', $sex_2);
                break;
        }
        //职业状态
        switch ($personal_data['p_state']) {
            case 1:
                $p_state_1 = "selected='true'";
                $tpl->assign('p_state_1', $p_state_1);
                break;
            case 2:
                $p_state_2 = "selected='true'";
                $tpl->assign('p_state_2', $p_state_2);
                break;
        }
        //工作室
       if ($personal_data['is_studio'] == 1) 
       {
            $is_studio_1 = "selected='true'";
            $tpl->assign('is_studio_1', $is_studio_1);
            $is_studio_on = "style='display:none'";
            $tpl->assign('is_studio_on', $is_studio_on);
       }
       //远景
       if ($personal_data['is_fview'] == 1) 
       {
           $is_fview_1 = "selected='true'";
           $tpl->assign('is_fview_1', $is_fview_1);
       }
    }

    //风格
    $style_list = $cameraman_add_obj->cameraman_list_style($uid);
    $style_str = '';
    if (!empty($style_list) && is_array($style_list)) 
    {
     foreach ($style_list as $key => $vo) 
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
        }
        if ($key != 0) 
        {
            $style_str .= ',';
        }
        $style_str .= $style_name;
      }
    }
    $tpl->assign('style_str', $style_str);
    //获取电话号码
    $cellphone = $user_obj->get_phone_by_user_id($uid);
    //账户绑定内容提取
    $account_info = $payment_obj->get_user_account_info ( $uid );
    $tpl->assign($account_info);
    $tpl->assign($personal_data);
    $tpl->assign('inputer_name_true', $inputer_name);
    $tpl->assign('nickname', $nickname);
    $tpl->assign('cellphone', $cellphone);
    $tpl->assign($cameraman_data);
    $tpl->assign($other_data);
    $tpl->assign('follow_total', $follow_total);
    $tpl->assign('list', $follow_data);
    $tpl->assign('img_url', $img_url);
    $tpl->assign('user_id', $uid);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();



 ?>