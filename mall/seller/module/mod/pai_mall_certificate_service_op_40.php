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

//��Ӱ����
//���ݻ�ȡ
$years = (int)$_INPUT['years'];
$often_equipment = trim($_INPUT['often_equipment']);
$zone_num = trim($_INPUT['zone_num']);
$society_num = $_INPUT['society_num'];
$self_desc = trim($_INPUT['self_desc']);
$order_income = (int)$_INPUT['order_income'];
$team = (int)$_INPUT['team'];

//��ӰʦͼƬ����
$work_type_id = $_INPUT['work_type_id'];
//$work_name = $_INPUT['work_name'];
//$work_desc = $_INPUT['work_desc'];
$work_part = $_INPUT['word_part'];//������¼ͼƬ��λ��

//����ͼƬ��ṹ
$work_type_id_count = count($work_type_id);
for($i=0;$i<$work_type_id_count;$i++)
{
    $author_content[$i]['work_type_id'] = (int)$work_type_id[$i];
    //$author_content[$i]['work_name'] = trim($work_name[$i]);
    //$author_content[$i]['work_desc'] = trim($work_desc[$i]);

    //����ͼƬ�ṹ
    $tmp_img_mark = $work_part[$i];
    $tmp_img = $_INPUT['yue_upload_group_'.$tmp_img_mark];
    if(empty($tmp_img))
    {

        $author_content[$i]['work_img'] = array();
    }
    else
    {
        foreach($tmp_img as $key => $value)
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
            $author_content[$i]['work_img'][$key]['img_url'] = $value;
        }
    }
}

//����У��
if(empty($years))
{

    echo "<script>top.alert('��ѡ������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($often_equipment))
{

    echo "<script>top.alert('�����볣������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

if(empty($zone_num))
{

    echo "<script>top.alert('�����������Ʒ����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($society_num))
{

    echo "<script>top.alert('�������罻�˺�');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($self_desc))
{

    echo "<script>top.alert('��������˽���');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($order_income))
{

    echo "<script>top.alert('��ѡ���¾�����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($team))
{

    echo "<script>top.alert('��ѡ���Ŷӹ���');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
//���罻�˺Ž����ַ�����ƴ����ʹ��|�ֿ�
$society_num = implode("|",$society_num);
//���罻�˺Ž����ַ�����ƴ����ʹ��|�ֿ�

$data_insert['service_type'] = $service_type;
$data_insert['years'] = $years;
$data_insert['often_equipment'] = $often_equipment;
$data_insert['zone_num'] = $zone_num;
$data_insert['society_num'] = $society_num;
$data_insert['self_desc'] = $self_desc;
$data_insert['order_income'] = $order_income;
$data_insert['team'] = $team;
$data_insert['author_content'] = $author_content;
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