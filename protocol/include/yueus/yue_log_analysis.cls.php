<?php

//defined('PROTOCOL_MASTER_ROOT') || die('ERROR: yueus INC file not loaded!');

/**
 * 日志分析 类
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-14
 * @version 1.0 Beta
 */
class yue_log_analysis {

    /**
     * @var string 日志标准分隔符
     */
    private $_delimiter = '^$^';

    /**
     * 检测 日志格式
     *
     * @param string $file_name
     * @return array
     */
    public function detect_log_format($file_name) {
        if (strpos($file_name, '_yuepai') !== false) {
            // 访问日志
            return array(
                'rows_delimiter' => $this->_delimiter,
                'rows_format' => 'serialize',
                'cols_delimiter' => PHP_EOL,
            );
        } elseif (strpos($file_name, '_event') !== false) {
            // 事件日志
            return array(
                'rows_delimiter' => '|',
                'rows_format' => '',
                'cols_delimiter' => $this->_delimiter,
            );
        } elseif (strpos($file_name, '_runtime') !== false) {
            // 耗时日志
            return array(
                'rows_delimiter' => '|',
                'rows_format' => '',
                'cols_delimiter' => $this->_delimiter,
            );
        } elseif (strpos($file_name, '_slowquery') !== false) {
            // 慢查询日志
            return array(
                'rows_delimiter' => $this->_delimiter,
                'rows_format' => 'serialize',
                'cols_delimiter' => PHP_EOL,
            );
        } elseif (strpos($file_name, '_token') !== false) {
            // TOKEN日志
            return array(
                'rows_delimiter' => '|',
                'rows_format' => '',
                'cols_delimiter' => $this->_delimiter,
            );
        }
    }

    /**
     * 查询 日志内容
     *
     * @param string $file 文件
     * @param string $keyword 查询关键词
     * @param string $delimiter 分隔符
     * @param int $finite 限定匹配数 (非返回条数)
     * @return array
     */
    public function search_log_contents($file, $keyword, $delimiter = PHP_EOL, $finite = 20) {
        $finite = intval($finite);
        if (!file_exists($file) || empty($keyword) || $finite < 0) {
            return array();
        }
        $size = filesize($file);  // 文件大小 bite
        $split_num = intval(($size / 1024) / 100);  // 分割块数
        $split_size = ceil($size / ($split_num + 1));  // 分割块大小
        if (stripos(PHP_OS, 'win') !== false) {  // win 系统
            $delimiter = ($delimiter === PHP_EOL) ? "\n" : $delimiter; // 处理win与linux换行符不一致
        }
        $handle = $tmp_handle = fopen($file, 'r');
        $times = 0;  // 检索次数
        do {
            $invert_size = $split_num * $split_size;  // 指针位置
            rewind($handle);  // 重置指针
            if ($invert_size > 0 && $invert_size < $size) {
                fseek($handle, -$invert_size, SEEK_END);  // 指针从尾往前定位
                // fix stream_get_line指针无效
                stream_copy_to_stream($handle, $tmp_handle, $size, $invert_size); // 按需截取流
            } else {
                // 从头开始
                $tmp_handle = $handle;
            }
            $line = 0;  // 有效检索行数
            $matchs = array(); // 匹配内容
            //获取文件的一行内容 (php > 5.0)
            while ($rows = stream_get_line($tmp_handle, 100000, $delimiter)) {
                $line++;
                $times++;
                if (strpos($rows, $keyword) === false) {
                    continue;
                }
                $matchs[] = trim($rows);
            }
            if (count($matchs) >= $finite) {
                // 结果集 超过限制时,退出查询
                break;
            }
            $split_num--;
        } while ($split_num > -1);
        fclose($handle);  // 关闭文件
        unset($tmp_handle);
        return array('line' => $line, 'times' => $times, 'matchs' => $matchs, 'size' => $size);
    }

