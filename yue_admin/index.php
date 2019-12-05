<?php 

      include_once 'common.inc.php';
	   include_once 'yue_access_control.inc.php';
   	if ($yue_login_id) 
   	{
   		header("location:manage/index.php");
   		exit;
         /* $access_control_url = yueyue_admin_authorization_list();
         //print_r($access_control_url);exit;
         if (array_key_exists('audit_url', $access_control_url)) 
         {
            header("location:manage/index.php");
            exit;
         }
   		elseif (array_key_exists('oa_url', $access_control_url)) 
         {
            header("location:oa/index.php");
            exit;
         }
         elseif (array_key_exists('version_url', $access_control_url)) 
         {
            header("location:version/index.php");
            exit;
         }
          elseif (array_key_exists('cms_url', $access_control_url)) 
         {
            header("location:cms/index.php");
            exit;
         } */
         //exit;
   	}
   	else
   	{
   		$tpl = new SmartTemplate("login.tpl.htm");
   		$tpl->output();
   	}


 ?>