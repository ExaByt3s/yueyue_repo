<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$query              = $client_data['data']['param']['query'];
$limit              = $client_data['data']['param']['limit'];

$score_obj              = POCO::singleton ('pai_score_rank_class');
$date_obj               = POCO::singleton ('pai_date_rank_class'); 
$user_obj               = POCO::singleton ('pai_model_style_v2_class');
$pic_obj                = POCO::singleton ('pai_pic_class');
$pic_score_obj          = POCO::singleton ('pai_score_rank_class');
$model_card_obj         = POCO::singleton ('pai_model_card_class');
$cameraman_card_obj     = POCO::singleton ('pai_cameraman_card_class');
$rank_event_obj         = POCO::singleton( 'pai_rank_event_class' );
$pai_user_obj           = POCO::singleton ( 'pai_user_class' );

$cms_obj        = new cms_system_class();
/**
if(version_compare($client_data['data']['version'], '1.0.5', '>='))
{
    if($query)
    {
        $rank_info  = $rank_event_obj->get_rank_event_info_by_id($query);
        $name       = '';
        $rank_id    = $rank_info['rank_id'];
        $about      = $rank_info['rank_desc'];
        $unit = '魅力';
        if($rank_info['unit'] == 2) $unit = '小时';
        if($rank_info['unit'] == 1) $unit = '魅力';
        $show_num   = '';
        if($rank_info['rank_desc']) $order = 'DESC';
    }else
    {
        $name = '';
        $rank_id = $query;
        $about = '';
        $unit = '魅力';
        $role = 0;
    }
}
else
**/
{
//    switch($query)
//    {
//        case 'score_list':
//            $name = '魅力排名榜';
//            $rank_id = 3;
//            $about = 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/charm_list';
//            $unit = '魅力';
//            $show_num = 1;
//            $order = 'DESC';
//            break;
//
//        case 'date_list':
//            $name = '约拍排名榜';
//            $rank_id = 4;
//            $about = 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/date_list';
//            $unit = '次';
//            $show_num = 1;
//            $order = 'DESC';
//            break;
//
//        case 'comment_list':
//            $name = '优评排名榜';
//            $rank_id = 5;
//            $about = 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/commit_list';
//            $unit = '分';
//            $show_num = 1;
//            $order = 'DESC';
//            break;
//
//        case 'hot_model':
//            $name = '性感女王 私房专属';
//            $rank_id = 10;
//            $about = '';
//            $unit = '小时';
//            break;
//
//        case 'xiong_model':
//            $name = '美胸热模';
//            $rank_id = 11;
//            $about = '';
//            $unit = '魅力';
//            break;
//
//        case 'new_model':
//            $name = '每日新模';
//            $rank_id = 12;
//            $about = '';
//            $unit = '魅力';
//            break;
//
//        case 'recommend_model':
//            $name = '萌妹子等你约';
//            $rank_id = 13;
//            $about = '';
//            $unit = '小时';
//            break;
//
//        case 'test_model':
//            $name = '新模试镜';
//            $rank_id = 14;
//            $about = '';
//            $unit = '魅力';
//            break;
//
//        case 'pay_cameraman':
//            $name = '交易排行榜';
//            $rank_id = 15;
//            $about = '';
//            $unit = '魅力';
//            $role = 1;
//            break;
//
//        case 'search_cameraman':
//            $name = '交易排行榜';
//            $rank_id = 17;
//            $about = '';
//            $unit = '魅力';
//            $role = 1;
//            break;
//
//        case 'date_cameraman':
//            $name = '约拍排行榜';
//            $rank_id = 20;
//            $about = '';
//            $unit = '魅力';
//            $role = 1;
//            break;
//
//        case 'comment_cameraman':
//            $name = '优评排行榜';
//            $rank_id = 21;
//            $about = '';
//            $unit = '魅力';
//            $role = 1;
//            break;
//
//        case 'test_list':
//            $name = '测试排行版';
//            $rank_id = 22;
//            $about = '';
//            $unit = '魅力';
//            $role = 1;
//            break;
//
//        case 'whb':
//            $name = '武汉榜';
//            $rank_id = 24;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 'wh_yytj':
//            $name = '约约推荐 出片保证';
//            $rank_id = 34;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 'bjb':
//            $name = '北京榜';
//            $rank_id = 25;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 'shb':
//            $name = '上海榜';
//            $rank_id = 26;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 'spbwpgpy':
//            $name = '私拍比外拍更便宜';
//            $rank_id = 14;
//            $about = '';
//            $unit = '小时';
//            $role = 0;
//            break;
//
//        case 'ctns_wmbl':
//            $name = '长腿女神 完美比例';
//            $rank_id = 27;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//
//        case 'nsxtbyl':
//            $name = '女神学堂毕业啦';
//            $rank_id = 28;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 'wxts_axax':
//            $name = '微笑天使 爱笑爱聊';
//            $rank_id = 29;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 'yytj_cpbz':
//            $name = '约约推荐 出片保证';
//            $rank_id = 31;
//            $about = '';
//            $unit = '小时';
//            $role = 0;
//            break;
//
//        case 'gdmt':
//            $name = '更多模特';
//            $rank_id = 30;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '25':
//            $name = '每日新模';
//            $rank_id = 25;
//            $about = '';
//            $unit = '';
//            $role = 0;
//            break;
//
//        case '36':
//            $name = '魅力值排行榜';
//            $rank_id = 36;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '37':
//            $name = '性感女王 私房专属';
//            $rank_id = 37;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '38':
//            $name = '约约推荐 出片保证';
//            $rank_id = 38;
//            $about = '';
//            $unit = '';
//            $role = 0;
//            break;
//
//        case '39':
//            $name = '更多模特';
//            $rank_id = 39;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '26':
//            $name = '每日新模';
//            $rank_id = 26;
//            $about = '';
//            $unit = '';
//            $role = 0;
//            break;
//
//        case '40':
//            $name = '魅力值排行榜';
//            $rank_id = 40;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '41':
//            $name = '性感女王 私房专属';
//            $rank_id = 41;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '42':
//            $name = '约约推荐 出片保证';
//            $rank_id = 42;
//            $about = '';
//            $unit = '';
//            $role = 0;
//            break;
//
//        case '43':
//            $name = '更多模特';
//            $rank_id = 43;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '44':
//            $name = '每日新模';
//            $rank_id = 44;
//            $about = '';
//            $unit = '';
//            $role = 0;
//            break;
//
//        case '45':
//            $name = '魅力值排行榜';
//            $rank_id = 45;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '46':
//            $name = '性感女王 私房专属';
//            $rank_id = 46;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case '47':
//            $name = '约约推荐 出片保证';
//            $rank_id = 47;
//            $about = '';
//            $unit = '';
//            $role = 0;
//            break;
//
//        case 48:
//            $name = '更多模特';
//            $rank_id = 48;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 49:
//            $name = '每日新模';
//            $rank_id = 49;
//            $about = '';
//            $unit = '';
//            $role = 0;
//            break;
//
//        case 50:
//            $name = '魅力值排行榜';
//            $rank_id = 50;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 51:
//            $name = '性感女王 私房专属';
//            $rank_id = 51;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//
//        case 52:
//            $name = '约约推荐 出片保证';
//            $rank_id = 52;
//            $about = '';
//            $unit = '';
//            $role = 0;
//            break;
//
//        case 53:
//            $name = '更多模特';
//            $rank_id = 53;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//        default:
//            $name = '';
//            $rank_id = $query;
//            $about = '';
//            $unit = '魅力';
//            $role = 0;
//            break;
//    }
}

