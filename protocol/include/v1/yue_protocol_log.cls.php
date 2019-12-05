<?php

//defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * 协议日志记录
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-3
 */
class yue_protocol_log {

    /**
     * @var string 日志类型
     */
    const VISIT_LOG = 'visit_log';
    const EVENT_LOG = 'event_log';
    const RUNTIME_LOG = 'runtime_log';
    const SLOWQ_LOG = 'slowquery_log';
    const TOKEN_LOG = 'token_log';

    /**
     * @var string 分割符
     */
    static private $_delimiter = '^$^';

    /**
     * @var int 日志分组
     */
    static private $_log_group_id = 0;

    /**
     * @var boolean 是否调试
     */
    static private $_debug = FALSE;

    /**
     * 通过ID 设置日志分组
     *
     * @param int $group_id 分组ID
     * @return void
     */
    static public function set_group_by_id($group_id) {
        self::$_log_group_id = intval($group_id);
    }

    /**
     * 通过app_name 设置日志分组
     *
     * @param string $app_name
     * @return boolean
     */
    static public function set_group_by_name($app_name) {
        $config = conf($app_name, 'access');
        if (empty($config)) {
            return FALSE;
        }
        self::$_log_group_id = intval($config['group_id']);
        return TRUE;
    }

    /**
     * 获取日志路径
     *
     * @param string $date 时间 ( 正常日期格式: 2015-9-3, 2015/9/3 )
     * @return string
     */
    static private function get_log_path($date) {
        $log_path = YUE_PROTOCOL_ROOT . 'log/';  // 组装日志目录
        if (!is_dir($log_path)) {
            // 非目录， 则创建
            mkdir($log_path, 0777, TRUE);
        }
        $date = date('ym', strtotime($date));
        if ($date == '7001') {  // 错误格式,则表示今天
            $date = date('ym');
        }
        $group_id = self::$_log_group_id;  // 日志分组ID
        $path = $log_path . '/' . $date . '_' . substr(md5($date . '@YUEUS.com'), 8, 8) . '_' . $group_id . '/';
        if (!is_dir($path)) {
            // 非目录， 则创建
            mkdir($path, 0777, TRUE);
        }
        return str_replace('//', '/', $path . '/');
    }

    /**
     * 获取 日志文件
     *
     * @param string $type 日志类型 ( const 内容 )
     * @param string $date 日期 ( 正常日期格式: 2015-9-3, 2015/9/3 )
     * @param string $app_name
     * @return string
     */
    static public function get_log_file($type, $date = null, $app_name = null) {
        $app_name = trim($app_name);
        if(empty($app_name)){  // 没有app类型,不记录
            return false;
        }
        if (empty($date)) {
            $date = date('Y-m-d');
        } else {
            $date = date('Y-m-d', strtotime($date));
            if ($date == '1970-01-01') {
                // 日期格式错误, 则当天
                $date = date('Y-m-d');
            }
        }
        if (!empty($app_name)) {
            // 设置分组
            self::set_group_by_name($app_name);
        }
        $path = self::get_log_path($date);  // 文件路径
        $file = '';
        switch ($type) {
            case self::VISIT_LOG:  // 访问日志
                $app_name = str_replace(array(' ', '#', '.', ':', '&', '"', '<', '>', '|', '`', '~', '=', '$', '%', '*', '^', '/', '\\', '?', 'poco_'), '', $app_name);
                $file = $path . $date . '_' . $app_name . '.log';
                break;
            case self::EVENT_LOG:  // 事件日志
                $file = $path . substr($date, 0, 7) . '_event' . '.log';
                break;
            case self::RUNTIME_LOG:  // 耗时日志
                $file = $path . substr($date, 0, 7) . '_runtime' . '.log';
                break;
            case self::SLOWQ_LOG:  // 慢查询日志
                $file = $path . substr($date, 0, 7) . '_slowquery' . '.log';
                break;
            case self::TOKEN_LOG:  // TOKEN日志
                $file = $path . substr($date, 0, 7) . '_token' . '.log';
                break;
            default:
                break;
        }
        return $file;
    }

    /**
     *  写入 访问日志
     *
     * @param mixed $data 日志内容
     * @param string $app_name 类型 ( 例如: poco_yuepai_iphone )
     * @return boolean
     */
    static public function visit_log($data, $app_name = null) {
        if (!conf('OPEN_VISIT_LOGGER', 'config')) {
            // 不写入日志
            return FALSE;
        }
        if (empty($data) || is_resource($data)) {
            // 资源文件不可记录
            return FALSE;
        }
        $app_name = trim($app_name);
        $log_file = self::get_log_file(self::VISIT_LOG, null, $app_name);  // 日志文件
        if (empty($log_file)) {
            return FALSE;
        }
        $log_data = date('Y-m-d H:i:s') . self::$_delimiter . serialize($data) . PHP_EOL;  // 组装日志内容
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // 独占锁
    }