    /**
     * 获取 某天日志
     *
     * @param string $date 默认今天
     * @return array
     */
    public function event_daily_log_analysis($date = null) {
        $date = date('Y-m-d', strtotime($date));
        if ($date == '1970-01-01') {
            $date = date('Y-m-d');  // 今天
        }
        $cache_key = 'MASTER_EVENT_LOG_' . $date;
        $event = yue_protocol_cache::get_cache($cache_key);
        if (!empty($event)) {
            // 有缓存,读取缓存
            $event['fr_cache'] = 1;
            return $event; // json_decode($event,true);
        }
        $file = yue_protocol_log::get_log_file(yue_protocol_log::EVENT_LOG, $date, 'poco_yuepai_api');
        if (empty($file) || !file_exists($file)) {
            return FALSE;
        }
        $file_size = (filesize($file) / 1024); // 文件大小
        $handle = fopen($file, 'r');
        $delimiter = $this->_delimiter;  // 分割号
        $is_continue = false;
        $user_stat = $os_stat = $version_stat = $location_stat = $uri_stat = $time_stat = $min_stat = array();
        while (!feof($handle)) {
            $line = stream_get_line($handle, 1000, $delimiter); // 读取一行
            if (strpos($line, $date) === FALSE) {
                $line_date = substr($line, 0, 10);  // 当前行记录日期
                if (strpos($line_date, '-') === false) { // 非日期
                    continue;
                }
                if (strcmp($line_date, $date) > 0) {
                    // 日期大于当前日志,则停止
                    break;
                }
                if ($is_continue === true) { // 不重复计算(防死循环)
                    continue;
                }
                if ($file_size < 10 * 1024) { // 小于10M 不处理
                    continue;
                }
//                $line_time = strtotime(fgets($handle, 11));  // 行记录日时间
                $line_time = strtotime($line_date);  // 行记录时间
                $date_step = ceil((time() - $line_time) / (24 * 60 * 60));
                if ($date_step < 5) { // 允许某个范围内不处理
                    $is_continue = true;
                    continue;
                }
                if ($date_step > 31) { // 超过一个月,计算当月天数
                    $date_step = intval(date('t', $line_time)) - intval(date('j', $line_time)) + 1;
                }
                $step = floor((strtotime($date) - $line_time) / (34 * 60 * 60));  // 偏移天数
                $invert_size = ($file_size / $date_step) * ($step - 1); // // 计算偏移量
                rewind($handle);  // 重置指针
                fseek($handle, $invert_size, SEEK_SET);  // 指针从新定位
                $is_continue = true;
                continue;
            }
            list($line_date, $user_id, $location_id, $os_type, $version, $uri, $goods_id) = explode('|', $line);
            if (strpos($line_date, '-') === false) {
                continue;
            }
            $h = substr($line_date, 11, 2);  // 小时
            $m = substr($line_date, 14, 2);  // 分钟
            if (isset($time_stat[$h])) {
                $time_stat[$h] += 1;
            } else {
                $time_stat[$h] = 1;
            }
            if (isset($min_stat[$h][$m])) {
                $min_stat[$h][$m] += 1;
            } else {
                $min_stat[$h][$m] = 1;
            }
            if (!empty($user_id) && is_numeric($user_id)) {
                $uk = substr($user_id, 0, 2);
                if (isset($user_stat[$uk][$user_id])) {
                    $user_stat[$uk][$user_id] += 1;
                } else {
                    $user_stat[$uk][$user_id] = 1;
                }
            }
            if (!empty($location_id) && is_numeric($location_id)) {
                if (isset($location_stat[$location_id])) {
                    $location_stat[$location_id] += 1;
                } else {
                    $location_stat[$location_id] = 1;
                }
            }
            if (!empty($os_type)) {
                if (isset($os_stat[$os_type])) {
                    $os_stat[$os_type] += 1;
                } else {
                    $os_stat[$os_type] = 1;
                }
            }
            if (!empty($version)) {
                if (isset($version_stat[$version])) {
                    $version_stat[$version] += 1;
                } else {
                    $version_stat[$version] = 1;
                }
            }
            if (!empty($uri) && $uri != '/') {
                if (($pos = strpos($uri, '?')) > 0) {
                    $uri = substr($uri, 0, ($pos - 1));
                }
                if (isset($uri_stat[$uri])) {
                    $uri_stat[$uri] += 1;
                } else {
                    $uri_stat[$uri] = 1;
                }
            }
            if (!empty($goods_id) && is_numeric($goods_id)) {
                // TODO::goods_id 的处理

            }
        }
        fclose($handle);
        $event = array(
            'os_stat' => $os_stat,
            'version_stat' => $version_stat,
            'location_stat' => $location_stat,
            'uri_stat' => $uri_stat,
            'user_stat' => $user_stat,
            'time_stat' => $time_stat,
            'min_stat' => $min_stat
        );
        yue_protocol_cache::set_cache($cache_key, $event, array('life_time' => 30 * 60));  // 缓存30分
        $event['fr_cache'] = 0;
        return $event;
    }

