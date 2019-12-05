<?php

//defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * Э����־��¼
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-3
 */
class yue_protocol_log {

    /**
     * @var string ��־����
     */
    const VISIT_LOG = 'visit_log';
    const EVENT_LOG = 'event_log';
    const RUNTIME_LOG = 'runtime_log';
    const SLOWQ_LOG = 'slowquery_log';
    const TOKEN_LOG = 'token_log';

    /**
     * @var string �ָ��
     */
    static private $_delimiter = '^$^';

    /**
     * @var int ��־����
     */
    static private $_log_group_id = 0;

    /**
     * @var boolean �Ƿ����
     */
    static private $_debug = FALSE;

    /**
     * ͨ��ID ������־����
     *
     * @param int $group_id ����ID
     * @return void
     */
    static public function set_group_by_id($group_id) {
        self::$_log_group_id = intval($group_id);
    }

    /**
     * ͨ��app_name ������־����
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
     * ��ȡ��־·��
     *
     * @param string $date ʱ�� ( �������ڸ�ʽ: 2015-9-3, 2015/9/3 )
     * @return string
     */
    static private function get_log_path($date) {
        $log_path = YUE_PROTOCOL_ROOT . 'log/';  // ��װ��־Ŀ¼
        if (!is_dir($log_path)) {
            // ��Ŀ¼�� �򴴽�
            mkdir($log_path, 0777, TRUE);
        }
        $date = date('ym', strtotime($date));
        if ($date == '7001') {  // �����ʽ,���ʾ����
            $date = date('ym');
        }
        $group_id = self::$_log_group_id;  // ��־����ID
        $path = $log_path . '/' . $date . '_' . substr(md5($date . '@YUEUS.com'), 8, 8) . '_' . $group_id . '/';
        if (!is_dir($path)) {
            // ��Ŀ¼�� �򴴽�
            mkdir($path, 0777, TRUE);
        }
        return str_replace('//', '/', $path . '/');
    }

    /**
     * ��ȡ ��־�ļ�
     *
     * @param string $type ��־���� ( const ���� )
     * @param string $date ���� ( �������ڸ�ʽ: 2015-9-3, 2015/9/3 )
     * @param string $app_name
     * @return string
     */
    static public function get_log_file($type, $date = null, $app_name = null) {
        $app_name = trim($app_name);
        if(empty($app_name)){  // û��app����,����¼
            return false;
        }
        if (empty($date)) {
            $date = date('Y-m-d');
        } else {
            $date = date('Y-m-d', strtotime($date));
            if ($date == '1970-01-01') {
                // ���ڸ�ʽ����, ����
                $date = date('Y-m-d');
            }
        }
        if (!empty($app_name)) {
            // ���÷���
            self::set_group_by_name($app_name);
        }
        $path = self::get_log_path($date);  // �ļ�·��
        $file = '';
        switch ($type) {
            case self::VISIT_LOG:  // ������־
                $app_name = str_replace(array(' ', '#', '.', ':', '&', '"', '<', '>', '|', '`', '~', '=', '$', '%', '*', '^', '/', '\\', '?', 'poco_'), '', $app_name);
                $file = $path . $date . '_' . $app_name . '.log';
                break;
            case self::EVENT_LOG:  // �¼���־
                $file = $path . substr($date, 0, 7) . '_event' . '.log';
                break;
            case self::RUNTIME_LOG:  // ��ʱ��־
                $file = $path . substr($date, 0, 7) . '_runtime' . '.log';
                break;
            case self::SLOWQ_LOG:  // ����ѯ��־
                $file = $path . substr($date, 0, 7) . '_slowquery' . '.log';
                break;
            case self::TOKEN_LOG:  // TOKEN��־
                $file = $path . substr($date, 0, 7) . '_token' . '.log';
                break;
            default:
                break;
        }
        return $file;
    }

