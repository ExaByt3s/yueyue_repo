<?php
/**
 * 用户活动作品列表页
 * @author  tom 
 */

//引入应用公共文件
define("G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK",1);
//define("G_DB_GET_REALTIME_DATA", 1 );
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$pai_config_obj = POCO::singleton ( 'pai_config_class' );//大外拍
$waipai_arr = $pai_config_obj->big_waipai_event_id_arr('one_waipai');//大外拍

//取得模板对象
$tpl = $my_app_pai->getView('party_commend_act_list.tpl.htm');

$details_obj = POCO::singleton('event_details_class');
$cmd_obj = POCO::singleton('event_commend_act_class');
$system_obj  = POCO::singleton('event_system_class');
$enroll_obj  = POCO::singleton('event_enroll_class');
$activity_code_obj = POCO::singleton('pai_activity_code_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');

//检查绑定
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
//大奖商业活动时，没有此页
if($event_info['category']==3)
{
    header("location:event_detail.php?event_id={$event_id}");
}


//大外拍跳转，上线后加回
if(!in_array($event_id,$waipai_arr))//大外拍跳转
{
    header("location:http://event.poco.cn/commend_act_list.php?event_id={$event_id}");
} 



//增加点击数
//$details_obj->add_hit_count($event_id, 1, true);
    if (rand(1, 15) == 1)
    {
        $hit_count = $details_obj->add_hit_count2($event_id, 1, true,false,true);
    }else{
        $hit_count = $details_obj->add_hit_count2($event_id, 1, true,false,false);
    }
    $event_info['hit_count'] = $hit_count;

//活动所在城市
$tpl->assign('location_name', $location_name);
//对应活动作品
if($where_str !='') $where_str.=" AND ";
$where_str.="event_id = {$event_id}";
//判断是否管理人员

//查询对应的poco_id
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
    //是否发布者
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

//是否显示推荐提示
if (isset($_COOKIE["event_close_recommend_tips"]) && $_COOKIE["event_close_recommend_tips"]==$poco_login_id)
{
    setcookie("event_close_recommend_tips", $poco_login_id, time()+2400*3600,"/", ".poco.cn");
    $event_close_recommend_tips = 1;
}

//显示作品数
$cmd_no_recommend_count = $cmd_obj->get_cmd_list_by_event_id($event_id,'0',true,"");	//没推荐作品总数
$cmd_recommend_count = $cmd_obj->get_cmd_list_by_event_id($event_id,'1',true,"");		//推荐作品总数
$cmd_count = $cmd_no_recommend_count + $cmd_recommend_count;							//作品总数
$tpl->assign('cmd_no_recommend_total_count', $cmd_no_recommend_count);	
$tpl->assign('cmd_recommend_total_count', $cmd_recommend_count);	
$tpl->assign('cmd_total_count', $cmd_count);	
//活动大类名
if($event_info['category']!="")
$event_info['category_name'] = $category_name_arr[$event_info['category']];
//是否已经有活动回顾
if($event_info['event_review']!="" && $event_info['review_time']>0)
$event_info['review_button']="修改此活动回顾";
else
$event_info['review_button']="添加此活动回顾";
//活动类型名称
$event_info['type_name'] = $type_name_arr[$event_info['type_icon']];



$tpl->assign($event_info);
    
//初始化分页
$page_obj =POCO::singleton('show_page'); 
$show_count = 20;//每页显示数
$page_setvar = array('res_name'=>$res_name);


//增加分页条件
$page_setvar['event_id'] = $event_id;

// 筛选不同类别的活动		推荐 / 非推荐
$type=trim($_INPUT['search_type']);
if ($type !== '')
{
    if (in_array($type, array("0","1"))) 
    {
        if($where_str !='') $where_str.=" AND ";
        $where_str.="is_recommend = '{$type}'";
        //增加分页条件
        $page_setvar['search_type'] = $type;
        //放入页hidden，方便分页
        $tpl->assign('search_type', $type);
    }
}

// 筛选不同类别的活动		推荐 / 非推荐
$order_type=trim($_INPUT['order_type']);
if ($order_type != '')
{
    if (in_array($order_type, array("relate_time","hit_count","vote_count"))) 
    {
        $order_by = $order_type." DESC";
        //增加分页条件
        $page_setvar['order_type'] = $order_type;
        //放入页hidden，方便分页
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

//--------------------------------------修正显示格式 start --------------------------------------------//
$count = count($cmd_list);
for($i=0;$i<$count;$i++)
{
    if($cmd_list[$i]['img_url']!="")
        $cmd_list[$i]['img_url_165'] = $system_obj->get_small_image($cmd_list[$i]['img_url'], 165);//取缩略图
    else 
        $cmd_list[$i]['img_url_165'] =" http://www1.poco.cn/event/images/default.jpg";		
    //每5个作品换行和添加ul,div
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
//--------------------------------------修正显示格式 end  ---------------------------------------------//
$tpl->assign('cmd_list', $cmd_list);


//留言系统
$cmt_html = $system_obj->get_cmt_html($event_id,$event_info['title'],$event_info['user_id'],$event_info['category'],$event_info['status']);
$tpl->assign('cmt_html', $cmt_html);

//检查报名是否过期
$date_return = $details_obj->check_date_is_over($event_id);	
$date_is_over = ($date_return)?1:'';
$tpl->assign('date_is_over', $date_is_over);

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





//检查活动是否已经结束30天
$days = G_POCO_EVENT_MAX_DAY;
$check_event_over_some_days = $details_obj->check_event_over_some_days($event_id,$days);
if($check_event_over_some_days)
{
    $tpl->assign('check_event_over_some_days', 1);	
}

//是否展示电话报名表格
$setting = unserialize($event_info['setting']);	
$is_show_phone_form = $setting['is_show_phone_form'];
$tpl->assign('is_show_phone_form', $is_show_phone_form);	




//检查约ID绑定情况
//绑定结构暂时先注释，检查绑定的情况
/* if($event_info['new_version']==2)
{
    if(!empty($poco_login_id))
    {
        $relate_yue_id = $relate_obj->get_relate_yue_id($poco_login_id);

        //获取约约PC版绑定的POCOID
        $poco_id = $bind_obj->get_bind_poco_id($relate_yue_id);
        if($poco_id || $relate_yue_id)//表示已经绑定
        {
            $bind_link = 0;
        }
        else
        {
            $bind_link = 1;
        }

    }
} */
//检查约ID绑定情况


$cur_url= "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$encode_link = urlencode($cur_url);
$tpl->assign("bind_link",$bind_link);
$tpl->assign("encode_link",$encode_link);
//绑定结构

//发作品链接
if(!empty($event_id))
{
    $encode_tag = urlencode("约约作品");
    $publish_article_link = "http://my.poco.cn/blog_v2/publish.php?publish_type=photo&init_tag=".$encode_tag."&event_id=0&best_pocoer_type_id=";
}
else
{
    $publish_article_link = "javascript:void(0);";
} 



//取得POCO默认头尾
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
$tpl->assign("page_select",$page_select);//分页
$tpl->assign("total_count",$total_count);//分页
$tpl->assign("rand",time());

$tpl->output();

?>