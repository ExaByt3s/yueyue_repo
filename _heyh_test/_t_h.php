<?php
set_time_limit(36000);
ini_set('memory_limit', '512M');
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');





$content = '���Բ��Բ��Բ���';

$post_data ['send_user_id']     = ( string ) 10002;
$post_data ['to_user_id']       = ( string ) $_GET['to_user_id']?$_GET['to_user_id']:100045;

$post_data ['content']          = iconv ( 'gbk', 'utf-8', $content );
$post_data ['card_title']       = $post_data ['content'];
$post_data ['card_text1']       = $post_data ['content'];
$post_data ['card_text2']       = $post_data ['content'];

$post_data ['file_small_url']   = 'http://image19-d.yueus.com/yueyue/20151123/20151123155642_108660_10002_59465_640.jpg?1000x330_120';
$post_data ['media_type']       = 'notice';
$post_data ['link_url']         = 'http://www.yueus.com/mall/user/category/index.php?type_id=5';

$post_data = json_encode ( $post_data );
$data ['data'] = $post_data;

var_dump($data);
var_dump(yueyue_message_base_service($data, 'all'));
exit();
$task_goods_obj = POCO::singleton('pai_mall_goods_class');




$sql_str = "SELECT param FROM yueyue_interface_db.interface_analysis_log_tbl_for_sell_services LIMIT 60000, 10000";
$result = db_simple_getdata($sql_str, FALSE, 22);
//print_r($result);
foreach($result AS $key=>$val)
{
    //print_r($val);
    $param = unserialize($val['param']);
    //var_dump($param);
    if($param['goods_id'])
    {
        $goods_id = (int)$param['goods_id'];
        $tmp_result = $task_goods_obj->get_goods_info($goods_id);

        $user_id = $param['user_id']?$param['user_id']:0;
        //print_r($tmp_result);
        //print_r($tmp_result['goods_data']['type_id']);

        $type_id = $tmp_result['goods_data']['type_id'];



        switch($type_id)
        {
            case 31:
                $type_name = $tmp_result['goods_att'][46];
                break;

            case 5:
                $type_name = $tmp_result['goods_att'][62];
                break;

            case 40:
                $type_name = $tmp_result['goods_att'][90];
                break;

            case 43:
                $type_name = $tmp_result['goods_att'][278];
                break;

            case 12:
                $type_name = $tmp_result['goods_att'][17];
                break;

            case 41:
                $type_name = $tmp_result['goods_att'][219];
                break;

            case 3:
                $type_name = $tmp_result['goods_att'][68];
                break;
        }

        if($type_id && $goods_id)
        {
            $type_name = strip_tags($type_name);
            $sql_str = "INSERT INTO yueyue_interface_db.interface_data_tbl(type_id, type_name, user_id, goods_id)
                    VALUES ($type_id, '{$type_name}', $user_id, $goods_id)";
            db_simple_getdata($sql_str, TRUE, 22);
        }


    }

}


exit();
//$sql_str = "";
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');

$c_rst = $weixin_helper_obj->wx_creat_scene_qr_code(1,'QR_LIMIT_STR_SCENE',null,null,'��������');
var_dump($c_rst);


exit();
$user_id = 100022;
//send_message_for_10002($user_id, $send_msg, '', 'all', 'sys_msg');

for($i=1;$i<1000;$i++)
{
   $send_id = 100000+$i;
    $post_data = '';
    $post_data ['send_user_id'] = ( string ) $send_id;
    $post_data ['to_user_id']   = ( string ) $user_id;
    $post_data ['content']      = iconv ( 'gbk', 'utf-8', '����󱿵�' );
    $post_data ['media_type']    = 'text';

    $post_data = json_encode ( $post_data );
    $data ['data'] = $post_data;

    var_dump(yueyue_message_base_service($data, 'all'));
}

exit();
$obj = POCO::singleton('pai_search_class');
$result = $obj->get_search_tag('seller', 31);
print_r($result);

exit();

exit();

//$user_id = 100008;
//$send_msg = '�𾴵�ԼԼ�û������������ʺ����ӷ���������Ϣ���û��ٱ���ϵͳ�������������͵��κ���Ϣ���������ߣ��벦�� 400-082-9003 ��ϵ�ͷ�����';
//send_message_for_10002($user_id, $send_msg, '', 'all', 'sys_msg');
/*
$mall_order_obj = POCO::singleton('pai_mall_order_class');
$num_result = $mall_order_obj->get_order_number_for_buyer(100008);
print_r($num_result);


$status = 8;
$waitcomm_list = $mall_order_obj->get_order_list_for_buyer(100008, 0, $status, false, 'order_id DESC', '0,' . $num_result['wait_comment'], '*', 1);
print_r($waitcomm_list);
exit();*/
//$msg_str = "�𾴵Ŀͻ����ã����������̼��˺ž���̨���������ٶ������������Ѷ����������ֲ��¼ܷ���������2015��10��20��ǰ��ԼԼ�ͷ��ṩ��֤�����Ľ��׶�����ʵ�Ե����ϣ������������ڡ��̼����ʡ���ʵ�����ϵ��ʽ������������Ʒ������ǰ����ҵĹ�ͨ��¼���������ߣ���δ��ʱ�ṩ������2015��10��21����ʽ�۳�����ʹ�õ�ƽ̨�Ż݄���TIHUAN_MSG�����������ı��𣬲��������֣���֪Ϥ��";
//msg_str = "�𾴵Ŀͻ����ã����������̼��˺ž���̨���������ٶ������������Ѷ����������ֲ��¼ܷ���������2015��10��25��ǰ��ԼԼ�ͷ��ṩ��֤�����Ľ��׶�����ʵ�Ե����ϣ������������ڡ��̼����ʡ���ʵ�����ϵ��ʽ������������Ʒ������ǰ����ҵĹ�ͨ��¼���������ߣ���δ��ʱ�ṩ������2015��10��26����ʽ�۳�����ʹ�õ�ƽ̨�Ż݄���TIHUAN_MSG�����������ı��𣬲��������֣���֪Ϥ��";
//$msg_str = "�𾴵Ŀͻ����ã����������̼��˺ž���̨���������ٶ������������Ѷ����������ֲ��¼ܷ���������2015��10��27��ǰ��ԼԼ�ͷ��ṩ��֤�����Ľ��׶�����ʵ�Ե����ϣ������������ڡ���ʵ�����ϵ��ʽ������������Ʒ������ǰ����ҵĹ�ͨ��¼���������ߣ���δ��ʱ�ṩ������2015��10��28����ʽ�۳�����ʹ�õ�ƽ̨�Ż݄���TIHUAN_MSG�����������ı��𣬲��������֣���֪Ϥ��";
//$msg_str = "�𾴵Ŀͻ����ã����������̼��˺ž���̨���������ٶ������������Ѷ����������ֲ��¼ܷ���������2015��10��30��ǰ��ԼԼ�ͷ��ṩ��֤�����Ľ��׶�����ʵ�Ե����ϣ������������ڡ���ʵ�����ϵ��ʽ������������Ʒ������ǰ����ҵĹ�ͨ��¼���������ߣ���δ��ʱ�ṩ������2015��10��31����ʽ�۳�����ʹ�õ�ƽ̨�Ż݄���TIHUAN_MSG�����������ı��𣬲��������֣���֪Ϥ��";
//$msg_str = "�𾴵Ŀͻ����ã����������̼��˺ž���̨���������ٶ������������Ѷ����������ֲ��¼ܷ���������2015��11��02��ǰ��ԼԼ�ͷ��ṩ��֤�����Ľ��׶�����ʵ�Ե����ϣ������������ڡ���ʵ�����ϵ��ʽ������������Ʒ������ǰ����ҵĹ�ͨ��¼���������ߣ���δ��ʱ�ṩ������2015��11��03����ʽ�۳�����ʹ�õ�ƽ̨�Ż݄���TIHUAN_MSG�����������ı��𣬲��������֣���֪Ϥ��";
$msg_str = "�𾴵Ŀͻ����ã����������˻������Կ۳�����10�·���ٶ����������Ż݄������ϵͳ��������������˻������������ʹ��ԼԼ���нӵ����벹�������Ż݄����1410Ԫ������ⶳ��";
echo $msg_str ;
$black_list = array(
    106347=>'',
);

