<?php 

/*
 * xiao xiao 
 * �鿴ͷ���ͬ��ͷ��
 *
*/
 include("common.inc.php");
 $model_add_obj  = POCO::singleton('pai_model_add_class');
 $tpl = new SmartTemplate("model_check_pic.tpl.htm");
 $act = $_INPUT['act'] ? $_INPUT['act'] : 'check';
 $uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
 if ($act == 'check') 
 {
 	if (empty($uid)) 
 	{
 		die('�ύ��������������!');
 	}
 	$img_url = get_user_icon($uid, 165 );
 	$tpl->assign('img_url', $img_url);
 	$tpl->assign('uid', $uid);
 	$tpl->output();
 }
 elseif($act == 'send_icon')
 {
 	$img_url = $_INPUT['img_url'] ? $_INPUT['img_url'] : '';
 	$data['img_url'] = $img_url;
 	$model_add_obj->insert_model_info(false, $uid ,$data);
 	echo true;
 }


 ?>