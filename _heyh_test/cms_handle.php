<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/9/21
 * Time: 12:22
 */

set_time_limit(3600);
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');



$cms_obj = new cms_system_class();
/*
$array_type_loaction = array('ģ��Ʒ����ࣨ3.1.0��', 'Լ��ѵ���ࣨ3.1.0��', 'Լ��ױ���ࣨ3.1.0��', '��ҵ���Ʒ��ࣨ3.1.0��', 'Լ�����(3.1.0)', 'Լ��Ӱ����(3.1.0)', 'Լ��ʳ����(3.1.0)',
                                'Լ�������(3.1.0)', 'Լģ�ر�ǩ(3.1.0)', 'Լ��ѵ��ǩ(3.1.0)', 'Լ��ױ��ǩ(3.1.0)', '��ҵ���Ʊ�ǩ(3.1.0)', 'Լ��Ӱ��ǩ(3.1.0)', 'Լ���ǩ(3.1.0)',
                                'Լ��ʳ��ǩ(3.1.0)', 'Լ�����ǩ(3.1.0)');

$array_location = array(
    '����'=>3,
    '�Ϻ�'=>4,
    '����'=>5,
    '�ɶ�'=>6,
    '���'=>10,
    '����'=>11
);

foreach($array_location AS $k=>$v)
{
    $order_num = 4001;
    foreach($array_type_loaction AS $val)
    {
        $cms_obj->add_rank($v, $val, '', '', 0, $order_num++);
    }
}



exit();*/

$result = $cms_obj->get_record_by_rank_id(616);
print_r($result);

$array_location = array(
    '����'=>727,
    '�Ϻ�'=>743,
    '����'=>759,
    '�ɶ�'=>775,
    '����'=>807
);

$city_code = array(
    '����'=>101001001,
    '�Ϻ�'=>101003001,
    '����'=>101004001,
    '�ɶ�'=>101022001,
    '����'=>101015001,
);

$sh_code = array(
    '����'=>101001,
    '�Ϻ�'=>101003,
    '����'=>101004,
    '�ɶ�'=>101022,
    '����'=>101015,
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $link_url = str_replace('101029001', $city_code[$key], $v['link_url']);
        $link_url = str_replace('101029', $sh_code[$key], $link_url);
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $link_url, $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
    //exit();
}



exit();
/*$result = $cms_obj->get_record_by_rank_id(605);
print_r($result);

$array_location = array(
    '����'=>719,
    '�Ϻ�'=>735,
    '����'=>751,
    '�ɶ�'=>767,
    '����'=>799
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $v['link_url'], $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
}

$result = $cms_obj->get_record_by_rank_id(604);
print_r($result);

$array_location = array(
    '����'=>718,
    '�Ϻ�'=>734,
    '����'=>750,
    '�ɶ�'=>766,
    '����'=>798
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $v['link_url'], $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
}

$result = $cms_obj->get_record_by_rank_id(603);
print_r($result);

$array_location = array(
    '����'=>717,
    '�Ϻ�'=>733,
    '����'=>749,
    '�ɶ�'=>765,
    '����'=>797
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $v['link_url'], $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
}

$result = $cms_obj->get_record_by_rank_id(601);
print_r($result);

$array_location = array(
    '����'=>716,
    '�Ϻ�'=>732,
    '����'=>748,
    '�ɶ�'=>764,
    '����'=>796
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $v['link_url'], $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
}

$result = $cms_obj->get_record_by_rank_id(599);
print_r($result);

$array_location = array(
    '����'=>715,
    '�Ϻ�'=>731,
    '����'=>747,
    '�ɶ�'=>763,
    '����'=>795
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $v['link_url'], $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
}

$result = $cms_obj->get_record_by_rank_id(597);
print_r($result);

$array_location = array(
    '����'=>714,
    '�Ϻ�'=>730,
    '����'=>746,
    '�ɶ�'=>762,
    '����'=>794
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $v['link_url'], $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
}

$result = $cms_obj->get_record_by_rank_id(595);
print_r($result);

$array_location = array(
    '����'=>713,
    '�Ϻ�'=>729,
    '����'=>745,
    '�ɶ�'=>761,
    '����'=>793
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $v['link_url'], $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
}

$result = $cms_obj->get_record_by_rank_id(589);
print_r($result);

$array_location = array(
    '����'=>712,
    '�Ϻ�'=>728,
    '����'=>744,
    '�ɶ�'=>760,
    '����'=>792
);

foreach($array_location AS $key=>$val)
{
    $issue_id   = $cms_obj->add_issue_by_rank_id($val);
    foreach($result AS $k=>$v)
    {
        $cms_obj->add_record_by_issue_id($issue_id, $v['user_id'], $v['title'], $v['place_number'], $v['link_url'], $v['img_url'], $v['content'], $v['remark'], $v['link_type']);
    }
    unset($issue_id);
}*/