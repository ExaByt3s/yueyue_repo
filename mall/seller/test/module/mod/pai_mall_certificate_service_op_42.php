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

//������֤
//���ݻ�ȡ
$do_well = $_INPUT['do_well'];
$do_well_other = trim($_INPUT['do_well_other']);
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
$is_lead_activity = trim($_INPUT['is_lead_activity']);
$past_activity_content = trim($_INPUT['past_activity_content']);
$img = $_INPUT['yue_upload_group_1'];//һά������ʽ




//����У��
if(empty($do_well))
{

    echo "<script>top.alert('�빴ѡ�ó��Ļ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
/*if(empty($other_other_identifine))
{

echo "<script>top.alert('����������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
exit();
}*/
if(empty($introduce))
{

    echo "<script>top.alert('����д���ҽ���');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
if(empty($is_lead_activity))
{

    echo "<script>top.alert('�빴ѡ��ǰ�Ƿ���֯���');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

//���˽��ܹ��˴���
    /*****2015-10-28У��ͼ�ı༭������ͼƬ��Ⱦ���********/
    $loadingclass_res = strpos($introduce,"loadingclass");
    if($loadingclass_res>0)
    {

        echo "<script>top.alert('���˽�����ͼƬ�ڼ��أ����Ժ��ύ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    /*****2015-10-284У��ͼ�ı༭������ͼƬ��Ⱦ���********/

    //echo $_INPUT['default_data'][$key];
    //src����У��
    $check = mall_src_link_check($introduce);

    if(!$check)
    {

        echo "<script>top.alert('���˽�����ͼƬ����Ϊ��վ�ϴ���ͼƬ');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
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
//���˽��ܹ��˴������


//����img�ṹ��Ӱ����Ϊ��Ʒ
$certificate_common_type = pai_mall_load_config('certificate_common_type');//ͼƬ����
foreach($certificate_common_type as $key => $value)
{
    if($value=="�ͼƬ")
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


//���ݴ���
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

?>