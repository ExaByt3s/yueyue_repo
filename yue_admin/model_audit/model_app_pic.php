<?php
/*
 * app 相册查看控制器
 * xiao xiao
*/

 include_once 'common.inc.php';
 //check_authority(array('model'));
 $app_pic_obj  = POCO::singleton('pai_pic_class');
 $user_add_obj  = POCO::singleton('pai_model_add_class');
 $tpl = new SmartTemplate("model_app_pic.tpl.htm");
 $act = $_INPUT['act'] ? $_INPUT['act'] : 'list';
 $uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
 if ($act == 'list') 
 {
 	 $list = $app_pic_obj->get_user_pic($uid);
 	 $tpl->assign('list', $list);
 	 $tpl->assign('uid', $uid);
 	 $tpl->output();
 }
 elseif ($act == 'same_pic') 
 {
 	$ids = $_INPUT['ids'] ? $_INPUT['ids'] : '';
 	$msg = false;
 	if (!empty($ids) && is_array($ids)) 
 	{
 		$msg = true;
 		foreach ($ids as $key => $id) 
 		{
 			$ret = $app_pic_obj->get_pic_info($id);
 			$data['img_url'] = $ret['img'];
 			$user_add_obj->insert_model_pic($data, $uid);
 		}
 		$where_str = "uid = {$uid}";
 		//$list = $model_add_obj->get_pic_list(false, $where_str);
 	}
 	echo $msg;
 }



?>