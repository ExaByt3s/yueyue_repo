<?php
/**
 * @desc:   测试获取数据
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   14:08
 * version: 1.0
 */
include_once('common.inc.php');
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); //公共类
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$cms_obj = new cms_system_class();
// +--------------------------------------------------------------------------------------------------------------------
// |榜单测试
// +--------------------------------------------------------------------------------------------------------------------
$ico_result = $cms_obj->get_last_issue_record_list(false, '0,8', 'place_number ASC', 587);  // 不多于八个
print_r($ico_result);
exit;

$type_id = (int)$_INPUT['type_id'];
if($type_id <1) exit('分类不能为空');
$sql_str = "SELECT DISTINCT(type_name) FROM yueyue_interface_db.interface_data_tbl WHERE type_id={$type_id} AND type_name!='' ORDER BY type_name DESC";
$result = db_simple_getdata($sql_str, false, 22);
echo "<table>";
echo "<tr>";
echo "<th>用户ID</th>";
foreach($result as $key=>$val)
{
    $type_name = trim($val['type_name']);
    echo "<th>{$type_name}</th>";
}
echo "</tr>";
$user_list = get_interface_user_list($type_id);
if(!is_array($user_list)) $user_list = array();
$sql_in_str = '';
foreach($user_list as $v)
{
    if(strlen($sql_in_str)>0) $sql_in_str .= ',';
    $sql_in_str .= $v['user_id'];
}
if(strlen($sql_in_str)>0)
{

}
/*$type_list = get_interface_type_count_list($type_id,$v['user_id'],$result);
if(!is_array($type_list)) $type_list = array();

echo "<tr>";
echo "<td>{$v['user_id']}</td>";
foreach($type_list as $vo)
{
    echo "<td>{$vo}</td>";
}
echo "</tr>";*/
echo "<table>";


//获取分类下的所有用户
function get_interface_user_list($type_id)
{
    $type_id = (int)$type_id;
    if($type_id <1) return false;
    $sql_str = "SELECT DISTINCT(user_id) FROM yueyue_interface_db.interface_data_tbl WHERE type_id={$type_id} AND user_id !=0";
    $result = db_simple_getdata($sql_str, false, 22);
    if(!is_array($result)) $result = array();
    return $result;
}

//获取商家信息
function get_interface_type_count_list($type_id,$sql_in_str,$result)
{
    $return_data = array();
    $type_id = (int)$type_id;
    $sql_in_str = trim($sql_in_str);
    if($type_id <1 || strlen($sql_in_str)) return false;
    /* $sql_str = "SELECT DISTINCT(type_name) FROM yueyue_interface_db.interface_data_tbl WHERE type_id={$type_id} AND type_name!='' ORDER BY type_name DESC";
     $result = db_simple_getdata($sql_str, false, 22);*/
    if(!is_array($result)) $result = array();
    foreach($result as $key=>$v)
    {
        $type_name = trim($v['type_name']);
        $sql_str = "SELECT count(*) AS C,user_id FROM yueyue_interface_db.interface_data_tbl WHERE type_name='".mysql_escape_string($type_name)."' AND user_id IN ({$sql_in_str}) AND type_id={$type_id} AND user_id !=0 GROUP BY user_id ";
        $user_ret = db_simple_getdata($sql_str, false, 22);
        $return_data[$type_name] = (int)$user_ret['C'];
    }
    return $return_data;
}



exit;
// +--------------------------------------------------------------------------------------------------------------------
// |下面是屏蔽测试
// +--------------------------------------------------------------------------------------------------------------------
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$user_arr = array(195679,201819,204249,204325,199461,199077,134398,188112,188250,185799,116843,171944,173927,155798,164864,162188,158937,123914,123914,152214,132820,133433,132984,117359,132908,132913,124728,132592,127051,132033,128061,110050,129741,129190,126193,121803,128198,128223,128223,127968,117277,115164,127417,113538,117013,124114,117166,115598,117907,115676,111339,118158,115426,109690,105293,113315,114851,114851,114851,113406,103807,105742,100452,113196,111802,106883,111874,102933,111629,110043,111306,110952,110952,110880,103401,103999,107212,109809,109356,109365,109246,105070,103921,109263,107421,109356,109356,109742,109356,108518,108694,108735,104888,102334,107719,102596,107203,106720,104135,105911,105768,105289);

$inform_shield_obj = POCO::singleton('pai_log_inform_shield_class');

foreach($user_arr as $user_id)
{
    $user_id = (int)$user_id;
    if($user_id >0)
    {
        echo $user_id.'<br/>';
        $inform_shield_obj->shield_user($user_id);
    }
}

exit;

