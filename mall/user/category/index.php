<?php
include_once 'config.php';


// ========================= 初始化接口 start =======================


$type_id  = $_INPUT['type_id'];

$ret = get_api_result('customer/classify_index.php',array(
    'location_id' => empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'],
    'user_id' => $yue_login_id ,
    'type_id' => $type_id 
));



// 单双数样式处理
foreach ( $ret['data']['category_list']  as $k => $val ) 
{
    if ( count($ret['data']['category_list']) % 2 == 0 ) 
    {
        $ret['data']['category_list'][$k]['even_or_odd'] = 'even';
    }
    else
    {
        $ret['data']['category_list'][$k]['even_or_odd'] = 'odd';
    }
}



// php截取数组
foreach ( $ret['data']['module_list'] as $k => $val ) 
{
    foreach ( $val['exhibit'] as $key => $value ) 
    {
        if ( $key < 2) 
        {

            $ret['data']['module_list'][$k]['exhibit'][$key] = $value;
        }
        else
        {
            unset($ret['data']['module_list'][$k]['exhibit'][$key]);
        }
    }

}



if ($_INPUT['print']) 
{
    print_r($ret);
}





// ========================= 初始化接口 end =======================


// ========================= 区分pc，wap模板与数据格式整理 start  =======================
$user_agent_arr = mall_get_user_agent_arr();
if(MALL_UA_IS_PC == 1)
{
    //****************** pc版 ******************
    include_once './index-pc.php';
   
}
else
{
    
    //****************** wap版 ******************
    include_once './index-wap.php';
    
} 
// ========================= 区分pc，wap模板与数据格式整理 end  =======================








// ========================= 最终模板输出  =======================
$tpl->output();
?>