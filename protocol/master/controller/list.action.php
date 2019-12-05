<?php
/**
 * Created by PhpStorm.
 * User: willike
 * Date: 2015/9/23
 * Time: 14:25
 */
 defined('PROTOCOL_MASTER_ROOT') or die('ERROR: list error!');
$log_res = yue_protocol_log::get_log_files_list();
$log_list = array();
foreach ($log_res as $key => $logs) {
    foreach ($logs as $val) {
        $name = $val['name'];
        $md5 = md5($name . '@willike');
        $file = $val['file'];
        $fsize = sprintf('%.2f', filesize($file) / 1024);
        $size = ($fsize > 1024 ? sprintf('%.2f', $fsize / 1024) . 'MB' : $fsize . 'KB');
        $del_link = 'op=' . md5($md5 . '&DEL') . '&name=' . $md5;
        $log_list[$key][] = array(
            'name' => $name,
            'size' => $size,
            'link' => $del_link,
            'file' => $file,
        );
    }
}
// 设置列表缓存
$cache_key = yue_log_operate::LIST_CACHE_KEY;
yue_protocol_cache::set_cache($cache_key, $log_list, array('life_time' => 60 * 60));
return array('title' => '日志文件列表', 'list' => $log_list);