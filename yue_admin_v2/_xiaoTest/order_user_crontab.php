<?php
/**
 * @desc:   �û���Ϣ��Ϣ
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/5
 * @Time:   14:22
 * version: 1.0
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
echo "test5";
$user_obj = POCO::singleton('pai_user_class');//�û���
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");//����
$sql_str = "SELECT user_id,nickname,location_id FROM pai_db.pai_user_tbl WHERE user_id >= 100000 LIMIT 30000,1000";

$end_time = '2015-11-04';
$result = db_simple_getdata($sql_str,FALSE,101);
if(!is_array($result)) $result = array();

echo "<table border='1'>";
echo "<tr>";
echo "<th>�û�ID</th>";
echo "<th>�û���</th>";
echo "<th>�绰����</th>";
echo "<th>�û�����¼ʱ��</th>";
echo "<th>����</th>";
echo "<th>�ܵ���</th>";
echo "<th>�����</th>";
echo "<th>�ѹر�</th>";
echo "<th>�����</th>";
echo "<th>֧����</th>";
echo "<th>Լ��Ӱʦ</th>";
echo "<th>Ʒ�ඩ������</th>";
echo "<th>�û�����չ���൥��</th>";
echo "<th>ģ�ط���</th>";
echo "<th>Ʒ�ඩ������</th>";
echo "<th>�û�����չ���൥��</th>";
echo "<th>��ױ����</th>";
echo "<th>Ʒ�ඩ������</th>";
echo "<th>�û�����չ���൥��</th>";
echo "<th>Ӱ������</th>";
echo "<th>Ʒ�ඩ������</th>";
echo "<th>�û�����չ���൥��</th>";
echo "<th>Լ��ѵ</th>";
echo "<th>Ʒ�ඩ������</th>";
echo "<th>�û�����չ���൥��</th>";
echo "<th>Լ��ʳ</th>";
echo "<th>Ʒ�ඩ������</th>";
echo "<th>�û�����չ���൥��</th>";
echo "<th>Լ�</th>";
echo "<th>Ʒ�ඩ������</th>";
echo "<th>�û�����չ���൥��</th>";
echo "<th>Լ��Ȥ</th>";
echo "<th>Ʒ�ඩ������</th>";
echo "<th>�û�����չ���൥��</th>";
/*
echo "<th>�ܱ�</th>";
echo "<th>�±�</th>";
echo "<th>��Ӧ��ƽ������</th>";
echo "<th>�ܱ�</th>";
echo "<th>�±�</th>";
echo "<th>������</th>"; //209���û�
echo "<th>�ܱ�</th>";
echo "<th>�±�</th>";
echo "<th>���������빩Ӧ��</th>";
echo "<th>�ܱ�</th>";
echo "<th>�±�</th>";*/
echo "</tr>";
foreach($result as $v)
{
    $order_result = get_order_list($v['user_id'],$end_time);
    $userphone= $user_obj->get_phone_by_user_id($v['user_id']);
    $location_name = get_poco_location_name_by_location_id($v['location_id']);
    $get_out_result = get_user_price($v['user_id'],$end_time);
    $type_result = get_type_id($v['user_id'],$end_time);
    $last_time = get_last_login_time($v['user_id']);

    //$sql_insert_str = "INSERT INTO TABLE (user_id,nickname,userphone,last_time,location_name,total_order)"
    /*echo "<tr>";
    echo "<td align='center'>{$v['user_id']}</td>"; //���۶�
    echo "<td align='center'>{$v['nickname']}</td>";
    echo "<td align='center'>{$userphone}</td>";
    echo "<td align='center'>{$last_time}</td>";
    echo "<td align='center'>{$location_name}</td>";
    echo "<td align='center'>{$order_result['total_order']}</td>";//����
    echo "<td align='center'>{$order_result['S']}</td>";
    echo "<td align='center'>{$order_result['C']}</td>";
    echo "<td align='center'>{$get_out_result['S']}</td>";
    echo "<td align='center'>{$get_out_result['B']}</td>";
    if(!is_array($type_result)) $type_result = array();
    foreach($type_result as $val)
    {
        echo "<td align='center'>{$val['C']}</td>";
        echo "<td align='center'>{$val['avg_price']}</td>";
        echo "<td align='center'>{$val['cat_str']}</td>";
    }*/
    //print_r($type_result);
}
echo "<table>";

