<?php

/**
 * ��ع˷���
 *
 * 2015-10-30
 *
 * author  ����
 *
 */
include_once 'common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;


//��ȡ�ID
$goods_id = (int)$_INPUT["goods_id"];
if(empty($goods_id))
{
    //û�н����̼���֤��
    echo "<script>alert('ȱ����ƷID');window.location.href='./normal_certificate_basic.php';</script>";
    exit;
}

$goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
$type_id = $goods_info['data']['goods_data']['type_id'];
$can_edit_review_arr = array(42);//����ӻع˵�����
if(!in_array($type_id,$can_edit_review_arr))
{
    //û�н����̼���֤��
    echo "<script>alert('����Ʒ���Ͳ�����ӻع�');window.location.href='./normal_certificate_basic.php';</script>";
    exit;
}

//print_r($goods_info);
//��ȡ��Ʒ������������ݴ���
$activity_title = $goods_info["data"]["default_data"]["titles"]["value"];
//��ȡ��Ʒ��������
$activity_type_name = "";
$activity_type_arr = $goods_info["data"]["system_data"]["39059724f73a9969845dfe4146c5660e"]["child_data"];
$activity_type_value = $goods_info["data"]["system_data"]["39059724f73a9969845dfe4146c5660e"]["value"];
foreach($activity_type_arr as $k => $v)
{
    //�ҵ�ѡ�е�������
    if($v["key"]==$activity_type_value)
    {
        $activity_type_name = $v["name"];
        //�ж��Ƿ����ӷ���
        if(!empty($v["child_data"]))
        {
            foreach($v["child_data"] as $key => $value)
            {
                if($value["is_select"]==1)
                {
                    $activity_type_name = $activity_type_name."-".$value["name"];
                }
            }

        }
    }
}






//��ȡ��ع�����
$review_content = $pai_mall_goods_obj->get_activity_review($goods_id);
if(empty($review_content))
{
    $action_name = "����";
}
else
{
    $action_name = "�༭";
}

$introduce = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$review_content["content"]);


$hide_system_msg = 1;

$page_title = $action_name."�û�ع�";
//��ظ�ֵ����

$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'activity_review.tpl.htm');

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

$tpl->assign("page_title",$page_title);
$tpl->assign("activity_type_name",$activity_type_name);
$tpl->assign("activity_title",$activity_title);
$tpl->assign("introduce",$introduce);
$tpl->assign("hide_system_msg",$hide_system_msg);
$tpl->assign("goods_id",$goods_id);

$tpl->assign("system_msg",$system_msg);

$tpl->output();

?>