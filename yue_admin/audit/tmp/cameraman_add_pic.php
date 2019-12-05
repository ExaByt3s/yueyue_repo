<?php 
/*
 *摄影师图片添加
*/


	include_once 'common.inc.php';
	check_authority(array('cameraman'));
 	$cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
 	$data['img_url'] = $_INPUT['img_url'] ? $_INPUT['img_url'] : '';
    $uid             = $_INPUT['uid']     ? $_INPUT['uid'] : 0;
    $act             = $_INPUT['act']     ? $_INPUT['act'] : '';
    $arr = array();
 	switch ($act) {
 		//进相册表
 		case 'thumb':
 			$ret = $cameraman_add_obj->insert_cameraman_pic($uid, $data);
 			$ret['img'] = substr($ret['img_url'], 0, strrpos($ret['img_url'], ".")-4).substr($ret['img_url'],strrpos($ret['img_url'], "."), 4);
 			$arr  = array('msg' => 'success','ret' => $ret);
 			echo json_encode($arr);
 			break;
 		case 'pic':
 			$cameraman_add_obj->insert_cameraman_info(false, $uid, $data);
 			echo true;
 			break;
 		//荣誉表
 		case 'honor':
 		    $ret = $cameraman_add_obj->insert_cameraman_honor($uid, $data);
 			$ret['img'] = substr($ret['img_url'], 0, strrpos($ret['img_url'], ".")-4).substr($ret['img_url'],strrpos($ret['img_url'], "."), 4);
 			$arr  = array('msg' => 'success','ret' => $ret);
 			echo json_encode($arr);
 			break;
 	}
 	
    
    
    
 ?>