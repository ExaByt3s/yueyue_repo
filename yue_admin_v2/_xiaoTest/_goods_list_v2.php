<?php
/**
 * @desc:   商品测试
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/18
 * @Time:   11:07
 * version: 1.0
 */

include_once('common.inc.php');


$begin_month = '2015-10';
$prev_time = date('Y-m',strtotime($begin_month.'-01')-24*3600);











// +-----------------------------------------------------------------------------------
// | 获取【商城】月报表数据
// +-----------------------------------------------------------------------------------
/*
$type_id = intval($_INPUT['type_id']);
if($type_id <0) exit('type_id 不能为空');

//需要去掉的机构ID
global $org_user_str;
$org_user_str = '111715,119879,124214';
echo "<table>";
echo "<tr><th colspan='3'>{$type_id}</th><th colspan='2'>{$begin_month}-》{$prev_time}</th></tr>";
echo "<tr>";
echo "<th>月总额</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>订单数</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>平均客单价</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>消费人数（去重）</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>复购率</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>留存率</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>新客率</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>有收入供应商数量</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>供应商平均收入</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>动销率</th>"; //209个用户
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>新增有收入供应商</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "</tr>";

//月总额
$this_order_ret = get_order_date($begin_month,$type_id);//本月
$prev_order_ret = get_order_date($prev_time,$type_id); //上月
$this_order_ret['order_hb_scala'] = sprintf('%.4f',$this_order_ret['total_price']/$prev_order_ret['total_price']);//环比
$this_order_ret['order_tb_scala'] = sprintf('%.4f',($this_order_ret['total_price']-$prev_order_ret['total_price'])/$prev_order_ret['total_price']); //同比
//订单
$this_order_ret['order_num_hb_scala'] = sprintf('%.4f',$this_order_ret['C']/$prev_order_ret['C']);
$this_order_ret['order_num_tb_scala'] = sprintf('%.4f',($this_order_ret['C']-$prev_order_ret['C'])/$prev_order_ret['C']);

//客的数量和商家数量
$this_user_ret = get_buyer_count($begin_month,$type_id);
$prev_user_ret = get_buyer_count($prev_time,$type_id);
//上上个月的
$prev_prev_user_ret = get_buyer_count(date('Y-m',strtotime($prev_time.'-01')-24*3600),$type_id);

$avg_buyer_price = sprintf('%.4f',$this_order_ret['total_price']/$this_user_ret['b']);
$prev_avg_buyer_price = sprintf('%.4f',$prev_order_ret['total_price']/$prev_user_ret['b']);
$this_user_ret['avg_buyer_price'] = $avg_buyer_price;
$this_user_ret['avg_buyer_price_hb_scala'] = sprintf('%.4f',$avg_buyer_price/$prev_avg_buyer_price);//环比
$this_user_ret['avg_buyer_price_tb_scala'] = sprintf('%.4f',($avg_buyer_price-$prev_avg_buyer_price)/$prev_avg_buyer_price);//同比
//消费者
$this_user_ret['b_hb_scala'] = sprintf('%.4f',$this_user_ret['b']/$prev_user_ret['b']); //环比
$this_user_ret['b_tb_scala'] = sprintf('%.4f',($this_user_ret['b']-$prev_user_ret['b'])/$prev_user_ret['b']); //同比

$new_twice_ret = array();
//复购率【（订单数-消费人数）/订单数】
$this_twice_pay = sprintf('%.4f',($this_order_ret['C']-$this_user_ret['b'])/$this_order_ret['C']);
$prev_twice_pay = sprintf('%.4f',($prev_order_ret['C']-$prev_user_ret['b'])/$prev_order_ret['C']);
$new_twice_ret['twice_pay'] = $this_twice_pay;
$new_twice_ret['twice_pay_hb_scala'] = sprintf('%.4f',$this_twice_pay/$prev_twice_pay);
$new_twice_ret['twice_pay_tb_scala'] = sprintf('%.4f',($this_twice_pay-$prev_twice_pay)/$prev_twice_pay);
//用户留存率
$new_user_twice_ret = array();
$this_user_twice_num = sprintf('%.4f',get_twice_info($begin_month,$type_id)/$prev_user_ret['b']);
$prev_user_twice_num = sprintf('%.4f',get_twice_info($prev_time,$type_id)/$prev_prev_user_ret['b']);
$new_user_twice_ret['user_twice_num'] = $this_user_twice_num;
$new_user_twice_ret['user_twice_num_hb_scala'] = sprintf('%.4f',$this_user_twice_num/$prev_user_twice_num);
$new_user_twice_ret['user_twice_num_tb_scala'] = sprintf('%.4f',($this_user_twice_num-$prev_user_twice_num)/$prev_user_twice_num); //同比
//新客率
$new_user_ret = array();
$this_new_user_num = get_new_user_num($begin_month,$type_id,$this_user_ret['b']);
$prev_new_user_num = get_new_user_num($prev_time,$type_id,$prev_user_ret['b']);
$new_user_ret['new_user_num'] = $this_new_user_num;
$new_user_ret['new_user_num_hb_scala'] = sprintf('%.4f',$this_new_user_num/$prev_new_user_num);
$new_user_ret['new_user_num_tb_scala'] = sprintf('%.4f',($this_new_user_num-$prev_new_user_num)/$prev_new_user_num);


//供应商处理
$this_user_ret['s_hb_scala'] = sprintf('%.4f',$this_user_ret['s']/$prev_user_ret['s']);
$this_user_ret['s_tb_scala'] = sprintf('%.4f',($this_user_ret['s']-$prev_user_ret['s'])/$prev_user_ret['s']);
//供应商平均收入
$avg_seller_price = sprintf('%.4f',$this_order_ret['total_price']/$this_user_ret['s']);
$prev_avg_seller_price = sprintf('%.4f',$prev_order_ret['total_price']/$prev_user_ret['s']);
$this_user_ret['avg_seller_price'] = $avg_seller_price;
$this_user_ret['avg_seller_price_hb_scala'] = sprintf('%.4f',$avg_seller_price/$prev_avg_seller_price);
$this_user_ret['avg_seller_price_tb_scala'] = sprintf('%.4f',($avg_seller_price-$prev_avg_seller_price)/$prev_avg_seller_price); //同比

//获取用户动销率
$new_pin_arr = array();
$this_user_pin = get_seller_count($begin_month,$type_id,$this_user_ret['s']);
$prev_user_pin = get_seller_count($prev_time,$type_id,$prev_user_ret['s']);
$new_pin_arr['user_pin'] =$this_user_pin;
$new_pin_arr['user_pin_hb_scala'] = sprintf('%.4f',$this_user_pin/$prev_user_pin);
$new_pin_arr['user_pin_tb_scala'] = sprintf('%.4f',($this_user_pin-$prev_user_pin)/$prev_user_pin);

//新增有收入供应商
$new_seller_ret = array();
$this_new_seller_num = get_new_seller_num($begin_month,$type_id,$this_user_ret['s']);
$prev_new_seller_num = get_new_seller_num($prev_time,$type_id,$prev_user_ret['s']);
$new_seller_ret['new_seller_num'] = $this_new_seller_num;
$new_seller_ret['new_seller_num_hb_scala'] =sprintf('%.4f',$this_new_seller_num/$prev_new_seller_num);
$new_seller_ret['new_seller_num_tb_scala'] =sprintf('%.4f',($this_new_seller_num-$prev_new_seller_num)/$prev_new_seller_num);

echo "<tr>";
echo "<td align='center'>{$this_order_ret['total_price']}</td>";
echo "<td align='center'>{$this_order_ret['order_hb_scala']}</td>";
echo "<td align='center'>{$this_order_ret['order_tb_scala']}</td>";
echo "<td align='center'>{$this_order_ret['C']}</td>";
echo "<td align='center'>{$this_order_ret['order_num_hb_scala']}</td>";
echo "<td align='center'>{$this_order_ret['order_num_tb_scala']}</td>";
//平均客单价
echo "<td align='center'>{$this_user_ret['avg_buyer_price']}</td>";
echo "<td align='center'>{$this_user_ret['avg_buyer_price_hb_scala']}</td>";
echo "<td align='center'>{$this_user_ret['avg_buyer_price_tb_scala'] }</td>";
//消费者
echo "<td align='center'>{$this_user_ret['b']}</td>";
echo "<td align='center'>{$this_user_ret['b_hb_scala']}</td>";
echo "<td align='center'>{$this_user_ret['b_tb_scala']}</td>";
//复购率
echo "<td align='center'>{$new_twice_ret['twice_pay']}</td>";
echo "<td align='center'>{$new_twice_ret['twice_pay_hb_scala']}</td>";
echo "<td align='center'>{$new_twice_ret['twice_pay_tb_scala']}</td>";
//消费留存率
echo "<td align='center'>{$new_user_twice_ret['user_twice_num']}</td>";
echo "<td align='center'>{$new_user_twice_ret['user_twice_num_hb_scala']}</td>";
echo "<td align='center'>{$new_user_twice_ret['user_twice_num_tb_scala']}</td>";
//新客率
echo "<td align='center'>{$new_user_ret['new_user_num']}</td>";
echo "<td align='center'>{$new_user_ret['new_user_num_hb_scala']}</td>";
echo "<td align='center'>{$new_user_ret['new_user_num_tb_scala']}</td>";
//有收入供应商数量
echo "<td align='center'>{$this_user_ret['s']}</td>";
echo "<td align='center'>{$this_user_ret['s_hb_scala']}</td>";
echo "<td align='center'>{$this_user_ret['s_tb_scala']}</td>";
//供应商平均收入
echo "<td align='center'>{$this_user_ret['avg_seller_price']}</td>";
echo "<td align='center'>{$this_user_ret['avg_seller_price_hb_scala']}</td>";
echo "<td align='center'>{$this_user_ret['avg_seller_price_tb_scala']}</td>";
//动销率
echo "<td align='center'>{$new_pin_arr['user_pin']}</td>";
echo "<td align='center'>{$new_pin_arr['user_pin_hb_scala']}</td>";
echo "<td align='center'>{$new_pin_arr['user_pin_tb_scala']}</td>";
//新增有收入供应商
echo "<td align='center'>{$new_seller_ret['new_seller_num']}</td>";
echo "<td align='center'>{$new_seller_ret['new_seller_num_hb_scala']}</td>";
echo "<td align='center'>{$new_pin_arr['user_pin_tb_scala']}</td>";
echo "</tr>";
echo "<table>";


//获取订单数和金额
function get_order_date($begin_month,$type_id)
{
    global $org_user_str;
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month) || $type_id <1) return false;
    $sql_str ="SELECT count(*) AS C,SUM(total_amount) AS total_price,AVG(total_amount) AS avg_price FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$begin_month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//获取消费者个数和商家个数
function get_buyer_count($begin_month,$type_id)
{
    global $org_user_str;
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month) || $type_id <1) return false;
    $sql_str = "SELECT COUNT(DISTINCT(buyer_user_id)) AS b,COUNT(DISTINCT(seller_user_id)) AS s FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$begin_month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//上个月和这个都有消费的用户数
function get_twice_info($begin_month,$type_id)
{
    global $org_user_str;
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month) || $type_id <1) return false;
    $prev_month = date('Y-m',strtotime($begin_month.'-01')-24*3600); //上个月
    $sql_str = "SELECT DISTINCT(buyer_user_id) FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$begin_month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $result = db_simple_getdata($sql_str, FALSE, 101);
    if(!is_array($result)) $result;
    $sql_tmp_str = '';
    foreach($result as $v)
    {
        if(strlen($sql_tmp_str) >0) $sql_tmp_str .=',';
        $sql_tmp_str .= $v['buyer_user_id'];
    }
    unset($result);
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT COUNT(DISTINCT(buyer_user_id)) AS C FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$prev_month}' AND type_id={$type_id} AND buyer_user_id IN ({$sql_tmp_str}) AND org_user_id NOT IN ({$org_user_str})";
        $ret = db_simple_getdata($sql_str,TRUE,101);
        if(!is_array($ret)) $ret = array();
        return (int)$ret['C'];
    }
    return false;

}

//新客客数量【获取以前没有买过改商品的用户】在这个月也买了商品人数
function get_new_user_num($begin_month,$type_id,$user_num)
{
    global $org_user_str;
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    $user_num = (int)$user_num;//这个月的用户个数
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month) || $type_id <1 || $user_num<1) return false;
    $sql_str = "SELECT DISTINCT(buyer_user_id) FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$begin_month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $result = db_simple_getdata($sql_str, FALSE, 101);
    if(!is_array($result)) $result;
    $sql_tmp_str = '';
    foreach($result as $v)
    {
        if(strlen($sql_tmp_str) >0) $sql_tmp_str .=',';
        $sql_tmp_str .= $v['buyer_user_id'];
    }
    unset($result);
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT COUNT(DISTINCT(buyer_user_id)) AS C FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')<'{$begin_month}' AND type_id={$type_id} AND buyer_user_id IN ({$sql_tmp_str}) AND org_user_id NOT IN ({$org_user_str})";
        $ret = db_simple_getdata($sql_str,TRUE,101);
        if(!is_array($ret)) $ret = array();
        return sprintf('%.4f',($user_num-$ret['C'])/$user_num);
    }
}

//动销率
function get_seller_count($begin_month,$type_id,$user_num)
{
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    $user_num = (int)$user_num;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month) || $type_id <1 ||$user_num <1) return false;
    $sql_str = "SELECT COUNT(DISTINCT(p.user_id)) AS user_count
                            FROM mall_db.mall_seller_profile_tbl p,mall_db.mall_seller_tbl m
                            WHERE m.status=1 AND p.user_id = m.user_id AND FIND_IN_SET($type_id,p.type_id) AND FROM_UNIXTIME(m.add_time,'%Y-%m')<='{$begin_month}'";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    $user_count = (int)$ret['user_count'];
    return sprintf('%.2f',$user_num/$user_count);
}

//获取新增有收入供应商数量
function get_new_seller_num($begin_month,$type_id,$user_num)
{
    global $org_user_str;
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    $user_num = (int)$user_num;//这个月的用户个数
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month) || $type_id <1 || $user_num<1) return false;
    $sql_str = "SELECT DISTINCT(seller_user_id) FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$begin_month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $result = db_simple_getdata($sql_str, FALSE, 101);
    if(!is_array($result)) $result;
    $sql_tmp_str = '';
    foreach($result as $v)
    {
        if(strlen($sql_tmp_str) >0) $sql_tmp_str .=',';
        $sql_tmp_str .= $v['seller_user_id'];
    }
    unset($result);
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT COUNT(DISTINCT(seller_user_id)) AS C FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')<'{$begin_month}' AND type_id={$type_id} AND seller_user_id IN ({$sql_tmp_str}) AND org_user_id NOT IN ({$org_user_str})";
        $ret = db_simple_getdata($sql_str,TRUE,101);
        if(!is_array($ret)) $ret = array();
        return (int)($user_num-$ret['C']);
    }
}


exit;
*/









