<?php

include_once('/disk/data/htdocs232/poco/poco_location_ip_lib/location_common.inc.php');

//$poco_member_obj = new poco_member_class();
//$current_locate_info = $poco_member_obj->get_ip_location_info($ibforums->input['IP_ADDRESS']);

echo $ibforums->input['IP_ADDRESS'];
echo "<br>";
echo ($current_locate_info['address']);
?>