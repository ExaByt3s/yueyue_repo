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


//美食达人
//数据获取
$diet_job = (int)$_INPUT['diet_job'];
$diet_years = (int)$_INPUT['diet_years'];
$diet_identification = (int)$_INPUT['diet_identification'];
$diet_max_forward = (int)$_INPUT['diet_max_forward'];
$diet_web_name = trim($_INPUT['diet_web_name']);
$diet_self_desc = trim($_INPUT['diet_self_desc']);
$diet_media_address = trim($_INPUT['diet_media_address']);
$img = $_INPUT['yue_upload_group_1'];//一维数组形式


//处理img结构，化妆师作为作品
$certificate_common_type = pai_mall_load_config('certificate_common_type');//图片类型
foreach($certificate_common_type as $key => $value)
{
    if($value=="凭证")
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
if(empty($diet_years))
{

    echo "<script>top.alert('请选择年限');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_job))
{

    echo "<script>top.alert('请选择职业');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_identification))
{

    echo "<script>top.alert('请选择被认证身份');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_max_forward))
{

    echo "<script>top.alert('请选择转发量');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_self_desc))
{

    echo "<script>top.alert('请填入介绍');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($diet_media_address))
{

    echo "<script>top.alert('请填入自媒体地址');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
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