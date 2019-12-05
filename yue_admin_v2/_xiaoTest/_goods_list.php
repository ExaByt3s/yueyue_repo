<?php
/**
 * @desc:   商品测试
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/18
 * @Time:   11:07
 * version: 1.0
 */

include_once('common.inc.php');
/*
$begin_time = strtotime('2015-09-28');
$end_time = strtotime('2015-10-04');

$title_begin_date = date('Y-m-d',$begin_time);
$title_end_date = date('Y-m-d',$end_time);

echo "<table>";
echo "<tr><th colspan='3'>{$type_id}</th><th colspan='2'>{$title_begin_date}</th><th colspan='3'>{$title_end_date}</th></tr>";
echo "<tr>";
echo "<th>周销售总额</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>订单数</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>平均客单价</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>消费人数（去重）</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>复购率</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>有收入供应商数量</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>供应商平均收入</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>动销率</th>"; //209个用户
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>新增有收入供应商</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "</tr>";

for($i =0;$i <4;$i++)
{
    //周销售额和订单数
    $waipai_order_result = get_waipai_order($begin_time,$end_time);
    $waipai_7_order_result = get_waipai_order($begin_time-7*24*3600,$end_time-7*24*3600);
    $waipai_30_order_result = get_waipai_order($begin_time-4*7*24*3600,$end_time-4*7*24*3600); //月比

    //销售额【周比】
    $waipai_7_order_price_scala = sprintf('%.4f',$waipai_order_result['total_price']/$waipai_7_order_result['total_price']);
    //销售额【月比】
    $waipai_30_order_price_scala = sprintf('%.4f',($waipai_order_result['total_price']-$waipai_30_order_result['total_price'])/$waipai_30_order_result['total_price']);

    //订单【周比】
    $waipai_7_order_count_scala =  sprintf('%.4f',$waipai_order_result['C']/$waipai_7_order_result['C']);
    //订单【月比】
    $waipai_30_order_count_scala = sprintf('%.4f',($waipai_order_result['C']-$waipai_30_order_result['C'])/$waipai_30_order_result['total_price']);

    //客数和商家数
    $waipai_user_result = get_role_user_count($begin_time,$end_time);
    $waipai_7_user_result = get_role_user_count($begin_time-7*24*3600,$end_time-7*24*3600);
    $waipai_30_user_result = get_role_user_count($begin_time-4*7*24*3600,$end_time-4*7*24*3600);

    //平均客单价
    $avg_user_price =  sprintf('%.4f',$waipai_order_result['total_price']/$waipai_user_result['b']);

    $avg_7_user_price =  sprintf('%.4f',$waipai_7_order_result['total_price']/$waipai_7_user_result['b']);
    $avg_30_user_price =  sprintf('%.4f',$waipai_30_order_result['total_price']/$waipai_30_user_result['b']);
    //平均客单价【周比】
    $avg_7_user_price_scala = sprintf('%.4f',$avg_user_price/$avg_7_user_price);
    //平均客单价【月比】
    $avg_30_user_price_scala = sprintf('%.4f',($avg_user_price-$avg_30_user_price)/$avg_30_user_price);

    //消费者个数
    $buyer_7_count_scala = sprintf('%.4f',$waipai_user_result['b']/$waipai_7_user_result['b']);
    $buyer_30_count_scala = sprintf('%.4f',($waipai_user_result['b']-$waipai_30_user_result['b'])/$waipai_30_user_result['b']);

    //复购率
    $buyer_again_count = sprintf('%.4f',($waipai_order_result['C']-$waipai_user_result['b'])/$waipai_order_result['C']);
    $buyer_7_again_count = sprintf('%.4f',($waipai_7_order_result['C']-$waipai_7_user_result['b'])/$waipai_7_order_result['C']);
    $buyer_30_again_count = sprintf('%.4f',($waipai_30_order_result['C']-$waipai_30_user_result['b'])/$waipai_30_order_result['C']);
    //复购率【周比】
    $buyer_7_again_scala = sprintf('%.4f',$buyer_again_count/$buyer_7_again_count);
    $buyer_30_again_scala = sprintf('%.4f',($buyer_again_count-$buyer_30_again_count)/$buyer_30_again_count);

    //有收入供应商数量【周比】
    $seller_7_user_scala = sprintf('%.4f',$waipai_user_result['s']/$waipai_7_user_result['s']);
    $seller_30_user_scala = sprintf('%.4f',($waipai_user_result['s']-$waipai_30_user_result['s'])/$waipai_30_user_result['s']);

    //供应商平均收入
    $avg_seller_price =  sprintf('%.4f',$waipai_order_result['total_price']/$waipai_user_result['s']);
    $avg_7_seller_price =  sprintf('%.4f',$waipai_7_order_result['total_price']/$waipai_7_user_result['s']);
    $avg_30_seller_price =  sprintf('%.4f',$waipai_30_order_result['total_price']/$waipai_30_user_result['s']);
    $avg_7_seller_price_scala = sprintf('%.4f',$avg_seller_price/$avg_7_seller_price);
    $avg_30_seller_price_scala = sprintf('%.4f',($avg_seller_price-$avg_30_seller_price)/$avg_30_seller_price);

    //动销率
    $pin_seller = sprintf('%.4f',$waipai_user_result['s']/209);
    $pin_7_seller = sprintf('%.4f',$waipai_7_user_result['s']/209);
    $pin_30_seller = sprintf('%.4f',$waipai_30_user_result['s']/209);
    //动销率【周比】
    $pin_7_seller_scala =  sprintf('%.4f',$pin_seller/$pin_7_seller);
    //动销率【月比】
    $pin_30_seller_scala =  sprintf('%.4f',($pin_seller-$pin_30_seller)/$pin_30_seller);

    //新增有收入供应商
    $waipai_new_count = get_new_add_user_count($begin_time,$end_time);
    $waipai_7_new_count = get_new_add_user_count($begin_time-7*24*3600,$end_time-7*24*3600);
    $waipai_30_new_count = get_new_add_user_count($begin_time-4*7*24*3600,$end_time-4*7*24*3600);
    $waipai_7_new_count_scala = sprintf('%.4f',$waipai_new_count/$waipai_7_new_count);
    $waipai_30_new_count_scala = sprintf('%.4f',($waipai_new_count-$waipai_30_new_count)/$waipai_30_new_count);

    echo "<tr>";
    echo "<td align='center'>{$waipai_order_result['total_price']}</td>"; //销售额
    echo "<td align='center'>{$waipai_7_order_price_scala}</td>";
    echo "<td align='center'>{$waipai_30_order_price_scala}</td>";
    echo "<td align='center'>{$waipai_order_result['C']}</td>";//订单
    echo "<td align='center'>{$waipai_7_order_count_scala}</td>";
    echo "<td align='center'>{$waipai_30_order_count_scala}</td>";
    echo "<td align='center'>{$avg_user_price}</td>";//平均客单价
    echo "<td align='center'>{$avg_7_user_price_scala}</td>";
    echo "<td align='center'>{$avg_30_user_price_scala}</td>";
    echo "<td align='center'>{$waipai_user_result['b']}</td>";//消费人数（去重）
    echo "<td align='center'>{$buyer_7_count_scala}</td>";
    echo "<td align='center'>{$buyer_30_count_scala}</td>";
    echo "<td align='center'>{$buyer_again_count}</td>";//复购率
    echo "<td align='center'>{$buyer_7_again_scala}</td>";
    echo "<td align='center'>{$buyer_30_again_scala}</td>";
    echo "<td align='center'>{$waipai_user_result['s']}</td>";//有收入供应商数量
    echo "<td align='center'>{$seller_7_user_scala}</td>";
    echo "<td align='center'>{$seller_30_user_scala}</td>";
    echo "<td align='center'>{$avg_seller_price}</td>"; //供应商平均收入
    echo "<td align='center'>{$avg_7_seller_price_scala}</td>";
    echo "<td align='center'>{$avg_30_seller_price_scala}</td>";
    echo "<td align='center'>{$pin_seller}</td>";//动销率
    echo "<td align='center'>{$pin_7_seller_scala}</td>";
    echo "<td align='center'>{$pin_30_seller_scala}</td>";
    echo "<td align='center'>{$waipai_new_count}</td>";//新增有收入供应商
    echo "<td align='center'>{$waipai_7_new_count_scala}</td>";//新增有收入供应商
    echo "<td align='center'>{$waipai_30_new_count_scala}</td>";//新增有收入供应商
    echo "</tr>";

    $begin_time += 7*24*3600;
    $end_time += 7*24*3600;
}

echo "<table>";




//获取外拍订单数据
function get_waipai_order($begin_time,$end_time)
{
    $begin_time = (int)$begin_time;
    $end_time = (int)$end_time;
    if($begin_time <1 || $end_time <1) return false;
    $sql_str = "SELECT COUNT(*) AS  C,SUM(enroll_num*budget) AS total_price FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND complete_time >= {$begin_time} AND complete_time<=$end_time AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $ret = db_simple_getdata($sql_str, true, 22);
    if(!is_array($ret)) $ret = array();
    return $ret;
}
//获取消费者个数和商家个数
function get_role_user_count($begin_time,$end_time)
{
    $begin_time = (int)$begin_time;
    $end_time = (int)$end_time;
    if($begin_time <1 || $end_time <1) return false;
    $sql_str = "SELECT COUNT(DISTINCT(from_date_id)) AS b ,COUNT(DISTINCT(to_date_id)) AS s FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND complete_time >= {$begin_time} AND complete_time<=$end_time AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $ret = db_simple_getdata($sql_str, true, 22);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//新增有购买的用户
function get_new_add_user_count($begin_time,$end_time)
{
    $begin_time = (int)$begin_time;
    $end_time = (int)$end_time;
    if($begin_time <1 || $end_time <1) return false;
    $sql_str = "SELECT DISTINCT(from_date_id) FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND complete_time >= {$begin_time} AND complete_time<=$end_time AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
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
        $sql_str = "SELECT COUNT(DISTINCT(from_date_id)) AS C FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND complete_time < {$begin_time} AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0 AND from_date_id IN ($sql_tmp_str)";
        $ret = db_simple_getdata($sql_str, true, 22);
        if(!is_array($ret)) $ret = array();
        //print_r($ret);
        return intval($i-(int)$ret['C']);
    }

}
















exit;*/
//下面是商城部分
$type_id = (int)$_INPUT['type_id'];

