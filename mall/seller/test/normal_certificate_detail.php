<?php

/**
 * ��֤ѡ����ϸҳ����������ҵ����ͬģ�壩
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
$pai_mall_certificate_basic_obj = POCO::singleton('pai_mall_certificate_basic_class');
$pc_wap = 'pc/';

$type     = trim($_INPUT['type']);
$user_id  = intval($yue_login_id);
$type_array = array("company","person");
if(empty($type) || !in_array($type,$type_array))
{
    $type = "person";
}

if($user_id <1)
{

    $r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $r_url = urlencode($r_url);
    echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=".$r_url."';</script>";
    exit;
    //echo "not login error";
    //exit();

    //echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=http%3a%2f%2fyp.yueus.com%2fmall%2fseller%2ftest%2fnormal_certificate_detail.php%3ftype%3d{$type}';</script>";
    //exit;
}
//У���û���ɫ,���̼ұ���
$user_id = $yue_login_id;
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_name=$seller_info['seller_data']['name'];
if(!empty($seller_name) && $seller_info['seller_data']['status']==1)
{
    //�����̼���֤��
    header("location:./normal_certificate_basic.php");
    exit;
}


$ret = $pai_mall_certificate_basic_obj->check_can_add($user_id);

if(isset($ret['status']) && $ret['status'] ==0)//�û����������
{
    echo "<script type='text/javascript'>window.alert('�̼���֤������֤��');location.href='normal_certificate_choose.php';</script>";
    exit;
}
elseif(isset($ret['status']) && $ret['status'] ==1)//�Ѿ���֤ͨ��
{
    echo "<script type='text/javascript'>window.alert('�̼���֤����֤');location.href='normal_certificate_choose.php';</script>";
    exit;
}

if($type=="company")
{

     //��ҵ��֤
    //�����ض�����
    $certificate_company_bank = pai_mall_load_config('certificate_company_bank');//����������
    if(!is_array($certificate_company_bank)) $certificate_company_bank = array();
    //�������д���
    $bank_ret = array();
    $i = 0;
    foreach($certificate_company_bank as $key=>$val)
    {
        $bank_ret[$i]['company_bank_id'] = $key;
        $bank_ret[$i]['name']            = $val;
        $i++;
    }

    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'normal_certificate_detail_company.tpl.htm');
    $tpl->assign('bank_ret',$bank_ret);
}
else
{
    //������֤
    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'normal_certificate_detail_person.tpl.htm');
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

$tpl->assign('user_id',$user_id);
$tpl->assign('isphone','{10}');

$tpl->output();

?>