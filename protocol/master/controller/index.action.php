<?php
/**
 * 主页
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-10-31
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: index error!');

$date = date('Y-m-d');
//$date = '2015-09-17';
if (!pm_is_admin()) {  // 非 管理员
	$user_id = pm_get_user_id();
	$log_file = PROTOCOL_MASTER_ROOT . 'logs/' . $user_id . '.log';  // 日志配置
	$log_operate = new yue_log_operate();
	$log_result = $log_operate->get_log_section($log_file, 5, -1, PHP_EOL);
	$log_list = $log_result['list'];
	if(empty($log_list)){
		return array(
	        'date' => $date,
	        'user_log' => array(),
	    );
	}
	rsort($log_list);
	foreach($log_list as $log){
		if(empty($log)){
			continue;
		}
		list($log_time,$log_params) = explode('|', $log);
		$log_params = unserialize($log_params); // 日志内容
		$time_dif = pm_get_time_diff(strtotime($log_time));
		$agent = pm_client_agent($log_params['user_agent']);
		$log_data[] = array(
			'time' => $time_dif,
			'method' => $log_params['method'],
			'url' => $log_params['request_uri'],
			'ip' => $log_params['ip'],
			'agent' => $agent,
		);
	}
//	unset($log_data[0]);  // 第一个不要
    return array(
        'date' => $date,
        'user_log' => $log_data,
    );
}
$log_analysis = new yue_log_analysis();
$stat_res = $log_analysis->event_daily_log_analysis($date);
if (empty($stat_res)) {
    return array(
        'date' => $date,
        'max' => 0,
        'stat' => '[]',
    );
}
$fr_cache = $stat_res['fr_cache'];
$location_stat = $stat_res['location_stat'];  // 地区
if (isset($_GET['_debug']) && $_GET['_debug'] == 'willike') {
    var_dump($location_stat);
    exit;
}
$stat_list_[] = array('id' => $date, 'name' => '全部', 'num' => array_sum($location_stat));
arsort($location_stat);  // 排序
foreach ($location_stat as $location => $num) {
    $name = pm_get_location_name($location);
    $stat_list_[] = array(
        'id' => $location,
        'name' => empty($name) ? '<u>未知</u>' : $name,
        'num' => $num,
    );
    $location = substr($location, 0, 6);  // 省份ID
    $name = pm_get_location_name($location);  // 省份
    if (!empty($name)) {
        $name = $name;
    } else {
        $name = '南海诸岛';
        $location = 0;
    }
    if (isset($stat_[$location])) {
        $stat_[$location]['value'] += $num;
        continue;
    }
    $stat_[$location] = array(
        'name' => $name,
        'value' => $num,
    );
}
return array(
    'date' => $date,
    'fr_cache' => $fr_cache,
    'max' => max($location_stat),
    'stat' => json_encode(array_values($stat_)),
    'stat_list' => $stat_list_,
);