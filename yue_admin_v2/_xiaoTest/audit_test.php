<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/9
 * @Time:   15:54
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
exit;
/*$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//订单类
$user_result = array();
$arr = array(工作室,策划,广告,传媒,活动,文化,传播);
foreach($arr as $val)
{
    $val = trim($val);
    $sql_str = "SELECT * FROM pai_db.pai_user_tbl WHERE nickname LIKE '%{$val}%'" ;
    $result = db_simple_getdata($sql_str,false,101);
    if(!is_array($result)) $result = array();
    $user_result = array_merge($user_result,$result);
}

if(!is_array($user_result)) $user_result = array();
foreach($user_result as &$v)
{
    $v['seller_count'] = $mall_order_obj->get_order_full_list(0, 8, true, "seller_user_id={$v['user_id']}");
    $v['buyer_count'] = $mall_order_obj->get_order_full_list(0, 8, true, "buyer_user_id={$v['user_id']}");
}
if(!is_array($user_result)) $user_result = array();

echo "<table>";
echo "<tr>";
echo "<th>用户ID</th>";
echo "<th>昵称</th>";
echo "<th>注册时间</th>";
echo "<th>手机号</th>";
echo "<th>购买次数</th>";
echo "<th>被购买次数</th>";
echo "</tr>";

foreach($user_result as $vo)
{
    $date_time = date('Y-m-d H:i:s',$vo['add_time']);
    echo "<tr>";
    echo "<th>{$vo['user_id']}</th>";
    echo "<th>{$vo['nickname']}</th>";
    echo "<th>{$date_time}</th>";
    echo "<th>{$vo['cellphone']}</th>";
    echo "<th>{$vo['buyer_count']}</th>";
    echo "<th>{$vo['seller_count']}</th>";
    echo "</tr>";
}
echo "</table>";

 exit;
echo "ok";
print_r($_COOKIE);
echo get_client_ip();
echo "<br/>";
echo $_SERVER['REMOTE_ADDR'] ;
exit;*/
/*
//导出数据
/*$send_message_log_v2_obj = POCO::singleton( 'pai_send_message_log_v2_class');
$update_data['status'] = 0;
$send_message_log_v2_obj->update_info($update_data, 214);
exit;
exit;*/
/*
$mall_obj = POCO::singleton('pai_mall_seller_class');
$page_size = 1000;
$sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
$total_count = intval($sign_ret['total']);
$page_count = ceil($total_count/$page_size);
$start_limit = 0;
$data = array();
echo $page_count;
for($i=0;$i<= $page_count-1;$i++)
{
    $start_limit = $i*$page_size;
    $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
    $ret = array_merge($ret,$list['data']);
}
if(!is_array($ret)) $ret = array();
$user_list = array();
foreach($ret as $v)
{
    $user_list[] = 'yueseller/'.trim($v['user_id']);
}
echo "<pre>";*/
/*$user_arr = get_mall_seller_list_v2();
echo "<pre>";
print_r($user_arr);
print_r(count($user_arr));

function get_mall_seller_list_v2($type_id,$location_id,$user_str ='',$status =1,$option = array())
{
    $mall_obj = POCO::singleton('pai_mall_seller_class');
    $user_list = array();
    $data = array();
    $type_id = intval($type_id);
    $location_id = intval($location_id);
    $user_str = trim($user_str);
    $page_size = 1000;
    $start_limit = 0;
    if($location_id >0)
    {
        $data['location_id'] = $location_id;
    }
    if($status>0)
    {
        $data['status'] = $status;
    }
    $ret = array();
    if($type_id >0)
    {
        if($type_id == 311)//为非模特商家准备的
        {
            $all_ret = array();
            $model_ret = array();
            $sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
            $total_count = intval($sign_ret['total']);
            $page_count = ceil($total_count/$page_size);
            for($i=0;$i<= $page_count-1;$i++)
            {
                $start_limit = $i*$page_size;
                $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
                $all_ret = array_merge($all_ret,$list['data']);
            }
            $data['type_id'] = 31;
            $model_sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
            $total_count = intval($model_sign_ret['total']);
            $page_count = ceil($total_count/$page_size);
            for($i=0;$i<= $page_count-1;$i++)
            {
                $start_limit = $i*$page_size;
                $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
                $model_ret = array_merge($model_ret,$list['data']);
            }
            foreach($all_ret as $v)
            {
                if($v['user_id'] >=100000) $all_list[] = 'yueseller/'.trim($v['user_id']);
            }
            foreach($model_ret as $val)
            {
                if($val['user_id'] >=100000) $model_list[] = 'yueseller/'.trim($val['user_id']);
            }
            $user_list = array_values(array_diff($all_list,$model_list));
        }
        else //有类型type_id 准备的
        {
            $data['type_id'] = $type_id;
            $sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
            $total_count = intval($sign_ret['total']);
            $page_count = ceil($total_count/$page_size);
            for($i=0;$i<= $page_count-1;$i++)
            {
                $start_limit = $i*$page_size;
                $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
                $ret = array_merge($ret,$list['data']);
            }
        }
    }else //没有type_id
    {
        $sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
        $total_count = intval($sign_ret['total']);
        $page_count = ceil($total_count/$page_size);
        for($i=0;$i<= $page_count-1;$i++)
        {
            $start_limit = $i*$page_size;
            $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
            $ret = array_merge($ret,$list['data']);
        }
    }
    if(!is_array($ret)) $ret = array();
    foreach($ret as $v)
    {
        $user_list[] = 'yueseller/'.trim($v['user_id']);
    }
    unset($ret);
    return $user_list;
}

exit;*/
/*$user_arr = $send_message_log_v2_obj->get_mall_seller_list();
echo "<pre>";
print_r($user_arr);
print_r(count($user_arr));
exit;*/

