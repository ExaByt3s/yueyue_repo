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


//��ѵʦ��֤
//���ݻ�ȡ
$teacher_type = (int)$_INPUT['teacher_type'];
$years = (int)$_INPUT['years'];
$course_special = trim($_INPUT['course_special']);
$can_learn = trim($_INPUT['can_learn']);
$society_num = trim($_INPUT['society_num']);
$class_way = (int)$_INPUT['class_way'];
$teacher_num = (int)$_INPUT['teacher_num'];
$student_num = (int)$_INPUT['student_num'];
$img = $_INPUT['yue_upload_group_1'];//һά������ʽ������


//����img�ṹ����Ӱ��ѵ��Ϊ��Ʒ
$certificate_common_type = pai_mall_load_config('certificate_common_type');//ͼƬ����
foreach($certificate_common_type as $key => $value)
{
if($value=="����")
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

if(empty($teacher_type))
{
echo "<script>top.alert('��ѡ��ʦ����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
echo "years empty";
exit();
}
if(empty($years))
{
echo "<script>top.alert('��ѡ������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
echo "years empty";
exit();
}
if(empty($course_special))
{
echo "<script>top.alert('������γ���ɫ';top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
echo "course_special empty";
exit();
}
if(empty($can_learn))
{
echo "<script>top.alert('��������ѧ������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}
if(empty($society_num))
{
echo "<script>top.alert('�����볣���˺�');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}
if(empty($class_way))
{
echo "<script>top.alert('��ѡ����ѵ��ʽ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}
if(empty($teacher_num))
{
echo "<script>top.alert('������ʦ������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}
if(empty($student_num))
{
echo "<script>top.alert('������ѧԱ����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}


$data_insert['service_type'] = $service_type;
$data_insert['teacher_type'] = $teacher_type;
$data_insert['years'] = $years;
$data_insert['course_special'] = $course_special;
$data_insert['can_learn'] = $can_learn;
$data_insert['society_num'] = $society_num;
$data_insert['class_way'] = $class_way;
$data_insert['teacher_num'] = $teacher_num;
$data_insert['student_num'] = $student_num;
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