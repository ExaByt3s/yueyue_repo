<?php
/**
 * @desc:   商家认证
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/16
 * @Time:   14:46
 * version: 1.0
 */

include_once '../common.inc.php';
$pai_mall_certificate_basic_obj = POCO::singleton('pai_mall_certificate_basic_class');
$pai_user_obj = POCO::singleton('pai_user_class');

$basic_type_array = array('company','person','is_card');
$user_id       = intval($yue_login_id);
$basic_type    = trim($_INPUT['basic_type']);
$brand_img_url = trim($_INPUT['brand_img_url']);
$heads_img_url = trim($_INPUT['heads_img_url']);
$tails_img_url = trim($_INPUT['tails_img_url']);

$arr = array('msg'=> '','status'=>0);
//print_r($_INPUT);
//用户没有登录
if($user_id <1)
{
    $arr['msg'] = 'user_id empty';
    $arr['status'] = -1;
    echo json_encode($arr);
    exit;
}
//非法操作
if(!in_array($basic_type,$basic_type_array))
{
    $arr['msg'] = 'basic_type_error';
    $arr['status'] = -2;
    echo json_encode($arr);
    exit();
}
$data = array();

if($basic_type == 'is_card')
{
    //$type = trim($_INPUT['type']);
    $id_card = trim($_INPUT['person_id_card']);
    if(strlen($person_id_card) <1)
    {
        $arr['status'] = -3;
        echo json_encode($arr);
        exit;
    }
    $ret = $pai_mall_certificate_basic_obj->id_card_check($id_card);
    $status = intval($ret['status']);
    if($status == 0)
    {
        $arr['status'] = 1;
        echo json_encode($arr);
        exit;
    }
    $arr['status'] = -2;
    echo json_encode($arr);
    exit;
}