/*
 *
 * //这里是三人影相的
$arr = array(46171,46371,46411,46435,46437,46518,46562,46632,46695,46746,46702,46793,46796,46820,46856,46997,47016,47065,47067,47103,47113,47128,47190,47304,47338,47339,47340,47439,47453,47465,47492,47538,47579,47628,47648,47712,47748,47749,47750,47751,47783,48003,48002,48014,48457,48467,48593,48757,48980,48981,49139,49153,49188,49233,49333,49374,49696,49792,49914,49976,50000,50310,50311,50312,50396,50459,50462,50463,50485,50519,50696,50801,50973,51459,51501,51502,51557,51832,51930,51931,52242,52243,52248,52262,52272,52305,52344,52983,53117,53189,53190,53961,53962,54167,54168,54418,55012,55308,55235,55524,56492);

foreach($arr as $event_id)
{
    $detail_ret = array();
    $ret= array();
    $event_id = intval($event_id);
    $sql_detail_sql = "SELECT address,budget,title FROM event_db.event_details_tbl WHERE  event_id={$event_id}";
    $detail_ret = db_simple_getdata($sql_detail_sql,true);
    $sql_str = "SELECT event_id,from_date_id,to_date_id,enroll_num,(enroll_num*budget) as price,FROM_UNIXTIME(complete_time,'%Y-%m-%d %H:%i:%s') AS complete_time FROM yueyue_stat_db.yueyue_event_order_tbl WHERE event_id={$event_id} AND date_id=0 AND pay_status=1 AND event_status='2' AND enroll_status=1 AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')>='2015-04-01' AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')<='2015-06-30'";
    $ret = db_simple_getdata($sql_str,false,22);
    if(!is_array($ret)) $ret = array();
    echo "<table>";
    foreach($ret as $vo)
    {
        echo "<tr><td style='border: 1px solid #000;'>{$vo['to_date_id']}</td>";
        echo "<td style='border: 1px solid #000;'>{$detail_ret['title']}</td>";
        echo "<td style='border: 1px solid #000;'>{$vo['complete_time']}</td>";
        echo "<td style='border: 1px solid #000;'>{$detail_ret['address']}</td>";
        echo "<td style='border: 1px solid #000;'>{$vo['enroll_num']}</td>";
        echo "<td style='border: 1px solid #000;'>{$detail_ret['budget']}</td>";
        echo "<td style='border: 1px solid #000;'>{$vo['price']}</td>";
        echo "<td style='border: 1px solid #000;'>{$vo['from_date_id']}</td></tr>";
    }
    echo "</table>";
}
*/
/*$sql_str = "SELECT user_id,location_id FROM pai_db.pai_user_tbl WHERE location_id IN (101029001,101001001,101003001,101004001)";
$list = db_simple_getdata($sql_str,false,101);
var_dump($list);
exit;*/


$sql_str = "SELECT d.to_date_id,COUNT(d.date_id) as d_num from event_db.event_date_tbl d,event_db.event_details_tbl t
WHERE d.event_id=t.event_id AND t.event_status='2' AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')>='2015-07-01'
AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')<='2015-07-31' GROUP BY d.to_date_id ORDER BY d_num desc";
$list = db_simple_getdata($sql_str,false);
if(!is_array($list)) $list = array();

echo "<table>";
foreach($list as $key=>$val)
{
    $sql_tmp_str1 = "SELECT count(d.date_id) as d_count from event_db.event_date_tbl d,event_db.event_details_tbl t
WHERE d.event_id=t.event_id AND t.event_status='2' AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')>='2015-06-16'
AND FROM_UNIXTIME(complete_time,'%Y-%m-%d')<='2015-06-30' AND d.to_date_id={$val['to_date_id']} limit 0,1";
    $ret1 = db_simple_getdata($sql_tmp_str1,true);
    $num = intval($ret1['d_count']);

    if($num == 0)
    {
        $sql_tmp_str2 ="SELECT  u.location_id,u.user_id FROM pai_db.pai_model_audit_tbl a,pai_db.pai_user_tbl u WHERE a.is_approval=1 AND a.user_id={$val['to_date_id']} AND a.user_id=u.user_id AND u.location_id IN (101029001,101001001,101003001,101004001) limit 0,1";
        $ret2 = db_simple_getdata($sql_tmp_str2,true,101);
        if(is_array($ret2) && !empty($ret2))
        {
            $nickname = get_user_nickname_by_user_id($val['to_date_id']);
            $city = get_poco_location_name_by_location_id($ret2['location_id']);
            echo "<tr>";
            echo "<td style='border: 1px solid #000;'>{$nickname}</td>";
            echo "<td style='border: 1px solid #000;'>{$val['to_date_id']}</td>";
            echo "<td style='border: 1px solid #000;'>{$val['d_num']}</td>";
            echo "<td style='border: 1px solid #000;'>{$city}</td>";
            echo "</tr>";
        }
    }




    //print_r($ret);
    /*$num = intval($ret['d_count']);
    if($num==0)
    {
        $nickname = get_user_nickname_by_user_id($val['to_date_id']);
        echo "<tr>";
        echo "<td style='border: 1px solid #000;'>{$nickname}</td>";
        echo "<td style='border: 1px solid #000;'>{$val['to_date_id']}</td>";
        echo "<td style='border: 1px solid #000;'>{$val['d_num']}</td>";
        echo "</tr>";
    }*/
}
echo "</table>";