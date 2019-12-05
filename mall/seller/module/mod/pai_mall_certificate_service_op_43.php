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

//其他认证
//数据获取
$other_identifine = $_INPUT['other_identifine'];
$other_other_identifine = trim($_INPUT['other_other_identifine']);
$other_self_desc = trim($_INPUT['other_self_desc']);
$other_job = trim($_INPUT['other_job']);
$other_service_desc = trim($_INPUT['other_service_desc']);

//数据处理
$other_identifine = implode(",",$other_identifine);


//数据校验
if(empty($other_identifine))
{

echo "<script>top.alert('请勾选标签');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}
/*if(empty($other_other_identifine))
{

echo "<script>top.alert('请其他内容');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}*/
if(empty($other_self_desc))
{

echo "<script>top.alert('请填写自我介绍');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}
if(empty($other_job))
{

echo "<script>top.alert('请填写身份职业');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}
if(empty($other_service_desc))
{

echo "<script>top.alert('请填入提供的服务');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
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

?>