foreach($black_list AS $key=>$val)
{
    $user_id = $key;
    $send_msg = str_replace('TIHUAN_MSG', $val, $msg_str);
    send_message_for_10002($user_id, $send_msg, '', 'yueseller');
    var_dump($user_id);
    var_dump($send_msg);
}
exit();
$version = '3.2.0_bate1';
if(version_compare($version, '3.1.9', '>'))
{
    echo $version ."<BR>";
}

$version = '3.2.0_bate2';
if(version_compare($version, '3.1.9', '>'))
{
    echo $version ."<BR>";
}

$version = '3.2.0_bate3';
if(version_compare($version, '3.1.9', '>'))
{
    echo $version ."<BR>";
}

$version = '3.1.0_bate1';
if(version_compare($version, '3.1.9', '>'))
{
    echo $version ."<BR>";
}
exit();
$type = '/mobile_app/customer/search_sellers';

$q['keyword']   = '����';
$q['type_id']   = '31';
$q['page']      = 10;
$query = 'query=' . serialize($q);

var_dump(yueyuetj_touch_log($type, $query));

exit();
$obj = POCO::singleton('pai_search_class');
$result = $obj->get_search_recommend_content('goods');
print_r($result);
exit();

$chat_user_obj = POCO::singleton('pai_mall_seller_class');

$array_type[31] = 'ģ����Լ';
$array_type[5] = '��Ӱ��ѵ';
$array_type[12] = 'Ӱ������';
$array_type[3] = '��ױ����';
$array_type[40] = '��Ӱ����';
$array_type[41] = 'Լ��ʳ';

$sql_str = "SELECT * FROM test.data_analysis_log_tbl WHERE to_user_role LIKE '%43%'";
$result = db_simple_getdata($sql_str, FALSE, 101);
echo "<table>";
foreach($result AS $key=>$val)
{
        $user_info = get_user_info($val[to_user_id]);
        $phone  = $user_info['cellphone'];
        $last   = get_user_last_login_time($val[to_user_id]);


        echo "<tr>";
        echo "<td>" . date('Y-m-d' , $val[send_time]) . "</td>";
        echo "<td>" . $val['send_user_id'] . "</td>";
        echo "<td>" . $val['to_user_id'] . "</td>";
        echo "<td>" . $phone . "</td>";
        echo "<td>" . $last . "</td>";
        echo "<td>" . $val['content'] . "</td>";
        echo "<td>" . $rs['type_id'] . "</td>";
        echo "</tr>";

}
echo "</table>";

function get_user_info($user_id)
{
    $sql_str = "SELECT * FROM pai_db.pai_user_tbl WHERE user_id=$user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);

    return $result;
}

function get_user_last_login_time($user_id)
{
    $sql_str = "SELECT MAX(login_id) AS last_login_time
            FROM test.login_id_logintime_tbl
            WHERE user_id = $user_id ";
    $result = db_simple_getdata($sql_str, TRUE, 22);
    return $result['last_login_time']?$result['last_login_time']:'0000-00-00';
}



exit();
//$msg_str = "�𾴵Ŀͻ����ã����������̼��˺ž���̨���������ٶ������������Ѷ����������ֲ��¼ܷ���������2015��10��15��ǰ��ԼԼ�ͷ��ṩ��֤�����Ľ��׶�����ʵ�Ե����ϣ������������ڡ���ʵ�����ϵ��ʽ������ǰ����ҵĹ�ͨ��¼���������ߣ���δ��ʱ�ṩ������2015��10��16����ʽ�۳�����ʹ�õ�ƽ̨�Ż݄���TIHUAN_MSG�����������ı��𣬲��������֣���֪Ϥ��";
$msg_str = "�𾴵Ŀͻ����ã����������̼��˺ž���̨���������ٶ������������Ѷ����������ֲ��¼ܷ���������2015��10��20��ǰ��ԼԼ�ͷ��ṩ��֤�����Ľ��׶�����ʵ�Ե����ϣ������������ڡ��̼����ʡ���ʵ�����ϵ��ʽ������������Ʒ������ǰ����ҵĹ�ͨ��¼���������ߣ���δ��ʱ�ṩ������2015��10��21����ʽ�۳�����ʹ�õ�ƽ̨�Ż݄���TIHUAN_MSG�����������ı��𣬲��������֣���֪Ϥ��";
$black_list = array(
    167264=>'���350Ԫ',
    166463=>'���200Ԫ',
    162440=>'���320Ԫ',
    162407=>'���180Ԫ',
    161963=>'���100Ԫ',
    156323=>'���500Ԫ',
    );
