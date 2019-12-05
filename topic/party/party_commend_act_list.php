<?php
/**
 * �û����Ʒ�б�ҳ
 * @author  tom 
 */

//����Ӧ�ù����ļ�
define("G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK",1);
//define("G_DB_GET_REALTIME_DATA", 1 );
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$pai_config_obj = POCO::singleton ( 'pai_config_class' );//������
$waipai_arr = $pai_config_obj->big_waipai_event_id_arr('one_waipai');//������

//ȡ��ģ�����
$tpl = $my_app_pai->getView('party_commend_act_list.tpl.htm');

$details_obj = POCO::singleton('event_details_class');
$cmd_obj = POCO::singleton('event_commend_act_class');
$system_obj  = POCO::singleton('event_system_class');
$enroll_obj  = POCO::singleton('event_enroll_class');
$activity_code_obj = POCO::singleton('pai_activity_code_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');

//����
$bind_obj = POCO::singleton ( 'pai_bind_poco_class' );

$category_name_arr=$system_obj->get_status_name_array_by_name('category');
$type_name_arr=$system_obj->get_status_name_array_by_name('type');
$event_id=(int)$_INPUT['event_id'];


if(empty($event_id))
{
    header("location:event_list.php");
}
$event_info = $details_obj->get_event_by_event_id($event_id);
if(empty($event_info))
{
    header("location:event_list.php");
}
//����ҵ�ʱ��û�д�ҳ
if($event_info['category']==3)
{
    header("location:event_detail.php?event_id={$event_id}");
}


//��������ת�����ߺ�ӻ�
if(!in_array($event_id,$waipai_arr))//��������ת
{
    header("location:http://event.poco.cn/commend_act_list.php?event_id={$event_id}");
} 



//���ӵ����
//$details_obj->add_hit_count($event_id, 1, true);
    if (rand(1, 15) == 1)
    {
        $hit_count = $details_obj->add_hit_count2($event_id, 1, true,false,true);
    }else{
        $hit_count = $details_obj->add_hit_count2($event_id, 1, true,false,false);
    }
    $event_info['hit_count'] = $hit_count;

//����ڳ���
$tpl->assign('location_name', $location_name);
//��Ӧ���Ʒ
if($where_str !='') $where_str.=" AND ";
$where_str.="event_id = {$event_id}";
//�ж��Ƿ������Ա

//��ѯ��Ӧ��poco_id
if(!empty($yue_login_id))
{
    $poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);
}
else
{
    $poco_login_id = 0;
}

$is_event_admin = $system_obj->check_is_admin($poco_login_id);
if($is_event_admin)
{
    $tpl->assign('is_event_admin', 1);
    $event_info['is_event_user']=1;
}else{
    //�Ƿ񷢲���
    if($event_info['user_id']==$poco_login_id)
    {
        $event_info['is_event_user']=1;
    }
}
if($event_info['is_event_user']!=1 || !$is_event_admin)
{
    if($where_str !='') $where_str.=" AND ";
    $where_str.="is_del = '0'";
}

//�Ƿ���ʾ�Ƽ���ʾ
if (isset($_COOKIE["event_close_recommend_tips"]) && $_COOKIE["event_close_recommend_tips"]==$poco_login_id)
{
    setcookie("event_close_recommend_tips", $poco_login_id, time()+2400*3600,"/", ".poco.cn");
    $event_close_recommend_tips = 1;
}

//��ʾ��Ʒ��
$cmd_no_recommend_count = $cmd_obj->get_cmd_list_by_event_id($event_id,'0',true,"");	//û�Ƽ���Ʒ����
$cmd_recommend_count = $cmd_obj->get_cmd_list_by_event_id($event_id,'1',true,"");		//�Ƽ���Ʒ����
$cmd_count = $cmd_no_recommend_count + $cmd_recommend_count;							//��Ʒ����
$tpl->assign('cmd_no_recommend_total_count', $cmd_no_recommend_count);	
$tpl->assign('cmd_recommend_total_count', $cmd_recommend_count);	
$tpl->assign('cmd_total_count', $cmd_count);	
//�������
if($event_info['category']!="")
$event_info['category_name'] = $category_name_arr[$event_info['category']];
//�Ƿ��Ѿ��л�ع�
if($event_info['event_review']!="" && $event_info['review_time']>0)
$event_info['review_button']="�޸Ĵ˻�ع�";
else
$event_info['review_button']="��Ӵ˻�ع�";
//���������
$event_info['type_name'] = $type_name_arr[$event_info['type_icon']];



$tpl->assign($event_info);
    
//��ʼ����ҳ
$page_obj =POCO::singleton('show_page'); 
$show_count = 20;//ÿҳ��ʾ��
$page_setvar = array('res_name'=>$res_name);


//���ӷ�ҳ����
$page_setvar['event_id'] = $event_id;

// ɸѡ��ͬ���Ļ		�Ƽ� / ���Ƽ�
$type=trim($_INPUT['search_type']);
if ($type !== '')
{
    if (in_array($type, array("0","1"))) 
    {
        if($where_str !='') $where_str.=" AND ";
        $where_str.="is_recommend = '{$type}'";
        //���ӷ�ҳ����
        $page_setvar['search_type'] = $type;
        //����ҳhidden�������ҳ
        $tpl->assign('search_type', $type);
    }
}

