<?php

//defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * 通信协议
 *         ( 说明: 参数传递 __debug = true 则表示调试状态,在协议中可以 用 $this->_debug 调试 )
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-30
 */
class yue_protocol_system {

    /**
     * @var string app name
     */
    private $_app_name = null;

    /**
     * @var boolean 是否调试
     */
    private $_debug = FALSE;

    /**
     * @var boolean  是否加密
     */
    private $_encrypt = FALSE;

    /**
     * @var string 请求连接
     */
    private $_uri = null;

    /**
     * @var mixed 用户数据
     */
    private $_input = array();

    /**
     * @var object 请求分发对象
     */
    private $request_obj = null;

    /**
     * @var object 抛出返回数据对象
     */
    private $response_obj = null;

    /**
     * 初始化
     */
    public function __construct() {
        // 协议请求分发
        $request_obj = new yue_protocol_request();
        $this->request_obj = $request_obj;
        $this->_app_name = $request_obj->get_input_item('app_name');  // 获取 app name
        $input = $request_obj->get_input(FALSE);  // 输入数据
        $unique_sign = md5($input['sign_code'] . microtime(TRUE) . mt_rand(0, 9999));  // 唯一标识
        $input['unique_sign'] = substr($unique_sign, 8, 16);  // 唯一标识( 用于数据定位 )
        $this->_encrypt = ($input['is_enc'] == 1) ? TRUE : FALSE;  // 是否加密
        $this->_debug = $request_obj->is_debug();  // 是否在调试
        // 协议返回抛出
        $uri = $this->request_obj->get_request_uri();
        $this->_uri = $uri;
        $input['uri'] = $uri;   // 请求连接
        $this->response_obj = new yue_protocol_response($input, $this->_debug);
        $this->_input = $input;
        // 设置异常处理函数
        set_exception_handler(array($this, 'protocol_exception_handler'));
    }

    /**
     * 获取 app name
     *
     * @return string
     */
    public function get_app_name() {
        return $this->_app_name;
    }

