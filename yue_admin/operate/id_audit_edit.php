<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate ( "id_audit_edit.tpl.htm" );

$user_id = $_INPUT ['user_id'];
$status = $_INPUT ['status'];
$id_code = $_INPUT ['id_code'];
$img = $_INPUT ['img'];


$id_audit_obj = POCO::singleton('pai_id_audit_class');
$id_obj = POCO::singleton('pai_id_class');
$level_obj = POCO::singleton ( 'pai_user_level_class' );
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');

$id_info = $id_audit_obj->get_audit_info($user_id);


if($_POST && $status==1)
{
	$insert_data['user_id'] = $user_id;
	$insert_data['id_code'] = $id_code;
	$insert_data['img'] = $img;
	$insert_data['add_time'] = time();
	
    if(!$id_code)
    {
        echo "<script>alert('请输入身份证号码');</script>";
        exit;
    }
	
	$id_obj->add_id($insert_data);
    
    if($id_audit_obj->update_audit(array("status"=>1), $user_id))
    {
    	//发通过消息
    	$level_obj->send_level_approval_msg($user_id);
    	
    	//发微信通过消息
		$weixin_pub_obj->message_template_send_by_user_id($user_id, "G_PAI_WEIXIN_CREDIT2_PASSED", $data, $to_url);
    	
        echo "<script>alert('操作成功');parent.location.href='id_audit_list.php';</script>";
        exit;
    }
    
}

if($_POST && $status==2) 
{
    //发不通过消息
    $level_obj->send_level_deny_msg($user_id);
    
    //发微信不通过信息
    $weixin_pub_obj->message_template_send_by_user_id($user_id, "G_PAI_WEIXIN_CREDIT2_REFUSED", $data, $to_url);
    	
	$id_audit_obj->update_audit(array("status"=>2), $user_id);

    echo "<script>alert('操作成功');parent.location.href='id_audit_list.php';</script>";
    exit;
}
$id_info['img'] = yueyue_resize_act_img_url($id_info['img']);

$tpl->assign ( $id_info );
$tpl->assign ( 'act', $act );
//var_dump($act);
//$tpl->assign ( 'MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER );
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output ();
?>