// ɸѡ��ͬ���Ļ		�Ƽ� / ���Ƽ�
$order_type=trim($_INPUT['order_type']);
if ($order_type != '')
{
    if (in_array($order_type, array("relate_time","hit_count","vote_count"))) 
    {
        $order_by = $order_type." DESC";
        //���ӷ�ҳ����
        $page_setvar['order_type'] = $order_type;
        //����ҳhidden�������ҳ
        $tpl->assign('order_type', $order_type);
    }else{
        $order_by = "relate_time DESC";
    }
}else{
    $order_by = "relate_time DESC";
}

$page_obj->setvar($page_setvar);
$total_count = $cmd_obj->get_cmd_list($where_str,true,'','');
$page_obj->file = '';
$page_obj->set($show_count, $total_count);
$limit_str = $page_obj->limit();
$cmd_list = $cmd_obj->get_cmd_list($where_str,false, $limit_str ,$order_by);

//--------------------------------------������ʾ��ʽ start --------------------------------------------//
$count = count($cmd_list);
for($i=0;$i<$count;$i++)
{
    if($cmd_list[$i]['img_url']!="")
        $cmd_list[$i]['img_url_165'] = $system_obj->get_small_image($cmd_list[$i]['img_url'], 165);//ȡ����ͼ
    else 
        $cmd_list[$i]['img_url_165'] =" http://www1.poco.cn/event/images/default.jpg";		
    //ÿ5����Ʒ���к����ul,div
    if ($i%5==0 && $i!=0)
        $cmd_list[$i]['div_html'] = '</ul></div><div class="d_line"></div><div class="show_list clearfix"><ul>';	
    /*	
    if($cmd_list[$i]['act_type_id']==3)
    {
        $cmd_list[$i]['click_url'] = "http://photo.poco.cn/lastphoto-htx-id-".$cmd_list[$i]['item_id']."-p-0.xhtml";
    }elseif($cmd_list[$i]['act_type_id']==6)
    {
        $cmd_list[$i]['click_url'] = "http://food.poco.cn/foodiaryDetail-htx-id-".$cmd_list[$i]['item_id'].".shtml";
    }elseif($cmd_list[$i]['act_type_id']==7)
    {
        
        $cmd_list[$i]['click_url'] = "http://food.poco.cn/commendDetail-htx-id-".$cmd_list[$i]['item_id'].".shtml";
    }
    */
    if($i==0 && empty($event_close_recommend_tips))
    {
        $cmd_list[$i]['recommend_tips'] = 1;
    }
}
//--------------------------------------������ʾ��ʽ end  ---------------------------------------------//
$tpl->assign('cmd_list', $cmd_list);


//����ϵͳ
$cmt_html = $system_obj->get_cmt_html($event_id,$event_info['title'],$event_info['user_id'],$event_info['category'],$event_info['status']);
$tpl->assign('cmt_html', $cmt_html);

//��鱨���Ƿ����
$date_return = $details_obj->check_date_is_over($event_id);	
$date_is_over = ($date_return)?1:'';
$tpl->assign('date_is_over', $date_is_over);

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





//����Ƿ��Ѿ�����30��
$days = G_POCO_EVENT_MAX_DAY;
$check_event_over_some_days = $details_obj->check_event_over_some_days($event_id,$days);
if($check_event_over_some_days)
{
    $tpl->assign('check_event_over_some_days', 1);	
}

//�Ƿ�չʾ�绰�������
$setting = unserialize($event_info['setting']);	
$is_show_phone_form = $setting['is_show_phone_form'];
$tpl->assign('is_show_phone_form', $is_show_phone_form);	




//���ԼID�����
//�󶨽ṹ��ʱ��ע�ͣ����󶨵����
/* if($event_info['new_version']==2)
{
    if(!empty($poco_login_id))
    {
        $relate_yue_id = $relate_obj->get_relate_yue_id($poco_login_id);

        //��ȡԼԼPC��󶨵�POCOID
        $poco_id = $bind_obj->get_bind_poco_id($relate_yue_id);
        if($poco_id || $relate_yue_id)//��ʾ�Ѿ���
        {
            $bind_link = 0;
        }
        else
        {
            $bind_link = 1;
        }

    }
} */
//���ԼID�����


$cur_url= "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$encode_link = urlencode($cur_url);
$tpl->assign("bind_link",$bind_link);
$tpl->assign("encode_link",$encode_link);
//�󶨽ṹ

//����Ʒ����
if(!empty($event_id))
{
    $encode_tag = urlencode("ԼԼ��Ʒ");
    $publish_article_link = "http://my.poco.cn/blog_v2/publish.php?publish_type=photo&init_tag=".$encode_tag."&event_id=0&best_pocoer_type_id=";
}
else
{
    $publish_article_link = "javascript:void(0);";
} 



//ȡ��POCOĬ��ͷβ
$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$header_html = $my_app_pai->webControl('PartyHeader', array(), true);
$footer_html = $my_app_pai->webControl('PartyFooter', array(), true);



$tpl->assign('publish_article_link',$publish_article_link);
$tpl->assign('page_title', G_POCO_EVENT_PAGE_TITLE);
$tpl->assign('G_POCO_EVENT_TYPE_CHANNEL', G_POCO_EVENT_TYPE_CHANNEL);	

$tpl->assign('global_header_html', $global_header_html);
$tpl->assign('header_html', $header_html);
$tpl->assign('footer_html', $footer_html);
$page_select = str_replace('&nbsp;', '', $page_obj->output_pre10.$page_obj->output_pre.$page_obj->output_page.$page_obj->output_back.$page_obj->output_back10);
$page_select = str_replace('color:red','color:#737373',$page_select);
$tpl->assign("page_select",$page_select);//��ҳ
$tpl->assign("total_count",$total_count);//��ҳ
$tpl->assign("rand",time());

$tpl->output();

?>