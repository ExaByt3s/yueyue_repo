<?php

/**
 * ��֤ѡ��ҳ����̬
 *
 *
 * 2015-6-16
 *
 *
 *  author    ����
 *
 *
 */

include_once 'common.inc.php';

$user_id = intval($yue_login_id);
if($user_id <1)
{


    $r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $r_url = urlencode($r_url);
    echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=".$r_url."';</script>";
    exit;
    //echo "not login error";
    //exit();

    //echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=http%3a%2f%2fyp.yueus.com%2fmall%2fseller%2ftest%2fnormal_certificate_choose.php';</script>";
    //exit;
}
//У���û���ɫ,���̼ұ���
$user_id = $yue_login_id;
/*$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_name=$seller_info['seller_data']['name'];*/

//�°�ֱ����ȥ����ҳ��
header("location:./normal_certificate_basic.php");


$mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
$user_basic_status_list = $mall_basic_check_obj->get_person_status_by_user_id($user_id);
if(!empty($user_basic_status_list['basic_type']) && in_array($user_basic_status_list['status'],array(0,1)))
{
    //�����̼���֤��
    header("location:./normal_certificate_basic.php");
    exit;
}



$pai_mall_certificate_basic_obj = POCO::singleton('pai_mall_certificate_basic_class');
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'normal_certificate_choose.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// ������
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// �ײ�
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');


// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// ͷ��bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);


// �ײ�
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);

$ret = $pai_mall_certificate_basic_obj->check_can_add($user_id);


$tpl->assign($ret);
$tpl->output();

?>