<?php

include_once 'common.inc.php';
include_once 'config/authority_model_audit.php';
include_once 'include/common_function.php';
$tpl = new SmartTemplate("model_audit_list.tpl.htm");

$page_obj = new show_page ();

$user_obj = POCO::singleton ( 'pai_user_class' );
$user_add_obj = POCO::singleton ( 'pai_model_add_class' );
$model_audit_obj = POCO::singleton('pai_model_audit_class');
$hot_model_obj = POCO::singleton('pai_hot_model_class');
$model_card_obj = POCO::singleton('pai_model_card_class');
$model_style_v2_obj = POCO::singleton('pai_model_style_v2_class');
//��ȡ֧�����˺�
$user_account_obj = POCO::singleton('pai_bind_account_class');
$is_approval = isset($_INPUT['is_approval']) ? (int)$_INPUT['is_approval'] : -1 ;
$phone = (int)$_INPUT['phone'] ? (int)$_INPUT['phone'] : '';
$user_id   = (int)$_INPUT['user_id'] ? (int)$_INPUT['user_id'] : '';
$start_add_time   = $_INPUT['start_add_time'] ? $_INPUT['start_add_time'] : '';
$end_add_time     = $_INPUT['end_add_time'] ? $_INPUT['end_add_time'] : '';
$start_audit_time = $_INPUT['start_audit_time'] ? $_INPUT['start_audit_time'] : '';
$end_audit_time   = $_INPUT['end_audit_time'] ? $_INPUT['end_audit_time'] : '';
if($_INPUT['act'] == 'approval')
{
    $reason = $_INPUT['reason'] ?  $_INPUT['reason'] : '';
    $user_id = $_INPUT['user_id'] ? $_INPUT['user_id'] : 0;
    if (empty($user_id)) 
    {
        echo "<script>alert('�Ƿ�����');location.href='model_audit_list.php'</script>";
        exit;
    }
    $inputer_name = get_user_nickname_by_user_id($yue_login_id);
    if ($_INPUT['is_approval'] == 1 && !$user_add_obj->get_is_set_by_user_id($user_id)) 
    {
        $user_info   = $user_obj->get_user_info_by_user_id($user_id);
        $user_alipay = $user_account_obj->get_alipay_account_by_user_id($user_id);
        //var_dump($user_alipay);exit;
        if (!empty($user_info) && is_array($user_info)) 
         {
        /*
        *���ݴ���
        */
          //��������
           $data_info['app_name']       = $user_info['nickname'];
           $data_info['phone']          = $user_info['cellphone'];
           $data_info['location_id']    = $user_info['location_id'];
           $data_info['img_url']        = $user_info['user_icon'];
           $data_info['inputer_time']   = date('Y-m-d H:i:s');
           $data_info['inputer_name']   = $inputer_name;
           //print_r($data_info);exit;
           $user_add_obj->insert_model_info(true ,$user_id, $data_info);
           //print_r($user_info);
           //�������
           $stature_data['chest']    = $user_info['chest'];
           $stature_data['waist']    = $user_info['waist'];
           $stature_data['chest_inch'] = $user_info['hip'];
           $stature_data['height']   = $user_info['height'];
           $stature_data['weight']   = $user_info['weight'];
           $stature_data['sex']      = intval($user_info['sex']);
           $stature_data['age']      = substr($user_info['birthday'],0, 7);
           $user_add_obj->insert_model_stature($user_id, $stature_data);
           //print_r($stature_data);
           //���ѭ��
           if (!empty($user_info['model_style_v2']) && is_array($user_info['model_style_v2'])) 
           {
              foreach ($user_info['model_style_v2'] as $key => $vo) 
              {
                 $style_data['style']      = change_style_int($vo['style']);
                 $style_data['addh_price'] = $vo['continue_price'];
                 $price = $vo['price'];
                 if ($vo['hour'] == 2) 
                 {
                    $style_data['twoh_price'] = $price;
                 }
                 else 
                 {
                    $style_data['twoh_price'] = $price;
                 }
                 $user_add_obj->add_style($user_id, $style_data);
               } 
            }
           //ͼƬ����
            if (!empty($user_info['model_pic']) && is_array($user_info['model_pic'])) 
            {
              foreach ($user_info['model_pic'] as $pic_key => $vo) 
              {
                 $pic_data['img_url'] = $vo['img'];
                 $user_add_obj->insert_model_pic($pic_data, $user_id);
              }
            }
            //֧������Ϣ
            $other_data['alipay_info'] = $user_alipay;
            $other_data['information_sources'] = 'APP�û�';
            $user_add_obj->insert_model_other($user_id, $other_data);
         }
    }
    /*die();
    print_r($user_info);exit;*/
    //var_dump($user_info);exit;
    if($model_audit_obj->update_model(array("is_approval"=>$is_approval,"audit_time"=>time(),"audit_user_id"=>$yue_login_id, "reason"=> $reason), $user_id))
    {
        echo "<script>alert('�����ɹ�');location.href='{$_SERVER['HTTP_REFERER']}'</script>";
        exit;
    }
    
}

