<?php
/**
 * @desc:   ��������ҳ
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/25
 * @Time:   11:31
 * version: 1.0
 */

include_once 'common.inc.php';

$mall_obj = POCO::singleton('pai_mall_seller_class');
$pc_wap = 'pc/';

$user_id = intval($yue_login_id);
if($user_id <1)
{
    if(empty($yue_login_id))
    {
        $r_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $r_url = urlencode($r_url);
        echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=".$r_url."';</script>";
        exit;
        //echo "not login error";
        //exit();
    }
    //echo "<script type='text/javascript'>location.href='http://www.yueus.com/pc/login.php?r_url=http%3a%2f%2fwww.yueus.com%2fmall%2fseller%2ftest%2fnormal_certificate_detail.php%3ftype%3d{$type}';</script>";
    //exit;
}

//У���û���ɫ,���̼ұ���
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_name=$seller_info['seller_data']['name'];
if(empty($seller_name))
{
    //û�н����̼���֤��
    header("location:./normal_certificate_choose.php");
    exit;
}

//��ȡprofile_id
//$seller_info=$mall_obj->get_seller_info($user_id,2);

if(!is_array($seller_info)) $seller_info = array();
$seller_profile_id = intval($seller_info['seller_data']['profile'][0]['seller_profile_id']);
//print_r($seller_info['seller_data']['profile'][0]);

if($seller_profile_id <1)
{
    echo "�Ƿ�����";
    exit;
}

$ret = $mall_obj->get_seller_profile($seller_profile_id);
//print_r($ret);
$default_data = array();

$default_data['avatar']        = $ret[0]['default_data']['0'];
$default_data['cover']         = $ret[0]['default_data']['1'];
$default_data['name']          = $ret[0]['default_data']['2'];
$default_data['sex']           = $ret[0]['default_data']['3'];
$default_data['location_id']   = $ret[0]['default_data']['4'];
$default_data['location_id']['province'] = substr($default_data['location_id']['value'],0,6);
$default_data['introduce']        = $ret[0]['default_data']['5'];
$default_data['introduce']['value']     = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$ret[0]['default_data']['5']['value']);


$type_id = $ret[0]['type_id'];
$type_arr = explode(',',$type_id);



$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'personal-center-edit.tpl.htm');


//��ȡ��ǰ�û����Ա༭���̼���Ϣ
    $mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
    $user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id,false);
    $seller_obj = POCO::singleton('pai_mall_seller_class');
    $type_tmp_list=$seller_obj->get_store_type_id_by_user_id($user_id);
    foreach($type_tmp_list as $key => $value)
    {
        if($value['show']==1)
        {
            $can_edit_type_id_array[] = $value['id'];
        }
    }



    $task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
    $config_type_list = $task_goods_type_obj->get_type_cate();
    foreach($config_type_list as $key => $value)
    {
        $tmp_type_name_array[$value["id"]] = $value["name"];
    }
    //����һ������
    $type_name_array = array("model"=>array(31,$tmp_type_name_array["31"]),
        "cameror"=>array(40,$tmp_type_name_array["40"]),
        "studio"=>array(12,$tmp_type_name_array["12"]),
        "teacher"=>array(5,$tmp_type_name_array["5"]),
        "dresser"=>array(3,$tmp_type_name_array["3"]),
        "diet"=>array(41,$tmp_type_name_array["41"]),
        "activity"=>array(42,$tmp_type_name_array["42"]),
        "other"=>array(43,$tmp_type_name_array["43"])
    );
    $status_array = array("-2"=>"no_record","0"=>"checking","1"=>"pass","2"=>"recheck");
    //$type_id_array = explode(',',$seller_info['seller_data']['profile'][0]['type_id']);
    //ƥ�����ǰ�˵ķ�����֤������״̬
    $i=0;
    foreach($user_status_list as $key => $value)
    {
        $tmp_id = $type_name_array[$value['service_type']][0];
        if($value['status']=="-2")
        {
            //�жϺ�̨�Ƿ��п�ͨ
            if(in_array($tmp_id,$can_edit_type_id_array))
            {
                $tmp_status = "pass";
            }
            else
            {
                $tmp_status = "no_record";
            }
        }
        else
        {
            $tmp_status = $status_array[$value['status']];
        }

        //ͨ�����߼����ܱ༭
        if($tmp_status=="pass")
        {
            $type_list[$i]['id'] = $type_name_array[$value['service_type']][0];
            $type_list[$i]['name'] = $type_name_array[$value['service_type']][1];
            $type_list[$i]['choose'] = 0;
            $i++;
        }

    }
if(!empty($type_list))
{
    $type_list[0]['choose'] = 1;
    $match_type_id = $type_list[0]['id'];
}


$tpl->assign("type_list",$type_list);








// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// ������
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// �ײ�
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// ͷ��bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);


// �ײ�
$footer = _get_wbc_footer();

//�û�ͷ��Icon����2015-7-8
$icon_hash = md5($yue_login_id.'YUE_PAI_POCO!@#456');
$tpl->assign("icon_hash",$icon_hash);

$tpl->assign('footer', $footer);
$tpl->assign('user_id',$user_id);
$tpl->assign('id',$seller_profile_id);
$tpl->assign($default_data);
$tpl->assign('match_type_id',$match_type_id);
$tpl->assign($ret);
$tpl->output();