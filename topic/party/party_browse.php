<?php
/** 
 * 
 * ����ҳ
 * 
 * author ����
 * 
 * 
 * 2014-7-21
 * 
 * 
 */
include_once("./party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
//������ֲ��ļ� $_local_club_array
include_once("/disk/data/htdocs232/photo/photo_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//ȡ��ģ�����
//$__mp_manage_branch = G_POCO_EVENT_PAY;//������Ϊ֧���棬�ǿ�����Ϊ��֧��
$__mp_manage_branch = 1;//ȷ��Ϊ֧����
$tpl = $my_app_pai->getView('party_browse.tpl.htm');
$details_obj = POCO::singleton('event_details_class');
$event_table_obj = POCO::singleton('event_table_class');
$enroll_obj     = POCO::singleton('event_enroll_class');
$cmd_obj     = POCO::singleton('event_commend_act_class');
$system_obj  = POCO::singleton('event_system_class');
$check_obj = POCO::singleton('event_check_class');
$activity_code_obj = POCO::singleton('pai_activity_code_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');
$pai_user_obj = POCO::singleton('pai_user_class');
$pai_user_icon_obj = POCO::singleton('pai_user_icon_class');

//�Ż݄����⴦��
$share_data = (int)$_INPUT['share_data'];
if($share_data)
{
    $share_data = base_convert($share_data,8,10);
    //�Ż݄�����cookie
    $time_data = time()+3600*24*30;
    setcookie('share_event_id', $event_id, $time_data, '/', 'yueus.com');
    setcookie('share_phone', $share_data, $time_data, '/', 'yueus.com');
}





//ȡ�������ͻ�״̬��������
$category_name_arr=$system_obj->get_status_name_array_by_name('category');
$type_name_arr=$system_obj->get_status_name_array_by_name('type');
$act = trim($_INPUT['act']);
$event_id = (int)$_INPUT['event_id'];
$check_id=(int)$_INPUT['c_id'];
if(empty($act))
{
    $act = "browse";
}









if($act=="browse")
{
    
    if($event_id)
    {
        
        //�����豸��ת
        /**
         * �жϿͻ���
         */
        $__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
        $__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
        $__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
        //�    
        if ($__is_weixin)
        {
            
            $weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
            $url = $weixin_pub_obj->auth_get_authorize_url(array('mode' => 'wx','route' => 'act/detail/'.$event_id), 'snsapi_base');
            header("Location:{$url}");
            exit;
        }
        elseif ($__is_android || $__is_iphone) 
        {
            
            $url = "http://app.yueus.com/";
            header("Location:{$url}");
            exit;
        }
        //�����豸��ת
        
        //$details_obj->auto_update_event_status($event_id);		//�Զ����»״̬
        $event_info = $details_obj->get_event_by_event_id($event_id);
        if(empty($event_info))
        {
            header("location:http://event.poco.cn/event_list.php");
        }
        //��ó�����Ϣ
        $table_arr = $event_table_obj->get_event_table($event_id);
    }
    else
    {
        if($check_id)//��˴������ת
        {
            $check_info = $check_obj->get_event_by_check_id($check_id);
            if(empty($check_info))
            {
                header("location:http://event.poco.cn/event_list.php");
            }
            $is_review = 1;//�����ڲ鿴�޸ĺ������
            $tpl->assign("is_review",$is_review);
            $event_info = $check_info;
            $table_arr = unserialize($event_info['table_data']);
            if(!empty($event_info['event_id']))
            {
                $event_id = $event_info['event_id'];
            }
        }
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
     
    
    
    $setting = unserialize($event_info['setting']);
    //����Ƿ���ֱ�����ť
    if($poco_login_id!=$event_info['user_id'])
    {
        $tpl->assign("is_not_author",1);
    }
    //�����Ϣ
    $leader_info_arr = unserialize($event_info['leader_info']);
    
    //��ѧԺ����
    if($event_info["type_icon"]=="youxue")
    {
    	die('youxue');
    }
    
    if(!empty($event_id))
    {
        
    
        //�ж����ṹ
        //���ӵ������ע��
        if (rand(1, 15) == 1)
        {
            $hit_count = $details_obj->add_hit_count2($event_id, 1, true,false,true);
        }else{
            $hit_count = $details_obj->add_hit_count2($event_id, 1, true,false,false);
        }
        $event_info['hit_count'] = $hit_count;
        
        //��鱨���Ƿ����
        if($event_info['new_version']==1)
        {
            $date_return = $details_obj->check_date_is_over($event_id);	
            $date_is_over = ($date_return)?1:'';
        }
        else if($event_info['new_version']==2)
        {
            $date_is_over = ($event_info['event_status']>0)?1:'';
        }
        
        $tpl->assign('date_is_over', $date_is_over);
        

        //����ר����������
        $special_topic_obj = POCO::singleton('event_special_topic_class');
        $special_topic_info = $special_topic_obj->get_info_by_event_id_display_type($event_id,'detail');
        if(!empty($special_topic_info))
        {
            if(!empty($special_topic_info['content']))
            {
                $content_list = $special_topic_info['content'];
                $special_topic_list = array();
                foreach ($content_list as $key=>$item)
                {
                    if($key<3)
                    {
                        $content_list[$key]['image_145'] = $system_obj->get_small_image($item['image'], 145);
                        $special_topic_list[] = $content_list[$key];
                    }
                }
                $tpl->assign('special_topic_info',$special_topic_info);
                $tpl->assign('special_topic_list', $special_topic_list);
            }

        }

        //����Ƿ��Ѿ�����30��
        $days = G_POCO_EVENT_MAX_DAY;
        $check_event_over_some_days = $details_obj->check_event_over_some_days($event_id,$days);
        if($check_event_over_some_days)
        {
            $tpl->assign('check_event_over_some_days', 1);	
        }
        //�Ƿ��Ѿ�����
        $grade_obj = POCO::singleton('event_grade_class');
        $grade_info = $grade_obj->get_list_by_event_id_user_id($event_id,$poco_login_id);
        if(!empty($grade_info))
        {
            $tpl->assign('is_grade', 1);	
        }

        //�Ƿ���ʾ������ʾ
        if (!isset($_COOKIE["event_close_grade_tips"]))
        {
            $tpl->assign('is_show_grade_tips', 1);
        }
        //�Ƿ���ʾͼƬ��ͼ��ʾ
        if (!isset($_COOKIE["event_close_image_tips"]))
        {
            $tpl->assign('is_show_image_tips', 1);
        }
        //�Ƿ��ȼ���ʾ
        if (!isset($_COOKIE["event_close_event_level_tips"]))
        {
            $tpl->assign('is_show_event_level_tips', 1);
        }
        
        //�������
        if($event_info['category']!="")
        $event_info['category_name'] = $category_name_arr[$event_info['category']];
        //�Ƿ��Ѿ��л�ع�
        if($event_info['event_review']!="" && $event_info['review_time']>0)
        $event_info['review_button']="�޸Ĵ˻�ع�";
        else
        $event_info['review_button']="��Ӵ˻�ع�";
        
        //�μ�����������Ա
        
        /* if($setting['enroll_mode']==1)
        {
            $true_total_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(1));
            
        }
        else
        {
            $true_total_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(0));
            
        } */
        //$tpl->assign("true_total_enroll_count",$true_total_enroll_count);
        
        //�油����
        $true_total_back_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(1));
        $tpl->assign("true_total_back_enroll_count",$true_total_back_enroll_count);
        
        //�׷�����
        $first_total_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(0));
        $tpl->assign("first_total_enroll_count",$first_total_enroll_count);
        
        //�ж����ṹ����
    }
    
    
    //�ұ���ģ��
    $event_user_yue_id = $relate_obj->get_relate_yue_id($event_info['user_id']);
    $event_user_name_tmp = $pai_user_obj->get_user_nickname_by_user_id($event_user_yue_id);
    $event_user_name = poco_cutstr($event_user_name_tmp,25);
    $event_user_icon = $pai_user_icon_obj->get_user_icon($event_user_yue_id, 64);
    $tpl->assign("event_yue_user_name",$event_user_name);
    $tpl->assign("event_yue_user_icon",$event_user_icon); 



    

    //�ұ���ģ�����


        
    //�����Ƽ���Ʒ
    $is_hidden_recommend_cmd = $setting['is_hidden_recommend_cmd'];//�Ƿ���������
    if(empty($is_hidden_recommend_cmd))
    {
        $cmd_recommend_count = $cmd_obj->get_cmd_list_by_event_id($event_id,'1',true,"");		//�Ƽ���Ʒ����
        $cmd_limit = ($event_info['category'] ==1 )?15:5;		//���ϻ���Ƽ���Ʒ�����3��15��������ֻ��1��5��
        $cmd_limit = empty($setting['recommend_cmd_show_limit'])?$cmd_limit:$setting['recommend_cmd_show_limit'];//�����������ʾ����������ʾ������Ĭ����ʾ��
        $cmd_recommend_list = $cmd_obj->get_cmd_list_by_event_id($event_id,'1',false,"0,{$cmd_limit}");
        if(!empty($cmd_recommend_list))
        {
            $count = count($cmd_recommend_list);
            for($i=0;$i<$count;$i++)
            {
                if($cmd_recommend_list[$i]['img_url']!="")
                $cmd_recommend_list[$i]['img_url_165'] = $system_obj->get_small_image($cmd_recommend_list[$i]['img_url'], 165);//ȡ����ͼ
                else
                $cmd_recommend_list[$i]['img_url_165'] =" http://www1.poco.cn/event/images/default.jpg";
                if(($i+1)%5==0)
                {
                    $cmd_recommend_list[$i]['ul'] = '</ul><div class="clear"></div><ul>';
                }
            }
            $tpl->assign('cmd_recommend_count', $cmd_recommend_count);
            $tpl->assign('cmd_recommend_list', $cmd_recommend_list);
        }
    }

    //������Ʒ
    $is_hidden_all_cmd = $setting['is_hidden_all_cmd'];//�Ƿ���������
    if(empty($is_hidden_all_cmd))
    {
        $recommend_status = "";
        if($event_id==33564)
        {
            $recommend_status = "0";
        }
        $cmd_count = $cmd_obj->get_cmd_list_by_event_id($event_id,$recommend_status,true);		//��Ʒ����
        $cmd_limit = empty($setting['all_cmd_show_limit'])?5:$setting['all_cmd_show_limit'];//�����������ʾ����������ʾ������Ĭ����ʾ��
        $cmd_list = $cmd_obj->get_cmd_list_by_event_id($event_id,$recommend_status,false,"0,$cmd_limit",'relate_time DESC');
        if(!empty($cmd_list))
        {
            $count = count($cmd_list);
            for($i=0;$i<$count;$i++)
            {
                if($cmd_list[$i]['img_url']!="")
                $cmd_list[$i]['img_url_165'] = $system_obj->get_small_image($cmd_list[$i]['img_url'], 165);//ȡ����ͼ
                else
                $cmd_list[$i]['img_url_165'] =" http://www1.poco.cn/event/images/default.jpg";
            }

            $tpl->assign('cmd_total_count', $cmd_count);
            $tpl->assign('cmd_list', $cmd_list);
            //var_dump($cmd_list);
        }
    }
    
    //�ײ���ģ�����
    
    
    //���� combo����JS
    $combo_js_files = array(
        '/disk/data/htdocs233/poco_main/js_common/mootools/mt_more/pocoStatusBox.min.js',
        '/disk/data/htdocs233/poco_main/js_common/mootools/mt_more/share/share.js',
        '/disk/data/htdocs233/poco_main/js_common/mootools/mt_more/share/share_toolbar.js'
    );
    $tpl->assign('share_combo_js_files', $combo_js_files);
    
    
    //���»,���������Ϣ
    if($event_info['category'] ==2 )
    {
        
        $parameters = array($event_info);
        $temp_info = $details_obj->use_type_obj_and_function($event_info['type_icon'], "get_related_info_detail", $parameters);
        if(!empty($temp_info))
        {
            $event_info = $temp_info;
        }

        
    }
    
    //����ϵͳ
    $cmt_html = $system_obj->get_cmt_html($event_id,$event_info['title'],$event_info['user_id'],$event_info['category'],$event_info['status']);
    $tpl->assign('cmt_html', $cmt_html);
    
}
else if($act=="preview")
{
    $user_id = (int)$_INPUT['user_id'];
    $time = (int)$_INPUT['time'];
    $cache_key = "event_preview_".$user_id."_".$time;
    $cache_arr = POCO::getCache($cache_key);
    //����
    $table_arr = $cache_arr['cache_table_arr'];

    //�����Ϣ
    $leader_info_arr = unserialize($cache_arr['leader_info']);
    $event_info = $cache_arr;
}

//�����������ݽṹ
$model_part_img_list = unserialize($event_info['other_info']);

foreach($model_part_img_list as $key => $value)
{


    //$model_part_img_list[$key]['text'] = str_replace("&lt;br&gt;","<br>",$value['text']);
    $model_part_img_list[$key]['text'] = str_replace("\n","<br>",$value['text']);
    
    //����ͼƬ�ṹ
    if(!empty($value['img']))
    {
        foreach($value['img'] as $k => $v)
        {
            
            $model_part_img_list[$key]['img_arr'][$k]['img_value'] = $v;
            
            

        }
    }
}

//��ַ
if(!empty($event_info['location_id']))
{
    //�����
    $event_location_name_arr = $system_obj->get_city_name_by_location_id($event_info['location_id']);
    $event_info['location_name1']=$event_location_name_arr[0];
    $event_info['location_name2']=$event_location_name_arr[1];
}

//����
$event_info['content'] = str_replace("\n","<br>",str_replace(" ","&nbsp;",$event_info['content']));

//ע������
$event_info['remark'] = str_replace("\n","<br>",str_replace(" ","&nbsp;",$event_info['remark']));


//���ֲ�
$event_info['club_name'] = $_local_club_array[$event_info['club_id']];


$photo_level_list = $system_obj->get_join_level_list_by_type_icon('photo');
$food_level_list = $system_obj->get_join_level_list_by_type_icon('food');


//���μ�ģʽ

if($event_info['join_mode']=="3")
{
    if(in_array($event_info['type_icon'],array('food','photo')))
    {
        $level_list = $system_obj->get_join_level_list_by_type_icon($event_info['type_icon']);
        foreach ($level_list as $item)
        {
            if($item['level']==$event_info['join_ids'])
            {
                $event_info['join_level_name'] = $item['name'];
            }
        }
    }
}

$join_mode_arr = array("ȫ������","ֻ�޺���","ֻ��ĳЩ�û�","���ϵȼ�");
$event_info['join_mode_name'] = $join_mode_arr[$event_info['join_mode']];

//�����νṹ
$table_config_arr = array("��","һ","��","��","��","��","��","��","��","��","ʮ");
foreach($table_arr as $key => $value)
{
    $table_arr[$key]['data_mark'] = ((int)$key)+1;
    $table_arr[$key]['site_name'] = $table_config_arr[$table_arr[$key]['data_mark']];//��Ӧ������
    
}


//��ʼ�������ڸ�ʽ
$date_ymd = date("Y��m��d�� ",$event_info['start_time']);
$date_week = $system_obj->get_chinese_week(date("w",$event_info['start_time']));
$date_hour = date(" H:i",$event_info['start_time']);
$event_info['start_time'] = $date_ymd.$date_week.$date_hour;
$date_ymd = date("Y��m��d�� ",$event_info['end_time']);
$date_week = $system_obj->get_chinese_week(date("w",$event_info['end_time']));
$date_hour = date(" H:i",$event_info['end_time']);
$event_info['end_time'] = $date_ymd.$date_week.$date_hour;

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
    $tpl->assign('login_id', $poco_login_id);
}


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



