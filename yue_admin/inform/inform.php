<?php

/**
 *�ٱ�������
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-04 14:10:13
 * @version 1
 */
 include('common.inc.php');
 $tpl = new SmartTemplate("inform_edit.tpl.htm");
 //�ٱ���
 $log_inform_obj = POCO::singleton('pai_log_inform_class');
 //������
 $inform_shield_obj = POCO::singleton('pai_log_inform_shield_class');
 //�����û���
 $user_obj       = POCO::singleton('pai_user_class');
 //��Ӱʦ�ȼ�
 $user_level_obj = POCO::singleton('pai_user_level_class');
 //ģ�صȼ�
 $score_rank_obj = POCO::singleton('pai_score_rank_class');
 $act = $_INPUT['act'] ? $_INPUT['act'] : 'list';
 $id   = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
 if (!$id) 
 {
   echo "<script type='text/javascript'>window.alert('�Ƿ�����');history.back();</script>";
   exit;
 }
 //�����û�
 if ($act == 'shield') 
 {
 	$user_id = $_INPUT['user_id'] ? intval($_INPUT['user_id']) : 0;
   $reason  = $_INPUT['reason'] ? $_INPUT['reason'] : '';
   $audit_id   = $yue_login_id;
 	if (!$user_id) 
 	{
 		echo "<script type='text/javascript'>window.alert('�Ƿ�����');history.back();</script>";
 		exit;
 	}
   $info               = $log_inform_obj->update_info(array('state'=> 1), $id);
   $shield_info        = $inform_shield_obj->shield_user($user_id);
   //echo $shield_info;exit;
   $shield_insert_info = $inform_shield_obj->insert_data(array('user_id' => $user_id, 'reason' => $reason, 'audit_id' => $audit_id,'inform_id'=> $id, 'add_time' => time()));
   if ($info && $shield_info && $shield_insert_info) 
   {

      echo "<script type='text/javascript'>window.alert('�����ɹ�');location.href='inform_list.php';</script>";
      exit;
   }
   echo "<script type='text/javascript'>window.alert('����ʧ��');history.back();</script>";
   exit;
 }
 //���ݲ���
 //��ɫ(by ��ʾ�ٱ���|to ���ٱ�����)
 //$role = $_INPUT['role'] ? $_INPUT['role'] : 'by';
 $ret  = $log_inform_obj->get_info($id);
 if (is_array($ret)) 
 {
    //���ٱ���״̬
    $ret['info']  = $inform_shield_obj->get_info_by_user_id($ret['to_informer']);
    //�ٱ�����Ϣ���
    $by_informer_data['by_informer_name'] = get_user_nickname_by_user_id($ret['by_informer']);
    $by_informer_data['by_cellphone'] = $user_obj->get_phone_by_user_id($ret['by_informer']);
    //�ٱ���
    $by_informer_data['by_be_count'] = $log_inform_obj->get_inform_count($ret['by_informer'],'by');
    //�ٱ�����
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
    //���ٱ�����Ϣ���
    $to_informer_data['to_informer_name'] = get_user_nickname_by_user_id($ret['to_informer']);
    $by_informer_data['to_cellphone'] = $user_obj->get_phone_by_user_id($ret['to_informer']);
    //���˾ٱ�
    $to_informer_data['to_be_count'] = $log_inform_obj->get_inform_count($ret['to_informer'],'by');
    //�ٱ�����
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