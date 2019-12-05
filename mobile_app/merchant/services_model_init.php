<?php

/**
 * 服务商品编辑(初始化)接口
 * 
 * @author chenweibiao <chenwb@yueus.com>
 * @since 2015-8-25
 */
//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$goods_id = $client_data['data']['param']['goods_id'];   // 商品编号
$operate = $client_data['data']['param']['operate'];   // 操作类型  edit/add

$task_goods_obj = POCO::singleton('pai_mall_goods_class');
if ($operate == 'edit') {
    if (empty($goods_id)) {
        $options['data'] = array(); //
        return $cp->output($options);
    }
    $goods_result = $task_goods_obj->user_get_goods_info($goods_id, $user_id);
    if ($location_id == 'test') { //for debug
        $options['data'] = $goods_result;
        return $cp->output($options);
    }
    if ($goods_result['result'] != 1) {
        return $cp->output(array('data' => array()));
    }
    $goods = $goods_result['data'];

    $service = $guide = $menu = array();  // 服务内容
    $property_unit_config = pai_mall_load_config('property_unit');  // 获取属性对应单位

    $preview = array(); // 图片
    foreach ($goods['goods_data']['img'] as $value) {
        $img_url = $value['img_url'];
        $preview[] = array(
            'img' => yueyue_resize_act_img_url($img_url), // 原图
        );
    }
    $name = preg_replace('/&#\d+;/', '', $goods['default_data']['titles']['value']);
    $prices_data = $goods['prices_data_list'];
} else if ($operate == 'add') {
    $type_id = 31; // 模特
    $type_info = $task_goods_obj->user_show_goods_data($type_id);
    $goods = $type_info['data'];
    $name = '';
    $prices_data = $goods['prices_data']['d09bf41544a3365a46c9077ebb5e35c3']['child_data'];
} else {
    $options['data'] = array(); //
    return $cp->output($options);
}
if ($location_id == 'test2') { //for debug
    $options['data'] = $goods;
    return $cp->output($options);
}
// 风格分类 (三种分类)
$style_arr = array(
    '67c6a1e7ce56d3d6fa748ab6d9af3fd7' => 1, // 欧美
    '642e92efb79421734881b53e1e1b18b6' => 1, // 情绪
    'f457c545a9ded88f18ecee47145a72c0' => 1, // 糖水
    'c0c7c76d30bd3dcaefc96f40275bdc0a' => 1, // 古装
    '2838023a778dfaecdc212708f721b788' => 1, // 日韩
    '9a1158154dfa42caddbd0694a4e9bdc8' => 1, // 文艺复兴
    'a684eceee76fc522773286a895bc8436' => 1, // 内衣/比基尼
    'b53b3a3d6ab90ce0268229151c9bde11' => 1, // 甜美
    '1afa34a7f984eeabdbb0a7d494132ee5' => 1, // 真空
    '9f61408e3afb633e50cdf1b20de6f466' => 2, // 礼仪
    'd1f491a404d6854880943e5c3cd9ca25' => 2, // 车展
    '9b8619251a19057cff70779273e95aa6' => 2, // 走秀
    '72b32a1f754ba1c09b3695e0cb6cde7f' => 3, // 淘宝
);
// 风格
$style_data = $goods['system_data']['d9d4f495e875a2e075a1a4a6e1b9770f'];
$style_option = $standerd_option = array();
$style_name = $style_data['name'];
foreach ($style_data['child_data'] as $val) {
    $key = $val['key'];
    $type_id = $style_arr[$key];
    $style_option[] = array(
        'style_name' => $val['name'],
        'style_key' => $key,
        'style_type' => strval($type_id),
    );
    if ($key == $style_data['value']) {
        $style_name = $val['name'];
    }
}
// 起拍件数 ( for 淘宝 )
$prices_up = $goods['system_data']['16a5cdae362b8d27a1d8f8c7b78b4330'];
if (!empty($prices_up)) {
    $ups = array(
        'id' => $prices_up['key'],  // 16a5cdae362b8d27a1d8f8c7b78b4330
//        'id' => $prices_up['id'],
        'title' => $prices_up['name'],
        'value' => empty($prices_up['value']) ? '' : $prices_up['value'],
        'input_type' => '2',
        'hint' => '件数',
    );
    $prices_list['taobao'] = array($ups);
}
// 价格
$prices_rule = array(
    //约拍: 2 , 4 , 1天
    'yuepai' => array(76, 77, 87),
    //商业: 半天,一天
    'trade' => array(287, 87),
    //淘宝: 一件,半天,一天
    'taobao' => array(288, 287, 87),
);
foreach ($prices_data as $value) {
    $id = $value['id'];
    foreach ($prices_rule as $k => $v) {
        if (!in_array($id, $v)) {
            continue;
        }
        $prices_list[$k][] = array(
            'id' => $id,
            'title' => empty($value['name_val']) ? $value['name'] : $value['name_val'],
            'value' => empty($value['value']) ? '' : $value['value'],
            'input_type' => '2',
            'hint' => '价格',
        );
    }
}
// 定义的规格(三种)
$standerd_option = array(
    array(
        'style_type' => '1',
        'prices_list' => $prices_list['yuepai'], // 约拍
    ),
    array(
        'style_type' => '2',
        'prices_list' => isset($prices_list['trade']) ? $prices_list['trade'] : $prices_list['yuepai'], // 商业
    ),
    array(
        'style_type' => '3',
        'prices_list' => isset($prices_list['taobao']) ? $prices_list['taobao'] : $prices_list['yuepai'], // 淘宝
    ),
);
// 背景图
$img_data = $goods['goods_data']['img'];
$img_list = array();
foreach ($img_data as $val) {
    $img_list[] = array(
        'img_url' => $val['img_url'],
    );
}
// 商品详情
$contents = trim($goods['default_data']['content']['value']);
if (!empty($contents)) {
    $contents = content_replace_pics($contents, 640);  // 替换图片大小
    $contents = stripos($contents, '&lt;') < 10 ? html_entity_decode($contents, ENT_COMPAT | ENT_HTML401, 'GB2312') : $contents;
    $contents = '[color=#333333]' . ubb_encode($contents) . '[/color]';
}
$limit_num = intval($goods['system_data']['66f041e16a60928b05a7e228a89c3799']['value']);  // 限制拍摄人数
$goods_info = array(
    'goods_id' => empty($goods_id) ? '0' : $goods_id,
    'goods_name' => $name,
    'style' => array(
        'style_name' => $style_name,
        'style_key' => empty($style_data['value']) ? '' : $style_data['value'],
        'style_type' => strval($style_arr[$style_data['value']]),
    ),
    'style_option' => $style_option,
    'standerd_title' => '规格&价格(元)',
    'standerd_option' => $standerd_option,
    'pic_arr' => $img_list,
    'limit_num' => array(
        'title' => $limit_num > 0 ? $limit_num . '人' : '请选择',
        'value' => $limit_num,
    ),
    'limit_option' => array(
        array(
            'title' => '1人',
            'value' => '1',
        ),
        array(
            'title' => '2人',
            'value' => '2',
        ),
        array(
            'title' => '3人',
            'value' => '3',
        ),
        array(
            'title' => '4人',
            'value' => '4',
        ),
        array(
            'title' => '5人',
            'value' => '5',
        )
    ),
    'detail_edit' => array(
        'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
        'post_pic_wifi' => 'http://sendmedia-w-wifi.yueus.com:8079/upload.cgi',
        'pic_size' => 640,
        'contents' => $contents,
    ),
);

// 上传配置
$upload_config = array(
    'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'post_pic_wifi' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
    'pic_size' => 640,
    'pic_num' => 3,
);

/**
 * 替换 详情中的图片
 * 
 * @param string $contents 内容
 * @param int $size 尺寸
 * @return string
 */
function content_replace_pics($contents, $size = 640) {
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

$options['data'] = array_merge($goods_info, $upload_config); //
return $cp->output($options);
