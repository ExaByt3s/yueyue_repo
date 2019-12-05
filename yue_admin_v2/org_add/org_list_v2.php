<?php
/**
 * @desc:   机构列表v2
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/9
 * @Time:   9:46
 * version: 2.0
 */
include("common.inc.php");
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php"); //获取地区

$organization_obj = POCO::singleton('pai_organization_class');
$user_obj         = POCO::singleton('pai_user_class');
$model_relate_obj = POCO::singleton('pai_model_relate_org_class');
/*旧的**/
$payment_obj      = POCO::singleton('pai_payment_class');
$order_obj        = POCO::singleton ( 'pai_order_org_class' );
/**/
$mall_obj = POCO::singleton('pai_mall_seller_class'); //店铺类
$mall_order_obj = POCO::singleton ( 'pai_mall_order_class' );
$page_obj          = new show_page ();
$show_count        = 20;
$tpl = new SmartTemplate("org_list_v2.tpl.htm");

$user_id     = intval($_INPUT['user_id']);
$nick_name   = trim($_INPUT['nick_name']);//机构名
$link_man    = trim($_INPUT['link_man']); //联系人姓名
$start_time  = trim($_INPUT['start_time']);
$end_time    = trim($_INPUT['end_time']);
$province    = intval($_INPUT['province']);
$location_id = intval($_INPUT['location_id']);

//初始化,这里要分页的话，需要链表查询o,表示机构表,u表示用户表
$where_str = "u.user_id = o.user_id";
$setParam  = array();

if($user_id >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "o.user_id = {$user_id}";
    $setParam['user_id'] = $user_id;
}
if(strlen($nick_name) >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "o.nick_name like '%".mysql_escape_string($nick_name)."%'";
    $setParam['nick_name'] = $nick_name;
}
if(strlen($link_man) >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "o.link_man like '%".mysql_escape_string($link_man)."%'";
    $setParam['link_man'] = $link_man;
}
if(strlen($start_time) >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(o.add_time,'%Y-%m-%d') >= '".mysql_escape_string($start_time)."'";
    $setParam['start_time'] = $start_time;
}
if(strlen($end_time) >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(o.add_time,'%Y-%m-%d') <= '".mysql_escape_string($end_time)."'";
    $setParam['end_time'] = $end_time;
}
if($province >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    if($location_id >0)
    {
        $where_str .= "u.location_id = {$location_id}";
        $setParam['location_id'] = $location_id;
    }
    else
    {
        $where_str .= "LEFT(u.location_id,6) = {$province}";
    }
    $setParam['province'] = $province;
}
//查询语句
$sql_str = "pai_db.pai_user_tbl u,pai_user_library_db.organization_tbl o WHERE o.user_id = u.user_id";
if(strlen($where_str) >0)
{
    $sql_str .= ' AND ';
    $sql_str .= "{$where_str}";
}
//查询机构
$page_obj->setvar($setParam);
$total_count =  get_org_list(true, $sql_str,'', '','o.user_id');
$page_obj->set($show_count,$total_count);
$list = get_org_list(false, $sql_str,'o.add_time DESC,o.id DESC', $page_obj->limit(),'o.*,u.location_id');


if(!is_array($list)) $list = array();
$begin_time = strtotime(date('Y-m-d 00:00:00',time()-31*24*3600));
$end_time   = strtotime(date('Y-m-d 23:59:59',time()-24*3600));
$date_tmp_str = "sign_time BETWEEN {$begin_time} AND $end_time";
//订单sql
foreach($list as $key=>$val)
{
    $date_sql_str = '';
    $list[$key]['city']  = get_poco_location_name_by_location_id($val['location_id']);
    $list[$key]['store_num']   = intval(get_store_num(true,$val['user_id']));
    $list[$key]['is_store_num']= intval(get_store_num(true,$val['user_id'],1));
    $date_sql_str = $date_tmp_str." AND org_user_id={$val['user_id']}";
    $list[$key]['unsettle']    = sprintf('%.2f',$payment_obj->get_unsettle_org_amount($val['user_id']));
    $ret = $mall_order_obj->get_order_list(0, -1, false, $date_sql_str, 'add_time DESC,order_id DESC','0,1','sum(total_amount) as true_budget,count(*) as pay_sum');
    $list[$key]['order_price'] = sprintf('%.2f',$ret[0]['true_budget']);
    $list[$key]['pay_sum']     = intval($ret[0]['pay_sum']);
}