    /**
     *  д�� ������־
     *
     * @param mixed $data ��־����
     * @param string $app_name ���� ( ����: poco_yuepai_iphone )
     * @return boolean
     */
    static public function visit_log($data, $app_name = null) {
        if (!conf('OPEN_VISIT_LOGGER', 'config')) {
            // ��д����־
            return FALSE;
        }
        if (empty($data) || is_resource($data)) {
            // ��Դ�ļ����ɼ�¼
            return FALSE;
        }
        $app_name = trim($app_name);
        $log_file = self::get_log_file(self::VISIT_LOG, null, $app_name);  // ��־�ļ�
        if (empty($log_file)) {
            return FALSE;
        }
        $log_data = date('Y-m-d H:i:s') . self::$_delimiter . serialize($data) . PHP_EOL;  // ��װ��־����
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // ��ռ��
    }

    /**
     * ���־ ( ͳ�ƻ�Ծ�� )
     *
     * @param array $data ���־��Ϣ
     * @param string $app_name APP����
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
        $log_file = self::get_log_file(self::EVENT_LOG, null, $app_name);  // ��־�ļ�
        if (empty($log_file)) {
            return FALSE;
        }
        $log_data = date('Y-m-d H:i:s') . '|' . implode('|', $data) . self::$_delimiter;  // ��־����
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // ��ռ��
    }

    /**
     * ��ʱ��־
     *
     * @param string $uri ��������
     * @param array $data ��ʱ����
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
        $log_file = self::get_log_file(self::RUNTIME_LOG, null, $app_name);  // ��־�ļ�
        if (empty($log_file)) {
            return FALSE;
        }
        if (($pos = strpos($uri, '?')) > 0) {
            // ȥ������ "?" �����
            $uri = substr($uri, 0, $pos);
        }
        $log_data = date('Y-m-d H:i:s') . '|' . $uri . '|' . implode('|', $data) . self::$_delimiter;  // ��־����
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // ��ռ��
    }

    /**
     * ����ѯ��־
     *
     * @param string $uri ��������
     * @param string $runtime ��ʱ
     * @param mixed $input �û�����
     * @param string $app_name
     * @return boolean
     */
    static public function slowquery_log($uri, $runtime, $input, $app_name = null) {
        if (!conf('OPEN_SLOWQUERY_LOGGER', 'config')) {
            // ��д����־
            return FALSE;
        }
        if (empty($uri) || empty($runtime) || empty($input)) {
            return FALSE;
        }
        $app_name = trim($app_name);
        $log_file = self::get_log_file(self::SLOWQ_LOG, null, $app_name);  // ��־�ļ�
        if (empty($log_file)) {
            return FALSE;
        }
        $delimiter = self::$_delimiter;  // �ָ��
        $agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');  // ��������Ϣ
        $log_data = date('Y-m-d H:i:s') . $delimiter . $app_name . $delimiter . $runtime .
            $delimiter . $uri . $delimiter . serialize($input) . $delimiter . $agent . PHP_EOL; // ��־����
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // ��ռ��
    }

    /**
     * ������Ȩ(���ݿ�)��־
     *
     * @param string $function ��������
     * @param array $data
     * @param string $app_name
     * @return boolean
     */
    static public function token_log($function, $data, $app_name = null) {
        if (!conf('OPEN_TOKEN_LOGGER', 'config')) {
            // ��д����־
            return FALSE;
        }
        if (empty($data)) {
            return FALSE;
        }
        $app_name = trim($app_name);
        $log_file = self::get_log_file(self::TOKEN_LOG, null, $app_name);  // ��־�ļ�
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
        $log_data = date('Y-m-d H:i:s') . '|' . implode('|', $token_data) . '|' . $app_name . self::$_delimiter; // ��־����
        return file_put_contents($log_file, $log_data, LOCK_EX | FILE_APPEND);  // ��ռ��
    }

