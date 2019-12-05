<?php
/**
 * 内容举报
 * 
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/include/pai_examine_report_class.inc.php');

$examine_report_obj = POCO::singleton('pai_examine_report_class');

$to_informer = $yue_login_id;
$by_informer = $_INPUT['by_informer'];
$data        = $_INPUT['data'];

if(!empty($to_informer))
{
    $status = $examine_report_obj->add_report_content($to_informer, $by_informer, $data);   
    if($status)
    {
        $output_arr['code'] = 1;
        $msg = '举报成功!';
        $output_arr['msg'] = $msg;
        //$output_arr['msg'] = mb_convert_encoding($msg,'utf-8','gbk');
    }else{
        $output_arr['code'] = 1;
        $msg = '举报失败!';
        $output_arr['msg'] = $msg;
        //$output_arr['msg'] = mb_convert_encoding($msg,'utf-8','gbk');        
    }
}else{
    $output_arr['code'] = 0;
	$msg = '缺少数据';
	$output_arr['msg'] = $msg;
    //$output_arr['msg'] = mb_convert_encoding($msg,'utf-8','gbk');
}
mobile_output($output_arr,false); 
?>