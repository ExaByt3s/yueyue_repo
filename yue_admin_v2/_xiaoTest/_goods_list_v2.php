<?php
/**
 * @desc:   ��Ʒ����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/18
 * @Time:   11:07
 * version: 1.0
 */

include_once('common.inc.php');


$begin_month = '2015-10';
$prev_time = date('Y-m',strtotime($begin_month.'-01')-24*3600);











// +-----------------------------------------------------------------------------------
// | ��ȡ���̳ǡ��±�������
// +-----------------------------------------------------------------------------------
/*
$type_id = intval($_INPUT['type_id']);
if($type_id <0) exit('type_id ����Ϊ��');

//��Ҫȥ���Ļ���ID
global $org_user_str;
$org_user_str = '111715,119879,124214';
echo "<table>";
echo "<tr><th colspan='3'>{$type_id}</th><th colspan='2'>{$begin_month}-��{$prev_time}</th></tr>";
echo "<tr>";
echo "<th>���ܶ�</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>ƽ���͵���</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>����������ȥ�أ�</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>�¿���</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>�����빩Ӧ������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>��Ӧ��ƽ������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>������</th>"; //209���û�
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>���������빩Ӧ��</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "</tr>";

//���ܶ�
$this_order_ret = get_order_date($begin_month,$type_id);//����
$prev_order_ret = get_order_date($prev_time,$type_id); //����
$this_order_ret['order_hb_scala'] = sprintf('%.4f',$this_order_ret['total_price']/$prev_order_ret['total_price']);//����
$this_order_ret['order_tb_scala'] = sprintf('%.4f',($this_order_ret['total_price']-$prev_order_ret['total_price'])/$prev_order_ret['total_price']); //ͬ��
//����
$this_order_ret['order_num_hb_scala'] = sprintf('%.4f',$this_order_ret['C']/$prev_order_ret['C']);
$this_order_ret['order_num_tb_scala'] = sprintf('%.4f',($this_order_ret['C']-$prev_order_ret['C'])/$prev_order_ret['C']);

//�͵��������̼�����
$this_user_ret = get_buyer_count($begin_month,$type_id);
$prev_user_ret = get_buyer_count($prev_time,$type_id);
//���ϸ��µ�
$prev_prev_user_ret = get_buyer_count(date('Y-m',strtotime($prev_time.'-01')-24*3600),$type_id);

$avg_buyer_price = sprintf('%.4f',$this_order_ret['total_price']/$this_user_ret['b']);
$prev_avg_buyer_price = sprintf('%.4f',$prev_order_ret['total_price']/$prev_user_ret['b']);
$this_user_ret['avg_buyer_price'] = $avg_buyer_price;
$this_user_ret['avg_buyer_price_hb_scala'] = sprintf('%.4f',$avg_buyer_price/$prev_avg_buyer_price);//����
$this_user_ret['avg_buyer_price_tb_scala'] = sprintf('%.4f',($avg_buyer_price-$prev_avg_buyer_price)/$prev_avg_buyer_price);//ͬ��
//������
$this_user_ret['b_hb_scala'] = sprintf('%.4f',$this_user_ret['b']/$prev_user_ret['b']); //����
$this_user_ret['b_tb_scala'] = sprintf('%.4f',($this_user_ret['b']-$prev_user_ret['b'])/$prev_user_ret['b']); //ͬ��

$new_twice_ret = array();
//�����ʡ���������-����������/��������
$this_twice_pay = sprintf('%.4f',($this_order_ret['C']-$this_user_ret['b'])/$this_order_ret['C']);
$prev_twice_pay = sprintf('%.4f',($prev_order_ret['C']-$prev_user_ret['b'])/$prev_order_ret['C']);
$new_twice_ret['twice_pay'] = $this_twice_pay;
$new_twice_ret['twice_pay_hb_scala'] = sprintf('%.4f',$this_twice_pay/$prev_twice_pay);
$new_twice_ret['twice_pay_tb_scala'] = sprintf('%.4f',($this_twice_pay-$prev_twice_pay)/$prev_twice_pay);
//�û�������
$new_user_twice_ret = array();
$this_user_twice_num = sprintf('%.4f',get_twice_info($begin_month,$type_id)/$prev_user_ret['b']);
$prev_user_twice_num = sprintf('%.4f',get_twice_info($prev_time,$type_id)/$prev_prev_user_ret['b']);
$new_user_twice_ret['user_twice_num'] = $this_user_twice_num;
$new_user_twice_ret['user_twice_num_hb_scala'] = sprintf('%.4f',$this_user_twice_num/$prev_user_twice_num);
$new_user_twice_ret['user_twice_num_tb_scala'] = sprintf('%.4f',($this_user_twice_num-$prev_user_twice_num)/$prev_user_twice_num); //ͬ��
//�¿���
$new_user_ret = array();
$this_new_user_num = get_new_user_num($begin_month,$type_id,$this_user_ret['b']);
$prev_new_user_num = get_new_user_num($prev_time,$type_id,$prev_user_ret['b']);
$new_user_ret['new_user_num'] = $this_new_user_num;
$new_user_ret['new_user_num_hb_scala'] = sprintf('%.4f',$this_new_user_num/$prev_new_user_num);
$new_user_ret['new_user_num_tb_scala'] = sprintf('%.4f',($this_new_user_num-$prev_new_user_num)/$prev_new_user_num);


//��Ӧ�̴���
$this_user_ret['s_hb_scala'] = sprintf('%.4f',$this_user_ret['s']/$prev_user_ret['s']);
$this_user_ret['s_tb_scala'] = sprintf('%.4f',($this_user_ret['s']-$prev_user_ret['s'])/$prev_user_ret['s']);
//��Ӧ��ƽ������
$avg_seller_price = sprintf('%.4f',$this_order_ret['total_price']/$this_user_ret['s']);
$prev_avg_seller_price = sprintf('%.4f',$prev_order_ret['total_price']/$prev_user_ret['s']);
$this_user_ret['avg_seller_price'] = $avg_seller_price;
$this_user_ret['avg_seller_price_hb_scala'] = sprintf('%.4f',$avg_seller_price/$prev_avg_seller_price);
$this_user_ret['avg_seller_price_tb_scala'] = sprintf('%.4f',($avg_seller_price-$prev_avg_seller_price)/$prev_avg_seller_price); //ͬ��

//��ȡ�û�������
$new_pin_arr = array();
$this_user_pin = get_seller_count($begin_month,$type_id,$this_user_ret['s']);
$prev_user_pin = get_seller_count($prev_time,$type_id,$prev_user_ret['s']);
$new_pin_arr['user_pin'] =$this_user_pin;
$new_pin_arr['user_pin_hb_scala'] = sprintf('%.4f',$this_user_pin/$prev_user_pin);
$new_pin_arr['user_pin_tb_scala'] = sprintf('%.4f',($this_user_pin-$prev_user_pin)/$prev_user_pin);

//���������빩Ӧ��
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
//ƽ���͵���
echo "<td align='center'>{$this_user_ret['avg_buyer_price']}</td>";
echo "<td align='center'>{$this_user_ret['avg_buyer_price_hb_scala']}</td>";
echo "<td align='center'>{$this_user_ret['avg_buyer_price_tb_scala'] }</td>";
//������
echo "<td align='center'>{$this_user_ret['b']}</td>";
echo "<td align='center'>{$this_user_ret['b_hb_scala']}</td>";
echo "<td align='center'>{$this_user_ret['b_tb_scala']}</td>";
//������
echo "<td align='center'>{$new_twice_ret['twice_pay']}</td>";
echo "<td align='center'>{$new_twice_ret['twice_pay_hb_scala']}</td>";
echo "<td align='center'>{$new_twice_ret['twice_pay_tb_scala']}</td>";
//����������
echo "<td align='center'>{$new_user_twice_ret['user_twice_num']}</td>";
echo "<td align='center'>{$new_user_twice_ret['user_twice_num_hb_scala']}</td>";
echo "<td align='center'>{$new_user_twice_ret['user_twice_num_tb_scala']}</td>";
//�¿���
echo "<td align='center'>{$new_user_ret['new_user_num']}</td>";
echo "<td align='center'>{$new_user_ret['new_user_num_hb_scala']}</td>";
echo "<td align='center'>{$new_user_ret['new_user_num_tb_scala']}</td>";
//�����빩Ӧ������
echo "<td align='center'>{$this_user_ret['s']}</td>";
echo "<td align='center'>{$this_user_ret['s_hb_scala']}</td>";
echo "<td align='center'>{$this_user_ret['s_tb_scala']}</td>";
//��Ӧ��ƽ������
echo "<td align='center'>{$this_user_ret['avg_seller_price']}</td>";
echo "<td align='center'>{$this_user_ret['avg_seller_price_hb_scala']}</td>";
echo "<td align='center'>{$this_user_ret['avg_seller_price_tb_scala']}</td>";
//������
echo "<td align='center'>{$new_pin_arr['user_pin']}</td>";
echo "<td align='center'>{$new_pin_arr['user_pin_hb_scala']}</td>";
echo "<td align='center'>{$new_pin_arr['user_pin_tb_scala']}</td>";
//���������빩Ӧ��
echo "<td align='center'>{$new_seller_ret['new_seller_num']}</td>";
echo "<td align='center'>{$new_seller_ret['new_seller_num_hb_scala']}</td>";
echo "<td align='center'>{$new_pin_arr['user_pin_tb_scala']}</td>";
echo "</tr>";
echo "<table>";


//��ȡ�������ͽ��
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

//��ȡ�����߸������̼Ҹ���
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

//�ϸ��º�����������ѵ��û���
function get_twice_info($begin_month,$type_id)
{
    global $org_user_str;
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month) || $type_id <1) return false;
    $prev_month = date('Y-m',strtotime($begin_month.'-01')-24*3600); //�ϸ���
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

//�¿Ϳ���������ȡ��ǰû���������Ʒ���û����������Ҳ������Ʒ����
function get_new_user_num($begin_month,$type_id,$user_num)
{
    global $org_user_str;
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    $user_num = (int)$user_num;//����µ��û�����
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

//������
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

//��ȡ���������빩Ӧ������
function get_new_seller_num($begin_month,$type_id,$user_num)
{
    global $org_user_str;
    $begin_month = trim($begin_month);;
    $type_id = (int)$type_id;
    $user_num = (int)$user_num;//����µ��û�����
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
// |  �����ġ��·����ݻ�ȡ
// +------------------------------------------------------------------------------------------
echo "<table>";
echo "<tr><th colspan='2'>{$begin_month}-��{$prev_time}</th></tr>";
echo "<tr>";
echo "<th>���ܶ�</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>ƽ���͵���</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>����������ȥ�أ�</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>�¿���</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>�����빩Ӧ������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>��Ӧ��ƽ������</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>������</th>"; //209���û�
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "<th>���������빩Ӧ��</th>";
echo "<th>����</th>";
echo "<th>ͬ��</th>";
echo "</tr>";

//���۶�Ͷ�����
$this_order_result = get_waipai_order($begin_month);
$prev_order_result = get_waipai_order($prev_time);
$this_order_result['order_hb_scala'] = sprintf('%.4f',$this_order_result['total_price']/$prev_order_result['total_price']);//����
$this_order_result['order_tb_scala'] = sprintf('%.4f',($this_order_result['total_price']-$prev_order_result['total_price'])/$prev_order_result['total_price']); //ͬ��
//������
$this_order_result['order_num_hb_scala'] = sprintf('%.4f',$this_order_result['C']/$prev_order_result['C']); //����
$this_order_result['order_num_tb_scala'] = sprintf('%.4f',($this_order_result['C']-$prev_order_result['C'])/$prev_order_result['C']);//ͬ��
//�ͺ��̼�����
$this_user_result = get_role_user_count($begin_month);
$prev_user_result = get_role_user_count($prev_time);

//���ϸ���
$prev_prev_user_result = get_role_user_count(date('Y-m',strtotime($prev_time.'-01')-24*3600));
$avg_buyer_price = sprintf('%.4f',$this_order_result['total_price']/$this_user_result['b']);
$prev_avg_buyer_price = sprintf('%.4f',$prev_order_result['total_price']/$prev_user_result['b']);

$this_user_result['avg_buyer_price'] = $avg_buyer_price;
$this_user_result['avg_buyer_price_hb_scala'] = sprintf('%.4f',$avg_buyer_price/$prev_avg_buyer_price);
$this_user_result['avg_buyer_price_tb_scala'] = sprintf('%.4f',($avg_buyer_price-$prev_avg_buyer_price)/$prev_avg_buyer_price);//����
//��������
$this_user_result['b_hb_scala'] = sprintf('%.4f',$this_user_result['b']/$prev_user_result['b']); //����
$this_user_result['b_tb_scala'] = sprintf('%.4f',($this_user_result['b']-$prev_user_result['b'])/$prev_user_result['b']); //ͬ��
//�����ʡ���������-����������/��������
$new_twice_result = array();
$this_twice_pay = sprintf('%.4f',($this_order_result['C']-$this_user_result['b'])/$this_order_result['C']);
$prev_twice_pay = sprintf('%.4f',($prev_order_result['C']-$prev_user_result['b'])/$prev_order_result['C']);
$new_twice_result['twice_pay'] = $this_twice_pay;
$new_twice_result['twice_pay_hb_scala'] = sprintf('%.4f',$this_twice_pay/$prev_twice_pay);
$new_twice_result['twice_pay_tb_scala'] = sprintf('%.4f',($this_twice_pay-$prev_twice_pay)/$prev_twice_pay); //ͬ��
//�û������ʡ�ͬƷ�����������Ѽ�¼���³�����������/����������������
$new_user_twice_result = array();
$this_twice_num = get_twice_info($begin_month);
$prev_twice_num = get_twice_info($prev_time);
$this_user_twice_num = sprintf('%.4f',$this_twice_num/$prev_user_result['b']);
$prev_user_twice_num = sprintf('%.4f',$prev_twice_num/$prev_prev_user_result['b']);
$new_user_twice_result['user_twice_num'] = $this_user_twice_num;
$new_user_twice_result['user_twice_num_hb_scala'] = sprintf('%.4f',$this_user_twice_num/$prev_user_twice_num);
$new_user_twice_result['user_twice_num_tb_scala'] = sprintf('%.4f',($this_user_twice_num-$prev_user_twice_num)/$prev_user_twice_num); //ͬ��

//�����¿��ʡ���Ʒ�����û�����Ѽ�¼����/����������������
$new_user_result = array();
$this_new_user_num = get_new_add_user_count($begin_month);
$prev_new_user_num = get_new_add_user_count($prev_time);
$new_user_result['new_user_num'] = $this_new_user_num;
$new_user_result['new_user_num_hb_scala'] = sprintf('%.4f',$this_new_user_num/$prev_new_user_num);
$new_user_result['new_user_num_tb_scala'] = sprintf('%.4f',($this_new_user_num-$prev_new_user_num)/$prev_new_user_num);

//�����빩Ӧ������
$this_user_result['s_hb_scala'] = sprintf('%.4f',$this_user_result['s']/$prev_user_result['s']);
$this_user_result['s_tb_scala'] = sprintf('%.4f',($this_user_result['s']-$prev_user_result['s'])/$prev_user_result['s']);
//��Ӧ��ƽ������
$avg_seller_price = sprintf('%.4f',$this_order_result['total_price']/$this_user_result['s']);
$prev_avg_seller_price = sprintf('%.4f',$prev_order_result['total_price']/$prev_user_result['s']);
$this_user_result['avg_seller_price'] = $avg_seller_price;
$this_user_result['avg_seller_price_hb_scala'] = sprintf('%.4f',$avg_seller_price/$prev_avg_seller_price);
$this_user_result['avg_seller_price_tb_scala'] = sprintf('%.4f',($avg_seller_price-$prev_avg_seller_price)/$prev_avg_seller_price); //ͬ��
//��ȡ�û��������н��׹�Ӧ������/�ܹ�Ӧ��������
$new_pin_result = array();
$this_user_pin = sprintf('%.4f',$this_user_result['s']/209);
$prev_user_pin = sprintf('%.4f',$prev_user_result['s']/209);;
$new_pin_result['user_pin'] =$this_user_pin;
$new_pin_result['user_pin_hb_scala'] = sprintf('%.4f',$this_user_pin/$prev_user_pin);
$new_pin_result['user_pin_tb_scala'] = sprintf('%.4f',($this_user_pin-$prev_user_pin)/$prev_user_pin);
//���������빩Ӧ�̡���Ʒ�����û�н��׼�¼�����н��׼�¼������
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
//ƽ���͵���
echo "<td align='center'>{$this_user_result['avg_buyer_price']}</td>";
echo "<td align='center'>{$this_user_result['avg_buyer_price_hb_scala']}</td>";
echo "<td align='center'>{$this_user_result['avg_buyer_price_tb_scala']}</td>";
//������
echo "<td align='center'>{$this_user_result['b']}</td>";
echo "<td align='center'>{$this_user_result['b_hb_scala']}</td>";
echo "<td align='center'>{$this_user_result['b_tb_scala']}</td>";
//������
echo "<td align='center'>{$new_twice_result['twice_pay']}</td>";
echo "<td align='center'>{$new_twice_result['twice_pay_hb_scala']}</td>";
echo "<td align='center'>{$new_twice_result['twice_pay_tb_scala']}</td>";
//����������
echo "<td align='center'>{$new_user_twice_result['user_twice_num']}</td>";
echo "<td align='center'>{$new_user_twice_result['user_twice_num_hb_scala']}</td>";
echo "<td align='center'>{$new_user_twice_result['user_twice_num_tb_scala']}</td>";
//�¿���
echo "<td align='center'>{$new_user_result['new_user_num']}</td>";
echo "<td align='center'>{$new_user_result['new_user_num_hb_scala']}</td>";
echo "<td align='center'>{$new_user_result['new_user_num_tb_scala']}</td>";
//�����빩Ӧ������
echo "<td align='center'>{$this_user_result['s']}</td>";
echo "<td align='center'>{$this_user_result['s_hb_scala']}</td>";
echo "<td align='center'>{$this_user_result['s_tb_scala']}</td>";
//��Ӧ��ƽ������
echo "<td align='center'>{$this_user_result['avg_seller_price']}</td>";
echo "<td align='center'>{$this_user_result['avg_seller_price_hb_scala']}</td>";
echo "<td align='center'>{$this_user_result['avg_seller_price_tb_scala']}</td>";
//������
echo "<td align='center'>{$new_pin_result['user_pin']}</td>";
echo "<td align='center'>{$new_pin_result['user_pin_hb_scala']}</td>";
echo "<td align='center'>{$new_pin_result['user_pin_tb_scala']}</td>";
//���������빩Ӧ��
echo "<td align='center'>{$new_seller_result['new_seller_num']}</td>";
echo "<td align='center'>{$new_seller_result['new_seller_num_hb_scala']}</td>";
echo "<td align='center'>{$new_seller_result['new_seller_num_tb_scala']}</td>";
echo "</tr>";
echo "<table>";




//��ȡ���Ķ�������
function get_waipai_order($begin_month)
{
    $begin_month = trim($begin_month);;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month)) return false;
    $sql_str = "SELECT COUNT(*) AS  C,SUM(enroll_num*budget) AS total_price FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')='{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $ret = db_simple_getdata($sql_str, true, 22);
    if(!is_array($ret)) $ret = array();
    return $ret;
}
//��ȡ�����߸������̼Ҹ���
function get_role_user_count($begin_month)
{
    $begin_month = trim($begin_month);;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month)) return false;
    $sql_str = "SELECT COUNT(DISTINCT(from_date_id)) AS b ,COUNT(DISTINCT(to_date_id)) AS s FROM yueyue_stat_db.yueyue_event_order_tbl WHERE complete_time !=0 AND FROM_UNIXTIME(complete_time,'%Y-%m')='{$begin_month}' AND pay_status=1 AND event_status='2' AND enroll_status !=2 AND date_id=0";
    $ret = db_simple_getdata($sql_str, true, 22);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//�����й�����û�
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

//��ȡ�����ϸ��º�����������ѵ��û���
function get_twice_info($begin_month)
{
    $begin_month = trim($begin_month);;
    if(!preg_match("/\d\d\d\d-\d\d/", $begin_month)) return false;
    $prev_month = date('Y-m',strtotime($begin_month.'-01')-24*3600); //�ϸ���
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

//������������̼�
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
