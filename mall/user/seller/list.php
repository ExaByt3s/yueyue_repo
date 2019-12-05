<?php
/**
 *
 * @author 汤圆
 * @copyright  2015-8-26
 */


include_once 'config.php';

$type_id = (int)$_INPUT['type_id'];
if(!$type_id && isset($_INPUT['return_query']))
{
	$rq = urldecode($_INPUT['return_query']);
	parse_str($rq);
	
}


// ========================= 区分pc，wap模板与数据格式整理 start  =======================
if(MALL_UA_IS_PC == 1)
{
    //****************** pc版 ******************
    include_once './list-pc.php';
}
else
{
	// ========================= 初始化接口 start =======================
	$ret = get_api_result('customer/goods_list.php',array(
	    'user_id' => $yue_login_id,
	    'limit' => $limit, 
	    'return_query' =>urlencode($return_query)
	    )
	);
	// ========================= 初始化接口 end =======================
    //****************** wap版 ******************
    include_once './list-wap.php';
} 
// ========================= 区分pc，wap模板与数据格式整理 end  =======================


// ========================= 最终模板输出  =======================
$tpl->output();

?>