<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];


$score_obj              = POCO::singleton ('pai_score_rank_class');
$date_obj               = POCO::singleton ('pai_date_rank_class');
$user_obj               = POCO::singleton ('pai_model_style_v2_class');
$pic_obj                = POCO::singleton ('pai_pic_class');
$pic_score_obj          = POCO::singleton ('pai_score_rank_class');
$model_card_obj         = POCO::singleton ('pai_model_card_class');
$cameraman_card_obj     = POCO::singleton ('pai_cameraman_card_class');
$rank_event_obj         = POCO::singleton('pai_rank_event_class');
$pai_user_obj           = POCO::singleton ( 'pai_user_class' );
$pai_cms_parse_obj      = POCO::singleton( 'pai_cms_parse_class' );
$cms_obj                = new cms_system_class();

/**
if($client_data['data']['version'] == '88.8.8')
{
//������
    $role = $pai_user_obj->check_role($user_id);

//��ʼ������
    if(empty($role))        $role         = 'cameraman';
    if(empty($location_id)) $location_id  = '101029001';

    //$ranking_array = $rank_event_obj->get_rank_event_by_location_id($location_id, $role);
    $data = '';
    $ranking_array = '';
    $options['data'] = '';
    $ranking_array[23][0] = '����-�����Ƽ�(��������)';
    $ranking_array[23][1] = 94;
    $ranking_array[23][2] = 'Сʱ';
    $ranking_array[23][3] = '';
    $ranking_array[23][4] = '';
    $ranking_array[23][5] = 0;

    $data = $pai_cms_parse_obj->cms_parse_by_array_v2($ranking_array);
    $options['data'] = $data;
}else
 * */
{

    //������
        $role = $pai_user_obj->check_role($user_id);

    //��ʼ������
        if(empty($role))        $role         = 'cameraman';
        if(empty($location_id)) $location_id  = '101029001';

    //ƻ���г��� ���⴦�� ��ʼ
        //if(version_compare($client_data['data']['version'], '2.0.10', '=')) $location_id = '101035001';
    //ƻ���г��� ���⴦�� ����

        $ranking_array = $rank_event_obj->get_rank_event_by_location_id($location_id, $role);
        $data = $pai_cms_parse_obj->cms_parse_by_array_v2($ranking_array);

        $options['data'] = $data;


}


$cp->output($options);
?>