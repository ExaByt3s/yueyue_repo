<?php

//defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * Э������ַ���
 *
 * @author willike  <chenwb@yueus.com>
 * @since 2015-7-30
 * @version 1.0 Beta
 */
class yue_protocol_request {

    /**
     * @var string ����ʽ
     */
    private $_request_method = null;

    /**
     * @var string �û�����
     */
    private $_input = null;

    /**
     * @var string ������Ϣ
     */
    private $_errmsg = null;

    /**
     * @var string ����URI
     */
    private $_request_uri = null;

    /**
     * @var string ����ʱIP
     */
    private $_request_ip = null;

    /**
     * @var boolean �Ƿ����
     */
    private $_debug = FALSE;

    /**
     * ��ʼ��
     */
    public function __construct() {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            // ��ֹ magic quotes
            set_magic_quotes_runtime(FALSE);
        }
        $request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD'); // ����ʽ
        $this->_request_method = $request_method;
        $this->_request_uri = filter_input(INPUT_SERVER, 'REQUEST_URI');   // �����URI
        $this->_request_ip = isset($_REQUEST['session_ip']) ? $_REQUEST['session_ip'] : get_client_ip();  // �����IP
        // ��������ʽ��ȡ�ͻ��˵�����
        switch ($request_method) {
            case 'POST':
                $this->_input = (isset($_POST['req']) && !empty($_POST['req'])) ? $_POST['req'] : '';
                break;
            case 'GET':
                $input = (isset($_GET['req']) && !empty($_GET['req'])) ? $_GET['req'] : '';
                $this->_input = base64_decode(str_replace(' ', '+', $input));  // Base64���С�+���ţ�����URL��Ϊ�˿ո�
                break;
            default:
                $this->_input = null;
        }
        $this->_debug = $this->is_debug();  // �Ƿ����
    }

    /**
     * ��ȡ ������Ϣ
     * 
     * @return string 
     */
    public function get_errmsg() {
        return $this->_errmsg;
    }

    /**
     * ���ؿͻ��˵�����ʽ
     * 
     * @return string
     */
    public function get_method() {
        return $this->_request_method;
    }

    /**
     * �ж��Ƿ���GET����
     * 
     * @return boolean - true ��
     */
    public function is_get() {
        return $this->_request_method == 'GET' ? TRUE : FALSE;
    }

    /**
     * �ж��Ƿ���POST����
     * 
     * @return boolean - true ��
     */
    public function is_post() {
        return $this->_request_method == 'POST' ? TRUE : FALSE;
    }

    /**
     * �ж��Ƿ���PUT����
     * 
     * @return boolean - true ��
     */
    public function is_put() {
        return $this->_request_method == 'PUT' ? TRUE : FALSE;
    }

    /**
     * �ж��Ƿ���DELETE����
     * 
     * @return boolean - true ��
     */
    public function is_delete() {
        return $this->_request_method == 'DELETE' ? TRUE : FALSE;
    }

    /**
     * ���ؿͻ��˵Ĳ���
     * 
     * @access public
     * @param boolean $raw ԭʼ����
     * @return mixed
     */
    public function get_input($raw = FALSE) {
        return ($raw === TRUE) ? $this->_input : json_decode($this->_input, TRUE);
    }

    /**
     * ��ȡ ĳ��������
     * 
     * @return mixed
     */
    public function get_input_item($name) {
        $input = json_decode($this->_input, TRUE);
        return isset($input[$name]) ? $input[$name] : null;
    }

    /**
     * �Ƿ����
     * 
     * @return boolean - true ����
     */
    public function is_debug() {
        if (!conf('OPEN_DEBUG', 'config')) {
            return FALSE;
        }
        $input = json_decode($this->_input, TRUE);
        $debug = isset($input['param']['__debug']) ? $input['param']['__debug'] : null;  // �Ƿ����
        return strtolower($debug) === 'true' ? TRUE : FALSE;
    }

    /**
     * ���ؿͻ��˵�URI
     * 
     * @return string 
     */
    public function get_request_uri() {
        return $this->_request_uri;
    }

    /**
     * ���� IP
     * 
     * @return string IP
     */
    public function get_request_ip() {
        return $this->_request_ip;
    }

    /**
     * У�� �����Ƿ���Ч
     *
     * @access public
     * @param array $data  У������
     * @return bolean -true ��Ч
     */
    public function is_input_valid($data) {
        // ������Ϊ��
        if (empty($data)) {
            $this->_errmsg = 'The client request data is empty';
            return FALSE;
        }
        // �жϿͻ����Ƿ��д��ݼ��ܵı�ʶ
        if (strlen($data['is_enc']) < 1) {
            $this->_errmsg = 'The encrypt key is empty!';
            return FALSE;
        }
        // �жϿͻ����Ƿ��д��ݰ汾��
        if (empty($data['version'])) {
            $this->_errmsg = 'The client version is empty!';
            return FALSE;
        }
        // �жϿͻ����Ƿ��д��ݲ���ϵͳ����
        if (empty($data['os_type'])) {
            $this->_errmsg = 'The client type is empty!';
            return FALSE;
        }
        // �жϿͻ����Ƿ��д���ʱ��
        if (empty($data['ctime'])) {
            $this->_errmsg = 'The client time sign is empty!';
            return FALSE;
        }
        // �ж�����ʱ���Ƿ���� ( ������Ч�� )
        $data_limited = conf('DATA_LIMITED', 'config');
        if ($data_limited > 0) {
            $ctime = intval(substr($data['ctime'], 0, 10));
            if (time() - $ctime > $data_limited) {
                $this->_errmsg = 'The client data is overdue!';
                return FALSE;
            }
        }
        // �жϿͻ����Ƿ��д���APP NAME
        if (empty($data['app_name'])) {
            $this->_errmsg = 'The client app name is empty!';
            return FALSE;
        }
        $config = conf($data['app_name'], 'access');  // app ��Ȩ��Ϣ
        if (empty($config)) {
            // û�и� app name
            $this->_errmsg = 'The client app name is invalid!';
            return FALSE;
        }
        // �жϿͻ����Ƿ��д���ǩ��
        if (empty($data['sign_code'])) {
            $this->_errmsg = 'The client sign is empty!';
            return FALSE;
        }
        // TODO: �ж� sign ǩ���Ƿ�ʹ�ù�
        return TRUE;
    }

    /**
     * У��ͻ��˵�У�����Ƿ���ȷ
     * 
     * @access public 
     * @param  string  $param У������
     * @param string $sign_code У����
     * @param  string  $raw_data ԭʼ����
     * @return boolean  -true ��Ч
     */
    public function is_sign_valid($param, $sign_code, $raw_data = null) {
        if (empty($sign_code)) {
            $this->_errmsg = 'The encrypt sign is empty!';
            return FALSE;
        }
        // ֱ�Ӷ� param У��
        if (substr(md5('poco_' . json_encode($param) . '_app'), 5, -8) == $sign_code) {
            return TRUE;
        }
        // ʹ�� ԭʼ���� ƥ��У��
        if (empty($raw_data)) {
            $this->_errmsg = 'The sign cannot be checked!';
            return FALSE;
        }
        // ƥ��� param���� JSON
        $match = array();
        if (preg_match("/\"param\":({.*})[,}]{1}|\"param\":(\[.*\])[,}]{1}|\"param\":\"(.*?)\"[,}]{1}|\"param\":(\d+\.?\d*)[,}]{1}|\"param\":(null)[,}]{1}/", $raw_data, $match)) {
            // �ҵ�ƥ�������
            $param_json = '{}';
            foreach ($match as $k => $v) {
                if ($k > 0 && strlen($v) > 0) {
                    $param_json = $v;
                    break;
                }
            }
        }
        // ����base64����󾭹�JSON��ת�������
//        if ($data['is_enc'] == 1) {
//            $param_json = str_replace('\/', '/', $param_json);
//        }
        // param JSON ��������
        $server_sign_code = substr(md5('poco_' . $param_json . '_app'), 5, -8);
        // �ж�У�����Ƿ�һ��
        if ($sign_code == $server_sign_code) {
            return TRUE;
        }
        // ��¼����
        $error_data = array(
            'client_sign_code' => $sign_code,
            'server_sign_code' => $server_sign_code,
            'param_json' => $param_json,
            'input_str' => $raw_data
        );
        // д ǩ����֤��־
        yue_protocol_log::sign_error_log($error_data, $this->get_input_item('app_name'));
        $this->_errmsg = 'The encrypt sign not match!';
        return FALSE;
    }

    /**
     * token �Ƿ���Ч
     * 
     * @param int $user_id �û�ID
     * @param string $app_name Ӧ������
     * @param string $access_token ����
     * @return boolean -true ��Ч
     */
    public function is_token_valid($user_id, $app_name, $access_token) {
        if (empty($user_id) || empty($app_name) || empty($access_token)) {
            $this->_errmsg = 'The token is empty!';
            return FALSE;
        }
        if (!class_exists('POCO_TDG')) {
            exit('PROTOCOL ERROR: Without the framework of YUEUS included!');
        }
        $oauth_obj = new yue_protocol_oauth($app_name, $this->_debug);
        $token_info = $oauth_obj->get_access_info($user_id, $app_name);   // ��ȡ��Ȩ��Ϣ
        if (empty($token_info)) {
            $msg = $oauth_obj->get_errmsg();
            $this->_errmsg = empty($msg) ? 'Non-existent Token' : $msg;
            return FALSE;
        }
        // �ж�token�Ƿ���ȷ
        if (strcmp($token_info['access_token'], $access_token) != 0) {
            $this->_errmsg = 'The access token is invalid!';
            return FALSE;
        }
        // �ж��Ƿ����
        if ($token_info['expire_time'] <= time()) {
            $this->_errmsg = 'The access token is overdue!';
            return FALSE;
        }
        return TRUE;
    }

}
