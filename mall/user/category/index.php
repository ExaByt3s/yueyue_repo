<?php
include_once 'config.php';


// ========================= ��ʼ���ӿ� start =======================


$type_id  = $_INPUT['type_id'];

$ret = get_api_result('customer/classify_index.php',array(
    'location_id' => empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'],
    'user_id' => $yue_login_id ,
    'type_id' => $type_id 
));



// ��˫����ʽ����
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



// php��ȡ����
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
$tpl->output();
?>