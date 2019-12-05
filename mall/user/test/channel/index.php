<?php
include_once 'config.php';


// ========================= 初始化接口 start =======================

$type_id  = intval($_INPUT['type_id']);
$query  = intval( $_INPUT['query']);


if ($_INPUT['print']) 
{
    print_r($ret['data']);
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
$tpl->assign('type_id', $type_id);
$tpl->assign('query', $query);
$tpl->output();
?>