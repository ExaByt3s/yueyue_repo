<?php

/**
 * 公共方法类
 *
 * @author willike
 * @since 2015-9-21
 */
defined('PROTOCOL_MASTER_ROOT') or die('----------func error----------');

/**
 * 是否 POST请求
 *
 * @return boolean (-true 是)
 */
function pm_is_post() {
    $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    return $method == 'POST' ? TRUE : FALSE;
}

/**
 * 获取 登陆用户ID
 *
 * @return int
 */
function pm_get_user_id() {
    $user_id = isset($GLOBALS['_user_id']) ? $GLOBALS['_user_id'] : 0;
    if (empty($user_id)) {
        $login_info = pm_get_login_status();
        if (empty($login_info)) {
            return 0;
        }
        $user_id = $login_info['uid'];
        $GLOBALS['_user_id'] = $user_id;
    }
    return $user_id;
}

/**
 * 是否 登陆
 *
 * @return mixed
 */
function pm_is_login() {
    $login_res = pm_get_login_status();
    if (empty($login_res)) {
        return false;
    }
    $user_id = $login_res['uid'];
    if ($user_id < 1) {
        return false;
    }
    return $user_id;
}

/**
 * 是否是有权限登陆
 *
 * @param int $user_id
 * @return boolean -true 是
 */
function pm_is_login_access($user_id = 0) {
    if (empty($user_id)) {
        $user_id = pm_get_user_id();
        if (empty($user_id)) {
            return FALSE;
        }
    }
    return in_array($user_id, pm_conf('LOGIN_ACCESS')) ? true : false;
}

/**
 * 是否是管理员
 *
 * @param int $user_id
 * @return boolean -true 是
 */
function pm_is_admin($user_id = 0) {
    if (empty($user_id)) {
        $user_id = pm_get_user_id();
        if (empty($user_id)) {
            return FALSE;
        }
    }
    return in_array($user_id, pm_conf('MASTER_ADMIN')) ? true : false;
}

/**
 * 获取 配置内容
 *
 * @param string $str 配置名称
 * @return mixed 配置值
 */
function pm_conf($str) {
    $conf = isset($GLOBALS['__p_m_conf']) ? $GLOBALS['__p_m_conf'] : array();
    if (empty($conf)) {
        $conf = array();
        $conf_file = PROTOCOL_MASTER_ROOT . 'conf/conf.inc.php';  // 配置
        if (file_exists($conf_file)) {
            $conf = include($conf_file);
        }
        $GLOBALS['__p_m_conf'] = $conf;
    }
    return isset($conf[$str]) ? $conf[$str] : null;
}

/**
 * 重定向
 *
 * @param string $url
 * @return void
 */
function pm_redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * json 返回
 *
 * @param mixed $data 返回的数据
 * @param string $info 提示信息
 * @param string $status 状态
 * @return void
 */
function pm_json_return($data, $info = '', $status = null) {
    $return_arr = (empty($info) && is_null($status)) ? $data : array(
        'data' => $data,
        'msg' => $info,
        'code' => $status
    );
    header('Content-Type:application/json; charset=utf-8');  // 定义返回格式
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');  // 不缓存
    header('Expires: 0');
    exit(json_encode($return_arr));
//  exit(preg_replace('#\":\s*(null|false)#iUs', '":""', json_encode($return_arr)));
}

/**
 * 简易模板输出
 *
 * @param string $tpl 模板名称
 * @param array $param 参数
 * @param boolean $layout 是否引入头文件
 * @return void
 */
