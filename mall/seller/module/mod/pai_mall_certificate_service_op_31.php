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

//ģ����֤���ݲ���
//���ݻ�ȡ


//����ɹ����������
$sex = (int)$_INPUT['sex'];
$weixin_qq = trim($_INPUT['weixin_qq']);
$height = (int)$_INPUT['height'];
$weight = (int)$_INPUT['weight'];
$bust = (int)$_INPUT['bust'];
$waist = (int)$_INPUT['waist'];
$hips = (int)$_INPUT['hips'];
$cup_type = $_INPUT['cup_type'];
$self_desc = trim($_INPUT['self_desc']);
$img = $_INPUT['yue_upload_group_1'];//һά������ʽ
//����У��
if(empty($weixin_qq))
{
    echo "<script>top.alert('������΢��/qq');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($height))
{
    echo "<script>top.alert('���������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($weight))
{
    echo "<script>top.alert('����������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($bust))
{
    echo "<script>top.alert('��������Χ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($waist))
{
    echo "<script>top.alert('��������Χ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($hips))
{
    echo "<script>top.alert('��������Χ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($cup_type))
{
    echo "<script>top.alert('������CUP��');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}

//����cup����
$cup_type = $cup_type[0]."|".$cup_type[1];//��ʱ�����Ӵ���

//����img�ṹ��ģ����Ϊ��Ʒ
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



$data_insert['service_type'] = $service_type;
$data_insert['sex'] = $sex;
$data_insert['weixin_qq'] = $weixin_qq;
$data_insert['height'] = $height;
$data_insert['weight'] = $weight;
$data_insert['bust'] = $bust;
$data_insert['waist'] = $waist;
$data_insert['hips'] = $hips;
$data_insert['cup_type'] = $cup_type;
$data_insert['self_desc'] = $self_desc;
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