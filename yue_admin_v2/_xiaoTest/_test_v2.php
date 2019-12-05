<?php
/**
 * @desc:   导出案例
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/12
 * @Time:   11:06
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); //公共类

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
foreach($user_list as $v)
{
    $type_list = get_interface_type_count_list($type_id,$v['user_id'],$result);
    if(!is_array($type_list)) $type_list = array();

    echo "<tr>";
    echo "<td>{$v['user_id']}</td>";
    foreach($type_list as $vo)
    {
        echo "<td>{$vo}</td>";
    }
    echo "</tr>";
}

echo "<table>";


//获取分类下的所有用户
function get_interface_user_list($type_id)
{
    $type_id = (int)$type_id;
    if($type_id <1) return false;
    $sql_str = "SELECT DISTINCT(user_id) FROM yueyue_interface_db.interface_data_tbl WHERE type_id={$type_id} AND user_id !=0 LIMIT 1000,1000";
    $result = db_simple_getdata($sql_str, false, 22);
    if(!is_array($result)) $result = array();
    return $result;
}

//获取商家信息
function get_interface_type_count_list($type_id,$user_id,$result)
{
    $return_data = array();
    $type_id = (int)$type_id;
    $user_id = (int)$user_id;
    if($type_id <1 || $user_id<1) return false;
   /* $sql_str = "SELECT DISTINCT(type_name) FROM yueyue_interface_db.interface_data_tbl WHERE type_id={$type_id} AND type_name!='' ORDER BY type_name DESC";
    $result = db_simple_getdata($sql_str, false, 22);*/
    if(!is_array($result)) $result = array();
    foreach($result as $key=>$v)
    {
        $type_name = trim($v['type_name']);
        $sql_str = "SELECT count(*) AS C FROM yueyue_interface_db.interface_data_tbl WHERE type_name='".mysql_escape_string($type_name)."' AND user_id={$user_id} AND type_id={$type_id}";
        $user_ret = db_simple_getdata($sql_str, true, 22);
        //$return_data['user_id'] = $user_id;
        $return_data[$type_name] = (int)$user_ret['C'];
    }
    return $return_data;
}



exit;
// +--------------------------------------------------------------------------------------------------------------------
// |发送消息测试
// +--------------------------------------------------------------------------------------------------------------------
$event_mass_message_obj = POCO::singleton('pai_event_mass_message_class');
$send_user_id = 100293;
$send_role = "yuebuyer";
$user_arr = array(117452);
$content = '伟标要吃了';
$type = 'text';
 echo "ok";
$ret = $event_mass_message_obj->start_mass_message($send_user_id,$send_role,$user_arr,$content,$type);
var_dump($ret);


exit;
$pai_model_relate_org_obj = POCO::singleton( 'pai_model_relate_org_class' );
$pai_organization_obj = POCO::singleton("pai_organization_class");//机构库

$sql_str ="SELECT DISTINCT(p.user_id),p.type_id,m.location_id,m.user_name,m.name,m.onsale_num,m.goods_num FROM mall_db.mall_seller_profile_tbl p,mall_db.mall_seller_tbl m WHERE m.status=1 AND p.user_id=m.user_id GROUP BY p.user_id LIMIT 5000,1000";

$result = db_simple_getdata($sql_str, false, 101);
if(!is_array($result)) $result = array();

$list = array();
echo "<table border='1'>";
echo "<tr>";
echo "<th>商家ID</th>";
echo "<th>商家昵称</th>";
echo "<th>手机号码</th>";
echo "<th>商家地区</th>";
echo "<th>商家认证品类</th>";
echo "<th>在线商品数</th>";
echo "<th>全部商品数</th>";
echo "<th>商家等级</th>";
echo "<th>APP商家端最后登录时间</th>";
echo "<th>所属机构</th>";
echo "<th>交易额</th>";
echo "<th>交易次数</th>";
echo "<th>被收藏数</th>";
echo "</tr>";
foreach($result as $val)
{
    $userphone = get_nickname_info_by_user_id($val['user_id']);//电话号码
    $location_name = get_location_name_by_location_id($val['location_id']);
    $type_result = get_type_name($val['type_id'],$val['user_id']);//分类和等级
    $last_time = get_seller_last_time($val['user_id']);//最后登陆时间
    $org_username = get_org_nickname_by_user_id($val['user_id']);//机构名
    $order_result = get_order_result($val['user_id']);
    $follow_num = get_be_follow_count($val['user_id']);//被收藏数
    echo "<tr>";
    echo "<td align='center'>{$val['user_id']}</td>"; //销售额
    echo "<td align='center'>{$val['name']}</td>";
    echo "<td align='center'>{$userphone}</td>";
    echo "<td align='center'>{$location_name}</td>";
    echo "<td align='center'>{$type_result['type_str']}</td>";
    echo "<td align='center'>{$val['onsale_num']}</td>";
    echo "<td align='center'>{$val['goods_num']}</td>";
    echo "<td align='center'>{$type_result['type_level_str']}</td>";
    echo "<td align='center'>{$last_time}</td>";
    echo "<td align='center'>{$org_username}</td>";
    echo "<td align='center'>{$order_result['order_num']}</td>";
    echo "<td align='center'>{$order_result['total_price']}</td>";
    echo "<td align='center'>{$follow_num}</td>";
    echo "</tr>";
}
echo "</table>";
//print_r($result);




