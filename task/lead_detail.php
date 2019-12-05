<?php
/** 
 * 
 * tt
 * ��Բ
 * 2015-4-11
 * 
 */
 
/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

// Ȩ���ļ�
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top_nav.php');
include_once($file_dir. '/./webcontrol/footer.php');
 
$tpl = $my_app_pai->getView('lead_detail.tpl.htm');

$tpl->assign('time', time());  //�����

// ������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'lead_list'));
$tpl->assign('pc_global_nav', $pc_global_nav);

// �ײ�
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);


$lead_id = $_INPUT['lead_id'];


/*
 * �鿴һ������
 * @param int $lead_id
 * @return array
 */
$task_lead_obj = POCO::singleton('pai_task_lead_class');
$re = $task_lead_obj->check_user_auth($yue_login_id,$lead_id);//��֤����Ȩ��
if(!$re)
{
	js_pop_msg("�Ƿ�����",false,"http://task.yueus.com/");
}
$lead_info = $task_lead_obj->get_lead_by_lead_id($lead_id);


$tpl->assign('lead_info', $lead_info);
$rank = floor($lead_info['rank']);

// for ($i=0; $i < $rank  ; $i++) { 
//     $rank_arr[$i]['starts'] = 1 ;
// }
// 
// print_r($rank);
for ($i=0; $i < 5; $i++) { 

    if ( $i < $rank ) 
    {
        $starts[$i]['start'] = 1 ;
    }
    else
    {
        $starts[$i]['start'] = 0 ;
    }
    
}

// print_r($rank_arr);
$tpl->assign('starts', $starts);



/*
 * ��������鿴״̬
 * @param int $lead_id
 * @param int $user_id
 * @return int
 */

$task_lead_obj->update_is_read($lead_id,$yue_login_id);


/*
 * ��ǰ�������ж���������
 * @param int $request_id
 * @param bool $b_select_count
 * @return int
 */
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$count_quote = $task_quotes_obj->get_quotes_list_for_valid($lead_info['request_id'], true);
$count_quote = (int)$count_quote;
$tpl->assign('count_quote', $count_quote);

/*
 * ��ȡ�û����⿨��
 * @param int $user_id
 * @return array
 */
 $task_coin_obj = POCO::singleton('pai_task_coin_class');
$coin_info = $task_coin_obj->get_coin_info($yue_login_id);
$balance = $coin_info['balance'];
$tpl->assign('balance', $balance*1);

/*
 * ��ȡ��ǰ������Ҫ�����⿨��
 * @param int $service_id
 * @return array
 */
$task_service_obj = POCO::singleton('pai_task_service_class');
$service_info = $task_service_obj->get_service_info($lead_info['service_id']);
$pay_coins = $service_info['pay_coins'];
$tpl->assign('pay_coins', $pay_coins*1);
$tpl->assign('service_name', $service_info['service_name']);

/*
 * ��ȡ�����ʾ��ʴ�
 * @param int $request_id
 * @return array
 */
$obj = POCO::singleton('pai_task_questionnaire_class');
$arr = $obj->show_questionnaire_data($lead_info['request_id'],true);
$tpl->assign('sidebar_list', $arr);




// ��ѯ�Ƿ����
$task_request_obj = POCO::singleton('pai_task_request_class');
$request_detail_info = $task_request_obj->get_request_detail_info_by_id($lead_info['request_id']);
if( in_array($request_detail_info['status_code'], array('started', 'introduced')) )
{
    $tpl->assign('status', 'ava');
}
else
{
    $tpl->assign('status', 'no');
}



$tpl->output();
 ?>