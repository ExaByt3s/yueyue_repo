<?php
/** 
 * 
 * �֧���ɹ������ѱ����б�ҳ
 * 
 * author ����
 * 
 * 
 * 2015-3-3
 * 
 * 
 */

define("G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK",1);
define("G_DB_GET_REALTIME_DATA",1);
include_once("./party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//$__mp_manage_branch = G_POCO_EVENT_PAY;//������Ϊ֧���棬�ǿ�����Ϊ��֧��
$__mp_manage_branch = 1;//������Ϊ֧���棬�ǿ�����Ϊ��֧��


$event_id = (int)$_INPUT['event_id'];
$is_pay_return = (int)$_COOKIE['is_pay_return'];//�ж��Ƿ�֧���ɹ���ķ���
$enroll_res = (int)$_COOKIE['enroll_res'];//��֧����ı������
//ȡ��ģ�����
$tpl = $my_app_event->getView('party_sign_list.tpl.htm');

//��ȡ��
$details_obj = POCO::singleton('event_details_class');
$system_obj  = POCO::singleton('event_system_class');
$event_table_obj    = POCO::singleton('event_table_class');
$enroll_obj     = POCO::singleton('event_enroll_class');
$activity_code_obj = POCO::singleton('pai_activity_code_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');
$pai_user_obj = POCO::singleton('pai_user_class');
$pai_user_icon_obj = POCO::singleton('pai_user_icon_class');

//��������
$category_name_arr=$system_obj->get_status_name_array_by_name('category');
$type_name_arr=$system_obj->get_status_name_array_by_name('type');

//�ж������Ƿ����Ҫ��
if(empty($event_id))
{
    header("location:http://event.poco.cn/event_list.php");
}
$event_info = $details_obj->get_event_by_event_id($event_id);

if(empty($event_info))
{
    header("location:http://event.poco.cn/event_list.php");
}
//����ҵ�ʱ��û�д�ҳ
if($event_info['category']==3)
{
    header("location:http://event.poco.cn/event_detail.php?event_id={$event_id}");
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

//��Ĺٷ�����˳��
$setting = unserialize($event_info['setting']);

//��ȡ�����б�
$event_table = $event_table_obj->get_event_table($event_id);
$table_config_arr = array("��","һ","��","��","��","��","��","��","��","��","ʮ");

//�жϼ���Ƿ�����˳�����
$can_logout_enroll = $details_obj->check_logout_enroll($event_id);//��ʱ�����жϸû�ܷ��˳�
if($can_logout_enroll) 
{
    $can_logout_enroll = 1;
}
else
{
    $can_logout_enroll = 0;
}


$show_count = 20;//ÿҳ��ʾ��



//����μ��б�ṹ
foreach($event_table as $k=>$val){
    $event_table[$k]['data_mark'] = ((int)$k)+1;
    $event_table[$k]['site_name'] = $table_config_arr[$event_table[$k]['data_mark']];//��Ӧ������
    $table_id = $val['id'];
    $event_table[$k]['table_id'] = $val['id'];
    
    
    //ǩ��
    $checked_str = "event_id = {$event_id} AND table_id = {$table_id}";
    $checked_tmp_list = $enroll_obj->get_enroll_list($checked_str,false,"");
    $implode_arr = array();
    foreach($checked_tmp_list as $key => $value)
    {
        $implode_arr[] = $value['enroll_id'];
    }
    $implode_str = implode(",",$implode_arr);
    $checked_total_count = $activity_code_obj->count_code_is_checked(true,$implode_str,$limit='0,10',false);
    
    
    $check_return_arr = consturct_page_select($show_count,$checked_total_count,"checked",$table_id,$event_id);
    $checked_limit_str = $check_return_arr[0];
    
    /* if($yue_login_id==100021)
    {
        echo $checked_total_count;
    }  */
    
    
    
    $checked_tmp = $activity_code_obj->count_code_is_checked(false,$implode_str,$checked_limit_str);

    
    foreach($checked_tmp as $key => $value)
    {
        $enroll_info = $enroll_obj->get_enroll_by_enroll_id($value['enroll_id']);
        $checked[$key]['user_id'] = $enroll_info['user_id'];
        
        $tmp_yue_id = $relate_obj->get_relate_yue_id($enroll_info['user_id']);
        $checked[$key]['user_name'] = poco_cutstr($pai_user_obj->get_user_nickname_by_user_id($tmp_yue_id),20);
        $checked[$key]['user_icon'] = $pai_user_icon_obj->get_user_icon($tmp_yue_id, 64);

        
        
        $checked[$key]['enroll_num'] = $value['c'];
    }
    
    
    $checked_page_select = $check_return_arr[1];
    //��ǩ��ʵ������
    if(!empty($implode_str))
    {
        $check_count_where = "enroll_id IN ({$implode_str}) AND is_checked = 1";
        $check_count = $activity_code_obj->get_code_list(true,$check_count_where);
    }
    else
    {
        $check_count = 0;
    }
    
    
    
    $event_table[$k]['checked_count'] = $check_count;
    $event_table[$k]['checked'] = $checked;
    unset($checked);
    $event_table[$k]['checked_page_select'] = $checked_page_select;

    
    
    
    //��ѡ
    
    $first_str = "status = 0 AND event_id = {$event_id} AND table_id = {$table_id}";
    $first_total_count = $enroll_obj->get_enroll_list($first_str,true);
    $first_return_arr = consturct_page_select($show_count,$first_total_count,"first",$table_id,$event_id);
    $first_limit_str = $first_return_arr[0];
    $first = $enroll_obj->get_enroll_list_and_event_info(array("status"=>0,"event_id"=>$event_id,"table_id"=>$table_id), $type_icon = $event_info['type_icon'], $first_limit_str,"enroll_id asc");
    
    foreach($first as $key => $value)
    {
        $tmp_yue_id = $relate_obj->get_relate_yue_id($value['user_id']);
        $first[$key]['user_name'] = poco_cutstr($pai_user_obj->get_user_nickname_by_user_id($tmp_yue_id),20);
        $first[$key]['user_icon'] = $pai_user_icon_obj->get_user_icon($tmp_yue_id, 64);
        
        //����Ƿ��б�ɨ�룬�򲻳�ɾ��
        $first[$key]['scan_res'] = $activity_code_obj->check_code_scan($value['enroll_id']);
        
    }
    


    $first_page_select = $first_return_arr[1];
    
    $event_table[$k]['first'] = $first;
    $event_table[$k]['first_count'] = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(0),$table_id);
    $event_table[$k]['first_page_select'] = $first_page_select;
    

    
    //������
    
    $backup_str = "status = 1 AND event_id = {$event_id} AND table_id = {$table_id}";
    $backup_total_count = $enroll_obj->get_enroll_list($backup_str,true);
    $backup_return_arr = consturct_page_select($show_count,$backup_total_count,"backup",$table_id,$event_id);
    $backup_limit_str = $backup_return_arr[0];

    $backup = $enroll_obj->get_enroll_list_and_event_info(array("status"=>1,"event_id"=>$event_id,"table_id"=>$table_id), $type_icon = $event_info['type_icon'], $backup_limit_str,"enroll_id asc");
    

    
    
    
    foreach($backup as $key => $value)
    {
        $tmp_yue_id = $relate_obj->get_relate_yue_id($value['user_id']);
        $backup[$key]['user_name'] = poco_cutstr($pai_user_obj->get_user_nickname_by_user_id($tmp_yue_id),20);
        $backup[$key]['user_icon'] = $pai_user_icon_obj->get_user_icon($tmp_yue_id, 64);
        
        //����Ƿ��б�ɨ�룬�򲻳�ɾ��
        $backup[$key]['scan_res'] = $activity_code_obj->check_code_scan($value['enroll_id']);

    }
    

    $backup_page_select = $backup_return_arr[1];
    
    $event_table[$k]['backup'] = $backup;
    $event_table[$k]['backup_count'] = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(1),$table_id);
    $event_table[$k]['backup_page_select'] = $backup_page_select;
    
    
    
    //����
    $onlooker_str = "status = 3 AND event_id = {$event_id} AND table_id = {$table_id}";
    $onlooker_total_count = $enroll_obj->get_enroll_list($onlooker_str,true);
    $onlooker_return_arr = consturct_page_select($show_count,$onlooker_total_count,"onlooker",$table_id,$event_id);
    $onlooker_limit_str = $onlooker_return_arr[0];
    
    $onlooker = $enroll_obj->get_enroll_list_and_event_info(array("status"=>3,"event_id"=>$event_id,"table_id"=>$table_id), $type_icon = $event_info['type_icon'], $onlooker_limit_str,"enroll_id asc");
    
    
    foreach($onlooker as $key => $value)
    {
        $tmp_yue_id = $relate_obj->get_relate_yue_id($value['user_id']);
        $onlooker[$key]['user_name'] = poco_cutstr($pai_user_obj->get_user_nickname_by_user_id($tmp_yue_id),20);
        $onlooker[$key]['user_icon'] = $pai_user_icon_obj->get_user_icon($tmp_yue_id, 64);
        
        $check_allow = false;
        
        if($setting['enroll_mode']==1)
        {
            $check_allow = false;
        }
        else
        {
            
        
            if($event_info['join_mode']==0)
            {
                
                $check_allow = true;
                
                
            }
            else if($event_info['join_mode']==1)
            {
                $return = POCO::execute(array('friends.check_friend_exists',$event_info['user_id']), array($value['user_id']));
                if(!$return)
                {
                    //����Ǻ��Ѿ͵���3  Ϊ������
                    $check_allow = false;
            
                }
                else
                {
                    
                    $check_allow = true;
                    
                }
                    
            }
            else if($event_info['join_mode']==3)
            {
                $type_icon   = $event_info['type_icon'];
                $credit_info = POCO::execute('credit_system.credit_system_get_user_all_info', array($value['user_id']));
                $join_level  = (int)$event_info['join_ids'];
                if($type_icon=="photo" || $type_icon=="food")
                {

                    $point_info = $credit_info[$type_icon];
                    if($point_info['level']<=$join_level)
                    {

                        $check_allow = false;
                    
                    }
                    else
                    {
                        
                        $check_allow = true;
                        
                    }
                
                }
                else{

                    $check_allow = true;

                }
            }
        }   
        
    
        if($check_allow)
        {
            $onlooker[$key]['check_allow'] = 1;
        }
        else
        {
            $onlooker[$key]['check_allow'] = 0;
        }

    }
    
    $onlooker_page_select = $onlooker_return_arr[1];
    
    $event_table[$k]['onlooker'] = $onlooker;
    $event_table[$k]['onlooker_count'] = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(3),$table_id);
    $event_table[$k]['onlooker_page_select'] = $onlooker_page_select;
    
    
    
    
    //����Ƿ��Ѿ��μ���

    $enroll_info = $enroll_obj->get_enroll_info_by_event_id_and_user_id_and_table_id($event_id,$poco_login_id,$table_id);

    if(!empty($poco_login_id))
    {
        if(!empty($enroll_info))
        {
            $event_table[$k]['have_enroll'] = true;
            //����Ƿ�ɨ����
            $scan_res = $activity_code_obj->check_code_scan($enroll_info['enroll_id']);
            if($scan_res)
            {
                $scan_res = 1;
            }
            else
            {
                $scan_res = 0;
            }
            $event_table[$k]['scan_res'] = $scan_res;
        }
        $event_table[$k]['can_logout_enroll'] = $can_logout_enroll;
        
    }
    

    //���ɶ�ά��
    //�жϵ�ǰ���ⳡ�Ƿ��Ǯ��
    
    /* if($enroll_info['pay_status']==1)
    {
        //���ɶ�ά��
        
        
        $code_obj     = POCO::singleton('pai_activity_code_class');
        $tmp_qr_url_list = $code_obj->create_qr_code($event_id,$enroll_info['enroll_id']);
        foreach($tmp_qr_url_list as $key => $value)
        {
            $qr_url_list[$key]['qr_url'] = $value;
        }
        

        //��ȡ���
        $event_code_list = $code_obj->get_code_by_enroll_id_by_status($enroll_info['enroll_id'],0);
        

        
                        
        $event_table[$k]['qr_url_list'] = $qr_url_list;
        $event_table[$k]['event_code_list'] = $event_code_list;
        $tpl->assign("pay_status",$enroll_info['pay_status']);
        unset($qr_url_list);
    } */

    
    
    //������
    
    
    $event_table[$k]['table_total_enroll_count'] = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(0,1),$table_id);
    
        
 
}



