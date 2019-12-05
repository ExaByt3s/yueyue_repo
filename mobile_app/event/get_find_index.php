<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$user_id            = $client_data['data']['param']['user_id'];

$ad_list = '';

include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$cms_obj = new cms_system_class();
$rank_id = 16;
$info = $cms_obj->get_last_issue_record_list(false, '0,16', 'place_number DESC', $rank_id);

//苹果市场版 特殊处理 开始
if(version_compare($client_data['data']['version'], '2.0.10', '='))
{
    $info = $cms_obj->get_last_issue_record_list(false, '1,16', 'place_number DESC', $rank_id);
}
//苹果市场版 特殊处理 结束

$tag_list = '';
foreach($info AS $key=>$val)
{
    $top_tag['tag']         = $val['title'];
    $top_tag['tag_id']      = $val['rank_id'];
    $top_tag['dmid']        = "$key";
    $top_tag['url']     = "yueyue://goto?type=inner_app&pid=1220045&tag=" . $top_tag['tag'];

    $tag_list[] = $top_tag;
}


/**
 * 
 * 发现四格内容
 * */
//摄影榜单ID
$rank_id = 6;
$topic_where = " is_effect = 1 AND display_type='all'";
if($user_id)
{
    $pai_user_obj = POCO::singleton ( 'pai_user_class' );
    if($pai_user_obj->check_role($user_id) == 'model')
    {
        //模特榜单ID
        $rank_id = 7;
        $topic_where = " is_effect = 1 AND (display_type='model' OR display_type='all') AND type != 'weixin'";
    }else{
        $topic_where = " is_effect = 1 AND (display_type='cameraman' OR display_type='all') AND type != 'weixin'";
    }
}
$info = $cms_obj->get_last_issue_record_list(false, '0,4', 'place_number DESC', $rank_id);
foreach($info AS $key=>$val)
{
    $centent_ad_list[$key]['ad_img']    = $val['img_url'];
    $centent_ad_list[$key]['ad_url']    = $val['link_url'];
    $centent_ad_list[$key]['ad_type']   = $val['remark'];
    $centent_ad_list[$key]['dmid']      = "$key";

     if($val[link_type] == 'inner_web')
     {
         $centent_ad_list[$key]['ad_url']    = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
     }elseif($val[link_type] == 'inner_app'){
         $centent_ad_list[$key]['ad_url']    = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $val['user_id'];
     }
     unset($centent_ad_list[$key]['ad_type']);

}

//苹果市场版 特殊处理 开始
if(version_compare($client_data['data']['version'], '2.0.10', '='))
{
    $centent_ad_list[1]['ad_url']    = "yueyue://goto?type=inner_web&url=" . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/65') . "&wifi_url=" . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/65');
    $topic_where = " id IN (121, 99, 107, 64, 62) ";
}
//苹果市场版 特殊处理 结束

$topic_obj = POCO::singleton ('pai_topic_class');
$result = $topic_obj->get_topic_list(false, $topic_where, 'sort DESC', '0,5');
$topic_list = '';
foreach($result AS $key=>$val)
{
    $topic_val['cover_image']   = $val['cover_image'];
    $topic_val['url']               = 'http://yp.yueus.com/mobile/app?from_app=1#topic/' . $val['id'];
    $topic_val['url_wifi']          = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/' . $val['id'];
    $topic_val['dmid']              = "$key";
    $topic_val['url']          = "yueyue://goto?type=inner_web&url=" . urlencode($topic_val['url']) . "&wifi_url=" . urlencode($topic_val['url_wifi']);
    $topic_val['url_wifi']    = $topic_val['url'] ;
    unset($topic_val['url_wifi']);
    
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
    $ad_list['topic_list']['list']     = $topic_list;
}       
$ad_list['topic_more']  = 'http://yp.yueus.com/mobile/app?from_app=1#topic_list';
$ad_list['topic_name']  = '火热专题';
$ad_list['topic_more'] = "yueyue://goto?type=inner_web&url=" . urlencode($ad_list['topic_more']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $ad_list['topic_more']));

$options['data'] = $ad_list;

$cp->output($options);
?>