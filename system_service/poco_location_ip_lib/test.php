<?php


include('/disk/data/htdocs232/poco/poco_location_ip_lib/location_common.inc.php');
		$location_obj = new location_conf_class();

	
		$ret = $location_obj->get_location_ip_2_location($ip);
var_dump($ret);
exit;

echo dirname(__FILE__);
/*$poco_member_obj = new poco_member_class();
$current_locate_info = $poco_member_obj->get_ip_location_info($ibforums->input['IP_ADDRESS']);
var_dump($current_locate_info);exit;*/

$locat = new location_conf_class();
var_dump($locat->get_location_2_location_id('นใถซ',true));
/*$rows = $locat->get_location_all_list();
print_r($rows);
$province_arr = array();
foreach ($rows as $item)
{
	$tmp_arr[$item['lct_id']] = $item;
}
foreach ($tmp_arr as $item) 
{
	if ($item['lct_fid']!=0) 
	{
		$province_arr[$tmp_arr[$item['lct_fid']]['lct_name']][$item['lct_name']] = $item['lct_icon'];
	}
}
print_r($province_arr);*/
$ip = $_GET['ip'];
//var_dump($locat->get_location_ip_2_location($ip,true));

/*include_once('/disk/data/htdocs232/poco/poco_location_ip_lib/location_common.inc.php');

function get_ip_location_info($ip)
{
	//include_once('/disk/data/htdocs232/poco/poco_location_ip_lib/location_common.inc.php');
	$location_obj = new location_conf_class();
	if (preg_match("/^\d+$/",$ip))
	{
		$ip = long2ip($ip);
	}

	$ret = $location_obj->get_location_ip_2_location($ip);

	return $ret;
}

var_dump(get_ip_location_info($ip));*/

?>