// +------------------------------------------------------------------------------------------
// |  【外拍】月份数据获取
// +------------------------------------------------------------------------------------------
echo "<table>";
echo "<tr><th colspan='2'>{$begin_month}-》{$prev_time}</th></tr>";
echo "<tr>";
echo "<th>月总额</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>订单数</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>平均客单价</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>消费人数（去重）</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>复购率</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>留存率</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>新客率</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>有收入供应商数量</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>供应商平均收入</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>动销率</th>"; //209个用户
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "<th>新增有收入供应商</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "</tr>";

//月售额和订单数
$this_order_result = get_waipai_order($begin_month);
$prev_order_result = get_waipai_order($prev_time);
$this_order_result['order_hb_scala'] = sprintf('%.4f',$this_order_result['total_price']/$prev_order_result['total_price']);//环比
$this_order_result['order_tb_scala'] = sprintf('%.4f',($this_order_result['total_price']-$prev_order_result['total_price'])/$prev_order_result['total_price']); //同比
//订单数
$this_order_result['order_num_hb_scala'] = sprintf('%.4f',$this_order_result['C']/$prev_order_result['C']); //环比
$this_order_result['order_num_tb_scala'] = sprintf('%.4f',($this_order_result['C']-$prev_order_result['C'])/$prev_order_result['C']);//同比
//客和商家数量
$this_user_result = get_role_user_count($begin_month);
$prev_user_result = get_role_user_count($prev_time);

