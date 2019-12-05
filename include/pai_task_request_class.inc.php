<?php
/**
 * ����
 * @author Henry
 * @copyright 2015-04-09
 */

class pai_task_request_class extends POCO_TDG
{
	/**
	 * �ȴ�
	 * @var int
	 */
	const STATUS_WAIT = 0;
	
	/**
	 * �ѹ�Ӷ
	 * @var int
	 */
	const STATUS_HIRED = 1;
	
	/**
	 * ��ȡ��
	 * @var int
	 */
	const STATUS_CANCELED = 2;
	
	/**
	 * ��������
	 */
	private $expire_seconds = 86400; //24Сʱ��86400�룬��ʱ10���ӣ�600��
	
	/**
	 * ����ģ��
	 * @var string
	 */
	private $channel_module = 'task_request';
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_task_db');
	}
	
	/**
	 * ָ����
	 */
	private function set_task_request_tbl()
	{
		$this->setTableName('task_request_tbl');
	}	
	/**
	 * ָ����
	 */
	private function set_task_request_del_tbl()
	{
		$this->setTableName('task_request_del_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_task_request_question_tbl()
	{
		$this->setTableName('task_request_question_tbl');
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_request($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_task_request_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * ����ʾ����ϸ
	 * @param array $data
	 * @param int $id �ʾ��ID
	 * @return int
	 */	
	private function add_request_detail($data,$id)
	{
		if(is_array($data))
		{
			$this->set_task_request_question_tbl();
			foreach($data as $key => $val)
			{
				$in_data = array(
				                 'request_id'=>$id,
				                 'question_id'=>$val['question_titleid'],
				                 'question_detail_id'=>$val['anwserid'],
				                 'message'=>$val['message'],
								 );
				$this->insert($in_data);
			}
		}
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $request_id
	 * @return bool
	 */
	private function update_request($data, $request_id)
	{
		$request_id = intval($request_id);
		if( !is_array($data) || empty($data) || $request_id<1 )
		{
			return false;
		}
		$this->set_task_request_tbl();
		$this->update($data, "request_id={$request_id}");
		return true;
	}
	
	/**
	 * ��������״̬
	 * @param int $request_id
	 * @param array $more_info array('lead_status'=>1)
	 * @return boolean
	 */
	public function update_request_lead_status($request_id, $lead_status=1)
	{
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			return false;
		}
		$data = array(
			'lead_status' => $lead_status?1:0,
		);
		$this->set_task_request_tbl();
		$affected_rows = $this->update($data, "request_id={$request_id}");
		return $affected_rows>0?true:false;
	}

	/**
	 * ������֧��
	 * @param int $request_id
	 * @param array $more_info array('pay_time'=>0)
	 * @return boolean
	 */
	public function update_request_pay($request_id, $more_info=array())
	{
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_pay' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_request_tbl();
		$affected_rows = $this->update($data, "request_id={$request_id} AND is_pay=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ����������
	 * @param int $request_id
	 * @param array $more_info array('review_time'=>0)
	 * @return boolean
	 */
	public function update_request_review($request_id, $more_info=array())
	{
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_review' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_request_tbl();
		$affected_rows = $this->update($data, "request_id={$request_id} AND is_review=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ���õ���ʱ��
	 * @param int $request_id
	 * @return boolean
	 */
	public function reset_request_expire_time($request_id)
	{
		$expire_time = time() + $this->expire_seconds;
		
		//��������
		$data = array(
			'expire_time' => $expire_time,
		);
		$ret = $this->update_request($data, $request_id);
		
		if( $ret )
		{
			//���¹�������
			$task_lead_obj = POCO::singleton('pai_task_lead_class');
			$task_lead_obj->update_lead_expire_time($request_id, $expire_time);
		}
		
		return $ret;
	}
	
	/**
	 * �����ѹ�Ӷ
	 * @param int $request_id
	 * @param array $more_info array('hired_time'=>0)
	 * @return boolean
	 */
	public function update_request_status_hired($request_id, $more_info=array())
	{
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => self::STATUS_HIRED,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_request_tbl();
		$affected_rows = $this->update($data, "request_id={$request_id} AND status=" . self::STATUS_WAIT);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ������ȡ��
	 * @param int $request_id
	 * @param array $more_info array('canceled_time'=>0, 'canceled_reason'=>'')
	 * @return boolean
	 */
	private function update_request_status_canceled($request_id, $more_info=array())
	{
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => self::STATUS_CANCELED,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_request_tbl();
		$affected_rows = $this->update($data, "request_id={$request_id} AND status=" . self::STATUS_WAIT);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $request_id
	 * @return array
	 */
	public function get_request_info($request_id)
	{
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			return array();
		}
		$this->set_task_request_tbl();
		return $this->find("request_id={$request_id}");
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $user_id
	 * @param int $service_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_request_list($user_id, $service_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$user_id = intval($user_id);
		$service_id = intval($service_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $user_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "user_id={$user_id}";
		}
		
		if( $service_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "service_id={$service_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_task_request_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
		/**
	 * ��ȡ��ɾ���б�
	 * @param int $user_id
	 * @param int $service_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_request_del_list($user_id, $service_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$user_id = intval($user_id);
		$service_id = intval($service_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $user_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "user_id={$user_id}";
		}
		
		if( $service_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "service_id={$service_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_task_request_del_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_question($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_task_request_question_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $request_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_question_list($request_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$request_id = intval($request_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $request_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "request_id={$request_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_task_request_question_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ��ȡĬ��������Ϣ:��������/�ص�/ʱ��
	 * @param int $questionnaire_id
	 * @param array $data
	 * @return array array('service_name'=>'', 'when_str'=>'', 'where_str'=>'')
	 */
	public function get_questionnaire_defaultdata($data)
	{
		$questionnaire_id = (int)$data['question_id'];
		$version = $data['version'];
		$this->setTableName('pai_questionnaire_version_log_tbl');
		$re = $this->find('questionnaire_id="'.$questionnaire_id.'" and version="'.$version.'"');
		$version_data = unserialize(iconv('UTF-8','GBK',$re['content']));
		$when_str = '';
		$where_str = '';
		if(is_array($data['data']['question_detail_list']) and $version_data)
		{
			$re_detail_data=array();
			foreach($data['data']['question_detail_list'] as $key => $val)
			{
				if(is_array($val['data']))
				{
					foreach($val['data'] as $key_de => $val_de)
					{
						$val_de['question_titleid'] = $val['question_titleid'];
						$val_de['message']=$val_de['data']?1:$val_de['message'];
						$re_detail_data[$val_de['anwserid']]=$val_de;
						if(is_array($val_de['data']))
						{
							foreach($val_de['data'] as $key_de_2 => $val_de_2)
							{
								$val_de_2['question_titleid'] = $val['question_titleid'];
								$re_detail_data[$val_de_2['anwserid']]=$val_de_2;
							}
						}
					}
				}
			}
		}
		foreach($version_data['data'] as $val)
		{
			if($val['is_default'] == 1 and $val['type'] == 6 and $where_str=='')//����
			{
				$add_1 = '';
				$add_2 = '';
				$location_id = '';
				foreach($val['data'] as $val_de)
				{
					if($val_de['type']==6 and $location_id == '')
					{
						$location_id = (int)$re_detail_data[$val_de['id']]['message'];
						$add_1 = get_poco_location_name_by_location_id($location_id);
					}
					elseif($val_de['type']==4 and $add_2 == '')
					{
						$add_2 = $re_detail_data[$val_de['id']]['message'];
					}
				}
				$where_str=$add_1.$add_2;
			}
			elseif($val['is_default'] == 2 and $val['type'] == 7 and $when_str=='')//ʱ��
			{
				$time_1 = '';
				$time_2 = '';
				$time_3 = '';
				foreach($val['data'] as $val_de)
				{
					if($val_de['type']==7 and !$time_1)
					{
						$time_1 = $re_detail_data[$val_de['id']]['message'];
					}
					if($val_de['type']==8 and !$time_2)
					{
						$time_2 = $re_detail_data[$val_de['id']]['message'];
					}
					if($val_de['type']==9 and !$time_3)
					{
						$time_3 = $re_detail_data[$val_de['id']]['message'];
					}
				}
				$time_str = $time_1.($time_2?" ".$time_2:"");
				$when_str = $time_3?('��ʼʱ��:'.$time_str.' '.$time_3.' ��'):$time_str;
			}
		}
		$return = array(
			            'service_name'=>$version_data['service_name'],
			            'when_str'=>$when_str,
                        'where_str'=>$where_str,
                        'location_id'=>$location_id,
			           );
		return $return;
	}

	/**
	 * �ύ����
	 * @param int $user_id
	 * @param int $service_id
	 * @param bool $auto
	 * @param array auto_data
	 * @param array $more_info array('title'=>'', 'cellphone'=>'', 'email'=>'')
	 * @param array $question_list array( array('question_id'=>0, 'version'=>'', 'option_id_str'=>'', 'title'=>'', 'content'=>''),... )
	 * @return array array('result'=>0, 'message'=>'', 'request_id'=>0)
	 */
	public function submit_request($user_id, $service_id, $more_info, $question_list, $auto=false,$auto_data=array())
	{
		$ex_str = '--SN20YUE14--';//message�ָ���
		$result = array('result'=>0, 'message'=>'', 'request_id'=>0);
		
		$user_id = intval($user_id);
		$service_id = intval($service_id);
		if( !is_array($more_info) ) $more_info = array();
		//if( !is_array($question_list) ) $question_list = array();
		
		if( $user_id<1 || $service_id<1 || empty($question_list) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		//////////�ж��Ƿ��ܷ�������
		$is_submit_tmp = $this->get_request_is_submit($user_id,$service_id);
		if($is_submit_tmp == 0 and $auto==false)
		{
			$result['result'] = -1;
			$result['message'] = '�����ظ�������ͬ������';
			return $result;
		}
		//////////
		$title = trim($more_info['title']);
		$cellphone = trim($more_info['cellphone']);
		$email = trim($more_info['email']);
		//$when_str = trim($more_info['when_str']);
		//$where_str = trim($more_info['where_str']);
		$questionnaire_id = intval($question_list['question_id']);
		$version = trim($question_list['version']);
		
		$cur_time = time();
		$expire_time = $cur_time + $this->expire_seconds;
		$default_data = array();
		$default_data = $this->get_questionnaire_defaultdata($question_list);
		
		$request_data = array(
			'user_id' => $user_id,
			'service_id' => $service_id,
			'title' => $title,
			'cellphone' => $cellphone,
			'email' => $email,
			'when_str' => $default_data['when_str'],
			'where_str' => $default_data['where_str'],
			'location_id' => $default_data['location_id'],
			'questionnaire_id' => $questionnaire_id,
			'version' => $version,
			'request_data' => serialize($question_list),
			'status' => self::STATUS_WAIT,
			'lead_status' => 0,
			'expire_time' => $expire_time,
			'add_time' => $cur_time,
		);
		$request_id = $this->add_request($request_data);
		if( $request_id<1 )
		{
			$result['result'] = -2;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		//���`task_request_question_tbl`
		if(is_array($question_list['data']['question_detail_list']))
		{
			$re_detail_data=array();
			foreach($question_list['data']['question_detail_list'] as $key => $val)
			{
				//$val['data']['question_titleid']=$val['question_titleid'];
				//$re_detail_data[]=$val['data'];
				if(is_array($val['data']))
				{
					foreach($val['data'] as $key_de => $val_de)
					{
						$val_de['question_titleid'] = $val['question_titleid'];
						$val_de['message']=$val_de['data']?1:$val_de['message'];
						$re_detail_data[]=$val_de;
						if(is_array($val_de['data']))
						{
							foreach($val_de['data'] as $key_de_2 => $val_de_2)
							{
								$val_de_2['question_titleid'] = $val['question_titleid'];
								$re_detail_data[]=$val_de_2;
							}
						}
					}
				}
			}
		}
		$this->add_request_detail($re_detail_data,$request_id);
		////////////////
		
		//�¼�����
		$trigger_params = array('request_id'=>$request_id);
		$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
		$task_trigger_obj->request_submit_after($trigger_params);
		
		//�ж��Ƿ��Զ��ɵ�
		if($auto)//�Ƶ�������ڵ��û�id
		{
			$task_log_obj = POCO::singleton('pai_task_admin_log_class');
			$task_log_obj->add_log($user_id,1009,1,$_INPUT,'ϵͳ�Զ��ɵ�,����['.$request_id.']',$request_id);//
			$user_id_str = $auto_data['user_id'];
			//����
			$lead_re = $this->send_request_lead_by_artificial($request_id,$user_id_str,$auto);
			$task_log_obj->add_log($user_id,1009,1,$_INPUT,'ϵͳ�Զ��ɵ�,����['.$user_id_str.']',$request_id);//
			//����
			$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
			$quotes_ret = $task_quotes_obj->submit_quotes($request_id, $auto_data['user_id'], $auto_data['price'], $auto_data['content'],'',$auto);
			$task_log_obj->add_log($user_id,1009,1,$_INPUT,'ϵͳ�Զ��ɵ�,����['.$quotes_ret['quotes_id'].']',$request_id);//
			//��Ӷ
			$ret = $task_quotes_obj->hire_quotes($quotes_ret['quotes_id']);
			$task_log_obj->add_log($user_id,1009,1,$_INPUT,'ϵͳ�Զ��ɵ�,��Ӷ['.$quotes_ret['quotes_id'].']',$request_id);//
		}
		else
		{
			//��ͣ�Զ����͹���
			/*
			//�ж��Ƿ��Զ�����
			$lead_mode = $this->get_request_lead_mode();
			if($lead_mode==1)//�Ƿ��Զ�����
			{
				$this->submit_lead_by_request_id_all($request_id);
			}
			*/
			
			//��TT���󵥣�ת��OA���� 2015-08-05 18:39:00
			$model_oa_order_obj = POCO::singleton('pai_model_oa_order_class');
			$insert_data = array();
			$insert_data['service_id'] = $service_id;
			$insert_data['cameraman_phone'] = $cellphone;
			$insert_data['cameraman_nickname'] = get_user_nickname_by_user_id($user_id);
			$insert_data['location_id'] = $default_data['location_id'];
			$insert_data['date_address'] = '';
			$insert_data['audit_status'] = 'wait';
			$insert_data['order_status'] = 'wait';
			$insert_data['source'] = 5;
			$insert_data['request_id'] = $request_id;
			$model_oa_order_obj->add_order($insert_data);
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['request_id'] = $request_id;
		return $result;
	}
	
	/**
	 * ȡ������
	 * @param int $request_id
	 * @param string $reason
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function cancel_request($request_id, $reason)
	{
		$result = array('result'=>0, 'message'=>'');
		
		$request_id = intval($request_id);
		$reason = trim($reason);
		if( $request_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$request_info = $this->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		
		$more_info = array(
			'canceled_time' => time(),
			'canceled_reason' => $reason,
		);
		$update_ret = $this->update_request_status_canceled($request_id, $more_info);
		if( !$update_ret )
		{
			$result['result'] = -3;
			$result['message'] = 'ȡ��ʧ��';
			return $result;
		}
		
		//�������� declined
		$task_message_obj = POCO::singleton('pai_task_message_class');
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_list = $task_quotes_obj->get_quotes_list_for_valid($request_id);
		foreach($quotes_list as $val)
		{
			if( $val['status']==0 )
			{
				$task_message_obj->submit_message($request_info['user_id'], $val['quotes_id'], 'declined');
			}
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * �������
	 * @param int $request_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function end_request($request_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$request_info = $this->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$title = trim($request_info['title']);
		
		//��ȡ�����б���֧�������
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_list = $task_quotes_obj->get_quotes_list($request_id, 0, false, 'status=1 AND is_review=1 AND is_pay=1', 'quotes_id ASC', '0,99999999');
		if( empty($quotes_list) )
		{
			$result['result'] = -3;
			$result['message'] = 'δ֧�������';
			return $result;
		}
		
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$coupon_cash_list = array();
		foreach($quotes_list as $quotes_info)
		{
			$quotes_id = intval($quotes_info['quotes_id']);
			
			$in_list[] = array(
				'discount_amount' => $quotes_info['discount_price'],
				'user_id' => $quotes_info['user_id'],
				'org_user_id' => 0,
				'apply_id' => 0,
				'amount' => $quotes_info['pay_amount'],
				'org_amount' => 0,
				'subject' => $title,
				'remark' => '',
			);
			
			//�����Ż�ȯ
			$ref_order_list = $coupon_obj->get_ref_order_list_by_oid($this->channel_module, $quotes_id);
			if( !is_array($ref_order_list) ) $ref_order_list = array();
			foreach( $ref_order_list as $ref_order_info)
			{
				$coupon_cash_list[] = array(
					'id' => $ref_order_info['id'],
					'user_id' => $quotes_info['user_id'],
					'org_user_id' => 0,
					'amount' => $ref_order_info['used_amount'],
					'org_amount' => 0,
					'subject' => $title,
					'remark' => '',
				);
			}
		}
		
		$refund_list = array();
		$coupon_refund_list = array();
		$payment_obj = POCO::singleton('pai_payment_class');
		$end_ret = $payment_obj->end_event_v2($this->channel_module, $request_id, $refund_list, $in_list, $coupon_refund_list, $coupon_cash_list);
		if( $end_ret['error']!==0 )
		{
			$result['result'] = -4;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * ������ϸ��Ϣ
	 * @param array $list
	 * @return array
	 */
	private function fill_request_detail_list($list)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		
		$cur_time = time();
		foreach($list as $key=>$info)
		{
			$request_id = intval($info['request_id']);
			$is_pay = intval($info['is_pay']);
			$is_review = intval($info['is_review']);
			$expire_time = intval($info['expire_time']);
			$status = intval($info['status']);
			$add_time = intval($info['add_time']);
			$add_time_str = '������' . date('Y.m.d', $add_time);
			
			
			//��ȡ��Ч�ı����б�
			$quotes_list = $task_quotes_obj->get_quotes_detail_list_for_valid($request_id);
			$quotes_count = count($quotes_list);
			
			//����״̬
			$status_color = ''; //״̬��ɫ
			$status_code = ''; //״̬��ʶ
			$status_name = ''; //״̬����
			if( $status===self::STATUS_WAIT ) //����Ӷ
			{
				if( $cur_time<$expire_time ) //����Ӷ��������
				{
					if( $quotes_count<1 ) //����Ӷ�������ڣ��ޱ���
					{
						$status_color = '0xff5daaeb';
						$status_code = 'started';
						$status_name = '���������ɹ�';
					}
					else //����Ӷ�������ڣ��б���
					{
						$status_color = '0xff5daaeb';
						$status_code = 'introduced';
						$status_name = "{$quotes_count}�˽ӵ�";
					}
				}
				else //����Ӷ���ѹ���
				{
					if( $quotes_count<1 ) //����Ӷ���ѹ��ڣ��ޱ���
					{
						$status_color = '0xff9ba5b1';
						$status_code = 'closed';
						$status_name = '���˽ӵ�';
					}
					else //����Ӷ���ѹ��ڣ��б���
					{
						$status_color = '0xfffe9920';
						$status_code = 'quoted';
						$status_name = 'ȷ�϶���';
					}
				}
			}
			elseif( $status===self::STATUS_HIRED ) //�ѹ�Ӷ
			{
				if( $is_pay===0 ) //�ѹ�Ӷ����֧��
				{
					$status_color = '0xff63cb76';
					$status_code = 'hired';
					$status_name = '�ȴ�֧�������';
				}
				else //�ѹ�Ӷ����֧��
				{
					if( $is_review===0 ) //�ѹ�Ӷ����֧����������
					{
						$status_color = '0xff63cb76';
						$status_code = 'paid';
						$status_name = '����������';
					}
					else //�ѹ�Ӷ����֧����������
					{
						$status_color = '0xff63cb76';
						$status_code = 'reviewed';
						$status_name = '���������';
					}
				}
			}
			elseif( $status===self::STATUS_CANCELED ) //��ȡ��
			{
				$status_color = '0xff9ba5b1';
				$status_code = 'canceled';
				$status_name = '��ȡ������';
			}
			else //δ֪״̬
			{
				$status_color = '0xff9ba5b1';
				$status_code = 'closed';
				$status_name = 'δ֪״̬';
			}
			
			$info['add_time_str'] = $add_time_str;
			$info['status_color'] = $status_color;
			$info['status_code'] = $status_code;
			$info['status_name'] = $status_name;
			$info['status_code'] = $status_code;
			$info['quotes_count'] = $quotes_count;
			$info['quotes_list'] = $quotes_list;
			$info['nickname'] = get_user_nickname_by_user_id($info['user_id']);
			
			$list[$key] = $info;
		}
		
		return $list;
	}
	
	/**
	 * ��ȡ������Ϣ
	 * @param int $quotes_id
	 * @return array
	 */
	public function get_request_detail_info_by_id($request_id)
	{
		$request_info = $this->get_request_info($request_id);
		if( empty($request_info) )
		{
			return array();
		}
		$request_detail_list = $this->fill_request_detail_list(array($request_info));
		$request_detail_info = $request_detail_list[0];
		if( !is_array($request_detail_info) ) $request_detail_info = array();
		return $request_detail_info;
	}
	
	/**
	 * ��ȡδ�����������б�
	 * @param int $user_id
	 * @param int $service_id
	 * @return array
	 */
	public function get_request_detail_list_by_user_id($user_id, $service_id=0)
	{
		$user_id = intval($user_id);
		$service_id = intval($service_id);
		if( $user_id<1 )
		{
			return array();
		}
		$where_str = "status NOT IN (7,8)";
		$request_list = $this->get_request_list($user_id, $service_id, false, $where_str, 'request_id DESC', '0,99999999');
		return $this->fill_request_detail_list($request_list);
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $user_id
	 * @param int $service_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_request_detail_list($user_id, $service_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$request_list = $this->get_request_list($user_id, $service_id, $b_select_count, $where_str, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $request_list;
		}
		return $this->fill_request_detail_list($request_list);
	}	
	/**
	 * ��ȡ��ɾ�����б�
	 * @param int $user_id
	 * @param int $service_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_request_del_detail_list($user_id, $service_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$request_list = $this->get_request_del_list($user_id, $service_id, $b_select_count, $where_str, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $request_list;
		}
		return $this->fill_request_detail_list($request_list);
	}
	
	/**
	 * ��ȡ�Ƿ��������Ա��ж��ǽ������б������·�����
	 * @param int $user_id
	 * @return string 0�� 1��
	 */
	public function get_request_is_have($user_id)
	{
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return '0';
		}
		$where_str = "status NOT IN (7,8)";
		$request_count = $this->get_request_list($user_id, 0, true, $where_str);
		return $request_count?'1':'0';
	}
	
	/**
	 * ��ȡ�Ƿ��ܷ�����
	 * @param int $user_id
	 * @param int $service_id
	 * @return string 0�� 1��
	 */
	public function get_request_is_submit($user_id, $service_id)
	{
		return '1'; //TT����ת��OA�������ظ�������
		
		$result = '1';
		
		$user_id = intval($user_id);
		$service_id = intval($service_id);
		if( $user_id<1 || $service_id<1 )
		{
			return '0';
		}
		
		//TODO ��ʱ������Ҫ
		if( $user_id==100028 )
		{
			return '1';
		}
		
		$request_detail_list = $this->get_request_detail_list_by_user_id($user_id, $service_id);
		foreach ($request_detail_list as $request_detail_info)
		{
			$status_code = trim($request_detail_info['status_code']);
			if( in_array($status_code, array('started', 'introduced', 'quoted')) )
			{
				$result = '0';
				break;
			}
		}
		return $result;
	}
	
	/**
	 * �޸�״̬Ϊɾ��
	 * @param int $user_id
	 * @param int $request_id
	 * @return array
	 */
	public function change_request_status_del($user_id, $request_id)
	{
		$result = array('result'=>1,'message'=>'ɾ���ɹ�');
		$request_id = (int)$request_id;
		$user_id = (int)$user_id;
		$request_info = $this->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if($request_info['user_id'] != $user_id)
		{
			$result['result'] = -2;
			$result['message'] = '��Ȩ��,���ɲ���';
			return $result;
		}
		if(in_array($request_info['status'],array(7,8)))
		{
			$result['result'] = -3;
			$result['message'] = '�Ѿ�ɾ��,���ɲ���';
			return $result;
		}		
		$request_detail_list = $this->fill_request_detail_list(array($request_info));
		$request_detail_info = $request_detail_list[0];
		if(!in_array($request_detail_info['status_code'],array('closed')))
		{
			$result['result'] = -4;
			$result['message'] = '����������,���ɲ���';
			return $result;
		}
		$re = $this->update_request(array('status'=>7), $request_id);
		if(!$re)
		{
			$result['result'] = -5;
			$result['message'] = 'ɾ��ʧ��';
			return $result;
		}
		return $result;
	}

	/**
	 * ɾ������
	 * @param int $request_id
	 * @param array $data=array('admin_id','admin_note')
	 * @return string 0ʧ�� 1�ɹ�
	 */
	public function del_request($request_id,$data = array())
	{
		$result = 0;
		$request_id = (int)$request_id;
		if($request_id)
		{
			$request_info = $this->get_request_info($request_id);		
	        if($request_info and $request_info['lead_status'] == 0)
	        {
	        	$request_info['admin_id'] = (int)$data['admin_id'];
	        	$request_info['admin_note'] = $data['admin_note'];
	        	$request_info['del_time'] = time();
		 		$this->set_task_request_del_tbl();
				$re = $this->insert($request_info);
				if($re)
				{
					$this->set_task_request_tbl();
					$where = 'request_id="'.$request_id.'"';
					$this->delete($where);
					$result = 1;
				}	        	
	        }
        }
		return $result;
	}

	/**
	 * �ֶ��ύ��������
	 * @param int $request_id
	 * @param int $user_id_str �û�id,�����ö��ŷֿ�,��"100251,123652"
	 * @return array
	 */
	public function send_request_lead_by_artificial($request_id,$user_id_str,$auto=false)
	{
		$result = array('result'=>0, 'message'=>'');
		
		$request_id = intval($request_id);
		if( $request_id<1 or !preg_match ("/^[\d+,]+$/", $user_id_str) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		$request_info = $this->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'û�и������ʾ�';
			return $result;
		}
		/*��˲���Ҫ��������
		if($request_info['expire_time'] < time() )
		{
			$result['result'] = -3;
			$result['message'] = '�����ѹ���';
			return $result;
		}
		*/
		if($request_info['lead_status']!= 0 )
		{
			$result['result'] = -4;
			$result['message'] = '�Ѿ����͹�����';
			return $result;
		}
		$re_update = $this->update_request_lead_status($request_id,1);//����lead_status
		if($re_update)
		{
			$task_lead_obj = POCO::singleton('pai_task_lead_class');
			$result = $task_lead_obj -> submit_lead_by_artificial($request_id,$user_id_str,$auto);
			
			//�¼�����
			$trigger_params = array('request_id'=>$request_id);
			$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
			$task_trigger_obj->request_pass_after($trigger_params,$auto);
		}
		return $result;			
	}


	/**
	 * �ֶ��ύ��������
	 * @param int $request_id
	 * @param int $user_id_str �û�id,�����ö��ŷֿ�,��"100251,123652"
	 * @return array
	 */
	public function send_request_lead_by_artificial_again($request_id,$user_id_str)
	{
		$result = array('result'=>0, 'message'=>'');
		
		$request_id = intval($request_id);
		if( $request_id<1 or !preg_match ("/^[\d+,]+$/", $user_id_str) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		$request_info = $this->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'û�и������ʾ�';
			return $result;
		}
		if($request_info['expire_time'] < time() )
		{
			$result['result'] = -3;
			$result['message'] = '�����ѹ���';
			return $result;
		}

		$task_lead_obj = POCO::singleton('pai_task_lead_class');
		$result = $task_lead_obj -> submit_lead_by_artificial($request_id,$user_id_str);
		
		return $result;			
	}

	/**
	 * �ֶ��ύ�������� All
	 * @param int $request_id
	 * @return array
	 */
	public function submit_lead_by_request_id_all($request_id)
	{
		$request_id = (int)$request_id;
		if( $request_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$request_info = $this->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'û�и������ʾ�';
			return $result;
		}
		/*��˲���Ҫ��������
		if($request_info['expire_time'] < time() )
		{
			$result['result'] = -3;
			$result['message'] = '�����ѹ���';
			return $result;
		}
		*/
		if($request_info['lead_status']!= 0 )
		{
			$result['result'] = -4;
			$result['message'] = '�Ѿ����͹�����';
			return $result;
		}
		$re_update = $this->update_request_lead_status($request_id,1);//����lead_status
		if($re_update)
		{	
			$task_lead_obj = POCO::singleton('pai_task_lead_class');
			$result = $task_lead_obj->submit_lead_by_request_id($request_id);
			
			//�¼�����
			$trigger_params = array('request_id'=>$request_id);
			$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
			$task_trigger_obj->request_pass_after($trigger_params);
	    }
		return $result;
	}	

	/**
	 * ��ȡ����ģʽ
	 * @return string 0�ֶ� 1�Զ�
	 */
	public function get_request_lead_mode()
	{
		$lead_mode = 0;
		$name = 'G_PAI_TASK_REQUEST_LEAD_MODE';
        $task_setting_obj = POCO::singleton('pai_task_setting_class');
        $lead_mode = $task_setting_obj->get($name);
        return $lead_mode;
	}

	/**
	 * �޸�����ģʽ
	 * @param int $lead_mode 0�ֶ� 1�Զ�
	 * @return true
	 */
	public function set_request_lead_mode($lead_mode=0)
	{
		$lead_mode = (int)$lead_mode == 1?1:0;
		$name = 'G_PAI_TASK_REQUEST_LEAD_MODE';
        $task_setting_obj = POCO::singleton('pai_task_setting_class');
        $task_setting_obj->set($name, $lead_mode);
        return true;
	}

	/**
	 * ��ʱ��ʶ������û�״̬
	 * @param int $request_id
	 * @return true
	 */
	public function update_request_paytouser($request_id)
	{
		$request_id = (int)$request_id;
		$re_info = $this->get_request_info($request_id);
		if(!$re_info or !($re_info['is_pay'] and $re_info['is_review']))
		{
			return false;
		}
		$data = array(
			         'is_paytouser'=>1,
			         'paytouser_time'=>time(),
			         );
		$re = $this->update_request($data, $request_id);
		return $re;
	}
	
	
	/*
	 * ��ȡ�û��ʾ��ѹ�Ӷ���̼�
	 * @param int $request_id
	 * @return array
	 */
	public function get_seller_info_by_request_id($request_id)
	{
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$task_profile_obj = POCO::singleton('pai_task_profile_class');
		
		$quotes_list = $task_quotes_obj->get_quotes_list($request_id, 0, false, "status=1", '', '0,1');
		
		$profile_info = $task_profile_obj->get_profile_info_by_id($quotes_list[0]['profile_id']);
		
		$info_arr['profile_title'] = $profile_info['title'];
		$info_arr['pay_amount'] = $quotes_list[0]['pay_amount'];
		
		return $info_arr;
		
	}
}
