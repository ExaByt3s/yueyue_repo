<?php
/**
 * @desc:   城市开通 列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/9
 * @Time:   11:11
 * version: 1.0
 */

include('common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php');
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");

$location_open_obj = POCO::singleton('pai_location_open_class');
$version_open_obj = POCO::singleton('pai_version_open_class');

$tpl = new SmartTemplate("location_open_list.tpl.htm");
$page_obj = new show_page ();
$show_count = 20;



$act = trim($_INPUT['act']);

$version_id = intval($_INPUT['version_id']);
$user_id    = intval($_INPUT['user_id']);
$start_time = trim($_INPUT['start_time']);
$end_time   = trim($_INPUT['end_time']);

$where_str = '';
$setParam  = array();

//数据赋值
if($version_id >0) $setParam['version_id'] = $version_id;
if($user_id >0) $setParam['user_id'] = $user_id;
if(strlen($start_time)>0) $setParam['start_time'] = $start_time;
if(strlen($end_time)>0) $setParam['end_time'] = $end_time;

$total_count = $location_open_obj->get_list(true,$version_id,$user_id, $start_time,$end_time,$where_str);
$page_obj->set($show_count,$total_count);
$page_obj->setvar($setParam);

$list   = $location_open_obj->get_list(false,$version_id,$user_id, $start_time,$end_time,$where_str,'id DESC',$page_obj->limit());

if(!is_array($list)) $list = array();

$tmp_str = '';
foreach($list as $key=>$val)
{
    if($key !=0) $tmp_str .= ',';
    $tmp_str .= "{$val['version_id']}";
    $list[$key]['nickname']      = get_user_nickname_by_user_id($val['user_id']);
    $list[$key]['location_name'] = get_poco_location_name_by_location_id($val['location_id']);
}
if(strlen($tmp_str) >0)
{
    $sql_in_str = "id IN ({$tmp_str})";
    $ret = $version_open_obj->get_list(false,'','',0,$sql_in_str,'id DESC','0,999999999','id AS version_id,version_num');
    //print_r($ret);
    if(is_array($ret) && !empty($ret))  $list = combine_arrxxx($list,$ret,'version_id');
}


$tpl->assign('total_count', $total_count);
$tpl->assign ( 'list', $list );
$tpl->assign ($setParam);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign ( 'MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER );
$tpl->output ();


/**
 * 根据一个相同的值合并两个二维数组  1对多
 * @param array $arr1  二维数组1
 * @param array $arr2  二维数组2
 * @param $same_filed  两个数组相同的值
 * @return $arr       返回值
 */
function combine_arrxxx($arr1 = array(),$arr2 = array(),$same_filed)
{
    foreach ($arr1 as $key => $val)
    {
        foreach ($arr2 as $k => $v)
        {
            if ($val[$same_filed] == $v[$same_filed]) {
                $new_arr[$key] = array_merge((array)$val, (array)$v);
                unset($arr1[$key]);
                break;
            }
        }
        if (empty($new_arr[$key])) {
            $new_arr[$key] = $val;
        }
    }
    return $new_arr;
}