    /**
     * 是否 校验token
     *
     * @param string $app_name
     * @return boolean - true 不校验
     */
    public function is_client_detect($app_name) {
        if (empty($app_name)) {
            return FALSE;
        }
        $debug = conf('OPEN_DEBUG', 'config');
        if ($debug !== TRUE) {
            return FALSE;
        }
        $auth = conf('NON_TOKEN_AUTH', 'config');
        $auth_str = is_array($auth) ? implode(',', $auth) : $auth;  // 转为字符串
        if (strpos($auth_str, $app_name) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 获取客户端的数据
     *
     * @access public
     * @param  array $options 参数数组
     *   array(
     *         'be_check_token' => true,     // 是否检测Token , 默认true (检查)
     *         'b_response' => true,   // 是否抛出数据 , 默认true (是)
     *    );
     * @return array
     */
    public function get_input(array $options) {
        // 格式化参数
        $be_check_token = isset($options['be_check_token']) ? (bool)$options['be_check_token'] : TRUE;  // 是否验证TOKEN
        $b_response = isset($options['b_response']) ? (bool)$options['b_response'] : TRUE;  // 是否抛出数据
        $input = $this->_input;   // 输入
        return $this->get_input_process($input, $be_check_token, $b_response);
    }

    /**
     * 数据 处理
     *
     * @param array $input
     * @param boolean $token_check 是否进行token验证
     * @param boolean $b_response 是否抛出数据 ( 仅对结果有效 )
     * @return array
     */
    public function get_input_process($input, $token_check = TRUE, $b_response = TRUE) {
        $this->response_obj->set_json_ejection($b_response);  // 设置是否json抛出
        if (empty($input)) {
            return $this->response_obj->response(201);
        }
        // 修正 web 数据缺失 2015-9-22
        if (!isset($input['uri']) || !isset($input['unique_sign'])) {
            $input['uri'] = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $unique_sign = md5($input['sign_code'] . microtime(TRUE) . mt_rand(0, 9999));  // 唯一标识
            $input['unique_sign'] = substr($unique_sign, 8, 16);  // 唯一标识( 用于数据定位 )
            // 给参数赋值 ( fixed for web )
            $this->response_obj->set_response_param($input, $this->_debug);
        }
        // 写(访问)日志
        yue_protocol_log::yue_log($input, yue_protocol_log::VISIT_LOG);
        // 验证数据是否正确
        if (!$this->request_obj->is_input_valid($input)) {
            $errmsg = $this->request_obj->get_errmsg();
            return $this->response_obj->response(202, array(), $errmsg);   // 抛回错误
        }
        // 写(活动)日志 2015-8-11
        yue_protocol_log::yue_log($input, yue_protocol_log::EVENT_LOG);
        // 验证校验码是否正确
        if (!$this->request_obj->is_sign_valid($input['param'], $input['sign_code'], $this->request_obj->get_input(TRUE))) {
            $errmsg = $this->request_obj->get_errmsg();
            return $this->response_obj->response(203, array(), $errmsg);   // 抛回错误
        }
        $app_name = trim($input['app_name']);  // 请求来源
        $no_detect = $this->is_client_detect($app_name);  // 是否检测token ( -true 不检查 )
        // 是否授权验证
        if ($token_check !== FALSE && $no_detect === FALSE) {
            // 需要验证 access_token 授权
            $check_res = $this->request_obj->is_token_valid($input['param']['user_id'], $app_name, $input['param']['access_token']);
            if (empty($check_res)) {
                $errmsg = $this->request_obj->get_errmsg();
                return $this->response_obj->response(204, array(), $errmsg);   // 抛回错误
            }
        }
        $encrypt_key = conf('ENCRYPT_KEY', 'config');  // 密钥
        // 判断是否解密
        if ($input['is_enc'] == 1) {
            $input['param'] = json_decode(protocol_api_decrypt(base64_decode($input['param']), $encrypt_key), true);
        }
        // 过滤html标签 ( UTF8 )
        $param = protocol_api_input_html_filter($input['param'], 'quotes', FALSE);
        // 判断数据是否需转编码
        $client_charset = conf('CLIENT_DATA_CHARSET', 'config');  // 客户端编码
        $service_charset = conf('SERVICE_DATA_CHARSET', 'config');  // 服务端编码
        if (charset_compare($client_charset, $service_charset) !== 0) {
            // 数据编码转换
            $param = protocol_api_charset_conv($param, $service_charset, $client_charset);
        }
        // 封包
        $data = array(
            'app_name' => $input['app_name'],
            'param' => $param,
            'sign_code' => $input['sign_code'], // 签名
            'is_enc' => $input['is_enc'], // 是否加密
            'version' => $input['version'], // 客户端版本
            'os_type' => $input['os_type'],
            'ctime' => $input['ctime'],
            'udid' => $input['udid'], // 客户端的机器码
            'uri' => $this->_uri, // 请求链接
            'method' => $this->request_obj->get_method(), // 请求方式
            'ip_address' => $this->request_obj->get_request_ip(),
        );
        // 返回数据给 服务器端
        return array('code' => 100, 'msg' => 'Success!', 'data' => $data);
    }

    /**
     * 数据返回给客户端
     *
     * @access public
     * @param array $options 返回数据
     *      array( 'code' => 200, 'data' => array(), 'message' => 'xxxx' );
     * @param boolean $is_conv 是否转编码 ( 默认-true, 转编码 )
     * @return void
     */
    public function output($options, $is_conv = TRUE) {
        $code = empty($options['code']) ? 200 : $options['code'];  // 响应码
        $message = empty($options['message']) ? 'Success!' : $options['message'];  // 返回信息
        $data = empty($options['data']) ? array() : protocol_api_handle_image_cdn($options['data']);  // 处理图片的CDN问题
        return $this->response_obj->response($code, $data, $message, $this->_encrypt, $is_conv);
    }

    /**
     * 创建一个 授权 ( 写入数据 )
     *
     * @access public
     * @param int $user_id 用户ID
     * @param string $app_name 授权来源名称
     * @param boolean $b_auto_create 自动生成
     * @param boolean $b_use_cache 是否获取缓存数据
     * @return array
     */
    public function get_access_info($user_id, $app_name, $b_auto_create = TRUE, $b_use_cache = TRUE) {
        $app_name = empty($app_name) ? $this->_app_name : $app_name;
        if (!class_exists('POCO_TDG')) {
            exit('PROTOCOL ERROR: Without the framework of YUEUS included!');
        }
        $oauth_obj = new yue_protocol_oauth($app_name);
        $oauth_info = $oauth_obj->get_access_info($user_id, $app_name, $b_use_cache);
        if (empty($oauth_info)) {
            // 没有 token
            if ($b_auto_create == TRUE) {
                // 自动创建
                $oauth_info = $oauth_obj->create_access_info($user_id, $app_name);
            }
        }
        if ($oauth_info['expire_time'] < time()) {
            // token 过期
            if ($b_auto_create == TRUE) {
                // 自动自动
                $refresh_token = $oauth_info['refresh_token'];
                $oauth_info = $oauth_obj->update_by_refresh_token($user_id, $refresh_token, $app_name);
            }
        }
        return $oauth_info;
    }

    /**
     * 刷新 TOKEN
     *
     * @param int $user_id
     * @param string $app_name
     * @param string $refresh_token TOKEN
     * @param array $access_info 授权信息
     * @return array
     */
    public function refresh_access_info($user_id, $app_name, $refresh_token, $access_info = array()) {
        $user_id = empty($user_id) ? $access_info['user_id'] : $user_id;
        if (empty($app_name)) {
            $app_id = $access_info['app_id'];
            $apps = conf(null, 'access');
            foreach ($apps as $value) {
                if ($app_id == $value['app_id']) {
                    $app_name = $value['app_name'];
                    break;
                }
            }
            $app_name = empty($app_name) ? $this->_app_name : $app_name;
        }
        $refresh_token = empty($refresh_token) ? $access_info['refresh_token'] : $refresh_token;
        if (empty($user_id) || empty($app_name) || empty($refresh_token)) {
            return array();
        }
        if (!class_exists('POCO_TDG')) {
            exit('PROTOCOL ERROR: Without the framework of YUEUS included!');
        }
        $oauth_obj = new yue_protocol_oauth($app_name);
        return $oauth_obj->update_by_refresh_token($user_id, $refresh_token, $app_name);
    }

    /**
     * TOKEN 是否过期
     *
     * @param int $user_id
     * @param string $app_name
     * @param string $access_token TOKEN
     * @param array $access_info 授权信息
     * @return boolean - true 已过期
     */
    public function is_access_expire($user_id, $app_name, $access_token, $access_info = array()) {
        $user_id = empty($user_id) ? $access_info['user_id'] : $user_id;
        if (empty($app_name)) {
            $app_id = $access_info['app_id'];  // 通过 app_id 获取 app name
            $apps = conf(null, 'access');
            foreach ($apps as $value) {
                if ($app_id == $value['app_id']) {
                    $app_name = $value['app_name'];
                    break;
                }
            }
            $app_name = empty($app_name) ? $this->_app_name : $app_name;
        }
        $access_token = empty($access_token) ? $access_info['access_token'] : $access_token;
        if (empty($user_id) || empty($app_name) || empty($access_token)) {
            return array();
        }
        $oauth_obj = new yue_protocol_oauth($app_name);
        return $oauth_obj->is_access_expire($user_id, $access_token, $app_name);
    }

    /**
     * 异常处理
     *
     * @param Exception|object $ex 错误对象
     * @return bool|void
     */
    public function protocol_exception_handler(Exception $ex) {
        // 写错误日志
        $path = YUE_PROTOCOL_ROOT . 'log/PROTOCOL_ERROR_HANDLER/';
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        $file = $path . '/err-' . date('Y-m') . '.log';
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');  // uri
        $msg = $ex->getMessage(); // 错误信息
        $msg = mb_convert_encoding($msg, conf('CLIENT_DATA_CHARSET'), conf('SERVICE_DATA_CHARSET'));  // 转编码
        if (empty($_REQUEST['req'])) {
            $param = array(
                'cookie' => $_COOKIE,
                'get' => $_GET,
                'post' => $_POST,
            );
            $input = json_encode($param);
        } else {
            $input = $_REQUEST['req'];  // 用户输入
        }
        $agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');  // 服务器信息
        $md5 = substr(md5(time() . $uri . protocol_api_get_rand_string(8)), 8, 16); // 快速定位
        $data = date('Y-m-d H:i:s') . '^$^' . $md5 . '^$^' . $uri . '^$^' . $msg . '^$^' . $input . '^$^' . $agent . PHP_EOL; // 日志内容
        file_put_contents($file, $data, LOCK_EX | FILE_APPEND);  // 独占锁
        exit('ERROR(' . $md5 . '):  A protocol error occured, pls call yueus administrator!');
    }

}
