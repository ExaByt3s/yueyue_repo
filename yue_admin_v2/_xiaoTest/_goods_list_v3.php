<?php
/**
 * @desc:   ��Ʒ����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/18
 * @Time:   11:07
 * version: 1.0
 */

include_once('common.inc.php');

$type_list = array(
    0=>array('id'=>3,'name'=>'��ױ����'),
    1=>array('id'=>5,'name'=>'��Ӱ��ѵ'),
    2=>array('id'=>12,'name'=>'Ӱ������'),
    3=>array('id'=>31,'name'=>'ģ����Լ'),
    4=>array('id'=>40,'name'=>'��Ӱ����'),
    5=>array('id'=>41,'name'=>'Լ��ʳ'),
    6=>array('id'=>43,'name'=>'Լ��Ȥ'),
    7=>array('id'=>99,'name'=>'���Ļ')
);

echo "<table>";
echo "<tr>";
echo "<th>����</th>";
echo "<th>ʱ��</th>";
echo "<th>�������ܶ�</th>";
echo "<th>���ܻ���</th>";
echo "<th>����ͬ��ͬ��</th>";
echo "<th>������</th>";
echo "<th>���ܻ���</th>";
echo "<th>����ͬ��ͬ��</th>";
echo "<th>ƽ���͵���</th>";
echo "<th>���ܻ���</th>";
echo "<th>����ͬ��ͬ��</th>";
echo "<th>����������ȥ�أ�</th>";
echo "<th>���ܻ���</th>";
echo "<th>����ͬ��ͬ��</th>";
echo "<th>������</th>";
echo "<th>���ܻ���</th>";
echo "<th>����ͬ��ͬ��</th>";
echo "<th>�����빩Ӧ������</th>";
echo "<th>���ܻ���</th>";
echo "<th>����ͬ��ͬ��</th>";
echo "<th>��Ӧ��ƽ������</th>";
echo "<th>���ܻ���</th>";
echo "<th>����ͬ��ͬ��</th>";
echo "<th>������</th>";
echo "<th>���ܻ���</th>";
echo "<th>����ͬ��ͬ��</th>";
echo "<th>���������빩Ӧ��</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
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
        echo "<td align='center'>{$val['order_num_tb_scala']}</td>";//ƽ���͵���
        echo "<td align='center'>{$val['avg_buyer_price']}</td>";
        echo "<td align='center'>{$val['avg_buyer_price_hb_scala']}</td>";
        echo "<td align='center'>{$val['avg_buyer_price_tb_scala'] }</td>";//������
        echo "<td align='center'>{$val['buyer_user_count']}</td>";
        echo "<td align='center'>{$val['buyer_user_count_hb_scala']}</td>";
        echo "<td align='center'>{$val['buyer_user_count_tb_scala']}</td>";
        echo "<td align='center'>{$val['buyer_reply_buy_scala']}</td>"; //������
        echo "<td align='center'>{$val['buyer_reply_buy_scala_hb_scala']}</td>";
        echo "<td align='center'>{$val['buyer_reply_buy_scala_tb_scala']}</td>";//����������
        echo "<td align='center'>{$val['seller_user_count']}</td>";
        echo "<td align='center'>{$val['seller_user_count_hb_scala']}</td>";
        echo "<td align='center'>{$val['seller_user_count_tb_scala']}</td>";
        echo "<td align='center'>{$val['avg_seller_price']}</td>";//��Ӧ��ƽ������
        echo "<td align='center'>{$val['avg_seller_price_hb_scala']}</td>";
        echo "<td align='center'>{$val['avg_seller_price_tb_scala']}</td>";
        echo "<td align='center'>{$user_pin_scala}</td>"; //������
        echo "<td align='center'>{$val['user_pin_scala_hb_scala']}</td>";
        echo "<td align='center'>{$val['user_pin_scala_tb_scala']}</td>";
        echo "<td align='center'>{$val['new_gain_seller_num']}</td>";
        echo "<td align='center'>{$val['new_gain_seller_num_hb_scala']}</td>";
        echo "<td align='center'>{$val['new_gain_seller_num_tb_scala']}</td>";
        echo "</tr>";
    }
}
echo "<table>";