//����
function get_order_list($user_id,$end_time)
{

    $user_id = (int)$user_id;
    $end_time = trim($end_time);
    if($user_id <1 || !preg_match("/\d\d\d\d-\d\d-\d\d/", $end_time) ) return false;
    $sql_str = "SELECT COUNT(order_id) AS total_order,SUM(CASE 8 WHEN `status` THEN 1 ELSE 0 END) AS S,SUM(CASE 7 WHEN `status` THEN 1 ELSE 0 END) AS C FROM mall_db.mall_order_tbl WHERE status IN (7,8) AND (FROM_UNIXTIME(sign_time,'%Y-%m-%d')<='".mysql_escape_string($end_time)."' OR (FROM_UNIXTIME(close_time,'%Y-%m-%d')<='".mysql_escape_string($end_time)."')) AND buyer_user_id={$user_id}";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//�û���ˮ
function get_user_price($user_id,$end_time)
{
    //BΪ֧����S����
    $user_id = (int)$user_id;
    $end_time = trim($end_time);
    if($user_id <1 || !preg_match("/\d\d\d\d-\d\d-\d\d/", $end_time) ) return false;
    $sql_str = "SELECT SUM(CASE {$user_id} WHEN buyer_user_id THEN total_amount ELSE 0 END ) AS B,SUM(CASE {$user_id} WHEN seller_user_id THEN total_amount ELSE 0 END ) AS S FROM mall_db.mall_order_tbl WHERE `status`=8 AND (buyer_user_id={$user_id} OR seller_user_id={$user_id}) AND (FROM_UNIXTIME(sign_time,'%Y-%m-%d')<='".mysql_escape_string($end_time)."');";
    $ret = db_simple_getdata($sql_str, true, 101);
    if(!is_array($ret)) $ret = array();
    return $ret;
}

//��ȡ��������
function get_type_id($user_id,$end_time)
{
    $result_arr = array();
    $type_obj = POCO::singleton('pai_mall_goods_type_class');//��ƷƷ��
    if($user_id <1 || !preg_match("/\d\d\d\d-\d\d-\d\d/", $end_time) ) return false;
    $type_list = $type_obj->get_type_cate(2);
    foreach($type_list as $v)
    {
        $sql_str = "SELECT COUNT(order_id) AS C,SUM(CASE 8 WHEN `status` THEN 1 ELSE 0 END) AS success_order,SUM(CASE 8 WHEN `status` THEN total_amount ELSE 0 END) AS success_price FROM mall_db.mall_order_tbl WHERE type_id={$v['id']} AND `status` IN (7,8) AND (FROM_UNIXTIME(sign_time,'%Y-%m-%d')<='".mysql_escape_string($end_time)."'
        OR FROM_UNIXTIME(close_time,'%Y-%m-%d')<='".mysql_escape_string($end_time)."') AND buyer_user_id={$user_id} ;";
        $ret = db_simple_getdata($sql_str, true, 101);
        if(!is_array($ret))$ret = array();
        $ret['cat_str'] = get_type_id_info($v['id'],$end_time,$user_id);
        $ret['name'] = $v['name'];
        $ret['avg_price'] = sprintf('%.2f',$ret['success_price']/$ret['success_order']);
        $ret['name'] = $v['name'];
        $result_arr[] = $ret;
    }
    return $result_arr;
}