foreach($black_list AS $key=>$val)
{
    $user_id = $key;
    $send_msg = str_replace('TIHUAN_MSG', $val, $msg_str);
    send_message_for_10002($user_id, $send_msg, '', 'yueseller');
    var_dump($user_id);
    var_dump($send_msg);
}

exit();
$obj = POCO::singleton('pai_mall_goods_type_attribute_class');
$result = $obj->property_for_search_get_data(5);
print_r($result);

exit();
$topic_id_array = array(687,689,684,680,677,676,671,670,668,666,663);
foreach($topic_id_array AS $val)
{
    $result = get_count_pv_result($val);

    echo $val . "<BR>";
    print_r($result);
    echo "<BR><BR>";

}

function get_count_pv_result($id)
{
    for($i=1;$i<8;$i++)
    {
        $sql_str = "SELECT COUNT(*) AS C FROM yueyue_log_tmp_db.yueyue_tmp_log_2015100" . $i . " WHERE current_page_url_unfiltered LIKE '%topic_id=" . $id  . "%'";
        echo $sql_str;
        $result[$i] = db_simple_getdata($sql_str, TRUE, 22);
    }

    return $result;
}

exit();
$sql_str = "SELECT location_id, COUNT(*) AS C
FROM `pai_db`.`pai_user_tbl`
GROUP BY location_id
ORDER BY C DESC;";
$result = db_simple_getdata($sql_str, FALSE, 101);
echo "<table>";
foreach($result AS $key=>$val)
{
    echo "<tr>";
    echo "<td>" . get_poco_location_name_by_location_id($val['location_id']) . "</td><td>" . $val['C'] . "</td>";
    echo "</tr>";
}
echo "</table>";

exit();
$sql_str = "SELECT goods_id FROM test.mall_order_goods_tbl GROUP BY goods_id";
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    $name_90 = get_name_90_by_goods_id($val[goods_id]);
    $sql_str = "UPDATE test.mall_order_goods_tbl SET name_90='{$name_90}' WHERE goods_id=$val[goods_id]";
    echo $sql_str . "<BR>";
    db_simple_getdata($sql_str, TRUE, 101);
}

function get_name_90_by_goods_id($goods_id)
{
    $sql_str = "SELECT name_90 FROM mall_db.mall_goods_40_tbl WHERE goods_id=$goods_id LIMIT 1";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    return $result['name_90'];
}
exit();
$event_details_obj = POCO::singleton ( 'event_details_class' );

$querys ["location_id"] = '101029001';
$querys ["titleusername"] = '����';
$ret = $event_details_obj->event_fulltext_search ( $querys, $b_select_count, $limit, 'is_top DESC 4,event_status ,add_time DESC', false );
foreach($ret AS $key=>$val)
{
    echo $val['title'] . "<BR>";
}
unset($querys);
echo "<BR>-------------------------------------------<BR>";
$querys ["location_id"] = '101029001';
$querys ["titleusername"] = '�����ӡ�';
$ret = $event_details_obj->event_fulltext_search ( $querys, $b_select_count, $limit, 'is_top DESC 4,event_status ,add_time DESC', false );
foreach($ret AS $key=>$val)
{
    echo $val['title'] . "<BR>";
}


exit();
$pai_home_page_topic_obj = POCO::singleton('pai_home_page_topic_class');
$banner_result = $pai_home_page_topic_obj->get_banner_type_list(101029001, 31);
print_r($banner_result);


echo "<img src='http://image16-d.poco.cn/yueyue/cms/20150911/53742015091117244751953313.jpg'>";
exit();
$array_type[31] = 'ģ����Լ';
$array_type[5] = '��Ӱ��ѵ';
$array_type[12] = 'Ӱ������';
$array_type[3] = '��ױ����';
$array_type[40] = '��Ӱ����';
$array_type[41] = 'Լ��ʳ';

$chat_user_obj = POCO::singleton('pai_mall_seller_class');
$user_list = array(128315,120217,119907,126288,100293,117735,128494,130602,124382,114694,119564,110244,110331,106347,111535,103146,116175,108164,113414,113093,101143,105737,112911,108470,104741,119358,102869,112876,105487,105784,104369,119749,100313,106130,120734,109492,100769,100136,107369,119648,100519,109479,106724,101702,120187,102651,110459,113374,128805,101675,114198,109139,104203,111156,100127,111448,127791,108191,106411,125962,117882,113907,129745,115881,101555,125579,127319,104278,119860,104846,103069,100588,127233,117187,125329,100537,128404,105047,125980,110208,128808,117004,110790,100342,101510,118234,100167,100974,126262,103281,125491,104809,124144,115430,100116,100038,111993,117671,102036,128973,100580,100787,124219,130371,127264,116773,130711,110711,129670,100148,103383,116349,107745,121507,105263,121298,100892,107973,120581,104282,127611,101524,104817,122335,119542,110986,106399,100207,123202,102490,104374,124299,123814,111711,120740,103460,100587,103586,112799,130945,129821,104310,130335,119992,111847,110131,116780,124027,121708,100382,110254,110256,110257,110258,110259,110260,110456,104103,106696,113568,107509,115117,100739,116069,102294,102938,108909,129586,119482,101367,103388,106623,130121,110207,101693,110431,112766,115478,100104,112829,110642,110215,117656,103832);
$array_type = array();
foreach($user_list AS $val)
{
    $result = $chat_user_obj->get_first_profile_m_level_by_user_id($val);
    $array_type_id = explode(',', $result['type_id']);
    foreach($array_type_id AS $k=>$v) {
        $type_name = get_type_name_by_type_id($v);
        $array_type[$type_name] += 1;
    }
}
print_r($array_type);

function get_type_name_by_type_id($type_id)
{
    $array_type[31] = 'ģ����Լ';
    $array_type[5] = '��Ӱ��ѵ';
    $array_type[12] = 'Ӱ������';
    $array_type[3] = '��ױ����';
    $array_type[40] = '��Ӱ����';
    $array_type[41] = 'Լ��ʳ';

    return $array_type[$type_id]?$array_type[$type_id]:'��������';
}
exit();
$rank_event_v2_obj = POCO::singleton('pai_rank_event_v2_class');
$event_result = $rank_event_v2_obj->get_rank_event_by_location_id('list', 99, 101029001,'123');
var_dump($event_result);
exit();

