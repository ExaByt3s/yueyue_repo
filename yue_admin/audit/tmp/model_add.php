<?php 


	include_once 'common.inc.php';
    //修改权限
    check_authority_by_list('exit_type',$authority_list,'model', 'is_update');
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
    //print_r($_POST);exit;
    $uid                         = $_INPUT['uid'] ? $_INPUT['uid'] : 0; 
    $data_info['name']           = $_INPUT['name'] ? $_INPUT['name'] : '';
    $data_info['nick_name']      = $_INPUT['nick_name'] ? $_INPUT['nick_name'] : '';
    $data_info['weixin_name']    = $_INPUT['weixin_name'] ? $_INPUT['weixin_name'] : '';
    $data_info['discuz_name']    = $_INPUT['discuz_name'] ? $_INPUT['discuz_name'] : '';
    $data_info['poco_name']      = $_INPUT['poco_name'] ? $_INPUT['poco_name'] : '';
    $data_info['app_name']       = $_INPUT['app_name'] ? $_INPUT['app_name'] : '';
    $data_info['phone']          = $_INPUT['phone'] ? $_INPUT['phone'] : '';
    $data_info['weixin_id']      = $_INPUT['weixin_id'] ? $_INPUT['weixin_id'] : '';
    //$data_info['weixin_id']      = (int)$weixin_id;
    $qq                          = $_INPUT['qq'] ? $_INPUT['qq'] : 0;
    $data_info['qq']             = (int)$qq;
    $data_info['email']          = $_INPUT['email'] ? $_INPUT['email'] : '';
    $poco_id                     = $_INPUT['poco_id'] ? $_INPUT['poco_id'] : 0;
    $data_info['poco_id']        = $poco_id;
    //$data_info['province']       = $_INPUT['province'] ? intval($_INPUT['province']) : 0;
    $data_info['location_id']    = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
    $data_info['inputer_name']   = $_INPUT['inputer_name'] ? $_INPUT['inputer_name'] : '';
    $data_info['inputer_time']   = $_INPUT['inputer_time'] ? $_INPUT['inputer_time'] : date('Y-m-d H:i:s', time());
    $data_info['phone']          = $_INPUT['phone'] ? $_INPUT['phone'] : '';
    //插进入基本信息
    $model_add_obj->insert_model_info(true ,$uid,$data_info);
    $p_state                     = $_INPUT['p_state'] ? $_INPUT['p_state'] : 0;
    $data_prof['p_state']        =(int)$p_state;
    //$data_prof['p_url']          = $_INPUT['p_url'] ? $_INPUT['p_url'] : '';
    $data_prof['p_join_time']    = $_INPUT['p_join_time'] ? $_INPUT['p_join_time'] : '';
    $data_prof['p_honor']        = $_INPUT['p_honor'] ? $_INPUT['p_honor'] : '';
    $data_prof['p_school']       = $_INPUT['p_school'] ? $_INPUT['p_school'] : '';
    $data_prof['p_specialty']    = $_INPUT['p_specialty'] ? $_INPUT['p_specialty'] : '';
    $data_prof['p_enter_school_time']= $_INPUT['p_enter_school_time'] ? $_INPUT['p_enter_school_time'] : '';
    //print_r($data_prof);exit;
    //事业录入
    $model_add_obj->insert_model_profession($uid, $data_prof);
    $honor_score                 = $_INPUT['honor_score'] ? $_INPUT['honor_score'] : 0;
    $score_data['honor_score']   = (int)$honor_score;
    $makeup_score                = $_INPUT['makeup_score'] ? $_INPUT['makeup_score'] : 0;
    $score_data['makeup_score']  = (int)$makeup_score;
    $expressiveness_score        = $_INPUT['expressiveness_score'] ? $_INPUT['expressiveness_score'] : 0;
    $score_data['expressiveness_score'] = (int)$expressiveness_score;
    $join_score                  = $_INPUT['join_score'] ? $_INPUT['join_score'] : 0;
    $score_data['join_score']    = (int)$join_score;
    $appearance_score            = $_INPUT['appearance_score'] ? $_INPUT['appearance_score'] : 0;
    $score_data['appearance_score'] = (int)$appearance_score;
    $height_score                = $_INPUT['height_score'] ? $_INPUT['height_score'] : 0;
    $score_data['height_score']  = (int)$height_score;
    $weight_score                = $_INPUT['weight_score'] ? $_INPUT['weight_score'] : 0;
    $score_data['weight_score']  = (int)$weight_score;
    $cup_score                   = $_INPUT['cup_score'] ? $_INPUT['cup_score'] : 0;
    $score_data['cup_score']     = (int)$cup_score;
    $bwh_score            = $_INPUT['bwh_score'] ? $_INPUT['bwh_score'] : 0;
    $score_data['bwh_score'] = (int)$bwh_score;
    //插入积分表
    $model_add_obj->insert_model_score($uid, $score_data);
    //其他插入
    $other_data['information_sources'] = $_INPUT['information_sources'] ? $_INPUT['information_sources'] : '';
    $other_data['organization'] = $_INPUT['organization'] ? trim($_INPUT['organization']) : '';
    $other_data['alipay_info']      = $_INPUT['alipay_info'] ? $_INPUT['alipay_info'] : '';
    //$other_data['activity']         = $_INPUT['activity'] ? $_INPUT['activity'] : '';
    //$label            = $_INPUT['label'] ? $_INPUT['label'] : '';
    //标签插入到另外一张表中
    //$model_add_obj->insert_label($uid, $label);
    $model_add_obj->insert_model_other($uid, $other_data);
    $sex                         = $_INPUT['sex'] ? $_INPUT['sex'] : '';
    $stature_data['sex']         = (int)$sex;
    $age                         = $_INPUT['age'] ? $_INPUT['age'] : 0;
    $stature_data['age']         = $age;
    $height                      = $_INPUT['height'] ? $_INPUT['height'] : 0;
    $stature_data['height']      = (int)$height;
    $weight                      = $_INPUT['weight'] ? $_INPUT['weight'] : 0;
    $stature_data['weight']      = (int)$weight;
    $stature_data['cup_id']      = $_INPUT['cup_id'] ? intval($_INPUT['cup_id']) : 0;
    $stature_data['cup_a']       = $_INPUT['cup_a'] ? $_INPUT['cup_a'] : '';
    $stature_data['chest']       = $_INPUT['chest'] ? intval($_INPUT['chest']) : 0;
    $stature_data['waist']       = $_INPUT['waist'] ? intval($_INPUT['waist']) : 0;
    $stature_data['chest_inch']  = $_INPUT['chest_inch'] ? intval($_INPUT['chest_inch']) : 0;
    $shoe_size                   = $_INPUT['shoe_size'] ? $_INPUT['shoe_size'] : 0;
    $stature_data['shoe_size']   = (int)$shoe_size;
    $model_add_obj->insert_model_stature($uid, $stature_data);
    echo "<script type='text/javascript'>window.alert('提交成功');location.href='model_detail.php?uid={$uid}';</script>";
    exit;
   
 ?>