<?php

/**
 * ������֤����ҳ
 *
 * 2015-6-16
 *
 * author  ����
 *
 */
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once '../common.inc.php';
if($yue_login_id==134207)
{
    //���û����⴦��ú�ɾ��
    //��ʱ��־  http://yp.yueus.com/logs/201509/23_goods_op.txt
    pai_log_class::add_log(array(), 'goods_op', 'goods_op');
}


$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$pai_mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');


/*if($yue_login_id==100004)
{
    var_dump($_POST);
    $default_data = $_INPUT['default_data']['content'];
    $editorValue = $_INPUT['editorValue'];
    echo "<script>top.alert('".$default_data."');top.alert('".$editorValue."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit("����");
}*/

/*******��ʳ�������⴦��2015-10-20********/
$update_detail = (int)$_INPUT['update_detail'];
/*******��ʳ�������⴦��2015-10-20********/
$type_id = (int)$_INPUT['type_id'];//ģ��Ҫ����
//$store_id = (int)$_INPUT['store_id'];//ģ��Ҫ����
$goods_id = (int)$_INPUT['goods_id'];//�༭ʹ��
$type_id_array = array(3,5,12,31,40,41,43);

if(empty($yue_login_id))
{
    echo "<script>top.alert('û�е�¼');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
$user_id = $yue_login_id;

//��ȡ�û���Ϣ
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_store_id=$seller_info['seller_data']['company'][0]['store'][0]['store_id'];
//echo "store_id {$seller_store_id}";




if(!in_array($type_id,$type_id_array))
{
    echo "<script>top.alert('type_id ����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
$action = trim($_INPUT['action']);//����
$action_array = array("add","edit","money_edit","preview");
if(empty($action) || !in_array($action,$action_array))
{
    $action = "add";
}
//�����������

//exit();
//ѭ������У�鴦��
$data_error = "";
if(!empty($_INPUT['default_data']))
{
    //2015-8-20���������������
    if($_INPUT['editorValue']!="")
    {
        $_INPUT['default_data']['content'] = trim($_INPUT['editorValue']);
    }
    //2015-8-20���������������
    foreach($_INPUT['default_data'] as $key => $value)
    {
        if(empty($value))
        {
            //��Ӱ����û��ͼ������
            if($key=="content")
            {
                if($type_id==40)
                {
                    continue;
                }
            }
            //��Ӱ����û��ͼ������

            $data_error = $pai_mall_goods_type_attribute_obj->get_name_by_key($key);
            $data_error_tips = "data_error";
            break;
        }
        else
        {
            $_INPUT['default_data'][$key] = trim($value);
            //��ͼ�ı༭�����Ĺ��˴���2015-7-6
            if($key=="content")
            {
                /*****2015-8-24У��ͼ�ı༭������ͼƬ��Ⱦ���********/
                $loadingclass_res = strpos($_INPUT['default_data'][$key],"loadingclass");
                if($loadingclass_res>0)
                {
                    $data_error = "����������ͼƬ�ڼ��أ�";
                    $data_error_tips = "data_error";
                }
                /*****2015-8-24У��ͼ�ı༭������ͼƬ��Ⱦ���********/

                //echo $_INPUT['default_data'][$key];
                //src����У��
                $check = mall_src_link_check($_INPUT['default_data'][$key]);

                if(!$check)
                {
                    $data_error = "��������ͼƬ����Ϊ��վ�ϴ���ͼƬ��";
                    $data_error_tips = "data_error";
                }

                //src����У�����

                //ת�봦��
                //$tmp_content = html_entity_decode($_INPUT['default_data'][$key]);
                $tmp_content = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$_INPUT['default_data'][$key]);
                //�պϱ�ǩ����
                $tmp_content = mall_closetags($tmp_content);
                //���˴���
                $tmp_content = strip_tags($tmp_content,'<p><img><br><embed>');
                //��html�ַ�����������Թ��˴���
                //$tmp_content                = preg_replace("/class=\"(.*)\"/isU","",$tmp_content);
                $tmp_content                = preg_replace("/style=\"(.*)\"/isU","",$tmp_content);
                $tmp_content                = preg_replace("/style=\'(.*)\'/isU","",$tmp_content);
                //$tmp_content                = preg_replace("/width=\"(\d+)\"/is","",$tmp_content);
                //$tmp_content                = preg_replace("/height=\"(\d+)\"/is","",$tmp_content);
                //$tmp_content                = preg_replace("/width=(\d+)/is","",$tmp_content);
                //$tmp_content                = preg_replace("/height=(\d+)/is","",$tmp_content);
                $tmp_content                = preg_replace("/align=center/is","",$tmp_content);

                $_INPUT['default_data'][$key] = $tmp_content;

            }
            else if($key=="prices")
            {
                //�Լ۸���в�ȡ������2015-9-15
                $_INPUT['default_data'][$key] = trim($_INPUT['default_data'][$key]);
            }
        }
    }
}


/******2015-9-25ȥ����ʳ�˵�ƴ�Ӵ���******/
//$many_data_system_data_array = array("077e29b11be80ab57e1a2ecabb7da330");//����������,�������ַ�������������ʳ���˵Ĳ˵�
/******2015-9-25ȥ����ʳ�˵�ƴ�Ӵ���******/

$can_empty_system_data_array = array(
    "7cbbc409ec990f19c78c75bd1e06f215",
    "2723d092b63885e0d7c260cc007e8b9d",
    "5f93f983524def3dca464469d2cf9f3e",
    "ed3d2c21991e3bef5e069713af9fa6ca",
    "fb7b9ffa5462084c5f4e7e85a093e6d7",
    "16a5cdae362b8d27a1d8f8c7b78b4330",
    "5737c6ec2e0716f3d8a7a5c4e0de0d9a",
    "c058f544c737782deacefa532d9add4c",
    "072b030ba126b2f4b2374f342be9ed44",
    "e7b24b112a44fdd9ee93bdf998c6ca0e",
    "52720e003547c70561bf5e03b95aa99f",
    "2a38a4a9316c49e5a833517c45d31070",
    "7647966b7343c29048673252e490f736",
    "caf1a3dfb505ffed0d024130f58c5cfa",
    "fc490ca45c00b1249bbe3554a4fdf6fb",
    "8f121ce07d74717e0b1f21d122e04521"


);
if(!empty($_INPUT['system_data']))
{
    foreach($_INPUT['system_data'] as $key => $value)
    {
        if(empty($value))
        {
            //����ѡ����
            if(in_array($key,$can_empty_system_data_array))
            {
                //�������񣬹�ѡ����ѡ����������ж�
                if($key=="fb7b9ffa5462084c5f4e7e85a093e6d7")
                {
                    if($_INPUT['system_data']['07cdfd23373b17c6b337251c22b7ea57']=="6c524f9d5d7027454a783c841250ba71")//��ʾѡ������
                    {
                        $data_error = "����ѡ��";
                        $data_error_tips = "data_error";
                        break;
                    }
                }

                continue;

            }
            else
            {
                $data_error = $pai_mall_goods_type_attribute_obj->get_name_by_md5_key($key);
                $data_error_tips = "data_error";
                break;
            }



        }
        else
        {
            /******2015-9-25ȥ����ʳ�˵�ƴ�Ӵ���******/
            /*if(is_array($value))
            {
                //��ʳ���˵Ĳ˵�ƴ�Ӵ���
                if(in_array($key,$many_data_system_data_array))
                {
                    $tmp_value = implode("|",$value);
                    $value = $tmp_value;
                }

                $_INPUT['system_data'][$key] = $value;
            }
            else
            {
                $_INPUT['system_data'][$key] = trim($value);
            }*/
            /******2015-9-25ȥ����ʳ�˵�ƴ�Ӵ���******/
            if(is_array($value))
            {
                $_INPUT['system_data'][$key] = $value;
            }
            else
            {
                $_INPUT['system_data'][$key] = trim($value);
            }

        }
    }
}

$prices_mark = 0;//��Ǽ۸�������� 2015-7-14
if(!empty($_INPUT['prices_de']))
{
    foreach($_INPUT['prices_de'] as $key => $value)
    {
        $_INPUT['prices_de'][$key] = trim($value);
        if(!empty($value))
        {
            $prices_mark = 1;
        }

        //���⴦��Լ���ײͼ۸�-2015-10-13
        if($key==312)//�������
        {
            if($value=="")
            {
                unset($_INPUT["system_data"]["3fe94a002317b5f9259f82690aeea4cd"]);
            }
        }
        //���⴦��Լ���ײͼ۸�-2015-10-13
    }
}
else
{
    $prices_mark = 1;
}

//���۸�������һ���Ϊ�� 2015-7-14
if($prices_mark<1)
{
    $data_error = "�۸�";
    $data_error_tips = "data_error";
}

//Լ��ʳ�ײ͵Ĳ�����⴦��2015-9-24
if($type_id==41)
{
    //ʹ��|@|�����ײ�������������
    $prices_diy_tmp = $_INPUT['prices_diy'];
    foreach($prices_diy_tmp as $key => $value)
    {
        $prices_diy_tmp[$key]["name"] = $value["name_v1"]."��".$value["name_v2"]."�ˣ�";//��Լ�����
    }
    $_INPUT['prices_diy'] = $prices_diy_tmp;
}


//Լ��ʳ�ײ͵Ĳ�����⴦��2015-9-24




//����������
unset($_INPUT['goods_id']);
$op_data = $_INPUT;
//����store_id
$op_data['store_id'] = $seller_store_id;

//ͼƬ
if($type_id==41 || $type_id==40)
{
    //��ʳ���˵���ͼƬ����
    $guide_img = $_INPUT['yue_upload_group_2'];
    $guide_img_data = array();
    $patt = "/undefined/";
    foreach($guide_img as $val)
    {
        //�ж��Ƿ�ͼƬ����
        preg_match($patt,$val,$matches);
        if(!empty($matches))
        {
            $data_error = "ͼƬ";
            $data_error_tips = "data_error";
            break;
        }
        //�ж�ͼƬ�Ƿ�Ϊ��
        if($val=="")
        {
            $data_error = "ͼƬ";
            $data_error_tips = "data_error";
            break;
        }

        $guide_img_data[] = $val;
    }
    $guide_img_data_str = implode(",",$guide_img_data);
    if($type_id==40)//��Ӱ������ͼ
    {
        $op_data["system_data"]['c3e878e27f52e2a57ace4d9a76fd9acf'] = $guide_img_data_str;
        //���⴦����Ӱ����,��ͼ���ݴ���ͼ�ı༭�ֶ�
        $img_content = "<p>";
        foreach($guide_img_data as $k => $v)
        {
            $v = yueyue_resize_act_img_url($v,'640');
            $img_content .= '<img src="'.$v.'" _src="'.$v.'">';
        }
        $img_content .="</p>";
        $op_data["default_data"]['content'] = $img_content;


    }
    else if($type_id==41)//��ʳ���˵���ͼ
    {
        $op_data["system_data"]['e56954b4f6347e897f954495eab16a88'] = $guide_img_data_str;
    }


}





$img = $_INPUT['yue_upload_group_1'];
$img_data = array();
$patt = "/undefined/";
foreach($img as $val)
{
    //�ж��Ƿ�ͼƬ����
    preg_match($patt,$val,$matches);
    if(!empty($matches))
    {
        $data_error = "ͼƬ";
        $data_error_tips = "data_error";
        break;
    }
    //�ж�ͼƬ�Ƿ�Ϊ��
    if($val=="")
    {
        $data_error = "ͼƬ";
        $data_error_tips = "data_error";
        break;
    }

    $img_data[] = array('img_url'=>$val);
}
$op_data['img'] = $img_data;



if(!empty($data_error_tips))
{
    if($data_error=="����������ͼƬ�ڼ��أ�" || $data_error=="��������ͼƬ����Ϊ��վ�ϴ���ͼƬ��")
    {
        echo "<script>top.alert('".$data_error."���������������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    }
    else
    {
        echo "<script>top.alert('".$data_error."������������ȷ��д�������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    }
    exit();
}


//���⴦����ʱ��α������ڵ�ǰʱ��
if($op_data["system_data"]['072b030ba126b2f4b2374f342be9ed44'])
{
    $post_time = $op_data["system_data"]['072b030ba126b2f4b2374f342be9ed44'];
    $post_time = strtotime($post_time);
    $now_time = time()-86400;
    if($post_time<=$now_time)
    {
        echo "<script>top.alert('����ʱ�䲻��С������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
}

//���⴦��

//print_r($op_data);
//exit();

if($action=="add")
{
    //��������
    $res = $pai_mall_goods_obj->user_add_goods($op_data,$user_id);
    if($res['result'] < 1)
    {
        echo "<script>top.alert('".$res['message']."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    else
    {
        //echo "insert success";
        echo "<script>top.window.layer.closeAll();top.window.location.href='../goods_list.php?show=0'</script>";
        exit();
    }
}
else if($action=="edit")
{
    //��������
    /*******��ʳ�������⴦��2015-10-20********/
    if($type_id==41 && $update_detail==1)
    {

        //ֻ���¹涨�õ��ֶ�
        $op_data = "";
        $op_data["system_data"]['f7664060cc52bc6f3d620bcedc94a4b6'] = trim($_INPUT["system_data"]['f7664060cc52bc6f3d620bcedc94a4b6']);
        $res = $pai_mall_goods_obj->user_update_goods_for_detail($goods_id,$op_data,$user_id);

    }
    else
    {
        $res = $pai_mall_goods_obj->user_update_goods($goods_id,$op_data,$user_id);
    }
    /*******��ʳ�������⴦��2015-10-20********/

    if($res['result'] < 1)
    {
        echo "<script>top.alert('".$res['message']."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    else
    {
        //echo "insert success";
        echo "<script>top.window.layer.closeAll();top.window.location.href='../goods_list.php?show=0'</script>";
        exit();
    }

}
else if($action=="money_edit")
{
    $res = $pai_mall_goods_obj->user_update_goods_prices($goods_id,$op_data,$user_id);
    if($res['result'] < 1)
    {
        echo "<script>top.alert('".$res['message']."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    else
    {
        //echo "insert success";
        //������Ʒԭ��״̬ȷ����תλ��
        $goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
        $status = $goods_info['data']['goods_data']['status'];
        $is_show = $goods_info['data']['goods_data']['is_show'];
        if($status==1)
        {
            if($is_show==1)
            {
                $show = 1;
            }
            else
            {
                $show = 2;
            }
        }
        else
        {
            $show = 0;
        }


        echo "<script>top.window.layer.closeAll();top.window.location.href='../goods_list.php?show=".$show."'</script>";
        exit();
    }
}
else if($action=="preview")
{

    $time_mark_value = date("Ymdhis",time());
    $op_data['cache_id'] = $user_id.$time_mark_value;
    $ret = $pai_mall_goods_obj->set_goods_data_for_temp($op_data);
    if($ret)
    {
        //���ɶ�ά��ͼƬ
        $text = TASK_PROJECT_ROOT."/preview_middle_jump.php?cache_id=".$op_data['cache_id']."&type=service";
        $img = pai_activity_code_class::get_qrcode_img($text);
        //ҳ����ֶ�ά��
        echo "<script>top.document.getElementById('qr_code_url').value='".$text."';top.document.getElementById('qr_code_img').value='".$img."';top.__qr_code_preview_obj.set_qr_img('".$img."');top.__qr_code_preview_obj.change_hide();</script>";
        exit();

    }
    else
    {
        //����ԭҳ����߼�
        echo "<script>top.alert('���ɶ�ά��ʧ��');</script>";
        exit();
    }


}






?>