<?php


// ע�⣺ �˳���Ϊ��ҳʹ�� ���𿽱�
    define("index_value",1);
// ע�⣺ �˳���Ϊ��ҳʹ�� end


include_once 'common.inc.php';


$pc_config = $_INPUT['pc'];


if((false!==stripos($_SERVER['HTTP_USER_AGENT'], 'android') || false!==stripos($_SERVER['HTTP_USER_AGENT'], 'iphone')) && empty($pc_config) )
{
    //wap��

    $pc_wap = 'wap/';

    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'index-page.tpl.htm');
}
else
{
    //Pc��

    $pc_wap = 'pc/';

    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'index-page.tpl.htm');

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

}


$tpl->assign('formal_is_hide', 1);  //��ʽ����������  1��ʾ 0����
$tpl->assign('wait_is_hide', 0);   //�����ڴ�         1��ʾ 0����



// iPhone�汾����
$tpl->assign('down_iPhone_app', 'http://app.yueus.com/dl_merchant_for_i.php');

// Android�汾
$tpl->assign('down_android_app', 'http://app.yueus.com/dl_merchant_for_a.php');


$tpl->output();


?>