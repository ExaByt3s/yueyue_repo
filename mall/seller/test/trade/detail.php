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


$topic_id =  intval($_INPUT['topic_id']);
$state = $_INPUT['state'];

if (empty($topic_id)) 
{
    header("location: ".'./list.php');
}


$trade_detail_obj = POCO::singleton ( 'pai_topic_class' );
$ret = $trade_detail_obj->get_task_detail($topic_id,$yue_login_id);






// ========================= ��ʼ���ӿ� end =======================



// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
$user_agent_arr = mall_get_user_agent_arr();
if(MALL_UA_IS_PC == 1)
{
    //****************** pc�� ******************
    include_once './detail-pc.php';
   
}
else
{
    
    //****************** wap�� ******************
    include_once './detail-wap.php';
    
} 
// ========================= ����pc��wapģ�������ݸ�ʽ���� end  =======================




// ========================= ����ģ�����  =======================
$tpl->output();

?>