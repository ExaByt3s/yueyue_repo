<?php 


	include_once 'common.inc.php';
  check_authority(array('cameraman'));
    $user_obj  = POCO::singleton('pai_user_class');
    //�˺Ű�
    $payment_obj = POCO::singleton ( 'pai_payment_class' );
    $cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
    $tpl = new SmartTemplate("cameraman_detail.tpl.htm");
    $uid = $_INPUT['uid'] ? $_INPUT['uid'] : "";
    $uid = (int)$uid;
    if (empty($uid)) 
    {
    	echo "<script type='text/javascript'>window.alert('������������������!');location.href='model_quick_search.php';</script>";	
   		exit;
    }
    //��ȡ�ǳ�
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
    //������Ϣ
    $personal_data = $cameraman_add_obj->get_personal_info($uid);
    $other_data    = $cameraman_add_obj->get_cameraman_other($uid);
    $follow_total  = $cameraman_add_obj->get_cameraman_follow(true, $uid);
    $follow_data   = $cameraman_add_obj->get_cameraman_follow(false, $uid);
    //��������
    if (!empty($follow_data) && is_array($follow_data)) 
    {
        foreach ($follow_data as $key => $vo) 
        {
            switch ($vo['result']) {
                case '0':
                    $follow_data[$key]['result'] = '�ѽ��';
                    break;
                case '1':
                    $follow_data[$key]['result'] = 'δ���';
                    break;
                case '2':
                    $follow_data[$key]['result'] = '������';
                    break;
                default:
                    $follow_data[$key]['result'] = '�ѽ��';
                    break;
            }
        }
        # code...
    }

    if (!empty($personal_data) && is_array($personal_data)) 
    {
        //�Ա�
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
        //ְҵ״̬
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
        //������
       if ($personal_data['is_studio'] == 1) 
       {
            $is_studio_1 = "selected='true'";
            $tpl->assign('is_studio_1', $is_studio_1);
            $is_studio_on = "style='display:none'";
            $tpl->assign('is_studio_on', $is_studio_on);
       }
       //Զ��
       if ($personal_data['is_fview'] == 1) 
       {
           $is_fview_1 = "selected='true'";
           $tpl->assign('is_fview_1', $is_fview_1);
       }
    }

    //���
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
             $style_name = 'ŷ��';
            break;
           case 1:
             $style_name = '����';
            break;
           case 2:
              $style_name = '����';
             break;
           case 3:
              $style_name = '����';
            break;
           case 4:
              $style_name = '��ϵ';
            break;
            case 5:
              $style_name = '��ϵ';
           break;
           case 6:
               $style_name = '�Ը�';
           break;
           case 7:
              $style_name = '����';
           break;
           case 8:
              $style_name = '��Ƭ';
            break;
          case 9:
             $style_name = '��ҵ';
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
    //��ȡ�绰����
    $cellphone = $user_obj->get_phone_by_user_id($uid);
    //�˻���������ȡ
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