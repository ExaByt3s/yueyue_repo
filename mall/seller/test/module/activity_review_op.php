<?php

/*
 *
 *
 * //更新或者添加活动回顾页面
 *
 *
 *
 *
 */

include_once '../common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;
$goods_id = (int)$_INPUT["goods_id"];

//判断goods_id
if(empty($goods_id))
{

    echo "<script>top.alert('缺少商品ID值');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
//数据获取
$goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
$type_id = $goods_info['data']['goods_data']['type_id'];
$can_edit_review_arr = array(42);//可添加回顾的类型
if(!in_array($type_id,$can_edit_review_arr))
{
    //没有进行商家认证的
    echo "<script>top.alert('该商品类型不能添加回顾');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}



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

if(empty($introduce))
{

    echo "<script>top.alert('请填写活动回顾内容');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}



//活动回顾介绍过滤处理
    /*****2015-10-28校验图文编辑器内容图片渲染情况********/
    $loadingclass_res = strpos($introduce,"loadingclass");
    if($loadingclass_res>0)
    {

        echo "<script>top.alert('活动回顾内容有图片在加载，请稍后提交');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    /*****2015-10-284校验图文编辑器内容图片渲染情况********/

    //echo $_INPUT['default_data'][$key];
    //src链接校验
    $check = mall_src_link_check($introduce);

    if(!$check)
    {

        echo "<script>top.alert('活动回顾内容的图片，须为本站上传的图片');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
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
//活动回顾介绍过滤处理结束

//做相应入库处理
$ret = $pai_mall_goods_obj->add_activity_review($goods_id,$user_id,$introduce);
if($ret["result"] > 0)
{
    echo "<script>top.window.layer.closeAll();top.window.location.href='../activity_list.php?show=2'</script>";
    exit();
}
else
{
    echo "<script>top.alert('添加失败，原因：".$ret["msg"]."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

//相应处理
?>