<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];
$page        = $client_data['data']['param']['page'];
$search_type = $client_data['data']['param']['search_type'];

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
if($pai_user_obj->check_role($user_id) == 'model')
{
    $role = 1;
}

if($search_type == 'waipai')
{
    //$array_tag = array('三人', '室内', '室外');
    $array_tag = array();
}else{
    //$array_tag = array('真空','内衣/比坚尼','甜美','糖水','情绪','日韩','欧美','古装','文艺复古','走秀','淘宝','礼仪/车展');
    $array_tag = array();
}


if(version_compare($client_data['data']['version'], '2.1.10', '='))   $array_tag = array();

$tag_array = '';
foreach($array_tag AS $key=>$val)
{
    $tag_array['tag'] = $val;
    $data['tag_list'][]    = $tag_array;
    if($key == 11) break;
}

$data['mid']   = '122SE01001';
$data['dmid']  = "s001";

//$data['tag_num']  = ceil(count($array_tag)/12);
$data['tag_num']  = 2;
$options['data']  = $data;



$cp->output($options);
?>