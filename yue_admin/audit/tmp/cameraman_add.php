<?php 


	include_once 'common.inc.php';
    check_authority(array('cameraman'));
 	$cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
    //print_r($_POST);exit;
    $uid                         = $_INPUT['uid'] ? $_INPUT['uid'] : 0; 
    $data_info['name']           = $_INPUT['name'] ? $_INPUT['name'] : '';
    $data_info['nick_name']      = $_INPUT['nick_name'] ? $_INPUT['nick_name'] : '';
    $data_info['weixin_name']    = $_INPUT['weixin_name'] ? $_INPUT['weixin_name'] : '';
    $data_info['discuz_name']    = $_INPUT['discuz_name'] ? $_INPUT['discuz_name'] : '';
    $data_info['poco_name']      = $_INPUT['poco_name'] ? $_INPUT['poco_name'] : '';
    $data_info['app_title']      = $_INPUT['app_title'] ? $_INPUT['app_title'] : '';
    $data_info['app_name']       = $_INPUT['app_name'] ? $_INPUT['app_name'] : '';
    $data_info['phone']          = $_INPUT['phone'] ? $_INPUT['phone'] : '';
    $data_info['weixin_id']      = $_INPUT['weixin_id'] ? $_INPUT['weixin_id'] : '';
    //$data_info['weixin_id']      = (int)$weixin_id;
    $qq                          = $_INPUT['qq'] ? $_INPUT['qq'] : 0;
    $data_info['qq']             = (int)$qq;
    $data_info['email']          = $_INPUT['email'] ? $_INPUT['email'] : '';
    $data_info['weibo']          = $_INPUT['weibo'] ? $_INPUT['weibo'] : '';
    $data_info['homepage']       = $_INPUT['homepage'] ? $_INPUT['homepage'] : '';
    $poco_id                     = $_INPUT['poco_id'] ? $_INPUT['poco_id'] : 0;
    $data_info['poco_id']        = $poco_id;
    $data_info['city']           = $_INPUT['city'] ? $_INPUT['city'] : 0;
    $data_info['inputer_name']   = $_INPUT['inputer_name'] ? $_INPUT['inputer_name'] : '';
    $data_info['inputer_time']   = date('Y-m-d H:i:s', time());
    $data_info['phone']          = $_INPUT['phone'] ? $_INPUT['phone'] : '';
    //插进入基本信息
    $cameraman_add_obj->insert_cameraman_info(true ,$uid,$data_info);
    //exit;
    //个人信息
    $data_person['sex']              = $_INPUT['sex'] ? intval($_INPUT['sex']) : 0;
    //$data_person['month_take']       = $_INPUT['month_take'] ? intval($_INPUT['month_take']) : 0;
    $data_person['car_type']         = $_INPUT['car_type'] ? $_INPUT['car_type'] : '';
    $data_person['birthday']         = $_INPUT['birthday'] ? $_INPUT['birthday'] : '';
    //$data_person['attend_total']     = $_INPUT['attend_total'] ? intval($_INPUT['attend_total']) : 0;
    $data_person['good_at']          = $_INPUT['good_at'] ? $_INPUT['good_at'] : '';
    $data_person['p_state']          = $_INPUT['p_state'] ? intval($_INPUT['p_state']) : 0;
    $data_person['is_studio']        = $_INPUT['is_studio'] ? intval($_INPUT['is_studio']) : 0;
    $data_person['join_age']         = $_INPUT['join_age'] ? intval($_INPUT['join_age']) : 0;
    $data_person['studio_name']      = $_INPUT['studio_name'] ? $_INPUT['studio_name'] : '';
    $data_person['photographic']     = $_INPUT['photographic'] ? $_INPUT['photographic'] : '';
    $data_person['is_fview']         = $_INPUT['is_fview'] ? intval($_INPUT['is_fview']) : 0;
    $cameraman_add_obj->insert_cameraman_personal($uid, $data_person);
    //个人信息 END
    //其他插入
    $data_other['information_sources'] = $_INPUT['information_sources'] ? $_INPUT['information_sources'] : '';
    $data_other['activity']            = $_INPUT['activity'] ? $_INPUT['activity'] : '';
    $data_other['remark']              = $_INPUT['remark'] ? $_INPUT['remark'] : '';
    $cameraman_add_obj->insert_cameraman_other($uid, $data_other);   
    echo "<script type='text/javascript'>window.alert('提交成功');location.href='cameraman_detail.php?uid={$uid}';</script>";
    exit;
   
 ?>