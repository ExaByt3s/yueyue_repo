<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");
global $yue_login_id;


/*��
 * �ύ�ʾ� ��Բ
 * @param int $quote_id
 * @return int
 */
$user_id = $yue_login_id;

$service_id = (int)$_INPUT['service_id'];
$version = (int)$_INPUT['version'];
$question_id = (int)$_INPUT['question_id'];

// $question_list_json = $_REQUEST['data'];
$question_list_json = poco_iconv_arr($_REQUEST['data'],'UTF-8', 'GBK');


// $question_list_json = $_INPUT['data'];
// $question_list = json_decode($question_list_json);
// print_r($question_list_json);
// echo $user_id."====>".$service_id."====>".$question_list_json;
// print_r($question_list_json);

$question_list = array(
    'user_id' => $user_id,
    'service_id' =>  $service_id ,
    'version'=> $version,
    'question_id' => $question_id,
    'data'=> $question_list_json
);

// print_r($question_list);

////////////////////////////////////////////////////�ύ�ʾ�������
function sub_request($user_id,$service_id,$question_list)

//$user_id�û�ID $service_id�ʾ����� $question_list�����ݸ�ʽ����http://113.107.204.251/wiki/index.php/��ҳ#.E6.8F.90.E4.BA.A4.E9.97.AE.E5.8D.B7.E4.BF.A1.E6.81.AF.EF.BC.8C.E7.94.9F.E6.88.90.E9.9C.80.E6.B1.82
{
    $user_id = (int)$user_id;
    $service_id = (int)$service_id;
    //��ȡ������Ϣ
    $task_service_obj = POCO::singleton('pai_task_service_class');
    $service_info = $task_service_obj->get_service_info($service_id);
    $title = trim($service_info['service_name']);
    //��ȡ�û���Ϣ
    $pai_user_obj = POCO::singleton('pai_user_class');
    $user_info = $pai_user_obj->get_user_info($user_id);
    $cellphone = trim($user_info['cellphone']);
    //��������
    $more_info = array(
        'title' => $title,
        'cellphone' => $cellphone,
        'email' => '',
    );
    $task_request_obj = POCO::singleton('pai_task_request_class');
    $submit_ret = $task_request_obj->submit_request($user_id, $service_id, $more_info, $question_list);
    //$submit_ret����ֵΪarray,Ԫ��������
    //$submit_ret['result'];//result����1Ϊ�ɹ�
    //$submit_ret['message'];
    //$submit_ret['request_id'];
    return $submit_ret;
}


$ret = sub_request($user_id,$service_id,$question_list);

// print_r($ret);

mobile_output($ret,false);


?>