/**
 * @param bool $b_select_count
 * @param int $user_id
 * @param int $status
 * @param string $where_str
 * @param string $sort
 * @param string $limit
 * @param string $fields
 * @return bool
 */
function get_store_num($b_select_count=false,$user_id =0,$status = 0,$where_str ='',$sort='store_id DESC',$limit='0,20',$fields='*')
{
    $model_relate_obj = POCO::singleton('pai_model_relate_org_class');
    $mall_obj = POCO::singleton('pai_mall_seller_class'); //店铺类
    $status = intval($status);
    $user_id = intval($user_id);
    if($user_id <1) return false;
    if(strlen($where_str)>1) $where_str .= ' AND ';
    $where_str .= "org_id={$user_id}";
    $user_arr = $model_relate_obj->get_model_org_list_by_org_id(false,$where_str, '0,99999999', 'id DESC','DISTINCT(user_id) AS user_id');
    /*global $yue_login_id;
    if($yue_login_id == 100293 && $user_id== 103078)
    {
      print_r($user_arr);
    }*/
    if(!is_array($user_arr)) $user_arr = array();
    $sql_tmp_str = '';
    foreach($user_arr as $key=>$val)
    {
        if($key !=0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $val['user_id'];
    }
    if(strlen($sql_tmp_str) >1)
    {
        $sql_str = "user_id IN ({$sql_tmp_str})";
        if($status >0)
        {
            if(strlen($sql_str) >0) $sql_str .= ' AND ';
            $sql_str .= "status={$status}";
        }
        if($b_select_count == true)
        {
            $ret =$mall_obj->get_seller_store_list(true, $sql_str,'','','DISTINCT(user_id)');
        }
        else
        {
            $ret = $mall_obj->get_seller_store_list(false, $sql_str,$sort,$limit,$fields);
        }
        return $ret;
    }
    return false;
}
/**
 * 查询机构
 * @param bool   $b_select_count  是否查询个数
 * @param string $sql_str            "pai_db.pai_user_tbl u,pai_user_library_db.organization_tbl o WHERE o.user_id = u.user_id“;
 * @param string $sort        排序
 * @param string $limit       循环条数
 * @param string $fields      查询字段
 * @return array|int
 */
function get_org_list($b_select_count=false, $sql_str, $sort='o.user_id DESC', $limit='0,20', $fields='o.*')
{
    if(strlen($sql_str) <1) return array();
    if($b_select_count == true)
    {
        $sql = "SELECT COUNT({$fields}) AS c FROM {$sql_str}";
        $tmp = db_simple_getdata($sql, true, 101);
        return (int)$tmp['c'];
    }
    else
    {
        //处理排序
        $sortby = $sort != '' ? " ORDER BY {$sort}" : '';
        // 处理 $limit
        if (!empty($limit) && preg_match("/^\d+,\d+$/i", $limit))
        {
            list($length, $offset) = explode(',', $limit);
        }
        else
        {
            //必须有limit 限制
            $length = 0;
            $offset = 1000;
        }
        $sql = "SELECT {$fields} FROM {$sql_str} {$sortby}";
        // 根据 $length 和 $offset 参数决定是否使用限定结果集的查询
        if (null !== $length || null !== $offset)
        {
            $sql = sprintf('%s LIMIT %d,%d', $sql, $length, (int)$offset);
        }
        $rows = db_simple_getdata($sql, false, 101);
        return $rows;
    }
}

$tpl->assign($setParam);
$tpl->assign('list', $list);
$tpl->assign('total_count', $total_count);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