$obj = POCO::singleton('pai_chat_user_info');

$obj->redis_get_user_info_v2(128606, true);
exit();
$chat_user_obj = POCO::singleton('pai_mall_seller_class');

$array_type[31] = 'ģ����Լ';
$array_type[5] = '��Ӱ��ѵ';
$array_type[12] = 'Ӱ������';
$array_type[3] = '��ױ����';
$array_type[40] = '��Ӱ����';
$array_type[41] = 'Լ��ʳ';

$sql_str = "SELECT user_id FROM `mall_db`.`mall_seller_tbl`";
$result = db_simple_getdata($sql_str, FALSE, 101);
echo "<table>";
foreach($result AS $key=>$val)
{


    $result = $chat_user_obj->get_first_profile_m_level_by_user_id($val['user_id']);
    $type_name_str = '';
    $model_off = 0;
    if($result['type_id'])
    {

        $array_type_id = explode(',', $result['type_id']);
        foreach($array_type_id AS $k=>$v) {
            $type_name_str .= get_type_name_by_type_id($v) . ",";
            if($v == 31) $model_off =1;
        }
        $type_name_str = trim($type_name_str, ',');
    }
    if($model_off) {
        echo "<tr>";
        echo "<td>" . $val['user_id'] . "</td>";
        echo "<td>" . $type_name_str . "</td>";
        echo "<td>" . get_last_time_by_user_id($val['user_id']) . "</td>";
        echo "<td>" . get_service_belong_by_userid($val['user_id']) . "</td>";
        echo "</tr>";
    }

}
echo "</table>";


function get_last_time_by_user_id($user_id)
{
    $sql_str = "SELECT last_date_time FROM yueyue_log_tmp_db.sendserver_login_log_201508 WHERE user_id = $user_id AND app_role = 'yueseller' ORDER BY last_date_time DESC  LIMIT 1";
    $result = db_simple_getdata($sql_str, TRUE, 22);
    return $result['last_date_time']?$result['last_date_time']:0;
}

function get_service_belong_by_userid($user_id)
{
    $chat_user_obj = POCO::singleton('pai_mall_seller_class');
    $result = $chat_user_obj->get_seller_service_belong_by_userid($user_id);
    return $result['31']?$result['31']:0;

}

exit();
$chat_user_obj = POCO::singleton('pai_mall_seller_class');
$result = $chat_user_obj->get_first_profile_m_level_by_user_id(114758);
var_dump($result);
exit();
$rank_event_v2_obj = POCO::singleton('pai_rank_event_v2_class');
$event_result = $rank_event_v2_obj->get_rank_event_by_location_id('list', 100, 101029001,'123');
var_dump($event_result);
exit();

$to_url = "/mall/user/comment/?event_id=58918&table_id=5476&type=event";
$content = '��μӡ�'.$event_info ['title'].'�����'.$num.'���Ѿ�������������۰ɣ�';
send_message_for_10002 ( 100021, $content, $to_url ,'yuebuyer');
send_message_for_10002 ( 100008, $content, $to_url ,'yuebuyer');
exit();
$obj = POCO::singleton('pai_chat_user_info');

$obj->redis_get_user_info_v2(128606, true);
exit();
$sql_str = "SELECT user_id FROM pai_db.pai_user_tbl";
//echo $sql_str;
$result = db_simple_getdata($sql_str, FALSE, 101);
//var_dump($result);
foreach($result AS $key=>$val)
{
    $obj->redis_get_user_info_v2($val['user_id'], true);
    echo "<BR>";
}


exit();
$obj = POCO::singleton('pai_chat_user_info');
$obj->save_all_event_by_user_id();

exit();
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$r_data['keywords'] = '2117519';
$tmp_result = $task_goods_obj->user_search_goods_list($r_data, '0,1');
print_r($tmp_result);

exit();

$obj = POCO::singleton('pai_chat_user_info');
$data['user_id'] = '100008';
$obj->set_redis_for_event_id($data);
$obj->save_all_event_by_user_id();
exit();
$obj->redis_get_user_info_v2(126803, true);
$obj->save_all_event_by_user_id();

exit();
$obj = POCO::singleton('pai_app_update_class');

$sql_str = "SELECT user_id FROM pai_db.pai_user_tbl WHERE role = 'cameraman'";
//echo $sql_str;
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    if(!$obj->check_user_is_update($val[user_id])) echo $val[user_id] . "<BR>";
}
exit();
$obj = POCO::singleton('pai_chat_user_info');
$obj->redis_get_user_info_v2(106932, true);


$level_obj = POCO::singleton('pai_user_level_class');
$level = $level_obj->get_user_level(100008);
var_dump($level);

$level = $level_obj->get_user_level(100260);
var_dump($level);

$level = $level_obj->get_user_level(106932);
var_dump($level);

exit();

$cms_obj = new cms_system_class();
$ico_key = 312;
$ico_result = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number ASC', $ico_key);
print_r($ico_result);
exit();
$obj = POCO::singleton('pai_app_update_class');

$sql_str = "SELECT user_id FROM pai_db.pai_user_tbl WHERE role = 'cameraman'";
//echo $sql_str;
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    if(!$obj->check_user_is_update($val[user_id])) echo $val[user_id] . "<BR>";
}
exit();
$type_str = '3,31,40,12,5';
$type_array = explode(',',$type_str);
print_r($type_array);
if(in_array(31, $type_array)) echo "111";

$type_str = '3,40,12,5';
$type_array = explode(',',$type_str);
print_r($type_array);
if(in_array(31, $type_array)) echo "222";
exit();

$chat_user_obj = POCO::singleton('pai_mall_seller_class');
$result = $chat_user_obj->get_first_profile_m_level_by_user_id(100008);
var_dump($result);
exit();
$obj = POCO::singleton('pai_app_update_class');