    /**
     * 活动日志 ( 统计活跃数 )
     *
     * @param array $data 活动日志信息
     * @param string $app_name APP名称
     * @return boolean
     */
    static public function event_log($data, $app_name = null) {
        if (!conf('OPEN_EVENT_LOGGER', 'config')) {
            return FALSE;
        }
        if (empty($data)) {
            return FALSE;
        }
        $app_name = trim($app_name);
        $log_file = self::get_log_file(self::EVENT_LOG, null, $app_name);  // 日志文件
        if (empty($log_file)) {
            return FALSE;
        }
        $log_data = date('Y-m-d H:i:s') . '|' . implode('|', $data) . self::$_delimiter;  // 日志内容
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // 独占锁
    }

    /**
     * 耗时日志
     *
     * @param string $uri 访问链接
     * @param array $data 耗时数据
     * @param string $app_name
     * @return boolean
     */
    static public function runtime_log($uri, $data, $app_name = null) {
        if (!conf('OPEN_RUNTIME_LOGGER', 'config')) {
            return FALSE;
        }
        if (empty($uri) || empty($data)) {
            return FALSE;
        }
        $app_name = trim($app_name);
        $log_file = self::get_log_file(self::RUNTIME_LOG, null, $app_name);  // 日志文件
        if (empty($log_file)) {
            return FALSE;
        }
        if (($pos = strpos($uri, '?')) > 0) {
            // 去除链接 "?" 后参数
            $uri = substr($uri, 0, $pos);
        }
        $log_data = date('Y-m-d H:i:s') . '|' . $uri . '|' . implode('|', $data) . self::$_delimiter;  // 日志内容
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // 独占锁
    }

    /**
     * 慢查询日志
     *
     * @param string $uri 访问链接
     * @param string $runtime 耗时
     * @param mixed $input 用户输入
     * @param string $app_name
     * @return boolean
     */
    static public function slowquery_log($uri, $runtime, $input, $app_name = null) {
        if (!conf('OPEN_SLOWQUERY_LOGGER', 'config')) {
            // 不写入日志
            return FALSE;
        }
        if (empty($uri) || empty($runtime) || empty($input)) {
            return FALSE;
        }
        $app_name = trim($app_name);
        $log_file = self::get_log_file(self::SLOWQ_LOG, null, $app_name);  // 日志文件
        if (empty($log_file)) {
            return FALSE;
        }
        $delimiter = self::$_delimiter;  // 分割符
        $agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');  // 服务器信息
        $log_data = date('Y-m-d H:i:s') . $delimiter . $app_name . $delimiter . $runtime .
            $delimiter . $uri . $delimiter . serialize($input) . $delimiter . $agent . PHP_EOL; // 日志内容
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // 独占锁
    }

    /**
     * 操作授权(数据库)日志
     *
     * @param string $function 访问链接
     * @param array $data
     * @param string $app_name
     * @return boolean
     */
    static public function token_log($function, $data, $app_name = null) {
        if (!conf('OPEN_TOKEN_LOGGER', 'config')) {
            // 不写入日志
            return FALSE;
        }
        if (empty($data)) {
            return FALSE;
        }
        $app_name = trim($app_name);
        $log_file = self::get_log_file(self::TOKEN_LOG, null, $app_name);  // 日志文件
        if (empty($log_file)) {
            return FALSE;
        }
        $access_token = $data['access_token'];
        $refresh_token = $data['refresh_token'];
        $token_data = array(
            $data['user_id'],
            substr($access_token, 0, 6) . '***' . substr($access_token, -6, 6),
            substr($refresh_token, 0, 6) . '***' . substr($refresh_token, -6, 6),
            $data['expire_time'],
            $function,
        );
        $log_data = date('Y-m-d H:i:s') . '|' . implode('|', $token_data) . '|' . $app_name . self::$_delimiter; // 日志内容
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // 独占锁
    }

    /**
     * 简易缓存 签名校验错误
     *
     * @param  mixed $data 日志数据
     * @param  string $app_name
     * @return mixed
     */
    static public function sign_error_log($data, $app_name = null) {
        // 写错误日志
        $path = YUE_PROTOCOL_ROOT . 'log/PROTOCOL_ERROR/';
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        $file = $path . '/err-sign-' . date('Y-m') . '.log';
        $log_data = date('Y-m-d H:i:s') . self::$_delimiter . $app_name . self::$_delimiter . serialize($data) . PHP_EOL; // 日志内容
        return file_put_contents($file, $log_data, LOCK_EX | FILE_APPEND);  // 独占锁
    }