//获取用户ABCD等级
function get_rating_level_by_seller_user_id($seller_user_id,$type_id)
{
    $mall_obj = POCO::singleton('pai_mall_seller_class');
    $rating_result =pai_mall_load_config('seller_rating');//加载等级文件
    $seller_user_id = (int)$seller_user_id;
    $type_id = (int)$type_id;
    if($seller_user_id <1 || $type_id <1) return '--';
    $result = $mall_obj->get_seller_rating($seller_user_id);
    if(strlen($result) <1) return '--';
    $result = explode(",",$result);
    if(!is_array($result) || empty($result)) return '--';
    $rating_level = 0;
    foreach($result as $key=>$v)
    {
        list($type_val,$rating_level_val) = explode('-',$v);
        if($type_id == $type_val)
        {
            $rating_level = $rating_level_val;
            break;
        }
    }
    if($type_id <1 || $rating_level <0) return '--';
    return $rating_result[$type_id][$rating_level]['text'];
}

/*$type_str = "3,31,40,12,5,41";
$seller_user_id = 100036;
get_type_name($type_str,$seller_user_id);*/
//echo get_seller_last_time(100293);

//获取商家等级和分类
function get_type_name($type_str,$seller_user_id)
{
    $result = array('type_str'=>'','type_level_str'=>'');
    $pai_mall_type_obj = POCO::singleton('pai_mall_goods_type_class');
    $type_str = trim($type_str);
    $seller_user_id = (int)$seller_user_id;
    if(strlen($type_str) <1 ||$seller_user_id<1) return false;
    $type_arr = explode(',',$type_str);
    if(!is_array($type_arr)) $type_arr = array();

    foreach($type_arr as $type_id)
    {
        $type_id = (int)$type_id;
        $ret = $pai_mall_type_obj->get_type_info($type_id);
        if(!is_array($ret)) $ret = array();
        //获取type_name
        if(strlen($result['type_str'])>0) $result['type_str'] .= '、';
        $result['type_str'] .= "{$ret['name']}";
        //等级
        if(strlen($result['type_level_str'])) $result['type_level_str'] .= '、';
        $result['type_level_str'] .= "{$ret['name']}=>".get_rating_level_by_seller_user_id($seller_user_id,$type_id);
    }
    return $result;
}

//获取最后登陆时间
function get_seller_last_time($seller_user_id)
{
    $seller_user_id = (int)$seller_user_id;
    if($seller_user_id <1) return 'xxxx';
    $sql_str = "SELECT last_login_time FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201511 WHERE user_id={$seller_user_id} AND app_role='yueseller' ORDER BY last_login_time DESC limit 0,1";
    $result = db_simple_getdata($sql_str, true, 22);
    if(!$result)
    {
        $sql_str = "SELECT last_login_time FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201510 WHERE user_id={$seller_user_id} AND app_role='yueseller' ORDER BY last_login_time DESC limit 0,1";
        $result = db_simple_getdata($sql_str, true, 22);
        if(!$result)
        {
            $sql_str = "SELECT last_login_time FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201509 WHERE user_id={$seller_user_id} AND app_role='yueseller' ORDER BY last_login_time DESC limit 0,1";
            $result = db_simple_getdata($sql_str, true, 22);
        }
    }
    if(!is_array($result)) $result = array();
    if(empty($result['last_login_time'])) return 'xxxx';
    return date('m月d日',strtotime($result['last_login_time']));
}

//被收藏数
function get_be_follow_count($seller_user_id)
{
    $seller_user_id = (int)$seller_user_id;
    if($seller_user_id <1) return 0;
    $sql_str = "SELECT COUNT(*) AS C FROM mall_db.mall_follow_user_tbl WHERE follow_type='collect' AND be_follow_user_id={$seller_user_id}";
    $result = db_simple_getdata($sql_str, true, 101);
    if(!is_array($result)) $result = array();
    return (int)$result['C'];
}

//获取订单数
function get_order_result($seller_user_id)
{
    $seller_user_id = (int)$seller_user_id;
    if($seller_user_id <1) return 0;
    $sql_str = "SELECT COUNT(*) AS order_num,sum(total_amount) AS total_price FROM mall_db.mall_order_tbl WHERE status=8 AND seller_user_id={$seller_user_id}";
    $result = db_simple_getdata($sql_str, true, 101);
    if(!is_array($result)) $result = array();
    return $result;

}

//获取机构名
function get_org_nickname_by_user_id($seller_user_id)
{
    $seller_user_id = (int)$seller_user_id;
    if($seller_user_id <1) return 0;
    $sql_str = "SELECT o.nick_name FROM pai_user_library_db.model_relation_org_tbl m,pai_user_library_db.organization_tbl o WHERE m.user_id={$seller_user_id} AND m.priority=1 AND m.org_id=o.user_id";
    $result = db_simple_getdata($sql_str, true, 101);
    if(!is_array($result)) $result = array();
    if(empty($result['nick_name'])) return '--';
    return trim($result['nick_name']);
}

//获取地区
function get_location_name_by_location_id($location_id)
{
    include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
    $location_name = get_poco_location_name_by_location_id($location_id);
    return $location_name;
}

//手机号码
function get_nickname_info_by_user_id($seller_user_id)
{
    $seller_user_id = (int)$seller_user_id;
    if($seller_user_id <1) return 0;
    $sql_str = "SELECT cellphone FROM pai_db.pai_user_tbl WHERE user_id={$seller_user_id}";
    $result = db_simple_getdata($sql_str, true, 101);
    if(!is_array($result)) $result = array();
    return $result['cellphone'];
}



