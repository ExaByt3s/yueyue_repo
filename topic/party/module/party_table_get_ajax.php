<?php
/** 
 * 
 * �첽��ȡ����
 * 
 * author ����
 * 
 * 2014-8-14
 * 
 */
include_once("../party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$event_id = (int)$_INPUT['event_id'];
$event_table_obj = POCO::singleton('event_table_class');
$enroll_obj     = POCO::singleton('event_enroll_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');
$ajax_status = 1;
if(empty($event_id))
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
    $ajax_status = 0;
    $poco_login_id = 0;
}



if(!empty($ajax_status))
{
    //��ȡ����
    $table_list = $event_table_obj->get_event_table($event_id);
    
    $table_config_arr = array("��","һ","��","��","��","��","��","��","��","��","ʮ");
    //��ѯ֧�����
    $user_enroll_list = $enroll_obj->get_event_enroll_list($poco_login_id,$event_id,false,"");
    //������������Ϣ
    foreach($user_enroll_list as $key => $value)
    {
        $tmp_enroll_list[$value['table_id']] = array($value['pay_status'],$value['enroll_id'],$value['enroll_num']);
    }
    
    /* if($yue_login_id==101428)
    {
        
        var_dump($user_enroll_list);
        
    } */
    
    foreach($table_list as $key => $value)
    {
        
        //�жϵ�ǰ�ⳡ�Ƿ��Ѿ���ʼ���������
        if($value['begin_time']<time())
        {
            $table_list[$key]['had_began'] = 1;//��ʼ��
        }
        else
        {
            $table_list[$key]['had_began'] = 0;//��ʼ��
        }
        
        //��鵱ǰ�˵ı������
        $tmp_duplicate = $enroll_obj->check_duplicate($poco_login_id,$event_id,$status="all", $value['id']);//��¼�û��ó��ı�����Ϣ
        if($tmp_duplicate)
        {
            //�ж��Ƿ��Ѿ�֧��
            if($tmp_enroll_list[$value['id']][0]>0)
            {
                $table_list[$key]['had_enroll'] = 2;//��������֧����
            }
            else
            {
                $table_list[$key]['had_enroll'] = 1;//������δ֧��
            }
            //���ס����ID
            $table_list[$key]['data_enroll'] = $tmp_enroll_list[$value['id']][1];
            $table_list[$key]['data_num'] = $tmp_enroll_list[$value['id']][2];
            
            
        }
        else
        {
            //���ñ���IDΪ��
            $table_list[$key]['data_enroll'] = 0;
            $table_list[$key]['data_num'] = 0;
        }
        $table_list[$key]['data_mark'] = ((int)$key)+1;
        $table_list[$key]['site_name'] = $table_config_arr[$table_list[$key]['data_mark']];//��Ӧ������
        $table_list[$key]['site_name'] = iconv("GBK","UTF-8",$table_list[$key]['site_name']);
        
        /********10��13���޸�ʱ��*****************/
        $table_list[$key]['begin_time'] = iconv("GBK","UTF-8",date("m��d�� H:i",$table_list[$key]['begin_time']));
        //�ж��¸����Ƿ�ͬһ��
        $tmp_begin_day = date("md",$value['begin_time']);
        $tmp_end_day = date("md",$value['end_time']);
        if($tmp_begin_day==$tmp_end_day)
        {
            $table_list[$key]['end_time'] = iconv("GBK","UTF-8",date("H:i",$table_list[$key]['end_time'])); 
        }
        else
        {
           $table_list[$key]['end_time'] = iconv("GBK","UTF-8",date("m��d�� H:i",$table_list[$key]['end_time'])); 
        }
        unset($tmp_begin_day);
        unset($tmp_end_day);
        /********10��13���޸�ʱ��*****************/
        
        
    }
    
    //���Ȩ��
    $join_mode_auth = $enroll_obj->check_join_mode_auth( $event_id,$poco_login_id );
    if($join_mode_auth)
    {
        $res = "Y";
    }
    else
    {
        $res = "N";
    }
}

$res_arr = array(
"ajax_status"=>$ajax_status,
"table_list"=>$table_list,
"res"=>$res

);

echo json_encode($res_arr);



?>