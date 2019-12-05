<?php
/**
 * @desc:   每日消息私聊列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/19
 * @Time:   11:20
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include('common.inc.php');
check_auth($yue_login_id,'sendserver_seller_message_list');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$sendserver_seller_reply_obj = POCO::singleton( 'pai_sendserver_seller_reply_class' );
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'sendserver_seller_message_list.tpl.htm' );

$act = trim($_INPUT['act']);
$month = trim($_INPUT['month']);

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


$list = $sendserver_seller_reply_obj->get_info_list(false,$month,$type_id,$where_str,"GROUP BY FROM_UNIXTIME(date_time,'%Y-%m-%d')","date_time DESC,id DESC","0,31","FROM_UNIXTIME(date_time,'%Y-%m-%d') AS date,count(DISTINCT(sender_id)) AS sender_count,COUNT(DISTINCT(receive_id)) AS receive_count");

$title = '每日消息私聊列表';
if($act == 'export'){//导出数据

    $headArr  = array("日期","发起人个数","接受者个数");
    Excel_v2::start($headArr,$list,$title);
    exit;
}

$tpl->assign('type_list', $type_list);
$tpl->assign($setParam);
$tpl->assign('title',$title);
$tpl->assign('list', $list);
$tpl->output();