if($_INPUT['act'] == 'approval')
{
    $user_id = $_INPUT['user_id'];
    $model_audit_obj->update_model(array("is_approval"=>$is_approval,"audit_time"=>time()), $user_id);

    $user_info = $user_obj->get_user_info($user_id);
    $location_id = $user_info["location_id"];

    //���͵�����ģ��
    //$hot_model_obj->add_model(array("user_id"=>$user_id,"location_id"=>$location_id));

    echo "<script>alert('��˳ɹ�');</script>";
    exit;
}



$where  = "1";
//$setvar = "";
if ($is_approval != -1) 
{
  $where .= " AND is_approval={$is_approval}";
  //$setvar = array("is_approval"=>$is_approval);
}
if($phone)
{
    $where .= " AND cellphone={$phone}";
}

if($user_id)
{
    $where .= " AND user_id={$user_id}";
}
if($start_add_time && $end_add_time)
{
  $start_tmp_add_time = strtotime($start_add_time);
  $end_tmp_add_time   = strtotime($end_add_time);
  $where .= " AND add_time BETWEEN {$start_tmp_add_time} AND {$end_tmp_add_time}";
}
if($start_audit_time && $end_audit_time)
{
  $start_tmp_audit_time = strtotime($start_audit_time);
  $end_tmp_audit_time   = strtotime($end_audit_time);
  $where .= " AND audit_time BETWEEN {$start_tmp_audit_time} AND {$end_tmp_audit_time}";
}
/*
*���ñ��������
*/
$title = "�����ģ��";
switch ($is_approval) 
{
    case -1:
      $title = "ģ������ҳ";
      $sort = 'add_time DESC';
      break;
    case 0:
        $title = "�����ģ��";
        $sort = 'add_time DESC';
        break;
    case 1:
        $title = "���ͨ��ģ��";
        $sort = 'audit_time DESC';
        break;
    case 2:
        $title = "��˲�ͨ��ģ��";
        $sort = 'audit_time DESC';
        break;
}
//var_dump($where);
$show_count = 20;
$page_obj->setvar (array("is_approval"=>$is_approval, "start_add_time"=>$start_add_time, "end_add_time" => $end_add_time, "start_audit_time"=>$start_audit_time,"end_audit_time"=>$end_audit_time));

$total_count = $model_audit_obj->get_model_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $model_audit_obj->get_model_list(false, $where, $sort, $page_obj->limit());
//print_r($list);exit;
//print_r($list);exit;
foreach($list as $k=>$val){
    $list[$k]['add_time'] = date("Y-m-d",$val['add_time']);    
    $list[$k]['nickname'] = get_user_nickname_by_user_id($val ['user_id'] );
    $list[$k]['user_thumb'] = str_replace("_86","",get_user_icon($val ['user_id']));
    $list[$k]['user_icon'] = str_replace("_86","_100",get_user_icon($val ['user_id'], 86));
    $list[$k]['audit_name'] = get_user_nickname_by_user_id($val['audit_user_id']);
    $list[$k]['audit_time'] = !empty($val['audit_time']) ? date("Y-m-d H:i", $val['audit_time']) : '';
    /*$user_info = $user_obj->get_user_info($val ['user_id']);
    $user_data_info = $user_obj->get_user_info_by_user_id($val['user_id']);
    print_r($user_info);//exit;
    $cellphone = $user_info["cellphone"];*/
    $add_time = date("Y-m-d H:i",$val["add_time"]);
    $list[$k]['is_complete'] = $model_card_obj->check_input_is_complete($val ['user_id']);
    $list[$k]['cellphone'] = $val['cellphone'];
    $list[$k]['add_time'] = $add_time;
    $list[$k]['is_set']  = $user_add_obj->get_user_inputer_name_by_user_id($val['user_id']);
}
//print_r($list);
$tpl->assign('title', $title);
$tpl->assign('start_add_time', $start_add_time);
$tpl->assign('end_add_time', $end_add_time);
$tpl->assign('start_audit_time', $start_audit_time);
$tpl->assign('end_audit_time', $end_audit_time);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('total_count', $total_count);
$tpl->assign('list', $list);
$tpl->assign('phone', $phone);
$tpl->assign('user_id', $user_id);
$tpl->assign('is_approval', $is_approval);
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
?>