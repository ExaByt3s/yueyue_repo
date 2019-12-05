<?php

/**
 * 获取用户信息
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$idx =0;
$_local_club_array = include(G_YUEYUE_ROOT_PATH . '/config/photo_club_config.php');
foreach ($_local_club_array as $key=>$item)
{  
    $club_list[$idx]['value'] = $key;
    $club_list[$idx]['text'] = $item;
  
  	$idx++;
}

$output_arr['list'] = $club_list;

mobile_output($output_arr,false);

?>