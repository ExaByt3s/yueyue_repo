<?php
/**
 * @desc:   商家每日消息的回复数据统计
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/18
 * @Time:   16:38
 * version: 1.0
 */

ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'sendserver_seller_reply');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$sendserver_seller_reply_obj = POCO::singleton( 'pai_sendserver_seller_reply_class' ); //回复v2
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类

$tpl= new SmartTemplate( REPORT_TEMPLATES_ROOT.'sendserver_seller_reply_list.tpl.htm' );


$act = trim($_INPUT['act']);
$month = trim($_INPUT['month']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

$type = isset($_INPUT['type']) ? intval($_INPUT['type']) : 1 ;
$type_id = intval($_INPUT['type_id']);


$where_str = '1';
$setParam = array();

//月份处理
if(!preg_match("/\d\d\d\d-\d\d/", $month))
{
    $month = date('Y-m',time()-2*24*3600);
}
if(strlen($month) >0)
{
    $setParam['month'] = $month;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(date_time,'%Y-%m-%d') >='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(date_time,'%Y-%m-%d') <='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

if($type>0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    if($type == 1) {//只记用户信息
         $where_str .= "sender_id>=100000 AND receive_id>=100000";
    }else{//只记系统信息
        $where_str .= "sender_id<100000 AND receive_id<100000";
    }
    $setParam['type'] = $type;
}
$type_list = $type_obj->get_type_cate(2);
if($type_id >0)
{
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
    $setParam['type_id'] = $type_id;
}

$list = $sendserver_seller_reply_obj->get_info_list(false,$month,$type_id,$where_str,"GROUP BY FROM_UNIXTIME(date_time,'%Y-%m-%d')","date_time DESC,id DESC","0,31","FROM_UNIXTIME(date_time,'%Y-%m-%d') AS date,SUM(reply_five) AS five_sum,SUM(reply_ten) AS ten_sum,SUM(reply_twoten) AS twoten_sum,SUM(reply_threeten) AS threeten_sum,SUM(reply_onehour) AS onehour_sum,SUM(reply_tourhour) AS tourhour_sum,SUM(reply_tweehour) AS tweehour_sum,SUM(no_reply) AS no_reply_sum,SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour) AS reply_sum,SUM(reply_five+reply_ten+reply_twoten+reply_threeten+reply_onehour+reply_tourhour+reply_tweehour+no_reply) AS date_sum");

if(!is_array($list)) $list = array();

$title = '每日消息回复列表';
if($act == 'export') //导出数据
{
    $data = array();
    foreach($list as $key=>$val ) {
        $data[$key]['date'] = $val['date'];
        $five_scale = sprintf('%.2f', ($val['five_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['five_sum'] = $val['five_sum']."({$five_scale})";

        $ten_scale = sprintf('%.2f', ($val['ten_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['ten_sum'] = $val['ten_sum']."({$ten_scale})";

        $twoten_scale = sprintf('%.2f', ($val['twoten_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['twoten_sum'] = $val['twoten_sum']."({$twoten_scale})";

        $threeten_scale = sprintf('%.2f', ($val['threeten_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['threeten_sum'] = $val['threeten_sum']."({$threeten_scale})";

        $onehour_scale = sprintf('%.2f', ($val['onehour_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['onehour_sum'] = $val['onehour_sum']."({$onehour_scale})";

        $tourhour_scale = sprintf('%.2f', ($val['tourhour_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['tourhour_sum'] = $val['tourhour_sum']."({$tourhour_scale})";

        $tweehour_scale = sprintf('%.2f', ($val['tweehour_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['tweehour_sum'] = $val['tweehour_sum']."({$tweehour_scale})";

        $no_reply_scale = sprintf('%.2f', ($val['no_reply_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['no_reply_sum'] = $val['no_reply_sum']."({$no_reply_scale})";

        $reply_scale = sprintf('%.2f', ($val['reply_sum'] / $val['date_sum']) * 100).'%';
        $data[$key]['reply_sum'] = $val['reply_sum']."({$reply_scale})";;
        $data[$key]['date_sum'] = $val['date_sum'];
    }
    $headArr  = array("日期","5分钟内回复","10分钟内回复","20分钟内回复","30分钟内回复","1小时内回复","12小时内回复","24小时内回复","无回复","回复百分比","聊天总数");
    Excel_v2::start($headArr,$data,$title);
    exit;
}


$ret = array();//累计数组
$ret_day = array();//日平均数组

$i = 0;
foreach($list as &$val )
{
    $val['five_scale'] = sprintf('%.2f',($val['five_sum']/$val['date_sum'])*100);
    $val['ten_scale'] = sprintf('%.2f',($val['ten_sum']/$val['date_sum'])*100);
    $val['twoten_scale'] = sprintf('%.2f',($val['twoten_sum']/$val['date_sum'])*100);
    $val['threeten_scale'] = sprintf('%.2f',($val['threeten_sum']/$val['date_sum'])*100);
    $val['onehour_scale'] = sprintf('%.2f',($val['onehour_sum']/$val['date_sum'])*100);
    $val['tourhour_scale'] = sprintf('%.2f',($val['tourhour_sum']/$val['date_sum'])*100);
    $val['tweehour_scale'] = sprintf('%.2f',($val['tweehour_sum']/$val['date_sum'])*100);
    $val['no_reply_scale'] = sprintf('%.2f',($val['no_reply_sum']/$val['date_sum'])*100);
    $val['reply_scale'] = sprintf('%.2f',($val['reply_sum']/$val['date_sum'])*100);

    //时间
    $val['month'] = $month;

    //累计数据
    $ret['count_five'] += $val['five_sum'];
    $ret['count_ten'] += $val['ten_sum'];
    $ret['count_twoten'] += $val['twoten_sum'];
    $ret['count_threeten'] += $val['threeten_sum'];
    $ret['count_onehour'] += $val['onehour_sum'];
    $ret['count_tourhour'] += $val['tourhour_sum'];
    $ret['count_tweehour'] += $val['tweehour_sum'];
    $ret['count_no_reply'] += $val['no_reply_sum'];
    $ret['count_reply'] += $val['reply_sum'];
    $ret['count_date_sum'] += $val['date_sum'];

    $ret['count_five_scale'] += $val['five_scale'];
    $ret['count_ten_scale'] += $val['ten_scale'];
    $ret['count_twoten_scale'] += $val['twoten_scale'];
    $ret['count_threeten_scale'] += $val['threeten_scale'];
    $ret['count_onehour_scale'] += $val['onehour_scale'];
    $ret['count_tourhour_scale'] += $val['tourhour_scale'];
    $ret['count_tweehour_scale'] += $val['tweehour_scale'];
    $ret['count_no_reply_scale'] += $val['no_reply_scale'];
    $ret['count_reply_scale'] += $val['reply_scale'];
    $i++;
}
//累计数据
//每日平均数据
$ret_day['day_five'] = sprintf('%.2f',$ret['count_five']/$i);
$ret_day['day_ten'] = sprintf('%.2f',$ret['count_ten']/$i);
$ret_day['day_twoten'] = sprintf('%.2f',$ret['count_twoten']/$i);
$ret_day['day_threeten'] = sprintf('%.2f',$ret['count_threeten']/$i);
$ret_day['day_onehour'] = sprintf('%.2f',$ret['count_onehour']/$i);
$ret_day['day_tourhour'] = sprintf('%.2f',$ret['count_tourhour']/$i);
$ret_day['day_tweehour'] = sprintf('%.2f',$ret['count_tweehour']/$i);
$ret_day['day_no_reply'] = sprintf('%.2f',$ret['count_no_reply']/$i);
$ret_day['day_reply'] = sprintf('%.2f',$ret['count_reply']/$i);
$ret_day['day_reply_sum'] = sprintf('%.2f',$ret['count_date_sum']/$i);


//比例
$ret_day['day_five_scale'] = sprintf('%.2f',($ret['count_five']/$ret['count_date_sum'])*100);
$ret_day['day_ten_scale'] = sprintf('%.2f',($ret['count_ten']/$ret['count_date_sum'])*100);
$ret_day['day_twoten_scale'] = sprintf('%.2f',($ret['count_twoten']/$ret['count_date_sum'])*100);
$ret_day['day_threeten_scale'] = sprintf('%.2f',($ret['count_threeten']/$ret['count_date_sum'])*100);
$ret_day['day_onehour_scale'] = sprintf('%.2f',($ret['count_onehour']/$ret['count_date_sum'])*100);
$ret_day['day_tourhour_scale'] = sprintf('%.2f',($ret['count_tourhour']/$ret['count_date_sum'])*100);
$ret_day['day_tweehour_scale'] = sprintf('%.2f',($ret['count_tweehour']/$ret['count_date_sum'])*100);
$ret_day['day_no_reply_scale'] = sprintf('%.2f',($ret['count_no_reply']/$ret['count_date_sum'])*100);
$ret_day['day_reply_scale'] = sprintf('%.2f',($ret['count_reply']/$ret['count_date_sum'])*100);


$tpl->assign('type_list', $type_list);
$tpl->assign($ret);
$tpl->assign($ret_day);
$tpl->assign($setParam);
$tpl->assign('title',$title);
$tpl->assign('list', $list);
$tpl->output();