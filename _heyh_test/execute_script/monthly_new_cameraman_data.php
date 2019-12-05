<?php
/**
 * Created by PhpStorm.
 * User: heyh
 * Date: 2015/4/30
 * Time: 18:47
 */
include_once("../poco_app_common.inc.php");

/*$month = '04';

$reg_time = '2015-' . $month;

$sql_str = "SELECT user_id, cellphone, location_id, reg_from FROM pai_db.pai_user_tbl WHERE DATE_FORMAT(FROM_UNIXTIME(add_time), '%Y-%m') = '2015-04' AND role = 'cameraman'";
$result  = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    $sql_str = "INSERT INTO yueyue_user_data_db.new_cameraman_data_tbl_201504(yue_user_id, location_id, cellphone, reg_from)
                  VALUES ($val[user_id],$val[location_id], $val[cellphone],'{$val[reg_from]}')";
    echo $sql_str . "<BR>";
    $rs = db_simple_getdata($sql_str, TRUE, 22);
}*/

/*
$sql_str = "SELECT * FROM  yueyue_user_data_db.new_cameraman_data_tbl_201504";
$result = db_simple_getdata($sql_str, FALSE, 22);
foreach($result AS $key=>$val)
{
    $poco_id = get_poco_id_by_yue_id($val['yue_user_id']);
    if($poco_id)
    {
        $sql_str = "UPDATE yueyue_user_data_db.new_cameraman_data_tbl_201504 SET poco_user_id=$poco_id WHERE yue_user_id=$val[yue_user_id]";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 22);

    }

    $yuepai_data = get_yuepai_data($val['yue_user_id']);
    if($yuepai_data['C'])
    {
        $sql_str = "UPDATE yueyue_user_data_db.new_cameraman_data_tbl_201504 SET yuepai_num = $yuepai_data[C], yuepai_pay = '{$yuepai_data[SUM_PRICE]}' WHERE yue_user_id=$val[yue_user_id]";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 22);
    }

    $waipai_data = get_waipai_data($val['poco_user_id']);
    if($waipai_data['C'])
    {
        $sql_str = "UPDATE yueyue_user_data_db.new_cameraman_data_tbl_201504 SET waipai_num = $waipai_data[C], waipai_pay = '{$waipai_data[SUM_PRICE]}' WHERE yue_user_id=$val[yue_user_id]";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 22);
    }
}
*/
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');

$sql_str = "SELECT location_id
FROM yueyue_user_data_db.new_cameraman_data_tbl_201504
GROUP BY location_id";
$result =db_simple_getdata($sql_str, FALSE, 22);
foreach($result AS $key=>$val)
{
    $system_obj  = POCO::singleton('event_system_class');
    $location_array = $system_obj->get_city_name_by_location_id($val[location_id]);
    if($location_array[0])
    {
        $sql_str = "UPDATE yueyue_user_data_db.new_cameraman_data_tbl_201504 SET location_name='{$location_array[0]}' WHERE location_id=$val[location_id]";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 22);
    }
}

function get_name_by_location_id($location_id)
{
    $location_name = '';

    return $location_name;
}

function get_poco_id_by_yue_id($yue_user_id)
{
    $yue_user_id = (int)$yue_user_id;
    $poco_id = 0;
    if(!empty($yue_user_id))
    {
        $sql_str = "SELECT poco_id FROM pai_db.pai_relate_poco_tbl WHERE user_id=$yue_user_id";
        $result = db_simple_getdata($sql_str, TRUE, 101);
        if($result) $poco_id = $result['poco_id'];

    }
    return $poco_id;
}

function get_yuepai_data($yue_user_id)
{
    $yue_user_id = (int)$yue_user_id;
    if (!empty($yue_user_id))
    {
        $sql_str = "SELECT 	COUNT(date_id) AS C, SUM(date_price) AS SUM_PRICE, SUM(discount_price) AS DIS_PRICE
                        FROM `event_db`.`event_date_tbl`
                        WHERE from_date_id = $yue_user_id AND date_status = 'confirm'";
        echo $sql_str . "<BR>";
        $result = db_simple_getdata($sql_str, TRUE);
        print_r($result);
        if($result) return $result;
    }
    return FALSE;
}

function get_waipai_data($poco_user_id)
{
    $poco_user_id = (int)$poco_user_id;
    if(!empty($poco_user_id))
    {
        $sql_str = "SELECT 	COUNT(enroll_id) AS C, SUM(original_price) AS SUM_PRICE
                        FROM  `event_db`.`event_enroll_tbl`
                        WHERE user_id=$poco_user_id AND table_id > 0";
        $result = db_simple_getdata($sql_str, TRUE);
        print_r($result);
        if($result) return $result;
    }
    return FALSE;
}
?>