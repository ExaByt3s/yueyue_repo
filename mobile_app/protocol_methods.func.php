<?php

/**
 * API接口 公共方法
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015/10/12 9:56
 */

if (!function_exists('interface_content_replace_pics')) {

    /**
     * 替换 图文详情中的图片
     *
     * @param string $contents 内容
     * @param int $size 尺寸
     * @return string
     */
    function interface_content_replace_pics($contents, $size = 640) {
        $size = intval($size);
        if ($size < 0 || empty($contents)) {
            return $contents;
        }
        $match = array();
        preg_match_all('/http[s]?:\/\/[^"]+/', $contents, $match);
        if (empty($match)) {
            return $contents;
        }
        foreach ($match[0] as $value) {
            if (strpos($value, '.poco.cn') === FALSE && strpos($value, '.yueus.com') === FALSE) {
                continue;
            }
            $ext = pathinfo($value, PATHINFO_EXTENSION);
            if (($offset = strpos($ext, '?')) > 0) {
                $ext = substr($ext, 0, $offset);
            }
            $ext = strtolower($ext);
            if (!in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                // 非图片文件
                continue;
            }
            // 尺寸替换
            $new = yueyue_resize_act_img_url($value, $size);
            $contents = str_replace($value, $new, $contents);
        }
        return $contents;
    }
}

if (!function_exists('interface_html_decode')) {
    /**
     * html实例 转编译
     *
     * @param string $str
     * @return string
     */
    function interface_html_decode($str) {
        $reg = array(
            '&lt;', '&gt;', '&quot;', '&nbsp;',
            '&#160;', '&#032', '&#124;', '&#036;',
            '&#33;', '&#39;', '&#092;', '&#60;', '&#62;',
        );
        $rpl = array(
            '<', '>', '"', ' ',
            ' ', ' ', '|', '$',
            '!', '\'', '\\', '<', '>',
        );
        $str = str_replace($reg, $rpl, $str);
        return trim($str);
    }
}

if (!function_exists('interface_ubb_encode')) {
    /**
     * html 转 ubb 格式
     *
     * @param string $str
     * @return string
     */
    function interface_ubb_encode($str) {
        if (empty($str)) {
            return FALSE;
        }
        $reg = array(
            '/\<a[^>]+href="mailto:(\S+)"[^>]*\>(.*?)<\/a\>/i', //    Email
            '/\<a[^>]+href=\"([^\"]+)\"[^>]*\>(.*?)<\/a\>/i',
            '/\<img[^>]+src=\"([^\"]+)\"[^>]*\>/i',
            '/\<div[^>]+align=\"([^\"]+)\"[^>]*\>(.*?)<\/div\>/i',
            '/\<([\/]?)u\>/i',
            '/\<([\/]?)em\>/i',
            '/\<([\/]?)strong\>/i',
            '/\<([\/]?)b[^(a|o|>|r)]*\>/i',
            '/\<([\/]?)i\>/i',
            '/&amp;/i',
            '/&lt;/i',
            '/&gt;/i',
            '/&nbsp;/i',
            '/\s+/',
            '/&#160;/', //    特殊符号
            '/\<p[^>]*\>/i',
            '/\<br[^>]*\>/i',
            '/\<[^>]*?\>/i',
            '/\&#\d+;/', // 特殊符号
        );
        $rpl = array(
            '[email=$1]$2[/email]',
            '[url=$1]$2[/url]',
            '[img]$1[/img]',
            '[align=$1]$2[/align]',
            '[$1u]',
            '[$1I]',
            '[$1b]',
            '[$1b]',
            '[$1i]',
            '&',
            '<',
            '>',
            ' ',
            ' ',
            ' ',
            "\r\n",
            "\r\n",
            '',
            '',
        );
        $str = preg_replace($reg, $rpl, $str);
        return trim($str);
    }
}

if (!function_exists('interface_grab_content_images')) {

    /**
     * 获取 内容中的图片
     *
     * @param string $contents 内容
     * @param int $size 修改图片尺寸
     * @param boolean $check_domain 图片域名验证
     * @return array
     */
    function interface_grab_content_images($contents, $size = 0, $check_domain = true) {
        $match = array();
        preg_match_all('/http[s]?:\/\/[^"]+/', $contents, $match);
        if (empty($match)) {
            return $contents;
        }
        $size = intval($size);
        $check_domain == ($check_domain === false) ? false : true;
        $images = array();
        foreach ($match[0] as $value) {
            if ($check_domain && strpos($value, '.poco.cn') === FALSE && strpos($value, '.yueus.com') === FALSE) {
                continue;
            }
            $ext = pathinfo($value, PATHINFO_EXTENSION);
            if (($offset = strpos($ext, '?')) > 0) {
                $ext = substr($ext, 0, $offset);
            }
            if (!in_array(strtolower($ext), array('jpg', 'jpeg', 'gif', 'png'))) {
                // 非图片文件
                continue;
            }
            // 尺寸替换
            if ($size > 0) {
                $value = yueyue_resize_act_img_url($value, $size);
            }
            $images[] = $value;
        }
        return $images;
    }
}