//上上个月
$prev_prev_user_result = get_role_user_count(date('Y-m',strtotime($prev_time.'-01')-24*3600));
$avg_buyer_price = sprintf('%.4f',$this_order_result['total_price']/$this_user_result['b']);
$prev_avg_buyer_price = sprintf('%.4f',$prev_order_result['total_price']/$prev_user_result['b']);

$this_user_result['avg_buyer_price'] = $avg_buyer_price;
$this_user_result['avg_buyer_price_hb_scala'] = sprintf('%.4f',$avg_buyer_price/$prev_avg_buyer_price);
$this_user_result['avg_buyer_price_tb_scala'] = sprintf('%.4f',($avg_buyer_price-$prev_avg_buyer_price)/$prev_avg_buyer_price);//环比
//消费人数
$this_user_result['b_hb_scala'] = sprintf('%.4f',$this_user_result['b']/$prev_user_result['b']); //环比
$this_user_result['b_tb_scala'] = sprintf('%.4f',($this_user_result['b']-$prev_user_result['b'])/$prev_user_result['b']); //同比
//复购率【（订单数-消费人数）/订单数】
$new_twice_result = array();
$this_twice_pay = sprintf('%.4f',($this_order_result['C']-$this_user_result['b'])/$this_order_result['C']);
$prev_twice_pay = sprintf('%.4f',($prev_order_result['C']-$prev_user_result['b'])/$prev_order_result['C']);
$new_twice_result['twice_pay'] = $this_twice_pay;
$new_twice_result['twice_pay_hb_scala'] = sprintf('%.4f',$this_twice_pay/$prev_twice_pay);
$new_twice_result['twice_pay_tb_scala'] = sprintf('%.4f',($this_twice_pay-$prev_twice_pay)/$prev_twice_pay); //同比
//用户留存率【同品类上月有消费记录今月持续消费人数/上月消费总人数】
$new_user_twice_result = array();
$this_twice_num = get_twice_info($begin_month);
$prev_twice_num = get_twice_info($prev_time);
$this_user_twice_num = sprintf('%.4f',$this_twice_num/$prev_user_result['b']);
$prev_user_twice_num = sprintf('%.4f',$prev_twice_num/$prev_prev_user_result['b']);
$new_user_twice_result['user_twice_num'] = $this_user_twice_num;
$new_user_twice_result['user_twice_num_hb_scala'] = sprintf('%.4f',$this_user_twice_num/$prev_user_twice_num);
$new_user_twice_result['user_twice_num_tb_scala'] = sprintf('%.4f',($this_user_twice_num-$prev_user_twice_num)/$prev_user_twice_num); //同比