//�������
if($event_info['category']!="")
$event_info['category_name'] = $category_name_arr[$event_info['category']];


//�����
$cmt_html = $system_obj->get_cmt_html($event_id,$event_info['title'],$event_info['user_id'],$event_info['category'],$event_info['status']);
$tpl->assign('cmt_html', $cmt_html);


//�жϵ�¼�˵Ľ�ɫ
if(!empty($poco_login_id))
{
    define("G_DB_GET_REALTIME_DATA", 1 );
    if($event_info['user_id']==$poco_login_id)
    {
        $event_info['event_user_role'] = "author";
        $event_info['is_event_user']=1;
        //�ж��Ƿ������Ա
        $is_event_admin = $system_obj->check_is_admin($poco_login_id);
        if($is_event_admin)
        {
            $event_info['event_user_role'] = "admin";
            $tpl->assign('is_event_admin', 1);
            $event_info['is_event_user']=1;
        }
    }
    else
    {
        //�ж��Ƿ������Ա
        $is_event_admin = $system_obj->check_is_admin($poco_login_id);
        if($is_event_admin)
        {
            $event_info['event_user_role'] = "admin";
            $tpl->assign('is_event_admin', 1);
            $event_info['is_event_user']=1;
        }
    }
}

//����Ƿ���ֱ�����ť
if($poco_login_id!=$event_info['user_id'])
{
    $tpl->assign("is_not_author",1);
}

