<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/9/22
 * Time: 21:19
 */
ignore_user_abort(true);
set_time_limit(36000);

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
/*
$sql_str = "SELECT * FROM test.spread_data_tbl";
$result = db_simple_getdata($sql_str, FALSE, 101);
var_dump($result);

echo "<table>";
foreach($result AS $key=>$val)
{
    $user_info = get_user_info($val['user_id']);
    $login_info = get_user_login_info($val['user_id']);
    //var_dump($login_info);

    echo "<tr><td>" . $val['user_id'] . "</td>";
    echo "<td>" . $val['nickname'] . "</td>";
    echo "<td>" . date('Y-m-d H:i:s', $val['add_time']) . "</td>";
    echo "<td>" . date('Y-m-d H:i:s', $val['visit_time']) . "</td>";
    echo "<td>" . $val['y_30_login'] . "</td>";
    echo "<td>" . $val['y_7_login'] . "</td>";
    echo "<td>" . $val['y_last_time'] . "</td>";
    echo "<td>" . $val['s_30_login'] . "</td>";
    echo "<td>" . $val['s_7_login'] . "</td>";
    echo "<td>" . $val['s_last_time'] . "</td>";
    echo "<td>" . $val['package_sn'] . "</td>";
    echo "<td>" . $val['reg_from'] . "</td></tr>";

}
echo "</table>";

exit();*/
$sql_str = "SELECT * FROM pai_coupon_db.coupon_exchange_tbl WHERE package_sn LIKE 'yueus%' OR package_sn LIKE 'yueyue%' ORDER BY user_id ASC LIMIT 40000,10000";
$result = db_simple_getdata($sql_str, FALSE, 101);

//echo "<table>";
foreach($result AS $key=>$val)
{
    $user_info = get_user_info($val['user_id']);
    $login_info = get_user_login_info($val['user_id']);
    //var_dump($login_info);

/*    echo "<tr><td>" . $val['user_id'] . "</td>";
    echo "<td>" . $user_info['nickname'] . "</td>";
    echo "<td>" . date('Y-m-d H:i:s', $val['add_time']) . "</td>";
    echo "<td>" . date('Y-m-d H:i:s', $user_info['add_time']) . "</td>";
    echo "<td>" . $login_info['yuebuyer_30_day_login'] . "</td>";
    echo "<td>" . $login_info['yuebuyer_7_day_login'] . "</td>";
    echo "<td>" . $login_info['yuebuyer_last_time'] . "</td>";
    echo "<td>" . $login_info['yueseller_30_day_login'] . "</td>";
    echo "<td>" . $login_info['yueseller_7_day_login'] . "</td>";
    echo "<td>" . $login_info['yueseller_last_time'] . "</td>";
    echo "<td>" . $val['package_sn'] . "</td>";
    echo "<td>" . get_poco_location_name_by_location_id($user_info['location_id']) . "</td>";
    echo "<td>" . $user_info['reg_from'] . "</td></tr>";*/

    $add_time = date('Y-m-d H:i:s', $val['add_time']);
    $visit_time = date('Y-m-d H:i:s', $user_info['add_time']);
    $location = get_poco_location_name_by_location_id($user_info['location_id']);

    $sql_str = "INSERT IGNORE INTO `test`.`spread_data_tbl` (`user_id`,`nickname`,`add_time`, `visit_time`,`y_30_login`,`y_7_login`,`y_last_time`,`s_30_login`,`s_7_login`,`s_last_time`, `package_sn`, `location`, `reg_from`)
                VALUES ('{$val[user_id]}', :x_nickname, '{$add_time}', '{$visit_time}', '{$login_info[yuebuyer_30_day_login]}', '{$login_info[yuebuyer_7_day_login]}', '{$login_info[yuebuyer_last_time]}', '{$login_info[yueseller_30_day_login]}'
                        , '{$login_info[yueseller_7_day_login]}' , '{$login_info[yueseller_last_time]}', '{$val[package_sn]}', '{$location}', '{$user_info[reg_from]}')";
    sqlSetParam($sql_str, 'x_nickname', $user_info[nickname]);
    echo $sql_str . "<BR>";
    db_simple_getdata($sql_str, TRUE, 101);
}
//echo "</table>";

function get_user_info($user_id)
{
    if($user_id)
    {
        $sql_str = "SELECT * FROM pai_db.pai_user_tbl WHERE user_id=$user_id";
        $result = db_simple_getdata($sql_str, TRUE, 101);
        return $result;
    }
}

function get_user_login_info($user_id)
{
    if($user_id)
    {
        $sql_str = "SELECT * FROM yueyue_user_data_db.yueyue_user_info_tbl WHERE user_id = $user_id";
        $result = db_simple_getdata($sql_str, TRUE, 22);
        return $result;
    }
}