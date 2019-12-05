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


//化妆师认证
//数据获取
$years = (int)$_INPUT['years'];
$has_place = (int)$_INPUT['has_place'];
$dresser_desc = trim($_INPUT['dresser_desc']);
$order_way = (int)$_INPUT['order_way'];
$team_num = (int)$_INPUT['team_num'];
$do_well = $_INPUT['do_well'];
$do_well_other = trim($_INPUT['do_well_other']);
$img = $_INPUT['yue_upload_group_1'];//一维数组形式


//处理img结构，化妆师作为作品
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

//数据校验
if(empty($years))
{
    echo "<script>top.alert('请选择入行年限');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($dresser_desc))
{
    echo "<script>top.alert('请填入化妆师介绍');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($order_way))
{
    echo "<script>top.alert('请选择接单方式');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($team_num))
{
    echo "<script>top.alert('请选择团队规模');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
if(empty($do_well))
{
    echo "<script>top.alert('请选择擅长妆容');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";

    exit();
}
//组织处理类型数据
$do_well_str = "";
foreach($do_well as $key => $value)
{
    if($key!=(count($do_well)-1))
    {
        $do_well_str = $do_well_str.$value.",";
    }
    else
    {
        $do_well_str = $do_well_str.$value;
    }
}



$data_insert['service_type'] = $service_type;
$data_insert['years'] = $years;
$data_insert['has_place'] = $has_place;
$data_insert['dresser_desc'] = $dresser_desc;
$data_insert['order_way'] = $order_way;
$data_insert['team_num'] = $team_num;
$data_insert['do_well'] = $do_well_str;
$data_insert['do_well_other'] = $do_well_other;
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

//插入成功后后续操作
?>