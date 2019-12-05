<?php

/**
 * 日志查询
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-22
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: search error!');
$keyword = filter_input(INPUT_POST, 'keyword');
if (empty($keyword)) {
    return pm_json_return('', '查询标识为空!', 0);
}
if (strlen($keyword) < 3 || strlen($keyword) > 50 || !preg_match('/^(\w|\.)+$/', $keyword)) {
    return pm_json_return('', '查询标识不合法', 0);
}
$serach_date = filter_input(INPUT_GET, '_date');
if (empty($serach_date)) {
    $today = date('Y-m-d');
} else {
    $today = $serach_date;
}
//$today = '2015-09-17';
$log_res = yue_protocol_log::get_daily_logs($today);
if (is_numeric($keyword)) {
    $finite = 10; // 限制
} else {
    $finite = 5;
}
$search_list = array();
$log_operate_obj = new yue_log_analysis();  // 载入分析类
foreach ($log_res as $log_file) {
    if (preg_match('/(_event\.log|_slowquery\.log|_runtime\.log|_api\.log)$/', $log_file)) {
        // 活动日志,慢查询日志,耗时日志,API调试 不分析
        continue;
    }
    $file_name = basename($log_file);  // 文件名
    $format_res = $log_operate_obj->detect_log_format($file_name); // 检测日志格式
    if (empty($format_res)) {
        $search_list[] = array(
            'file_name' => $file_name,
            'search_count' => 0,
            'search_result' => 'unknown format!',
        );
        continue;
    }
    $search_res = $log_operate_obj->search_log_contents($log_file, $keyword, $format_res['cols_delimiter'], $finite);
    $matchs = $search_res['matchs']; // 匹配项目
    if (empty($matchs)) {
        $search_list[] = array(
            'file_name' => $file_name,
            'search_count' => 0,
            'search_result' => 'line: ' . $search_res['line'] . '. times: ' . $search_res['times'] . ' data unmatched!',
        );
        continue;
    }
    $count = count($matchs); // 总匹配数
    $rows_delimiter = $format_res['rows_delimiter'];
    $search_result = array();
    for ($i = $count; $i > 0; $i--) {
        if ($count - $i >= $finite) {
            break;
        }
        $rows = array();
        $line = explode($rows_delimiter, $matchs[$i - 1]);
        foreach ($line as $v) {
            if (preg_match('/[a-z]:\d+:/', $v)) {
                // 序列化数据
                $rows['code'] = json_encode(unserialize($v));
                continue;
            }
            $rows[] = $v;
        }
        $search_result[] = $rows;
    }
    $search_list[] = array(
        'file_name' => $file_name,
        'search_count' => count($search_result),
        'search_result' => $search_result,
    );
}
return pm_json_return($search_list, '查询完成!', 1);