elseif($basic_type == 'person')//个人
{
    $person_true_name = trim($_INPUT['person_true_name']);
    $person_nick      = trim($_INPUT['person_nick']);
    $person_area_id   = intval($_INPUT['person_area_id']);
    $person_zone_id   = intval($_INPUT['person_zone_id']);
    $person_id_card   = trim($_INPUT['person_id_card']);
    if(strlen($person_true_name) <1)
    {
        $arr['msg'] = 'erson_true_name empty';
        $arr['status'] = -3;
        echo json_encode($arr);
        exit();
    }
    if(strlen($person_nick) <1)
    {
        $arr['msg'] = 'person_nick empty';
        $arr['status'] = -4;
        echo json_encode($arr);
        exit();
    }
    if($person_area_id <1)
    {
        $arr['msg'] = 'person_area_id empty';
        $arr['status'] = -5;
        echo json_encode($arr);
        exit();
    }
    if($person_zone_id <1)
    {
        $arr['msg'] = 'person_zone_id empty';
        $arr['status'] = -6;
        echo json_encode($arr);
        exit();
    }
    if(strlen($person_id_card) <1)
    {
        $arr['msg'] = 'person_id_card empty';
        $arr['status'] = -7;
        echo json_encode($arr);
        exit();
    }
    if(strlen($brand_img_url) <1)
    {
        $arr['msg'] = 'brand_img_url empty';
        $arr['status'] = -22;
        echo json_encode($arr);
        exit();
    }
    if(strlen($heads_img_url) <1)
    {
        $arr['msg'] = 'heads_img_url empty';
        $arr['status'] = -22;
        echo json_encode($arr);
        exit();
    }
    if(strlen($tails_img_url) <1)
    {
        $arr['msg'] = 'tails_img_url empty';
        $arr['status'] = -22;
        echo json_encode($arr);
        exit();
    }
    $data['person_true_name']    = utf8_to_gbk($person_true_name);
    $data['person_nick']         = utf8_to_gbk($person_nick);
    $data['person_area_id']      = $person_area_id;
    $data['person_zone_id']      = $person_zone_id;
    $data['person_id_card']      = $person_id_card;
    $data['brand_img_url']       = $brand_img_url;
    $data['heads_img_url']       = $heads_img_url;
    $data['tails_img_url']       = $tails_img_url;

}
elseif($basic_type == 'company')//企业
{
    //接受数据
    $company_name            = trim($_INPUT['company_name']);
    $company_license_num      = trim($_INPUT['company_license_num']);
    $company_place           = trim($_INPUT['company_place']);
    $company_date_line       = trim($_INPUT['company_date_line']);
    $company_bank_id         = intval($_INPUT['company_bank_id']);
    $company_bank_area_id    = intval($_INPUT['company_bank_area_id']);
    $company_bank_city_id    = intval($_INPUT['company_bank_city_id']);
    $company_bank_name       = trim($_INPUT['company_bank_name']);
    $company_card_num        = trim($_INPUT['company_card_num']);
    $company_true_name       = trim($_INPUT['company_true_name']);
    $company_id_card         = trim($_INPUT['company_id_card']);
    $company_contact_name    = trim($_INPUT['company_contact_name']);
    $company_contact_phone   = trim($_INPUT['company_contact_phone']);
    $company_brand_name      = trim($_INPUT['company_brand_name']);
    $person_nick             = trim($_INPUT['person_nick']);
    //$company_license_img_url = trim($_INPUT['company_license_img_url']);
    //必填数据
    if(strlen($company_name) <1)
    {
        $arr['msg'] = 'company_name empty';
        $arr['status'] = -8;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_license_num) <1)
    {
        $arr['msg'] = 'company_license_num empty';
        $arr['status'] = -9;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_place) <1)
    {
        $arr['msg'] = 'company_place empty';
        $arr['status'] = -10;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_date_line) <1)
    {
        $arr['msg'] = 'company_date_line empty';
        $arr['status'] = -11;
        echo json_encode($arr);
        exit();
    }
    if($company_bank_id <1)
    {
        $arr['msg'] = 'company_bank_id empty';
        $arr['status'] = -12;
        echo json_encode($arr);
        exit();
    }
    if($company_bank_area_id <1)
    {
        $arr['msg'] = 'company_bank_area_id empty';
        $arr['status'] = -13;
        echo json_encode($arr);
        exit();
    }
    if($company_bank_city_id <1)
    {
        $arr['msg'] = 'company_bank_city_id empty';
        $arr['status'] = -14;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_bank_name) <1)
    {
        $arr['msg'] = 'company_bank_name empty';
        $arr['status'] = -15;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_card_num) <1)
    {
        $arr['msg'] = 'company_card_num empty';
        $arr['status'] = -16;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_true_name) <1)
    {
        $arr['msg'] = 'company_true_name empty';
        $arr['status'] = -17;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_id_card)<1)
    {
        $arr['msg'] = 'company_id_card empty';
        $arr['status'] = -18;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_contact_name)<1)
    {
        $arr['msg'] = 'company_contact_name empty';
        $arr['status'] = -19;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_contact_phone)<1)
    {
        $arr['msg'] = 'company_contact_phone empty';
        $arr['status'] = -20;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_brand_name) <1)
    {
        $arr['msg'] = 'company_brand_name empty';
        $arr['status'] = -21;
        echo json_encode($arr);
        exit();
    }
    if(strlen($company_license_img_url) <1)
    {
        $arr['msg'] = 'company_license_img_url empty';
        $arr['status'] = -22;
        echo json_encode($arr);
        exit();
    }
    if(strlen($brand_img_url) <1)
    {
        $arr['msg'] = 'brand_img_url empty';
        $arr['status'] = -22;
        echo json_encode($arr);
        exit();
    }
    if(strlen($heads_img_url) <1)
    {
        $arr['msg'] = 'heads_img_url empty';
        $arr['status'] = -22;
        echo json_encode($arr);
        exit();
    }
    if(strlen($tails_img_url) <1)
    {
        $arr['msg'] = 'tails_img_url empty';
        $arr['status'] = -22;
        echo json_encode($arr);
        exit();
    }
    if(strlen($person_nick) <1)
    {
        $arr['msg'] = 'person_nick empty';
        $arr['status'] = -4;
        echo json_encode($arr);
        exit();
    }
    //参数整理
    $data['company_name']           = utf8_to_gbk($company_name);
    $data['company_license_num']    = $company_license_num;
    $data['company_place']          = utf8_to_gbk($company_place);
    $data['company_date_line']      = $company_date_line;
    $data['company_bank_id']        = $company_bank_id;
    $data['company_bank_area_id']   = $company_bank_area_id;
    $data['company_bank_city_id']   = $company_bank_city_id;
    $data['company_bank_name']      = utf8_to_gbk($company_bank_name);
    $data['company_card_num']       = $company_card_num;
    $data['company_true_name']      = utf8_to_gbk($company_true_name);
    $data['company_id_card']        = $company_id_card;
    $data['company_contact_name']   = utf8_to_gbk($company_contact_name);
    $data['company_contact_phone']  = $company_contact_phone;
    $data['company_brand_name']     = utf8_to_gbk($company_brand_name);
    $data['company_license_img_url']= $company_license_img_url;
    $data['brand_img_url']          = $brand_img_url;
    $data['heads_img_url']          = $heads_img_url;
    $data['tails_img_url']          = $tails_img_url;
    $data['person_nick']            = utf8_to_gbk($person_nick);
}
/********上传上来的昵称，更新到这个用户的昵称************/
if($basic_type == 'company' || $basic_type == 'person')
{
    $ret = $pai_user_obj->update_nickname($user_id,$data['person_nick']);
    /*if(empty($ret))
    {
        $arr['msg'] = 'person_nick empty';
        $arr['status'] = -4;
        echo json_encode($arr);
        exit();
    }*/
}
/********上传上来的昵称，更新到这个用户的昵称************/


$data['basic_type'] = $basic_type;
$data['user_id']    = $user_id;
//print_r($data);
//exit;
$ret = $pai_mall_certificate_basic_obj->add_seller_sq($data);

$status = intval($ret['status']);

if($status == 1)
{
    $msg  = 'success';
    $status = 1;
    //$data = array('status' => $status);
}
else
{
    $msg  = 'fail';
    $status = '-1';
    //$data = array('status' => -1);
}

$arr = array(
    'msg'    => $msg,
    'status' => $status
) ;
echo json_encode($arr);


function utf8_to_gbk($str)
{
    if( is_string($str) )
    {
        $str = iconv('utf-8', 'gbk//IGNORE', $str);
    }
    elseif( is_array($str) )
    {
        foreach ($str as $key=>$val)
        {
            $str[$key] = utf8_to_gbk($val);
        }
    }
    return $str;
}