//消费新客率【此品类过往没有消费记录人数/今月总消费人数】
$new_user_result = array();
$this_new_user_num = get_new_add_user_count($begin_month);
$prev_new_user_num = get_new_add_user_count($prev_time);
$new_user_result['new_user_num'] = $this_new_user_num;
$new_user_result['new_user_num_hb_scala'] = sprintf('%.4f',$this_new_user_num/$prev_new_user_num);
$new_user_result['new_user_num_tb_scala'] = sprintf('%.4f',($this_new_user_num-$prev_new_user_num)/$prev_new_user_num);

//有收入供应商数量
$this_user_result['s_hb_scala'] = sprintf('%.4f',$this_user_result['s']/$prev_user_result['s']);
$this_user_result['s_tb_scala'] = sprintf('%.4f',($this_user_result['s']-$prev_user_result['s'])/$prev_user_result['s']);
//供应商平均收入
$avg_seller_price = sprintf('%.4f',$this_order_result['total_price']/$this_user_result['s']);
$prev_avg_seller_price = sprintf('%.4f',$prev_order_result['total_price']/$prev_user_result['s']);
$this_user_result['avg_seller_price'] = $avg_seller_price;
$this_user_result['avg_seller_price_hb_scala'] = sprintf('%.4f',$avg_seller_price/$prev_avg_seller_price);
$this_user_result['avg_seller_price_tb_scala'] = sprintf('%.4f',($avg_seller_price-$prev_avg_seller_price)/$prev_avg_seller_price); //同比
//获取用户动销【有交易供应商数量/总供应商数量】
$new_pin_result = array();
$this_user_pin = sprintf('%.4f',$this_user_result['s']/209);
$prev_user_pin = sprintf('%.4f',$prev_user_result['s']/209);;
$new_pin_result['user_pin'] =$this_user_pin;
$new_pin_result['user_pin_hb_scala'] = sprintf('%.4f',$this_user_pin/$prev_user_pin);
$new_pin_result['user_pin_tb_scala'] = sprintf('%.4f',($this_user_pin-$prev_user_pin)/$prev_user_pin);
//新增有收入供应商【此品类过往没有交易记录今月有交易记录数量】
$new_seller_result = array();
$this_new_seller_num = get_new_add_seller_count($begin_month);
//echo $this_new_user_num;
$prev_new_seller_num = get_new_add_seller_count($prev_time);
$new_seller_result['new_seller_num'] = $this_new_seller_num;
$new_seller_result['new_seller_num_hb_scala'] =sprintf('%.4f',$this_new_seller_num/$prev_new_seller_num);
$new_seller_result['new_seller_num_tb_scala'] =sprintf('%.4f',($this_new_seller_num-$prev_new_seller_num)/$prev_new_seller_num);
//print_r($new_seller_result);exit;