//�������
if($event_info['category']!="")
{
    $event_info['category_name'] = $category_name_arr[$event_info['category']];
}

//�Ƿ��Ѿ��л�ع�
if($event_info['event_review']!="" && $event_info['review_time']>0)
{
    $event_info['review_button']="�޸Ĵ˻�ع�";
}
else
{
    $event_info['review_button']="��Ӵ˻�ع�";
}



//���췢���Ʒ����
if(!empty($event_id))
{
    $encode_tag = urlencode("ԼԼ��Ʒ");
    $publish_article_link = "http://my.poco.cn/blog_v2/publish.php?publish_type=photo&init_tag=".$encode_tag."&event_id=0&best_pocoer_type_id=";
}
else
{
    $publish_article_link = "javascript:void(0);";
} 

//����Ƿ��Ѿ�����30��
$days = G_POCO_EVENT_MAX_DAY;
$check_event_over_some_days = $details_obj->check_event_over_some_days($event_id,$days);
if($check_event_over_some_days)
{
    $tpl->assign('check_event_over_some_days', 1);	
}


//�ж��Ƿ���һ������ѡ
/* $where_str = "event_id = {$event_id} AND user_id = {$poco_login_id}";
$enroll_info_list = $enroll_obj->get_enroll_list($where_str, $b_select_count = false, $limit = '0,10', $order_by = 'enroll_id DESC',$fields="*");
foreach($enroll_info_list as $key => $value)
{
    if($value['status']=="0")
    {
        //����ѡ
        $tpl->assign("is_enroll",1);
    }
} */

