<?php

/*
 *
 *
 * //������֤�첽����ģ��
 *
 *
 *
 *
 */


//��ױʦ��֤
//���ݻ�ȡ
$years = (int)$_INPUT['years'];
$has_place = (int)$_INPUT['has_place'];
$dresser_desc = trim($_INPUT['dresser_desc']);
$order_way = (int)$_INPUT['order_way'];
$team_num = (int)$_INPUT['team_num'];
$do_well = $_INPUT['do_well'];
$do_well_other = trim($_INPUT['do_well_other']);
$img = $_INPUT['yue_upload_group_1'];//һά������ʽ


//����img�ṹ����ױʦ��Ϊ��Ʒ
$certificate_common_type = pai_mall_load_config('certificate_common_type');//ͼƬ����
foreach($certificate_common_type as $key => $value)
{
    if($value=="��Ʒ")
    {
        $img_type = $key;
        break;
    }
}

foreach($img as $key => $value)
{
    //�ж�ͼƬ�Ƿ�undefined����Ϊ��
    preg_match($patt,$value,$matches);
    if(!empty($matches))
    {
        echo "<script>top.alert('ͼƬ�������������´�ͼ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    //�ж�ͼƬ�Ƿ�Ϊ��
    if($value=="")
    {
        echo "<script>top.alert('ͼƬ�������������´�ͼ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    $img_data_insert[$key]['img_url'] = $value;
    $img_data_insert[$key]['img_type'] = $img_type;

}

//����У��
if(empty($years))
{
    echo "<script>top.alert('��ѡ����������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($dresser_desc))
{
    echo "<script>top.alert('�����뻯ױʦ����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($order_way))
{
    echo "<script>top.alert('��ѡ��ӵ���ʽ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($team_num))
{
    echo "<script>top.alert('��ѡ���Ŷӹ�ģ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($do_well))
{
    echo "<script>top.alert('��ѡ���ó�ױ��');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
//��֯������������
$do_well_str = "";
foreach($do_well as $key => $value)
{
    if($key!=(count($do_well)-1))
    {
        $do_well_str = $do_well_str.$value.",";
    }
    else
    {
        $do_well_str = $do_well_str.$value;
    }
}



$data_insert['service_type'] = $service_type;
$data_insert['years'] = $years;
$data_insert['has_place'] = $has_place;
$data_insert['dresser_desc'] = $dresser_desc;
$data_insert['order_way'] = $order_way;
$data_insert['team_num'] = $team_num;
$data_insert['do_well'] = $do_well_str;
$data_insert['do_well_other'] = $do_well_other;
$data_insert['img'] = $img_data_insert;
$data_insert['user_id'] = $user_id;


//print_r($data_insert);
//exit;
//��������
$res = $pai_mall_certificate_service_obj->add_service_sq($data_insert);
if($res['status']<=0)
{
    echo "<script>top.alert('".$res['msg']." error');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
else
{
    echo "<script>top.window.location.href='../normal_certificate_check.php'</script>";
    exit();
}

//����ɹ����������
?>