function pm_render($tpl, $param = array(), $layout = TRUE) {
    if (headers_sent()) {  // 头部已发送, 则直接 退出
        exit('ERROR: An unknown error occurred! pls contact @willike .');
    }
    header('Content-type: text/html; charset=utf-8');  // 发送头,设置编码
    $tpl = empty($tpl) ? 'index' : $tpl;
    $tpl_file = PROTOCOL_MASTER_ROOT . 'view/' . $tpl . '.tpl.html';
    if (!file_exists($tpl_file)) {
        if (!pm_is_login()) {
            $tpl_file = PROTOCOL_MASTER_ROOT . 'view/error.tpl.html'; // 错误页面
            ob_start();
            require($tpl_file);
            ob_end_flush();
            exit;
        } else {
            // 已登录, 则使用layout页面
            $tpl = 'info';
            $tpl_file = PROTOCOL_MASTER_ROOT . 'view/' . $tpl . '.tpl.html';
        }
    }
    if (is_array($param) && !empty($param)) {
        extract($param, EXTR_OVERWRITE);  // 如果是数组,则导入到当前的符号表中
    }
    $GLOBALS['__CSS__'] = '';
    $main_css = PROTOCOL_MASTER_ROOT . 'script/master.main.css';
    if (file_exists($main_css)) {
        $GLOBALS['__CSS__'] .= '<link href="/protocol/master/script/master.main.css" rel="stylesheet">';
    } else {
        $main_css = PROTOCOL_MASTER_ROOT . 'script/min/master.main.min.css';
        if (file_exists($main_css)) {
            $GLOBALS['__CSS__'] .= '<link href="/protocol/master/script/min/master.main.min.css" rel="stylesheet">';
        }
    }
    $css_file = PROTOCOL_MASTER_ROOT . 'script/master.' . $tpl . '.css';
    if (file_exists($css_file)) {
        $GLOBALS['__CSS__'] .= '<link href="/protocol/master/script/master.' . $tpl . '.css" rel="stylesheet">';
    } else {
        $css_file = PROTOCOL_MASTER_ROOT . 'script/min/master.' . $tpl . '.min.css';
        if (file_exists($css_file)) {
            $GLOBALS['__CSS__'] .= '<link href="/protocol/master/script/min/master.' . $tpl . '.min.css" rel="stylesheet">';
        }
    }
    $GLOBALS['__JS__'] = '';
    $main_js = PROTOCOL_MASTER_ROOT . 'script/master.main.js';
    if (file_exists($main_js)) {
        $GLOBALS['__JS__'] .= '<script src="/protocol/master/script/master.main.js"></script>';
    } else {
        $main_js = PROTOCOL_MASTER_ROOT . 'script/min/master.main.min.js';
        if (file_exists($main_js)) {
            $GLOBALS['__JS__'] .= '<script src="/protocol/master/script/min/master.main.min.js"></script>';
        }
    }
    $js_file = PROTOCOL_MASTER_ROOT . 'script/master.' . $tpl . '.js';
    if (file_exists($js_file)) {
        $GLOBALS['__JS__'] .= '<script src="/protocol/master/script/master.' . $tpl . '.js"></script>';
    } else {
        $js_file = PROTOCOL_MASTER_ROOT . 'script/min/master.' . $tpl . '.min.js';
        if (file_exists($js_file)) {
            $GLOBALS['__JS__'] .= '<script src="/protocol/master/script/min/master.' . $tpl . '.min.js"></script>';
        }
    }
    $GLOBALS['__CONTENT__'] = '';
    if ($layout === FALSE) {  // 不使用布局
        ob_start();
        require($tpl_file);
        ob_end_flush();
        exit;
    }
    ob_start();
    ob_implicit_flush(FALSE);  // 打开/关闭绝对刷送
    require($tpl_file);  // 引入模板
    $GLOBALS['__CONTENT__'] = ob_get_clean();  // 获取并清空缓存

    $layout_tpl = PROTOCOL_MASTER_ROOT . 'view/layout.tpl.html';  // 布局
    ob_start();
    require($layout_tpl);
    ob_end_flush();
    exit;
}

/**
 * 检查 路径
 *
 * @param string $path
 * @return string
 */
function pm_check_yue_path($path) {
    $path = strpos($path, '.php') ? $path : $path . '.php';
    if (strpos($path, 'mall/') === FALSE && strpos($path, 'ssl/') === FALSE) {
        $path = 'mobile_app/' . $path;
    }
    $file = dirname(dirname(PROTOCOL_MASTER_ROOT)) . '/' . $path;
    if (!file_exists($file)) {
        return false;
    }
    return true;
}

/**
 * 通过curl 方式获取数据
 *
 * @param string $path
 * @param array $params
 * @return string
 */
