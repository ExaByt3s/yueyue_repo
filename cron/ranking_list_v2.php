<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_model_array['����']['location_id']  = 101029001;
$location_model_array['����']['all']          = 118;
$location_model_array['����']['week_cash']    = 119;
$location_model_array['����']['week_order']   = 120;
$location_model_array['����']['three_visit']  = 121;

$location_model_array['����']['location_id']  = 101001001;
$location_model_array['����']['all']          = 122;
$location_model_array['����']['week_cash']    = 123;
$location_model_array['����']['week_order']   = 124;
$location_model_array['����']['three_visit']  = 125;

$location_model_array['�Ϻ�']['location_id']  = 101003001;
$location_model_array['�Ϻ�']['all']          = 126;
$location_model_array['�Ϻ�']['week_cash']    = 127;
$location_model_array['�Ϻ�']['week_order']   = 128;
$location_model_array['�Ϻ�']['three_visit']  = 129;

$location_model_array['�ɶ�']['location_id']  = 101022001;
$location_model_array['�ɶ�']['all']          = 130;
$location_model_array['�ɶ�']['week_cash']    = 131;
$location_model_array['�ɶ�']['week_order']   = 132;
$location_model_array['�ɶ�']['three_visit']  = 133;

$location_model_array['����']['location_id']  = 101004001;
$location_model_array['����']['all']          = 134;
$location_model_array['����']['week_cash']    = 135;
$location_model_array['����']['week_order']   = 136;
$location_model_array['����']['three_visit']  = 137;

$location_model_array['����']['location_id']  = 101015001;
$location_model_array['����']['all']          = 138;
$location_model_array['����']['week_cash']    = 139;
$location_model_array['����']['week_order']   = 140;
$location_model_array['����']['three_visit']  = 141;


$rank_obj   = POCO::singleton('pai_user_hot_report_class');
$cms_obj    = new cms_system_class();

$role       = 'model';

foreach($location_model_array AS $key=>$val)
{
    $result = $rank_obj->get_hot_list($val['location_id'],$role,'all');
    if($result['data'])
    {
        print_r($result);
        $rank_id       = (int)$val['all'];
        echo $rank;
        if($rank_id)
        {
            $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);

            if($issue_id)
            {
                foreach($result['data'] AS $k=>$v)
                {
                    $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], '', $v['details_price']);
                }
                unset($issue_id);
            }
            unset($rank_id);
        }
    }

    $result = $rank_obj->get_hot_list($val['location_id'],$role,'week_cash');
    if($result['data'])
    {
        print_r($result);
        $rank_id       = (int)$val['week_cash'];
        echo $rank;
        if($rank_id)
        {
            $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);

            if($issue_id)
            {
                foreach($result['data'] AS $k=>$v)
                {
                    $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], '', $v['details_price']);
                }
                unset($issue_id);
            }
            unset($rank_id);
        }
    }

    $result = $rank_obj->get_hot_list($val['location_id'],$role,'week_order');
    if($result['data'])
    {
        print_r($result);
        $rank_id       = (int)$val['week_order'];
        echo $rank;
        if($rank_id)
        {
            $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);

            if($issue_id)
            {
                foreach($result['data'] AS $k=>$v)
                {
                    $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], '', $v['details_price']);
                }
                unset($issue_id);
            }
            unset($rank_id);
        }
    }

    $result = $rank_obj->get_hot_list($val['location_id'],$role,'three_visit');
    if($result['data'])
    {
        print_r($result);
        $rank_id       = (int)$val['three_visit'];
        echo $rank;
        if($rank_id)
        {
            $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);

            if($issue_id)
            {
                foreach($result['data'] AS $k=>$v)
                {
                    $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], '', $v['details_price']);
                }
                unset($issue_id);
            }
            unset($rank_id);
        }
    }
}

?>