<?php
include_once 'config.php';


// ========================= ��ʼ���ӿ� start =======================

$type_id  = intval($_INPUT['type_id']);
$query  = intval( $_INPUT['query']);


if ($_INPUT['print']) 
{
    print_r($ret['data']);
}



// ========================= ��ʼ���ӿ� end =======================


// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
$user_agent_arr = mall_get_user_agent_arr();
if(MALL_UA_IS_PC == 1)
{
    //****************** pc�� ******************
    include_once './index-pc.php';
   
}
else
{
    
    //****************** wap�� ******************
    include_once './index-wap.php';
    
} 
// ========================= ����pc��wapģ�������ݸ�ʽ���� end  =======================








// ========================= ����ģ�����  =======================
$tpl->assign('type_id', $type_id);
$tpl->assign('query', $query);
$tpl->output();
?>