echo "<tr>";
echo "<td align='center'>{$this_order_result['total_price']}</td>";
echo "<td align='center'>{$this_order_result['order_hb_scala']}</td>";
echo "<td align='center'>{$this_order_result['order_tb_scala']}</td>";
echo "<td align='center'>{$this_order_result['C']}</td>";
echo "<td align='center'>{$this_order_result['order_num_hb_scala']}</td>";
echo "<td align='center'>{$this_order_result['order_num_tb_scala']}</td>";
//平均客单价
echo "<td align='center'>{$this_user_result['avg_buyer_price']}</td>";
echo "<td align='center'>{$this_user_result['avg_buyer_price_hb_scala']}</td>";
echo "<td align='center'>{$this_user_result['avg_buyer_price_tb_scala']}</td>";
//消费者
echo "<td align='center'>{$this_user_result['b']}</td>";
echo "<td align='center'>{$this_user_result['b_hb_scala']}</td>";
echo "<td align='center'>{$this_user_result['b_tb_scala']}</td>";
//复购率
echo "<td align='center'>{$new_twice_result['twice_pay']}</td>";
echo "<td align='center'>{$new_twice_result['twice_pay_hb_scala']}</td>";
echo "<td align='center'>{$new_twice_result['twice_pay_tb_scala']}</td>";
//消费留存率
echo "<td align='center'>{$new_user_twice_result['user_twice_num']}</td>";
echo "<td align='center'>{$new_user_twice_result['user_twice_num_hb_scala']}</td>";
echo "<td align='center'>{$new_user_twice_result['user_twice_num_tb_scala']}</td>";
//新客率
echo "<td align='center'>{$new_user_result['new_user_num']}</td>";
echo "<td align='center'>{$new_user_result['new_user_num_hb_scala']}</td>";
echo "<td align='center'>{$new_user_result['new_user_num_tb_scala']}</td>";
//有收入供应商数量
echo "<td align='center'>{$this_user_result['s']}</td>";
echo "<td align='center'>{$this_user_result['s_hb_scala']}</td>";
echo "<td align='center'>{$this_user_result['s_tb_scala']}</td>";
//供应商平均收入
echo "<td align='center'>{$this_user_result['avg_seller_price']}</td>";
echo "<td align='center'>{$this_user_result['avg_seller_price_hb_scala']}</td>";
echo "<td align='center'>{$this_user_result['avg_seller_price_tb_scala']}</td>";
//动销率
echo "<td align='center'>{$new_pin_result['user_pin']}</td>";
echo "<td align='center'>{$new_pin_result['user_pin_hb_scala']}</td>";
echo "<td align='center'>{$new_pin_result['user_pin_tb_scala']}</td>";
//新增有收入供应商
echo "<td align='center'>{$new_seller_result['new_seller_num']}</td>";
echo "<td align='center'>{$new_seller_result['new_seller_num_hb_scala']}</td>";
echo "<td align='center'>{$new_seller_result['new_seller_num_tb_scala']}</td>";
echo "</tr>";
echo "<table>";




