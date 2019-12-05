<?php

/**
 *����Ա������� 
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-04-08 17:50:51
 * @version 1
 */
 include('common.inc.php');
 $tpl = new SmartTemplate("admin_add_shield.tpl.htm");
 //�ٱ���
 $log_inform_obj = POCO::singleton('pai_log_inform_class');

 //������
 $inform_shield_obj = POCO::singleton('pai_log_inform_shield_class');

 $act = trim($_INPUT['act']);

 //���ο�ʼ
 if ($act == 'shield') 
 {
 	$user_id = (int)$_INPUT['user_id'];
 	if($user_id < 1) 
 	{
 		js_pop_msg("����id����Ϊ��");
 		exit;
 	}
    if($user_id < 100000) 
    {
        js_pop_msg("�����θ�ʽ����");
        exit;
    }
     $data_str = trim($_INPUT['data_str']);
     if(strlen($data_str) <1)
     {
         js_pop_msg("����д����ԭ��");
         exit;
     }
 	//�ٱ���
 	$data['by_informer'] =  $yue_login_id;
 	//���ٱ���
 	$data['to_informer'] = $user_id;
 	$data['cause_str']   = '����Ա�ֶ�����';
 	$data['state']       = 1;
 	$data['add_time']    = date('Y-m-d H:i:s', time());
    $data['data_str'] = $data_str;
    $inform_id = $log_inform_obj->add_info($data);
    if($inform_id)
    {
    	$inform_id = (int)$inform_id;
    	$shield_info        = $inform_shield_obj->shield_user($user_id);
    	$shield_insert_info = $inform_shield_obj->insert_data(array('user_id' => $user_id, 'reason' => '����Ա�ֶ�����', 'audit_id' => $yue_login_id,'inform_id'=> $inform_id, 'add_time' => time()));
    	if($shield_insert_info) 
    	{
    		js_pop_msg("���γɹ�");
    		exit;
    	}
    }
    
    js_pop_msg("����ʧ��");
    exit;
 }

 $tpl->output();



 ?>
