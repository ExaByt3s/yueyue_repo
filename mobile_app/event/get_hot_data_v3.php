<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];


$score_obj              = POCO::singleton ('pai_score_rank_class');
$date_obj               = POCO::singleton ('pai_date_rank_class');
$user_obj               = POCO::singleton ('pai_model_style_v2_class');
$pic_obj                = POCO::singleton ('pai_pic_class');
$pic_score_obj          = POCO::singleton ('pai_score_rank_class');
$model_card_obj         = POCO::singleton ('pai_model_card_class');
$cameraman_card_obj     = POCO::singleton ('pai_cameraman_card_class');
$rank_event_obj         = POCO::singleton('pai_rank_event_class');
$pai_user_obj           = POCO::singleton ( 'pai_user_class' );
$pai_cms_parse_obj      = POCO::singleton( 'pai_cms_parse_class' );
$cms_obj                = new cms_system_class();


if(version_compare($client_data['data']['version'], '1.0.5', '>='))
{
//检查身份
    $role = $pai_user_obj->check_role($user_id);

//初始化数据
    if(empty($role))        $role         = 'cameraman';
    if(empty($location_id)) $location_id  = '101029001';

    $ranking_array = $rank_event_obj->get_rank_event_by_location_id($location_id, $role);
//print_r($ranking_array);
    $data = $pai_cms_parse_obj->cms_parse_by_array($ranking_array);
    $options['data'] = $data;
}
else
{
//排行榜单
    $ranking_array = array('12'=>array('每日新模', 'new_model', '魅力', ''),
        '14'=>array('私拍比外拍更便宜', 'spbwpgpy', '小时', ''),
        '11'=>array('比基尼诱惑', 'xiong_model', '魅力', ''),
        '3'=>array('魅力排行榜', 'score_list', '魅力', 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/charm_list'),
        '5'=>array('优评排行榜', 'comment_list', '分', 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/date_list'),
        '10'=>array('性感女王 私房专属', 'hot_model', '小时', ''),
        '13'=>array('萌妹子等你约', 'recommend_model', '小时', ''),
        '27'=>array('长腿女神 完美比例', 'ctns_wmbl', '魅力', ''),
        '28'=>array('女神学堂毕业啦', 'nsxtbyl', '魅力', ''),
        '29'=>array('微笑天使 爱笑爱聊', 'wxts_axax', '魅力', ''),
        '31'=>array('约约推荐 出片保证', 'yytj_cpbz', '小时', ''),
        '30'=>array('更多模特', 'gdmt', '魅力', ''),
    );


    $pai_user_obj = POCO::singleton ( 'pai_user_class' );
    if($pai_user_obj->check_role($user_id) == 'model')
    {
        $role = 1;
        $ranking_array = array('15'=>array('', 'pay_cameraman', '', ''),
            '17'=>array('正在找模特', 'search_cameraman', '', ''),
            '21'=>array('摄影师 约拍排行榜', 'date_cameraman', '', ''),
            '20'=>array('摄影师 优评排行榜', 'comment_cameraman', '', '')
        );
    }

    if(version_compare($client_data['data']['version'], '1.0.4', '>='))
    {
        if($pai_user_obj->check_role($user_id) == 'model')
        {
            $role = 1;
            $ranking_array = array('15'=>array('', 'pay_cameraman', '', ''),
                '17'=>array('正在找模特', 'search_cameraman', '', ''),
                '21'=>array('摄影师 约拍排行榜', 'date_cameraman', '', ''),
                '20'=>array('摄影师 优评排行榜', 'comment_cameraman', '', '')
            );
        }else{
            $role = 0;
            switch($location_id)
            {
                case 101019001:
                    $ranking_array = array('24'=>array('每日新模', 'whb', '小时', ''),
                        //'32'=>array('魅力值排行榜', 'wh_mlbxb', '魅力', ''),
                        //'33'=>array('性感女王 私房专属','wh_xgnw', '魅力', ''),
                        '34'=>array('约约推荐 出片保证','wh_yytj', '小时', ''),
                        //'35'=>array('更多模特', 'wh_gdmt', '魅力', '')
                    );
                    break;

                case 101001001:
                    $ranking_array = array('25'=>array('每日新模', '25', '小时', ''),
                        //'36'=>array('魅力值排行榜', '36', '魅力', ''),
                        '37'=>array('性感女王 私房专属','37', '魅力', ''),
                        '38'=>array('约约推荐 出片保证','38', '小时', ''),
                        '39'=>array('更多模特', '39', '魅力', ''));
                    break;

                case 101003001:
                    $ranking_array = array('26'=>array('每日新模', '26', '小时', ''),
                        //'40'=>array('魅力值排行榜', '40', '魅力', ''),
                        '41'=>array('性感女王 私房专属','41', '魅力', ''),
                        '42'=>array('约约推荐 出片保证','42', '小时', ''),
                        '43'=>array('更多模特', '43', '魅力', ''));
                    break;

                case 101004001:
                    $ranking_array = array('44'=>array('每日新模', '44', '小时', ''),
                        //'45'=>array('魅力值排行榜', '45', '魅力', ''),
                        //'46'=>array('性感女王 私房专属','46', '魅力', ''),
                        '47'=>array('约约推荐 出片保证','47', '小时', ''),
                        //'48'=>array('更多模特', '48', '魅力', '')
                    );
                    break;

                case 101022001:
                    $ranking_array = array('49'=>array('每日新模', '49', '小时', ''),
                        //'50'=>array('魅力值排行榜', '50', '魅力', ''),
                        '51'=>array('性感女王 私房专属','51', '魅力', ''),
                        '52'=>array('约约推荐 出片保证','52', '小时', ''),
                        '53'=>array('更多模特', '53', '魅力', ''));
                    break;
            }
        }
    }

    /**
    if($client_data['data']['version'] == '88.8.8'){
    $ranking_array = array('22'=>array('测试排行版', 'test_list', '小时', ''));
    }
     **/

    foreach($ranking_array AS $key=>$val)
    {
        $data  = '';
        if($val[1] == 'score_list' || $val[1] == 'comment_list')
        {
            $info = $cms_obj->get_last_issue_record_list(false, '0,4', 'place_number DESC', $key);
            $info_count = $cms_obj->get_last_issue_record_list(TRUE, '0,4', 'place_number DESC', $key);
        }else{
            $info = $cms_obj->get_last_issue_record_list(false, '0,4', 'place_number ASC', $key);
            $info_count = $cms_obj->get_last_issue_record_list(TRUE, '0,4', 'place_number DESC', $key);
        }


        $data['name']  = $val[0];
        $data['query'] = $val[1];
        if(version_compare($client_data['data']['version'], '1.0.5', '>='))
        {
            if($info_count > 4) $data['query_str'] ='更多';
        }

        if(version_compare($client_data['data']['version'], '1.0.5', '>='))
        {
            $data['mid']   = "122PT02001";
            $data['dmid']  = "$key";
        }

        if($val[3])
        {
            $data['about'] = $val[3];
        }else{
            $data['about'] = "";
        }

        $unit = $val[2];
        $record = '';
        foreach($info AS $k=>$v)
        {
            $record['user_id']      = $v['user_id'];
            if(version_compare($client_data['data']['version'], '1.0.5', '>='))
            {
                $record['vid']          = $v['user_id'];
                $record['jid']          = "001";
                $record['dmid']         = "$key";

            }

            if($role)
            {

                $record['url']          = 'http://yp.yueus.com/mobile/app?from_app=1#zone/' . $v['user_id'] . '/cameraman';
                $record['url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/' . $v['user_id'] . '/cameraman';


                if($key == 15)
                {
                    $record['user_icon'] = $v['img_url'];
                    $record['nickname']  = $v['title'];
                    $record['url']       = $v['link_url'];
                    $record['url_wifi']  = str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']);
                    $record['num']       = $v['remark'];
                    $record['style']     = '详情';
                }elseif($role){
                    $record['user_icon'] = $v['img_url'];
                    $record['nickname']  = $v['title'];
                    $record['num']       = $v['remark'];
                    $record['style']     = '';
                }
                //头像修改
                $pic_array = $pic_obj->get_user_pic($v['user_id'],  '0,5');
                foreach($pic_array AS $a=>$b)
                {

                    $num = explode('?', $b['img']);
                    $num = explode('x', $num[1]);
                    $num_v2 = explode('_', $num[1]);

                    $width = $num[0];
                    $height = $num_v2[0];

                    if($width<$height)
                    {
                        $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                        break;
                    }
                    $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                }

                if(version_compare($client_data['data']['version'], '1.0.5', '>=')){
                    $cameraman_card_info = $cameraman_card_obj->get_cameraman_card_info($v['user_id']);
                    if($cameraman_card_info['cover_img'])
                    {
                        $record['user_icon'] = $cameraman_card_info['cover_img'];
                    }
                }

            }else{
                $record['url']          = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/' . $v['user_id'];
                $record['url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/' . $v['user_id'];

                if($unit == '魅力')
                {
                    $result                 = $pic_score_obj->get_score_rank($v['user_id']);
                    $record['num']          = $result['score'];
                    $record['unit']         = $unit;
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

                }

                $record['nickname']     = $v['user_name'];
                $record['user_icon']    =  get_user_icon($v['user_id'], $size = 468);

                //头像修改
                $pic_array = $pic_obj->get_user_pic($v['user_id'], $limit = '0,5');
                foreach($pic_array AS $a=>$b)
                {

                    $num = explode('?', $b['img']);
                    $num = explode('x', $num[1]);
                    $num_v2 = explode('_', $num[1]);

                    $width = $num[0];
                    $height = $num_v2[0];

                    if($width<$height)
                    {
                        $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                        break;
                    }
                    $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                }
                $user_info              = $user_obj->get_model_style_combo($v['user_id']);
                $style_array            = explode(' ', $user_info['main'][0]['style']);
                $record['style']        = $style_array[0]?$style_array[0]:'清新';



                if(version_compare($client_data['data']['version'], '1.0.5', '>=')){

                    $model_card_info = $model_card_obj->get_model_card_info($v['user_id']);

                    if($model_card_info['cover_img'])
                    {
                        $record['user_icon'] = $model_card_info['cover_img'];
                    }
                }

            }

            $data['user_list'][]      = $record;

        }



        $options['data'][] = $data;
        $data = '';
    }
}

$cp->output($options);
?>