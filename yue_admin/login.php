<?php 

   include_once 'common.inc.php';
   $cellphone = $_INPUT['cellphone'] ? $_INPUT['cellphone'] : "";
   $password  = $_INPUT['password'] ?  $_INPUT['password'] : "";
   $referer_url = $_INPUT['referer_url'];
   
   if (empty($cellphone) || empty($password)) 
   {
      echo "<script type='text/javascript'>parent.alert('密码或者用户名不能为空');</script>";
      exit;
   }
   $pai_user = POCO::singleton ('pai_user_class');
   $user_id  = $pai_user->user_login($cellphone, $password);
   if ($user_id) 
   {
      if($referer_url) 
      {
        $url = urldecode($referer_url);
        echo "<script type='text/javascript'>parent.location='{$url}'</script>";
        exit();
      }
      echo "<script type='text/javascript'>parent.location='./manage/index.php'</script>";
      exit;
   }
   else
   {
       echo "<script type='text/javascript'>parent.alert('密码或者用户名错误');</script>";
      exit;
   }

 ?>