    /**
     * 约约日志
     *
     * @param array $input 请求数据
     * @param string $log_type 日志类
     * @return boolean
     */
    static public function yue_log($input, $log_type = null) {
        if (empty($input) || empty($log_type)) {
            return FALSE;
        }
        if (isset($input['param']['__debug']) && strtolower($input['param']['__debug']) == 'true') {
            // 是否 调试
            self::$_debug = TRUE;
        }
        switch ($log_type) {
            case self::VISIT_LOG:  // 访问日志
                $param = $input['param'];
                // 去除敏感数据
                if (isset($param['access_token'])) {  // 去除token
                    $access_token = $param['access_token'];
                    $input['param']['access_token'] = substr($access_token, 0, 6) . '***' . substr($access_token, -6, 6);
                }
                if (isset($param['refresh_token'])) {  // 去除token
                    $refresh_token = $param['refresh_token'];
                    $input['param']['refresh_token'] = substr($refresh_token, 0, 6) . '***' . substr($refresh_token, -6, 6);
                }
                if (isset($param['pwd'])) {  // 去除密码
                    $input['param']['pwd'] = '***';
                }
                return self::visit_log($input, $input['app_name']);
            case self::EVENT_LOG: // 活动日志
                $event_data = array(
                    intval($input['param']['user_id']),
                    $input['param']['location_id'],
                    $input['os_type'],
                    $input['version'],
                    $input['uri'],
                    intval($input['param']['goods_id']),
                );
                return self::event_log($event_data, $input['app_name']);
            case self::RUNTIME_LOG:   // 耗时日志
                $runtime_data = array(
                    $input['runtime'],
                    intval($input['param']['user_id']),
                    $input['app_name'],
                    $input['unique_sign'],
                );
                return self::runtime_log($input['uri'], $runtime_data, $input['app_name']);
            default:
                break;
        }
        return FALSE;
    }

    /**
     * 获取 日志列表
     *
     * @return array
     */
    static public function get_log_files_list() {
        $path = YUE_PROTOCOL_ROOT . 'log/';
        $logs = $tmp = array();
        foreach (new RecursiveFileFilterIterator($path) as $item) {
            $file = str_replace('\\', '/', strval($item));
            $tmp[] = $file;
        }
        sort($tmp);  // 排序
        foreach ($tmp as $file) {
            list($basedir, $file_name) = explode('/', str_replace($path, '', $file));
            $logs[$basedir][] = array(
                'file' => $file,
                'name' => $file_name,
            );
        }
        return $logs;
    }

    /**
     *  获取 日志列表 缓存
     *
     * @return array
     */
    static public function get_log_files_cache() {
        $cache_key = 'YUE_LOGS_FILE_CACHE';
        $logs_list = yue_protocol_cache::get_cache($cache_key);
        if (empty($logs_list)) {
            $logs_list = self::get_log_files_list();
            yue_protocol_cache::set_cache($cache_key, $logs_list, array('life_time' => 60 * 60));
        }
        return $logs_list;
    }

    /**
     * 获取 每天记录日志列表
     *
     * @param string $date 日期 ( 例如: 2015-9-23 )
     * @return array
     */
    static public function get_daily_logs($date = null) {
        $apps = conf(null, 'access');
        $type = array(self::VISIT_LOG, self::EVENT_LOG, self::RUNTIME_LOG, self::SLOWQ_LOG, self::TOKEN_LOG);
        $log_list = array();
        foreach ($apps as $val) {
            $name = $val['app_name'];  // 客户端
            foreach ($type as $v) {
                $file = self::get_log_file($v, $date, $name);
                if (file_exists($file)) {
                    $fname = basename($file);
                    $log_list[$fname] = $file;
                }
            }
        }
        sort($log_list);
        return $log_list;
    }

    /**
     * 获取 协议错误 文件
     *
     * @param string $date 日期
     * @return string
     */
    static public function get_error_handler_file($date = null) {
        if (empty($date)) {
            $date = date('Y-m');
        } else {
            $date = date('Y-m', strtotime($date));
            if ($date == '1970-01') {
                // 日期格式错误, 则当天
                $date = date('Y-m');
            }
        }
        $path = YUE_PROTOCOL_ROOT . 'log/PROTOCOL_ERROR_HANDLER/';
        $file = $path . '/err-' . $date . '.log';
        return $file;
    }
}

/**
 * 目录遍历
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-11
 */
class RecursiveFileFilterIterator extends FilterIterator {

    /**
     * @var array 满足条件的扩展名
     */
    protected $ext = array('log');

    /**
     * 提供 $path 并生成对应的目录迭代器
     *
     * @param string $path
     */
    public function __construct($path) {
        if (!is_dir($path)) {  // 非目录
            exit('Invalid directory!');
        }
        parent::__construct(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)));
    }

    /**
     * 检查文件扩展名是否满足条件
     *
     * @return void
     */
    public function accept() {
        $item = $this->getInnerIterator();
        if ($item->isFile() && in_array(pathinfo($item->getFilename(), PATHINFO_EXTENSION), $this->ext)) {
            return TRUE;
        }
    }

}
