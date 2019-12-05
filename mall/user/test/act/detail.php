<?php


/**
 * Define 外拍详情
 */

include_once 'config.php';

/**
 * 页面接收参数
 */
$goods_id = intval($_INPUT['goods_id']);
$stage_id = intval($_INPUT['stage_id']);







// ========================= 区分pc，wap模板与数据格式整理 start  =======================
if(MALL_UA_IS_PC == 1)
{
    //****************** pc版 ******************
    header("location: http://event.poco.cn/event_browse.php?event_id=".$event_id) ;
}
else
{

    //****************** wap版 ******************
    include_once './detail-wap.php';

} 
// ========================= 区分pc，wap模板与数据格式整理 end  =======================











$tpl->assign('goods_id', $goods_id); 

$tpl->output();

?>