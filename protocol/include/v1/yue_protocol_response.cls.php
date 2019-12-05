<?php

defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * 数据抛出 ( 返回客户端 )
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-31
 * @version 1.0 Beta
 */
class yue_protocol_response {

    /**
     * @var array 输入数据
     */
    private $_input = array();

    /**
     * @var string 请求链接
     */
    private $_uri = '';

    /**
     * @var string 是否调试
     */
    private $_debug = FALSE;

    /**
     * @var boolean JSON 抛出 ( 返回客户端 )
     */
    private $_json_ejection = TRUE;

    /**
     * 初始化
     */
    public function __construct($input = array(), $debug = FALSE) {
        $this->_input = $input;
        $this->_uri = $input['uri'];
        $this->_debug = ($debug === TRUE) ? TRUE : FALSE;
    }

    /**
     * 设置 参数
     * 
     * @param array $input
     * @param boolean $debug
     * @return void 
     */
    public function set_response_param($input, $debug = FALSE) {
        $this->_input = $input;
        $this->_debug = ($debug === TRUE) ? TRUE : FALSE;
    }

    /**
     * 是否 json 返回
     * 
     * @param boolean $ejection 返回
     * @return void
     */
    public function set_json_ejection($ejection = TRUE) {
        $this->_json_ejection = ($ejection === FALSE) ? FALSE : TRUE;
    }

    /**
     * 响应客户端
     * 
     * @access public
     * @param int $code 服务器端状态码
     * @param mixed 返回数据
     * @param string $message 提示信息
     * @param boolean $b_encrypt 数据是否加密
     * @param boolean $b_conv 是否转编码 ( -true 转换 )
     * @return void
     */
    public function response($code, $data = array(), $message = null, $b_encrypt = FALSE, $b_conv = TRUE) {
        if (empty($message)) {
            // 错误码 配置
            $message = conf($code, 'code');
        }
        // 判断数据是否需转编码
        $client_charset = conf('CLIENT_DATA_CHARSET', 'config');  // 客户端编码
        $service_charset = conf('SERVICE_DATA_CHARSET', 'config');  // 服务端编码
        if (charset_compare($client_charset, $service_charset) !== 0 && $b_conv !== FALSE) {
            // 数据编码转换
            $message = protocol_api_charset_conv($message, $client_charset, $service_charset);  // 提示信息编码转换
            $data = protocol_api_charset_conv($data, $client_charset, $service_charset);
        }
        $input = $this->_input;   // 数据输入
        // 添加 协议内容处理 耗时
        $runtime = sprintf('%.4f', microtime(TRUE) - $GLOBALS['protocol_start_time']); // 耗时
        // 组装抛出数据
        $ret_arr = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'runtime' => $runtime, // 耗时
            'base' => $input['unique_sign'], // 唯一标识
            'appver' => $input['version'], // app 版本
            'apiver' => conf('PROTOCOL_VERSION', 'config'), // 版本
        );
        // 是否开启调试
        if ($this->_debug) {
            $ret_arr['uri'] = $this->_uri;
            unset($input['param']['__debug']);  // 去除调试
            $ret_arr['input'] = $input;  // 用户输入
        }
        // 转json, 并替换JSON中的换行回车符为'\n'
        $ret_json = str_replace(array("\r\n", "\n\r"), '\n', json_encode($ret_arr));
        // 是否加密
        if ($b_encrypt === TRUE) {
            $encrypt_key = conf('ENCRYPT_KEY', 'config');  // 密钥
            $ret_json = protocol_api_encrypt($ret_json, $encrypt_key);  // 加密
        }
        // 写(耗时)日志 2015-9-2
        $input['runtime'] = $runtime; // 耗时
        yue_protocol_log::yue_log($input, yue_protocol_log::RUNTIME_LOG);
        if ($runtime >= 2) {
            // 去除敏感信息
            if (isset($input['param']['access_token'])) {
                $access_token = $input['param']['access_token'];
                $input['param']['access_token'] = substr($access_token, 0, 6) . '***' . substr($access_token, -6, 6);
            }
            if (isset($input['param']['refresh_token'])) {
                $access_token = $input['param']['refresh_token'];
                $input['param']['refresh_token'] = substr($access_token, 0, 6) . '***' . substr($access_token, -6, 6);
            }
            if (isset($input['param']['pwd'])) {
                $input['param']['pwd'] = '***';
            }
            // 超过2秒,写慢查询日志
            yue_protocol_log::slowquery_log($this->_uri, $runtime, $input, $input['app_name']);
        }
        if ($this->_json_ejection === FALSE) {
            // 直接返回 而非抛出
            return $ret_json;
        }
        // 抛出数据
        header("Content-type:application/json;charset=utf-8");
        exit($ret_json);
    }

}
