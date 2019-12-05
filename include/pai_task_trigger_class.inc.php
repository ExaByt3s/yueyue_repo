<?php
/**
 * �¼�����
 * �������淶��ģ������_�¼�_ǰ/��
 * @author Henry
 * @copyright 2015-04-16
 */

class pai_task_trigger_class
{
	/**
	 * ���캯��
	 */
	public function __construct()
	{
	}
	
	/**
	 * ����_�ύ_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'request_id' => 0, //����ID
	 * );
	 */
	public function request_submit_after($params)
	{
		//������
		$request_id = intval($params['request_id']);
		if( $request_id<1 )
		{
			return false;
		}
		
		//��ȡ����
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		$cur_time = time();
		
		//2015��5�� �״���������ȯ
		if( strtotime('2015-05-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-05-31 23:59:59') )
		{
			$request_count = $task_request_obj->get_request_detail_list($buyer_user_id, 0, true);
			if( $request_count==1 )
			{
				$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
				$coupon_give_obj->submit_queue('Y2015M05D01_TASK_REQUEST_FIRST', '', $buyer_user_id, 0);
			}
		}
		
		return true;
	}
	
	/**
	 * ����_ͨ��_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'request_id' => 0, //����ID
	 * );
	 */
	public function request_pass_after($params,$auto=false)
	{
		if($auto)
		{
			return true;
		}
		//������
		$request_id = intval($params['request_id']);
		if( $request_id<1 )
		{
			return false;
		}
		
		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		//��ȡ������Ϣ
		$service_id = intval($request_info['service_id']);
		$task_service_obj = POCO::singleton('pai_task_service_class');
		$service_info = $task_service_obj->get_service_info($service_id);
		if( empty($service_info) )
		{
			return false;
		}
		
		$content = "�����Ѿ����㷢����{$service_info['service_name']}�������͸�ÿһ�������ʸ��{$service_info['profession_name']}��\r\n�������24Сʱ���յ����������ۣ����������ﵽ�������򷢲�����ʱ��ﵽ24Сʱ��{$service_info['profession_name']}�������ٷ��ͱ��ۡ�";
		$url = '';
		send_message_for_10006($buyer_user_id, $content, $url);
		
		return true;
	}
	
	/**
	 * ����_֧�����⿨_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //����ID
	 * );
	 */
	public function quotes_pay_coins_after($params)
	{
		//������
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		//��ȡ������Ϣ
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);
		
		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$service_id = intval($request_info['service_id']);
		$buyer_user_id = intval($request_info['user_id']);
		
		//��ȡ������Ϣ
		$task_service_obj = POCO::singleton('pai_task_service_class');
		$service_info = $task_service_obj->get_service_info($service_id);
		if( empty($service_info) )
		{
			return false;
		}
		
		$send_data = array();
		$send_data['media_type'] ='card';
		$send_data['card_style'] = 2;
		$send_data['card_text1'] = "���յ���һ��{$service_info['service_name']}���±���";
		$send_data['card_title'] = '�鿴��������';
		$send_data['link_url'] = 'yueyue://goto?type=inner_app&pid=1220079&request_id=' . $request_id;
		$send_data['wifi_url'] = 'yueyue://goto?type=inner_app&pid=1220079&request_id=' . $request_id;
		$push_obj = POCO::singleton('pai_information_push');
		$push_obj->send_msg_for_system_v2(10006, $buyer_user_id, $send_data);
		//���ۺ��app����������Ϣ
		$content = "���յ���һ���µı��ۡ���������뱨���б�";
		send_offline_message($buyer_user_id,$content);
		
		/*
		//��ȡ����ֻ�����
		$pai_user_obj = POCO::singleton('pai_user_class');
		$buyer_cellphone = $pai_user_obj->get_phone_by_user_id($buyer_user_id);
		$sms_data = array(
			'service_name' => $service_info['service_name'],
		);
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$pai_sms_obj->send_sms($buyer_cellphone, 'G_PAI_TASK_QUOTES_PAY_COINS_BUYER', $sms_data);
		*/
		
