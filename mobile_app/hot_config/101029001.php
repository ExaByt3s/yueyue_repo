<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );

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
                       
if($yue_login_id)
{
   if($pai_user_obj->check_role($yue_login_id) == 'model')
   {
        $ranking_array = array('15'=>array('', 'pay_cameraman', '', ''),
                            '17'=>array('正在找模特', 'search_cameraman', '', ''),
                            '21'=>array('摄影师 约拍排行榜', 'date_cameraman', '', ''),
                            '20'=>array('摄影师 优评排行榜', 'comment_cameraman', '', '')
                            );
   }
}

return $ranking_array;
?>