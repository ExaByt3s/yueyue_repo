<?php

//defined('PROTOCOL_MASTER_ROOT') || die('ERROR: yueus INC file not loaded!');

/**
 * 日志操作 类
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-9-23
 * @version 1.0 Beta
 */
class yue_log_operate {

    /**
     * @var string 缓存 KEY
     */
    const LIST_CACHE_KEY = 'YUE_LOGS_FILE_LIST_CACHE';

    /**
     * 文件 行数计算
     *
     * @param string $file 文件路径
     * @param string $delimiter 分隔符
     * @return int
     */
    public function file_rows_count($file, $delimiter = PHP_EOL) {
        if (!file_exists($file)) {
            return FALSE;
        }
        $line = 0;
        $handle = fopen($file, 'r');
        if (stripos(PHP_OS, 'win') !== false) {  // win 系统
            $delimiter = ($delimiter === PHP_EOL) ? "\n" : $delimiter; // 处理win与linux换行符不一致
        }
        // 获取文件的一行内容，注意：需要php5才支持该函数
        while (stream_get_line($handle, 100000, $delimiter)) {
            $line++;  // 行数叠加
        }
//        if ($line <= 1) {  // 当查询的结果不是所期望的
//            $line = 0;
//            rewind($handle); // 重置指针
//            while (stream_get_line($handle, 100000, ' ')) {
//                $line++;
//            }
//        }
        fclose($handle);
        return $line;
    }

    /**
     * 获取 部分日志
     *
     * @param string $file 日志文件
     * @param int $count 返回的行数
     * @param int $start 开始行数 ( 小于0,则 从结尾前某行往后取 )
     * @param string $delimiter 分隔符
     * @return array
     */
    public function get_log_section($file, $count = 50, $start = -1, $delimiter = PHP_EOL) {
        if (empty($file) || !file_exists($file) || $count < 1) {
            return array();
        }
        $start = intval($start);
        $line = 0;
        if ($start < 0) {
            $line = $this->file_rows_count($file, $delimiter);  // 总行数
            if ($line < 1) {
                return array();
            }
            $start = $line - $count;  // 起始行数
            $start = $start < 0 ? 0 : $start;
        }
        $count = $count * 1 < 1 ? 10 : intval($count);
        $fp = new SplFileObject($file, 'r');
        if ($start > 0) {
            $fp->seek($start); // 转到第N行, seek方法参数从0开始计数
        }
        $log = array();
        for ($i = 0; $i < $count; ++$i) {
            $content = $fp->current(); // current()获取当前行内容
            if (empty($content)) {
                continue;
            }
            $log[] = trim($content);
            $fp->next(); // 下一行
            unset($content);
        }
        return array('total' => $line, 'list' => $log);
    }

    /**
     * 获取 全部日志
     *
     * @param string $file 日志文件
     * @return array
     */
    public function get_log_contents($file) {
        if (!file_exists($file)) {
            return array();
        }
        if (!$fp = fopen($file, 'r')) {
            return array();
        }
        $log = array();
        while (false !== ($line = fgets($fp))) {
            if (empty($line)) {
                continue;
            }
            $log[] = trim($line);
        }
        fclose($fp);
        return $log;
    }

    /**
     * 删除日志文件
     *
     * @param string $name 文件名称 ( 加密 )
     * @param string $op 操作类型
     * @return boolean
     */
    public function del_logs_file($name, $op) {
        if (empty($name) || empty($op)) {
            return FALSE;
        }
        if ($op != md5($name . '&DEL')) {
            // op验证不通过
            return FALSE;
        }
        $files_list = yue_protocol_cache::get_cache(self::LIST_CACHE_KEY);
        if (empty($files_list) || !isset($files_list[$name])) {
            // 缓存失效
            return FALSE;
        }
        $file = $files_list[$name];
        if (strpos($file, 'protocol/log/') === FALSE) {
            // 非协议日志 则不能删除
            return FALSE;
        }
        if (!file_exists($file)) {
            // 文件不存在
            return FALSE;
        }
        if (strpos($file, date('Y-m-d')) !== FALSE) {
            // 当天则不能删除
            return FALSE;
        }
        if (strpos($file, 'yue_event.log') !== FALSE) {
            // 活动日志 不能删除
            return FALSE;
        }
        unlink($file);  // 删除日志文件
        return TRUE;
    }
}