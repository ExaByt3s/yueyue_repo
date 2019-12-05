<?php
/**
 * ˽�˶���
 */
include_once('../common.inc.php');
$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

$lower_limit = intval($_INPUT["lower_limit_v"]);
$ceiling = intval($_INPUT["ceiling_v"]);

// Ȩ�޼��
mall_check_user_permissions($yue_login_id);

if($lower_limit>=$ceiling)
{
    $output_arr['code'] = 0;
    $output_arr['title'] =  '�ύʧ��';
    $output_arr['msg'] =  'Ԥ�����޵��ڻ򳬹�����';
    mall_mobile_output($output_arr,false);
    die();
}

$user_obj = POCO::singleton ( 'pai_user_class' );
$user_info = $user_obj->get_user_info($yue_login_id);//�ֻ�����

$nickname  = get_user_nickname_by_user_id($yue_login_id);//�����û�id��ȡ�ǳ�
$insert_data['cameraman_phone'] = $user_info['cellphone'];//�ύ���ֻ�
$insert_data['cameraman_nickname'] = $user_info['nickname'];//�ύ���ǳ�
$insert_data['location_id'] = intval($_INPUT['location_id']) ? intval($_INPUT['location_id']) : 101029001 ;//����
$insert_data['date_remark'] = iconv('utf-8','gbk',$_INPUT['remark']);//��ע
$insert_data['date_time'] = $_INPUT['time_text']; //ʱ��
$insert_data['hour'] = intval($_INPUT['time_span']);//ʱ��
$insert_data['model_num'] = intval($_INPUT['order_num']);//����
$insert_data['about_budget'] = $lower_limit."-".$ceiling;//Ԥ������
$insert_data['audit_status'] = 'wait';//д��
$insert_data['order_status'] = 'wait';//д��
$insert_data['type_id_str'] = $_INPUT['type_id_str'];//����ID
$insert_data['source'] = 5;//

$ret = $model_oa_order_obj->add_order($insert_data);

$output_arr['code'] = $ret ? 1 :0;
$output_arr['title'] = $ret ? '�ύ�ɹ�' : '�ύʧ��';
$output_arr['msg'] = "��л���֧�֣����ǽ���24Сʱ��������ϵ����ȷ����������ҳ~";
mall_mobile_output($output_arr,false);

?>