//���������,�ж�״̬
$event_info['type_name'] = $type_name_arr[$event_info['type_icon']];


//�״̬
/* if($event_info['event_status']==0)
{
    $event_info['status_img_url'] ="http://event.poco.cn/images/fbend_none.jpg";
}
else if($event_info['event_status']==1)
{
    $event_info['status_img_url'] ="http://event.poco.cn/images/fbend_in.jpg";
}
else if($event_info['event_status']==2)
{
    $event_info['status_img_url'] ="http://event.poco.cn/images/fbend_stop.jpg";
}
else if($event_info['event_status']==3)
{
    $event_info['status_img_url'] ='http://event.poco.cn/images/fbend_cancel.jpg';	//�ȡ��ͼ��
} */




//PHP����ģ����ܸ����ν�������
$page_content_html = '<div class="look-waipai-mod">
                  <div class="titles fn_wryh">�����</div>
                  <div class="detail-item">
                    <div class="info-item">
                      <div class="txt-item">
                        <p style="text-indent:0;">'.$event_info['content'].'</p>
                      </div>
                    </div>
                    <div class="item-list">';
                      

                    foreach($model_part_img_list as $key => $value)
                    {
                          $page_content_html .='<div class="waipai-item">
                            <div class="txt-item">
                              <p>'.str_replace("\n","<br>",str_replace(" ","&nbsp;",$value['text'])).'</p>
                            </div>';
                            foreach($value['img_arr'] as $k => $v)
                            {
                                $page_content_html .='<div class="img-item mb5"><img src="'.$v['img_value'].'"/></div>';
                            }
                            
                            
                          $page_content_html .='</div>';
                    }

                      

