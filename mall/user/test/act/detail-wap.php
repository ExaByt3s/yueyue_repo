<?php

//****************** wap版 头部通用 start  ******************

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/detail.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap版 头部通用 end  ******************

if(MALL_UA_IS_YUEYUE == 1)
{
    define('MALL_NOT_REDIRECT_LOGIN',1);

    // 权限检查
    $check_arr = mall_check_user_permissions($yue_login_id);

    // 账号切换时
    if(intval($check_arr['switch']) == 1)
    {
        $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
        header("Location:{$url}");
        die();
    }
}



// 读取数据
$ret = get_api_result('customer/sell_services.php',array(

    'user_id' => $yue_login_id,
    'goods_id' => $goods_id

));


// 初始化场次状态
if ($stage_id) 
{
    foreach ( $ret['data']['showing']['exhibit'] as $k => $val ) 
    {
        if ($val['stage_id'] == $stage_id) 
        {
            $ret['data']['showing']['exhibit'][$k]['sec'] = 1 ;
        }

    }

    //如果都没匹配到尝试，默认显示第一场
    // foreach ( $ret['data']['showing']['exhibit'] as $k => $val ) 
    // {
    //     if ($val['stage_id'] == $stage_id) 
    //     {
    //         break;
    //     }

    //     print_r(123);

    //     $ret['data']['showing']['exhibit'][0]['sec'] = 1 ;
    // }

}


// 星星百分比
$stars =  $ret['data']['business']['merit']['value'] ;
$stars_width = (($stars/5)*100)."%";

// 处理更多场次的样式

// $ret['data']['showing']['exhibit'][2] =  Array
// (
//     'stage_id' => '2222' ,
//     'status' => '1',
//     'title' => '第二场 2015-11-10 17:19至2015-11-30 17:19',
//     'name' => '第二场',
//     'period' => '2015-11-10 17:19至2015-11-30 17:19',
//     'prices' => '￥1.00',
//     'unit' => '/人 起',
//     'attend_str' => '已报名人数' ,
//     'attend_num' => '2',
//     'total_num' => '10',
//     'stock_num' => '8'
// );

// $ret['data']['showing']['exhibit'][3] =  Array
// (
//     'stage_id' => '333333' ,
//     'status' => '1',
//     'title' => '第二场 2015-11-10 17:19至2015-11-30 17:19',
//     'name' => '第二场',
//     'period' => '2015-11-10 17:19至2015-11-30 17:19',
//     'prices' => '￥1.00',
//     'unit' => '/人 起',
//     'attend_str' => '已报名人数' ,
//     'attend_num' => '2',
//     'total_num' => '10',
//     'stock_num' => '8'
// );

// $ret['data']['showing']['exhibit'][4] =  Array
// (
//     'stage_id' => '4444' ,
//     'status' => '1',
//     'title' => '第二场 2015-11-10 17:19至2015-11-30 17:19',
//     'name' => '第二场',
//     'period' => '2015-11-10 17:19至2015-11-30 17:19',
//     'prices' => '￥1.00',
//     'unit' => '/人 起',
//     'attend_str' => '已报名人数' ,
//     'attend_num' => '2',
//     'total_num' => '10',
//     'stock_num' => '8'
// );


$exhibit_nums = $ret['data']['showing']['exhibit'] ;

if (count($exhibit_nums) > 3) 
{
    $show_more  =  1 ;

    $show_array =array_slice($exhibit_nums, 0, 3) ; //前三场

    $show_array_more = array_slice($exhibit_nums,3) ; //后面的

}
else
{
    $show_more  = 0 ;

    $show_array = $exhibit_nums ;
}



$share = mall_output_format_data($ret['data']['share']);



// 调试
if ($_INPUT['print']) 
{
    print_r($ret);
}



$tpl->assign('share', $share); 
$tpl->assign('show_more', $show_more); 
$tpl->assign('show_array', $show_array); 
$tpl->assign('show_array_more', $show_array_more); 
$tpl->assign('ret', $ret['data']); 
$tpl->assign('stars_width', $stars_width);

?>