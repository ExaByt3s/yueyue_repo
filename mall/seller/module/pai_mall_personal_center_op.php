<?php
/**
 * @desc:   �������Ĳ�����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/26
 * @Time:   10:49
 * version: 1.0
 */

include_once '../common.inc.php';
$mall_obj = POCO::singleton('pai_mall_seller_class');
$user_id     = intval($yue_login_id);
$id          = intval($_INPUT['id']);
$act         = trim($_INPUT['act']);
$cover       = $_INPUT['cover'];
$avatar      = $_INPUT['avatar'];
$name        = trim($_INPUT['name']);
$location_id = intval($_INPUT['location_id']);
$introduce   = trim($_INPUT['introduce']);
$match_type_id    = intval($_INPUT['match_type_id']);
//Ԥ��������cashe
$data_type = trim($_INPUT['data_type']);//У����Ԥ��ģʽ


//��ʾ��Ϣ
$arr = array('msg'=> '','status'=>0);
if($user_id <1)
{
    $arr['msg'] = 'user_id empty';
    $arr['status'] = -1;
    echo json_encode($arr);
    exit;
}
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_name=$seller_info['seller_data']['name'];
if(empty($seller_name))
{
    $arr['msg'] = 'no seller_name';
    $arr['status'] = -2;
    echo json_encode($arr);
    exit;
}
if($id <1)
{
    echo "id empty";
    $arr['msg'] = 'id empty';
    $arr['status'] = -3;
    echo json_encode($arr);
    exit;
}
/*******��������У��**********/
    $patt = '/undefined/';
    preg_match($patt,$cover,$match1);
    if(!empty($match1))
    {
        $arr['msg'] = 'cover underfined';
        $arr['status'] = -4;
        echo json_encode($arr);
        exit;
    }


    if(strlen($cover) <1)
    {
        $arr['msg'] = 'cover empty';
        $arr['status'] = -5;
        echo json_encode($arr);
        exit;
    }
    preg_match($patt,$avatar,$match2);
    if(!empty($match2))
    {
        $arr['msg'] = 'avatar underfined';
        $arr['status'] = -6;
        echo json_encode($arr);
        exit;
    }
    if(strlen($avatar) <1)
    {
        $arr['msg'] = 'avatar empty';
        $arr['status'] = -7;
        echo json_encode($arr);
        exit;
    }
    if(strlen($name) <1)
    {
        $arr['msg'] = 'name empty';
        $arr['status'] = -8;
        echo json_encode($arr);
        exit;
    }
    if($location_id <1)
    {
        $arr['msg'] = 'location_id empty';
        $arr['status'] = -9;
        echo json_encode($arr);
        exit;
    }
    if(strlen($introduce) <1)
    {
        $arr['msg'] = 'introduce empty';
        $arr['status'] = -10;
        echo json_encode($arr);
        exit;
    }
    //У��loadingͼ
    $loadingclass_res = strpos($introduce,"loadingclass");
    if($loadingclass_res>0)
    {
        $arr['msg'] = 'introduce loading';
        $arr['status'] = -250;
        echo json_encode($arr);
        exit;
    }




$data = array();
    $data['cover']       = $cover;
    $data['avatar']      = $avatar;
    $data['name']        = iconv('utf-8', 'gbk//IGNORE',$name);
    $data['location_id'] = $location_id;
    $introduce= iconv('utf-8', 'gbk//IGNORE',$introduce);

    //src����У��
    $check = mall_src_link_check($introduce);

    if(!$check)
    {
        $arr['msg'] = 'src error';
        $arr['status'] = -25;
        echo json_encode($arr);
        exit;
    }

    //src����У�����

    $tmp_content = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$introduce);
    $tmp_content = mall_closetags($tmp_content);
    $tmp_content = strip_tags($tmp_content,'<p><img><br><embed>');
    $tmp_content                = preg_replace("/style=\"(.*)\"/isU","",$tmp_content);
    $tmp_content                = preg_replace("/style=\'(.*)\'/isU","",$tmp_content);
    $tmp_content                = preg_replace("/align=center/is","",$tmp_content);
    $data['introduce'] = $tmp_content;
/*******��������У��**********/

if($match_type_id>0)
{

    //��������ѡ��ͬ�Ĵ����
    $include_once_url = "mod/pai_mall_personal_center_op_".$match_type_id.".php";
    if(file_exists($include_once_url))
    {
        //�㼶����
        include_once $include_once_url;
    }
    else
    {
        $arr['msg'] = 'data_error empty';
        $arr['status'] = -100;
        echo json_encode($arr);
        exit;
    }
}


if($data_type=="preview")
{
    //���
    $in_att_arr = array("cover","avatar","name","location_id","introduce");//������Ϣ
    $i=0;
    foreach($data as $k => $v)
    {
        if(!in_array($k,$in_att_arr))
        {
            $data['att'][$i]['key'] = $k;
            $data['att'][$i]['data'] = $v;
            $i++;
        }
    }

    //����cashe
    $time_mark_value = date("Ymdhis",time());
    $data['cache_id'] = $user_id.$time_mark_value;
    $data['user_id'] = $user_id;
    //print_r($data);
    $ret = $mall_obj-> set_seller_data_for_temp($data);
    if($ret)
    {
        $result = 1;
        //���ɶ�ά��ͼƬ
        $text = TASK_PROJECT_ROOT."/preview_middle_jump.php?cache_id=".$data['cache_id']."&type=person";
        $img = pai_activity_code_class::get_qrcode_img($text);
        $arr['code_img'] = $img;//���ض�ά��
        $arr['text'] = $text;//���ض�ά��
    }
    else
    {
        $result = 0;
    }



}
else
{
    //���
    $in_att_arr = array("cover","avatar","name","location_id","introduce");//������Ϣ
    $i=0;
    foreach($data as $k => $v)
    {
        if(!in_array($k,$in_att_arr))
        {
            $data['att'][$i]['key'] = $k;
            $data['att'][$i]['data'] = $v;
            $i++;
        }
    }


    $ret = $mall_obj->user_update_seller_profile($id,$data,$user_id);
    $result = intval($ret['result']);
}





if($result == 1) //�ɹ�
{
    $arr['msg'] = 'success';
    $arr['status'] = 1;
    echo json_encode($arr);
    exit;
}
$arr['msg'] = 'fail';
$arr['status'] = -21;
echo json_encode($arr);
exit;