$rank_info  = $rank_event_obj->get_rank_event_appinfo($query);
$name       = $cms_obj->get_rank_info_by_rank_id($rank_info[rank_id], 'rank_name');
$rank_id    = $rank_info[rank_id];
$about      = $rank_info[url];
$unit       = $rank_info[unit];
if($rank_info[app_sort] == 'place_number DESC')
{
    $show_num = 1;
    $order = 'DESC';
}

if($about) $about  = "yueyue://goto?type=inner_web&url=" . urlencode($about) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $about));

if($order)
{
    $info = $cms_obj->get_last_issue_record_list(false, $limit, 'place_number DESC', $rank_id);    
}else{
    $info = $cms_obj->get_last_issue_record_list(false, $limit, 'place_number ASC', $rank_id);
}


$data['name'] = $name;
$data['mid']   = "122LT01001";
$data['dmid']  = "";

if ($rank_id == 3||$rank_id == 4||$rank_id == 5)
{
    $data['mid']   = "122LT01003";
}


if($about)
{
    $data['about'] = $about;    
}

foreach($info AS $key=>$v)
{
    $record['user_id']      = $v['user_id'];
    $record['vid']           = $v['user_id'];
    $record['jid']           = "001";

    $role = $pai_user_obj->check_role($record['user_id']);

    if($role == 'cameraman')
    {
        $record['url']          = 'http://yp.yueus.com/mobile/app?from_app=1#zone/' . $v['user_id'] . '/cameraman';
        $record['url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/' . $v['user_id'] . '/cameraman';;
        $record['url']          = "yueyue://goto?type=inner_app&pid=1220026&mid=122RO02001&user_id=" . $v['user_id'];
        unset($record['url_wifi']);

        $record['num']      = '约拍 ' . $date_obj->count_cameraman_take_photo_times($v['user_id']);
        $record['unit']     = '次';
        $record['style']    = '';
        $record['nickname'] = $v['user_name'];
        
        if($rank_id == 15)
        {
            $record['url']          = $v['link_url'];
            $record['url_wifi']     = $v['link_url']; 
            $record['num']          = '';
            $record['unit']         = '';
            $record['style']        = ''; 
            
            $record['user_icon'] = $v['img_url'];
            $record['nickname']  = $v['remark'];

            $record['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($v['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']));
            unset($record['url_wifi']);

        }

        $record['user_icon']    =  get_user_icon($v['user_id'],  468);
        //头像修改
        $pic_array = $pic_obj->get_user_pic($v['user_id'],  '0,5');
        if($pic_array) {
            foreach ($pic_array AS $key => $val) {
                $num = explode('?', $val['img']);
                $num = explode('x', $num[1]);
                $num_v2 = explode('_', $num[1]);

                $width = $num[0];
                $height = $num_v2[0];

                if ($width < $height) {
                    $record['user_icon'] = str_replace("_260.", "_440.", $val['img']);
                    break;
                }
                $record['user_icon'] = str_replace("_260.", "_440.", $val['img']);
            }
        }

        $cameraman_card_info = $cameraman_card_obj->get_cameraman_card_info($v['user_id']);
        if($cameraman_card_info['cover_img']) $record['user_icon'] = $cameraman_card_info['cover_img'];
        
        
        
    }else{
        
        if($unit == '魅力')
        {
                $result                 = $pic_score_obj->get_score_rank($v['user_id']);
                $record['num']          = $result['score'];
                $record['unit']          = $unit;        
        }elseif($unit == '分'){
            $record['num']          = $v['place_number'] * 2; 
            $record['unit']         = $unit;      
        }elseif($unit == '小时'){
            $sql_str = "SELECT hour, price FROM pai_db.pai_model_style_v2_tbl WHERE user_id=$record[user_id] AND group_id=1";
            $result = db_simple_getdata($sql_str, TRUE, 101);
            if($result['hour'])
            {
                $record['num']          = $result['price'] . "/" . $result['hour'];
                $record['unit']         = $unit;                          
            }            
        }elseif($unit == '备注') {
            $record['num']          = $v['remark'];
           // $record['unit']         = $unit;
        }else{
            $record['num']          = '';
            $record['unit']         = $unit;
        }
        
    
    
        $record['nickname']     = $v['user_name'];
        $record['user_icon']    =  get_user_icon($v['user_id'],  468);
     
        //头像修改
        $pic_array = $pic_obj->get_user_pic($v['user_id'], $limit = '0,5');
        if($pic_array)
        {
            foreach($pic_array AS $key=>$val)
            {
                $num = explode('?', $val['img']);
                $num = explode('x', $num[1]);
                $num_v2 = explode('_', $num[1]);

                $width = $num[0];
                $height = $num_v2[0];

                if($width<$height)
                {
                    $record['user_icon'] = str_replace("_260.", "_440.", $val['img']);
                    break;
                }
                $record['user_icon'] = str_replace("_260.", "_440.", $val['img']);
            }
        }

        $model_card_info = $model_card_obj->get_model_card_info($v['user_id']);
        if($model_card_info['cover_img']) $record['user_icon'] = $model_card_info['cover_img'];

        $user_info              = $user_obj->get_model_style_combo($v['user_id']);
        $style_array            = explode(' ', $user_info['main'][0]['style']);
        $record['style']        = $style_array[0]?$style_array[0]:'清新';
        $record['style']        = '擅长 ' . $record['style'];
        
        $record['url']          = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/' . $v['user_id'];
        $record['url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/' . $v['user_id'];

        $record['url'] = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $v['user_id'];
        unset($record['url_wifi']);

        //打折布点
        $obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
        $tag = $obj->get_topic_user_tag($v['user_id']);
        if($tag) $record['tips'] = $tag;

        $record['tips'] = '特价';

    }

 

    
    $data['user_list'][]      = $record;
    $record = '';
}

if($data) $options['data'][] = $data;
//$options['data'][] = $data;
$cp->output($options);
?>