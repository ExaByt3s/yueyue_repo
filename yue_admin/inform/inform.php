<?php

/**
 *举报控制器
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-04 14:10:13
 * @version 1
 */
 include('common.inc.php');
 $tpl = new SmartTemplate("inform_edit.tpl.htm");
 //举报类
 $log_inform_obj = POCO::singleton('pai_log_inform_class');
 //屏蔽类
 $inform_shield_obj = POCO::singleton('pai_log_inform_shield_class');
 //引入用户类
 $user_obj       = POCO::singleton('pai_user_class');
 //摄影师等级
 $user_level_obj = POCO::singleton('pai_user_level_class');
 //模特等级
 $score_rank_obj = POCO::singleton('pai_score_rank_class');
 $act = $_INPUT['act'] ? $_INPUT['act'] : 'list';
 $id   = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
 if (!$id) 
 {
   echo "<script type='text/javascript'>window.alert('非法操作');history.back();</script>";
   exit;
 }
 //屏蔽用户
 if ($act == 'shield') 
 {
 	$user_id = $_INPUT['user_id'] ? intval($_INPUT['user_id']) : 0;
   $reason  = $_INPUT['reason'] ? $_INPUT['reason'] : '';
   $audit_id   = $yue_login_id;
 	if (!$user_id) 
 	{
 		echo "<script type='text/javascript'>window.alert('非法操作');history.back();</script>";
 		exit;
 	}
   $info               = $log_inform_obj->update_info(array('state'=> 1), $id);
   $shield_info        = $inform_shield_obj->shield_user($user_id);
   //echo $shield_info;exit;
   $shield_insert_info = $inform_shield_obj->insert_data(array('user_id' => $user_id, 'reason' => $reason, 'audit_id' => $audit_id,'inform_id'=> $id, 'add_time' => time()));
   if ($info && $shield_info && $shield_insert_info) 
   {

      echo "<script type='text/javascript'>window.alert('操作成功');location.href='inform_list.php';</script>";
      exit;
   }
   echo "<script type='text/javascript'>window.alert('操作失败');history.back();</script>";
   exit;
 }
 //内容部分
 //角色(by 表示举报人|to 被举报的人)
 //$role = $_INPUT['role'] ? $_INPUT['role'] : 'by';
 $ret  = $log_inform_obj->get_info($id);
 if (is_array($ret)) 
 {
    //被举报人状态
    $ret['info']  = $inform_shield_obj->get_info_by_user_id($ret['to_informer']);
    //举报人信息结合
    $by_informer_data['by_informer_name'] = get_user_nickname_by_user_id($ret['by_informer']);
    $by_informer_data['by_cellphone'] = $user_obj->get_phone_by_user_id($ret['by_informer']);
    //举报人
    $by_informer_data['by_be_count'] = $log_inform_obj->get_inform_count($ret['by_informer'],'by');
    //举报被人
    $by_informer_data['by_to_count'] = $log_inform_obj->get_inform_count($ret['by_informer'],'to');
    $by_role = $user_obj->check_role($ret['by_informer']);
    $by_informer_data['by_role'] = $by_role;
    if ($by_role == "cameraman") 
    {
       $level = $user_level_obj->get_user_level($ret['by_informer']);
       $by_informer_data['by_level'] = $level;
    }
    else
    {
       $level_by_data = $score_rank_obj->get_score_rank($ret['by_informer']);
       if (is_array($level_by_data)) 
       {
         $by_informer_data['by_level'] = $level_by_data['level'];
       }
    }
    //被举报人信息结合
    $to_informer_data['to_informer_name'] = get_user_nickname_by_user_id($ret['to_informer']);
    $by_informer_data['to_cellphone'] = $user_obj->get_phone_by_user_id($ret['to_informer']);
    //被人举报
    $to_informer_data['to_be_count'] = $log_inform_obj->get_inform_count($ret['to_informer'],'by');
    //举报被人
    $to_informer_data['to_to_count'] = $log_inform_obj->get_inform_count($ret['to_informer'],'to');
    $to_role = $user_obj->check_role($ret['to_informer']);
    $to_informer_data['to_role'] = $to_role;
    if ($to_role == "cameraman") 
    {
       $level = $user_level_obj->get_user_level($ret['to_informer']);
       $to_informer_data['to_level'] = $level;
    }
    else
    {
       $level_to_data = $score_rank_obj->get_score_rank($ret['to_informer']);
       if (is_array($level_to_data)) 
       {
         $to_informer_data['to_level'] = $level_to_data['level'];
       }
    }
 }
 $tpl->assign($ret);
 $tpl->assign($by_informer_data);
 $tpl->assign($to_informer_data);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output ();