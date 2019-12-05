<?php
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
ini_set('display_errors','Off');
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php');
//��ȡ��ַ
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
include('common.inc.php');
$time_start = microtime_float();//���ÿ�ʼʱ��
$fileName = "date";
$headArr = array ("������ID","����ID","�ID","����","Լ��״̬", "��ӰʦID", "��Ӱʦ�ǳ�", "��Ӱʦ�ֻ�", "ģ��ID", "ģ���ǳ�", "ģ���ֻ�", "������", "ʱ��", "�۸�","�Ż�", "����״̬", "����ʱ��", "����ʱ��", "����ص�", "ǩ��״̬", "�״̬","����","�Ƿ����","ģ�ص�״̬" );

$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
$event_details_obj = POCO::singleton ( 'event_details_class' );
//������
$pai_organization_obj = POCO::singleton("pai_organization_class");
//ģ�ؿ�
$user_add_obj = POCO::singleton ( 'pai_model_add_class' );
$model_audit_obj = POCO::singleton('pai_model_audit_class');//ģ����˿�
$date_status = $_INPUT ['date_status'] ? $_INPUT ['date_status'] : 'all';
$begin_time = $_INPUT ['begin_time'];
$end_time = $_INPUT ['end_time'];
$from_date_id = $_INPUT ['from_date_id'];

//������Ա�ʺ�
$test_user_id = TEST_PAI_USER_ID;

$where_str = "from_date_id not in ($test_user_id) and to_date_id not in ($test_user_id) and pay_status=1";

if ($date_status != 'all')
{
	$where_str .= " and date_status='{$date_status}'";
}

if ($from_date_id)
{
	$where_str .= " and from_date_id={$from_date_id}";
}

if($begin_time && $end_time)
{
	$bt = strtotime($begin_time);
	$et = strtotime($end_time)+86400;
	$where_str .= " and add_time between {$bt} and {$et}";
}

$date_list = get_all_event_date ( false, $where_str, "date_id DESC", '0,99999999' );

foreach ( $date_list as $k => $val )
{
	if ($val ['date_status'] == 'wait')
	{
		$status = "�ȴ�����";
	}
	elseif ($val ['date_status'] == 'confirm')
	{
		$status = "�ѽ���";
	}
	elseif ($val ['date_status'] == 'cancel')
	{
		$status = "�Ѿܾ�";
	}
	elseif ($val ['date_status'] == 'cancel_date')
	{
		$status = "��ȡ��";
	}
	elseif ($val ['date_status'] == 'refund')
	{
		$status = "����ǰ�˿�";
	}
	
	if ($val ['pay_status'] == 0)
	{
		$pay_status = "δ����";
	}
	elseif ($val ['pay_status'] == 1)
	{
		$pay_status = "�Ѹ���";
	}
	$new_date_list [$k]['date_id'] = $val ['date_id'];
	$new_date_list [$k]['enroll_id'] = $val ['enroll_id'];
	$new_date_list [$k]['event_id'] = $val ['event_id'];
	$user_info  = $user_obj->get_user_info($val ['to_date_id']);
	//print_r($user_info);
	if(is_array($user_info))
	{
	   $new_date_list[$k]['location_name'] = get_poco_location_name_by_location_id ($user_info['location_id']);
	   //echo $location_name;
	}
	$new_date_list [$k]['date_status'] = $status;
	$new_date_list [$k]['from_date_id'] = $val['from_date_id'];
	$new_date_list [$k] ['cameraman_nickname'] = get_user_nickname_by_user_id ( $val ['from_date_id'] );
	$new_date_list [$k] ['cameraman_cellphone'] = $user_obj->get_phone_by_user_id($val ['from_date_id']);
	$new_date_list [$k]['to_date_id'] = $val['to_date_id'];
	$new_date_list [$k] ['model_nickname'] = get_user_nickname_by_user_id ( $val ['to_date_id'] );
	$new_date_list [$k] ['model_cellphone'] = $user_obj->get_phone_by_user_id($val ['to_date_id']);
	$new_date_list [$k] ['style'] =  $val ['date_style'];
	$new_date_list [$k] ['hour'] =  $val ['hour'];
	$new_date_list [$k] ['date_price'] =  $val ['date_price'];
	$new_date_list [$k] ['discount_price'] =  $val ['discount_price'];
	$new_date_list [$k] ['pay_status'] =  $pay_status;
	$new_date_list [$k] ['pay_time'] = date ( "Y-m-d H:i", $val ['pay_time'] );
	
	$new_date_list [$k] ['date_time'] = date ( "Y-m-d H:i", $val ['date_time'] );
	
	$new_date_list [$k] ['date_address'] = $val ['date_address'];
	
	
	
	if ($val ['enroll_id'])
	{
		$is_checked = $activity_code_obj->check_code_scan ( $val ['enroll_id'] );
	}
	if ($is_checked)
	{
		$new_date_list [$k] ['is_checked'] = "��ǩ��";
	}
	else
	{
		$new_date_list [$k] ['is_checked'] = "δǩ��";
	}
	unset($is_checked);
	
	if ($val ['event_id'])
	{
		$event_info = $ret = $event_details_obj->get_event_by_event_id ( $val ['event_id'] );
		if ($event_info ['event_status'] == 0)
		{
			$new_date_list [$k] ['event_status'] = "������";
		}
		elseif ($event_info ['event_status'] == 2)
		{
			$new_date_list [$k] ['event_status'] = "�ѽ���";
		}
		elseif ($event_info ['event_status'] == 3)
		{
			$new_date_list [$k] ['event_status'] = "��ȡ��";
		}
	}else
	{
		$new_date_list [$k] ['event_status'] = "";
	}
	$new_date_list [$k] ['org_name']  = $pai_organization_obj->get_org_name_by_user_id($val ['org_user_id']);
	$new_date_list[$k]['is_set']  = $user_add_obj->get_user_inputer_name_by_user_id($val ['to_date_id']);
    $is_approval = $model_audit_obj->get_status_by_user_id( $val ['to_date_id']);
    if($is_approval == 1) $is_approval ='ͨ��';
    elseif($is_approval == 2) {
        $is_approval = 'δͨ��';
    }
    elseif($is_approval == 3) {
        $is_approval = '����';
    }
    else{
        $is_approval = '�����';
    }
    $new_date_list[$k]['is_approval'] = $is_approval;
}

//print_r($new_date_list);exit;
$title = "ģ���б�";
Excel_v2::start($headArr,$new_date_list,$fileName);
$time_end = microtime_float();
$time = $time_end - $time_start;
if($yue_login_id == 100293)
{
    echo $time;
}


?>