<?php
/**
 * @desc:   机构登录页，机构是独立的一块
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/6
 * @Time:   11:30
 * version: 2.0
 */
include_once('../common.inc.php');
define('YUE_LOGIN_ORGANIZATION',1);
$pai_user = POCO::singleton ('pai_user_class');
$organization_obj    = POCO::singleton('pai_organization_class');
$tpl= new SmartTemplate("login.tpl.htm");

$act = trim($_INPUT['act']);

if ($act == 'sign')
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
else
{
    $tpl->output();
    exit;
}
