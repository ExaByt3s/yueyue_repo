<?php
/**
 * @desc:   СФ����wiki
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/22
 * @Time:   11:06
 * version: 1.0
 */
include_once('common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$return_query = 'yueyue_static_cms_id%3D849%26cms_type%3Dgoods&title=%E4%BD%A0%E6%89%80%E4%B8%8D%E7%9F%A5%E9%81%93%E7%9A%84%E6%97%B6%E5%B0%9A%E5%9C%88';
$en_ret = mb_convert_encoding(urldecode($return_query), 'gbk', 'utf-8');
parse_str($en_ret, $data);   // ����������
print_r($data);
$goods = array();
$total = 0; // ����
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$default_cover = $mall_seller_obj->_seller_cover;  // Ĭ�ϱ���
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
if ($data['yueyue_static_cms_id']) {
    // ��ȡ������
    $cms_obj = new cms_system_class();
    $record_list = $cms_obj->get_last_issue_record_list(false, $limit_str, 'place_number DESC', $data['yueyue_static_cms_id']);
    // ��������
    $total = $cms_obj->get_last_issue_record_list(TRUE, $limit_str, 'place_number DESC', $data['yueyue_static_cms_id']);
    foreach ($record_list as $value) {
        if (empty($value['link_url']) && $data['cms_type'] == 'mall') {
            // �����̼�
            $search_data = array(
//                'user_id' => $value['user_id'],
                'keywords' => $value['user_id'],
            );
            $result_list = $mall_seller_obj->user_search_seller_list($search_data, '0,1');
            if ($location_id == 'test') {
                $options['data'] = array(
                    '$search_data' => $search_data,
                    '$result_list' => $result_list,
                    'param' => $client_data['data']['param'],
                );
                return $cp->output($options);
            }
            $search_result = $result_list['data'][0];
            if (empty($search_result)) {
                continue;
            }
            $seller_id = $search_result['user_id'];
            $name = get_seller_nickname_by_user_id($seller_id);
            $buy_num = $search_result['bill_finish_num']; // ��������
            $cover = empty($search_result['cover']) ? $default_cover : $search_result['cover'];
            $goods[] = array(
                'goods_id' => $search_result['seller_profile_id'],
                'seller_user_id' => $seller_id,
                'seller' => '', // fix double title 2015-9-6
                'titles' => $buy_num > 0 ? '����' . $buy_num . '�˹���' : '�ṩ������Ŀ' . $search_result['onsale_num'] . '��', // preg_replace('/&#\d+;/', '', $search_result['seller_introduce']),
                'images' => yueyue_resize_act_img_url($cover, '640'),
                'link' => 'yueyue://goto?user_id=' . $user_id . '&seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app', // �̼���ҳ
                'prices' => empty($name) ? '�̼�' : $name, // ��ʱ��Ϊ�̼�����
                'buy_num' => '�ṩ������Ŀ' . $search_result['onsale_num'] . '��',
            );
            continue;
        }
        if (empty($value['link_url']) && $data['cms_type'] == 'goods') {
            // ������Ʒ
            $r_data = array(
                'keywords' => $value['user_id'],
            );
        } else {
            $r_data = array();
            parse_str(urldecode($value['link_url']), $r_data);   // ����������
        }
        $tmp_result = $task_goods_obj->user_search_goods_list($r_data, '0,1');
        $goods_info = $tmp_result['data'][0];
        if (empty($goods_info)) {
            $err_arr[] = array('$r_data' => $r_data, '$tmp_result' => $tmp_result);  // for debug;
            continue;
        }
        $goods_info_arr[] = array('$r_data' => $r_data, '$goods_info' => $goods_info);  // for debug
        $goods_id = $goods_info['goods_id'];
        $name = get_seller_nickname_by_user_id($goods_info['user_id']);
        $price_str = sprintf('%.2f', $goods_info['prices']);
        $prices_list = unserialize($goods_info['prices_list']);
        if (!empty($prices_list)) {
            $min = 0;
            foreach ($prices_list as $v) {
                $v = intval($v);
                if ($v <= 0) {
                    continue;
                }
                $min = ($min > 0 && $min < $v) ? $min : $v;
            }
            if ($min > 0) {
                $price_str = sprintf('%.2f', $min) . 'Ԫ ��';
            }
        }
        $buy_num = $goods_info['bill_finish_num'];
        $cover = empty($goods_info['images']) ? $default_cover : $goods_info['images'];
        $goods[] = array(
            'goods_id' => $goods_id,
            'seller' => empty($name) ? '�̼�' : $name,
            'titles' => preg_replace('/&#\d+;/', '', $goods_info['titles']),
            'images' => yueyue_resize_act_img_url($cover, '640'),
            'link' => 'yueyue://goto?user_id=' . $user_id . '&goods_id=' . $goods_id . '&pid=1220102&type=inner_app',
            'prices' => '��' . $price_str,
//            'buy_num' => '����' . $goods_info['bill_finish_num'] . '�˹���',
            'buy_num' => $buy_num > 0 ? '����' . $buy_num . '�˹���' : $name,
        );
    }
    if ($location_id == 'test') {
        $options['data'] = array(
            '$err_arr' => $err_arr,
            '$goods_info_arr' => $goods_info_arr,
            '$record_list' => $record_list,
            'param' => $client_data['data']['param'],
        );
        return $cp->output($options);
    }
}
print_r($goods);
exit;

$path = filter_input(INPUT_POST, 'path');   // �ļ�·��
var_dump($path);
/*if (empty($path)) {
    $raw_data_str = $HTTP_RAW_POST_DATA;   // ֧�� raw ����
    if (empty($raw_data_str)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 400 Bad Request');
        exit();
    }
    $raw_data_arr = json_decode($raw_data_str, TRUE);
    if (empty($raw_data_arr)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 406 Not Acceptable');
        exit();
    }
    $path = $raw_data_arr['path'];
    if (empty($path)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 401 unauthorized');
        exit();
    }
    $param = $raw_data_arr;
}
if (strpos($path, 'mall/') === FALSE && strpos($path, 'ssl/') === FALSE) {
    // �ֻ� api �ӿ�
    $dir = str_replace('\\', '/', dirname(__FILE__));  // ��ǰ�ļ���·��
    $file = $dir . '/' . (strpos($path, '.php') ? $path : $path . '.php');
    if (!file_exists($file)) {
        header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 404 not found');
        exit();
    }
    $base_url = 'http://yp-new-t.yueus.com/mobile_app';
    $url = str_replace($dir, $base_url, $file);  // ƴװ url
} else {
    // ͨ�ýӿ� ( ��½,ע��� )
    $path = trim(trim($path), '.');
    $url = 'http://yp-new-t.yueus.com' . '/' . (strpos($path, '.php') ? $path : $path . '.php');
//    $url = 'https://ypays.yueus.com' . '/' . (strpos($path, '.php') ? $path : $path . '.php');
}
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 410 gone');
    exit();
}
foreach ($_POST as $key => $value) {
    if ($key == 'path') {
        continue;
    }
    $param[$key] = $value;   // ��װ����
}
isset($param['user_id']) || $param['user_id'] = 100293;  // ����û�ID*/

$param = array('user_id' =>100293, 'location_id'=>'101029001');

$url = "http://yp.yueus.com/mobile_app/customer/buyer_index_plus.php";
// ���ݲ���
$post_data = array(
    'version' => '3.0.0',
    'os_type' => 'api',
    'ctime' => time(),
    'app_name' => 'poco_yuepai_api',
    'sign_code' => substr(md5('poco_' . json_encode($param) . '_app'), 5, -8),
    'is_enc' => 0,
    'param' => $param,
);
//var_dump(json_encode($post_data));exit;
$cov_data = iconv('GBK', 'UTF-8', json_encode($post_data));  // ����ת����

header('Content-type: application/json; charset=utf-8');   // �����ļ���ʽ
echo mobile_app_curl($url, $cov_data);  // ֱ��������

/**
 * cURL ��ȡ����
 *
 * @param string $url ����
 * @param array $post_data POST����
 * @return string
 */
function mobile_app_curl($url, $post_data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
//    curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

exit;