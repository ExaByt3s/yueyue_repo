<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/7/23
 * Time: 9:35
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/*$rank_event_v2_obj = POCO::singleton( 'pai_rank_event_v2_class' ) ;
$ret = $rank_event_v2_obj->get_rank_event_by_location_id('index',0,101029001);
echo "<pre>";
print_r($ret);*/

$rank_event_v2_obj = POCO::singleton( 'pai_rank_event_v2_class' ) ;
$ret = $rank_event_v2_obj->get_rank_event_by_location_id('list',5,101029001);
echo "<pre>";
print_r($ret);

$rank_event_v2_obj = POCO::singleton( 'pai_rank_event_v2_class' ) ;
$ret = $rank_event_v2_obj->get_rank_event_by_location_id('list',31,101029001);
echo "<pre>";
print_r($ret);

$rank_event_v2_obj = POCO::singleton( 'pai_rank_event_v2_class' ) ;
$ret = $rank_event_v2_obj->get_rank_event_by_location_id('list',99,101029001);
echo "<pre>";
print_r($ret);
?>