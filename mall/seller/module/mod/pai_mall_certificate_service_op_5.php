<?php

/*
 *
 *
 * //服务认证异步操作模块
 *
 *
 *
 *
 */


//培训师认证
//数据获取
$teacher_type = (int)$_INPUT['teacher_type'];
$years = (int)$_INPUT['years'];
$course_special = trim($_INPUT['course_special']);
$can_learn = trim($_INPUT['can_learn']);
$society_num = trim($_INPUT['society_num']);
$class_way = (int)$_INPUT['class_way'];
$teacher_num = (int)$_INPUT['teacher_num'];
$student_num = (int)$_INPUT['student_num'];
$img = $_INPUT['yue_upload_group_1'];//一维数组形式，花絮


//处理img结构，摄影培训作为作品
$certificate_common_type = pai_mall_load_config('certificate_common_type');//图片类型
foreach($certificate_common_type as $key => $value)
{
if($value=="花絮")
{
$img_type = $key;
break;
}
}

foreach($img as $key => $value)
{
//判断图片是否undefined或者为空
preg_match($patt,$value,$matches);
if(!empty($matches))
{
echo "<script>top.alert('图片数据有误，请重新传图');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}
//判断图片是否为空
if($value=="")
{
echo "<script>top.alert('图片数据有误，请重新传图');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}
$img_data_insert[$key]['img_url'] = $value;
$img_data_insert[$key]['img_type'] = $img_type;

}


//数据校验

if(empty($teacher_type))
{
echo "<script>top.alert('请选择讲师类型');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
echo "years empty";
exit();
}
if(empty($years))
{
echo "<script>top.alert('请选择年限');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
echo "years empty";
exit();
}
if(empty($course_special))
{
echo "<script>top.alert('请填入课程特色';top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
echo "course_special empty";
exit();
}
if(empty($can_learn))
{
echo "<script>top.alert('请填入能学的内容');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}
if(empty($society_num))
{
echo "<script>top.alert('请填入常用账号');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}
if(empty($class_way))
{
echo "<script>top.alert('请选择培训方式');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}
if(empty($teacher_num))
{
echo "<script>top.alert('请填入师资人数');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

exit();
}
if(empty($student_num))
{
echo "<script>top.alert('请填入学员人数');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

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

//插入数据
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


//插入成功后后续操作

?>