function get_type_id_info($type_id,$end_time,$user_id)
{
    $arr= array();
    $cat_arr = array();
    $user_id = (int)$user_id;
    $type_id = (int)$type_id;
    $end_time = trim($end_time);
    if($user_id <1  || $type_id <1 || !preg_match("/\d\d\d\d-\d\d-\d\d/", $end_time) ) return false;
    $sql_str = "SELECT order_id FROM mall_db.mall_order_tbl WHERE type_id={$type_id} AND `status` IN (7,8) AND (FROM_UNIXTIME(sign_time,'%Y-%m-%d')<='".mysql_escape_string($end_time)."'
        OR FROM_UNIXTIME(close_time,'%Y-%m-%d')<='".mysql_escape_string($end_time)."') AND buyer_user_id={$user_id} ;";
    $ret = db_simple_getdata($sql_str, false, 101);
    if(!is_array($ret)) $ret = array();
    $sql_tmp_str = '';
    foreach($ret as $v)
    {
       if(strlen($sql_tmp_str)>0) $sql_tmp_str .= ',';
        $sql_tmp_str .= $v['order_id'];
    }
    unset($ret);
    if(strlen($sql_tmp_str)>0)
    {
        $sql_str = "SELECT goods_id FROM mall_db.mall_order_detail_tbl WHERE order_id IN ({$sql_tmp_str})";
        $result = db_simple_getdata($sql_str, false, 101);
        if(!is_array($result)) $result = array();
        $i = 0;
        foreach($result as $val)
        {
            $cat_name = get_little_cate_by_goods_id($val['goods_id']);
            if(count($cat_arr) == 0)
            {
                $cat_arr[] = $cat_name;
                $arr[$i]['cat_name'] = $cat_name;
                $arr[$i]['num'] = 1;
            }
            if(count($cat_arr)>0)
            {
                if(!in_array($cat_name,$cat_arr))
                {
                    $cat_arr[] = $cat_name;
                    $arr[$i]['cat_name'] = $cat_name;
                    $arr[$i]['num'] = 1;
                }
                 if(in_array($cat_name,$cat_arr))
                 {
                     foreach($arr as $key=>$vall)
                     {
                         if($vall['cat_name'] == $cat_name)
                         {
                             $arr[$key]['num'] ++;
                             break;
                         }
                     }
                 }
            }
            $i ++;
        }
        unset($cat_arr);
        if(!is_array($arr)) $arr = array();
        $cat_str = '';
        foreach($arr as $vo)
        {
            if(strlen($cat_str)>0) $cat_str .= '|';
            $cat_str .= "{$vo['cat_name']}=>{$vo['num']}";
        }
        return $cat_str;
    }
}

//��ȡС������
function get_little_cate_by_goods_id($goods_id)
{
    $mall_goods_obj = POCO::singleton('pai_mall_goods_class');//��Ʒ��
    $goods_id = intval($goods_id);
    if($goods_id <1) return '--';
    $goods_info = $mall_goods_obj->get_goods_info($goods_id);
    if(is_array($goods_info) && !empty($goods_info))
    {
        $system_data = $goods_info['system_data'];
        if(!is_array($system_data)) $system_data = array();
        foreach($system_data as $system_next)
        {
            foreach($system_next['child_data'] as $cate_type)
            {
                if($cate_type['key'] == $system_next['value']) return $cate_type['name'];

            }
        }
    }
    return '--';
}

//��ȡ����½ʱ��
function get_last_login_time($user_id)
{
    $sql_str = "SELECT last_login_time FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201511 WHERE  user_id={$user_id} ORDER BY last_login_time DESC LIMIT 0,1";
    $result = db_simple_getdata($sql_str, true, 22);
    if(!$result)
    {
        $sql_str = "SELECT last_login_time FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201510 WHERE  user_id={$user_id} ORDER BY last_login_time DESC LIMIT 0,1";
        $result = db_simple_getdata($sql_str, true, 22);
        if(!$result)
        {
            $sql_str = "SELECT last_login_time FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201509 WHERE  user_id={$user_id} ORDER BY last_login_time DESC LIMIT 0,1";
            $result = db_simple_getdata($sql_str, true, 22);
            if(!$result)
            {
                $sql_str = "SELECT last_login_time FROM yueyue_log_tmp_v2_db.yueyue_user_last_login_log_tbl_201508 WHERE  user_id={$user_id} ORDER BY last_login_time DESC LIMIT 0,1";
                $result = db_simple_getdata($sql_str, true, 22);
            }
        }
    }
    if(!is_array($result)) $result = array();
    return $result['last_login_time'];

}
