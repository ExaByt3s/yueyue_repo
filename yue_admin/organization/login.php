<?php 
/*
 *登录
*/
   include_once('../common.inc.php');
echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin_v2/organization';</script>";
exit;
   define('YUE_LOGIN_ORGANIZATION',1);
   $pai_user = POCO::singleton ('pai_user_class');
   $organization_obj    = POCO::singleton('pai_organization_class');
   $tpl       = new SmartTemplate("login.tpl.htm");
   $act       = $_INPUT['act'] ? $_INPUT['act'] : 'login';
   if ($act == 'login') 
   {
      $tpl->output();
      exit;
   }
   elseif ($act == 'sign') 
   {
       $cellphone = $_INPUT['cellphone'] ? $_INPUT['cellphone'] : "";
       $password  = $_INPUT['password'] ?  $_INPUT['password'] : "";
       if (empty($cellphone) || empty($password)) 
       {
          echo "<script type='text/javascript'>window.alert('密码或者用户名不能为空');location.href='login.php';</script>";
             exit;
       }
       $user_id  = $pai_user->user_login($cellphone, $password);
       if ($user_id) 
       {
          $id = $organization_obj->get_org_id_by_user_id_v2($user_id,1);
          if ($id) 
          {
            header("location:index.php");
            exit;
          }
          else
          {
            echo "<script type='text/javascript'>window.alert('您没有登录该模块权限');location.href='login.php';</script>";
             exit;
          }
       }
       else
       {
          echo "<script type='text/javascript'>window.alert('密码或者用户名错误');location.href='login.php';</script>";
          exit;
       }
   }
 ?>