<?php 
/*标签控制器*/

	include_once 'common.inc.php';
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
    $fulltext_obj = POCO::singleton('pai_fulltext_class');
 	$tpl = new SmartTemplate("model_add_label.tpl.htm");
    $act = $_INPUT['act'] ? $_INPUT['act'] : 'add';
    $uid = intval($_INPUT['uid']);
    $arr = array();
 	switch ($act)
    {
 		case 'add':
 			 $list = $model_add_obj->get_label_info($uid);
 			 if (!empty($list))
             {
 			 	 $tpl->assign('list', $list);
 			 }
 			 else
 			 {
 			 	$tpl->assign('default_label', true);
 			 }
 			 $tpl->assign('uid', $uid);
 			 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
             $tpl->output();
 			break;
 		case 'insert':
 			//插入数据
 			$label = $_INPUT['label'] ? $_INPUT['label'] : '';
 			//print_r($p_url);exit;
 			$model_add_obj->delete_label($uid);
 			if (!empty($label) && is_array($label)) 
 			{
 				foreach ($label as $key => $label_info) 
 				{
 					$data['label'] = iconv("UTF-8", "GB2312", $label_info);
 					$model_add_obj->insert_label($uid, $data);
 				}
 			}
 			$ret = $model_add_obj->find_label_info($uid);
            $fulltext_obj->cp_data_by_user_id($uid);
 			$ret['label'] = iconv( "GB2312", "UTF-8", $ret['label']);
 			$arr  = array( 'msg' => 'success' , 'ret' => $ret);
 			echo json_encode($arr);
 			exit;
 	}


 ?>