$sql_str = "SELECT user_id FROM pai_db.pai_user_tbl WHERE role = 'cameraman'";
//echo $sql_str;
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    if($obj->check_user_is_update($val[user_id])) echo $val[user_id] . "<BR>";
}
//var_dump($obj->check_user_is_update(109078));
exit();
$obj = POCO::singleton('pai_app_update_class');
var_dump($obj->check_user_is_update(109078));
exit();
$data['media_type'] = 'card';
$data['card_style'] = '2';
$data['card_text2'] = '111111111111111111111111111111111111';
$data['card_title'] = '222222222222222222222222222222222222';
$data['content']    = '333333333333333333333333333';
$data['link_url']   = 'yueyue://goto?type=inner_web&url=http%3a%2f%2fyp.yueus.com%2fmobile%2fapp%3ffrom_app%3d1%23topic%2f491%0d%0a&wifi_url=http%3a%2f%2fyp-wifi.yueus.com%2fmobile%2fapp%3ffrom_app%3d1%23topic%2f491%0d%0a';
$data['wifi_url']   = 'yueyue://goto?type=inner_web&url=http%3a%2f%2fyp.yueus.com%2fmobile%2fapp%3ffrom_app%3d1%23topic%2f491%0d%0a&wifi_url=http%3a%2f%2fyp-wifi.yueus.com%2fmobile%2fapp%3ffrom_app%3d1%23topic%2f491%0d%0a';
$data['send_user_id'] = '10000';
$data['to_user_id'] = '100008';
yueyue_message_base_service($data);
print_r($data);

exit();
$cms_obj        = new cms_system_class();
$info = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number DESC', 316);
print_r($info);
exit();
$cms_obj        = new cms_system_class();
$info = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number DESC', 351);
print_r($info);
exit();
$rank_event_v2_obj = POCO::singleton('pai_rank_event_v2_class');
$event_result = $rank_event_v2_obj->get_rank_event_by_location_id('list', 100, 101029001,'123');
var_dump($event_result);
exit();

$visit_time = '2015-07-27';

$sql_str = "SELECT 	DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m-%d') AS visit_time, img_url, link_url, interface_name, location_id, COUNT(*) AS C, remark
            FROM  `yueyue_log_tmp_db`.`yueyue_interface_request_log_201507`
            WHERE link_url <> '' AND img_url <> '' AND DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m-%d') = '{$visit_time}'
            GROUP BY DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m-%d'), img_url, link_url, interface_name, location_id;";
$result = db_simple_getdata($sql_str, FALSE, 22);

echo "<table>";
foreach($result AS $key=>$val)
{
    echo "<tr>";
    echo "<td>" . $val['visit_time'] . "</td>";
    if(strlen($val['remark']) > 10)
    {
        $title = $val['remark'];
    }else{
        $title = $val['img_url'];
    }

    echo "<td>" .$title . "</td>";

    $array_link = parse_url($val[link_url]);
    if($array_link[query])
    {
        $query_array = yueyue_query_format($array_link[query]);
        if($query_array[type] == 'inner_web')
        {
            $url = urldecode($query_array[url]);
        }else{
            $url = iconv('utf-8', 'gbk', urldecode($val[link_url]));
        }
    }else{
        $url = iconv('utf-8', 'gbk', urldecode($val[link_url]));
    }
    echo "<td>" . $url . "</td>";
    $array_interface_name = array("get_hot_top"=>"��ҳ", "get_ware_list:banner"=>"��Ʒ�б�ҳ-���λ", "get_ware_list:category"=>"��Ʒ�б�ҳ-����λ", "get_ware_list:goods"=>"��Ʒ�б�ҳ-��Ʒλ");
    echo "<td>" . $array_interface_name[$val[interface_name]] .  "</td>";
    $array_location_id  = array(0=>"δ֪", 101029001=>"����", 101001001=>"����", 101003001=>"�Ϻ�", 101022001=>"�ɶ�", 101004001=>"����", 101015001=>"����", 101024001=>"�½�");
    echo "<td>" . $array_location_id[$val[location_id]] . "</td>";
    echo "<td>" . $val['C'] . "</td>";

    if(stripos($url, 'yp.yueus.com/mobile/app'))
    {
        $check_url_array = parse_url($url);
        if($check_url_array[fragment])
        {

        }
        echo "<td>" .  yueyue_get_url_pv($url, $visit_time) .  "</td>";
    }

    echo "</tr>";
    //if($key == 10 ) exit();
}
echo "</table>";


function yueyue_get_url_pv($url, $visit_time)
{
    $visit_time = date('Ymd', strtotime($visit_time));
    $num = 0;
    $check_url_array = parse_url($url);
    if($check_url_array[fragment])
    {
        $like = urlencode($check_url_array[fragment]);
        $sql_str = "SELECT 	COUNT(*) AS C FROM yueyue_log_tmp_db.yueyue_tmp_log_$visit_time
                    WHERE request_filename_param  LIKE '%{$like}%'";
//        echo $sql_str;
        $result = db_simple_getdata($sql_str, TRUE, 22);
        if($result) $num = $result['C'];
    }
    return $num;

}

function yueyue_query_format($query_str)
{
    $result_array = explode("&", $query_str);
    foreach($result_array AS $key=>$val)
    {
        //echo $val . "\r\n";
        $rs = explode("=", $val);
        //print_r($rs);
        $return_array[$rs[0]] = $rs[1];
    }
    //print_r($return_array);
    return $return_array;

}


exit();
echo "1111";
$fulltext_obj = POCO::singleton('pai_fulltext_class');

$sql_str = "SELECT user_id FROM pai_db.pai_user_tbl WHERE role='cameraman'";
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $val)
{
    $fulltext_obj->cp_data_by_user_id($val['user_id']);
}




exit();
$rank_event_obj         = POCO::singleton('pai_rank_event_class');
$pai_cms_parse_obj      = POCO::singleton( 'pai_cms_parse_class' );


$location_id    = 101024001;
$role           = 'cameraman';

$ranking_array = $rank_event_obj->get_rank_event_by_location_id($location_id, $role);
print_r($ranking_array);

/*
$ranking_array[257][0] = '�Ƽ�ģ��';
$ranking_array[257][1] = 144;
$ranking_array[257][2] = '����';
$ranking_array[257][3] = '';
$ranking_array[257][4] = '';
$ranking_array[257][5] = 0;
*/

$data = $pai_cms_parse_obj->cms_parse_by_array_v2($ranking_array);
var_dump($data);


exit();
$pai_home_page_topic_obj = POCO::singleton('pai_home_page_topic_class');

$category_array = $pai_home_page_topic_obj->get_big_category(101001001);
print_r($category_array);

$result = $pai_home_page_topic_obj->get_category_text(9423);
//print_r($result);


//exit();
$banner_list             = $pai_home_page_topic_obj->get_banner_list(9422);
//print_r($banner_list);

