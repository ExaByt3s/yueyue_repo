<?php
/**
 * Created by PhpStorm.
 * User: 何耀华
 * Date: 2015/10/21
 * Time: 11:38
 */

set_time_limit(3600);
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$cms_obj = new cms_system_class();
/*
$record_list = $cms_obj->get_record_list_by_rank_id(FALSE,587);
if(count($record_list) > 0)
{
    $rank_id = 996;
    $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);
    if($issue_id)
    {
        foreach($record_list AS $k=>$v)
        {
            //$link_url = str_replace('pid=1220128', 'pid=1220125',$v['link_url']);
            $cms_obj->add_record_by_issue_id($issue_id,$v['user_id'],$v['title'],$v['place_number'],$v['link_url'],$v['img_url'],$v['content'],$v['remark'],$v['link_type']);
        }
        unset($issue_id);
        unset($rank_id);
    }
}



exit();*/

//榜单数据初始化
$target_data = array(
    '约模特标签'=>609,
    '约培训标签'=>610,
    '约化妆标签'=>611,
    '商业定制标签'=>612,
    '约摄影标签'=>613,
    '约活动标签'=>614,
    '约美食标签'=>615,
    '约更多标签'=>616,
);

$cp_data = array(
    '约模特标签'=>988,
    '约培训标签'=>989,
    '约化妆标签'=>990,
    '商业定制标签'=>991,
    '约摄影标签'=>992,
    '约活动标签'=>993,
    '约美食标签'=>994,
    '约更多标签'=>995,
);

foreach($target_data AS $key=>$val)
{
    //echo $val;
    $record_list = $cms_obj->get_record_list_by_rank_id(FALSE,$val);
    if(count($record_list) > 0)
    {
        $rank_id = $cp_data[$key];
        $issue_id   = (int)$cms_obj->add_issue_by_rank_id($rank_id);
        if($issue_id)
        {
            foreach($record_list AS $k=>$v)
            {
                $link_url = str_replace('pid=1220128', 'pid=1220126',$v['link_url']);
                $cms_obj->add_record_by_issue_id($issue_id,$v['user_id'],$v['title'],$v['place_number'],$link_url,$v['img_url'],$v['content'],$v['remark'],$v['link_type']);
            }
            unset($issue_id);
            unset($rank_id);
        }
    }
}