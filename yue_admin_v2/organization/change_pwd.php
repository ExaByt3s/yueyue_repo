<?php 
/*
*
* �����޸�
* xiao xiao 
* 2014-1-26
*/
  include('common.inc.php');
  $user_obj  = POCO::singleton('pai_user_class');
  $tpl = new SmartTemplate("change_pwd.tpl.htm");
  $act = $_INPUT['act'] ? $_INPUT['act'] : '';
  if ($act == 'update') 
  {
  		$user_id    = $_INPUT['user_id'] ? intval($_INPUT['user_id']) : 0;
      $pwd_hash   = $_INPUT['pwd_hash'] ? $_INPUT['pwd_hash'] : '';
      $new_pwd    = $_INPUT['new_pwd'] ? $_INPUT['new_pwd'] : '';
      $cofirm_pwd = $_INPUT['cofirm_pwd'] ? $_INPUT['cofirm_pwd'] : '';
      if (empty($user_id)) 
      {
         echo "<script type='text/javascript'>window.alert('�Ƿ�����');location.href='change_pwd.php';</script>";
         exit;
      }
      if (empty($pwd_hash)) 
      {
        echo "<script type='text/javascript'>window.alert('�����벻��Ϊ��');location.href='change_pwd.php';</script>";
         exit;
      }
      if (empty($new_pwd)) 
      {
        echo "<script type='text/javascript'>window.alert('�����벻��Ϊ��');location.href='change_pwd.php';</script>";
         exit;
      }
      if (strlen($new_pwd) < 6) 
      {
        echo "<script type='text/javascript'>window.alert('�����벻������6���ַ�');location.href='change_pwd.php';</script>";
         exit;
      }
      if ($new_pwd != $cofirm_pwd) 
      {
        echo "<script type='text/javascript'>window.alert('�������벻��ͬ');location.href='change_pwd.php';</script>";
         exit;
      }
      $res = $user_obj->check_pwd($user_id,$pwd_hash);
      if (empty($res)) 
      {
         echo "<script type='text/javascript'>window.alert('��������������');location.href='change_pwd.php';</script>";
         exit;
      }
      $info = $user_obj->update_pwd_by_user_id($user_id, $new_pwd);
      if ($info) 
      {
        //$user_info_arr['role']      = 'cameraman';
       // $user_obj->update_user($user_info_arr, $user_id);
        echo "<script type='text/javascript'>window.alert('��������ɹ�');location.href='change_pwd.php';</script>";
         exit;
      }
      else
      {
        echo "<script type='text/javascript'>window.alert('��������ʧ��');location.href='change_pwd.php';</script>";
         exit;
      }
  }

  $tpl->assign('user_id', $yue_login_id);
  $tpl->output();



 ?>