$goods_result = $pai_home_page_topic_obj->get_category_goods(9423);
print_r($goods_result);
exit();

$obj = POCO::singleton('pai_home_page_topic_class');
$category_array = $obj->get_big_category(0);
foreach($category_array AS $key=>$val)
{
    echo "<BR>" . $val[remark] . "<BR>";
    $result = $obj->get_small_category($val[remark]);
    print_r($result);

    $goods_result = $pai_home_page_topic_obj->get_category_goods();
    print_r($goods_result);
}

exit();
$fulltext_obj = POCO::singleton('pai_fulltext_class');
$fulltext_obj->cp_data_by_user_id(110896);

exit();
$chat_obj = POCO::singleton('pai_chat_user_info');
var_dump($chat_obj->redis_get_user_info(110896));
exit();
$cms_obj        = new cms_system_class();
$key            = 196;
$list_info      = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number DESC', $key);
print_r($list_info);
exit();
$chat_obj = POCO::singleton('pai_chat_user_info');
$chat_obj->introduction_all_user_data();

exit();
$obj = POCO::singleton('pai_home_page_topic_class');
/*$result = $obj->get_big_category(0);
var_dump($result);*/

$result = $obj->get_small_category(9422);
var_dump($result);

exit();
$chat_obj = POCO::singleton('pai_chat_user_info');
$chat_obj->introduction_all_user_data();

exit();
function get_liaotian_info($from_user_id, $to_user_id)
{
    $gmclient= new GearmanClient();
    $gmclient->addServers("113.107.204.233:9555");

        $query_str = $from_user_id . "?" . $to_user_id;
        $result= $gmclient->do("query_chatlog", $query_str);

    return $result;
}


function get_date_list($from_user_id, $to_user_id)
{
    $sql_str = "SELECT * FROM event_db.event_date_tbl WHERE from_date_id=$from_user_id AND to_date_id = $to_user_id";
    $result = db_simple_getdata($sql_str, FALSE);
    return $result;
}

var_dump(get_date_list(105270, 107355));


var_dump(get_liaotian_info(115155, 100751));


exit();
var_dump($_INPUT);

$content = $_INPUT['content'];
$content = iconv("GBK",  "UTF-8" , $content);
$content = iconv("UTF-8" , "GBK",  $content);
$content = str_replace('<br rel=auto>', "\r\n", $content);
$content .=$content;
echo $content;
exit;
$user_id = 100008;
send_message_for_10002_v2($user_id, $content);

exit();
var_dump(send_offline_message(100045, '������ǹ���������123������������123'));
var_dump(send_offline_message(100008, '������ǹ���������123������������123'));
//var_dump(send_offline_message(100003, '������ǹ���������������������'));
exit();
$obj = POCO::singleton('pai_chat_user_info');
var_dump($obj->redis_get_user_info(100028));
exit();
$obj->introduction_all_user_data();
exit();



$to_user_id = $_GET['to_user_id']?$_GET['to_user_id']:100260;

$data ['data'] = '{"send_user_id":"10000","to_user_id":"'. $to_user_id . '","media_type":"notify","from":"web","link_url":"yueyue://goto?type=inner_app&pid=1220079","content":"����һ���µı���"}';

$data ['data'] = iconv('GBK', 'UTF-8', $data['data']);
$url = 'http://113.107.204.233:8090/testfcgi.cgi';
$ch = curl_init ();
curl_setopt ( $ch, CURLOPT_POST, 1 );
curl_setopt ( $ch, CURLOPT_URL, $url );
curl_setopt ( $ch, CURLOPT_HEADER, 0 );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
$result = curl_exec ( $ch );
curl_close ( $ch );
print_r($result);


exit();
$event_details_obj = POCO::singleton ( 'event_details_class' );

$querys['event_id'] = 47185;
$b_select_count = false;
$limit = '0,30';
$result = $event_details_obj->event_fulltext_search ( $querys, $b_select_count, $limit, 'is_top DESC 4,event_status ,add_time DESC', false );
print_r($result);
exit();
$user_id = 100045;
$content ="���";
var_dump(send_message_for_10006($user_id, $content, $url));

exit();
echo $_SERVER['SERVER_NAME'];
//��ȡ��Դ��ַ,�����������ҳ����ҳ��ַ
echo $_SERVER["HTTP_REFERER"];
echo $_SERVER['REQUEST_URI'];//��ȡ��ǰ�����ĺ�׺
echo $_SERVER['HTTP_HOST'];//��ȡ��ǰ����

$jump_off = stripos($_SERVER['HTTP_HOST'], 'selltime.cn') ? true : false;
if($jump_off)
{
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: http://www.yueus.com/');
}
exit();
set_time_limit(3600);
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/2/12
 * Time: 15:44
 */

include_once "poco_app_common.inc.php";

$begin_time = strtotime('2015-03-15');
$end_time   = strtotime('2015-04-20');


for($i=$begin_time;$i<=$end_time;$i=$i+3600*24)
{
    $table_num = date('Ymd', $i);
    $sql_str  = "SELECT event_id , budget
                FROM event_db.event_details_tbl
                WHERE type_icon = 'photo' AND new_version = 2 AND event_status='2'
                AND DATE_FORMAT(FROM_UNIXTIME(active_time), '%Y%m%d') = '{$table_num}'";
    $result = db_simple_getdata($sql_str, FALSE);
    if($result)
    {
        foreach($result AS $key=>$val)
        {
            $sql_str = "SELECT COUNT(id) AS C
                        FROM `pai_db`.`pai_activity_code_tbl` WHERE event_id =47062 AND is_checked=1";
            $rs = db_simple_getdata($sql_str, TRUE, 101);
            if($rs)
            {
                $sum_money = $val['budget'] * $rs['C'];
            }
        }
    }
    echo $table_num . "|" . $sum_money . "<BR>";

}

exit();
$user_id = 100008;
$send_data = array();
$send_data['media_type'] ='card';
$send_data['card_style'] = 2;
$send_data['card_text1'] = '��ϲ���ü�ֵ';
$send_data['card_title'] = '�鿴�Ż�ȯ';
$send_data['link_url'] = 'yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=100260';
$send_data['wifi_url'] = 'yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=100260';
$push_obj = POCO::singleton('pai_information_push');
$ret = $push_obj->send_msg_for_system_v2(10006, $user_id, $send_data);
var_dump($ret);

