<?php

/**
 * ��������
 *
 * 2015-11-12
 *
 * author  ����
 *
 */

include_once '../common.inc.php';
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$order_obj = POCO::singleton('pai_mall_order_class');

$goods_id = (int)$_INPUT["goods_id"];//�ID
$type_id = trim($_INPUT["type_id"]);//����ID


if(empty($yue_login_id))
{
    echo "<script>top.alert('û�е�¼');window.location.href='../normal_certificate_basic.php';</script>";
    exit();
}

$user_id = $yue_login_id;


$goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);

$now_goods_user_id = $goods_info['data']['goods_data']["user_id"];
if($user_id!=$now_goods_user_id)
{
    //û�н����̼���֤��
    echo "<script>alert('��ǰ��Ʒ�����ڴ˵�¼�û�');window.location.href='../normal_certificate_basic.php';</script>";
    exit;
}

$activity_title = $goods_info["data"]["default_data"]["titles"]["value"];
$join_list = $order_obj->get_order_list_of_paid_by_stage($goods_id,$type_id,false,"","0,99999");
$data = array();
foreach($join_list as $key => $value)
{
    $data[$key]["sequence_num"] = (int)$key+1;
    $data[$key]["buyer_user_name"] = $value["buyer_user_name"];//�û��ǳ�
    $data[$key]["buyer_user_cellphone"] = $value["buyer_user_cellphone"];//�û��ֻ���
    //$data[$key]["buyer_user_id"] = $value["buyer_user_id"];//�û�ID
    $data[$key]["prices_spec"] = $value["prices_spec"];//���
    $data[$key]["add_time"] = date("Y-m-d h:i",$value["add_time"]);//����ʱ��
    $data[$key]["quantity"] = $value["quantity"];//��������
    $data[$key]["description"] = $value["description"];//��ע
    //$data[$key]["goods_id"] = $goods_id;//��ע

}


$fileName = "����⣺".$activity_title."--�ID��".$goods_id;
//$headArr  = array("���","�û��ǳ�","�û��ֻ���","�û�ID","���","����ʱ��","��������","��ע","�ID");
$headArr  = array("���","�û��ǳ�","�û��ֻ���","���","����ʱ��","��������","��ע");
Excel_v2::start($headArr,$data,$fileName);
















?>