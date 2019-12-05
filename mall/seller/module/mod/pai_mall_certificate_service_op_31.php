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

//模特认证数据插入
//数据获取


//插入成功后后续操作
$sex = (int)$_INPUT['sex'];
$weixin_qq = trim($_INPUT['weixin_qq']);
$height = (int)$_INPUT['height'];
$weight = (int)$_INPUT['weight'];
$bust = (int)$_INPUT['bust'];
$waist = (int)$_INPUT['waist'];
$hips = (int)$_INPUT['hips'];
$cup_type = $_INPUT['cup_type'];
$self_desc = trim($_INPUT['self_desc']);
$img = $_INPUT['yue_upload_group_1'];//一维数组形式
//数据校验
if(empty($weixin_qq))
{
    echo "<script>top.alert('请填入微信/qq');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($height))
{
    echo "<script>top.alert('请填入身高');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($weight))
{
    echo "<script>top.alert('请填入体重');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($bust))
{
    echo "<script>top.alert('请填入胸围');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($waist))
{
    echo "<script>top.alert('请填入腰围');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($hips))
{
    echo "<script>top.alert('请填入臀围');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($cup_type))
{
    echo "<script>top.alert('请填入CUP数');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}

//处理cup问题
$cup_type = $cup_type[0]."|".$cup_type[1];//暂时做链接处理

//处理img结构，模特作为作品
$certificate_common_type = pai_mall_load_config('certificate_common_type');//图片类型
foreach($certificate_common_type as $key => $value)
{
    if($value=="作品")
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