<?php 


	include_once 'common.inc.php';
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
 	$data['img_url'] = $_INPUT['img_url'] ? $_INPUT['img_url'] : '';
    $uid             = $_INPUT['uid']     ? $_INPUT['uid'] : 0;
    $act             = $_INPUT['act']     ? $_INPUT['act'] : '';
    $arr = array();
 	switch ($act) {
 		//进相册表
 		case 'thumb':
 			$ret = $model_add_obj->insert_model_pic($data, $uid);
 			//$ret['img'] = substr($ret['img_url'], 0, strrpos($ret['img_url'], ".")-4).substr($ret['img_url'],strrpos($ret['img_url'], "."), 4);
 			$ret['img'] = str_replace('_260.','.',$vo['img_url']);
 			$arr  = array('msg' => 'success','ret' => $ret);
 			echo json_encode($arr);
 			break;
 		case 'pic':
 			$model_add_obj->insert_model_info(false, $uid, $data);
 			echo true;
 			break;
 	}
 	
    
    
    
 ?>