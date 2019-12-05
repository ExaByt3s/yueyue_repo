<?php

/**
 * yue协议 公共方法
 * 
 * @author Tandl
 * @editor willike <chenwb@yueus.com>
 * @since 2015-7-30
 */
defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * 获取配置
 * 
 * @param string $item 配置项
 * @param string $name 配置文件名
 * @return mixed
 */
function conf($item = null, $name = 'config') {
    $name = empty($name) ? 'config' : strtolower($name);
    $file = YUE_PROTOCOL_ROOT . 'config/' . (strpos($name, '.php') ? $name : $name . '.php');
    if (!file_exists($file)) {
        return FALSE;
    }
    $config = isset($GLOBALS[$name]) ? $GLOBALS[$name] : array();
    if(empty($config)){
	    $config = include($file);
        $GLOBALS[$name] = $config;
    }
    if (empty($item)) {
        return $config;
    }
    return isset($config[$item]) ? $config[$item] : null;
}

if (!function_exists('get_client_ip')) {

    /**
     * 获取 客户端IP
     * 
     * @return string
     */
    function get_client_ip() {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            return getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            return getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CDN_SRC_IP') && strcasecmp(getenv('HTTP_CDN_SRC_IP'), 'unknown')) {
            return getenv('HTTP_CDN_SRC_IP');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            return getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return '';
        }
    }

}

/**
 * 编码比对
 * 
 * @param string $charset1
 * @param string $charset2
 * @return int
 */
function charset_compare($charset1, $charset2) {
    $charset1 = str_replace(array(' ', '-'), '', strtolower(trim($charset1)));
    $charset2 = str_replace(array(' ', '-'), '', strtolower(trim($charset2)));
    return strcmp($charset1, $charset2);
}

/**
 * 获取一个Hash编码
 *
 * @param string $str 字符串
 * @return string
 */
function protocol_api_hash_code($str) {
    if (empty($str)) {
        return '';
    }
    $mdv = md5($str);
    $mdv1 = substr($mdv, 0, 16);
    $mdv2 = substr($mdv, 16, 16);
    $crc1 = abs(crc32($mdv1));
    $crc2 = abs(crc32($mdv2));
    return bcmul($crc1, $crc2);
}

/**
 * 生成随机字符串
 *
 * @desc 字符串包含且只包含大小写英文字母和数字
 * @param int $length 字符串的长度（必须大于0）
 * @return string
 */
function protocol_api_get_rand_string($length = 6) {
    $str = '';
    $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($str_pol) - 1;
    $length = ($length > 0) ? $length : 6;
    for ($i = 0; $i < $length; $i++) {
        $str .= $str_pol[mt_rand(0, $max)];
    }
    return $str;
}

/**
 * 长整型转字符串（32位int）
 *
 * @param array   $v 长整型数组
 * @param boolean $w
 * @return string
 */
function protocol_api_long2str($v, $w) {
    $len = count($v);
    $n = ($len - 1) << 2;
    if ($w) {
        $m = $v[$len - 1];
        if (($m < $n - 3) || ($m > $n)) {
            return false;
        }
        $n = $m;
    }
    $s = array();
    for ($i = 0; $i < $len; $i++) {
        $s[$i] = pack("V", $v[$i]);
    }
    if ($w) {
        return substr(join('', $s), 0, $n);
    } else {
        return join('', $s);
    }
}

/**
 * 字符串转长整型
 *
 * @param string  $s 字符
 * @param boolean $w
 * @return array
 */
function protocol_api_str2long($s, $w) {
    $v = unpack("V*", $s . str_repeat("\0", (4 - strlen($s) % 4) & 3));
    $v = array_values($v);
    if ($w) {
        $v[count($v)] = strlen($s);
    }
    return $v;
}

/**
 * 格式化32位整型
 *
 * @param int $n 整型
 * @return int
 */
function protocol_api_int32($n) {
    while ($n >= 2147483648) {
        $n -= 4294967296;
    }
    while ($n <= -2147483649) {
        $n += 4294967296;
    }
    return (int) $n;
}

/**
 * 字符串加密
 *
 * @param string $str         待加密的字符串
 * @param string $encrypt_key 密匙
 * @return string
 */