if( $type_id <1)         //!preg_match("/\d\d\d\d-\d\d-\d\d/", $begin_time) || !preg_match("/\d\d\d\d-\d\d-\d\d/", $end_time) ||
{
    exit("非法操作");
}

$begin_date = strtotime('2015-09-28');
$end_date = strtotime('2015-10-04');

$title_begin_date = date('Y-m-d',$begin_date);
$title_end_date = date('Y-m-d',$end_date);
echo "<table>";
echo "<tr><th colspan='3'>{$type_id}</th><th colspan='2'>{$title_begin_date}</th><th colspan='3'>{$title_end_date}</th></tr>";
//需要去掉的机构ID
global $org_user_str;
$org_user_str = '111715,119879,124214';

echo "<tr>";
echo "<th>周销售总额</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>订单数</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>平均客单价</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>消费人数（去重）</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>复购率</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>有收入供应商数量</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>供应商平均收入</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>动销率</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>新增有收入供应商</th>";
echo "<th>周比</th>";
echo "<th>月比</th>";
echo "<th>供应商数量</th>";
echo "</tr>";
for($i =0;$i <4;$i++)
{
    $begin_time = date('Y-m-d',$begin_date);
    $end_time = date('Y-m-d',$end_date);


    $date_ret = get_order_date($begin_date,$end_date,$type_id);

    $buyer_ret = get_buyer_count($begin_date,$end_date,$type_id);
    $agei_count = get_buy_agei($date_ret['C'],$buyer_ret['b']);
    //复购率周比
    $zhou_date_ret = get_order_date($begin_date-7*24*3600,$end_date-7*24*3600,$type_id);
    $zhou_buyer_ret = get_buyer_count($begin_date-7*24*3600,$end_date-7*24*3600,$type_id);
    $agei_count_next = get_buy_agei($zhou_date_ret['C'],$zhou_buyer_ret['b']);

    $agei_week_scala = sprintf('%.4f',$agei_count/$agei_count_next);

    //复购率月比
    $yue_date_ret = get_order_date($begin_date-4*7*24*3600,$end_date-4*7*24*3600,$type_id);
    $yue_buyer_ret = get_buyer_count($begin_date-4*7*24*3600,$end_date-4*7*24*3600,$type_id);
    $agei_count_yue = get_buy_agei($yue_date_ret['C'],$yue_buyer_ret['b']);
    $agei_yue_scala = sprintf('%.4f',($agei_count-$agei_count_yue)/$agei_count_yue);

    //商家平均收入
    $avg_seller_price = sprintf('%.4f',$date_ret['total_price']/$buyer_ret['s']);

    //商家平均收入 【周比】
    $avg_week_seller_price = sprintf('%.4f',$zhou_date_ret['total_price']/$zhou_buyer_ret['s']);
    $avg_week_seller_price_scala = sprintf('%.4f',$avg_seller_price/$avg_week_seller_price);
    //商家平均收入 【月比】
    $avg_month_seller_price = sprintf('%.4f',$yue_date_ret['total_price']/$yue_buyer_ret['s']);
    $avg_month_seller_price_scala = sprintf('%.4f',($avg_seller_price-$avg_month_seller_price)/$avg_month_seller_price);

    $new_seller_count = get_new_seller_count($begin_date,$end_date,$type_id);

    //七天前有收入
    $new_week_seller_count = get_new_seller_count($begin_date-7*24*3600,$end_date-7*24*3600,$type_id);
    //一个月
    $new_month_seller_count = get_new_seller_count($begin_date-4*7*24*3600,$end_date-4*7*24*3600,$type_id);

    $new_week_seller_scala = sprintf('%.4f',$new_seller_count/$new_week_seller_count);
    $new_month_seller_scala = sprintf('%.4f',($new_seller_count-$new_month_seller_count)/$new_month_seller_count);

    $seller_count = get_seller_count($type_id,$end_date);
    //七天前的数量
    $seller_week_count = get_seller_count($type_id,$end_date-7*24*3600);
    //echo $seller_week_count;exit;
    //28天前的数量
    $seller_month_count = get_seller_count($type_id,$end_date-4*7*24*3600);

    $order_week_scala = get_week_scala($date_ret,$begin_date,$end_date,$type_id);
    $order_month_scala = get_month_scala($date_ret,$begin_date,$end_date,$type_id);
    //print_r($order_month_scala);exit;
    $buyer_week_scala = get_week_buyer_scala($buyer_ret,$begin_date,$end_date,$type_id);
    $buyer_month_scala = get_month_buyer_scala($buyer_ret,$begin_date,$end_date,$type_id);

    //平均客单价
    $avg_buyer_price = sprintf('%.4f',$date_ret['total_price']/$buyer_ret['b']);
    $avg_buyer_week_price = sprintf('%.4f',$zhou_date_ret['total_price']/$zhou_buyer_ret['b']);
    $avg_buyer_month_price = sprintf('%.4f',$yue_date_ret['total_price']/$yue_buyer_ret['b']);
    // 平均客单价【周比】
    $avg_buyer_week_price_scala = sprintf('%.4f',$avg_buyer_price/$avg_buyer_week_price);
    $avg_buyer_month_price_scala = sprintf('%.4f',($avg_buyer_price-$avg_buyer_month_price)/$avg_buyer_month_price);




    //动销率
    $dxl = sprintf('%.4f',$buyer_ret['s']/$seller_count);
    $dxl_week = sprintf('%.4f',$zhou_buyer_ret['s']/$seller_week_count);
    $dxl_month = sprintf('%.4f',$yue_buyer_ret['s']/$seller_month_count);

    $dxl_week_scala = sprintf('%.4f',$dxl/$dxl_week);
    $dxl_month_scala = sprintf('%.4f',($dxl-$dxl_month)/$dxl_month);


    echo "<tr>";
    echo "<td align='center'>{$date_ret['total_price']}</td>";
    echo "<td align='center'>{$order_week_scala['total_price']}</td>";
    echo "<td align='center'>{$order_month_scala['total_price']}</td>";
    echo "<td align='center'>{$date_ret['C']}</td>";
    echo "<td align='center'>{$order_week_scala['C']}</td>";
    echo "<td align='center'>{$order_month_scala['C']}</td>";
    echo "<td align='center'>{$avg_buyer_price}</td>";
    echo "<td align='center'>{$avg_buyer_week_price_scala}</td>";
    echo "<td align='center'>{$avg_buyer_month_price_scala}</td>";
    echo "<td align='center'>{$buyer_ret['b']}</td>";
    echo "<td align='center'>{$buyer_week_scala['b']}</td>";
    echo "<td align='center'>{$buyer_month_scala['b']}</td>";
    echo "<td align='center'>{$agei_count}</td>";
    echo "<td align='center'>{$agei_week_scala}</td>";
    echo "<td align='center'>{$agei_yue_scala}</td>";
    echo "<td align='center'>{$buyer_ret['s']}</td>";
    echo "<td align='center'>{$buyer_week_scala['s']}</td>";
    echo "<td align='center'>{$buyer_month_scala['s']}</td>";
    echo "<td align='center'>{$avg_seller_price}</td>";
    echo "<td align='center'>{$avg_week_seller_price_scala}</td>";
    echo "<td align='center'>{$avg_month_seller_price_scala}</td>";
    echo "<td align='center'>{$dxl}</td>";
    echo "<td align='center'>{$dxl_week_scala}</td>";
    echo "<td align='center'>{$dxl_month_scala}</td>";
    echo "<td align='center'>{$new_seller_count}</td>";
    echo "<td align='center'>{$new_week_seller_scala}</td>";
    echo "<td align='center'>{$new_month_seller_scala}</td>";
    echo "<td align='center'>{$seller_count}</td>";
    echo "</tr>";
    $begin_date = $begin_date+7*24*3600;
    $end_date = $end_date+7*24*3600;
}
echo "</table>";




