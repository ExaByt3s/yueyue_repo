<?php

/**
 *
 * @author ��Բ
 * @copyright  2015-8-26
 */


include_once 'config.php';

// ========================= ��ʼ���ӿ� start =======================
$seller_user_id = trim($_INPUT['seller_user_id']);
$service_status = $_INPUT['service_status'] ? $_INPUT['service_status'] : "on_sell";
$goods_type = $_INPUT['goods_type'] ? $_INPUT['goods_type'] : "all";

if (empty($seller_user_id)) 
{
    $index_url = G_MALL_PROJECT_USER_ROOT.'/';
    header("Location:{$index_url}");
    exit() ;
}   

// ====== ����Ԥ������ ======
// hudw 2015.9.6
if(intval($_INPUT['preview']) == 1)
{
    $ret = get_api_result('customer/trade_seller_preview.php',array(
    'user_id' => $yue_login_id,
    'seller_user_id'=> $seller_user_id 
    ), true); 

    $ret["data"]['detail'] = $ret["data"]['detail'].'&preview=1';
    $ret["data"]['business']['request'] = 'javascript:;';

    $ret_data =  $ret["data"];  

    if($_INPUT['print']==1)   
    {
        print_r($ret['data']);
        die();
    }
}
else
{
    

    $ret = get_api_result('customer/trade_seller.php',array(
    'user_id' => $yue_login_id,
    'seller_user_id'=> $seller_user_id 
    ), true); 

    $ret_data =  $ret["data"];     
}

// print_r($ret);

// �������ǿ�� start
// $stars =  4.5 ;
$stars =  $ret_data['business']['merit']['value'] ;
$stars_width = (($stars/5)*100)."%";

$list_img_data = mall_output_format_data($ret_data['showcase']);
// ========================= ��ʼ���ӿ� end =======================



// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
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


if ($_INPUT['print']) 
{
    print_r($ret_data);
}





// ========================= ����ģ�����  =======================
// �����������
$share = mall_output_format_data($ret_data['share']);
$tpl->assign('share',$share);

$tpl->output();

?>