function protocol_api_encrypt($str, $encrypt_key) {
    $key = $encrypt_key;
    if (empty($str) || empty($key)) {
        return '';
    }
    $v = protocol_api_str2long($str, true);
    $k = protocol_api_str2long($key, false);
    if (count($k) < 4) {
        for ($i = count($k); $i < 4; $i++) {
            $k[$i] = 0;
        }
    }
    $n = count($v) - 1;

    $z = $v[$n];
    $y = $v[0];
    $delta = 0x9E3779B9;
    $q = floor(6 + 52 / ($n + 1));
    $sum = 0;
    while (0 < $q--) {
        $sum = protocol_api_int32($sum + $delta);
        $e = $sum >> 2 & 3;
        for ($p = 0; $p < $n; $p++) {
            $y = $v[$p + 1];
            $mx = protocol_api_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ protocol_api_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $z = $v[$p] = protocol_api_int32($v[$p] + $mx);
        }
        $y = $v[0];
        $mx = protocol_api_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ protocol_api_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $z = $v[$n] = protocol_api_int32($v[$n] + $mx);
    }
    return protocol_api_long2str($v, false);
}

/**
 * 字符串解密
 *
 * @param  string  $str          待解密的字符串
 * @param  string  $encrypt_key  密匙
 * @return string
 */
function protocol_api_decrypt($str, $encrypt_key) {
    $key = $encrypt_key;
    if (empty($str) || empty($key)) {
        return '';
    }
    $v = protocol_api_str2long($str, false);
    $k = protocol_api_str2long($key, false);
    if (count($k) < 4) {
        for ($i = count($k); $i < 4; $i++) {
            $k[$i] = 0;
        }
    }
    $n = count($v) - 1;

    $z = $v[$n];
    $y = $v[0];
    $delta = 0x9E3779B9;
    $q = floor(6 + 52 / ($n + 1));
    $sum = protocol_api_int32($q * $delta);
    while ($sum != 0) {
        $e = $sum >> 2 & 3;
        for ($p = $n; $p > 0; $p--) {
            $z = $v[$p - 1];
            $mx = protocol_api_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ protocol_api_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y = $v[$p] = protocol_api_int32($v[$p] - $mx);
        }
        $z = $v[$n];
        $mx = protocol_api_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ protocol_api_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $y = $v[0] = protocol_api_int32($v[0] - $mx);
        $sum = protocol_api_int32($sum - $delta);
    }
    return protocol_api_long2str($v, true);
}

/**
 * 编码转换
 * 
 * @param mixed $data
 * @param string $to_charset 转换编码
 * @param string $fr_charset 数据来源编码
 * @return mixed
 */
function protocol_api_charset_conv($data, $to_charset = 'UTF-8', $fr_charset = 'GBK') {
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            $data[$k] = protocol_api_charset_conv($v, $to_charset, $fr_charset);
        }
        return $data;
    } else {
        return is_string($data) ? mb_convert_encoding($data, $to_charset, $fr_charset) : $data;
    }
}

/**
 * 图片CDN处理
 * 
 * @param  string or array  $data  参数数组
 * @return mixed
 */
function protocol_api_handle_image_cdn($data) {
    if (empty($data)) {
        return FALSE;
    }
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $data[$k] = protocol_api_handle_image_cdn($v);
        }
        return $data;
    } else {
        return is_string($data) ? POCO::execute('common.content_output_cdn_parser', $data) : $data;
    }
}

/**
 * 过滤HTML字符
 * 
 * @param mixed $param 待过滤的字符，compat: 仅双引号、quotes: 单双引号、noquotes: 不编译引号（默认）
 * @param string $flag 对待引号的方式
 * @param boolean $double_encode  是否编码已存在的HTML实体，默认：false
 * @return mixed
 */
function protocol_api_input_html_filter($param, $flag = 'noquotes', $double_encode = false) {
    $flags = array('compat' => ENT_COMPAT, 'quotes' => ENT_QUOTES, 'noquotes' => ENT_NOQUOTES);
    $myflag = isset($flags[$flag]) ? $flags[$flag] : ENT_NOQUOTES;
    if (is_array($param)) {
        foreach ($param as $k => $v) {
            $param[$k] = protocol_api_input_html_filter($v, $flag, $double_encode);
        }
    } else {
        $param = htmlspecialchars($param, $myflag, 'UTF-8', $double_encode);
    }
    return $param;
}