/*$ret = get_buyer_count($begin_date,$end_date,31);
print_r($ret);*/


//订单数量
function get_order_date($begin_date,$end_date,$type_id)
{
    global $org_user_str;
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $sql_str ="SELECT count(*) AS C,SUM(total_amount) AS total_price,AVG(total_amount) AS avg_price FROM mall_db.mall_order_tbl WHERE status=8 AND sign_time>={$begin_date} AND sign_time<={$end_date} AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//获取上周手机
function get_prev_week_order($begin_date,$end_date,$type_id)
{
    global $org_user_str;
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $begin_date -=7*24*3600;
    $end_date -=7*24*3600;
    $sql_str ="SELECT count(*) AS C,SUM(total_amount) AS total_price,AVG(total_amount) AS avg_price FROM mall_db.mall_order_tbl WHERE status=8 AND sign_time>={$begin_date} AND sign_time<={$end_date} AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//订单周比
function get_week_scala($ret,$begin_date,$end_date,$type_id)
{
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $prev_ret = get_prev_week_order($begin_date,$end_date,$type_id);
    $array_key = array();
    $array_key['total_price'] = sprintf('%.4f',$ret['total_price']/$prev_ret['total_price']);
    $array_key['C'] = sprintf('%.4f',$ret['C']/$prev_ret['C']);
    return $array_key;
}

//获取月数据
function get_prev_month_order($begin_date,$end_date,$type_id)
{
    global $org_user_str;
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $begin_date -=4*7*24*3600;
    $end_date -=4*7*24*3600;
    $sql_str ="SELECT count(*) AS C,SUM(total_amount) AS total_price,AVG(total_amount) AS avg_price FROM mall_db.mall_order_tbl WHERE status=8 AND sign_time>={$begin_date} AND sign_time<={$end_date} AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//获取订单月比
function get_month_scala($ret,$begin_date,$end_date,$type_id)
{
    $array_key = array();
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $prev_ret = get_prev_month_order($begin_date,$end_date,$type_id);
    if(empty($prev_ret)) return $array_key;
    $array_key['total_price'] = sprintf('%.4f',($ret['total_price']-$prev_ret['total_price'])/$prev_ret['total_price']);
    $array_key['C'] = sprintf('%.4f',($ret['C']-$prev_ret['C'])/$prev_ret['C']);
    return $array_key;
}


//消费者人数
function get_buyer_count($begin_date,$end_date,$type_id)
{
    global $org_user_str;
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $sql_str = "SELECT COUNT(DISTINCT(buyer_user_id)) AS b,COUNT(DISTINCT(seller_user_id)) AS s FROM mall_db.mall_order_tbl WHERE status=8 AND sign_time>={$begin_date} AND sign_time<={$end_date} AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

function get_prev_buyer_count($begin_date,$end_date,$type_id)
{
    global $org_user_str;
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $begin_date -=7*24*3600;
    $end_date -=7*24*3600;
    $sql_str = "SELECT COUNT(DISTINCT(buyer_user_id)) AS b,COUNT(DISTINCT(seller_user_id)) AS s FROM mall_db.mall_order_tbl WHERE status=8 AND sign_time>={$begin_date} AND sign_time<={$end_date} AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//消费人数周比
function get_week_buyer_scala($ret,$begin_date,$end_date,$type_id)
{
    $array_key = array();
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $prev_ret = get_prev_buyer_count($begin_date,$end_date,$type_id);
    if(empty($prev_ret)) return $array_key;
    $array_key['b'] = sprintf('%.4f',$ret['b']/$prev_ret['b']);
    $array_key['s'] = sprintf('%.4f',$ret['s']/$prev_ret['s']);
    return $array_key;
}

function get_7_prev_buyer_count($begin_date,$end_date,$type_id)
{
    global $org_user_str;
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $begin_date -=4*7*24*3600;
    $end_date -=4*7*24*3600;
    $sql_str = "SELECT COUNT(DISTINCT(buyer_user_id)) AS b,COUNT(DISTINCT(seller_user_id)) AS s FROM mall_db.mall_order_tbl WHERE status=8 AND sign_time>={$begin_date} AND sign_time<={$end_date} AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}
//月比
function get_month_buyer_scala($ret,$begin_date,$end_date,$type_id)
{
    $array_key = array();
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    $type_id = (int)$type_id;
    if($begin_date <1 || $end_date <1 || $type_id <1) return false;
    $prev_ret = get_7_prev_buyer_count($begin_date,$end_date,$type_id);
    if(empty($prev_ret)) return $array_key;
    $array_key['b'] = sprintf('%.4f',($ret['b']-$prev_ret['b'])/$prev_ret['b']);
    $array_key['s'] = sprintf('%.4f',($ret['s']-$prev_ret['s'])/$prev_ret['s']);
    return $array_key;
}


//复购率
function get_buy_agei($order_count,$buyer_count)
{
    $order_count = (int)$order_count;
    $buyer_count = (int)$buyer_count;
    if($order_count <1 || $buyer_count <1) return false;
    return sprintf('%.4f',($order_count-$buyer_count)/$order_count);
}

//获取上个月人数
function get_prev_month($type_id,$time)
{
    global $org_user_str;
    $type_id = (int)$type_id;
    $time = (int)$time;
    if($type_id <1 || $time <1) return false;
    $month = date('Y-m',$time-30*24*3600);
    $sql_str = "SELECT count(DISTINCT(buyer_user_id)) AS C FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return (int)$ret['C'];
}

//获取这个月有和上个月都有的用户数量
function get_same_month($type_id,$time)
{
    global $org_user_str;
    $type_id = (int)$type_id;
    $time = (int)$time;
    if($type_id <1 || $time <1) return false;
    $this_month = date('Y-m',$time);
    $prev_month = date('Y-m',$time-30*24*3600);//上个月
    $sql_str = "SELECT DISTINCT(buyer_user_id) FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$this_month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $list = db_simple_getdata($sql_str, false, 101);
    if(!is_array($list)) $list = array();
    $sql_tmp_str = '';
    foreach($list as $key=>$val)
    {
        if($key !=0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $val['buyer_user_id'];
    }
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT count(DISTINCT(buyer_user_id)) AS C FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$prev_month}' AND type_id={$type_id} AND buyer_user_id IN ({$sql_tmp_str}) AND org_user_id NOT IN ({$org_user_str})";
        $ret = db_simple_getdata($sql_str, true, 101);
        if(!is_array($ret)) $ret = array();
        return (int)$ret['C'];
    }
    return false;
}

//消费留存率(月)】
function get_life_scala($type_id,$time)
{
    $type_id = (int)$type_id;
    $time = (int)$time;
    if($type_id <1 || $time <1) return false;
    $same_count = get_same_month($type_id,$time);
    $prev_count = get_prev_month($type_id,$time);
    return ($same_count/$prev_count);
}

//获取这个月的消费者
function get_this_month_count($type_id,$time)
{
    global $org_user_str;
    $type_id = (int)$type_id;
    $time = (int)$time;
    if($type_id <1 || $time <1) return false;
    $month = date('Y-m',$time);
    $sql_str = "SELECT count(DISTINCT(buyer_user_id)) AS C FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return (int)$ret['C'];
}

//获取除了这个月之外以前没有消费的用户数
function get_old_not_pay($type_id,$time)
{
    global $org_user_str;
    $type_id = (int)$type_id;
    $time = (int)$time;
    if($type_id <1 || $time <1) return false;
    $this_month = date('Y-m',$time);
    $sql_str = "SELECT DISTINCT(buyer_user_id) FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')='{$this_month}' AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $list = db_simple_getdata($sql_str, false, 101);
    if(!is_array($list)) $list = array();
    $sql_tmp_str = '';
    $i = 0;
    foreach($list as $key=>$val)
    {
        if($key !=0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $val['buyer_user_id'];
        $i++;
    }
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT count(DISTINCT(buyer_user_id)) AS C FROM mall_db.mall_order_tbl WHERE status=8 AND FROM_UNIXTIME(sign_time,'%Y-%m')<'{$this_month}' AND type_id={$type_id} AND buyer_user_id IN ({$sql_tmp_str}) AND org_user_id NOT IN ({$org_user_str})";
        $ret = db_simple_getdata($sql_str, true, 101);
        if(!is_array($ret)) $ret = array();
        return intval($i-(int)$ret['C']);
    }
    return false;
}

//消费新客率(月)】
function get_new_buyer_scala($type_id,$time)
{
    $type_id = (int)$type_id;
    $time = (int)$time;
    if($type_id <1 || $time <1) return false;
    $old_count = get_old_not_pay($type_id,$time);
    $month_count = get_this_month_count($type_id,$time);
    return ($old_count/$month_count);
}

//新增有收入供应商
function get_new_seller_count($begin_date,$end_date,$type_id)
{
    global $org_user_str;
    $type_id = (int)$type_id;
    $begin_date = (int)$begin_date;
    $end_date = (int)$end_date;
    if($type_id <1 || $begin_date <1 || $end_date <1) return false;
    $sql_str = "SELECT DISTINCT(seller_user_id) FROM mall_db.mall_order_tbl WHERE status=8 AND sign_time>={$begin_date} AND sign_time<={$end_date} AND type_id={$type_id} AND org_user_id NOT IN ({$org_user_str})";
    $list = db_simple_getdata($sql_str, false, 101);
    if(!is_array($list)) $list = array();
    $sql_tmp_str = '';
    $i = 0;
    foreach($list as $key=>$val)
    {
        if($key !=0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $val['seller_user_id'];
        $i++;
    }
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT count(DISTINCT(seller_user_id)) AS C FROM mall_db.mall_order_tbl WHERE status=8 AND sign_time<{$begin_date} AND type_id={$type_id} AND seller_user_id IN ({$sql_tmp_str}) AND org_user_id NOT IN ({$org_user_str})";
        $ret = db_simple_getdata($sql_str, true, 101);
        if(!is_array($ret)) $ret = array();
        //print_r($ret);
        return intval($i-(int)$ret['C']);
    }
    return false;
}

//获取当前商家数量
function get_seller_count($type_id,$end_time)
{
    $type_id = (int)$type_id;
    $end_time = (int)$end_time;
    if($type_id <1 || $end_time <1) return false;
    $date_time = date('Y-m-d',$end_time);
    $sql_str = "SELECT COUNT(DISTINCT(p.user_id)) AS user_count
                            FROM mall_db.mall_seller_profile_tbl p,mall_db.mall_seller_tbl m
                            WHERE m.status=1 AND p.user_id = m.user_id AND FIND_IN_SET($type_id,p.type_id) AND FROM_UNIXTIME(m.add_time,'%Y-%m-%d')<='{$date_time}'";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return (int)$ret['user_count'] ;
}


exit;

//消费者个数
$sql_str = "SELECT DISTINCT(buyer_id) AS C FROM mall_db.mall_order_tbl WHERE status=8";


exit;
/*function trimall($str)//删除空格
{
    $qian=array(" ","　","\t","\n","\r");
    $hou=array("","","","","");
    return str_replace($qian,$hou,$str);
}

$str = trimall('yueyue://goto?type=inner_app&pid=1220102&goods_id=   211833');
echo $str;*/

/*$url ="yueyue://goto?type=inner_app&pid=1220101&return_query=yueyue_static_cms_id%3D701%26cms_type%3Dgoods&title=%E7%83%AD%E9%97%A8%E5%95%86%E5%93%81%E6%8E%A8%E8%8D%90";
$arr = parse_url($url);
$str_prev = "/&pid=([a-zA-Z0-9])+&/i";
$url = preg_replace($str_prev,"&pid=2&", $url);
//$url = str_replace($str_prev,"pid=2",$url);
print_r($url);
exit;*/



//不分品类 登录个数

/*$sql_str ="SELECT log_time,COUNT(DISTINCT(user_id)) as user_count FROM yueyue_user_last_login_log_tbl_201509 WHERE app_role='yuebuyer' GROUP BY log_time";


//消费者私聊个数

$sql = "SELECT FROM_UNIXTIME(date_time,'%Y-%m-%d') AS add_time,COUNT(DISTINCT(sender_id)) AS sender_count from sendserver_for_seller_reply_log_201510 WHERE sender_identity='yuebuyer' GROUP BY FROM_UNIXTIME(date_time,'%Y-%m-%d')";*/







/*//模特分类商家登录数据
$user_obj = POCO::singleton( 'pai_user_class' );
$mall_certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');//服务审核人类
$mall_goods_type_obj = POCO::singleton( 'pai_mall_goods_type_class' );
$sql_str="SELECT p.user_id,p.type_id from mall_db.mall_seller_tbl as s left join mall_db.mall_seller_profile_tbl as p on s.user_id=p.user_id WHERE s.`status`=1  AND FIND_IN_SET(31,p.type_id)";

$list = db_simple_getdata($sql_str, false, 101);
if(!is_array($list)) $list = array();

echo "<table>";
foreach($list as &$v)
{
    //$sql_tmp_str = "SELECT yueseller_last_time AS last_login_time from yueyue_user_data_db.yueyue_user_info_tbl WHERE user_id={$v['user_id']} limit 0,1";
    $sql_tmp_str = "SELECT last_login_time from yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201510 WHERE app_role='yueseller' AND user_id={$v['user_id']} ORDER BY log_time DESC limit 0,1";
    $ret = db_simple_getdata($sql_tmp_str, true, 22);
    if(!is_array($ret) || empty($ret))
    {
        $sql_tmp_str = "SELECT last_login_time from yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201509 WHERE app_role='yueseller' AND user_id={$v['user_id']} ORDER BY log_time DESC limit 0,1";
        $ret = db_simple_getdata($sql_tmp_str, true, 22);
        if(!is_array($ret) || empty($ret))
        {
            $sql_tmp_str = "SELECT last_login_time from yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201509 WHERE app_role='yueseller' AND user_id={$v['user_id']} ORDER BY log_time DESC limit 0,1";
            $ret = db_simple_getdata($sql_tmp_str, true, 22);
        }
    }
    $v['last_login_time'] = trim($ret['last_login_time']);
    $user_info = $user_obj->get_user_info($v['user_id']);
    $v['nickname'] = trim($user_info['nickname']);
    $v['cellphone'] = $user_info['cellphone'];
    $audit_name = $mall_certificate_service_obj->get_user_option_name(31,$v['user_id']);
    $v['audit_name'] = strlen($audit_name) >0 ? $audit_name : '无';
    $type_arr = explode(',',$v['type_id']);
    if(!is_array($type_arr)) $type_arr = array();
    $str_type = '';
    foreach($type_arr as $key2=>$id)
    {
        $type_info = $mall_goods_type_obj->get_type_info($id);
        if($key2 !=0) $str_type .= ',';
        $str_type .= "{$type_info['name']}";
    }
    $v['str_type'] = $str_type;
    //get_poco_location_name_by_location_id
    $v['location_name'] = get_poco_location_name_by_location_id($user_info['location_id']);

    echo "<tr>";
    echo "<td>{$v['user_id']}</td>";
    echo "<td>{$v['nickname']}</td>";
    echo "<td>{$v['cellphone']}</td>";
    echo "<td>{$v['str_type']}</td>";
    echo "<td>{$v['last_login_time']}</td>";
    echo "<td>{$v['audit_name']}</td>";
    echo "<td>{$v['audit_name']}</td>";
    echo "<td>{$v['location_name']}</td>";
    echo "</tr>";
}
echo "</table>";


exit;*/






$type_id = intval($_INPUT['type_id']);
if($type_id <0) die("品类id等于空");
//模特登录数统计
echo "<table>";
echo "<tr>";
echo "<th>日期</th>";
echo "<th>登录个数</th>";
echo "</tr>";
for ($i = 1; $i <= 31; $i++)
{
    $table_num = sprintf('%02d', $i);
    $log_time = "2015-10-{$table_num}";
    $log_num = get_login_num($log_time,$type_id);
    echo "<tr>";
    echo "<td>{$log_time}</td>";
    echo "<td>{$log_num}</td>";
    echo "</tr>";
}
echo "</table>";
function get_login_num($log_time='',$type_id)
{
    $log_time = trim($log_time);
    if(strlen($log_time)<1) die("日期不能为空");
    $sql_str ="SELECT user_id from yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201510 WHERE app_role='yueseller' AND log_time='{$log_time}'";
    $list = db_simple_getdata($sql_str, false, 22);
    if(!is_array($list)) $list = array();

    $sql_tmp_str = '';
    foreach($list as $key=>$val)
    {
        if($key !=0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $val['user_id'];
    }

    if(strlen($sql_tmp_str)>0)
    {
        $sql_in_str="SELECT COUNT(*) AS login_num from mall_db.mall_seller_tbl as s left join mall_db.mall_seller_profile_tbl as p on s.user_id=p.user_id WHERE s.`status`=1  AND FIND_IN_SET($type_id,p.type_id) AND p.user_id IN ({$sql_tmp_str})";
        //echo $sql_in_str;
        $ret = db_simple_getdata($sql_in_str, true, 101);
        //print_r($ret);
        return intval($ret['login_num']);
    }
}



