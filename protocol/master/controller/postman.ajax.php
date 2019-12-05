<?php
/**
 * POSTMAN 上传解析
 * User: willike
 * Date: 2015/9/30
 * Time: 15:59
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: postman error!');
$json_return = FALSE;
$pjson = filter_input(INPUT_POST, 'pjson');  // 直接json提交
if (isset($_POST['pjson']) && empty($_POST['pjson'])) {
    return pm_json_return('', 'json数据为空!', 0);
}
if (empty($pjson)) {
    $callback = filter_input(INPUT_GET, 'callback'); // 回调
    if ($callback == 'act.upPic') {
        $file_res = $_FILES['input-file'];
        $type = $file_res['type'];  // 文件类型
        if (strpos($type, 'application') === FALSE) {
            $result = array('code' => 0, 'message' => '上传文件格式错误!');
            return pm_iframe_upload_callback($result, $callback);
        }
    }
    $file_json = file_get_contents($file_res['tmp_name']);  // json 数据
    // 解析数据
    $post_arr = json_decode($file_json, TRUE);
    if (empty($post_arr)) {
        $result = array('code' => 0, 'message' => 'JSON解析失败!');
        return pm_iframe_upload_callback($result, $callback);
    }
    // 写入文件
    $tmp_file = PROTOCOL_MASTER_ROOT . 'tmp/' . pm_get_user_id() . '_' . time() . '.json';
    file_put_contents($tmp_file, $file_json, LOCK_EX);
} else {
    $json_return = TRUE;
    $post_arr = json_decode($pjson, TRUE);
    if (empty($post_arr)) {
        return pm_json_return(array(), 'JSON解析失败!', 0);
    }
}
$params = array();
foreach ($post_arr['collections'] as $collections) {
    foreach ($collections['requests'] as $requests) {
        $request_params = $requests['data'];
        if (is_array($request_params)) {
            $k = '';
            $api_params = array();
            foreach ($request_params as $rp) {
                $key = $rp['key'];
                if (in_array($key, array('access_token', '__debug', '_debug', 'debug', 'test', '_test'))) {
                    continue;
                }
                $value = $rp['value'];
                if (strpos($value, 'test') === 0) {
                    $value = '';
                }
                if ($key == 'path') {
                    if (!pm_check_yue_path($value)) {
                        continue;
                    }
                    $path = strpos($value, '.php') ? $value : $value . '.php';
                    list($k) = explode('/', trim($path, '/'));
                    continue;
                }
                $api_params[$key] = ($key == 'user_id') ? 0 : $value;
            }
            if (empty($k)) {
                continue;
            }
            $params[$k][$path] = $api_params;
            continue;
        }
        $request_params = json_decode($request_params, true);
        $path = $request_params['path'];
        if (!pm_check_yue_path($path)) {
            continue;
        }
        unset($request_params['path'], $request_params['access_token']);
        $request_params['user_id'] = 0;
        $path = strpos($path, '.php') ? $path : $path . '.php';
        list($k) = explode('/', trim($path, '/'));
        if (empty($k)) {
            continue;
        }
        $params[$k][$path] = json_encode($request_params);
    }
}
if (empty($params)) {
    $msg = '结构解析失败,请联系@willike!';
    if ($json_return === TRUE) {
        return pm_json_return('', $msg, 0);
    }
    $result = array('code' => 0, 'message' => $msg);
    return pm_iframe_upload_callback($result, $callback);
}
if ($json_return === FALSE) {
    unlink($tmp_file); // 删除临时文件
}
$json_file = PROTOCOL_MASTER_ROOT . 'conf/api_postman.json'; // api json文件
$res = file_put_contents($json_file, json_encode($params), LOCK_EX);
if ($res) {
	$data = pm_get_user_id() . '.' . (time() + 180);
    $mkey = pm_xor_encrypt($data);
    $msg = '解析成功,存储成功!';
    if ($json_return === TRUE) {
        return pm_json_return(array('mkey' => $mkey), $msg, 1);
    }
    $result = array('code' => 1, 'message' => $msg, 'mkey' => $mkey);
    return pm_iframe_upload_callback($result, $callback);
}
if ($json_return === TRUE) {
    return pm_json_return('', '保存失败!', 0);
}
$result = array('code' => 0, 'message' => '保存失败!');
return pm_iframe_upload_callback($result, $callback);
