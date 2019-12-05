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
$do_well = $_INPUT['do_well'];
$do_well_other = trim($_INPUT['do_well_other']);
//2015-8-20，兼容浏览器处理
if($_INPUT['editorValue']!="")
{
    $introduce = trim($_INPUT['editorValue']);
}
else
{
    $introduce = trim($_INPUT['introduce']);
}
//2015-8-20，兼容浏览器处理
$is_lead_activity = trim($_INPUT['is_lead_activity']);
$past_activity_content = trim($_INPUT['past_activity_content']);
$img = $_INPUT['yue_upload_group_1'];//一维数组形式




//数据校验
if(empty($do_well))
{

    echo "<script>top.alert('请勾选擅长的活动');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
/*if(empty($other_other_identifine))
{

echo "<script>top.alert('请其他内容');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}*/
if(empty($introduce))
{

    echo "<script>top.alert('请填写自我介绍');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($is_lead_activity))
{

    echo "<script>top.alert('请勾选以前是否组织过活动');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

//个人介绍过滤处理
    /*****2015-10-28校验图文编辑器内容图片渲染情况********/
    $loadingclass_res = strpos($introduce,"loadingclass");
    if($loadingclass_res>0)
    {

        echo "<script>top.alert('个人介绍有图片在加载，请稍后提交');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    /*****2015-10-284校验图文编辑器内容图片渲染情况********/

    //echo $_INPUT['default_data'][$key];
    //src链接校验
    $check = mall_src_link_check($introduce);

    if(!$check)
    {

        echo "<script>top.alert('个人介绍有图片，须为本站上传的图片');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }

    //src链接校验结束
    //转码处理
    //$tmp_introduce = html_entity_decode($_INPUT['default_data'][$key]);
    $tmp_introduce = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$introduce);
    //闭合标签处理
    $tmp_introduce = mall_closetags($tmp_introduce);
    //过滤处理
    $tmp_introduce = strip_tags($tmp_introduce,'<p><img><br><embed>');
    //对html字符串里进行属性过滤处理
    //$tmp_introduce                = preg_replace("/class=\"(.*)\"/isU","",$tmp_introduce);
    $tmp_introduce                = preg_replace("/style=\"(.*)\"/isU","",$tmp_introduce);
    $tmp_introduce                = preg_replace("/style=\'(.*)\'/isU","",$tmp_introduce);
    //$tmp_introduce                = preg_replace("/width=\"(\d+)\"/is","",$tmp_introduce);
    //$tmp_introduce                = preg_replace("/height=\"(\d+)\"/is","",$tmp_introduce);
    //$tmp_introduce                = preg_replace("/width=(\d+)/is","",$tmp_introduce);
    //$tmp_introduce                = preg_replace("/height=(\d+)/is","",$tmp_introduce);
    $tmp_introduce                = preg_replace("/align=center/is","",$tmp_introduce);

    $introduce = $tmp_introduce;
//个人介绍过滤处理结束


//处理img结构，影棚作为作品
$certificate_common_type = pai_mall_load_config('certificate_common_type');//图片类型
foreach($certificate_common_type as $key => $value)
{
    if($value=="活动图片")
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


//数据处理
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
$data_insert['do_well'] = $do_well_str;
$data_insert['do_well_other'] = $do_well_other;
$data_insert['introduce'] = $introduce;
$data_insert['is_lead_activity'] = $is_lead_activity;
$data_insert['past_activity_content'] = $past_activity_content;
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