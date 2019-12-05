<?php 
/*
 *获取模板内容
 *xiao xiao
 *2015-1-16
*/
 include("common.inc.php");
 $id  = intval($_INPUT['id']) ? intval($_INPUT['id']) : 0;
 //echo $id;exit;
 $template_obj = POCO::singleton('pai_template_class');
 $ret = $template_obj->get_template_info_by_id($id);
 //print_r($ret);exit;
 $arr = array();
 if (!empty($ret)) 
 {
 	$arr  = array
 	(
 		'msg' => $msg,
 		'ret' => iconv('gb2312', 'UTF-8',$ret['tpl_detail'])
 	);
 }
 echo json_encode($arr);

 ?>