/*
$client = new GearmanClient();
$client->addServer('172.18.5.233', 9830);
$client_json = array(
    'pocoid' => 'yueseller/100008' , // 消费者
);
$version_result = $client->do('get_client_info', json_encode($client_json));
print_r($version_result);
exit;
$user_arr = array(100293,100580);
$user_list = app_check_version($user_arr,'1.1.0');
print_r($user_list);
exit;*/
$type ='card';
$valid_time = '000000-235959';
$duration = '20151127-20151127';
//$user_list = array("yuebuyer/100293","yuebuyer/100000","yuebuyer/100001","yuebuyer/100002","yuebuyer/100003","yuebuyer/100004","yuebuyer/100005","yuebuyer/100006","yuebuyer/100007","yuebuyer/100008","yuebuyer/100009","yuebuyer/100010","yuebuyer/100011","yuebuyer/100012","yuebuyer/100013","yuebuyer/100014","yuebuyer/100015","yuebuyer/100016","yuebuyer/100017","yuebuyer/100018","yuebuyer/100019","yuebuyer/100041");
$user_list = array("yuebuyer/100293");
$send_uid = 10002;
$only_send_online = 0;
$auto_send = 1;
$is_me = 0;
$card_style = 2;
$card_title = '【若能正常收到约约推送，请忽略我】';
$card_text1 = '亲爱的约约用户，您好！
想体验更多约约的优质服务吗？打开约约的系统消息推送功能可让您及时了解更多约约的优惠消息和优质服务噢！具体操作请戳开查看[测试100000-100019]';//卡片描述
$link_url = 'yueyue://goto?type=inner_web&url=http%3a%2f%2fyp.yueus.com%2ftopic.php&wifi_url=http%3a%2f%2fyp-wifi.yueus.com%2ftopic.php';
$wifi_url = 'yueyue://goto?type=inner_web&url=http%3a%2f%2fyp.yueus.com%2ftopic.php&wifi_url=http%3a%2f%2fyp-wifi.yueus.com%2ftopic.php';
$card_text2 = '';
$data = array
(
    'type'       => $type,
    'valid_time' => $valid_time,
    'duration'   => $duration,
    'user_list'  => $user_list,
    'send_uid'   => trim($send_uid),
    'only_send_online' => $only_send_online,
    'auto_send'  => $auto_send,
    'is_me'      => trim($is_me),
    'card_style' => trim($card_style),
    'card_title' => iconv('gbk', 'utf-8',$card_title),
    'card_text1' => iconv('gbk', 'utf-8',$card_text1),
    'link_url'   => $link_url,
    'wifi_url'   => $wifi_url,
    'card_text2' => iconv('gbk', 'utf-8',$card_text2)
);

