<?php
/** 
 * 
 * ���������ҳ
 * 
 * author ����
 * 
 * 
 * 2015-3-9
 * 
 * 
 */
 
 
 
 
 
include_once("./party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
//������ֲ��ļ� $_local_club_array
include_once("/disk/data/htdocs232/photo/photo_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$relate_obj = POCO::singleton ('pai_relate_poco_class');

//ȡ��ģ�����
$tpl = $my_app_pai->getView('party_publish.tpl.htm');


//���ص�url
$redirect_url='event_list.php';
$__mp_manage_branch = 1;//������Ϊ֧���棬�ǿ�����Ϊ��֧��
if(empty($yue_login_id))
{
    //û��¼ȥ��¼
    $REFERERURL = "http://".$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI'];     
    //exit();
    $redirect_url="http://www.yueus.com/reg/login.php?r_url=".urlencode($REFERERURL);
    header("location:$redirect_url");
}

//��ѯ��Ӧ��poco_id
if(!empty($yue_login_id))
{
    $poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);
}





//ȡ��Ӧ�ò�������Ψһʵ��
$details_obj = POCO::singleton('event_details_class');
$system_obj = POCO::singleton('event_system_class');
$event_table_obj = POCO::singleton('event_table_class');

//��������
$type_name_arr = $system_obj->get_status_name_array_by_name('type');


$event_id = (int)$_INPUT['event_id'];
$category = (int)$_INPUT['category'];//��������

//�ж��Ƿ�ٷ�ID
$admin_user_obj = POCO::singleton('event_admin_user_class');
$is_authority = $admin_user_obj->get_is_authority_by_user_id($poco_login_id);		//�ж��Ƿ�ٷ�ID

if($is_authority)
{
    $tpl->assign('is_event_admin', 1);//��ֵ��ҳ��JS���ж�
}
else
{
    echo '<scrpit>alert("�ٷ����ܷ���")</scrpit>';
    header("location:http://www.yueus.com/topic/party_topic/");
}



if(!empty($event_id))//�޸Ļ
{
    
    
    //�жϻ�Ƿ��Ѿ�����
    $event_info=$details_obj->get_event_by_event_id($event_id);
    
    //�ж��ܷ�༭
    if($event_info['event_status']>0)
    {
        $is_event_admin = $system_obj->check_is_admin($poco_login_id);
        if(!$is_event_admin)
        {
            echo '<scrpit>alert("�û�Ѿ�����")</scrpit>';
            header("location:$redirect_url");
            die();
        }
    }
    
    
    
    
    
    if(empty($event_info))
    {
        header("location:$redirect_url");
    }
    if($event_info['user_id']!=$poco_login_id)
    {
        //�ж��Ƿ������Ա
        $is_event_admin = $system_obj->check_is_admin($poco_login_id);
        if(!$is_event_admin)
        {
            header("location:$redirect_url");
        }
    }
    
    //ȡ��ͬ���͵Ķ�Ӧ������
    $category = $event_info['category'];		//��ֵ��$category����������ȡ�����������
    if($category==3 || $category==1)
    {	
        //�󽱻����ǰ̨�޸�
        header("location:$redirect_url");
    }
    
    
    $event_info['type_name'] = $type_name_arr[$event_info['type_icon']];	//��ʾ�������
    if(empty($type_name_arr[$event_info['type_icon']]))//����
    {

        header("location:$redirect_url");
    }



    
    //����ͼ��ģ��
    $model_part_img_list = unserialize($event_info['other_info']);
    //���������Ϣ
    $leader_part_list = unserialize($event_info['leader_info']);
    //��������Ϣ
    $table_part_list = $event_table_obj->get_event_table($event_id);
    
    //�����
    $event_info['location_id1'] = 0;
    $event_info['location_id2'] = 0;
    $event_info['location_id3'] = 0;	
    if($event_info['location_id']!="")	
    {
        $location_id = $event_info['location_id'];
        $len = strlen($location_id);	
        if($len>=12)
        {
            $event_info['location_id1'] = substr($location_id,0,9);
            $event_info['location_id2'] = substr($location_id,0,12);
        }else{
            $event_info['location_id1'] = substr($location_id,0,6);
            $event_info['location_id2'] = substr($location_id,0,9);
        }
    }
    
    
    $setting_arr = unserialize($event_info['setting']);
 
    //ȡ��������ͼ
    $event_info['cover_image_145'] = poco_resize_act_img_url($event_info['cover_image'],145);
    

    //�Կ������ģ�����ݽ��з���ģ�崦��
    foreach($model_part_img_list as $key => $value)
    {
        $model_part_img_list[$key]['data_mark'] = ((int)$key)+1;
        $tmp_count = count($value['img']);
        if($tmp_count==5)
        {
            $model_part_img_list[$key]['fit_swf'] = true;
        }
        //����ͼƬ�ṹ
        if(!empty($value['img']))
        {
            foreach($value['img'] as $k => $v)
            {
                $model_part_img_list[$key]['img_arr'][$k]['img_value_145'] = poco_resize_act_img_url($v,145);
                $model_part_img_list[$key]['img_arr'][$k]['img_value'] = $v;
                $model_part_img_list[$key]['img_arr'][$k]['data_mark'] = $model_part_img_list[$key]['data_mark'];
                
                
            }
        }
        
        //$model_part_img_list[$key]['textarea_text'] = $value['text'];
        //$model_part_img_list[$key]['text'] = str_replace("\n","<br>",$value['text']);
        
        
        
    }
    
    
    $table_config_arr = array("��","һ","��","��","��","��","��","��","��","��","ʮ");
    foreach($table_part_list as $key => $value)
    {
        $table_part_list[$key]['data_mark'] = ((int)$key)+1;
        $table_part_list[$key]['site_name'] = $table_config_arr[$table_part_list[$key]['data_mark']];//��Ӧ������
        /********10��13���޸�ʱ��*****************/
        $table_part_list[$key]['begin_time'] = date("Y-m-d H:i",$value['begin_time']);
        $table_part_list[$key]['end_time'] = date("Y-m-d H:i",$value['end_time']); 
        /********10��13���޸�ʱ��*****************/
        
        
    }
    
    //�������
    foreach($leader_part_list as $key => $value)
    {
        $leader_part_list[$key]['data_mark'] = ((int)$key)+1;
        if($key+1==count($leader_part_list))
        {
            $leader_part_list[$key]['the_end_one'] = true;
        }
    }
    
    
    //����ͼ��item_id
    if(!empty($setting_arr['cover_image_item_id']))
    {
        $event_info['cover_image_item_id'] = $setting_arr['cover_image_item_id'];
    }
        
    
    
    $tpl->assign($event_info);
    $tpl->assign('act', 'update');
    $tpl->assign('model_part_img_list',$model_part_img_list);//ͼ��ģ��
    $tpl->assign('table_part_list',$table_part_list);//����ģ��
    $tpl->assign('leader_part_list',$leader_part_list);//���ģ��
    $tpl->assign('model_part_img_list_count_add',count($model_part_img_list)+1);
    $tpl->assign('table_part_list_count_add',count($table_part_list)+1);
    $tpl->assign('leader_part_list_count_add',count($leader_part_list)+1);
    
}
else//���
{
    
    
    //�������ϻ����»�ķ��������޸ģ�//Ĭ��Ϊ����
    $category = 2;
    

    $tpl->assign('category', $category);
    
    //�����
    $location_id1 = 0;
    $location_id2 = 0;
    $location_id3 = 0;	
    if($category==2)
    {
        if($POCO_EVENT_LOCATION_ID!="")	
        {
            $location_id = $POCO_EVENT_LOCATION_ID;
            $len = strlen($location_id);	
            if($len>=12)
            {
                $location_id1 = substr($location_id,0,9);
                $location_id2 = substr($location_id,0,12);
            }else{
                $location_id1 = substr($location_id,0,6);
                $location_id2 = substr($location_id,0,9);
            }
        }
    }
    $tpl->assign('location_id1',$location_id1);
    $tpl->assign('location_id2',$location_id2);
    $tpl->assign('location_id3',$location_id3);	
    //Ĭ������ʱ��
    $tpl->assign('start_time', time());
    $tpl->assign('end_time', time());
    
    //�Ӳ�ͬ�������뷢��ҳʱ��Ĭ�Ϸ����������������ͻ
    $type_icon = trim($_INPUT['type_icon']);
    if(!empty($type_icon))
    {
        $tpl->assign('type_icon',$type_icon);
    }


    
    $tpl->assign('act','add');

}

//��������


    //�Ƿ���ʾ���ʾ
    if (isset($_COOKIE["event_edit_tips_close_".$category]) && $_COOKIE["event_edit_tips_close_".$category]==$yue_login_id)
    {
        setcookie("event_edit_tips_close_".$category, $yue_login_id, time()+2400*3600,"/", ".poco.cn");
        $tpl->assign('event_edit_tips_close', "Y");
    }

    //����JS��ʽ
    $wdate = "WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',isShowClear:false})";//������ʼʱ��
    //$hm_wdate = "WdatePicker({dateFmt:'HH:mm',isShowClear:false})";//ʱ�֣����ڳ���
    $hm_wdate = "WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',isShowClear:false})";//ʱ�֣����ڳ���

    

    $category_name_arr = $system_obj->get_status_name_array_by_name('category');
//�����������

$tpl->assign('__mp_manage_branch',$__mp_manage_branch);
$tpl->assign('is_authority', $is_authority);

//���ڸ�ʽ
$tpl->assign('wdate', $wdate);
$tpl->assign('hm_wdate', $hm_wdate);

$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$header_html = $my_app_pai->webControl('PartyHeader', array(), true);
$footer_html = $my_app_pai->webControl('PartyFooter', array(), true);





$tpl->assign("rand",time());
$tpl->assign('global_header_html', $global_header_html);
$tpl->assign('header_html', $header_html);
$tpl->assign('footer_html', $footer_html);

$tpl->output();
?>