    /**
     * 绘制 柱状图
     *
     * @param array $data 绘制数据 array('key1' => 'value1','key2' => 'value2',)
     * @param int $width 图片长度
     * @param int $height 图片高度
     * @return void
     */
    public function draw_graph_stat($data, $width, $height) {
        if ($height < 22 || $width < 30 || empty($data)) {
            return FALSE;
        }
        $data_key = array_keys($data);  // 所有 键值
        $data_values = array_values($data); // 所有 值
        $num = count($data);  // 总个数
        $sum = array_sum($data);  // 总数
        // 计算 Y 轴 最大值
        $max = intval(max($data));  // 数组最大值
        $m = $max / pow(10, strlen($max) - 1);  // 转成小数
        if (round($m) > $m) {
            $tin_y = round($m);
            $top_y = $tin_y * pow(10, strlen($max) - 1);
        } else if (round($m) + 0.5 > $m) {
            $tin_y = (round($m) + 0.5);
            $top_y = $tin_y * pow(10, strlen($max) - 1);
        } else {
            $tin_y = round($m);
            $top_y = $tin_y * pow(10, strlen($max) - 1);
        }
        // 计算 Y 轴 刻度个数 ( $xkey )
        for ($xkey = 3; $xkey < 10; $xkey++) {
            if (($tin_y * 10) % $xkey == 0) {
                break;
            }
        }
        // 计算 X 轴 柱状 个数 ( 含间隔 )
        $len_x = $num * 2;

        $im = imagecreate($width, $height); // 创建图像
        imagecolorallocate($im, 255, 255, 255); // 背景色
        $acolor = imagecolorallocate($im, 0, 0, 0); // 线的颜色
        imageline($im, 25, $height - 20, $width - 5, $height - 20, $acolor); // X轴
        imageline($im, 25, $height - 20, 25, 2, $acolor); // Y轴
        // 计算, Y轴 刻度分布
        $xk_height = floor(abs($height - 20 - 2) / $xkey);  // 总高度/刻度数 = 每个刻度高度
        // 绘制 Y轴 刻度
        $y_unit = $top_y / $xkey;   // 每个刻度 值多少
        for ($i = 0; $i <= $xkey; $i++) {
            imageline($im, 25, $xk_height * $i + 2, 28, $xk_height * $i + 2, $acolor); // 画出Y轴刻度
            $offset = 10 - pow(2, $xkey / 1.5 - $i);
            imagestring($im, 2, 5, $xk_height * $i - $offset, $y_unit * ($xkey - $i), $acolor); // 标出刻度值
        }
        // 计算, X轴 刻度分布
        $yk_width = floor(($width - 5 - 25) / $len_x);  // 总长度/刻度数 = 每个刻度长度
        // 每个pix 值多少
        $ypix_unit = abs($height - 20 - 2) / $top_y;  // 总高度/总值 = 每个值的高度
        $jcolor = imagecolorallocate($im, 209, 250, 236); // 矩形的背景色
        $vcolor = imagecolorallocate($im, 204, 0, 0); // 柱体值的背景色
        $pcolor = imagecolorallocate($im, 78, 154, 6); // 百分比的背景色
        $dk = 0;
        for ($j = 0; $j < $len_x; $j += 2) {
            // 左上
            $x1 = $yk_width * ($j + 1);
            $v = $data_values[$dk];  // 柱体值
            $y1 = $height - $v * $ypix_unit;
            // 右下
            $x2 = $yk_width * ($j + 2);
            $y2 = $height;
            // 标值 偏移量
            $yd = $y1 - ($height - 40);
            $yo = $yd > 0 ? ceil($yd / 2) : -10;
            imagefilledrectangle($im, $x1 + 25, $y1 - 20, $x2 + 25, $y2 - 21, $jcolor); // 画矩形
            imagestring($im, 2, $x2 + (30 - $yk_width), $y2 - 20, $data_key[$dk], $acolor);  // X轴刻度
//            imagestring($im, 2, $x2 - 15, $y2 - 20, $data_key[$dk], $acolor);  // X轴刻度
            imagestring($im, 3, $x1 + 28, $y1 - 30 - $yo, $v, $vcolor);  // 标柱体值
            $p = sprintf('%.2f', $v / $sum); // 百分百
            imagestring($im, 3, $x1 + 28, $y1 - 18 - $yo, ($p * 100) . '%', $pcolor);  // 标百分比
            $dk++;
        }
        // 输出图片
        header('Content-Type:image/jpeg');
        imagejpeg($im);
        exit;
    }
}