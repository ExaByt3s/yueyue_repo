<?php

/*
* ɾ��ģ��
*/
 include_once 'common.inc.php';
 $model_add_obj  = POCO::singleton('pai_model_add_class');
 $ids = $_INPUT['ids'] ? $_INPUT['ids'] : '';
 $uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
 if (empty($uid) || empty($ids)) 
 {
 	echo "<script>alert('������������������!');location.href='{$_SERVER['HTTP_REFERER']}';</script>";
    exit();
 }
 if (!empty($ids) && is_array($ids)) 
 {
 	foreach ($ids as $key => $id) 
 	{
 		$model_add_obj->model_del_pic($id);
 	}
 }
 echo "<script>alert('ɾ��ͼƬ�ɹ�!');location.href='model_pic_show.php?uid={$uid}';</script>";
 exit();


?>