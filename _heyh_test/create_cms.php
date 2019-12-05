<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/11/17
 * Time: 14:18
 */
include_once "../poco_app_common.inc.php";
include_once "/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php";

$cms_obj = new cms_system_class();
$array_type_name = array(
    '31'=>'模特邀约',
    '5'=>'培训服务',
    '12'=>'影棚租赁',
    '3'=>'化妆服务',
    '40'=>'摄影服务',
    '41'=>'美食服务',
    '43'=>'有趣服务',
    '99'=>'有趣服务',
);

/**
 * 创建分类首页标签
 */
$tag_array = array('3' => 611, '5' => 610, '12' => 612, '31' => 884, '40' => 888, '41' => 615, '43' => 616, '99' => 613);

foreach($tag_array AS $key=>$val)
{
    $channel_id = 18;
    $rank_name  = '消费者版 —— ' . $array_type_name[$key]  . '首页标签';
    $rank_url   = '';
    $remark     = '消费者版 -> ' . $array_type_name[$key] . '首页标签内容';
    $img_size   = '640';
    $sort_order = 1000;
    $sort_order++;

    $rank_id    = $cms_obj->add_rank($channel_id, $rank_name, $rank_url, $remark, $img_size, $sort_order);     //建立榜单

    if($rank_id) {
        $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);     //创建期数
    }else{
        die('榜单ID为空');
    }
    echo $issue_id . "<BR>";
    if($issue_id)
    {
        $record_list = $cms_obj->get_record_by_rank_id($val, '0,100');
        foreach($record_list AS $key=>$val)
        {
            var_dump($val);
            $user_id        = $val['user_id'];
            $title          = $val['title'];
            $place_number   = $val['place_number'];
            $link_url       = $val['link_url'];
            $img_url        = $val['img_url'];
            $content        = $val['content'];
            $remark         = $val['remark'];

            $cms_obj->add_record_by_issue_id($issue_id, $user_id, $title, $place_number, $link_url, $img_url, $content, $remark);
        }
    }else{
        die('期数ID为空');
    }
}
exit();
/**
 * 创建测试分类首页分类内容
 *  处理开始
 */
$classify_array = array('3'=>597, '5'=> 595, '12' => 599, '31' => 589, '40' => 603, '41' => 604, '43' => 605, '99' => 601);
foreach($classify_array AS $key=>$val)
{
    $channel_id = 18;
    $rank_name  = '消费者版 —— ' . $array_type_name[$key]  . '首页分类';
    $rank_url   = '';
    $remark     = '消费者版 -> ' . $array_type_name[$key] . '首页分类内容';
    $img_size   = '640';
    $sort_order = 1000;
    $sort_order++;

    $rank_id    = $cms_obj->add_rank($channel_id, $rank_name, $rank_url, $remark, $img_size, $sort_order);     //建立榜单

    if($rank_id) {
        $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);     //创建期数
    }else{
        die('榜单ID为空');
    }
echo $issue_id . "<BR>";
    if($issue_id)
    {
        $record_list = $cms_obj->get_record_by_rank_id($val, '0,100');
        foreach($record_list AS $key=>$val)
        {
            var_dump($val);
            $user_id        = $val['user_id'];
            $title          = $val['title'];
            $place_number   = $val['place_number'];
            $link_url       = $val['link_url'];
            $img_url        = $val['img_url'];
            $content        = $val['content'];
            $remark         = $val['remark'];

            $cms_obj->add_record_by_issue_id($issue_id, $user_id, $title, $place_number, $link_url, $img_url, $content, $remark);
        }
    }else{
        die('期数ID为空');
    }
}
/**
 * 处理结束
 */

exit();

/**
 * 创建测试首页分类内容
 * 处理开始
 */
$origin_cms_id = 312;       //首页分类内容

$channel_id = 18;
$rank_name  = '消费者版 —— 首页分类';
$rank_url   = '';
$remark     = '消费者版 -> 首页分类内容';
$img_size   = '640';
$sort_order = '1000';

$rank_id    = $cms_obj->add_rank($channel_id, $rank_name, $rank_url, $remark, $img_size, $sort_order);     //建立榜单

if($rank_id) {
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);     //创建期数
}else{
    die('榜单ID为空');
}
sleep(2);
if($issue_id)
{
    $record_list = $cms_obj->get_record_by_rank_id($origin_cms_id, '0,100');
    foreach($record_list AS $key=>$val)
    {
        var_dump($val);
        $user_id        = $val['user_id'];
        $title          = $val['title'];
        $place_number   = $val['place_number'];
        $link_url       = $val['link_url'];
        $img_url        = $val['img_url'];
        $content        = $val['content'];
        $remark         = $val['remark'];

        $cms_obj->add_record_by_issue_id($issue_id, $user_id, $title, $place_number, $link_url, $img_url, $content, $remark);
    }
}else{
    die('期数ID为空');
}
/**
 * 处理结束
 */







