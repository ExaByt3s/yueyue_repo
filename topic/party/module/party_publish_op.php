<?php
/** 
 * ���������ҳҳ
 * 
 * author ����
 * 
 * 2014-7-29
 */


define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once("./party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$event_check_obj	= POCO::singleton('event_check_class');
$event_table_obj    = POCO::singleton('event_table_class');
$event_table_check_obj = POCO::singleton('event_table_check_class');
$event_details_obj = POCO::singleton('event_details_class');
$admin_user_obj = POCO::singleton('event_admin_user_class');
//����֧����
$__mp_manage_branch = 1;//������Ϊ֧���棬�ǿ�����Ϊ��֧��
$act= trim($_INPUT['act']);
$event_id = (int)$_INPUT['event_id'];


$data = array();

//��Ϣ����
    //��ͨ����
    $data['title'] = trim($_INPUT['title']);//����
    $data['type_icon'] = "photo";//ԼԼ�д��Ϊphoto
    
    

   
    //�������
    $location_radio = (int)$_INPUT['location_radio'];
    if($location_radio==1)
    {
        //��ȡ��Ӧ��location_id
        $locate_id_1 = $_INPUT['location_id1'];
        $locate_id_2 = $_INPUT['location_id2'];
        
        if(empty($locate_id_2))
        {
            $data['location_id'] = $locate_id_1;
        }
        else
        {
            $data['location_id'] = $locate_id_2;
        }

            
    }
    else
    {
        $data['location_id'] = "";
    }
    
    $data['address'] = trim($_INPUT['address']);//��ַ
    
    //����
    if($act=="add" || $act=="preview")
    {
        $data['budget'] = $_INPUT['budget'];

    }
    
    //����ģʽ
    $data['join_mode'] = (int)$_INPUT['join_mode'];
    
    

    


    
    
    //ȡ��ǩ����
    //�˿ʽ
    $pay_type_arr = $_INPUT['pay_type'];
    $data['pay_type'] = (int)$pay_type_arr[0];
    

    //����ͼitem_id
    $cover_image_item_id=(int)$_INPUT['cover_image_item_id'];
    
    $setting_arr = array('cover_image_item_id'=>$cover_image_item_id);
    $data['setting'] = serialize($setting_arr);

//���ܴ���

    //����ͼ
    $data['cover_image'] = trim($_INPUT['cover_image']);
    //����
    $data['content'] = trim($_INPUT['content']);
    
    //ģ�أ�ͼ��ģ��
    $text_part = $_INPUT['text'];
    $model_part = $_INPUT['model_part'];
    
    
    
    $part_num = count($text_part);
    
    for($i=0;$i<$part_num;$i++)
    {
        $other_info[$i]['text'] = trim($text_part[$i]);
        $tmp_img_mark = $model_part[$i];
        
        $tmp_img = $_INPUT['upload_imgs_'.$tmp_img_mark];
        
        
        if(empty($tmp_img))
        {
            
            $other_info[$i]['img'] = array();
        }
        else
        {
            foreach($tmp_img as $key => $value)
            {
                $other_info[$i]['img'][$key] = $value;
            }
        }
  
    }
    
    //ͼ��ģ��ͼƬ
    $other_info_str = serialize($other_info);
    $data['other_info'] = $other_info_str; 
 
//����̣�����
    
    //�ܱ�ע
    $data['remark'] = trim($_INPUT['remark']);
    //���
    $leader_nickname = $_INPUT['leader_nickname'];//�������
    $leader_phone = $_INPUT['leader_phone'];//��ϵ��ʽ
    
    //���
    foreach($leader_nickname as $key => $value)
    {
        $leader_info[$key]['name'] = $value;
        $leader_info[$key]['mobile'] = $leader_phone[$key];
        
    }


    $leader_info_str = serialize($leader_info);
    $data['leader_info'] = $leader_info_str; 


    //��������
    if($act=="add" || $act=="preview")
    {
        $data['user_id'] = $login_id;                              //������ID
    }
    $data['category'] = 2;                                     //����ࣨ���ϣ����£�
    
    //���ߺ��
    $data['limit_num']      = 10;                                    //�������0Ϊ����
    $data['limit_enroll']   = 1;                                     //һ�α����ܱ�������Ĭ��1��
        
 
    $data['add_time']       = time();                                //���ʱ��
    $data['status']         = 0;                                     //�״̬��0Ϊδ��ʼ��1�����У�2�ѽ���
    //��������
    
    $data['last_update_time']=time();
    //var_dump($data);

//��ȡ����
$site_start_time = $_INPUT['site_start_time'];//���ο�ʼʱ��
$site_end_time = $_INPUT['site_end_time'];//���ν���ʱ��
$site_join_num = $_INPUT['site_join_num'];//��������
$table_id = $_INPUT['table_id'];//������ʽ��ID����δ������û�У�

//���⴦����ѧԺ���ݣ����ߺ��

/********10��13���޸�ʱ��*****************/
    //�����νṹ
    foreach($site_start_time as $key => $value)
    {
        if($key>9)
        {
            break;//��ֹ���ι���
        }
        $table_data[$key]['begin_time'] = strtotime(trim($site_start_time[$key]));
        $table_data[$key]['end_time'] = strtotime(trim($site_end_time[$key]));
        $table_data[$key]['num'] = $site_join_num[$key];
        $table_data[$key]['table_id'] = (int)$table_id[$key];
        
        //��ȡ���һ��ʱ��
        $end_time = $site_end_time[$key];
    }

    $data['start_time'] = strtotime(trim($site_start_time[0]));//��һ����ʼʱ��
    $data['end_time'] = strtotime(trim($end_time));//���һ������ʱ��
    
    //��������
    $data['table_data'] = serialize($table_data);

/********10��13���޸�ʱ��*****************/

//�����޸�ʱ���ɹٷ�
if($act=="add" || $act=="preview")
{
    $data['is_authority'] = $admin_user_obj->get_is_authority_by_user_id($login_id,$data['type_icon']);		//��������ǹٷ�����
}
//���Ե��������ߺ��
$system_obj = POCO::singleton('event_system_class');

$data       = $system_obj->merge_extra_input_data($data,$data['type_icon']);       //��ȡ���������




/******2015-1-28********/
//����ID����
$relate_user_obj = POCO::singleton ('pai_relate_poco_class');
$yue_user_id = $relate_user_obj->get_relate_yue_id($login_id);
$relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
$org_info = $relate_org_obj->get_org_info_by_user_id($yue_user_id);
$data['org_user_id'] = (int)$org_info['org_id'];
/******2015-1-28********/
//���⴦������ʹ�ã�֮���ɾ
$match_publish_arr = array(66096046,175335503,65804368,174291459,177149128);
if(in_array($login_id,$match_publish_arr))
{
    $data['type_icon'] = 'yuepai';
}
//���⴦������ʹ�ã�֮���ɾ


if($act=="add")
{
    
    $data['new_version'] = 2;
        

    echo "���ڷ���...";
    $check_id = $event_check_obj->add_event($data, 0);
    if(!empty($check_id))
    {
        echo '<SCRIPT LANGUAGE="JavaScript">parent.location="http://event.poco.cn/event_audit.php?c_id='.$check_id.'"</SCRIPT>';
    }

    
}
else if($act=="update")
{
    
    echo "�༭�ύ��...";
    
    if(empty($event_id))
    {
        echo '<SCRIPT LANGUAGE="JavaScript">alert("�IDΪ�գ�����");parent.location="http://event.poco.cn/"</SCRIPT>';
    }
    
    //����Ƿ�����ʽ��
    $details_info = $event_details_obj->get_event_by_event_id($event_id);
    if(empty($details_info))
    {

        echo '<SCRIPT LANGUAGE="JavaScript">alert("�ID����");parent.location="http://event.poco.cn/event_list.php"</SCRIPT>';
        exit();
    }
    
    if($details_info['user_id']!=$login_id)
    {
        //�ж��Ƿ������Ա
        $webcheck_patch = "/disk/data/htdocs232/poco/webcheck/";
        include_once $webcheck_patch."admin_function.php";
        $is_event_admin = admin_chk("event", "admin",  $login_id); //�Ƿ����Ա
        if(!$is_event_admin)
        {
            //header("location:event_list.php");
            
            echo '<SCRIPT LANGUAGE="JavaScript">alert("�����ǻ�����߻��߹���Ա");parent.location="http://event.poco.cn/event_list.php"</SCRIPT>';
            exit();
        }
        
    }
    
    
    $data['last_update_time']=$details_info['last_update_time'];
    $check_info = $event_check_obj->get_event_by_event_id($event_id);
    if(empty($check_info))
    {
        
        echo '<SCRIPT LANGUAGE="JavaScript">alert("��˱�û�м�¼����������");parent.location="http://event.poco.cn/event_list.php"</SCRIPT>';
        exit();
    }
    
    $check_id = $check_info['check_id'];
    $ret=$event_check_obj->update_event($data,$check_id);				//������˱�
    if($ret)
    {
       
        echo '<SCRIPT LANGUAGE="JavaScript">parent.location="http://event.poco.cn/event_audit.php?c_id='.$check_id.'&edit=1"</SCRIPT>';
        exit();
    }
    else
    {
        echo '<SCRIPT LANGUAGE="JavaScript">alert("���³������Ժ�����");parent.location="http://event.poco.cn/event_list.php"</SCRIPT>';
        exit();
    }
    

     
    
    
}
else if($act=="preview")
{
    
    echo "��������Ԥ��ҳ��";
    //������
    $data['cache_table_arr'] = $table_data;
    
    
    $time_mark_value = date("Ymdhis",time());
    $cache_key = "event_preview_".$login_id."_".$time_mark_value;
    $set_cache_res = POCO::setCache($cache_key,$data, array('life_time'=>864000));
    if($set_cache_res)
    {
        echo "ҳ����ת��...";
        //header("Location:http://event.poco.cn/event_browse.php?user_id=".$data['user_id']."&time=".$time_mark_value."&act=preview");
        
        echo '<SCRIPT LANGUAGE="JavaScript">parent.location="http://event.poco.cn/event_browse.php?user_id='.$login_id.'&time='.$time_mark_value.'&act=preview"</SCRIPT>';
    }
    else
    {
        echo"�����������Ժ�����";
        die();
    }
}

?>