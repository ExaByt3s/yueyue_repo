<?php

/*
 *
 *
 * //���»�����ӻ�ع�ҳ��
 *
 *
 *
 *
 */

include_once '../common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;
$goods_id = (int)$_INPUT["goods_id"];

//�ж�goods_id
if(empty($goods_id))
{

    echo "<script>top.alert('ȱ����ƷIDֵ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
//���ݻ�ȡ
$goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
$type_id = $goods_info['data']['goods_data']['type_id'];
$can_edit_review_arr = array(42);//����ӻع˵�����
if(!in_array($type_id,$can_edit_review_arr))
{
    //û�н����̼���֤��
    echo "<script>top.alert('����Ʒ���Ͳ�����ӻع�');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}



//2015-8-20���������������
if($_INPUT['editorValue']!="")
{
    $introduce = trim($_INPUT['editorValue']);
}
else
{
    $introduce = trim($_INPUT['introduce']);
}
//2015-8-20���������������

if(empty($introduce))
{

    echo "<script>top.alert('����д��ع�����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}



//��ع˽��ܹ��˴���
    /*****2015-10-28У��ͼ�ı༭������ͼƬ��Ⱦ���********/
    $loadingclass_res = strpos($introduce,"loadingclass");
    if($loadingclass_res>0)
    {

        echo "<script>top.alert('��ع�������ͼƬ�ڼ��أ����Ժ��ύ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    /*****2015-10-284У��ͼ�ı༭������ͼƬ��Ⱦ���********/

    //echo $_INPUT['default_data'][$key];
    //src����У��
    $check = mall_src_link_check($introduce);

    if(!$check)
    {

        echo "<script>top.alert('��ع����ݵ�ͼƬ����Ϊ��վ�ϴ���ͼƬ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }

    //src����У�����
    //ת�봦��
    //$tmp_introduce = html_entity_decode($_INPUT['default_data'][$key]);
    $tmp_introduce = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$introduce);
    //�պϱ�ǩ����
    $tmp_introduce = mall_closetags($tmp_introduce);
    //���˴���
    $tmp_introduce = strip_tags($tmp_introduce,'<p><img><br><embed>');
    //��html�ַ�����������Թ��˴���
    //$tmp_introduce                = preg_replace("/class=\"(.*)\"/isU","",$tmp_introduce);
    $tmp_introduce                = preg_replace("/style=\"(.*)\"/isU","",$tmp_introduce);
    $tmp_introduce                = preg_replace("/style=\'(.*)\'/isU","",$tmp_introduce);
    //$tmp_introduce                = preg_replace("/width=\"(\d+)\"/is","",$tmp_introduce);
    //$tmp_introduce                = preg_replace("/height=\"(\d+)\"/is","",$tmp_introduce);
    //$tmp_introduce                = preg_replace("/width=(\d+)/is","",$tmp_introduce);
    //$tmp_introduce                = preg_replace("/height=(\d+)/is","",$tmp_introduce);
    $tmp_introduce                = preg_replace("/align=center/is","",$tmp_introduce);

    $introduce = $tmp_introduce;
//��ع˽��ܹ��˴������

//����Ӧ��⴦��
$ret = $pai_mall_goods_obj->add_activity_review($goods_id,$user_id,$introduce);
if($ret["result"] > 0)
{
    echo "<script>top.window.layer.closeAll();top.window.location.href='../activity_list.php?show=2'</script>";
    exit();
}
else
{
    echo "<script>top.alert('���ʧ�ܣ�ԭ��".$ret["msg"]."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

//��Ӧ����
?>