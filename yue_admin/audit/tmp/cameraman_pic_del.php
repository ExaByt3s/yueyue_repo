<?php

/*
* ɾ����Ӱʦ��������
*/
 include_once 'common.inc.php';
 check_authority(array('cameraman'));
 $cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
 $ids = $_INPUT['ids'] ? $_INPUT['ids'] : '';
 $uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
 $act = $_INPUT['act'] ? $_INPUT['act'] :'';
 if (empty($uid) || empty($ids)) 
 {
 	echo "<script>alert('������������������!');location.href='{$_SERVER['HTTP_REFERER']}';</script>";
    exit();
 }
 //die($honor);
 //�ж�ɾ������Щ����
 switch ($act) 
 {
 	case 'thumb':
 		 if (!empty($ids) && is_array($ids)) 
           {
 	        foreach ($ids as $key => $id) 
 	        {
 		      $cameraman_add_obj->cameraman_del_pic($id);
 	        }
           }
 		break;
 	case 'honor':
 		 //print_r($ids);exit;
 		  if (!empty($ids) && is_array($ids)) 
           {
 	        foreach ($ids as $key => $id) 
 	        {
 		      $cameraman_add_obj->cameraman_del_honor($id);
 	        }
           }
 		break;
 }
 echo "<script>alert('ɾ���ɹ�!');location.href='{$_SERVER['HTTP_REFERER']}';</script>";
 exit();


?>