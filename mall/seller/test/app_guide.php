<?php

/**
 * �༭����ѡ�񣬾�̬
 *
 *
 * 2015-6-17
 *
 *
 *  author    ����
 *
 *
 */

include_once 'common.inc.php';
$pc_wap = 'pc/';

$type = trim($_INPUT['type']);


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'app_guide.tpl.htm');
//У���û���ɫ,���̼ұ���
$user_id = $yue_login_id;

$guide_type_arr = array("order","money");
if(!in_array($type,$guide_type_arr))
{
    $type = "order";
}

if($type=="money")
{
    //to do:��ȡ�û��Ľ�Ǯ
    // �˺����
    $pai_payment_obj = POCO::singleton('pai_payment_class');
    $user_available_balance = $pai_payment_obj->get_user_available_balance($user_id);
    $tpl->assign('user_available_balance', $user_available_balance);



    $page_title = "��������ҳ";
    $nav_guide = "�������";

}
else
{
    $page_title = "��������ҳ";
    $nav_guide = "��������";
}




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




$tpl->assign('type', $type);
$tpl->assign('page_title', $page_title);
$tpl->assign('nav_guide', $nav_guide);

//echo $yue_login_id;
//print_r($type_list);


$tpl->output();

?>