exit();
$obj = POCO::singleton('pai_chat_user_info');
$obj->introduction_all_user_data();


exit();
echo "<table>";
for($i=1;$i<=31;$i++)
{
    $table_name = 'yueyue_log_tmp_db.yueyue_tmp_log_201503' . sprintf('%02d', $i);
    $sql_str= "SELECT 	COUNT(g_session_id) AS PV, COUNT(DISTINCTROW(g_session_id)) AS UV
            FROM $table_name
            WHERE current_page_url_path LIKE '/m/wx'";
    $result = db_simple_getdata($sql_str, TRUE, 22);
    echo '<tr><td>'.$result[PV]  . '</td><td>' . $result[UV] . '</td></tr>';
}
echo "</table>";


exit();

$location_info = POCO::execute('common.get_location_2_location_id', '���');
print_r($location_info);

exit();
$sql_str = "SELECT c.user_id AS model_id, b.user_id AS cameraman_id, c.add_time AS add_time
          FROM `pai_user_library_db`.`model_oa_order_tbl` AS a, pai_db.pai_user_tbl AS b, pai_user_library_db.model_oa_enroll_tbl AS c
        WHERE a.cameraman_phone = b.cellphone AND a.order_id = c.order_id";
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    $date_time = $val['add_time'] + 3600*172;
    $sql_str ="SELECT from_date_id, to_date_id, FROM_UNIXTIME(add_time) FROM event_db.event_date_tbl
               WHERE from_date_id=$val[cameraman_id] AND to_date_id=$val[model_id] AND add_time >= $val[add_time] AND add_time <= $date_time";
    $rs = db_simple_getdata($sql_str, FALSE);
    if($rs) print_r($rs);
    echo "<BR>---------------------------------------------------------<BR>";
}

exit();
$sql_str = "SELECT *
FROM `pai_db`.`pai_user_tbl` WHERE add_time >= '1425139200' AND add_time <= '1427817600' AND role='cameraman' ORDER BY add_time ASC";

$result = db_simple_getdata($sql_str, FALSE, 101);
echo "<table>";
foreach($result AS $key=>$val)
{
    $user_result = get_yuepai_result($val['user_id']);
    $poco_id = get_poco_id($val['user_id']);
    //echo $poco_id . "<BR>";
    if($poco_id) $waipai_result = get_waipai_result($poco_id);
    echo "<tr><td>" . $val['nickname'] . "</td><td>" . $val['cellphone'] . "</td><td>" . date("Y-m-d H:i:s", $val['add_time']) . "</td><td>" . get_poco_location_name_by_location_id ( $val ['location_id'] ) . "</td><td>" . $val['reg_from'] .  "</td><td>" . $user_result['PV'] . "</td><td>" . $user_result['SUM_PRICE'] . "</td><td>"  . $waipai_result['original_price'] . "</td><td>" . $waipai_result['discount_price'] . "</td><td>" . $waipai_result['sum_num'] . "</td></tr>";
}
echo "</table>";

function get_yuepai_result($id)
{
    $sql_str = "SELECT 	COUNT(from_date_id) AS PV, SUM(date_price) AS SUM_PRICE
                FROM `event_db`.`event_date_tbl`
                WHERE from_date_id = $id AND date_status='confirm' ";
    $result = db_simple_getdata($sql_str, TRUE);
    return $result;
}

function get_waipai_result($id)
{
    $sql_str = "SELECT 	SUM(original_price) AS original_price, SUM(discount_price) AS discount_price, SUM(enroll_num) AS sum_num
                FROM `event_db`.`event_enroll_tbl` WHERE user_id = $id";
    $result =db_simple_getdata($sql_str, TRUE);
    return $result;
}

function get_poco_id($id)
{
    $sql_str = "SELECT `user_id`, `poco_id` FROM `pai_db`.`pai_relate_poco_tbl` WHERE user_id = $id";
    $reuslt  = db_simple_getdata($sql_str, TRUE, 101);
    return $reuslt['poco_id'];
}
exit();


$obj = POCO::singleton('pai_information_push');

$send_data['media_type'] = 'card';
$send_data['card_style'] = '2';
$send_data['card_text1'] = '��ã����Ѿ��������㷢����XXXX���Ļ���ȴ������Ŷ��';
$send_data['card_text2'] = '��ã����Ѿ��������㷢����XXXX���Ļ���ȴ������Ŷ��';
$send_data['card_title'] = '�鿴����';
$send_data['link_url']   = 'http://yp.yueus.com/mobile/app?from_app=1#camera_demand/detail/1000512';
$send_data['wifi_url']   = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#camera_demand/detail/1000512';

$obj->send_msg_data(100000, 100008, $send_data, $send_data, 0, 10);

exit();
//$data ['data'] = '{"send_user_id":"100260","to_user_id":"100000","media_type":"text","content":"��ã����Ѿ��������㷢����XXXX���Ļ���ȴ������Ŷ��","is_me":"0"}';
$data ['data'] = '{"send_user_id":"100000","to_user_id":"100260","media_type":"card","card_style":"2","card_text1":"��ã����Ѿ��������㷢����XXXX���Ļ���ȴ������Ŷ��","card_text2":"��ã����Ѿ��������㷢����XXXX���Ļ���ȴ������Ŷ��","card_title":"�鿴����","link_url":"http://yp.yueus.com/mobile/app?from_app=1#camera_demand/detail/1000512","wifi_url":"http://yp-wifi.yueus.com/mobile/app?from_app=1#camera_demand/detail/1000512","is_me":"1"}';
print_r($data);
$data ['data'] = iconv('GBK', 'UTF-8', $data['data']);
print_r($data);
$url = 'http://113.107.204.233:8080/sendserver.cgi';
$ch = curl_init ();
curl_setopt ( $ch, CURLOPT_POST, 1 );
curl_setopt ( $ch, CURLOPT_URL, $url );
curl_setopt ( $ch, CURLOPT_HEADER, 0 );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
$result = curl_exec ( $ch );
curl_close ( $ch );
print_r($result);

