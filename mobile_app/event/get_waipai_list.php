<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/16
 * Time: 18:09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$limit              = $client_data['data']['param']['limit'];
$time_querys        = $client_data['data']['param']['time_querys'];
$price_querys       = $client_data['data']['param']['price_querys'];
$start_querys       = $client_data['data']['param']['start_querys'];
$remarks_querys     = $client_data['data']['param']['remarks_querys'];

//if(version_compare($client_data['data']['version'], '2.1.0', '>='))
//{
     $keywords           = iconv('utf8', 'gbk', urldecode($client_data['data']['param']['keyword_querys']));
//}else{
//     $keywords           = urldecode($client_data['data']['param']['keyword_querys']);
//}

if(empty($time_querys))     $time_querys    = '';
if(empty($price_querys))    $price_querys   = '';
if(empty($start_querys))    $start_querys   = '';
if(empty($keywords))        $keywords       = '';
if(empty($remarks_querys))  $remarks_querys = '';
//print_r($client_data['data']['param']);

$data['mid'] = '122LT02001';

if($keywords)
{
    $data['name'] = '';
}else{
    $data['name'] = '外拍活动';
}

if($remarks_querys == 'is_top')
{
    $querys["is_top"] = ">0";
}else{
    $querys = '';
}

define('FOR_YUEYUE_ORDER',1);//约约专属排序
if($keywords =='约约精品人像')
{
    $keywords       =   '人精品人像';
    $data['name']   =   '约约精品人像';
}
$ret = event_fulltext_search($time_querys,$price_querys,$start_querys, false, $limit,$location_id, $keywords, $querys);


if($client_data['data']['version'] == '88.8.8')
{
    //$event_id_str = "46737, 46592, 46794, 46764,46753, 46765,46454,46254,46818,46766,46704,46716,46664,46446,45877,45790,45726,44731,45307";
    $ret = get_event_list_no_fulltext($time_querys,$price_querys,$start_querys, false, $limit,$location_id);
}

$version_config = include('/disk/data/htdocs232/poco/pai/config/appstore_version_config.php');
$appstore_version = $version_config['version'];
$android_version = $version_config['android_version'];

if($appstore_version || $android_version)
{
	if($client_data['data']['version']==$appstore_version || $client_data['data']['version']==$android_version)
	{
        //$keywords = '室内';
        $event_id_str = "46592,46794,46764,46753,46454,46818,46766,46716,46664,46446,45877,45790,45726,44731,45307";
	    $ret = get_event_list_no_fulltext($time_querys,$price_querys,$start_querys, false, $limit,$location_id,$event_id_str,$keywords);
	}
}


$return_array = array();
foreach($ret AS $key=>$val)
{
    $return_array['dmid'] = $val['event_id'];
    $return_array['event_id'] = $val['event_id'];
    $return_array['title'] = $val['title'];
    $return_array['nickname'] = $val['nickname'];
    $return_array['start_time'] = $val['start_time'];
    $return_array['budget'] = "￥" . $val['budget'] . "/人";
    $return_array['event_join'] = "报名人数 " . $val['event_join'] . "人";
    $return_array['cover_image'] = $val['cover_image'];
    $url = 'http://yp.yueus.com/mobile/app?from_app=1#act/detail/' . $val['event_id'];
    if(version_compare($client_data['data']['version'], '2.9.9', '>=')) $url = 'http://yp.yueus.com/mall/user/act/detail.php?event_id=' . $val['event_id'];
    $return_array['url']  = "yueyue://goto?type=inner_web&url=" . urlencode($url) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $url));
    if(version_compare($client_data['data']['version'], '2.9.9', '>='))   $return_array['url'] .= "&showtitle=2";
    if($val[is_authority])
    {
        $return_array['icon_text'] = "官方";
        $return_array['icon_bg'] = "1";
    }

    if($val[is_free])
    {
        $return_array['icon_text'] = "免费";
        $return_array['icon_bg'] = "2";
    }

    if($val[is_recommend])
    {
        $return_array['icon_text'] = "推荐";
        $return_array['icon_bg'] = "3";
    }

    if($val['event_status'] == 2 || $val['event_status'] == 3)
    {
        $return_array['is_finish'] = '1';
    }else{
        $return_array['is_finish'] = '0';
    }

    $data['list'][] = $return_array;
    unset($return_array);
}
//var_dump($ret);
$options['data'] = $data;
$cp->output($options);
?>