<?php 
/*
 * 添加提交
 *
*/

	include_once 'common.inc.php';
    include_once 'include/common_function.php';
 	$model_add_obj      = POCO::singleton('pai_model_add_class');
    //获取支付宝账号
    $user_account_obj = POCO::singleton('pai_bind_account_class');
    $model_style_v2_obj = POCO::singleton('pai_model_style_v2_class');
    
    $phone = trim($_INPUT['phone']);
    $regPartton="/^1[3-8]+\d{9}$/i";
    if (!preg_match($regPartton, $phone )) 
    {
    	echo "<script type='text/javascript'>window.alert('手机号码格式有误');location.href='model_add_first.php';</script>";
    	exit;
    }
    $user_obj = POCO::singleton('pai_user_class');
    $model_card_obj = POCO::singleton ( 'pai_model_card_class' );
    $user_id = $user_obj->get_user_id_by_phone($phone);
    if (empty($user_id))
    {
        $insert_data['cellphone'] = $phone;
        $insert_data['pwd']       = 123456;
        $insert_data['role']      = 'model';
        $insert_data['nickname']  = "手机用户".substr($phone,-4);
        $insert_data['reg_from']  = 'import';
        $user_obj->create_account($insert_data);
        $user_id = $user_obj->get_user_id_by_phone($phone);
        $user_obj->update_model_db_pwd($user_id);
        $insert_model ["user_id"] = $user_id;
        $model_card_obj->add_model_card ( $insert_model );
    }
    $uid = $model_add_obj->get_model_id_by_phone($phone);
    if (empty($uid)) 
    {
        $user_info   = $user_obj->get_user_info_by_user_id($user_id);
        $user_alipay = $user_account_obj->get_alipay_account_by_user_id($user_id);
        if(!empty($user_info) && is_array($user_info)) 
         {
          //基本数据
           $data_info['app_name']       = $user_info['nickname'];
           $data_info['phone']          = $user_info['cellphone'];
           $data_info['location_id']    = $user_info['location_id'];
           $data_info['img_url']        = $user_info['user_icon'];
           $data_info['inputer_time']   = date('Y-m-d H:i:s',time());
           $data_info['inputer_id']     = $yue_login_id;
           $model_add_obj->insert_model_info(true ,$user_id, $data_info);
           //身材数据
           $stature_data['chest']      = $user_info['chest'];
           $stature_data['waist']      = $user_info['waist'];
           $stature_data['chest_inch'] = $user_info['hip'];
           $stature_data['height']     = $user_info['height'];
           $stature_data['weight']     = $user_info['weight'];
           $stature_data['sex']        = intval($user_info['sex']);
           $stature_data['age']        = substr($user_info['birthday'],0, 7);
           $model_add_obj->insert_model_stature($user_id, $stature_data);
           //print_r($stature_data);
           //风格循环
           if (!empty($user_info['model_style_v2']) && is_array($user_info['model_style_v2'])) 
           {
              foreach ($user_info['model_style_v2'] as $key => $vo) 
              {
                 $style_data['style']      = change_style_int($vo['style']);
                 $style_data['addh_price'] = $vo['continue_price'];
                 $price = $vo['price'];
                 if ($vo['hour'] == 2) 
                 {
                    $style_data['twoh_price'] = $price;
                 }
                 else
                 {
                    $style_data['twoh_price'] = $price;
                 }
                 $model_add_obj->add_style($user_id, $style_data);
               }
            }
            //图片处理
            if (!empty($user_info['model_pic']) && is_array($user_info['model_pic'])) 
            {
              foreach ($user_info['model_pic'] as $pic_key => $vo) 
              {
                 $pic_data['img_url'] = $vo['img'];
                 $model_add_obj->insert_model_pic($pic_data, $user_id);
              }
            }
            //支付宝信息
            $other_data['alipay_info'] = $user_alipay;
            $model_add_obj->insert_model_other($user_id, $other_data);
         }
    }
    //exit;
    header("location:model_detail.php?uid={$user_id}");
    exit;

 ?>