<?php

//****************** wap�� ͷ��ͨ�� start  ******************

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'act/detail.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************

if(MALL_UA_IS_YUEYUE == 1)
{
    define('MALL_NOT_REDIRECT_LOGIN',1);

    // Ȩ�޼��
    $check_arr = mall_check_user_permissions($yue_login_id);

    // �˺��л�ʱ
    if(intval($check_arr['switch']) == 1)
    {
        $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
        header("Location:{$url}");
        die();
    }
}



// ��ȡ����
$ret = get_api_result('customer/sell_services.php',array(

    'user_id' => $yue_login_id,
    'goods_id' => $goods_id

));


// ��ʼ������״̬
if ($stage_id) 
{
    foreach ( $ret['data']['showing']['exhibit'] as $k => $val ) 
    {
        if ($val['stage_id'] == $stage_id) 
        {
            $ret['data']['showing']['exhibit'][$k]['sec'] = 1 ;
        }

    }

    //�����ûƥ�䵽���ԣ�Ĭ����ʾ��һ��
    // foreach ( $ret['data']['showing']['exhibit'] as $k => $val ) 
    // {
    //     if ($val['stage_id'] == $stage_id) 
    //     {
    //         break;
    //     }

    //     print_r(123);

    //     $ret['data']['showing']['exhibit'][0]['sec'] = 1 ;
    // }

}


// ���ǰٷֱ�
$stars =  $ret['data']['business']['merit']['value'] ;
$stars_width = (($stars/5)*100)."%";

// ������ೡ�ε���ʽ

// $ret['data']['showing']['exhibit'][2] =  Array
// (
//     'stage_id' => '2222' ,
//     'status' => '1',
//     'title' => '�ڶ��� 2015-11-10 17:19��2015-11-30 17:19',
//     'name' => '�ڶ���',
//     'period' => '2015-11-10 17:19��2015-11-30 17:19',
//     'prices' => '��1.00',
//     'unit' => '/�� ��',
//     'attend_str' => '�ѱ�������' ,
//     'attend_num' => '2',
//     'total_num' => '10',
//     'stock_num' => '8'
// );

// $ret['data']['showing']['exhibit'][3] =  Array
// (
//     'stage_id' => '333333' ,
//     'status' => '1',
//     'title' => '�ڶ��� 2015-11-10 17:19��2015-11-30 17:19',
//     'name' => '�ڶ���',
//     'period' => '2015-11-10 17:19��2015-11-30 17:19',
//     'prices' => '��1.00',
//     'unit' => '/�� ��',
//     'attend_str' => '�ѱ�������' ,
//     'attend_num' => '2',
//     'total_num' => '10',
//     'stock_num' => '8'
// );

// $ret['data']['showing']['exhibit'][4] =  Array
// (
//     'stage_id' => '4444' ,
//     'status' => '1',
//     'title' => '�ڶ��� 2015-11-10 17:19��2015-11-30 17:19',
//     'name' => '�ڶ���',
//     'period' => '2015-11-10 17:19��2015-11-30 17:19',
//     'prices' => '��1.00',
//     'unit' => '/�� ��',
//     'attend_str' => '�ѱ�������' ,
//     'attend_num' => '2',
//     'total_num' => '10',
//     'stock_num' => '8'
// );


$exhibit_nums = $ret['data']['showing']['exhibit'] ;

if (count($exhibit_nums) > 3) 
{
    $show_more  =  1 ;

    $show_array =array_slice($exhibit_nums, 0, 3) ; //ǰ����

    $show_array_more = array_slice($exhibit_nums,3) ; //�����

}
else
{
    $show_more  = 0 ;

    $show_array = $exhibit_nums ;
}



$share = mall_output_format_data($ret['data']['share']);



// ����
if ($_INPUT['print']) 
{
    print_r($ret);
}



$tpl->assign('share', $share); 
$tpl->assign('show_more', $show_more); 
$tpl->assign('show_array', $show_array); 
$tpl->assign('show_array_more', $show_array_more); 
$tpl->assign('ret', $ret['data']); 
$tpl->assign('stars_width', $stars_width);

?>