		//��ȡ���۵��������жϱ����Ƿ�����������ֻ�����������������δ�������ڶ�ʱִ���Ǳߴ���
		$quotes_count = $task_quotes_obj->get_quotes_list_for_valid($request_id, true);
		$max_quotes_num = $task_quotes_obj->get_max_quotes_num();
		if( $quotes_count>0 && $quotes_count==$max_quotes_num )
		{
			//��ȡ����ֻ�����
			$pai_user_obj = POCO::singleton('pai_user_class');
			$buyer_cellphone = $pai_user_obj->get_phone_by_user_id($buyer_user_id);
			$sms_data = array();
			$pai_sms_obj = POCO::singleton('pai_sms_class');
			$pai_sms_obj->send_sms($buyer_cellphone, 'G_PAI_TASK_QUOTES_FINISH_BUYER', $sms_data);
			
// 			$content = "����ԼԼ�Ϸ�����{$service_info['service_name']}�����Ѿ���{$quotes_count}�˷����˱��ۡ���ȥ������";
// 			$url = '';
// 			send_message_for_10006($buyer_user_id, $content, $url);
		}
		
		return true;
	}
	
	/**
	 * ����_���鿴_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //����ID
	 * );
	 */
	public function quotes_read_after($params)
	{
		//������
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * ����_��Ӷ_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //����ID
	 * );
	 */
	public function quotes_hire_after($params)
	{
		//������
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		//��ȡ������Ϣ
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);
		$seller_user_id = intval($quotes_info['user_id']);
		$seller_nickname = get_user_nickname_by_user_id($seller_user_id);
		
		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		$buyer_nickname = get_user_nickname_by_user_id($buyer_user_id);
		
		$content = "���Ѿ���{$seller_nickname}��ɶ���������Ta��ϵ��ȷ��ѡ����ٽ��з�����֧����";
		$url = '';
		send_message_for_10006($buyer_user_id, $content, $url);
		
		//��ȡ����ֻ�����
		$pai_user_obj = POCO::singleton('pai_user_class');
		$buyer_cellphone = $pai_user_obj->get_phone_by_user_id($buyer_user_id);
		$sms_data = array(
			'seller_nickname' => $seller_nickname,
		);
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$pai_sms_obj->send_sms($buyer_cellphone, 'G_PAI_TASK_QUOTES_HIRE_BUYER', $sms_data);
		
		//��ȡ�����ֻ�����
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$seller_cellphone = $task_seller_obj->get_seller_cellphone($seller_user_id);
		$sms_data = array(
			'buyer_nickname' => $buyer_nickname,
			'url' => 'task.yueus.com/m/talk.php?quotes_id='.$quotes_id,
		);
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$pai_sms_obj->send_sms($seller_cellphone, 'G_PAI_TASK_QUOTES_HIRE_SELLER', $sms_data);
		
		return true;
	}
	
	/**
	 * ����_֧������_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //����ID
	 * );
	 */
	public function quotes_pay_after($params)
	{
		//������
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * ����_����_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'quotes_id' => 0, //����ID
	 * );
	 */
	public function quotes_review_after($params)
	{
		//������
		$quotes_id = intval($params['quotes_id']);
		if( $quotes_id<1 )
		{
			return false;
		}
		
		//��ȡ������Ϣ
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);
		$seller_user_id = intval($quotes_info['user_id']);
		
		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		$cur_time = time();
		
		//2015��5�� �״������̼���ȯ
		if( strtotime('2015-05-01 00:00:00')<=$cur_time && $cur_time<=strtotime('2015-05-31 23:59:59') )
		{
			$request_count = $task_request_obj->get_request_detail_list($buyer_user_id, 0, true, "is_review=1");
			if( $request_count==1 )
			{
				$coupon_give_obj = POCO::singleton('pai_coupon_give_class');
				$coupon_give_obj->submit_queue('Y2015M05D01_TASK_REVIEW_FIRST', '', $buyer_user_id, 0);
			}
		}
		
		return true;
	}
	
	/**
	 * ��Ϣ_�ύ_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'message_id' => 0, //��ϢID
	 * );
	 */
	public function message_submit_after($params)
	{
		//������
		$message_id = intval($params['message_id']);
		if( $message_id<1 )
		{
			return false;
		}
		
		//��ȡ��Ϣ��Ϣ
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$message_info = $task_message_obj->get_message_info($message_id);
		if( empty($message_info) )
		{
			return false;
		}
		$quotes_id = intval($message_info['quotes_id']);
		$from_user_id = intval($message_info['from_user_id']);
		$to_user_id = intval($message_info['to_user_id']);
		
		//��ȡ������Ϣ
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			return false;
		}
		$request_id = intval($quotes_info['request_id']);
		$seller_user_id = intval($quotes_info['user_id']);
		
		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		//���ң��̼ң�=> ����ң��û���
		if( $to_user_id==$buyer_user_id && in_array($message_info['message_type'], array('message')) )
		{
			$from_nickname = get_user_nickname_by_user_id($from_user_id);
			$send_data = array();
			$send_data['media_type'] ='card';
			$send_data['card_style'] = 2;
			$send_data['card_text1'] = "{$from_nickname}���㷢��һ���µ���Ϣ";
			$send_data['card_title'] = '�鿴��Ϣ����';
			$send_data['link_url'] = 'yueyue://goto?type=inner_app&pid=1220079&request_id=' . $request_id;
			$send_data['wifi_url'] = 'yueyue://goto?type=inner_app&pid=1220079&request_id=' . $request_id;
			$push_obj = POCO::singleton('pai_information_push');
			$push_obj->send_msg_for_system_v2(10006, $to_user_id, $send_data);
			//��Ϣ�ظ����app����������Ϣ
			$content = "���յ���һ���µĻظ�����������뱨���б��µ���Ϣ����";
			send_offline_message($to_user_id,$content);
		}
		
		return true;
	}
	
	/**
	 * ��������_�ύ_��
	 * @param array $params
	 * @return boolean
	 * @tutorial
	 * $params = array(
	 *   'lead_id' => 0, //��ϢID
	 * );
	 */
	public function lead_submit_after($params)
	{
		//������
		$lead_id = intval($params['lead_id']);
		if( $lead_id<1 )
		{
			return false;
		}
		
		//��ȡ������Ϣ
		$task_lead_obj = POCO::singleton('pai_task_lead_class');
		$lead_info = $task_lead_obj->get_lead_info($lead_id);
		if( empty($lead_info) )
		{
			return false;
		}
		$seller_user_id = intval($lead_info['user_id']);
		$request_id = intval($lead_info['request_id']);
		
		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info))
		{
			return false;
		}
		$service_id = intval($request_info['service_id']);
		$buyer_user_id = intval($request_info['user_id']);
		$buyer_nickname = get_user_nickname_by_user_id($buyer_user_id);
		
		//��ȡ������Ϣ
		$task_service_obj = POCO::singleton('pai_task_service_class');
		$service_info = $task_service_obj->get_service_info($service_id);
		if( empty($service_info) )
		{
			return false;
		}
		
		//��ȡ��������
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$email = $task_seller_obj->get_seller_email($seller_user_id);
		if( strlen($email)<1 ) 
		{
			return false;
		}
		
		//��ȡ�ʾ��
		$task_questionnaire_obj = POCO::singleton('pai_task_questionnaire_class');
		$questionnaire_data = $task_questionnaire_obj->show_questionnaire_data($request_id);
		if( empty($questionnaire_data) || !is_array($questionnaire_data['data']) || empty($questionnaire_data['data']) )
		{
			return false;
		}
		$question_answer_content = '';
		$question_list = $questionnaire_data['data'];
		foreach ($question_list as $question_info)
		{
			$question_answer_content .= trim($question_info['titles']) . '<br />';
			$answer_list = $question_info['data'];
			if( !is_array($answer_list) ) $answer_list = array();
			foreach ($answer_list as $answer_info)
			{
				$question_answer_content .= trim($answer_info['titles']) . '<br />';
			}
			$question_answer_content .= '<br />';
		}
		
		$email_title = "ԼԼ��������һ���µ��������";
		$email_html = "ԼԼ��������һ���µ�������ᡣ<br /><br />
{$buyer_nickname}��Ҫһ��{$service_info['profession_name']}<br />
�������£�<br />
{$question_answer_content}
��� <a href=\"http://task.yueus.com/lead_detail.php?lead_id={$lead_id}\" target=\"_blank\">����鿴����</a>
<br /><br />===============================================<br />
ԼԼ�����Ч����Ӱ����ƽ̨<br />
һվʽ������Ի�����<br />
��ģ�ء�����Ӱ��ѵ������Ӱ���ء�����Ӱ���ף���ԼԼ<br />
�������䣺iwantu@yueus.com<br />
ԼԼ�ٷ��绰��4000-82-9003<br />";

		$pai_email_obj = POCO::singleton('pai_email_class');
		$pai_email_obj->send_email($email, $email_title, $email_html);
		
		return true;
	}
	
}
