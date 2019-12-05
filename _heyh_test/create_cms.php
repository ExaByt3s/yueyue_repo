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
    '31'=>'ģ����Լ',
    '5'=>'��ѵ����',
    '12'=>'Ӱ������',
    '3'=>'��ױ����',
    '40'=>'��Ӱ����',
    '41'=>'��ʳ����',
    '43'=>'��Ȥ����',
    '99'=>'��Ȥ����',
);

/**
 * ����������ҳ��ǩ
 */
$tag_array = array('3' => 611, '5' => 610, '12' => 612, '31' => 884, '40' => 888, '41' => 615, '43' => 616, '99' => 613);

foreach($tag_array AS $key=>$val)
{
    $channel_id = 18;
    $rank_name  = '�����߰� ���� ' . $array_type_name[$key]  . '��ҳ��ǩ';
    $rank_url   = '';
    $remark     = '�����߰� -> ' . $array_type_name[$key] . '��ҳ��ǩ����';
    $img_size   = '640';
    $sort_order = 1000;
    $sort_order++;

    $rank_id    = $cms_obj->add_rank($channel_id, $rank_name, $rank_url, $remark, $img_size, $sort_order);     //������

    if($rank_id) {
        $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);     //��������
    }else{
        die('��IDΪ��');
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
        die('����IDΪ��');
    }
}
exit();
/**
 * �������Է�����ҳ��������
 *  ����ʼ
 */
$classify_array = array('3'=>597, '5'=> 595, '12' => 599, '31' => 589, '40' => 603, '41' => 604, '43' => 605, '99' => 601);
foreach($classify_array AS $key=>$val)
{
    $channel_id = 18;
    $rank_name  = '�����߰� ���� ' . $array_type_name[$key]  . '��ҳ����';
    $rank_url   = '';
    $remark     = '�����߰� -> ' . $array_type_name[$key] . '��ҳ��������';
    $img_size   = '640';
    $sort_order = 1000;
    $sort_order++;

    $rank_id    = $cms_obj->add_rank($channel_id, $rank_name, $rank_url, $remark, $img_size, $sort_order);     //������

    if($rank_id) {
        $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);     //��������
    }else{
        die('��IDΪ��');
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
        die('����IDΪ��');
    }
}
/**
 * �������
 */

exit();

/**
 * ����������ҳ��������
 * ����ʼ
 */
$origin_cms_id = 312;       //��ҳ��������

$channel_id = 18;
$rank_name  = '�����߰� ���� ��ҳ����';
$rank_url   = '';
$remark     = '�����߰� -> ��ҳ��������';
$img_size   = '640';
$sort_order = '1000';

$rank_id    = $cms_obj->add_rank($channel_id, $rank_name, $rank_url, $remark, $img_size, $sort_order);     //������

if($rank_id) {
    $issue_id   = $cms_obj->add_issue_by_rank_id($rank_id);     //��������
}else{
    die('��IDΪ��');
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
    die('����IDΪ��');
}
/**
 * �������
 */