function pm_yue_curl($path, $params = array()) {
    $path = strpos($path, '.php') ? $path : $path . '.php';
    if (strpos($path, 'mall/') === FALSE && strpos($path, 'ssl/') === FALSE) {
        $path = 'mobile_app/' . $path;
    }
    $file = dirname(dirname(PROTOCOL_MASTER_ROOT)) . '/' . $path;
    if (!file_exists($file)) {
        return '';
    }
    $base_url = 'http://yp.yueus.com/';
    $url = $base_url . $path;
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '';
    }
    // 接口版本
    $version = isset($params['version']) ? $params['version'] : pm_conf('DEFAULT_API_VERSION');
    unset($params['version']);
    $post_params = array();
    foreach ($params as $key => $value) {
        // 处理内容中的json
        if (strpos($value, '{"') === 0) {
            $slen = strlen($value);
            $kpos = strpos($value, '}');
            if ($kpos == $slen || $kpos == ($slen - 1)) {
                $value = json_decode($value, true);
            }
        }
        $post_params[$key] = $value;   // 组装参数
    }
    // 传递参数
    $post_data = array(
        'version' => $version,
        'os_type' => 'api',
        'ctime' => time(),
        'app_name' => 'poco_yuepai_api',
        'sign_code' => substr(md5('poco_' . json_encode($post_params) . '_app'), 5, -8),
        'is_enc' => 0,
        'param' => $post_params,
    );
//    $cov_data = mb_convert_encoding(json_encode($post_data),'UTF-8','GBK');  // 数据转编码
    $cov_data = json_encode($post_data);
    $res = pm_curl($url, array('req' => $cov_data), 'post');  // 直接输出结果
    return trim($res);
}

/**
 * 通过 curl 获取 内容
 *
 * @param string $link
 * @param array $params 参数
 * @param string $method 数据传递方法
 * @return mixed
 */
function pm_curl($link, $params = array(), $method = 'get') {
    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        return FALSE;
    }
    if (!extension_loaded('curl')) {
        // 未开启 curl 扩展
        exit('ERROR: Unable to load curl extension!');
    }
    if (strtolower($method) === 'get' && $params) {
        $link .= (strpos($link, '?') === FALSE ? '?' : '&') . http_build_query($params);
    }
    $curl = curl_init();  // 初始化
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  // 禁用后cURL将终止从服务端进行验证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (strtolower($method) === 'post') {
        curl_setopt($curl, CURLOPT_POST, TRUE);   // 发送一个常规的POST请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);  // 全部数据使用HTTP协议中的"POST"操作来发送
    } else {
        curl_setopt($curl, CURLOPT_FORBID_REUSE, TRUE);  // 在完成交互以后强迫断开连接，不能重用
    }
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  // 在HTTP请求中包含一个"User-Agent: "头的字符串
    }
    curl_setopt($curl, CURLOPT_HEADER, FALSE);  // 输出报头
    curl_setopt($curl, CURLOPT_NOBODY, FALSE);  // 启用时将不对HTML中的BODY部分进行输出
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);  // 在发起连接前等待的时间
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);  // 设置cURL允许执行的最长秒数
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);  // 0屏幕输出1不输出
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 允许自动跳转
    $contents = curl_exec($curl);  // 抓取的数据
    curl_close($curl);  // 关闭
    return $contents;
}

/**
 * 设定 用户登陆 状态
 *
 * @param int $uid
 * @param string $nick_name
 * @return boolean
 */
function pm_set_login_status($uid, $nick_name) {
    $uid = intval($uid);
    if ($uid < 1 || empty($nick_name)) {
        return FALSE;
    }
    $_SESSION['_uid'] = $uid;
    $_SESSION['_nick'] = $nick_name;
    $key = pm_conf('DATA_AUTH_KEY'); // 系统密钥
    $expire = time() + intval(pm_conf('COOKIE_EXPIRE'));  // cookie有效期
    $path = pm_conf('COOKIE_PATH');
    $info = $uid . ',' . $nick_name . ',' . substr(time(), 3);
    $value = pm_xor_encrypt($info, $key); // 值加密
    $encrypt = substr(md5($info . $key), 7, 8);  // 验证的密钥
    setcookie('_u', $value, $expire, $path);
    setcookie('_uK', $encrypt, $expire, $path);
    if (class_exists('yue_protocol_cache')) {  // 设置单点登录
        $key = 'master_' . $uid;
        $life_time = 12 * 60 * 60;  // 12 小时
        yue_protocol_cache::set_cache($key, md5($value), array('life_time' => $life_time));
    }
    return TRUE;
}