    /**
     * ���׻��� ǩ��У�����
     *
     * @param  mixed $data ��־����
     * @param  string $app_name
     * @return mixed
     */
    static public function sign_error_log($data, $app_name = null) {
        // д������־
        $path = YUE_PROTOCOL_ROOT . 'log/PROTOCOL_ERROR/';
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        $file = $path . '/err-sign-' . date('Y-m') . '.log';
        $log_data = date('Y-m-d H:i:s') . self::$_delimiter . $app_name . self::$_delimiter . serialize($data) . PHP_EOL; // ��־����
        return file_put_contents($file, $log_data, LOCK_EX | FILE_APPEND);  // ��ռ��
    }

    /**
     * ԼԼ��־
     *
     * @param array $input ��������
     * @param string $log_type ��־��
     * @return boolean
     */
    static public function yue_log($input, $log_type = null) {
        if (empty($input) || empty($log_type)) {
            return FALSE;
        }
        if (isset($input['param']['__debug']) && strtolower($input['param']['__debug']) == 'true') {
            // �Ƿ� ����
            self::$_debug = TRUE;
        }
        switch ($log_type) {
            case self::VISIT_LOG:  // ������־
                $param = $input['param'];
                // ȥ����������
                if (isset($param['access_token'])) {  // ȥ��token
                    $access_token = $param['access_token'];
                    $input['param']['access_token'] = substr($access_token, 0, 6) . '***' . substr($access_token, -6, 6);
                }
                if (isset($param['refresh_token'])) {  // ȥ��token
                    $refresh_token = $param['refresh_token'];
                    $input['param']['refresh_token'] = substr($refresh_token, 0, 6) . '***' . substr($refresh_token, -6, 6);
                }
                if (isset($param['pwd'])) {  // ȥ������
                    $input['param']['pwd'] = '***';
                }
                return self::visit_log($input, $input['app_name']);
            case self::EVENT_LOG: // ���־
                $event_data = array(
                    intval($input['param']['user_id']),
                    $input['param']['location_id'],
                    $input['os_type'],
                    $input['version'],
                    $input['uri'],
                    intval($input['param']['goods_id']),
                );
                return self::event_log($event_data, $input['app_name']);
            case self::RUNTIME_LOG:   // ��ʱ��־
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
     * ��ȡ ��־�б�
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
        sort($tmp);  // ����
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
     *  ��ȡ ��־�б� ����
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
     * ��ȡ ÿ���¼��־�б�
     *
     * @param string $date ���� ( ����: 2015-9-23 )
     * @return array
     */
    static public function get_daily_logs($date = null) {
        $apps = conf(null, 'access');
        $type = array(self::VISIT_LOG, self::EVENT_LOG, self::RUNTIME_LOG, self::SLOWQ_LOG, self::TOKEN_LOG);
        $log_list = array();
        foreach ($apps as $val) {
            $name = $val['app_name'];  // �ͻ���
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
     * ��ȡ Э����� �ļ�
     *
     * @param string $date ����
     * @return string
     */
    static public function get_error_handler_file($date = null) {
        if (empty($date)) {
            $date = date('Y-m');
        } else {
            $date = date('Y-m', strtotime($date));
            if ($date == '1970-01') {
                // ���ڸ�ʽ����, ����
                $date = date('Y-m');
            }
        }
        $path = YUE_PROTOCOL_ROOT . 'log/PROTOCOL_ERROR_HANDLER/';
        $file = $path . '/err-' . $date . '.log';
        return $file;
    }
}

/**
 * Ŀ¼����
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-11
 */
class RecursiveFileFilterIterator extends FilterIterator {

    /**
     * @var array ������������չ��
     */
    protected $ext = array('log');

    /**
     * �ṩ $path �����ɶ�Ӧ��Ŀ¼������
     *
     * @param string $path
     */
    public function __construct($path) {
        if (!is_dir($path)) {  // ��Ŀ¼
            exit('Invalid directory!');
        }
        parent::__construct(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)));
    }

    /**
     * ����ļ���չ���Ƿ���������
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
