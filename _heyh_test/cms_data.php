<?php
/**
 * Created by PhpStorm.
 * User: ��ҫ��
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

//�����ݳ�ʼ��
$target_data = array(
    'Լģ�ر�ǩ'=>609,
    'Լ��ѵ��ǩ'=>610,
    'Լ��ױ��ǩ'=>611,
    '��ҵ���Ʊ�ǩ'=>612,
    'Լ��Ӱ��ǩ'=>613,
    'Լ���ǩ'=>614,
    'Լ��ʳ��ǩ'=>615,
    'Լ�����ǩ'=>616,
);

$cp_data = array(
    'Լģ�ر�ǩ'=>988,
    'Լ��ѵ��ǩ'=>989,
    'Լ��ױ��ǩ'=>990,
    '��ҵ���Ʊ�ǩ'=>991,
    'Լ��Ӱ��ǩ'=>992,
    'Լ���ǩ'=>993,
    'Լ��ʳ��ǩ'=>994,
    'Լ�����ǩ'=>995,
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