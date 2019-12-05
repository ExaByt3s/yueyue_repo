<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
if($pai_user_obj->check_role($user_id) == 'model')
{
    $role = 1;
}

$cms_obj        = new cms_system_class();
$pic_obj        = POCO::singleton ('pai_pic_class');

$key = 23;
$info = $cms_obj->get_last_issue_record_list(false, '0,4', 'place_number DESC', $key); 
$data['name']  = '约约热模';
$data['query'] = '';


$data['mid']  = '122SE01002';
$data['dmid']  = '23';

foreach($info AS $k=>$v)
{
    $record['user_id']      = $v['user_id'];  
    $record['user_icon']    = $v['img_url'];
    
//头像修改
$pic_array = $pic_obj->get_user_pic($v['user_id'], $limit = '0,5');
foreach($pic_array AS $a=>$b)
{
    
    $num = explode('?', $b['img']);
    $num = explode('x', $num[1]);
    $num_v2 = explode('_', $num[1]);
    
    $width = $num[0];
    $height = $num_v2[0];
    
    if($width<$height)
    {
        $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
        break;
    }
    $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
}  
    
    $record['nickname']     = $v['title'];
    $record['num']          = $v['remark'];
    $record['style']        = $v['remark'];
    $record['vid']          = $record['user_id'];
    $record['jid']          = "001";
    
    $data['user_list'][]    = $record;
}

$options['data'][] = $data;

$cp->output($options);
?>