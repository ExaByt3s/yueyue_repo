<?php

defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * �����׳� ( ���ؿͻ��� )
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-31
 * @version 1.0 Beta
 */
class yue_protocol_response {

    /**
     * @var array ��������
     */
    private $_input = array();

    /**
     * @var string ��������
     */
    private $_uri = '';

    /**
     * @var string �Ƿ����
     */
    private $_debug = FALSE;

    /**
     * @var boolean JSON �׳� ( ���ؿͻ��� )
     */
    private $_json_ejection = TRUE;

    /**
     * ��ʼ��
     */
    public function __construct($input = array(), $debug = FALSE) {
        $this->_input = $input;
        $this->_uri = $input['uri'];
        $this->_debug = ($debug === TRUE) ? TRUE : FALSE;
    }

    /**
     * ���� ����
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
     * �Ƿ� json ����
     * 
     * @param boolean $ejection ����
     * @return void
     */
    public function set_json_ejection($ejection = TRUE) {
        $this->_json_ejection = ($ejection === FALSE) ? FALSE : TRUE;
    }

    /**
     * ��Ӧ�ͻ���
     * 
     * @access public
     * @param int $code ��������״̬��
     * @param mixed ��������
     * @param string $message ��ʾ��Ϣ
     * @param boolean $b_encrypt �����Ƿ����
     * @param boolean $b_conv �Ƿ�ת���� ( -true ת�� )
     * @return void
     */
    public function response($code, $data = array(), $message = null, $b_encrypt = FALSE, $b_conv = TRUE) {
        if (empty($message)) {
            // ������ ����
            $message = conf($code, 'code');
        }
        // �ж������Ƿ���ת����
        $client_charset = conf('CLIENT_DATA_CHARSET', 'config');  // �ͻ��˱���
        $service_charset = conf('SERVICE_DATA_CHARSET', 'config');  // ����˱���
        if (charset_compare($client_charset, $service_charset) !== 0 && $b_conv !== FALSE) {
            // ���ݱ���ת��
            $message = protocol_api_charset_conv($message, $client_charset, $service_charset);  // ��ʾ��Ϣ����ת��
            $data = protocol_api_charset_conv($data, $client_charset, $service_charset);
        }
        $input = $this->_input;   // ��������
        // ��� Э�����ݴ��� ��ʱ
        $runtime = sprintf('%.4f', microtime(TRUE) - $GLOBALS['protocol_start_time']); // ��ʱ
        // ��װ�׳�����
        $ret_arr = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'runtime' => $runtime, // ��ʱ
            'base' => $input['unique_sign'], // Ψһ��ʶ
            'appver' => $input['version'], // app �汾
            'apiver' => conf('PROTOCOL_VERSION', 'config'), // �汾
        );
        // �Ƿ�������
        if ($this->_debug) {
            $ret_arr['uri'] = $this->_uri;
            unset($input['param']['__debug']);  // ȥ������
            $ret_arr['input'] = $input;  // �û�����
        }
        // תjson, ���滻JSON�еĻ��лس���Ϊ'\n'
        $ret_json = str_replace(array("\r\n", "\n\r"), '\n', json_encode($ret_arr));
        // �Ƿ����
        if ($b_encrypt === TRUE) {
            $encrypt_key = conf('ENCRYPT_KEY', 'config');  // ��Կ
            $ret_json = protocol_api_encrypt($ret_json, $encrypt_key);  // ����
        }
        // д(��ʱ)��־ 2015-9-2
        $input['runtime'] = $runtime; // ��ʱ
        yue_protocol_log::yue_log($input, yue_protocol_log::RUNTIME_LOG);
        if ($runtime >= 2) {
            // ȥ��������Ϣ
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
            // ����2��,д����ѯ��־
            yue_protocol_log::slowquery_log($this->_uri, $runtime, $input, $input['app_name']);
        }
        if ($this->_json_ejection === FALSE) {
            // ֱ�ӷ��� �����׳�
            return $ret_json;
        }
        // �׳�����
        header("Content-type:application/json;charset=utf-8");
        exit($ret_json);
    }

}
