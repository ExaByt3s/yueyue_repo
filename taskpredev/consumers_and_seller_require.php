<?php
/** 
 * 
 * ���������̼Ҷ��ܿ�����ҳ�棬������ļ�
 * ��Բ
 * 2015-5-27
 * common
 */


// ===============  ���� > 0 ���̼ң���������ʾ��ͬ��ͷ��  ===============
$task_seller_obj = POCO::singleton('pai_task_seller_class');
$get_seller_info = $task_seller_obj->get_seller_info($yue_login_id);
if (count($get_seller_info) > 0) 
{
    include_once($file_dir. '/./webcontrol/top_nav.php');
}
else
{
    include_once($file_dir. '/./webcontrol/consumers_top_nav.php');
}
// =============== end ===============



// ===============  �����û���ݣ���ʾͷ���û���Ϣ�����������̼�  =============== 

if (count($get_seller_info) > 0) 
{
    $seller_id = $get_seller_info['user_id'];
    $tpl->assign('seller_id', $seller_id);
}
else
{
    $tpl->assign('seller_id', 0);
}

// ===============  �����û���ݣ���ʾͷ���û���Ϣ�����������̼� end  ============


?>