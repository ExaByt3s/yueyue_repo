<?php
/** 
 * 
 * ���Ļǩ���б���ҳ
 * 
 * 
 * author ����
 * 
 * 
 * 2015-3-3
 * 
 */

include_once("../party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$enroll_obj     = POCO::singleton('event_enroll_class');
$system_obj  = POCO::singleton('event_system_class');
$details_obj = POCO::singleton('event_details_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');

$__mp_manage_branch = 1;//֧����
$event_id = (int)$_INPUT['event_id'];//�ID
$enroll_id = (int)$_INPUT['enroll_id'];//����ID
$table_id = (int)$_INPUT['table_id'];//����ID
$action = trim($_INPUT['action']);//����

$ajax_status = 1;

if(empty($yue_login_id))
{
    $ajax_status = 0;
}


//��ѯ��Ӧ��poco_id
if(!empty($yue_login_id))
{
    $poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);
}
else
{
    $poco_login_id = 0;
}

if(empty($poco_login_id))
{
    $ajax_status = 0;
}


if(empty($enroll_id) || empty($table_id) || empty($action))
{
    $ajax_status = 0;
}

$event_info = $details_obj->get_event_by_event_id($event_id);
if(!empty($poco_login_id))
{
    //�ж�Ȩ��
    if($event_info['user_id']==$poco_login_id)
    {
        $event_user_role = "author";
        //�ж��Ƿ������Ա
        $is_event_admin = $system_obj->check_is_admin($poco_login_id);
        if($is_event_admin)
        {
            $event_user_role = "admin";
        }
    }
    else
    {
        $is_event_admin = $system_obj->check_is_admin($poco_login_id);
        if($is_event_admin)
        {
            $event_user_role = "admin";
        }
    }
    
}


if($ajax_status==1)
{
    if($event_user_role=="author" || $event_user_role=="admin")
    {
        $enroll_info = $enroll_obj->get_enroll_by_enroll_id($enroll_id);
        switch ($action)
        {
            case "first":
                //��������ѡ����
                    $return = $enroll_obj->check_is_full($enroll_info['enroll_num'],$enroll_info['event_id'],$table_id,array(0));
                    if(!$return)
                    {
                        $ret = $enroll_obj->update_enroll_status($enroll_id,0);
                        $msg = ($ret)?"Y":"N";

                    }
                    else
                    {
                        $ret = $enroll_obj->update_enroll_status($enroll_id,1);
                        $msg = "F";
                    }
                
            break;  
            case "backup":
                //�����ú󱸲���
                $status = 1;
                $ret = $enroll_obj->update_enroll_status($enroll_id,$status);
                if($status==1)
                {
                    send_pm($enroll_info['event_id'],$enroll_info['user_id'],$event_info['title'],$event_info['user_id']);
                }
                $msg=($ret)?"Y":"N";
            break;
            case "del":
                //��ɾ�������߲���
                $ret=$enroll_obj->del_enroll_v2($enroll_id);
                $msg=($ret)?"Y":"N";
                //$msg = "Y";
            break;
            case "pass":
                if($__mp_manage_branch=="development")
                {
                    $paid = "paid";
                }
                else
                {
                    $paid = "unpaid";
                }
                $ret=$enroll_obj->accept_join($enroll_info['event_id'],$enroll_info['table_id'],$enroll_info['enroll_id'],$paid);
                $msg=($ret)?"Y":"N";
            break;
            default:
            break;
        }
    }
}


$res_arr = array(
"ajax_status"=>$ajax_status,
"res"=>$msg
);
echo json_encode($res_arr);

function send_pm($event_id,$user_id,$title,$event_user_id)
{
    //����վ��pm
    
    
    $user_name = html_entities(POCO::execute(array('member.get_user_nickname_by_user_id'), array($user_id)));
    $event_user_name = html_entities(POCO::execute(array('member.get_user_nickname_by_user_id'), array($event_user_id)));
    //�ȫ��url
    global $my_app_event;
    $app_url = $my_app_event->ini('app_config/APP_URL');

    $msg_title="���ı����ѱ�תΪ�";
    $message="�������μӵ�&#34;<a href='".$app_url."event_detail.php?event_id={$event_id}' target='_blank'>".$title."</a>&#34;����Ѿ������֯�� {$event_user_name} תΪ����������ʣ�������֯����ϵ��";
    
    //POCO::execute(array('friends.add_pm'), array(10000, 'POCOϵͳ��Ϣ',$user_id, $user_name, $msg_title, $message, 0, 0));

    $notify_ext_par = array(
    'to_user_name'=>$user_name,
    'ext_msg_type'=>'other_notify',
    'ext_type_icon'=>'event_notice',
    );

    POCO::execute(array('pm.add_new_notify_msg'), array($user_id, $msg_title ,$message, $notify_ext_par ));
}


?>