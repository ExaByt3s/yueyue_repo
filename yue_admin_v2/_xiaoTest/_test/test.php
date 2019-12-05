<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/28
 * @Time:   11:34
 * version: 1.0
 */
include_once('../../common.inc.php');
$add_time = trim($_INPUT['add_time']);
$role     = trim($_INPUT['role']);
if(strlen($add_time)<1)
{
    echo "请传入add_time";
    exit;
}
$location_arr = array(
    0=> array('loctaion_id' =>101015001,'name' => '西安')
   /* 0=> array('loctaion_id' =>101029,'name' => '广东'),
    1=> array('loctaion_id' =>101024,'name' => '新疆'),
    2=> array('loctaion_id' =>101001,'name' => '北京'),
    3=> array('loctaion_id' =>101004,'name' => '重庆')*/
);
foreach($location_arr as $key=>$val)
{
    /*$sql_str = "SELECT FROM_UNIXTIME(add_time,'%Y-%m-%d') as add_time2,COUNT(*) as summ from pai_db.pai_user_tbl where
                     FROM_UNIXTIME(add_time,'%Y-%m-%d') >= '{$add_time}'
                     AND LEFT(location_id,6)={$val['loctaion_id']} GROUP BY FROM_UNIXTIME(add_time,'%Y-%m-%d')";*/
    /*$sql_str = "SELECT FROM_UNIXTIME(add_time,'%Y-%m-%d') as add_time2,COUNT(*) as summ from pai_db.pai_user_tbl where role='{$role}' AND FROM_UNIXTIME(add_time,'%Y-%m-%d') >= '{$add_time}' AND location_id={$val['loctaion_id']} GROUP BY FROM_UNIXTIME(add_time,'%Y-%m-%d')";*/
    $sql_str = "SELECT FROM_UNIXTIME(add_time,'%Y-%m-%d') as add_time2,COUNT(*) as summ from pai_db.pai_user_tbl where
                     FROM_UNIXTIME(add_time,'%Y-%m-%d') >= '{$add_time}'
                     AND location_id={$val['loctaion_id']} GROUP BY FROM_UNIXTIME(add_time,'%Y-%m-%d')";
    /*$sql_str = "SELECT FROM_UNIXTIME(add_time,'%Y-%m-%d') as add_time2,COUNT(*) as summ FROM mall_db.mall_seller_tbl WHERE FROM_UNIXTIME(add_time,'%Y-%m-%d') >= '{$add_time}' AND location_id={$val['loctaion_id']} AND status=1 GROUP BY FROM_UNIXTIME(add_time,'%Y-%m-%d')";*/

    $ret = db_simple_getdata($sql_str,false,101);
    if(!is_array($ret)) $ret = array();
    echo "<table><thead><tr><th>{$val['name']}</th><th>{$role}</th></tr></thead>";
    foreach($ret as $vo)
    {
        echo "<tr><td style='border: 1px solid #000;'>{$vo['add_time2']}</td>";
        echo "<td style='border: 1px solid #000;'>{$vo['summ']}</td></tr>";
    }
    echo "</table>";


}
exit;








exit;
$sql_seller_str = "SELECT p.type_id FROM mall_db.mall_seller_profile_tbl p,mall_db.mall_seller_tbl m,mall_db.mall_seller_profile_detail_tbl d WHERE m.status=1 AND p.user_id=m.user_id AND d.profile_id=p.seller_profile_id ";



exit;
$date_time = trim($_INPUT['date_time']);

$sql_str = "SELECT user_id,login_num FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201508 WHERE app_role='yueseller' AND log_time='{$date_time}' AND user_id>0";
$ret = db_simple_getdata($sql_str,false,22);

$total_num = 0;
foreach($ret as $v)
{
    $user_id = intval($v['user_id']);
    $sql_seller_str = "SELECT p.type_id FROM mall_db.mall_seller_profile_tbl p,mall_db.mall_seller_tbl m WHERE m.status=1 AND p.user_id={$user_id} AND m.user_id={$user_id} AND FIND_IN_SET(31,type_id) limit 0,1";
    $ret = db_simple_getdata($sql_seller_str, true, 101);
    $type_id= trim($ret['type_id']);
    if(strlen($type_id) >0)
    {
        $total_num += intval($v['login_num']);
    }
}
echo $total_num;
exit;
$user_level_obj = POCO::singleton( 'pai_user_level_class' ) ;


$sql_str = "SELECT user_id FROM pai_db.pai_user_tbl";
$ret = db_simple_getdata($sql_str,false,101);
if(!is_array($ret)) $ret = array();
$ret_level_v1 = array();
$ret_level_v2 = array();
$ret_level_v3 = array();
foreach($ret as $val)
{
    $level = $user_level_obj->get_user_level($val['user_id']);
    if($level == 1) $ret_level_v1[] = $val['user_id'];
    if($level == 2) $ret_level_v2[] = $val['user_id'];
    if($level == 3) $ret_level_v3[] = $val['user_id'];
}

$sql_tmp_str = '';
/*foreach($ret_level_v1 as $key=>$user_id)
{
    $user_id = intval($user_id);
    if($key !=0) $sql_tmp_str .=',';
    $sql_tmp_str .= $user_id;

}*/

$ret_level_vv = array_merge($ret_level_v2,$ret_level_v3);
foreach($ret_level_vv as $key=>$user_id)
{
    $user_id = intval($user_id);
    if($key !=0) $sql_tmp_str .=',';
    $sql_tmp_str .= $user_id;

}
echo "<table>";
for ($i = 12; $i < 26; $i++)
{
    $table_num = sprintf('%02d', $i);
    $date_time = "2015-08-{$table_num}";
    $sql_str2="SELECT COUNT(DISTINCT(receive_id)) AS UV FROM yueyue_sendserver_for_seller_db.sendserver_for_seller_reply_log_201508 WHERE FROM_UNIXTIME(date_time,'%Y-%m-%d')='{$date_time}' AND sender_id IN ({$sql_tmp_str}) AND receive_identity='yuebuyer' AND FIND_IN_SET(31,type_id)";
    $message_ret = db_simple_getdata($sql_str2,TRUE,22);
    echo "<tr>";
    echo "<td>{$date_time}</td>";
    echo "<td>{$message_ret['UV']}</td>";
    echo "</tr>";

}
echo "</table>";


//print_r($ret_level_v1);
/*print_r($ret_level_v2);
print_r($ret_level_v3);*/