<?php

include_once 'config.php';

// ========================= ��ʼ���ӿ� start =======================


// Ȩ�޼��
$check_arr = mall_check_user_permissions($yue_login_id);

// �˺��л�ʱ
if($check_arr['switch'] == 1)
{
$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
header("Location:{$url}");
die();
}


// ========================= ��ʼ���ӿ� end =======================



// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
$user_agent_arr = mall_get_user_agent_arr();
if(MALL_UA_IS_PC == 1)
{
    //****************** pc�� ******************
    include_once './list-pc.php';
   
}
else
{
    
    //****************** wap�� ******************
    include_once './list-wap.php';
    
} 
// ========================= ����pc��wapģ�������ݸ�ʽ���� end  =======================




// ========================= ����ģ�����  =======================
$tpl->output();


?>