$json_data = json_encode($data);
unset($data);
//md5校验
$request_md5 = md5($json_data);
$post_data = array
(
    'data' => $json_data,
    'request_md5' => $request_md5
);

print_r($post_data);
$url = "http://172.18.5.211:8085/sendall.cgi";
$ci = curl_init();
curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ci, CURLOPT_TIMEOUT, 20);
curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ci, CURLOPT_URL, $url);
curl_setopt($ci, CURLOPT_POST, true);
curl_setopt($ci, CURLOPT_POSTFIELDS, $post_data);
$response = curl_exec($ci);
curl_close($ci);

$result['response'] = $response;
print_r($result);
return $result;
exit;
//$sql_str ="SELECT * FROM pai_db.pai_user_tbl WHERE (nickname LIKE '%工作室%') OR (nickname LIKE '%策划%') OR (nickname LIKE '%活动%') OR (nickname LIKE '%广告%') OR (nickname LIKE '%经纪%')";
/*$sql_str ="SELECT * FROM pai_db.pai_user_tbl WHERE user_id IN (117549,116922,117552,110158,125208,114746,107202,100766,125208,133674,115098,132621,118614,132510)";

$list = db_simple_getdata($sql_str, false, 101);
if(!is_array($list)) $list = array();
echo "<table>";
foreach($list as $val)
{
    $add_time = date('Y-m-d H:i:s',$val['add_time']);
    $sql = "SELECT SUM(CASE WHEN buyer_user_id={$val['user_id']} THEN 1 ELSE 0 END) AS buyer_count,SUM(CASE WHEN seller_user_id={$val['user_id']} THEN 1 ELSE 0 END) AS seller_count FROM mall_db.mall_order_tbl WHERE status=8";
    $ret = db_simple_getdata($sql, true, 101);
    echo "<tr>";
    echo "<td>{$val['user_id']}</td>";
    echo "<td>{$val['nickname']}</td>";
    echo "<td>{$add_time}</td>";
    echo "<td>{$val['cellphone']}</td>";
    echo "<td>{$ret['buyer_count']}</td>";
    echo "<td>{$ret['seller_count']}</td>";
    echo "</tr>";
}
echo "</table>";



exit;*/
/*$pai_send_message_log_v2_obj = POCO::singleton('pai_send_message_log_v2_class');
$id = intval($_INPUT['id']);
$location_id = intval($_INPUT['location_id']);
$ret = $pai_send_message_log_v2_obj->get_mall_by_type_id($id,$location_id);
echo "<pre>";
print_r($ret);
echo 'shshhshs';
exit;*/

/*$sql_str ="DELETE FROM pai_log_db.pai_mall_all_message_log WHERE status=0 OR duration='20150915-20150915'";
db_simple_getdata($sql_str, true, 101);*/
/*$pai_information_push_obj = POCO::singleton( 'pai_information_push' );

$post_data = array();

$post_data['media_type'] = 'text';
$post_data['content'] = 'xxx';
//$post_data['msg_key'] = "100293100580J&&#3435WS#KSJDF";//发送方id+接收方id+密钥;
$post_data['send_user_id'] = 100293;
$post_data['to_user_id'] = 100580;
$post_data['send_user_role'] = 'yueseller';
foreach($post_data AS &$val)
{
    $val = (string)iconv ( 'gbk', 'utf-8', $val );
}

print_r($post_data);
$ret = $pai_information_push_obj->send_msg($post_data);
print_r($ret);*/