<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once("../protocol_common.inc.php");

$location_id        = $_COOKIE['yue_location_id'];
$user_id            = $yue_login_id;

$config = include_once '/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php';

$ad_list = '';

include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$cms_obj        = new cms_system_class();

$info = $cms_obj->get_last_issue_record_list(false, '0,16', 'place_number DESC', 16); 

$tag_list = '';
foreach($info AS $key=>$val)
{
    $top_tag['tag']     = $val['title'];
    $top_tag['url']     = '';
    
    $tag_list[] = $top_tag;
}

/**
 * 
 * 发现四格内容
 * */
//摄影榜单ID
$rank_id = 6;

$topic_where = " is_effect = 1 AND display_type='all' AND (type = 'weixin' OR type='all')";

if($user_id)
{
    $pai_user_obj = POCO::singleton ( 'pai_user_class' );
    if($pai_user_obj->check_role($user_id) == 'model')
    {
        //模特榜单ID
        $rank_id = 7;
        $topic_where = " is_effect = 1 AND (display_type='model' OR display_type='all') AND (type = 'weixin' OR type='all')";
    }else{
        $topic_where = " is_effect = 1 AND (display_type='cameraman' OR display_type='all') AND (type = 'weixin' OR type='all')";
    }
}
$info = $cms_obj->get_last_issue_record_list(false, '0,4', 'place_number DESC', $rank_id);
foreach($info AS $key=>$val)
{
    $centent_ad_list[$key]['img'] = $val['img_url'];
    $centent_ad_list[$key]['url'] = yue_convert_weixin_url($val['link_url']);
    $centent_ad_list[$key]['link_type'] = $val['link_type'];

}


$topic_obj = POCO::singleton ('pai_topic_class');
$result = $topic_obj->get_topic_list(false, $topic_where, 'sort DESC', '0,5');
$count_topic = $topic_obj->get_topic_list(true, $topic_where);

$topic_list = '';
foreach($result AS $key=>$val)
{
    $topic_val['img'] = $val['cover_image'];
    $url = 'http://yp.yueus.com/mobile/app?from_app=1#topic/' . $val['id'];
    $topic_val['url'] = yue_convert_weixin_url($url);
    
    $topic_list[] = $topic_val;    
}

if($tag_list)
{
    $ad_list['tag']['mid']          = '122PT03001';
    $ad_list['tag']['list']         = $tag_list; 
}

if($centent_ad_list)
{
    $ad_list['ad_list']['mid']      = '122PT03002';
    $ad_list['ad_list']['list']     = $centent_ad_list;
}

if($topic_list)
{
    $ad_list['topic_list']['mid']      = '122PT02004';
    $ad_list['topic_list']['list']  = $topic_list;  
}       

if($count_topic>5)
{
	$url = 'http://yp.yueus.com/mobile/app?from_app=1#topic_list';
	$ad_list['topic_more_link']  = yue_convert_weixin_url($url);
	$ad_list['topic_more_text'] = '更多';
}
else
{
	$ad_list['topic_more_link']  = '';
	$ad_list['topic_more_text'] = '';
}

$ad_list['topic_name']  = '火热专题';
      

$output_arr ['list'] = $ad_list;

mobile_output ( $output_arr, false );


function yue_convert_weixin_url($url)
{
	global $config;
	$cache_ver = $config['wx']['cache_ver'];
	$url = str_replace("http://yp.yueus.com/mobile/app?from_app=1","",$url);
	return $url;
}

?>