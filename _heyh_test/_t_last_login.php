<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/9/21
 * Time: 14:12
 */
set_time_limit(3600);
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$chat_user_obj = POCO::singleton('pai_mall_seller_class');
$sql_str = "SELECT user_id FROM mall_db.mall_seller_tbl";
$result = db_simple_getdata($sql_str, FALSE, 101);
echo "<table>";
$num = 0;
foreach($result AS $key=>$val)
{

    //$val['user_id'] = 100008;
    $result = $chat_user_obj->get_first_profile_m_level_by_user_id($val['user_id']);
    //print_r($result);

    if($result['type_id'])
    {
        $type_name_str = '';
        $model_off = 0;
        if($result['type_id'])
        {

            $array_type_id = explode(',', $result['type_id']);
            foreach($array_type_id AS $k=>$v) {
                if($v == 41){
                    $model_off =1;
                    $type_name_str = '约美食';
                }
            }
        }
        if($model_off) {
            echo "<tr>";
            echo "<td>" . $val['user_id'] . "</td>";
            echo "<td>" . yue_get_user_nickname_by_user_id($val['user_id']) . "</td>";
            echo "<td>" . yue_get_user_cellphone_by_user_id($val['user_id']) . "</td>";
            echo "<td>" . $type_name_str . "</td>";
            echo "<td>" . get_last_time_by_user_id($val['user_id']) . "</td>";
            echo "<td>" . get_user_nickname_by_user_id(get_service_belong_by_userid($val['user_id'])) . "</td>";
            echo "</tr>";
        }
    }
}
echo $num;
echo "</table>";


function get_type_name_by_type_id($type_id)
{
    $array_type[31] = '模特邀约';
    $array_type[5] = '摄影培训';
    $array_type[12] = '影棚租赁';
    $array_type[3] = '化妆服务';
    $array_type[40] = '摄影服务';
    $array_type[41] = '约美食';

    return $array_type[$type_id]?$array_type[$type_id]:'其他分类';
}

function yue_get_user_nickname_by_user_id($user_id)
{
    $sql_str = "SELECT nickname
                FROM pai_db.pai_user_tbl
                WHERE user_id = $user_id LIMIT 1";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    return $result['nickname']?$result['nickname']:'';
}

function yue_get_user_cellphone_by_user_id($user_id)
{
    $sql_str = "SELECT cellphone
                FROM pai_db.pai_user_tbl
                WHERE user_id = $user_id LIMIT 1";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    return $result['cellphone']?$result['cellphone']:'';
}


function get_last_time_by_user_id($user_id)
{
    /*    $sql_str = "SELECT 	last_login_time
    FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201508
    WHERE user_id=$user_id AND app_role='yueseller'
    ORDER BY last_login_time DESC  LIMIT 1;";*/

    $sql_str = "SELECT yueseller_last_time FROM yueyue_user_data_db.yueyue_user_info_tbl WHERE user_id=$user_id";
    $result = db_simple_getdata($sql_str, TRUE, 22);
    return $result['yueseller_last_time']?$result['yueseller_last_time']:0;
}

function get_service_belong_by_userid($user_id)
{
    $chat_user_obj = POCO::singleton('pai_mall_seller_class');
    $result = $chat_user_obj->get_seller_service_belong_by_userid($user_id);
    return $result['31']?$result['31']:0;

}

