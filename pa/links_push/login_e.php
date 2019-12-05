<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$act = trim($_INPUT['act']);

if($act == 'sign')//登录
{
    $cellphone = intval($_INPUT['cellphone']);
    $password  = trim($_INPUT['password']);
    if (empty($cellphone) || empty($password))
    {
        echo "<script type='text/javascript'>parent.alert('密码或者用户名不能为空');</script>";
        exit;
    }
    $pai_user = POCO::singleton ('pai_user_class');
    $user_id  = $pai_user->user_login($cellphone, $password);
    if($user_id)
    {
        echo "<script type='text/javascript'>parent.location='index.php'</script>";
        exit();
    }
    else
    {
        echo "<script type='text/javascript'>parent.alert('密码或者用户名错误');</script>";
        exit;
    }
}

$tpl = new SmartTemplate( 'templates/login.tpl.htm' );
$tpl->output();