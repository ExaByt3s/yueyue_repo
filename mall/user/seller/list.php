<?php
/**
 *
 * @author ��Բ
 * @copyright  2015-8-26
 */


include_once 'config.php';

$type_id = (int)$_INPUT['type_id'];
if(!$type_id && isset($_INPUT['return_query']))
{
	$rq = urldecode($_INPUT['return_query']);
	parse_str($rq);
	
}


// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{
    //****************** pc�� ******************
    include_once './list-pc.php';
}
else
{
	// ========================= ��ʼ���ӿ� start =======================
	$ret = get_api_result('customer/goods_list.php',array(
	    'user_id' => $yue_login_id,
	    'limit' => $limit, 
	    'return_query' =>urlencode($return_query)
	    )
	);
	// ========================= ��ʼ���ӿ� end =======================
    //****************** wap�� ******************
    include_once './list-wap.php';
} 
// ========================= ����pc��wapģ�������ݸ�ʽ���� end  =======================


// ========================= ����ģ�����  =======================
$tpl->output();

?>