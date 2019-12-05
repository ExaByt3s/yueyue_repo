<?php

/** 
 * 参加活动内容块
 * 
 * author 星星
 * 
 * 2014-7-29
 */



function _ctlPartyEnroll_container($attribs)
{
    global $my_app_event,$my_app_pai;
    global $yue_login_id;


    $eventenroll_container_tpl = $my_app_pai->getView('/disk/data/htdocs232/poco/pai/webcontrols/PartyEnroll_container.tpl.htm',true);
    
    include_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
    include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
    $details_obj = POCO::singleton('event_details_class');
    $enroll_obj     = POCO::singleton('event_enroll_class');
    $event_table_obj = POCO::singleton('event_table_class');
    $pai_user_obj = POCO::singleton('pai_user_class');
    if(!empty($attribs['event_id']))
    {
        $details_info = $details_obj->get_event_by_event_id($attribs['event_id']);	
        
    }
    
    
    
    
    
    //登录人的相关信息
    //$enroll_user_name = poco_cutstr(POCO::execute(array('member.get_user_nickname_by_user_id'), array($login_id)),25);
    $enroll_user_name_tmp = $pai_user_obj->get_user_nickname_by_user_id($yue_login_id);
    $enroll_user_name = poco_cutstr($enroll_user_name_tmp,25);
    $enroll_user_phone = $pai_user_obj->get_phone_by_user_id($yue_login_id);
    

    $eventenroll_container_tpl->assign("__mp_manage_branch",$attribs['__mp_manage_branch']);
    $eventenroll_container_tpl->assign("enroll_user_name",$enroll_user_name);
    $eventenroll_container_tpl->assign("event_id",$attribs['event_id']);
    $eventenroll_container_tpl->assign("budget",$details_info['budget']);
    $eventenroll_container_tpl->assign("enroll_user_phone",$enroll_user_phone);
    $eventenroll_container_tpl->assign("yue_login_id",$yue_login_id);
    $eventenroll_container_tpl->assign("yue_type",$attribs['yue_type']);//外拍活动类型
 
    //$eventenroll_container_tpl->assign("new_version",$attribs['new_version']);//绑约约处理
    //$eventenroll_container_tpl->assign("yue_type",$attribs['yue_type']);//绑约约处理
    //$yue_form_user_name = POCO::execute(array('member.get_user_nickname_by_user_id'), array($login_id));//绑约约处理
    //$eventenroll_container_tpl->assign("yue_form_user_name",$yue_form_user_name);//绑约约处理
    
    
    
    return $eventenroll_container_tpl->result();
}
?>