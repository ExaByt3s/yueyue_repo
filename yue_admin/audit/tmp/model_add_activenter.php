<?php 
/*活动入围控制器*/

	include_once 'common.inc.php';
	check_authority(array('model'));
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
 	$tpl = new SmartTemplate("model_add_activenter.tpl.htm");
    $act = $_INPUT['act'] ? $_INPUT['act'] : 'add';
    $uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;   
    $arr = array();
 	switch ($act) {
 		case 'add':
 			 $list = $model_add_obj->get_enter_info($uid);
 			 if (!empty($list)) {
 			 	 $tpl->assign('list', $list);
 			 }
 			 else
 			 {
 			 	$tpl->assign('default_enter', true);
 			 }
 			 $tpl->assign('uid', $uid);
 			 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
             $tpl->output();
 			break;
 		case 'insert':
 			//插入数据
 			$activity_enter = $_INPUT['activity_enter'] ? $_INPUT['activity_enter'] : '';
 			//print_r($p_url);exit;
 			$model_add_obj->delete_enter($uid);
 			if (!empty($activity_enter) && is_array($activity_enter)) 
 			{
 				foreach ($activity_enter as $key => $enter_info) 
 				{
 					$data['activity_enter'] = iconv("UTF-8", "GB2312", $enter_info);
 					$model_add_obj->insert_enter($uid, $data);
 				}
 			}
 			$ret = $model_add_obj->find_enter_info($uid);
 			$ret['activity_enter'] = iconv( "GB2312", "UTF-8", $ret['activity_enter']);
 			$arr  = array( 'msg' => 'success' , 'ret' => $ret);
 			echo json_encode($arr);
 			break;
 	}


 ?>