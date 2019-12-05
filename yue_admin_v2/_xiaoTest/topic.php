<?php
/**
 * @desc:   专题出数据
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/9
 * @Time:   9:41
 * version: 1.0
 */
include_once('common.inc.php');

$topic_str = trim($_INPUT['topic_str']);//查询条件

if(strlen($topic_str) <1) die("请输入条件");
for ($i = 1; $i <= 10; $i++){
    $table_num = sprintf('%02d', $i);
    $table_name = 'yueyue_log_tmp_db.yueyue_tmp_log_201511'.$table_num;
    $sql_str = "SELECT login_id,visit_time FROM {$table_name} WHERE current_page_url_unfiltered LIKE '%{$topic_str}%' AND login_id>0 GROUP BY login_id ORDER BY visit_time DESC";
    $ret = db_simple_getdata($sql_str,false,22);
    //print_r($ret);
    if(!is_array($ret) || empty($ret))
    {
        echo "暂无";
    }else
    {
        foreach($ret as $v)
        {
            $start_time = strtotime($v['visit_time']);
            $end_time = strtotime($v['visit_time'])+24*3600;
            $sql = "SELECT O.total_amount,FROM_UNIXTIME(O.pay_time) as xf_time,goods_id,goods_name,O.type_id,O.order_id FROM mall_db.mall_order_tbl AS O left join mall_db.mall_order_detail_tbl AS D on O.order_id=D.order_id WHERE O.buyer_user_id={$v['login_id']} AND O.pay_time>={$start_time} AND O.pay_time <={$end_time}";
            $order = db_simple_getdata($sql,false,101);
            if(!is_array($order)) $order = array();
            foreach($order as $val)
            {
                echo "<table>";
                echo "<tr>";
                echo "<td align='center'>11.{$table_num}</td>";
                echo "<td align='center'>{$v['login_id']}</td>";
                echo "<td align='center'>{$v['visit_time']}</td>";
                echo "<td align='center'>{$val['xf_time']}</td>";
                echo "<td align='center'>{$val['order_id']}</td>";
                echo "<td align='center'>{$val['total_amount']}</td>";
                echo "<td align='center'>{$val['goods_id']}</td>";
                echo "<td align='center'>{$val['goods_name']}</td>";
                echo "</tr>";
                echo "</table>";
            }

        }
    }
}




exit;
//获取pv和uv的
echo "<table>";
for ($i = 1; $i <= 10; $i++){
    $table_num = sprintf('%02d', $i);
    $table_name = 'yueyue_log_tmp_db.yueyue_tmp_log_201511'.$table_num;
    $sql_str = "SELECT COUNT(id) AS PV,COUNT(DISTINCT(login_id)) AS UV FROM {$table_name} WHERE current_page_url_unfiltered LIKE '%{$topic_str}%'";
    $ret = db_simple_getdata($sql_str,TRUE,22);
    echo "<tr>";
    echo "<td align='center'>11.{$table_num}</td>";
    echo "<td align='center'>{$ret['PV']}</td>";
    echo "<td align='center'>{$ret['UV']}</td>";
    echo "</tr>";
}
echo "</table>";
exit;


exit;
for ($i = 1; $i <= 8; $i++)
{
    echo "<table>";
    /*echo "<tr>";
    $table_num = sprintf('%02d', $i);
    echo "<td colspan='2'>{$topic_str}</td>";
    echo "<td colspan='2'>{$table_num}月</td>";
    echo "</tr>";*/

    $table_num = sprintf('%02d', $i);
    $table_name = 'yueyue_log_tmp_db.yueyue_tmp_log_201510'.$table_num;
    $sql_str = "SELECT COUNT(id) AS PV FROM {$table_name} WHERE current_page_url_unfiltered LIKE '%{$topic_str}%'";
    $ret = db_simple_getdata($sql_str,TRUE,22);
    echo "<tr>";
    echo "<td colspan='2' align='center'>{$table_num}月</td>";
    echo "<td colspan='2' align='center'>PV:{$ret['PV']}</td>";
    echo "</tr>";

    /*$table_name = 'yueyue_log_tmp_db.yueyue_tmp_log_201510'.$table_num;
    $sql_str = "SELECT login_id,visit_time FROM {$table_name} WHERE current_page_url_unfiltered LIKE '%{$topic_str}%' AND login_id>0 GROUP BY login_id ORDER BY id ASC";
    $ret = db_simple_getdata($sql_str,false,22);
    if(!is_array($ret) || empty($ret))
    {
        echo "<tr><td colspan='4'>暂无</td></tr>";
    }else
    {
        foreach($ret as &$v)
        {
            echo "<tr>";
            echo "<td>用户ID</td>";
            echo "<td>访问时间</td>";
            echo "<td>24内下单金额</td>";
            echo "<td>24内下单数</td>";
            echo "</tr>";
            $start_time = strtotime($v['visit_time']);
            $end_time = strtotime($v['visit_time'])+24*3600;
            $sql = "SELECT SUM(total_amount) AS total_price,COUNT(order_id) as total_order FROM mall_db.mall_order_tbl WHERE buyer_user_id={$v['login_id']} AND add_time>={$start_time} AND add_time <={$end_time}";
            $order = db_simple_getdata($sql,true,101);
            $v['total_price'] = sprintf('%.2f',$order['total_price']) ;
            $v['total_order'] = intval($order['total_order']);
            echo "<tr>";
            echo "<td>{$v['login_id']}</td>";
            echo "<td>{$v['visit_time']}</td>";
            echo "<td>{$v['total_price']}</td>";
            echo "<td>{$v['total_order']}</td>";
            echo "</tr>";
            unset($order);
        }
    } */
    echo "</table>";
    unset($ret);
}

