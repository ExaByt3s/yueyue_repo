<?php 
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql = "select * from test.task_seller_tbl";
$seller_arr = db_simple_getdata($sql,false,101);

foreach($seller_arr as $val)
{
	$location_info = POCO::execute('common.get_location_2_location_id', $val['address']);
	$location_id = $location_info['location_id'];
	
	$sql = "update test.task_seller_tbl set location_id={$location_id} where id=".$val['id'];
	db_simple_getdata($sql,false,101);
}
?>