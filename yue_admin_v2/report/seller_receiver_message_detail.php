<?php
/**
 * @desc:   商家接收信息
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/20
 * @Time:   16:07
 * version: 1.0
 */
include_once('common.inc.php');

//$month = trim($_INPUT['month']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$receive_id = intval($_INPUT['receive_id']);

if($receive_id <1) exit('非法操作');

$where_str ='';


if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = strtotime($start_date);
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = strtotime($end_date);
}

if($start_date < strtotime('2015-10-19') || $start_date >= strtotime(date('Y-m-d',time()))) die('开始时间还未录内容部分或者选择的日期不对');
if($end_date < strtotime('2015-10-19') || $end_date >= strtotime(date('Y-m-d',time()))) die('结束时间还未录内容部分或者选择的日期不对');

$sql_str = "";
while($start_date <=$end_date)
{
    if(strlen($sql_str)>0)
    {
        $sql_str .= " UNION ";
        $sql_str .= "SELECT server_id,`key`,date_time FROM yueyue_recovery_rate_db.yueyue_recovery_rate_log_".date('Ymd',$start_date)." WHERE (receive_id={$receive_id} AND receive_identity='yueseller' AND sender_id>=100000) OR (sender_id={$receive_id} AND sender_identity='yueseller' AND receive_id>=100000)";
    }else
    {
        $sql_str .= "(SELECT server_id,`key`,date_time FROM yueyue_recovery_rate_db.yueyue_recovery_rate_log_".date('Ymd',$start_date)." WHERE (receive_id={$receive_id} AND receive_identity='yueseller' AND sender_id>=100000) OR (sender_id={$receive_id} AND sender_identity='yueseller' AND receive_id>=100000))";
    }

    $start_date += 24*3600;

}

$ret = array();
if(strlen($sql_str)>0)
{
    $sql_str .= " ORDER BY `key` DESC,date_time ASC";
    $ret = db_simple_getdata($sql_str,false,22);
    if(!is_array($ret)) $ret = array();
    foreach($ret as &$v)
    {
        $result = get_content_by_service_id($v['server_id']);
        if(!is_array($result)) $result = array();
        $v['send_time'] = date('Y-m-d H:i:s',$result['send_time']);
        $v['send_user_id'] = (int)$result['send_user_id'];
        $v['send_user_role_name'] = get_send_user_role_name(trim($result['send_user_role']));
        $v['send_user_role'] = trim($result['send_user_role']);
        $v['to_user_id'] = (int)$result['to_user_id'];
        $v['to_user_role_name'] = get_to_user_role_name(trim($result['send_user_role']));
        $v['content'] = trim($result['content']);
    }

}

$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'seller_receiver_message_detail.tpl.htm' );

$tpl->assign('receive_id',$receive_id);
$tpl->assign('list',$ret);
$tpl->output();


//获取接收者角色名
function get_to_user_role_name($send_user_role)
{
    $send_user_role = trim($send_user_role);
    if(strlen($send_user_role) <1)return false;
    if($send_user_role == 'yueseller')
    {
        return '消费者';
    }
    else
    {
        return '商家';
    }
}

//获取发送者角色名
function get_send_user_role_name($send_user_role)
{
    $send_user_role = trim($send_user_role);
    if(strlen($send_user_role) <1)return false;
    if($send_user_role == 'yueseller')
    {
        return '商家';
    }
    else
    {
        return '消费者';
    }
}

//获取服务器数据
function get_content_by_service_id($notice_id)
{
    $notice_id = (int)$notice_id;
    if($notice_id <1) return false;
    $gmclient= new GearmanClient();
    $gmclient->addServers("172.18.5.13:9245");
    $gmclient->setTimeout(5000); // 设置超时
    do
    {
        $req_param['notice_id'] = $notice_id;
        $result = $gmclient->do("chatlog_from_nid",json_encode($req_param) );
        $result = trim($result);
    }
    while(false);
    //while($gmclient->returnCode() != GEARMAN_SUCCESS);
    if(!empty($result))
    {
        $result = json_decode($result,TRUE);
        $result = utf8_to_gbk($result);
        return $result;
    }
    return false;
}

function utf8_to_gbk($str)
{
    if( is_string($str) )
    {
        $str = iconv('utf-8', 'gbk//IGNORE', $str);
    }
    elseif( is_array($str) )
    {
        foreach ($str as $key=>$val)
        {
            $str[$key] = utf8_to_gbk($val);
        }
    }
    return $str;
}
/*if($receive_id >0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .="receive_id={$receive_id}";
}*/