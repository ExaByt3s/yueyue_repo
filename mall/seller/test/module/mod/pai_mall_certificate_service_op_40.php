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

//摄影服务
//数据获取
$years = (int)$_INPUT['years'];
$often_equipment = trim($_INPUT['often_equipment']);
$zone_num = trim($_INPUT['zone_num']);
$society_num = $_INPUT['society_num'];
$self_desc = trim($_INPUT['self_desc']);
$order_income = (int)$_INPUT['order_income'];
$team = (int)$_INPUT['team'];

//摄影师图片处理：
$work_type_id = $_INPUT['work_type_id'];
//$work_name = $_INPUT['work_name'];
//$work_desc = $_INPUT['work_desc'];
$work_part = $_INPUT['word_part'];//用来记录图片块位置

//处理图片块结构
$work_type_id_count = count($work_type_id);
for($i=0;$i<$work_type_id_count;$i++)
{
    $author_content[$i]['work_type_id'] = (int)$work_type_id[$i];
    //$author_content[$i]['work_name'] = trim($work_name[$i]);
    //$author_content[$i]['work_desc'] = trim($work_desc[$i]);

    //处理图片结构
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
            $author_content[$i]['work_img'][$key]['img_url'] = $value;
        }
    }
}

//数据校验
if(empty($years))
{

    echo "<script>top.alert('请选择年限');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($often_equipment))
{

    echo "<script>top.alert('请填入常用器材');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

if(empty($zone_num))
{

    echo "<script>top.alert('请填入个人作品链接');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($society_num))
{

    echo "<script>top.alert('请填入社交账号');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($self_desc))
{

    echo "<script>top.alert('请填入个人介绍');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($order_income))
{

    echo "<script>top.alert('请选择月均收入');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($team))
{

    echo "<script>top.alert('请选择团队构成');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
//对社交账号进行字符串合拼处理。使用|分开
$society_num = implode("|",$society_num);
//对社交账号进行字符串合拼处理。使用|分开

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