<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$location_id    = $client_data['data']['param']['location_id'];

include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$cms_obj        = new cms_system_class();

if($client_data['data']['app_name'] == 'poco_yuepai_android')
{
    switch($location_id)
    {
        //广州
        case 101029001:
            $rank_id = 146;
            break;

        //武汉
        case 101019001:
            $rank_id = 147;
            break;

        //北京
        case 101001001:
            $rank_id = 149;
            break;

        //上海
        case 101003001:
            $rank_id = 152;
            break;

        //成都
        case 101022001:
            $rank_id = 154;
            break;

        //重庆
        case 101004001:
            $rank_id = 156;
            break;

        //西安
        case 101015001:
            $rank_id = 159;
            break;

        //新疆
        case 101024001:
            $rank_id = 202;
            break;

        default:
            $rank_id = 146;
            break;
    }
}else{
    switch($location_id)
    {
        //广州
        case 101029001:
            $rank_id = 145;
            break;

        //武汉
        case 101019001:
            $rank_id = 148;
            break;

        //北京
        case 101001001:
            $rank_id = 150;
            break;

        //上海
        case 101003001:
            $rank_id = 153;
            break;

        //成都
        case 101022001:
            $rank_id = 155;
            break;

        //重庆
        case 101004001:
            $rank_id = 157;
            break;

        case 101015001:
            $rank_id = 158;
            break;

        //新疆
        case 101024001:
            $rank_id = 203;
            break;


        default:
            $rank_id = 145;
            break;
    }
}


//测试开发 开始
if(version_compare($client_data['data']['version'], '88.8.8', '>='))
{
    $rank_id = 117;
}
//$rank_id = 117;
//测试开发 结束
$info = $cms_obj->get_last_issue_record_list(false, '0,6', 'place_number DESC', $rank_id);
foreach($info AS $key=>$val)
{
    $ad_list[$key]['ad_img']    = $val['img_url'];
    $ad_list[$key]['ad_url']    = $val['link_url'];
    $ad_list[$key]['vid']       = "";
    $ad_list[$key]['jid']       = "";
    $ad_list[$key]['dmid']      = "ad-" . $rank_id;

    if($val[link_type] == 'inner_web')
    {
        $ad_list[$key]['ad_url']    = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
    }elseif($val[link_type] == 'inner_app'){
        $ad_list[$key]['ad_url']    = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $val['user_id'];
    }
}

//测试开发 开始
/**
if(version_compare($client_data['data']['version'], '2.1.10', '='))
{
    $ad_list[0]['ad_img']    = 'http://image17-c.poco.cn/yueyue/cms/20150515/44642015051519363320208658_640.jpg';
    $ad_list[0]['ad_url']    = 'http://yp.yueus.com/mobile/app?from_app=1#topic/178';
    $ad_list[0]['vid']       = "";
    $ad_list[0]['jid']       = "";
    $ad_list[0]['dmid']      = "apple";
    $ad_list[0]['ad_url']    = "yueyue://goto?type=inner_web&url=" . urlencode($ad_list[0]['ad_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $ad_list[0]['ad_url']));
}
**/
if($ad_list)
{
    $options['data']['list'] = $ad_list;
    //$options['data']['list'] = array();
    $options['data']['mid']  = '122PT01001';
}

$cp->output($options);
?>