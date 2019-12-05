<?php

/**
 * �������������
 *
 * 2015-10-30
 *
 * author  ����
 *
 */
include_once 'common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$order_obj = POCO::singleton('pai_mall_order_class');
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
    echo "<script>alert('����Ʒ����û�б�������');window.location.href='./normal_certificate_basic.php';</script>";
    exit;
}

$now_goods_user_id = $goods_info['data']['goods_data']["user_id"];
if($user_id!=$now_goods_user_id)
{
    //û�н����̼���֤��
    echo "<script>alert('��ǰ��Ʒ�����ڴ˵�¼�û�');window.location.href='./normal_certificate_basic.php';</script>";
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


//ѭ������������
$goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
$tmp_price_list = $goods_info['data']['prices_data'];
//print_r($goods_info);
//print_r($tmp_price_list);
$i=0;

foreach($tmp_price_list as $key => $value)
{
    $new_price_list[$i] = $value;
    //��ȡ���Ӧ��������
    $tmp_join_list = "";
    $tmp_join_list = $order_obj->get_order_list_of_paid_by_stage($goods_id, $value["type_id"],false,"","0,99999");
    //����ǰ���������
    foreach($tmp_join_list as $k => $v)
    {
        $tmp_join_list[$k]["sequence_num"] = (int)$k+1;
    }
    $new_price_list[$i]["join_list"] = $tmp_join_list;
    $new_price_list[$i]["data_mark"] = $i+1;
    //�Ѹ�������
    $section_info = $order_obj->sum_order_quantity_of_paid_by_stage($goods_id, $value["type_id"]);
    $new_price_list[$i]["paid_num"] = (int)$section_info["paid_num"];

    $i++;
}

//print_r($new_price_list);



//to do
//������ʾ
$hide_system_msg = 1;

$page_title = "��������";
//��ظ�ֵ����

$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'activity_join_list.tpl.htm');


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

$tpl->assign("goods_id",$goods_id);
$tpl->assign("new_price_list",$new_price_list);
$tpl->assign("page_title",$page_title);
$tpl->assign("activity_type_name",$activity_type_name);
$tpl->assign("activity_title",$activity_title);
$tpl->assign("hide_system_msg",$hide_system_msg);
$tpl->assign("system_msg",$system_msg);
$tpl->output();

?>