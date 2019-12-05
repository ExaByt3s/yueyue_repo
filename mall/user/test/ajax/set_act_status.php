<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 14 July, 2015
 * @package default
 */

/**
 * Define ����״̬
 */
include_once('../common.inc.php');	

$type = trim($_INPUT['type']);

switch($type)
{
	case 'del_enroll':
	$enroll_id = intval($_INPUT['enroll_id']);

	/**
	 * 
	 * ɾ������
	 * @param $enroll_id ����������
	 * 
	 * */
	$ret = del_enroll($enroll_id);

	$output_arr['code'] = $ret;
	$output_arr['msg'] = $ret ? '�˳��ɹ�' : '�˳�ʧ��';
	break;
	
	
	case 'cancel' :
	$event_id = intval($_INPUT['event_id']);

	/**
	 * 
	 * �ȡ��
	 * @param $event_id 
	 * 
	 * */
	$ret = set_event_status_by_cancel($event_id);

	$output_arr['code'] = $ret >0 ? 1 : 0;

	if($ret==-2){
		$output_arr['msg'] = "�ȡ��ʧ��";
	}elseif($ret==-1){
		$output_arr['msg'] = "�������ǩ��������ȡ���";
	}elseif($ret==1){
		$output_arr['msg'] = "�ȡ���ɹ�";
	}
	break;
	
	case 'end':
	$event_id = intval($_INPUT['event_id']);
	/**
	 * 
	 * �����
	 * @param $event_id 
	 * 
	 * */
	$ret = set_event_end_v2($event_id);

	$output_arr['code'] = $ret;

	if($ret==1){
		$output_arr['msg'] = 'ȷ�ϳɹ�';
	}elseif($ret==0){
		$output_arr['msg'] = 'ȷ�ϲ��ɹ�';
	}elseif($ret==-1){
		$output_arr['msg'] = '��û����ǩ�������Ժ�����';
	}elseif($ret==-2){
		$output_arr['msg'] = '״̬�쳣';
	}
	break;
	
}

mall_mobile_output($output_arr,false);

?>