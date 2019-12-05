<?php
/**
 * 模特卡基础信息
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/config/model_card_config.php');

$level_arr = include('/disk/data/htdocs232/poco/pai/config/level_require_config.php');
$output_arr = array();
$tmp_arr = array();
$model_style_arr = array();

//数据构造
//======================================================
//拍摄类型
foreach ($model_type as $key => $val) {
    $tmp_arr = array(
        'text' => (string)$val
    );

    $model_type[$key]=$tmp_arr;
}
//风格
foreach ($model_style as $key => $val) {
    $tmp_arr = array(
        'text' => (string)$val,
        'id' => rand(0,10000)
    );

    $model_style_arr[]=$tmp_arr;
}
//信用金
//foreach ($balance_require as $key => $val) {
//    $tmp_arr = array(
//        'text' => (string)$val,
//        'params' =>'￥'
//    );
//
//    $balance_require[$key]=$tmp_arr;
//}

foreach ($level_arr as $key => $val) {
    $tmp_arr = array(
        'text' => (string)$val['name'],
        'remark' =>(string)$val['remark'],
        'id' =>(string)$val['level'],
    );

    $level_arr[$key]=$tmp_arr;
}

$output_arr['data']['balance_require'] = $level_arr;
$output_arr['data']['model_style'] = $model_style_arr;
$output_arr['data']['model_type'] = $model_type;
//$output_arr['data']['balance_require'] = $balance_require;

mobile_output($output_arr,false);

?>