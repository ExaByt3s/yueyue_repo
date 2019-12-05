<?php
/**
 * Created by PhpStorm.
 * User: yaohua_he
 * Date: 15/6/2
 * Time: 14:31
 */
set_time_limit(3600);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql_str = "SELECT * FROM test.user_level_tbl WHERE level=3 AND liaotian_num > 1 AND user_id > 100200 ORDER BY RAND() LIMIT 200";
$array_user = db_simple_getdata($sql_str, FALSE, 101);

echo "<table>";
echo "<tr><td>摄影师ID</td><td>摄影师昵称</td><td>是否约拍成功</td><td>模特ID</td><td>模特昵称</td><td>风格</td><td>消费金额</td><td>聊天记录</td>";
foreach($array_user AS $val)
{
    $liaotian_list = get_liaotian_list($val[user_id]);
    foreach($liaotian_list AS $k=>$v)
    {
        $date_list = get_date_list($v[from_user_id],$v[to_user_id]);
        foreach($date_list AS $dk=>$dv)
        {
            $style .= $dv[date_style];
            $price += $dv[date_price];
        }
        $date_num = count($date_list)?count($date_list):'N';
        $liaotian_log = get_liaotian_info($v[from_user_id], $v[to_user_id]);

        echo "<tr><td>" . $v[from_user_id] . "</td><td>" . get_user_name($v[from_user_id]) . "</td><td>" . $date_num . "</td><td>" . $v[to_user_id] . "</td><td>" . get_user_name($v[to_user_id]) . "</td><td>" . $style . "</td><td>" . $price . "</td><td>" . $liaotian_log . "</td></tr>";



        insert_data($v[from_user_id], get_user_name($v[from_user_id]), 3, $date_num, $v[to_user_id],get_user_name($v[to_user_id]),$style,$price, $liaotian_log);
        unset($style);
        unset($price);
    }
}
echo "</table>";


function insert_data($c_id, $c_name, $level, $date_num, $m_id, $m_name, $style, $price, $liaotian_log)
{
    $sql_str = "INSERT INTO test.yunyin_log(c_id, c_name, level, date_num, m_id, m_name, style, price, liaotian_log) VALUES ('{$c_id}', '{$c_name}', '{$level}', '{$date_num}', '{$m_id}', '{$m_name}', '{$style}', '{$price}', :x_liaotian_log)";
    sqlSetParam($sql_str, 'x_liaotian_log', $liaotian_log);
    db_simple_getdata($sql_str, TRUE, 101);
}


function get_user_name($user_id)
{
    $sql_str = "SELECT nickname FROM pai_db.pai_user_tbl WHERE user_id=$user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    return $result['nickname'];
}

function get_date_list($from_user_id, $to_user_id)
{
    $sql_str = "SELECT * FROM event_db.event_date_tbl WHERE from_date_id=$from_user_id AND to_date_id = $to_user_id";
    $result = db_simple_getdata($sql_str, FALSE);
    return $result;
}

function get_liaotian_list($user_id)
{
    $sql_str = "SELECT * FROM test._tmp_yueyue_contact_tbl_0408_05_31
                WHERE from_user_id = $user_id GROUP BY to_user_id";
    $result = db_simple_getdata($sql_str, FALSE, 22);
    return $result;
}


function get_liaotian_info($from_user_id, $to_user_id)
{
    $gmclient= new GearmanClient();
    $gmclient->addServers("113.107.204.233:9555");
    do
    {
        $query_str = $from_user_id . "?" . $to_user_id;
        $result= $gmclient->do("query_chatlog", $query_str);
    }
    while($gmclient->returnCode() != GEARMAN_SUCCESS);
    return iconv('utf-8','gbk', $result);
}

?>