<?php
/**
 * 分析
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-11-1
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: expo error!');

$date = date('Y-m-d');
//$date = '2015-09-17';
$log_analysis = new yue_log_analysis();
$stat_res = $log_analysis->event_daily_log_analysis($date);
if(empty($stat_res)){
	return array(
	    'date_' => $date,
	    'time_stat' => '[]',
	    'system_stat' => '[]',
	);
}
if (isset($_GET['_debug']) && $_GET['_debug'] == 'willike') {
    var_dump($stat_res);
    exit;
}
$os_stat_res = $stat_res['os_stat'];  // 系统
$version_stat_res = $stat_res['version_stat'];  // 版本
$uri_stat_res = $stat_res['uri_stat'];  // uri
$time_stat_res = $stat_res['time_stat'];  // 时间
$min_stat_res = $stat_res['min_stat'];  // 分钟
$user_stat_res = $stat_res['user_stat'];  // 用户

$time_stat_ = $os_stat_ = $version_stat_ = $min_stat_ = $uri_stat_ = array();
// 时间分布
ksort($time_stat_res);
$time_stat_ = array(
    'xAxis_data' => array_keys($time_stat_res),
    'series_data' => array_values($time_stat_res),
);
// 版本
foreach($os_stat_res as $name => $num){
	$os_stat_[] = array(
		'name' => $name,
		'value' => $num,
	);
}
// 系统
foreach($version_stat_res as $name => $num){
	$version_stat_[] = array(
		'name' => $name,
		'value' => $num,
	);
}
$system_stat = array(
	'os' => $os_stat_,
	'version' => $version_stat_,
);
// 分钟
$hourline = array();
foreach($min_stat_res as $hour => $stat_){
	$hourline[] = strval($hour);
	$minline = $options = array();
	for($i = 0; $i < 60; $i++){
		$i_ = str_pad($i,2,'0',STR_PAD_LEFT);
		if($i % 2 == 0){
			$minline[] = $i_;
		}else{
			$minline[] = "\n".$i_;
		}
		$options[] = array(
			'name' => $i_,
			'value' => isset($stat_[$i_]) ? $stat_[$i_] : 0, 
		); 
	}
	$min_stat_[] = array(
		'title' => array(
			'text' => $hour.'时 访问频率分布', 
		), 
		'series' => array(
			array(
				'data' => $options,
			)
		),
	);
}
$min_stat = array(
	'timeline_data' => $hourline,
	'xAxis_data' => $minline,
	'options' => $min_stat_,
);
// 链接
arsort($uri_stat_res);  // 倒序,键值不变
$uri_i = 1;
foreach($uri_stat_res as $uri => $num){
	$name = 'uri_'.ceil($uri_i / 50);
	$uri_stat_[$name][] = array(
		'name' => $uri,
		'value' => $num,
	);
	$uri_i ++;
}

return array(
    'date_' => $date,
    'time_stat' => json_encode($time_stat_),
    'system_stat' => json_encode($system_stat),
    'min_stat' => json_encode($min_stat),
    'uri_stat' => json_encode($uri_stat_),
);