//��¼�û��Ƿ��Ѿ�����
if(!empty($poco_login_id))
{
    $tpl->assign('login_id', $poco_login_id);
    //֧���Ļ�ж��Ƿ��Ѿ�ǩ�����������ť
    if($event_info['new_version']==2)
    {
        
        $scan_yue_login_id = $relate_obj->get_relate_yue_id($poco_login_id);
        $scan_res = $activity_code_obj->check_user_event_code_scan($event_id,$scan_yue_login_id);
        if($scan_res)
        {
            $tpl->assign('is_enroll', 1);
            
        }
    }
    else
    {
        $is_enroll = $enroll_obj->check_duplicate($poco_login_id,$event_id,"0");
        if($is_enroll)
            $tpl->assign('is_enroll', 1);
    }
    
        
}
//2015-3-24�������֯�߿���Ҳͬ�����ַ�����ť
if($event_info['user_id']==$poco_login_id)
{
    $tpl->assign('is_enroll', 1);
}







/********10��13���޸�ʱ��*****************/
foreach($event_table as $key => $value)
{
    $event_table[$key]['begin_time'] = date("m��d�� H:i",$value['begin_time']);
    //�ж��¸����Ƿ�ͬһ��
    $tmp_begin_day = date("md",$value['begin_time']);
    $tmp_end_day = date("md",$value['end_time']);
    if($tmp_begin_day==$tmp_end_day)
    {
        $event_table[$key]['end_time'] = date("H:i",$value['end_time']);
    }
    else
    {
        $event_table[$key]['end_time'] = date("m��d�� H:i",$value['end_time']);
    }
    unset($tmp_begin_day);
    unset($tmp_end_day);

} 

