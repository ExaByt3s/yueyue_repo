<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$cms_obj        = new cms_system_class();
$score_obj      = POCO::singleton ('pai_score_rank_class');
$date_obj       = POCO::singleton ('pai_date_rank_class'); 
$comment_obj    = POCO::singleton ('pai_comment_score_rank_class');

$location_id = 101029001;

//���а�
$ranking_array = array('3'=>array('��������', 'score_list', $score_obj->get_score_rank_list($location_id, '0,10')), 
                       '4'=>array('Լ������', 'date_list', $date_obj->get_date_rank($location_id, '0,10')),
                       '5'=>array('��������', 'comment_list', $comment_obj->get_comment_rank($location_id, '0,10')));
                       
                       
foreach($ranking_array AS $key=>$val)
{
    //�����һ��
    $issue_id = $cms_obj->add_issue_by_rank_id($key);
    foreach($val[2] AS $k=>$v)
    {
        //��������
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['nickname'], $v['num']);
    }   
}
?>