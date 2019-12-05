<?php

/**
 *管理员添加屏蔽 
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-04-08 17:50:51
 * @version 1
 */
 include('common.inc.php');
 $tpl = new SmartTemplate("admin_add_shield.tpl.htm");
 //举报类
 $log_inform_obj = POCO::singleton('pai_log_inform_class');

 //屏蔽类
 $inform_shield_obj = POCO::singleton('pai_log_inform_shield_class');

 $act = trim($_INPUT['act']);

 //屏蔽开始
 if ($act == 'shield') 
 {
 	$user_id = (int)$_INPUT['user_id'];
 	if($user_id < 1) 
 	{
 		js_pop_msg("屏蔽id不能为空");
 		exit;
 	}
    if($user_id < 100000) 
    {
        js_pop_msg("您屏蔽格式有误");
        exit;
    }
     $data_str = trim($_INPUT['data_str']);
     if(strlen($data_str) <1)
     {
         js_pop_msg("请填写拉黑原因");
         exit;
     }
 	//举报者
 	$data['by_informer'] =  $yue_login_id;
 	//被举报者
 	$data['to_informer'] = $user_id;
 	$data['cause_str']   = '管理员手动屏蔽';
 	$data['state']       = 1;
 	$data['add_time']    = date('Y-m-d H:i:s', time());
    $data['data_str'] = $data_str;
    $inform_id = $log_inform_obj->add_info($data);
    if($inform_id)
    {
    	$inform_id = (int)$inform_id;
    	$shield_info        = $inform_shield_obj->shield_user($user_id);
    	$shield_insert_info = $inform_shield_obj->insert_data(array('user_id' => $user_id, 'reason' => '管理员手动屏蔽', 'audit_id' => $yue_login_id,'inform_id'=> $inform_id, 'add_time' => time()));
    	if($shield_insert_info) 
    	{
    		js_pop_msg("屏蔽成功");
    		exit;
    	}
    }
    
    js_pop_msg("屏蔽失败");
    exit;
 }

 $tpl->output();



 ?>