$data ['data'] = '{"send_user_id":"100000","to_user_id":"100260","media_type":"card","card_style":"2","card_text1":"��ã����Ѿ��������㷢����XXXX���Ļ���ȴ������Ŷ��","card_text2":"��ã����Ѿ��������㷢����XXXX���Ļ���ȴ������Ŷ��","card_title":"�鿴����","link_url":"http://yp.yueus.com/mobile/app?from_app=1#camera_demand/detail/1000512","wifi_url":"http://yp-wifi.yueus.com/mobile/app?from_app=1#camera_demand/detail/1000512","is_me":"0"}';
print_r($data);
$data ['data'] = iconv('GBK', 'UTF-8', $data['data']);
print_r($data);
$url = 'http://113.107.204.233:8080/sendserver.cgi';
$ch = curl_init ();
curl_setopt ( $ch, CURLOPT_POST, 1 );
curl_setopt ( $ch, CURLOPT_URL, $url );
curl_setopt ( $ch, CURLOPT_HEADER, 0 );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
$result = curl_exec ( $ch );
curl_close ( $ch );
print_r($result);

exit();
$str = "���g����Ҋ ��POCO��Ʒ����3��ר��";
send_message_for_10002(100008, $str);


exit();
$sql_str = "SELECT * FROM pai_db.pai_model_style_v2_tbl";
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    if($val[style])
    {
        $style_array = explode(' ',$val[style]);
        foreach($style_array AS $k=>$v)
        {
            $style_id = 0;
            switch($v)
            {
                case 'ŷ��':
                    $style_id = 0;
                    break;
                case '����':
                    $style_id = 1;
                    break;
                case '����':
                    $style_id = 2;
                    break;
                case '����':
                    $style_id = 3;
                    break;
                case '��ϵ':
                    $style_id = 4;
                    break;
                case '��ϵ':
                    $style_id = 5;
                    break;
                case '�Ը�':
                    $style_id = 6;
                    break;
                case '����':
                    $style_id = 7;
                    break;
                case '��Ƭ':
                    $style_id = 8;
                    break;
                case '��ҵ':
                    $style_id = 9;
            }

            if($val[hour] == 2)
            {
                $sql_str = "INSERT INTO pai_user_library_db.model_style_tbl(uid, style, twoh_price, addh_price)
                          VALUES ('{$val[user_id]}', '{$style_id}', '{$val[price]}', '{$val[continue_price]}')";
            }else{
                $sql_str = "INSERT INTO pai_user_library_db.model_style_tbl(uid, style, fourh_price, addh_price)
                          VALUES ('{$val[user_id]}', '{$style_id}', '{$val[price]}', '{$val[continue_price]}')";
            }

            echo $sql_str . "<BR>";
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }
}


exit();
echo "<table>";
for($i=1; $i<29; $i++)
{
    $table_num = sprintf('%02d', $i);
    $sql_str = "SELECT 	COUNT(id) AS C
              FROM  yueyue_log_tmp_db.yueyue_tmp_log_201502{$table_num}
            WHERE request_filename_param LIKE '%act%2Fdetail%'";
    $result = db_simple_getdata($sql_str, TRUE, 22);
    echo "<tr><td>2015-02-{$table_num}</td>";
    echo "<td>$result[C]</td></tr>";
}
echo "</table>";
exit();
echo "<table>";
$sql_str = "SELECT DATE_FORMAT(FROM_UNIXTIME(update_time), '%Y-%m-%d') AS visit_time ,event_id, COUNT(event_id) AS C
        FROM `pai_db`.`pai_activity_code_tbl`
        WHERE DATE_FORMAT(FROM_UNIXTIME(update_time), '%Y-%m') = '2015-03' AND is_checked=1
        GROUP BY DATE_FORMAT(FROM_UNIXTIME(update_time), '%Y-%m-%d'),event_id";
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    $money = get_money_for_event_id($val[event_id], $val[C]);
    if($money)
    {
        echo "<tr><td>$val[visit_time]</td>" ;
        echo "<td>{$money}</td></tr>";
    }

}
echo "</table>";

function get_money_for_event_id($event_id, $num)
{
    $money = 0;
    $sql_str = "SELECT * FROM event_db.event_details_tbl WHERE event_id=$event_id";
    $result = db_simple_getdata($sql_str, TRUE);
    if($result['type_icon'] == 'photo' && $result['new_version'] == 2 )
    {
        $money = $result['budget'] * $num;
        echo $sql_str . "<BR>";
    }
    return $money;
}
exit();
$sql_str = "SELECT DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m-%d'), event_id, budget
            FROM `event_db`.`event_details_tbl`
            WHERE type_icon = 'photo'
            AND new_version=2
            AND DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m') = '2015-03'
            AND event_status = '2' ";
$result = db_simple_getdata($sql_str, FALSE);
$sum_money = 0;
foreach($result AS $key=>$val)
{
    $money = get_socre_num($val[event_id], $val[budget]);
    $sum_money = $sum_money+$money;
}
echo $sum_money;

function get_socre_num($event_id, $budget)
{
    $sql_str = "SELECT COUNT(event_id) AS C
                FROM `pai_db`.`pai_activity_code_tbl`
                WHERE is_checked = 1 AND event_id = 44240";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    $money = $result[C] * $budget;
    return $money;
}

exit();
echo "<table>";
for($i=1; $i<16; $i++)
{
    $table_num = sprintf('%02d', $i);
//    $sql_str = "SELECT 	COUNT(login_id) AS PV, COUNT(DISTINCTROW(login_id)) AS UV
//                FROM yueyue_log_tmp_db.yueyue_tmp_log_201503{$table_num} ";
//    $result = db_simple_getdata($sql_str, TRUE, 22);
//    echo "<tr><td>2015-03-{$table_num}</td>";
//    echo "<td>$result[PV]</td>";
//    echo "<td>$result[UV]</td></tr>";

//    $sql_str = "SELECT 	login_id, COUNT(login_id) AS C
//                FROM  yueyue_log_tmp_db.yueyue_tmp_log_201503{$table_num}
//                GROUP BY login_id
//                HAVING C= 1";
//    $result = db_simple_getdata($sql_str, FALSE, 22);
//    $num = db_simple_get_affected_rows();
//
//    echo "<tr><td>2015-03-{$table_num}</td>";
//    echo "<td>{$num}</td></tr>";



//    $sql_str = "SELECT 	COUNT(id) AS C
//              FROM  yueyue_log_tmp_db.yueyue_tmp_log_201503{$table_num}
//            WHERE request_filename_param LIKE '%model_card%' ";
//    $result = db_simple_getdata($sql_str, TRUE, 22);
//    echo "<tr><td>2015-03-{$table_num}</td>";
//    echo "<td>$result[C]</td></tr>";
}
echo "</table>";


?>