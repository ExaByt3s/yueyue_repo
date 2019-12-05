<?php
include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');

/**
 * 流水
 */

$sql_str = "SELECT DATE_FORMAT(FROM_UNIXTIME(update_time), '%Y-%m-%d') AS visit_time ,event_id, COUNT(event_id) AS C
        FROM `pai_db`.`pai_activity_code_tbl`
        WHERE DATE_FORMAT(FROM_UNIXTIME(update_time), '%Y-%m') = '2015-05' AND is_checked=1
        GROUP BY DATE_FORMAT(FROM_UNIXTIME(update_time), '%Y-%m-%d'),event_id";
$result = db_simple_getdata($sql_str, FALSE, 101);
$array_result = array();
foreach($result AS $key=>$val)
{

    $money = get_money_for_event_id($val[event_id], $val[C]);
    if($money)
    {
        //echo "<tr><td>$val[visit_time]</td>" ;
        //echo "<td>{$money}</td></tr>";

        $array_result[$val[visit_time]] += $money;

    }

}
//print_r($array_result);
echo "<table>";
foreach($array_result AS $key=>$val)
{
    echo "<tr><td>$key</td>" ;
    echo "<td>$val</td></tr>";
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
        //echo $sql_str . "<BR>";
    }
    return $money;
}

exit();


/**
 * 下单数据
 */

echo "<table>";
for($i=1; $i<7; $i++)
{
    $table_num = sprintf('%02d', $i);
    $sql_str = "SELECT 	COUNT(id) AS C
              FROM  yueyue_log_tmp_db.yueyue_tmp_log_201505{$table_num}
            WHERE request_filename_param LIKE '%model_style%'";
    $result = db_simple_getdata($sql_str, TRUE, 22);
    echo "<tr><td>2015-05-{$table_num}</td>";
    echo "<td>$result[C]</td></tr>";
}
echo "</table>";

exit();
/**浏览数据**/
echo "<table>";
for($i=20; $i<31; $i++)
{
    $table_num = sprintf('%02d', $i);
//    $sql_str = "SELECT 	COUNT(login_id) AS PV, COUNT(DISTINCTROW(login_id)) AS UV
//                FROM yueyue_log_tmp_db.yueyue_tmp_log_201505{$table_num} ";
//    $result = db_simple_getdata($sql_str, TRUE, 22);
//    echo "<tr><td>2015-05-{$table_num}</td>";
//    echo "<td>$result[PV]</td>";
//    echo "<td>$result[UV]</td></tr>";

//    $sql_str = "SELECT 	login_id, COUNT(login_id) AS C
//                FROM  yueyue_log_tmp_db.yueyue_tmp_log_201505{$table_num}
//                GROUP BY login_id
//                HAVING C= 1";
//    $result = db_simple_getdata($sql_str, FALSE, 22);
//    $num = db_simple_get_affected_rows();
//
//    echo "<tr><td>2015-05-{$table_num}</td>";
//    echo "<td>{$num}</td></tr>";



    $sql_str = "SELECT 	COUNT(id) AS C
              FROM  yueyue_log_tmp_db.yueyue_tmp_log_201503{$table_num}
            WHERE request_filename_param LIKE '%model_card%' ";
    $result = db_simple_getdata($sql_str, TRUE, 22);
    echo "<tr><td>2015-04-{$table_num}</td>";
    echo "<td>$result[C]</td></tr>";
}
echo "</table>";


?>