<?php
/**
 * Created by PhpStorm.
 * User: ºÎÒ«»ª
 * Date: 2015/10/15
 * Time: 5:45
 */

set_time_limit(3600);
define(YUEYUE_HEYH_TEST, 1);
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
/*
$msg_info = 'a:10:{s:10:"media_type";s:4:"card";s:10:"card_style";s:1:"2";s:10:"card_text1";s:15:"å·²æ”¯ä»˜è?å?";s:10:"card_title";s:15:"æŽ¥å—æˆ–æ‹’ç»?";s:12:"send_user_id";s:6:"198991";s:10:"to_user_id";s:6:"127662";s:5:"is_me";s:1:"0";s:14:"send_user_role";s:8:"yuebuyer";s:8:"link_url";s:61:"yueseller://goto?order_sn=60513354&pid=1250022&type=inner_app";s:8:"wifi_url";s:61:"yueseller://goto?order_sn=60513354&pid=1250022&type=inner_app";}';
$array_info = mb_unserialize($msg_info);
echo iconv('utf-8', 'gbk', $array_info['card_text1']);

$msg_info = 'a:11:{s:10:"media_type";s:4:"card";s:10:"card_style";s:1:"1";s:10:"card_text1";s:43:"ä¸‹å•äº†æœåŠ? Kasonå°é»„é¸?‰‹å·¥ç²˜åœ?";s:10:"card_text2";s:8:"ï¿?1.00";s:10:"card_title";s:12:"ç­‰å¾…æ”?»˜";s:12:"send_user_id";s:6:"134227";s:10:"to_user_id";s:6:"198981";s:5:"is_me";s:1:"1";s:14:"send_user_role";s:9:"yueseller";s:8:"link_url";s:224:"yueyue://goto?type=inner_web&url=http%3A%2F%2Fyp.yueus.com%2Fmall%2Fuser%2Forder%2Fdetail.php%3Forder_sn%3D48513365&wifi_url=http%3A%2F%2Fyp-wifi.yueus.com%2Fmall%2Fuser%2Forder%2Fdetail.php%3Forder_sn%3D48513365&showtitle=1";s:8:"wifi_url";s:224:"yueyue://goto?type=inner_web&url=http%3A%2F%2Fyp.yueus.com%2Fmall%2Fuser%2Forder%2Fdetail.php%3Forder_sn%3D48513365&wifi_url=http%3A%2F%2Fyp-wifi.yueus.com%2Fmall%2Fuser%2Forder%2Fdetail.php%3Forder_sn%3D48513365&showtitle=1";}';
$array_info = mb_unserialize($msg_info);
echo iconv('utf-8', 'gbk', $array_info['card_text1']);*/

//$obj = POCO::singleton('pai_information_push');

function mb_unserialize($serial_str) {
    $serial_str= preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
    $serial_str= str_replace("\r", "", $serial_str);
    return unserialize($serial_str);
}
$sql_str = "SELECT * FROM pai_log_db.server_switching_information_log_tbl_copy ORDER BY id ASC";
$result = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    $array_info = mb_unserialize($val['log_data']);
    if($array_info)
    {
        var_dump($array_info);
        echo $val['add_time'];
        echo "<BR>---------------------------------------------<BR>";
        //$obj->send_msg_for_order($array_info);
    }
}