/********10��13���޸�ʱ��*****************/
$page_content_html .= '</div>
                  </div>
                </div>
                <div class="look-waipai-mod">
                  <div class="titles fn_wryh">���ΰ���</div>
                  <table cellpadding="0" cellspacing="0" width="768" class="cdap-table" style="word-wrap:break-word;word-break:break-all;">';
        
                        if(!empty($table_arr))
                        {
                            foreach($table_arr as $k => $v)
                            {
                                //�ж��¸����Ƿ�ͬһ��
                                $tmp_begin_day = date("md",$v['begin_time']);
                                $tmp_end_day = date("md",$v['end_time']);
                                if($tmp_begin_day==$tmp_end_day)
                                {
                                    $end_time_html = date("H:i",$v['end_time']);
                                }
                                else
                                {
                                    $end_time_html = date("m��d�� H:i",$v['end_time']);
                                }
                                
                                
                                if(count($table_arr)==1)
                                {
                                    $page_content_html .='<tr>
                                        <td width="350" valign="top">�ʱ�䣺'.date("m��d�� H:i",$v['begin_time']).' �� '.$end_time_html.'</td>
                                        <td width="400" valign="top">���'.$v['num'].'��</td>
                                    </tr>';
                                }
                                else
                                {
                                    $page_content_html .='<tr>
                                        <td width="350" valign="top">��'.$v['site_name'].'����'.date("m��d�� H:i",$v['begin_time']).' �� '.$end_time_html.'</td>
                                        <td width="400" valign="top">���'.$v['num'].'��</td>
                                    </tr>';
                                }
                                unset($tmp_begin_day);
                                unset($tmp_end_day);
                                
                            }
                            
                            if(!empty($event_info['remark']))
                            {

                                $page_content_html .='<tr>
                                    <td colspan="2" class="tips" valign="top">ע�����<br>'.$event_info['remark'].'<br>&nbsp;</td>
                                </tr>';
                            }
                        }
                        
                        

                        foreach($leader_info_arr as $k => $v)
                        {

                            $page_content_html .='<tr>
                                <td valign="top">���ӣ�'.$v['name'].'</td>
                                <td valign="top">��ϵ��ʽ��'.$v['mobile'].'</td>
                            </tr>';
                        }

                    $page_content_html .='</table>
                </div>';
