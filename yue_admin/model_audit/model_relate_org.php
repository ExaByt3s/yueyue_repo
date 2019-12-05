<?php 
/*
*ģ�غͻ������������
* xiao xiao
* 2014-1-23
*/

	include_once 'common.inc.php';
 	$organization_obj  = POCO::singleton('pai_organization_class');
 	$model_relate_org  = POCO::singleton('pai_model_relate_org_class');
 	
 	//ģ�ػ���log
 	$org_log_obj  = POCO::singleton('pai_org_log_class');
 	
 	$tpl = new SmartTemplate("model_relate_org.tpl.htm");
 	
 	
    $act = $_INPUT['act'] ? $_INPUT['act'] : 'add';
    $uid = $_INPUT['uid'] ? intval($_INPUT['uid']) : 0;   
    
    $arr = array();
 	switch ($act) 
 	{
 		case 'add':
 			 $selected_org = $model_relate_org->get_org_list_by_user_id(false, $uid);
 			 if (!empty($selected_org) && is_array($selected_org)) 
 			 {
 			 	foreach ($selected_org as $key => $vo) 
 			 	{
 			 		$selected_org[$key]['org_name'] = $organization_obj->get_org_name_by_user_id($vo['org_id']);
 			 	}
 			 	
 			 }
 			 $list = $organization_obj->get_org_list(false,'status=1','id DESC','0,1000','user_id,nick_name');
 			 $tpl->assign('uid', $uid);
 			 $tpl->assign('list', $list);
 			 $tpl->assign('selected_org', $selected_org);
 			 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
             $tpl->output();
 			break;
 		case 'insert':
 			//��������
 			
 			//�޸�ǰ��ȡ��������
 			$org_ret = $model_relate_org->get_org_list_by_user_id(false, $uid);
 			if(!is_array($org_ret)) $org_ret = array();
 			
 			//����log����
 			$data_log = array();
 			$data_log['model_user_id'] = $uid;
 			$data_log['audit_user_id'] = $yue_login_id;
 			$data_log['add_time']      = time();
 			foreach ($org_ret as $key=>$val)
 			{
 				if($val['priority'] == 1) $data_log['before_priority_org_id'] = $val['org_id'];
 				if($key != 0) $data_log['before_org_id'] .= ',';
 				$data_log['before_org_id'] .= $val['org_id'];
 			}
 			
 			
            //pai_log_class::add_log(array(), 'insert', 'org_log');
            
 			$org_id   = $_INPUT['org_id'] ? $_INPUT['org_id'] : 0;
 			$priority = $_INPUT['priority'] ? intval($_INPUT['priority']) : 0;
 			$model_relate_org->delete_org_by_user_id($uid);
 			if (!empty($org_id) && is_array($org_id)) 
 			{
 				$count = count($org_id);
 				//����Ϊ1
 				if ($count == 1) 
 				{
 					$data['priority'] = 1;
 					$data['org_id']   = $org_id[0];
 					$data['user_id']  = $uid;
 					$model_relate_org->add_model_org($data);
 				}
 				//����������Ϊ1
 				else
 				{
 					foreach ($org_id as $key => $vo) 
 				   {
 				   	  if (!$priority) 
 				   	  {
 				   	  	if ($key == 0) 
 				   	  	{
 				   	  		$data['priority'] = 1;
 				   	  	}
 				   	  	else
 				   	  	{
 				   	  		$data['priority'] = 0;
 				   	  	}
 				   	  }
 				   	  elseif($priority) 
 				   	  {
 				   	  	if ($vo == $priority) 
 					    {
 					       $data['priority'] = 1;
 					    }
 					    else
 					    {
 						  $data['priority'] = 0;
 					    }
 				   	  }
 					  $data['org_id']   = $vo;
 					  $data['user_id']  = $uid;
 					  $model_relate_org->add_model_org($data);
 				   }	
 				}
 			}
 			
 			
 			//�޸ĺ�ģ�ػ���
 			$after_org_ret = $model_relate_org->get_org_list_by_user_id(false, $uid);
 			if(!is_array($after_org_ret)) $after_org_ret = array();
 			foreach ($after_org_ret as $key=>$val)
 			{
 				if($val['priority'] == 1) $data_log['after_priority_org_id'] = $val['org_id'];
 				if($key != 0) $data_log['after_org_id'] .= ',';
 				$data_log['after_org_id'] .= $val['org_id'];
 			}
 			//��ӻ���log
 			$org_log_obj->add_info($data_log);
 			
 			
 			$ret = $model_relate_org->get_org_info_by_user_id($uid);
 			if (!empty($ret) && is_array($ret)) 
 			{
 			    $org_name = $organization_obj->get_org_name_by_user_id($ret['org_id']);
 			    $ret['org_name'] = iconv( "GB2312", "UTF-8", $org_name);
 			}
 			$arr  = array( 'msg' => 'success' , 'ret' => $ret);
 			echo json_encode($arr);
 			exit;
 	}


 ?>