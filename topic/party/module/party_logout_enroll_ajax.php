<?php
/** 
 * �˳����α����첽����ҳ
 * 
 * author ����
 * 
 * 
 * 2017-8-5
 * 
 * 
 */


include_once("../party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');



$event_id = (int)$_INPUT['event_id'];
$table_id = (int)$_INPUT['table_id'];
$text = urldecode(trim($_INPUT['text']));
$text = iconv("UTF-8","GBK",$text);


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

if(empty($event_id) || empty($table_id))
{
    $ajax_status = 0;
}


if($ajax_status==1)
{
    //ȡ��Ӧ�ò�������ʵ��
    $enroll_obj = POCO::singleton('event_enroll_class');
    $details_obj = POCO::singleton('event_details_class');
    $is_enroll = $enroll_obj->check_duplicate($poco_login_id,$event_id,$status="all", $table_id);
    if(empty($is_enroll))
    {
        $msg = "N";
    }
    else//�˳�����
    {
        $enroll_info = $enroll_obj->get_enroll_info_by_event_id_and_user_id($event_id,$poco_login_id);
        $event_info = $details_obj->get_event_by_event_id($event_id);
        
        //�鿴�Ƿ񷢹����ԣ���������
        $cmd_obj = POCO::singleton('event_commend_act_class');
        $cmd_list_count = $cmd_obj->get_cmd_list_by_event_id_user_id($event_id, $poco_login_id , true, "");
        if($cmd_list_count>0)
        {
            $msg = "H";
        }
        else
        {

            $where_str = "event_id = {$event_id} AND user_id = {$poco_login_id} AND table_id = {$table_id}";
            $enroll_info = $enroll_obj->get_enroll_list($where_str, $b_select_count = false, $limit = '0,20', $order_by = 'enroll_id DESC',$fields="*");
            $ret = $enroll_obj->del_enroll_v2($enroll_info[0]['enroll_id']);
            if($ret)
            {

                $user_name = html_entities(POCO::execute(array('member.get_user_nickname_by_user_id'), array($poco_login_id)));
                //���˳�ԭ���������
                $cmt_url = "http://event.poco.cn/event_detail.php?event_id=".$event_id;
                $topic_id = POCO::execute('cmt.register_topic', array($cmt_url, $event_info['title'], 0, $event_info['title']));
                $cmt_content = "���˳��˻�е�һ����{$text}";
                $cmt_id = POCO::execute('cmt.new_cmt_by_topic_id', array($topic_id, $poco_login_id, $user_name, '', $cmt_content, $custom_data));
                //�˳�������5��
                
                $action_type = "EVENT_DELAY_MINUS_POINT";//���ִ��� �˳���������5��
                $total_point = 0 - 5;
                POCO::execute('credit_system.credit_system_do_action', array($poco_login_id, $action_type,'',$total_point));
                //��վ����
                    
                //�ȫ��url
                $app_url = $my_app_event->ini('app_config/APP_URL');
                $event_title = $event_info['title'];
                $msg_title="�����˳��&#34;{$event_title}&#34;";
                $message="���ã����ѳɹ��˳���е�һ��&#34;<a href='".$app_url."event_detail.php?event_id={$event_id}' target='_blank'>".$event_title."</a>&#34;�������˳�������۵�5��POCO���֡�";
                //POCO::execute(array('friends.add_pm'), array(10000, 'POCOϵͳ��Ϣ',$enroll_info['user_id'], $user_name, $msg_title, $message, 0, 0));
                $notify_ext_par = array(
                'to_user_name'=>$user_name,
                'ext_msg_type'=>'other_notify',
                'ext_type_icon'=>'event_notice',
                );

                POCO::execute(array('pm.add_new_notify_msg'), array($login_id, $msg_title ,$message, $notify_ext_par ));
                $msg = "Y";
                


            }
            else
            {
                $msg = "N";
            }
        }
        
    }
}


$res_arr = array(
"ajax_status"=>$ajax_status,
"res"=>$msg,
);
echo json_encode($res_arr);

?>