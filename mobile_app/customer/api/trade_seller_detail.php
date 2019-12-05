<?php

/**
 * 商品 详细解释页
 * 
 * @since 2015-6-25
 * @author chenweibiao <chenwb@yueus.com>
 */
include_once("../../protocol_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input(array('be_check_token' => false));

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$seller_user_id = $client_data['data']['param']['seller_user_id'];  // 商家ID

$api_obj = POCO::singleton('pai_mall_api_class');
$user_result = $api_obj->api_get_seller_info($seller_user_id, 2);  // 获取用户信息
$introduce = $user_result['seller_data']['profile'][0]['introduce'];
if ($location_id == 'test') {
    $options['data'] = $introduce;
    $cp->output($options);
    exit;
}
if (empty($introduce)) {
    $introduce = '';  // 不出现 null
}
$contents = trim($introduce);
$contents = stripos($contents, '&lt;') < 10 ? html_entity_decode($contents, ENT_COMPAT | ENT_HTML401, 'GB2312') : $contents;
if ($location_id == 'test2') {
    $options['data'] = $contents;
    $cp->output($options);
    exit;
}
$options['data'] = array('introduce' => '[color=#333333]' . ubb_encode($contents) . '[/color]');
$cp->output($options);

/**
 * html 转 ubb 格式
 * 
 * @param string $str
 * @return string
 */
function ubb_encode($str) {
    if (empty($str)) {
        return FALSE;
    }
    $reg = array(
        '/\<a[^>]+href="mailto:(\S+)"[^>]*\>(.*?)<\/a\>/i', // Email
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
        '/&#160;/', // 特殊符号
        '/\<p[^>]*\>/i',
        '/\<br[^>]*\>/i',
        '/\<[^>]*?\>/i',
        '/\&#\d+;/',   // 特殊符号
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