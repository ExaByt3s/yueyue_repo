<?php

include_once 'common.inc.php';
$tpl = new SmartTemplate ( "model_audited_detail.tpl.htm" );

$user_id = $_INPUT ['user_id'] ? $_INPUT ['user_id'] : 0;
if (empty($user_id)) 
{
    echo "<script>window.alert\"非法操作\");location.href=\"model_audited_list.php\"</script>";
    exit;
}
$user_obj = POCO::singleton ( 'pai_user_class' );
$model_audit_obj = POCO::singleton('pai_model_audit_class');
$hot_model_obj = POCO::singleton('pai_hot_model_class');

$user_info  = $user_obj->get_user_info_by_user_id($user_id);
if (!empty($user_info) && is_array($user_info)) 
{
    $user_info['user_thumb'] = str_replace("_165","",$user_info['user_icon']);
    if (!empty($user_info['model_pic']) && is_array($user_info['model_pic'])) 
    {
        foreach ($user_info['model_pic'] as $key => $vo) 
        {
         $user_info['model_pic'][$key]['img_url'] = str_replace("_260","",$vo['img']);
       }
    }
    
}
//print_r($user_info);

if($_INPUT['act'] == 'del')
{
    
    if($model_audit_obj->update_model(array("is_delete"=>1), $user_id))
    {
        echo "<script>alert('操作成功');top.location.href='model_audited_list.php';</script>";
        exit;
    }
    
}

if($_INPUT['act'] == 'approval')
{
    
	$model_audit_obj->update_model(array("is_approval"=>1), $user_id);
	$user_info = $user_obj->get_user_info($user_id);
	$location_id = $user_info["location_id"];

	//推送到热门模特
	$hot_model_obj->add_model(array("user_id"=>$user_id,"location_id"=>$location_id));

    echo "<script>alert('推送成功');top.location.href='model_audited_list.php';</script>";
    exit;
}

$tpl->assign ( $user_info );
$tpl->assign ( 'act', $act );
$tpl->assign ( 'user_id', $user_id );
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output ();
?>