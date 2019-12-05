<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$time_querys = '';
$price_querys = '';
$start_querys = '';
$keywords = '';
$limit = '0,10';
$querys["is_top"] = "0";
$ret = event_fulltext_search($time_querys,$price_querys,$start_querys, false, $limit,$location_id, $keywords,$querys);
print_r($ret);

$querys["is_top"] = '';

$ret = event_fulltext_search($time_querys,$price_querys,$start_querys, false, $limit,$location_id, $keywords,$querys);
print_r($ret);
exit();
if(empty($role))        $role         = 'cameraman';
if(empty($location_id)) $location_id  = '101001001';
$rank_event_obj         = POCO::singleton('pai_rank_event_class');
$ranking_array = $rank_event_obj->get_rank_event_by_location_id($location_id, $role);
print_r($ranking_array);
?>