//获取外拍订单数据
function get_waipai_order($begin_month)
{
    $begin_month = trim($begin_month);;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month)) return false;
    $sql_str = "SELECT COUNT(*) AS  C,SUM(enroll_num*budget) AS total_price FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')='{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $ret = db_simple_getdata($sql_str, true, 22);
    if(!is_array($ret)) $ret = array();
    return $ret;
}
//获取消费者个数和商家个数
function get_role_user_count($begin_month)
{
    $begin_month = trim($begin_month);;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month)) return false;
    $sql_str = "SELECT COUNT(DISTINCT(from_date_id)) AS b ,COUNT(DISTINCT(to_date_id)) AS s FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')='{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $ret = db_simple_getdata($sql_str, true, 22);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//新增有购买的用户
function get_new_add_user_count($begin_month)
{
    $begin_month = trim($begin_month);;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month)) return false;
    $sql_str = "SELECT DISTINCT(from_date_id) FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')='{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $list = db_simple_getdata($sql_str, false, 22);
    if(!is_array($list)) $list = array();
    $sql_tmp_str = '';
    $i = 0;
    foreach($list as $key=>$val)
    {
        if($key !=0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $val['from_date_id'];
        $i++;
    }
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT COUNT(DISTINCT(from_date_id)) AS C FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')<'{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0 AND from_date_id IN ($sql_tmp_str)";
        $ret = db_simple_getdata($sql_str, true, 22);
        if(!is_array($ret)) $ret = array();
        //print_r($ret);
        return intval($i-(int)$ret['C']);
    }

}

