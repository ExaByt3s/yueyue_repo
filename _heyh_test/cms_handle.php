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
$array_type_loaction = array('模特品类分类（3.1.0）', '约培训分类（3.1.0）', '约化妆分类（3.1.0）', '商业定制分类（3.1.0）', '约活动分类(3.1.0)', '约摄影分类(3.1.0)', '约美食分类(3.1.0)',
                                '约更多分类(3.1.0)', '约模特标签(3.1.0)', '约培训标签(3.1.0)', '约化妆标签(3.1.0)', '商业定制标签(3.1.0)', '约摄影标签(3.1.0)', '约活动标签(3.1.0)',
                                '约美食标签(3.1.0)', '约更多标签(3.1.0)');

$array_location = array(
    '北京'=>3,
    '上海'=>4,
    '重庆'=>5,
    '成都'=>6,
    '天津'=>10,
    '西安'=>11
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
    '北京'=>727,
    '上海'=>743,
    '重庆'=>759,
    '成都'=>775,
    '西安'=>807
);

$city_code = array(
    '北京'=>101001001,
    '上海'=>101003001,
    '重庆'=>101004001,
    '成都'=>101022001,
    '西安'=>101015001,
);

$sh_code = array(
    '北京'=>101001,
    '上海'=>101003,
    '重庆'=>101004,
    '成都'=>101022,
    '西安'=>101015,
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
    '北京'=>719,
    '上海'=>735,
    '重庆'=>751,
    '成都'=>767,
    '西安'=>799
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
    '北京'=>718,
    '上海'=>734,
    '重庆'=>750,
    '成都'=>766,
    '西安'=>798
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
    '北京'=>717,
    '上海'=>733,
    '重庆'=>749,
    '成都'=>765,
    '西安'=>797
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
    '北京'=>716,
    '上海'=>732,
    '重庆'=>748,
    '成都'=>764,
    '西安'=>796
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
    '北京'=>715,
    '上海'=>731,
    '重庆'=>747,
    '成都'=>763,
    '西安'=>795
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
    '北京'=>714,
    '上海'=>730,
    '重庆'=>746,
    '成都'=>762,
    '西安'=>794
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
    '北京'=>713,
    '上海'=>729,
    '重庆'=>745,
    '成都'=>761,
    '西安'=>793
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
    '北京'=>712,
    '上海'=>728,
    '重庆'=>744,
    '成都'=>760,
    '西安'=>792
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