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

//������֤
//���ݻ�ȡ
$other_identifine = $_INPUT['other_identifine'];
$other_other_identifine = trim($_INPUT['other_other_identifine']);
$other_self_desc = trim($_INPUT['other_self_desc']);
$other_job = trim($_INPUT['other_job']);
$other_service_desc = trim($_INPUT['other_service_desc']);

//���ݴ���
$other_identifine = implode(",",$other_identifine);


//����У��
if(empty($other_identifine))
{

echo "<script>top.alert('�빴ѡ��ǩ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}
/*if(empty($other_other_identifine))
{

echo "<script>top.alert('����������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}*/
if(empty($other_self_desc))
{

echo "<script>top.alert('����д���ҽ���');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}
if(empty($other_job))
{

echo "<script>top.alert('����д���ְҵ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}
if(empty($other_service_desc))
{

echo "<script>top.alert('�������ṩ�ķ���');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}

$data_insert['service_type'] = $service_type;
$data_insert['other_identifine'] = $other_identifine;
$data_insert['other_other_identifine'] = $other_other_identifine;
$data_insert['other_self_desc'] = $other_self_desc;
$data_insert['other_job'] = $other_job;
$data_insert['other_service_desc'] = $other_service_desc;
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

?>