//ֻ��һ�������⴦��
$event_table_count = count($event_table);
if($event_table_count==1)
{
    $tpl->assign("only_one_site",true);
}
/********10��13���޸�ʱ��*****************/


//�����Ƿ�Լ�Ļ���һԪapp����
//�����Ƿ�Լ�Ļ���һԪapp����
$pai_config_obj = POCO::singleton ( 'pai_config_class' );
$waipai_arr = $pai_config_obj->big_waipai_event_id_arr('one_waipai');
if(in_array($event_id,$waipai_arr))
{
    $yue_type = "one_yuan_party";
}
else
{
    $yue_type = "big_party";
}
//�����Ƿ�Լ�Ļ���һԪapp����

//��鵱ǰ��Ƿ��ڴ����Ļ���һԪ����
$belong_waipai_arr = $pai_config_obj->big_waipai_event_id_arr('one_waipai');
if(!in_array($event_id,$belong_waipai_arr))
{
    header("location:http://event.poco.cn/event_sign_list.php?event_id={$event_id}");
}



//���������
$event_info['type_name'] = $type_name_arr[$event_info['type_icon']];
$tpl->assign('page_title', G_POCO_EVENT_PAGE_TITLE);

$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$header_html = $my_app_event->webControl('PartyHeader', array(), true);
$footer_html = $my_app_event->webControl('PartyFooter', array(), true);
$partyenroll_container = $my_app_event->webControl('PartyEnroll_container', array("event_id"=>$event_id,"__mp_manage_branch"=>$__mp_manage_branch,"new_version"=>$event_info['new_version'],"yue_type"=>$yue_type),true);

$tpl->assign('publish_article_link',$publish_article_link);
$tpl->assign("__mp_manage_branch",$__mp_manage_branch);
$tpl->assign($event_info);
$tpl->assign('login_id', $poco_login_id);
$tpl->assign("event",$event_table);
$tpl->assign("rand",time());
$tpl->assign("enroll_res",$enroll_res);
$tpl->assign("is_pay_return",$is_pay_return);
$tpl->assign('global_header_html', $global_header_html);
$tpl->assign('partyenroll_container', $partyenroll_container);
$tpl->assign('header_html', $header_html);
$tpl->assign('footer_html', $footer_html);


$tpl ->assign("rand",201503091415);

$tpl->output();

//�����ҳ�Ľṹ����
function consturct_page_select($show_count,$total_count,$type,$table_id,$event_id)
{
    //static $time;
    //$time++;
   // if( $time==4 )
        //exit();
    //var_dump(get_defined_vars());
    //$page_obj =new show_page;
    $page_obj =POCO::singleton('show_page');
    $page_obj->output_pre10  = '';
    $page_obj->output_pre    = '';
    $page_obj->output_page   = '';
    $page_obj->output_back   = '';
    $page_obj->output_back10 = '';

    $page_obj->file = '';
    $page_obj->set($show_count, $total_count);
    $page_reg = '/href=(?:\"|)(.*)(\?|&)p=(\d+)(?:\"| )/isU';
    //var_dump($page_obj->tpage);
    $limit_str = $page_obj->limit();
    $page_select = str_replace('&nbsp;', '', $page_obj->output_pre10.$page_obj->output_pre.$page_obj->output_page.$page_obj->output_back.$page_obj->output_back10);
    //�첽�����ҳ
     
    preg_match_all($page_reg,$page_select,$data_arr);
    $page_arr =	$data_arr[3];
    foreach( $page_arr as $key=>$val ){	
        $page_select = str_replace($data_arr[0][$key],"href='javascript:void(0)' data-class=\"J_list_page_ajax\" data-type=\"{$type}\" data-table=\"{$table_id}\" data-event=\"{$event_id}\" data-page=\"{$val}\"  ",$page_select);	
    }
    $page_select = str_replace('color:red','color:#737373',$page_select);
    $return_arr = array($limit_str,$page_select);
   // dump($return_arr);
    return $return_arr;
    
}
?>