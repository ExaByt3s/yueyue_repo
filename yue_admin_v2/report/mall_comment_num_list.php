<?php
/**
 * @desc:   订单评价总的数据处理
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/31
 * @Time:   16:40
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once('common.inc.php');
check_auth($yue_login_id,'mall_comment_num');//权限控制
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$mall_comment_log_obj = POCO::singleton( 'pai_mall_comment_log_class' );//评价类
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类


$tpl = new SmartTemplate(REPORT_TEMPLATES_ROOT.'mall_comment_num_list.tpl.htm');

$act = trim($_INPUT['act']);
$month = trim($_INPUT['month']) ? trim($_INPUT['month']) : date('Y-m',time()-24*3600);
$type_id = intval($type_id);


$where_str = '';
$setParam = array();

//商品品类选择
$type_list = $type_obj->get_type_cate(2);

//月份处理
if(!preg_match("/\d\d\d\d-\d\d/", $month))
{
    $month = date('Y-m',time()-24*3600);
}
if(strlen($month) >0)
{
    $date = $month.'-01';
    $setParam['month'] = $month;
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(add_time),'%Y-%m')='".mysql_escape_string($month)."'";
}
if($type_id >0)
{
    $setParam['type_id'] = $type_id;
    foreach($type_list as $k => &$v)
    {
        $v['selected'] = $type_id==$v['id'] ? true : false;
    }
}

$list = $mall_comment_log_obj->get_order_num_list(false,$date,$type_id,$where_str,'GROUP BY add_time' ,'add_time DESC','0,31',"add_time,SUM(sign_count) AS sign_count,SUM(commont_num_for_buyer) AS commont_num_for_buyer,SUM(commont_num_for_buyer)/SUM(sign_count) AS scale_comment_sign,SUM(commont_1_for_buyer) AS commont_1_num_for_buyer,SUM(commont_3_for_buyer) AS commont_3_num_for_buyer,SUM(commont_5_for_buyer) AS commont_5_num_for_buyer,SUM(commont_7_for_buyer) AS commont_7_num_for_buyer,SUM(commont_5_for_buyer) AS commont_5_num_for_buyer,SUM(commont_other_for_buyer) AS commont_other_num_for_buyer");
if(!is_array($list)) $list = array();

$i = 0;
foreach($list as &$v)
{
    $v['scale_comment_sign'] = ($v['scale_comment_sign']*100);
    $v['scale_1_comment_sign'] = sprintf('%.2f',($v['commont_1_num_for_buyer']/$v['commont_num_for_buyer']*100));
    $v['scale_3_comment_sign'] = sprintf('%.2f',($v['commont_3_num_for_buyer']/$v['commont_num_for_buyer']*100));
    $v['scale_5_comment_sign'] = sprintf('%.2f',($v['commont_5_num_for_buyer']/$v['commont_num_for_buyer']*100));
    $v['scale_7_comment_sign'] = sprintf('%.2f',($v['commont_7_num_for_buyer']/$v['commont_num_for_buyer']*100));
    $v['scale_other_comment_sign'] = sprintf('%.2f',($v['commont_other_num_for_buyer']/$v['commont_num_for_buyer']*100));
    $setParam['total_sign_count'] += $v['sign_count'];

    $setParam['total_commont_num_for_buyer'] += $v['commont_num_for_buyer'];
    $setParam['total_commont_1_num_for_buyer'] += $v['commont_1_num_for_buyer'];
    $setParam['total_commont_3_num_for_buyer'] += $v['commont_3_num_for_buyer'];
    $setParam['total_commont_5_num_for_buyer'] += $v['commont_5_num_for_buyer'];
    $setParam['total_commont_7_num_for_buyer'] += $v['commont_7_num_for_buyer'];
    $setParam['total_commont_other_num_for_buyer'] += $v['commont_other_num_for_buyer'];
    //百分累计
    $setParam['total_scale_comment_sign'] += $v['scale_comment_sign'];
    $setParam['total_scale_1_comment_sign'] += $v['scale_1_comment_sign'];
    $setParam['total_scale_3_comment_sign'] += $v['scale_3_comment_sign'];
    $setParam['total_scale_5_comment_sign'] += $v['scale_5_comment_sign'];
    $setParam['total_scale_7_comment_sign'] += $v['scale_7_comment_sign'];
    $setParam['total_scale_other_comment_sign'] += $v['scale_other_comment_sign'];
    $i++;
}
$setParam['avg_total_sign_count']  = sprintf('%.2f',$setParam['total_sign_count']/$i);
$setParam['avg_commont_num_for_buyer']  = sprintf('%.2f',$setParam['total_commont_num_for_buyer']/$i);
$setParam['avg_commont_1_num_for_buyer'] = sprintf('%.2f',$setParam['total_commont_1_num_for_buyer']/$i);
$setParam['avg_commont_3_num_for_buyer'] = sprintf('%.2f',$setParam['total_commont_3_num_for_buyer']/$i);
$setParam['avg_commont_5_num_for_buyer'] = sprintf('%.2f',$setParam['total_commont_5_num_for_buyer']/$i);
$setParam['avg_commont_7_num_for_buyer'] = sprintf('%.2f',$setParam['total_commont_7_num_for_buyer']/$i);
$setParam['avg_commont_other_num_for_buyer'] = sprintf('%.2f',$setParam['total_commont_other_num_for_buyer']/$i);

$setParam['avg_scale_comment_sign'] = sprintf('%.2f',$setParam['total_scale_comment_sign']/$i);
$setParam['avg_scale_1_comment_sign'] = sprintf('%.2f',$setParam['total_scale_1_comment_sign']/$i);
$setParam['avg_scale_3_comment_sign'] = sprintf('%.2f',$setParam['total_scale_3_comment_sign']/$i);
$setParam['avg_scale_5_comment_sign'] = sprintf('%.2f',$setParam['total_scale_5_comment_sign']/$i);
$setParam['avg_scale_7_comment_sign'] = sprintf('%.2f',$setParam['total_scale_7_comment_sign']/$i);
$setParam['avg_scale_other_comment_sign'] = sprintf('%.2f',$setParam['total_scale_other_comment_sign']/$i);



if($act == 'export')//导出数据
{
    $fileName = '评价数量列表';
    $headArr  = array("日期","总交易数","总评价数","(评价数/交易数)占比","1天内评价的订单","1天内评价的占比","3天内评价的订单","3天内评价的占比","5天内评价的订单","5天内评价的占比","7天内评价的订单","7天内评价的占比","超过7天内评价的订单","超过7天内评价的占比");
    $data = array();
    foreach($list as $key=>$val)
    {
        $data[$key]['add_time'] = $val['add_time'];
        $data[$key]['sign_count'] = $val['sign_count'];
        $data[$key]['commont_num_for_buyer'] = $val['commont_num_for_buyer'];
        $data[$key]['scale_comment_sign'] = $val['scale_comment_sign'].'%';
        $data[$key]['commont_1_num_for_buyer'] = $val['commont_1_num_for_buyer'];
        $data[$key]['scale_1_comment_sign'] = $val['scale_1_comment_sign'].'%';
        $data[$key]['commont_3_num_for_buyer'] = $val['commont_3_num_for_buyer'];
        $data[$key]['scale_3_comment_sign'] = $val['scale_3_comment_sign'].'%';
        $data[$key]['commont_5_num_for_buyer'] = $val['commont_5_num_for_buyer'];
        $data[$key]['scale_5_comment_sign'] = $val['scale_5_comment_sign'].'%';
        $data[$key]['commont_7_num_for_buyer'] = $val['commont_7_num_for_buyer'];
        $data[$key]['scale_7_comment_sign'] = $val['scale_7_comment_sign'].'%';
        $data[$key]['commont_other_num_for_buyer'] = $val['commont_other_num_for_buyer'];
        $data[$key]['scale_other_comment_sign'] = $val['scale_other_comment_sign'].'%';
    }
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}
$tpl->assign($setParam);
$tpl->assign('type_list',$type_list);
$tpl->assign('list',$list);
$tpl->output();