/**
 * 获取 用户登陆信息
 *
 * @return array | false
 */
function pm_get_login_status() {
    $uid = isset($_SESSION['_uid']) ? intval($_SESSION['_uid']) : 0;
    $nick_name = isset($_SESSION['_nick']) ? trim($_SESSION['_nick']) : '';
    if ($uid < 1 || empty($nick_name)) {
        // session 中无此值,从cookie中寻找
        $value = filter_input(INPUT_COOKIE, '_u');
        $encrypt = filter_input(INPUT_COOKIE, '_uK');
        if (empty($value) || empty($encrypt)) {
            return FALSE;
        }
        $key = pm_conf('DATA_AUTH_KEY'); // 系统密钥
        $info = pm_xor_decrypt($value, $key);
        if ($encrypt != substr(md5($info . $key), 7, 8)) {
            return FALSE;
        }
        $user_info = explode(',', $info);
        $uid = intval($user_info[0]);
        $nick_name = $user_info[1];
        $time = isset($user_info[2]) ? $user_info[2] : '';
        if ($uid < 1 || empty($nick_name) || empty($time)) {
            return FALSE;
        }
        if (substr(time(), 3) > (intval($time) + pm_conf('COOKIE_EXPIRE'))) {
            // cookie 时间过期
            return FALSE;
        }
        // 设定 session
        $_SESSION['_uid'] = $uid;
        $_SESSION['_nick'] = $nick_name;
    }
    return array(
        'uid' => $uid,
        'nick' => $nick_name,
    );
}

/**
 * 删除 用户登陆信息
 *
 * @return boolean
 */
function pm_drop_login_status() {
    // 删除 session
    if (isset($_SESSION['_uid'])) {
        unset($_SESSION['_uid']);
    }
    if (isset($_SESSION['_nick'])) {
        unset($_SESSION['_nick']);
    }
    $user_id = pm_get_user_id();
    if ($user_id > 0 && class_exists('yue_protocol_cache')) {
        $key = 'master_' . $user_id;
        yue_protocol_cache::delete_cache($key);
    }
    // 删除 cookie
    $path = pm_conf('COOKIE_PATH');
    setcookie('_u', null, time() - 360000, $path);
    setcookie('_uK', null, time() - 360000, $path);
    return TRUE;
}

/**
 * 系统加密方法 ( XOR 方法)
 *
 * @param string $string 要加密的字符串
 * @param string $key 加密密钥
 * @return string
 */
function pm_xor_encrypt($string, $key = '') {
    $string = base64_encode(trim($string));
    $key = md5(empty($key) ? '_@willike#):say^buyue!!(' : $key);
    $str_len = strlen($string);
    $key_len = strlen($key);
    for ($i = 0; $i < $str_len; $i++) {
        for ($j = 0; $j < $key_len; $j++) {
            $string[$i] = $string[$i] ^ $key[$j];
        }
    }
    return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
//	return base64_encode($string);
}

/**
 * 系统解密方法 ( XOR 方法)
 *
 * @param  string $string 要解密的字符串 （必须是 xor_encrypt 方法加密的字符串）
 * @param  string $key 加密密钥
 * @return string
 */
function pm_xor_decrypt($string, $key = '') {
    $string = base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
//	$string = base64_decode(trim($string));
    if (empty($string)) {
        return FALSE;
    }
    $key = md5(empty($key) ? '_@willike#):say^buyue!!(' : $key);
    $str_len = strlen($string);
    $key_len = strlen($key);
    for ($i = 0; $i < $str_len; $i++) {
        for ($j = 0; $j < $key_len; $j++) {
            $string[$i] = $key[$j] ^ $string[$i];
        }
    }
    return base64_decode($string);
}

