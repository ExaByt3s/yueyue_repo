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


//��ʳ����
//���ݻ�ȡ
$diet_job = (int)$_INPUT['diet_job'];
$diet_years = (int)$_INPUT['diet_years'];
$diet_identification = (int)$_INPUT['diet_identification'];
$diet_max_forward = (int)$_INPUT['diet_max_forward'];
$diet_web_name = trim($_INPUT['diet_web_name']);
$diet_self_desc = trim($_INPUT['diet_self_desc']);
$diet_media_address = trim($_INPUT['diet_media_address']);
$img = $_INPUT['yue_upload_group_1'];//һά������ʽ


//����img�ṹ����ױʦ��Ϊ��Ʒ
$certificate_common_type = pai_mall_load_config('certificate_common_type');//ͼƬ����
foreach($certificate_common_type as $key => $value)
{
    if($value=="ƾ֤")
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
if(empty($diet_years))
{

    echo "<script>top.alert('��ѡ������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_job))
{

    echo "<script>top.alert('��ѡ��ְҵ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_identification))
{

    echo "<script>top.alert('��ѡ����֤���');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_max_forward))
{

    echo "<script>top.alert('��ѡ��ת����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_self_desc))
{

    echo "<script>top.alert('���������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_media_address))
{

    echo "<script>top.alert('��������ý���ַ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

$data_insert['service_type'] = $service_type;
$data_insert['diet_job'] = $diet_job;
$data_insert['diet_years'] = $diet_years;
$data_insert['diet_identification'] = $diet_identification;
$data_insert['diet_max_forward'] = $diet_max_forward;
$data_insert['diet_web_name'] = $diet_web_name;
$data_insert['diet_self_desc'] = $diet_self_desc;
$data_insert['diet_media_address'] = $diet_media_address;
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
?>