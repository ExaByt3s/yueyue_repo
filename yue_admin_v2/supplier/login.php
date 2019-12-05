<?php
/**
 * @desc:   ������¼ҳ�������Ƕ�����һ��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/6
 * @Time:   11:30
 * version: 2.0
 */
include_once('../common.inc.php');
$pai_user = POCO::singleton ('pai_user_class');
$mall_supplier_obj  = POCO::singleton('pai_mall_supplier_class');
$tpl= new SmartTemplate("login.tpl.htm");

$act = trim($_INPUT['act']);

if ($act == 'sign')
{
    $cellphone = intval($_INPUT['cellphone']);
    $password  = trim($_INPUT['password']);
    if ($cellphone <1 || strlen($password) <1)
    {
        echo "<script type='text/javascript'>window.alert('��������û�������Ϊ��');location.href='login.php';</script>";
        exit;
    }
    $user_id  = $pai_user->user_login($cellphone, $password); //��ͨ��¼
    if ($user_id)
    {
        $supplier = $mall_supplier_obj->get_supplier_info_by_id($user_id);//��Ӧ�̵�¼
        if (!$supplier)
        {
            echo "<script type='text/javascript'>window.alert('��û�е�¼Ȩ��');top.location.href='login.php';</script>";
            exit;
        }
        header("location:index.php");
        exit;
    }
    else
    {
        echo "<script type='text/javascript'>window.alert('��������û�������');location.href='login.php';</script>";
        exit;
    }
}
else
{
    $tpl->output();
    exit;
}
