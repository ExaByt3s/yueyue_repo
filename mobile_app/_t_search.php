<?php
/**
 * Created by PhpStorm.
 * User: heyh
 * Date: 2015/4/30
 * Time: 9:54
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$time_querys    = '';
$price_querys   = '';
$start_querys   = '';
$limit          = '0,10';
$location_id    = '';
$keywords       = 'poco';
$querys         = '';

$result = event_fulltext_search($time_querys,$price_querys,$start_querys, $b_select_count = false, $limit,$location_id, $keywords, $querys);
var_dump($result);
?>