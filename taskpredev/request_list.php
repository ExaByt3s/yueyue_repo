<?php
/** 
 * 
 * tt
 * ��Բ
 * 2015-4-11
 * 
 */
 
 include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
 
/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

// Ȩ���ļ�
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');




include_once($file_dir. '/./webcontrol/footer.php');
 
$tpl = $my_app_pai->getView('request_list.tpl.htm');

// ������ ��������
include_once($file_dir.'/./consumers_require.php');

$tpl->assign('time', time());  //�����

// ������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'request_list'));
$tpl->assign('pc_global_nav', $pc_global_nav);

// �ײ�
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);


global $yue_login_id;

//��ȡ����
$user_id = 100028;
$service_id = 1;

//��ʼ������
$task_request_obj = POCO::singleton('pai_task_request_class');
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$task_service_obj = POCO::singleton('pai_task_service_class');

//������������
$hired_quotes_info = array();
$request_list_tmp = $task_request_obj->get_request_detail_list_by_user_id($user_id);
$request_count = count($request_list_tmp);
$request_list = array();
foreach($request_list_tmp as $request_info_tmp)
{
    $status_code_tmp = trim($request_info_tmp['status_code']);
    $quotes_count_tmp = trim($request_info_tmp['quotes_count']);
    $quotes_list_tmp = $request_info_tmp['quotes_list'];
    if( !is_array($quotes_list_tmp) ) $quotes_list_tmp = array();
    $service_id_tmp = trim($request_info_tmp['service_id']);
    
    //��ȡ������Ϣ
    $service_info_tmp = $task_service_obj->get_service_info($service_id_tmp);
    
    //�����۵�����
    $quotes_list = array();
    foreach($quotes_list_tmp as $quotes_info_tmp)
    {
        $quotes_id_tmp = trim($quotes_info_tmp['quotes_id']);
        
        //״̬��ɫ
        $quotes_is_gray_tmp = $task_quotes_obj->get_quotes_is_gray($quotes_info_tmp['status'], $request_info_tmp['status_code']);
        
        $remind_num_tmp = $task_quotes_obj->get_quotes_remind_num($user_id, $quotes_id_tmp);
        $remind_num_tmp = trim($remind_num_tmp);
        
        if( $quotes_info_tmp['status']==1 )
        {
            $hired_quotes_info = $quotes_info_tmp;
        }
        
        $quotes_list[] = array(
            'quotes_id' => $quotes_info_tmp['quotes_id'],
            'user_id' => $quotes_info_tmp['user_id'],
            'user_icon' => $quotes_info_tmp['user_icon'],
            'is_vip' => $quotes_info_tmp['is_vip'],
            'status' => $quotes_info_tmp['status'],
            'is_gray' => $quotes_is_gray_tmp,
            'remind_num' => $remind_num_tmp,
        );
    }
    
    $tip_title_tmp = ''; //��ʾ����
    $tip_content_tmp = ''; //��ʾ����
    if( $status_code_tmp=='started' ) //����Ӷ�������ڣ��ޱ���
    {
        $tip_title_tmp = '�ȴ�������';
        $tip_content_tmp = '���ǻὫ��������͸����з����ʸ�Ĺ�Ӧ�ߣ������24Сʱ���յ��ظ�';
    }
    elseif( $status_code_tmp=='introduced' ) //����Ӷ�������ڣ��б���
    {
        $tip_title_tmp = '';
        $tip_content_tmp = "{$quotes_count_tmp}��{$service_info_tmp['profession_name']}��Ϊ���ṩ����";
    }
    elseif( $status_code_tmp=='closed' ) //����Ӷ���ѹ��ڣ��ޱ���
    {
        $tip_title_tmp = '��ʱû�з���Ҫ�����ѡ';
        $tip_content_tmp = '���ź�����ʱû����Ϊ�����Ĺ�Ӧ�߷�����������';
    }
    elseif( $status_code_tmp=='quoted' ) //����Ӷ���ѹ��ڣ��б���
    {
        $tip_title_tmp = '';
        $tip_content_tmp = "{$quotes_count_tmp}��{$service_info_tmp['profession_name']}��Ϊ���ṩ����";
    }
    elseif( $status_code_tmp=='canceled' )
    {
        $canceled_time_str = date('Y.m.d', $request_info_tmp['canceled_time']);
        $tip_title_tmp = '';
        $tip_content_tmp = "����{$canceled_time_str}ȡ���˸ö���";
    }
    elseif( in_array($status_code_tmp, array('hired', 'paid', 'reviewed')) )
    {
        $tip_title_tmp = '';
        $tip_content_tmp = "��ѡ�С�{$hired_quotes_info['user_nickname']}��";
    }
    
    $request_list[] = array(
        'request_id' => $request_info_tmp['request_id'],
        'service_id' => $request_info_tmp['service_id'],
        'title' => $request_info_tmp['title'],
        'add_time_str' => $request_info_tmp['add_time_str'],
        'status_color' => $request_info_tmp['status_color'],
        'status_code' => $request_info_tmp['status_code'],
        'status_name' => $request_info_tmp['status_name'],
        'tip_title' => $tip_title_tmp,
        'tip_content' => $tip_content_tmp,
        'request_icon' => 'http://img16.poco.cn/yueyue/20150416/2015041614590034524576.png?64x64_130',
        'quotes_list' => $quotes_list,
    );
}

$data = array();
$data['request_count'] = trim($request_count);
$data['request_list'] = $request_list;
$data['mid'] = '122OD04002'; //ģ��ID




//  16������ɫת��
foreach ($data['request_list'] as $k => $v) 
{
    $data['request_list'][$k]['status_color'] = str_replace( substr($v['status_color'],0,4),"#",$v['status_color'] );
}



$tpl->assign('request_list', $data['request_list']);



if (isset($_INPUT['print'])) 
{
    print_r($data['request_list']);
}




$tpl->output();
 ?>