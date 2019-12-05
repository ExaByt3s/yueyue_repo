<?php
include_once("../poco_app_common.inc.php");

echo "<table>";

for($i=1; $i<=30; $i++)
{
    $day = sprintf('%02d', $i);
    $sql_str = "SELECT COUNT(*) AS PV, COUNT(DISTINCTROW(g_session_id)) AS UV
                FROM yueyue_log_tmp_db.yueyue_tmp_log_201504{$day}
                WHERE current_page_url_path = '/m/wx'";
    $result = db_simple_getdata($sql_str, TRUE, 22);
    echo "<tr><td>" . $result['PV'] . "</td><td>" . $result['UV'] . "</td></tr>";
}

echo "</table>";


?>