/********10��13���޸�ʱ��*****************/





$event_info['page_content_html'] = $page_content_html;

//�ı���ʾ��Ǯ��ʽ������������Ͳ���ʾ��.00��
$event_info['budget'] = abs($event_info['budget']);
//��ʾ����
$people_count = 0;
if(!empty($table_arr))
{
    foreach($table_arr as $key => $value)
    {
        $people_count = $people_count+(int)$value['num'];
    }
}
$event_info['people_count'] = $people_count;



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
    header("location:http://event.poco.cn/event_detail.htx&event_id={$event_id}");
}


//�ұ������췢���Ʒ����
if(!empty($event_id))
{
    $encode_tag = urlencode("ԼԼ��Ʒ");
    $publish_article_link = "http://my.poco.cn/blog_v2/publish.php?publish_type=photo&init_tag=".$encode_tag."&event_id=0&best_pocoer_type_id=";
}
else
{
    $publish_article_link = "javascript:void(0);";
} 

/* if($yue_login_id==100004)
{
    var_dump($publish_article_link);
}  */



$tpl->assign('page_title', G_POCO_EVENT_PAGE_TITLE);
$tpl->assign("act",$act);
$tpl->assign($event_info);
$tpl->assign("table_arr",$table_arr);
$tpl->assign("leader_info_arr",$leader_info_arr);
$tpl->assign('model_part_img_list',$model_part_img_list);
$tpl->assign('publish_article_link',$publish_article_link);
$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$header_html = $my_app_pai->webControl('PartyHeader', array(), true);
$footer_html = $my_app_pai->webControl('PartyFooter', array(), true);
$partyenroll_container = $my_app_pai->webControl('PartyEnroll_container', array("event_id"=>$event_id,"__mp_manage_branch"=>$__mp_manage_branch,"new_version"=>$event_info['new_version'],"yue_type"=>$yue_type), true);

$tpl->assign("rand",time());
$tpl->assign('partyenroll_container', $partyenroll_container);

$tpl->assign('global_header_html', $global_header_html);
$tpl->assign('header_html', $header_html);
$tpl->assign('footer_html', $footer_html);

// css �����
$tpl ->assign("rand",201503131417);

$tpl->output();
?>