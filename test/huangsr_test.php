<?php
/*
 * 自动处理过期的活动，有签到的自动完成，没签到的自动取消
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$ret=event_fulltext_search($time_querys = '', $price_querys = '', $start_querys = '', $b_select_count = false, $limit = '0,50',$location_id=101029001, $keyword='',$querys=array());

print_r($ret);

?>