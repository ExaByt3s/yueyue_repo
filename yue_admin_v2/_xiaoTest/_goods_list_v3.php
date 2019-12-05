<?php
/**
 * @desc:   商品测试
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/18
 * @Time:   11:07
 * version: 1.0
 */

include_once('common.inc.php');

$type_list = array(
    0=>array('id'=>3,'name'=>'化妆服务'),
    1=>array('id'=>5,'name'=>'摄影培训'),
    2=>array('id'=>12,'name'=>'影棚租赁'),
    3=>array('id'=>31,'name'=>'模特邀约'),
    4=>array('id'=>40,'name'=>'摄影服务'),
    5=>array('id'=>41,'name'=>'约美食'),
    6=>array('id'=>43,'name'=>'约有趣'),
    7=>array('id'=>99,'name'=>'外拍活动')
);

echo "<table>";
echo "<tr>";
echo "<th>类型</th>";
echo "<th>时间</th>";
echo "<th>周销售总额</th>";
echo "<th>上周环比</th>";
echo "<th>上月同期同比</th>";
echo "<th>订单数</th>";
echo "<th>上周环比</th>";
echo "<th>上月同期同比</th>";
echo "<th>平均客单价</th>";
echo "<th>上周环比</th>";
echo "<th>上月同期同比</th>";
echo "<th>消费人数（去重）</th>";
echo "<th>上周环比</th>";
echo "<th>上月同期同比</th>";
echo "<th>复购率</th>";
echo "<th>上周环比</th>";
echo "<th>上月同期同比</th>";
echo "<th>有收入供应商数量</th>";
echo "<th>上周环比</th>";
echo "<th>上月同期同比</th>";
echo "<th>供应商平均收入</th>";
echo "<th>上周环比</th>";
echo "<th>上月同期同比</th>";
echo "<th>动销率</th>";
echo "<th>上周环比</th>";
echo "<th>上月同期同比</th>";
echo "<th>新增有收入供应商</th>";
echo "<th>环比</th>";
echo "<th>同比</th>";
echo "</tr>";

foreach($type_list as $v)
{
    $sql_str = "SELECT * FROM test.yueyue_order_report_tbl WHERE type_id={$v['id']} AND date_time>='2015-11-01' AND type='week'";
    $result = db_simple_getdata($sql_str, false, 22);
    foreach($result as $val)
    {
        $user_pin_scala = ($val['user_pin_scala'] *100).'%';
        echo "<tr>";
        echo "<td align='center'>{$v['name']}({$v['id']})</td>";
        echo "<td align='center'>{$val['date_key']}({$val['date_time']})</td>";
        echo "<td align='center'>{$val['total_price']}</td>";
        echo "<td align='center'>{$val['total_price_hb_scala']}</td>";
        echo "<td align='center'>{$val['total_price_tb_scala']}</td>";
        echo "<td align='center'>{$val['order_num']}</td>";
        echo "<td align='center'>{$val['order_num_hb_scala']}</td>";
        echo "<td align='center'>{$val['order_num_tb_scala']}</td>";//平均客单价
        echo "<td align='center'>{$val['avg_buyer_price']}</td>";
        echo "<td align='center'>{$val['avg_buyer_price_hb_scala']}</td>";
        echo "<td align='center'>{$val['avg_buyer_price_tb_scala'] }</td>";//消费者
        echo "<td align='center'>{$val['buyer_user_count']}</td>";
        echo "<td align='center'>{$val['buyer_user_count_hb_scala']}</td>";
        echo "<td align='center'>{$val['buyer_user_count_tb_scala']}</td>";
        echo "<td align='center'>{$val['buyer_reply_buy_scala']}</td>"; //复购率
        echo "<td align='center'>{$val['buyer_reply_buy_scala_hb_scala']}</td>";
        echo "<td align='center'>{$val['buyer_reply_buy_scala_tb_scala']}</td>";//消费留存率
        echo "<td align='center'>{$val['seller_user_count']}</td>";
        echo "<td align='center'>{$val['seller_user_count_hb_scala']}</td>";
        echo "<td align='center'>{$val['seller_user_count_tb_scala']}</td>";
        echo "<td align='center'>{$val['avg_seller_price']}</td>";//供应商平均收入
        echo "<td align='center'>{$val['avg_seller_price_hb_scala']}</td>";
        echo "<td align='center'>{$val['avg_seller_price_tb_scala']}</td>";
        echo "<td align='center'>{$user_pin_scala}</td>"; //动销率
        echo "<td align='center'>{$val['user_pin_scala_hb_scala']}</td>";
        echo "<td align='center'>{$val['user_pin_scala_tb_scala']}</td>";
        echo "<td align='center'>{$val['new_gain_seller_num']}</td>";
        echo "<td align='center'>{$val['new_gain_seller_num_hb_scala']}</td>";
        echo "<td align='center'>{$val['new_gain_seller_num_tb_scala']}</td>";
        echo "</tr>";
    }
}
echo "<table>";