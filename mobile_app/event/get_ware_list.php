<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];
$query       = (int)$client_data['data']['param']['query'];

//统计测试
$tongji_obj = POCO::singleton('pai_tongji_class');

if(!$query)  $query =9422;

$data['pid'] = '1220094';
$data['mid'] = '';


if($query == '9421' || $query == '9474' || $query == '9479' || $query == '9484' || $query == '9518' || $query == '9489' || $query == '12267' || $query == '12268') $data['search_url'] = 'yueyue://goto?type=inner_app&pid=1220097&search_type=yuepai&url=' . urlencode('yueyue://goto?type=inner_app&pid=1220099&key=default');
if($query == '9948') $data['search_url'] = 'yueyue://goto?type=inner_app&pid=1220098&search_type=waipai&url=' . urlencode('yueyue://goto?type=inner_app&pid=1220076&key=default');



$pai_home_page_topic_obj = POCO::singleton('pai_home_page_topic_class');
$str_result = $pai_home_page_topic_obj->get_category_text($query);


$data['title'] = $str_result['top1'];

$banner_list             = $pai_home_page_topic_obj->get_banner_list($query);
foreach($banner_list AS $key=>$val)
{
    $top_banner['str']      = $val['title'];
    $top_banner['img']      = $val['app_img'];
    if($val['link_url']) {
        if ($val['link_type'] != 'inner_web') {
            $top_banner['url'] = $val['link_url'];
        } else {
            $top_banner['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
        }
    }else{
        $top_banner['url'] = '';
    }
    $data['banner_list'][]  = $top_banner;
    $remark =  $top_banner['str'] . "|" . $query;
    $tongji_obj->add_tongji_log($top_banner['img'], $top_banner['url'], 'get_ware_list:banner', $location_id, $user_id, $remark);
}

/**
$top_banner['str'] = '我们一起去探索 | 摄影培训';
$top_banner['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/39942015060415264825965487_640.jpg';
$top_banner['url'] = '';
$data['banner_list'][] = $top_banner;

$top_banner['str'] = '我们一起去探索 | 摄影培训';
$top_banner['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/22132015060415273827254635_640.jpg';
$top_banner['url'] = '';
$data['banner_list'][] = $top_banner;
**/

$data['category_title']     = $str_result['top2'];
$data['category_content']   = $str_result['top3'];

$array_goods_id = array();
$category_list = $pai_home_page_topic_obj->get_small_category($query);
foreach($category_list AS $key=>$val)
{
    $category['str'] = $val['title'];
    $category['img'] = $val['app_img'];
    if($val['link_type'] != 'inner_web')
    {
        $category['url'] = $val['link_url'];
    }else{
        $category['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
    }
    $data['category_list'][] = $category;
    $remark =  $category['str'] . "|" . $query;
    $tongji_obj->add_tongji_log($category['img'], $category['url'], 'get_ware_list:category', $location_id, $user_id, $remark);
    //if(version_compare($client_data['data']['version'], '2.2.10', '=') && $query == 9948) break;

}

/**
$category['str'] = '跟星空摄影第一人学拍星空';
$category['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/71782015060415283286545966_640.jpg';
$category['url'] = '';
$data['category_list'][] = $category;

$category['str'] = '零基础速成';
$category['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/60112015060415292683838953_640.jpg';
$category['url'] = '';
$data['category_list'][] = $category;

$category['str'] = '学摄影';
$category['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/93962015060415294260887730_640.jpg';
$category['url'] = '';
$data['category_list'][] = $category;

$category['str'] = '学后期';
$category['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/57232015060415300019813826_640.jpg';
$category['url'] = '';
$data['category_list'][] = $category;

$category['str'] = '学光影';
$category['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/5870201506041530154887559_640.jpg';
$category['url'] = '';
$data['category_list'][] = $category;

$category['str'] = '私人定制专属课程'."\r\n".'4000-82-9003';
$category['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/72072015060415311941961153_640.jpg';
$category['url'] = '';
$data['category_list'][] = $category;
**/

$data['topic_title']  = $str_result['top4'];

$goods_result = $pai_home_page_topic_obj->get_category_goods($query);
if($goods_result)
{
    foreach($goods_result AS $key=>$val)
    {
        $topic['img'] = $val['app_img'];
        if($val['link_type'] != 'inner_web')
        {
            $topic['url'] = $val['link_url'];
        }else{
            $topic['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
        }
        $data['topic_list'][] = $topic;
        $tongji_obj->add_tongji_log($topic['img'], $topic['url'], 'get_ware_list:goods', $location_id, $user_id, $query);
    }
}else{
    $data['topic_list'] = array();
}


/**
$topic['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/93222015060415313718968848_640.jpg';
$topic['url'] = '';
$data['topic_list'][] = $topic;

$topic['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/8092201506041532427642676_640.jpg';
$topic['url'] = '';
$data['topic_list'][] = $topic;

$topic['img'] = 'http://image17-c.poco.cn/yueyue/cms/20150604/19602015060415330332734932_640.jpg';
$topic['url'] = '';
$data['topic_list'][] = $topic;
**/
$task_request_obj = POCO::singleton('pai_task_request_class');
if($task_request_obj->get_request_is_have($user_id))
{
    $data[tt_link] = "yueyue://goto?type=inner_app&pid=1220079";
}else{
    $data[tt_link] = "yueyue://goto?type=inner_app&pid=1220080";
}


$options['data'] = $data;
$cp->output($options);
?>