<?php 
/*
*活动报名控制器
*/

	include_once 'common.inc.php';
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
 	$tpl = new SmartTemplate("model_add_activjoin.tpl.htm");
    $act = $_INPUT['act'] ? $_INPUT['act'] : 'add';
    $uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;   
    $arr = array();
 	switch ($act) {
 		case 'add':
 			 $list = $model_add_obj->get_join_info($uid);
 			 if (!empty($list)) {
 			 	 $tpl->assign('list', $list);
 			 }
 			 else
 			 {
 			 	$tpl->assign('default_join', true);
 			 }
 			 $tpl->assign('uid', $uid);
 			 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
             $tpl->output();
 			break;
 		case 'insert':
 			//插入数据
 			$activity_join = $_INPUT['activity_join'] ? $_INPUT['activity_join'] : '';
 			//print_r($p_url);exit;
 			$model_add_obj->delete_join($uid);
 			if (!empty($activity_join) && is_array($activity_join)) 
 			{
 				foreach ($activity_join as $key => $join_info) 
 				{
 					$data['activity_join'] = iconv("UTF-8", "GB2312", $join_info);
 					$model_add_obj->insert_join($uid, $data);
 				}
 			}
 			$ret = $model_add_obj->find_join_info($uid);
 			$ret['activity_join'] = iconv( "GB2312", "UTF-8", $ret['activity_join']);
 			$arr  = array( 'msg' => 'success' , 'ret' => $ret);
 			echo json_encode($arr);
 			break;
 	}


 ?>