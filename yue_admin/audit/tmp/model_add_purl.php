<?php 
/*样片链接控制器*/

	include_once 'common.inc.php';
	check_authority(array('model'));
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
 	$tpl = new SmartTemplate("model_add_purl.tpl.htm");
    $act = $_INPUT['act'] ? $_INPUT['act'] : 'add';
    $uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;   
    $arr = array();
 	switch ($act) {
 		case 'add':
 			 $list = $model_add_obj->get_purl($uid);
 			 if (!empty($list)) {
 			 	 $tpl->assign('list', $list);
 			 }
 			 else
 			 {
 			 	$tpl->assign('default_purl', true);
 			 }
 			 $tpl->assign('uid', $uid);
 			 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
             $tpl->output();
 			break;
 		case 'insert':
 			//插入数据
 			$p_url = $_INPUT['p_url'] ? $_INPUT['p_url'] : '';
 			//print_r($p_url);exit;
 			$model_add_obj->delete_purl($uid);
 			if (!empty($p_url) && is_array($p_url)) 
 			{
 				foreach ($p_url as $key => $url) 
 				{
 					$data['p_url'] = iconv("UTF-8", "GB2312" , $url);
 					$model_add_obj->insert_purl($uid, $data);
 				}
 			}
 			$ret = $model_add_obj->find_purl_info($uid);
 			$ret['p_url'] = iconv( "GB2312", "UTF-8", $ret['p_url']);
 			$arr  = array( 'msg' => 'success' , 'ret' => $ret);
 			echo json_encode($arr);
 			exit;
 	}


 ?>