<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$cms_obj        = new cms_system_class();

$hour = date('H');

if($hour == 1)
{
    //约拍排行榜
    $rank_id    = 4;
    $obj   = POCO::singleton('pai_date_rank_class');
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);
    $date_info  = $obj->get_date_rank('101029001', '0,11');
    foreach($date_info AS $key=>$val)
    {
		if($val['user_id'] != 100040) $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }
    
    //魅力排行榜
    $rank_id    = 3;
    $obj   = POCO::singleton('pai_score_rank_class');
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);
    $date_info  = $obj->get_score_rank_list('101029001', '0,20');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }
    
    //优评排行榜
    $rank_id    = 5;
    $obj   = POCO::singleton('pai_comment_score_rank_class');
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);
    $date_info  = $obj->get_comment_rank('101029001', '0,10');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }
}else{
    $rank_id    = 4;
    $result = $cms_obj->get_last_issue_record_list(false, '0, 20', 'place_number ASC', $rank_id);
    foreach($result AS $key=>$val)
    {
        $issue_id = $val['issue_id'];
        $log_id   = $val['log_id'];
        $cms_obj->del_issue_record_by_log_id($log_id, $issue_id);
    }
    $obj   = POCO::singleton('pai_date_rank_class');
    $date_info  = $obj->get_date_rank('101029001', '0,10');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }
    
    unset($issue_id);
    unset($log_id);
    $rank_id    = 3;
    $result = $cms_obj->get_last_issue_record_list(false, '0, 20', 'place_number ASC', $rank_id);
    foreach($result AS $key=>$val)
    {
        $issue_id = $val['issue_id'];
        $log_id   = $val['log_id'];
        $cms_obj->del_issue_record_by_log_id($log_id, $issue_id);
    }
    $obj   = POCO::singleton('pai_score_rank_class');
    $date_info  = $obj->get_score_rank_list('101029001', '0,20');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }
    
    unset($issue_id);
    unset($log_id);
    $rank_id    = 5;
    $result = $cms_obj->get_last_issue_record_list(false, '0, 20', 'place_number ASC', $rank_id);
    foreach($result AS $key=>$val)
    {
        $issue_id = $val['issue_id'];
        $log_id   = $val['log_id'];
        $cms_obj->del_issue_record_by_log_id($log_id, $issue_id);
    }
    $obj   = POCO::singleton('pai_comment_score_rank_class');
    $date_info  = $obj->get_comment_rank('101029001', '0,10');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }
    unset($issue_id);
    unset($log_id);
}

if($hour == 1)
{
    //魅力排行榜 北京
    $rank_id    = 260;
    $obj   = POCO::singleton('pai_score_rank_class');
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);
    $date_info  = $obj->get_score_rank_list('101001001', '0,20');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }

    //魅力排行榜 武汉
    $rank_id    = 261;
    $obj   = POCO::singleton('pai_score_rank_class');
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);
    $date_info  = $obj->get_score_rank_list('101019001', '0,20');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }

    //魅力排行榜 上海
    $rank_id    = 262;
    $obj   = POCO::singleton('pai_score_rank_class');
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);
    $date_info  = $obj->get_score_rank_list('101003001', '0,20');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }

    //魅力排行榜 成都
    $rank_id    = 263;
    $obj   = POCO::singleton('pai_score_rank_class');
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);
    $date_info  = $obj->get_score_rank_list('101022001', '0,20');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }

    //魅力排行榜 重庆
    $rank_id    = 264;
    $obj   = POCO::singleton('pai_score_rank_class');
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);
    $date_info  = $obj->get_score_rank_list('101004001', '0,20');
    foreach($date_info AS $key=>$val)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $val['nickname'], $val['num']);
    }



}
?>