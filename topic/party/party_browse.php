<?php
/** 
 * 
 * 活动浏览页
 * 
 * author 星星
 * 
 * 
 * 2014-7-21
 * 
 * 
 */
include_once("./party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
//引入俱乐部文件 $_local_club_array
include_once("/disk/data/htdocs232/photo/photo_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//取得模板对象
//$__mp_manage_branch = G_POCO_EVENT_PAY;//开发版为支付版，非开发版为非支付
$__mp_manage_branch = 1;//确定为支付版
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

//优惠劵特殊处理
$share_data = (int)$_INPUT['share_data'];
if($share_data)
{
    $share_data = base_convert($share_data,8,10);
    //优惠劵设置cookie
    $time_data = time()+3600*24*30;
    setcookie('share_event_id', $event_id, $time_data, '/', 'yueus.com');
    setcookie('share_phone', $share_data, $time_data, '/', 'yueus.com');
}





//取公共类型或状态名称数组
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
        
        //处理设备跳转
        /**
         * 判断客户端
         */
        $__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
        $__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
        $__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
        //活动    
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
        //处理设备跳转
        
        //$details_obj->auto_update_event_status($event_id);		//自动更新活动状态
        $event_info = $details_obj->get_event_by_event_id($event_id);
        if(empty($event_info))
        {
            header("location:http://event.poco.cn/event_list.php");
        }
        //获得场次信息
        $table_arr = $event_table_obj->get_event_table($event_id);
    }
    else
    {
        if($check_id)//审核错误的跳转
        {
            $check_info = $check_obj->get_event_by_check_id($check_id);
            if(empty($check_info))
            {
                header("location:http://event.poco.cn/event_list.php");
            }
            $is_review = 1;//隶属于查看修改后的内容
            $tpl->assign("is_review",$is_review);
            $event_info = $check_info;
            $table_arr = unserialize($event_info['table_data']);
            if(!empty($event_info['event_id']))
            {
                $event_id = $event_info['event_id'];
            }
        }
    }
    
    
    //查询对应的poco_id
    if(!empty($yue_login_id))
    {
        $poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);
    }
    else
    {
        $poco_login_id = 0;
    }
     
    
    
    $setting = unserialize($event_info['setting']);
    //检查是否出现报名按钮
    if($poco_login_id!=$event_info['user_id'])
    {
        $tpl->assign("is_not_author",1);
    }
    //领队信息
    $leader_info_arr = unserialize($event_info['leader_info']);
    
    //游学院处理
    if($event_info["type_icon"]=="youxue")
    {
    	die('youxue');
    }
    
    if(!empty($event_id))
    {
        
    
        //中顶部结构
        //增加点击数关注数
        if (rand(1, 15) == 1)
        {
            $hit_count = $details_obj->add_hit_count2($event_id, 1, true,false,true);
        }else{
            $hit_count = $details_obj->add_hit_count2($event_id, 1, true,false,false);
        }
        $event_info['hit_count'] = $hit_count;
        
        //检查报名是否过期
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
        

        //关联专题文字链接
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

        //检查活动是否已经结束30天
        $days = G_POCO_EVENT_MAX_DAY;
        $check_event_over_some_days = $details_obj->check_event_over_some_days($event_id,$days);
        if($check_event_over_some_days)
        {
            $tpl->assign('check_event_over_some_days', 1);	
        }
        //是否已经评分
        $grade_obj = POCO::singleton('event_grade_class');
        $grade_info = $grade_obj->get_list_by_event_id_user_id($event_id,$poco_login_id);
        if(!empty($grade_info))
        {
            $tpl->assign('is_grade', 1);	
        }

        //是否显示评分提示
        if (!isset($_COOKIE["event_close_grade_tips"]))
        {
            $tpl->assign('is_show_grade_tips', 1);
        }
        //是否显示图片大图提示
        if (!isset($_COOKIE["event_close_image_tips"]))
        {
            $tpl->assign('is_show_image_tips', 1);
        }
        //是否活动等级提示
        if (!isset($_COOKIE["event_close_event_level_tips"]))
        {
            $tpl->assign('is_show_event_level_tips', 1);
        }
        
        //活动大类名
        if($event_info['category']!="")
        $event_info['category_name'] = $category_name_arr[$event_info['category']];
        //是否已经有活动回顾
        if($event_info['event_review']!="" && $event_info['review_time']>0)
        $event_info['review_button']="修改此活动回顾";
        else
        $event_info['review_button']="添加此活动回顾";
        
        //参加名额或观望成员
        
        /* if($setting['enroll_mode']==1)
        {
            $true_total_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(1));
            
        }
        else
        {
            $true_total_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(0));
            
        } */
        //$tpl->assign("true_total_enroll_count",$true_total_enroll_count);
        
        //替补名额
        $true_total_back_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(1));
        $tpl->assign("true_total_back_enroll_count",$true_total_back_enroll_count);
        
        //首发名额
        $first_total_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(0));
        $tpl->assign("first_total_enroll_count",$first_total_enroll_count);
        
        //中顶部结构结束
    }
    
    
    //右边栏模块
    $event_user_yue_id = $relate_obj->get_relate_yue_id($event_info['user_id']);
    $event_user_name_tmp = $pai_user_obj->get_user_nickname_by_user_id($event_user_yue_id);
    $event_user_name = poco_cutstr($event_user_name_tmp,25);
    $event_user_icon = $pai_user_icon_obj->get_user_icon($event_user_yue_id, 64);
    $tpl->assign("event_yue_user_name",$event_user_name);
    $tpl->assign("event_yue_user_icon",$event_user_icon); 



    

    //右边栏模块结束


        
    //精华推荐作品
    $is_hidden_recommend_cmd = $setting['is_hidden_recommend_cmd'];//是否设置隐藏
    if(empty($is_hidden_recommend_cmd))
    {
        $cmd_recommend_count = $cmd_obj->get_cmd_list_by_event_id($event_id,'1',true,"");		//推荐作品总数
        $cmd_limit = ($event_info['category'] ==1 )?15:5;		//线上活动，推荐作品数变成3行15个，线下只有1行5个
        $cmd_limit = empty($setting['recommend_cmd_show_limit'])?$cmd_limit:$setting['recommend_cmd_show_limit'];//如果设置了显示数，则用显示数代替默认显示数
        $cmd_recommend_list = $cmd_obj->get_cmd_list_by_event_id($event_id,'1',false,"0,{$cmd_limit}");
        if(!empty($cmd_recommend_list))
        {
            $count = count($cmd_recommend_list);
            for($i=0;$i<$count;$i++)
            {
                if($cmd_recommend_list[$i]['img_url']!="")
                $cmd_recommend_list[$i]['img_url_165'] = $system_obj->get_small_image($cmd_recommend_list[$i]['img_url'], 165);//取缩略图
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

    //所有作品
    $is_hidden_all_cmd = $setting['is_hidden_all_cmd'];//是否设置隐藏
    if(empty($is_hidden_all_cmd))
    {
        $recommend_status = "";
        if($event_id==33564)
        {
            $recommend_status = "0";
        }
        $cmd_count = $cmd_obj->get_cmd_list_by_event_id($event_id,$recommend_status,true);		//作品总数
        $cmd_limit = empty($setting['all_cmd_show_limit'])?5:$setting['all_cmd_show_limit'];//如果设置了显示数，则用显示数代替默认显示数
        $cmd_list = $cmd_obj->get_cmd_list_by_event_id($event_id,$recommend_status,false,"0,$cmd_limit",'relate_time DESC');
        if(!empty($cmd_list))
        {
            $count = count($cmd_list);
            for($i=0;$i<$count;$i++)
            {
                if($cmd_list[$i]['img_url']!="")
                $cmd_list[$i]['img_url_165'] = $system_obj->get_small_image($cmd_list[$i]['img_url'], 165);//取缩略图
                else
                $cmd_list[$i]['img_url_165'] =" http://www1.poco.cn/event/images/default.jpg";
            }

            $tpl->assign('cmd_total_count', $cmd_count);
            $tpl->assign('cmd_list', $cmd_list);
            //var_dump($cmd_list);
        }
    }
    
    //底部栏模块结束
    
    
    //分享 combo加载JS
    $combo_js_files = array(
        '/disk/data/htdocs233/poco_main/js_common/mootools/mt_more/pocoStatusBox.min.js',
        '/disk/data/htdocs233/poco_main/js_common/mootools/mt_more/share/share.js',
        '/disk/data/htdocs233/poco_main/js_common/mootools/mt_more/share/share_toolbar.js'
    );
    $tpl->assign('share_combo_js_files', $combo_js_files);
    
    
    //线下活动,构造相关信息
    if($event_info['category'] ==2 )
    {
        
        $parameters = array($event_info);
        $temp_info = $details_obj->use_type_obj_and_function($event_info['type_icon'], "get_related_info_detail", $parameters);
        if(!empty($temp_info))
        {
            $event_info = $temp_info;
        }

        
    }
    
    //留言系统
    $cmt_html = $system_obj->get_cmt_html($event_id,$event_info['title'],$event_info['user_id'],$event_info['category'],$event_info['status']);
    $tpl->assign('cmt_html', $cmt_html);
    
}
else if($act=="preview")
{
    $user_id = (int)$_INPUT['user_id'];
    $time = (int)$_INPUT['time'];
    $cache_key = "event_preview_".$user_id."_".$time;
    $cache_arr = POCO::getCache($cache_key);
    //场次
    $table_arr = $cache_arr['cache_table_arr'];

    //领队信息
    $leader_info_arr = unserialize($cache_arr['leader_info']);
    $event_info = $cache_arr;
}

//处理数据内容结构
$model_part_img_list = unserialize($event_info['other_info']);

foreach($model_part_img_list as $key => $value)
{


    //$model_part_img_list[$key]['text'] = str_replace("&lt;br&gt;","<br>",$value['text']);
    $model_part_img_list[$key]['text'] = str_replace("\n","<br>",$value['text']);
    
    //处理图片结构
    if(!empty($value['img']))
    {
        foreach($value['img'] as $k => $v)
        {
            
            $model_part_img_list[$key]['img_arr'][$k]['img_value'] = $v;
            
            

        }
    }
}

//地址
if(!empty($event_info['location_id']))
{
    //活动地区
    $event_location_name_arr = $system_obj->get_city_name_by_location_id($event_info['location_id']);
    $event_info['location_name1']=$event_location_name_arr[0];
    $event_info['location_name2']=$event_location_name_arr[1];
}

//内容
$event_info['content'] = str_replace("\n","<br>",str_replace(" ","&nbsp;",$event_info['content']));

//注意事项
$event_info['remark'] = str_replace("\n","<br>",str_replace(" ","&nbsp;",$event_info['remark']));


//俱乐部
$event_info['club_name'] = $_local_club_array[$event_info['club_id']];


$photo_level_list = $system_obj->get_join_level_list_by_type_icon('photo');
$food_level_list = $system_obj->get_join_level_list_by_type_icon('food');


//检查参加模式

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

$join_mode_arr = array("全部网友","只限好友","只限某些用户","符合等级");
$event_info['join_mode_name'] = $join_mode_arr[$event_info['join_mode']];

//处理场次结构
$table_config_arr = array("零","一","二","三","四","五","六","七","八","九","十");
foreach($table_arr as $key => $value)
{
    $table_arr[$key]['data_mark'] = ((int)$key)+1;
    $table_arr[$key]['site_name'] = $table_config_arr[$table_arr[$key]['data_mark']];//对应场次名
    
}


//开始结束日期格式
$date_ymd = date("Y年m月d日 ",$event_info['start_time']);
$date_week = $system_obj->get_chinese_week(date("w",$event_info['start_time']));
$date_hour = date(" H:i",$event_info['start_time']);
$event_info['start_time'] = $date_ymd.$date_week.$date_hour;
$date_ymd = date("Y年m月d日 ",$event_info['end_time']);
$date_week = $system_obj->get_chinese_week(date("w",$event_info['end_time']));
$date_hour = date(" H:i",$event_info['end_time']);
$event_info['end_time'] = $date_ymd.$date_week.$date_hour;

//判断登录人的角色
if(!empty($poco_login_id))
{
    define("G_DB_GET_REALTIME_DATA", 1 );
    if($event_info['user_id']==$poco_login_id)
    {
        $event_info['event_user_role'] = "author";
        $event_info['is_event_user']=1;
        
        //判断是否管理人员
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
        //判断是否管理人员
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


//登录用户是否已经报名
if(!empty($poco_login_id))
{
    $tpl->assign('login_id', $poco_login_id);
    //支付的活动判断是否已经签到，出发活动按钮
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
//2015-3-24，如果组织者看，也同样出现发布按钮
if($event_info['user_id']==$poco_login_id)
{
    $tpl->assign('is_enroll', 1);
}



//活动类型名称,判断状态
$event_info['type_name'] = $type_name_arr[$event_info['type_icon']];


//活动状态
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
    $event_info['status_img_url'] ='http://event.poco.cn/images/fbend_cancel.jpg';	//活动取消图标
} */




//PHP构造模板介绍跟场次介绍内容
$page_content_html = '<div class="look-waipai-mod">
                  <div class="titles fn_wryh">活动介绍</div>
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

                      

/********10月13号修改时间*****************/
$page_content_html .= '</div>
                  </div>
                </div>
                <div class="look-waipai-mod">
                  <div class="titles fn_wryh">场次安排</div>
                  <table cellpadding="0" cellspacing="0" width="768" class="cdap-table" style="word-wrap:break-word;word-break:break-all;">';
        
                        if(!empty($table_arr))
                        {
                            foreach($table_arr as $k => $v)
                            {
                                //判断月跟日是否同一天
                                $tmp_begin_day = date("md",$v['begin_time']);
                                $tmp_end_day = date("md",$v['end_time']);
                                if($tmp_begin_day==$tmp_end_day)
                                {
                                    $end_time_html = date("H:i",$v['end_time']);
                                }
                                else
                                {
                                    $end_time_html = date("m月d号 H:i",$v['end_time']);
                                }
                                
                                
                                if(count($table_arr)==1)
                                {
                                    $page_content_html .='<tr>
                                        <td width="350" valign="top">活动时间：'.date("m月d号 H:i",$v['begin_time']).' 至 '.$end_time_html.'</td>
                                        <td width="400" valign="top">名额：'.$v['num'].'人</td>
                                    </tr>';
                                }
                                else
                                {
                                    $page_content_html .='<tr>
                                        <td width="350" valign="top">第'.$v['site_name'].'场：'.date("m月d号 H:i",$v['begin_time']).' 至 '.$end_time_html.'</td>
                                        <td width="400" valign="top">名额：'.$v['num'].'人</td>
                                    </tr>';
                                }
                                unset($tmp_begin_day);
                                unset($tmp_end_day);
                                
                            }
                            
                            if(!empty($event_info['remark']))
                            {

                                $page_content_html .='<tr>
                                    <td colspan="2" class="tips" valign="top">注意事项：<br>'.$event_info['remark'].'<br>&nbsp;</td>
                                </tr>';
                            }
                        }
                        
                        

                        foreach($leader_info_arr as $k => $v)
                        {

                            $page_content_html .='<tr>
                                <td valign="top">活动领队：'.$v['name'].'</td>
                                <td valign="top">联系方式：'.$v['mobile'].'</td>
                            </tr>';
                        }

                    $page_content_html .='</table>
                </div>';
/********10月13号修改时间*****************/





$event_info['page_content_html'] = $page_content_html;

//改变显示价钱格式，如果是整数就不显示“.00”
$event_info['budget'] = abs($event_info['budget']);
//显示人数
$people_count = 0;
if(!empty($table_arr))
{
    foreach($table_arr as $key => $value)
    {
        $people_count = $people_count+(int)$value['num'];
    }
}
$event_info['people_count'] = $people_count;



//控制是否约拍还是一元app拍摄
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
//控制是否约拍还是一元app拍摄

//检查当前活动是否在大外拍或者一元外拍
$belong_waipai_arr = $pai_config_obj->big_waipai_event_id_arr('one_waipai');
if(!in_array($event_id,$belong_waipai_arr))
{
    header("location:http://event.poco.cn/event_detail.htx&event_id={$event_id}");
}


//右边栏构造发活动作品链接
if(!empty($event_id))
{
    $encode_tag = urlencode("约约作品");
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

// css 随机数
$tpl ->assign("rand",201503131417);

$tpl->output();
?>