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


//Ӱ������
//��ȡ����
$studio_desc = trim($_INPUT['studio_desc']);
$location_id = (int)$_INPUT['location_id'];
$studio_place = trim($_INPUT['studio_place']);//��ַ
$studio_area_input = trim($_INPUT['studio_area_input']);//���
$img = $_INPUT['yue_upload_group_1'];//һά������ʽ


//����img�ṹ��Ӱ����Ϊ��Ʒ
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
if(empty($studio_desc))
{
    echo "<script>top.alert('�����볡�ؽ���');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($location_id))
{
    echo "<script>top.alert('��ѡ�����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($studio_place))
{
    echo "<script>top.alert('�����볡�ص�ַ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

if(empty($studio_area_input))
{
    echo "<script>top.alert('�����볡�����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}





//���ݲ���
$data_insert['service_type'] = $service_type;
$data_insert['studio_desc'] = $studio_desc;
$data_insert['location_id'] = $location_id;
$data_insert['studio_place'] = $studio_place;
$data_insert['studio_area_input'] = $studio_area_input;
$data_insert['img'] = $img_data_insert;
$data_insert['user_id'] = $user_id;


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