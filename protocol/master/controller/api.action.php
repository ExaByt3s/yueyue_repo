<?php
/**
 * api 调试接口
 * User: willike
 * Date: 2015/10/29
 * Time: 11:34
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: api error!');
// post数据
$json_file = PROTOCOL_MASTER_ROOT . 'conf/api_postman.json'; // 引入postman配置
if (!file_exists($json_file)) {
    return array('result' => 0, 'message' => 'POSTMAN 数据未找到!');
}
$post_arr = json_decode(file_get_contents($json_file), true);
if (empty($post_arr)) {
    return array('result' => 0, 'message' => 'POSTMAN 数据未处理,请联系@willike!');
}
$mkey = filter_input(INPUT_GET, 'mkey');
$is_reget = FALSE;
if(!empty($mkey)){
	$mkey_str = pm_xor_decrypt($mkey);
	if(!empty($mkey_str)){
		list($k_uid,$k_time) = explode('.', $mkey_str);
		if($k_uid == pm_get_user_id() && (intval($k_time) >= time())){
			$is_reget = TRUE;
		}
	}
}

// 获取 wiki数据
$wiki_file = PROTOCOL_MASTER_ROOT . 'conf/api_wiki.json'; // 引入wiki配置
$api_url_json = file_exists($wiki_file) ? file_get_contents($wiki_file) : '';
if (empty($api_url_json) || $api_url_json == '[]' || $is_reget === TRUE) {
    $url = 'http://113.107.204.251/wiki/index.php/Yueyue';// wiki 链接
    $contents = pm_curl($url, array(), 'get');
    if (empty($contents)) {
        return array('result' => 0, 'message' => '获取wiki数据失败!');
    }
    $matchs = array();
    preg_match_all('/\<h3>\<span class="editsection">.*?\<\/pre>/is', $contents, $matchs);
    $api_url = array();
    foreach ($matchs[0] as $html) {
        preg_match('/href="(.*?)">http/', $html, $url_match);
        $url = $url_match[1];
        if (strpos($url, 'customer/') === false && strpos($url, 'merchant/') === false) {
            continue;
        }
        $path = str_replace(array('http://yp.yueus.com/mobile_app/', 'http://yp.yueus.com/', 'https://ypays.yueus.com/', 'https://yp.yueus.com/mobile_app/'), '', $url);
        preg_match('/title="(.*?)">/', $html, $title_match);
        $title = $title_match[1];
        list($k) = explode('/', trim($path, '/'));
        $api_url[$k][$path] = str_replace('编辑段落：', '', trim($title));
    }
    if (empty($api_url)) {
        return array('result' => 0, 'message' => '解析wiki数据错误!');
    }
    $new_api_urls = array();
    foreach ($api_url as $name => $paths) {
        $post_keys = array_keys($post_arr[$name]);
        foreach ($paths as $key => $value) {
            if (!in_array($key, $post_keys)) {
                continue;
            }
            $new_api_urls[$name][$key] = $value;
        }
    }
    if (empty($new_api_urls)) {
        return array('result' => 0, 'message' => '整合api接口数据错误!');
    }
    $wiki_file = PROTOCOL_MASTER_ROOT . 'conf/api_wiki.json'; // 引入wiki配置
    file_put_contents($wiki_file, json_encode($new_api_urls), LOCK_EX);
} else {
    $new_api_urls = json_decode($api_url_json, true);
}
$api_arr = array();
foreach($new_api_urls as $api_list_){
	$api_arr = array_merge($api_arr,array_keys($api_list_));
}
return array(
    'result' => 1,
    'message' => '获取成功!',
    'list' => $new_api_urls,
    'api' => json_encode($api_arr),
    'postman' => json_encode($post_arr),
    'version' => json_encode(pm_conf('DEFAULT_API_VERSION')),
);