//获取外拍上个月和这个都有消费的用户数
function get_twice_info($begin_month)
{
    $begin_month = trim($begin_month);;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month)) return false;
    $prev_month = date('Y-m',strtotime($begin_month.'-01')-24*3600); //上个月
    $sql_str = "SELECT DISTINCT(from_date_id) FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')='{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $list = db_simple_getdata($sql_str, false, 22);
    //print_r($list);exit;
    if(!is_array($list)) $list = array();
    $sql_tmp_str = '';
    foreach($list as $key=>$val)
    {
        if($key !=0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $val['from_date_id'];
    }
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT COUNT(DISTINCT(from_date_id)) AS C FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')='{$prev_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0 AND from_date_id IN ($sql_tmp_str)";
        $ret = db_simple_getdata($sql_str, true, 22);
        if(!is_array($ret)) $ret = array();
        return (int)$ret['C'];
    }
    return false;

}

//新增有收入的商家
function get_new_add_seller_count($begin_month)
{
    $begin_month = trim($begin_month);;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month)) return false;
    $sql_str = "SELECT DISTINCT(to_date_id) FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')='{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $list = db_simple_getdata($sql_str, false, 22);
    if(!is_array($list)) $list = array();
    $sql_tmp_str = '';
    $i = 0;
    foreach($list as $key=>$val)
    {
        if($key !=0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $val['to_date_id'];
        $i++;
    }
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT COUNT(DISTINCT(to_date_id)) AS C FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')<'{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0 AND to_date_id IN ($sql_tmp_str)";
        $ret = db_simple_getdata($sql_str, true, 22);
        if(!is_array($ret)) $ret = array();
        print_r($ret);
        return intval($i-$ret['C']);
    }

}
