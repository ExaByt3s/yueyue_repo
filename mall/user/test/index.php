<?php
include_once 'config.php';

// ========================= ��ʼ���ӿ� start =======================

$ret = get_api_result('customer/buyer_index.php',array(
    'user_id' => $yue_login_id
    ));



$roll = get_api_result('event/get_hot_ad.php',array(
    'location_id' => empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'],
    'user_id' => $yue_login_id
    ));
    
    if($_INPUT['print'] == 1)
    {
        print_r($ret);
    }
    
foreach($roll['data']['list'] as $key => $val)
{
    $url_arr = parse_url($val['ad_url']);

    if ($url_arr['scheme'] === 'yueyue')
    {
        if(strpos($url_arr['fragment'],'topic/') == 0)
        {
            $topic_id = split('/',$url_arr['fragment']);
            $roll['data']['list'][$key]['ad_url'] = G_MALL_PROJECT_USER_ROOT.'/topic/index.php?topic_id='.$topic_id[1];
        
        }
    }

    
}



//  �°�v2�ӿڣ���ʱûͳһ����pc�� �ȳ�ҳ��
$ret_index_v2 = get_api_result('customer/buyer_index_plus.php',array(
    'location_id' => empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'],
    'user_id' => $yue_login_id
    ));


if($_INPUT['print'] == 1)
{
    print_r($ret_index_v2['data']);
}


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



// ========================= ����ģ�����  =======================
$tpl->output();


?>
