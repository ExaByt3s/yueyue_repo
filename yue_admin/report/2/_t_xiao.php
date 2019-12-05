<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/5/27
 * @Time:   14:11
 * version: 1.0
 */
include_once('../../common.inc.php');

/*$mall_obj = POCO::singleton('pai_mall_seller_class');

$user_id = intval($yue_login_id);

$ret = $mall_obj->get_seller_info($user_id,2);

//print_r($ret);
//exit;
$info = $mall_obj->get_seller_profile(13);//根据id获取个人信息
print_r($info);*/

$add_time = trim($_INPUT['add_time']);
$role     = trim($_INPUT['role']);
if(strlen($add_time)<1 || strlen($role) <1 )
{
    echo "请传入add_time 和role参数";
    exit;
}
$location_arr = array(
    0=> array('loctaion_id' =>101015001,'name' => '西安')
    /*0=> array('loctaion_id' =>101029,'name' => '广东'),
    1=> array('loctaion_id' =>101024,'name' => '新疆'),
    2=> array('loctaion_id' =>101001,'name' => '北京'),
    3=> array('loctaion_id' =>101004,'name' => '重庆')*/
);
foreach($location_arr as $key=>$val)
{
   /*$sql_str = "SELECT FROM_UNIXTIME(add_time,'%Y-%m-%d') as add_time2,COUNT(*) as summ from pai_db.pai_user_tbl where
                    role='{$role}' AND FROM_UNIXTIME(add_time,'%Y-%m-%d') >= '{$add_time}'
                    AND LEFT(location_id,6)={$val['loctaion_id']} GROUP BY FROM_UNIXTIME(add_time,'%Y-%m-%d')";*/
   $sql_str = "SELECT FROM_UNIXTIME(add_time,'%Y-%m-%d') as add_time2,COUNT(*) as summ from pai_db.pai_user_tbl where role='{$role}' AND FROM_UNIXTIME(add_time,'%Y-%m-%d') >= '{$add_time}' AND location_id={$val['loctaion_id']} GROUP BY FROM_UNIXTIME(add_time,'%Y-%m-%d')";
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