/**
 * 获取客户端 IP
 *
 * @return string
 */
function pm_get_client_ip() {
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        return getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        return getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_CDN_SRC_IP') && strcasecmp(getenv('HTTP_CDN_SRC_IP'), 'unknown')) {
        return getenv('HTTP_CDN_SRC_IP');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        return getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        return filter_input(INPUT_SERVER, 'REMOTE_ADDR');
    } else {
        return '';
    }
}

/**
 * 上传回调
 *
 * @param array $result
 * @param string $callback
 * @return void
 */
function pm_iframe_upload_callback($result, $callback = 'act.upPic') {
    $callback = 'parent.' . $callback . '(' . json_encode($result) . ')';
    $html = '<!DOCTYPE HTML><html><head><title>Upload</title><meta charset="utf-8"></head><body><script>' . $callback . '</script></body></html>';
    if (!headers_sent()) {
        // 发送http头
        header('Content-Type:text/html;charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
    exit($html);
}

/**
 * 获取 地理位置名称
 *
 * @param string $location_id
 * @return string
 */
function pm_get_location_name($location_id) {
    if (empty($location_id) || strlen($location_id) < 6) {
        return '';
    }
    $conf_file = PROTOCOL_MASTER_ROOT . 'conf/location_id.json';  // 地理位置配置
    if (!file_exists($conf_file)) {
        return '';
    }
    $location_arr = isset($GLOBALS['master_location_arr']) ? $GLOBALS['master_location_arr'] : array();
    if (empty($location_arr)) {
        $location_json = file_get_contents($conf_file);
        $location_arr = json_decode($location_json, true);
    }
    return isset($location_arr[$location_id]) ? $location_arr[$location_id] : '';
}

/**
 * 获取 API 默认 地理位置
 *
 * @return array
 */
function pm_yue_location_list() {
    $location_arr = array(
        '101029001' => '广州',
        '101029002' => '深圳',
        '101029003' => '佛山',
        '101001001' => '北京',
        '101003001' => '上海',
        '101003002' => '杭州',
        '101022001' => '成都',
        '101004001' => '重庆',
        '101015001' => '西安',
    );
    return $location_arr;
}

/*
 * 获取 分类列表
 *
 * @return array
 */
function pm_yue_type_list() {
    $type_arr = array(
        0 => '全部',
        3 => '化妆',
        31 => '模特',
        40 => '摄影师',
        12 => '影棚',
        5 => '培训',
        41 => '美食',
        43 => '约有趣',
        42 => '活动'
    );
    return $type_arr;
}

/**
 * 写 访问日志
 *
 * @return boolean
 */
function pm_write_visit_log() {
    $request_uri = filter_input(INPUT_SERVER, 'REQUEST_URI');  // 请求URL
    if (empty($request_uri)) {  // 无请求连接
        return FALSE;
    }
	if(strpos($request_uri, 'c=index') !== FALSE){  // 首页不记录
		return FALSE;
	}
    $user_id = pm_get_user_id();
    if (empty($user_id)) {  // 无用户
        return FALSE;
    }
    if(pm_is_admin()){  // 管理员不记录
        return false;
    }
    $user_agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');  // 用户代理
    $referer = filter_input(INPUT_SERVER, 'HTTP_REFERER');  // 来源
    if (!empty($referer)) {
        $base_url = filter_input(INPUT_SERVER, 'REQUEST_SCHEME') . '://' . filter_input(INPUT_SERVER, 'HTTP_HOST');
        $referer = str_replace($base_url, '', $referer);
    }
    $log_file = PROTOCOL_MASTER_ROOT . 'logs/' . $user_id . '.log';  // 配置
    $data_arr = array(
        'user_id' => $user_id,
        'request_uri' => $request_uri,
        'user_agent' => $user_agent,
        'ip' => pm_get_client_ip(),
        'referer' => $referer,
        'method' => filter_input(INPUT_SERVER, 'REQUEST_METHOD'),
    );
    $log_data = date('Y-m-d H:i:s') . '|' . serialize($data_arr) . PHP_EOL;
    return file_put_contents($log_file, $log_data, FILE_APPEND);
}

/**
 * 是否单点登录 ( 需缓存支持 )
 *
 * @return boolean -true 是单点登录
 */
function pm_check_single_login() {
    if (!class_exists('yue_protocol_cache')) {
        // 没有缓存, 则不验证
        return true;
    }
    if (pm_conf('SINGLE_LOGIN') != true) {
        // 设置不验证 单点登录
        return true;
    }
    $key = 'master_' . pm_get_user_id();
    $cache_login_user = yue_protocol_cache::get_cache($key);
    $cookie_login_user = filter_input(INPUT_COOKIE, '_u');
    if (empty($cache_login_user)) {
        if (empty($cookie_login_user)) {
            return false;
        }
        $life_time = 12 * 60 * 60;  // 12 小时
        yue_protocol_cache::set_cache($key, md5($cookie_login_user), array('life_time' => $life_time));
        return true;
    }
    if (strcmp($cache_login_user, md5($cookie_login_user)) == 0) {
        return true;
    }
    return false;
}

/**
 * 系统客户端类型
 *
 * @param string $agent
 * @return array
 */
function pm_client_agent($agent) {
    if (empty($agent)) {
        return array(
            'os' => 'empty',  // 系统
            'browser' => 'empty', // 浏览器
        );
    }
    // 系统
    $os = $browser = 'unknown';
    preg_match('/[\(].*?[\)]/', $agent, $match);
    if (!empty($match)) {
        $os_ = $match[0];
        $os_match = array();
        if (stripos($os_, 'java') !== false) {
            $os = 'android' . $match[0];
            $browser = 'WebView';
        } else if (stripos($os_, 'mac') !== false) {
            preg_match('/\((.*?);/i', $os_, $os_match);
            $os = empty($os_match) ? 'iOS' : $os_match[1]; // 系统
            preg_match('/OS\s(\d_\d)/i', $os_, $ios_match);
            $os = empty($ios_match) ? $os : $os . '(iOS ' . str_replace('_', '.', $ios_match[1]) . ')';
        } else if (stripos($os_, 'win') !== false) {
            preg_match('/\((.*?);/i', $os_, $os_match);
            $os = empty($os_match) ? 'win' : $os_match[1];  // 系统
            $os = str_replace('NT ', '', $os);
        }
    }
    // 浏览器
    if (empty($browser) || $browser == 'unknown') {
        $browser_reg = array(
            '/(Chrome)[\/]([\w.]+)/i',
            '/(opera)(?:.*version)?[ \/]([\w.]+)/i',
            '/(msie) ([\w.]+)/i',
            '/(Firefox)[\/]([\w.]+)/i',
            '/(AppleWebKit)[\/]([\w.]+)/i',
            '/(webkit)[\/]([\w.]+)/i',
            '/(Safari)[\/]([\w.]+)/i',
            '/(mozilla)(?:.*? rv:([\w.]+))?/i',
        );
        foreach ($browser_reg as $reg) {
            preg_match($reg, $agent, $browser_match);
            if (!empty($browser_match)) {
                $browser = $browser_match[0];
                break;
            }
        }
    }
    return array(
        'os' => $os,  // 系统
        'browser' => $browser, // 浏览器
    );
}


/**
 * 时间处理
 *
 * @param int $time  ( 例如: 1397574375 )
 * @return string
 */
function pm_get_time_diff($time) {
	$min = 60;
	$hour = $min * 60;
	$day = $hour * 24;
	$diff = time() - $time;
	switch ($diff) {
		case ($diff < 5):
			$str = '刚刚';
			break;
		case ($diff < $min):
			$str = $diff . ' 秒前';
			break;
		case ($diff < $hour):
			$str = floor($diff / $min) . ' 分钟前';
			break;
		case ($diff < $day):
			$str = floor($diff / $hour) . ' 小时前';
			break;
		case ($diff >= $day):
			$str = date('m-d H:i', $time);
			break;
		default:
			$str = '公元前';
	}
	return $str;
}

