<?php
/**
 * 推送引导
 * @author Henry
 * @copyright 2015-04-09
 */

class pai_task_lead_class extends POCO_TDG
{
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_task_db');
	}
	
	/**
	 * 指定表
	 */
	private function set_task_lead_tbl()
	{
		$this->setTableName('task_lead_tbl');
	}
	
	/**
	 * 获取操作权限
	 * @param int $user_id
	 * @param int $lead_id
	 * @return true false
	 */
	public function check_user_auth($user_id,$lead_id)
	{
		$user_id = (int)$user_id;
		$lead_id = (int)$lead_id;
		if(!$user_id or !$lead_id)
		{
			return false;
		}		
		$return = $this->get_lead_info($lead_id);
		return $return['user_id'] == $user_id?true:false;
	}

	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_lead($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_task_lead_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/*
	 * 获取需求列表
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_lead_list($b_select_count = false, $where_str = '', $order_by = 'add_time DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_task_lead_tbl();
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	/**
	 * 补充详细信息
	 * @param array $list
	 * @return array
	 */
	private function fill_lead_detail_list($list)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$task_review_obj = POCO::singleton('pai_task_review_class');
		$task_profile_obj = POCO::singleton('pai_task_profile_class');
		
		foreach($list as $k=>$val)
		{
			$list[$k]['seller_user_icon'] = get_user_icon($val['user_id']);
			$list[$k]['buyer_user_icon'] = get_user_icon($val['from_user_id']);
			$list[$k]['buyer_nickname'] = get_user_nickname_by_user_id($val['from_user_id']);
			$list[$k]['seller_nickname'] = get_user_nickname_by_user_id($val['user_id']);
			$list[$k]['count_review'] = floor($task_review_obj->get_user_review_list(true,$val['user_id']));
			
			$request_info = $task_request_obj->get_request_info($val['request_id']);
			$profile_info = $task_profile_obj->get_profile_info_by_user_id($val['user_id']);
			//$profile_info = $task_profile_obj->get_profile_info_by_id($val['profile_id']);
			
			$list[$k]['rank'] = $profile_info['rank'];
			$list[$k]['service_id'] = $request_info['service_id'];
			$list[$k]['cellphone'] = $profile_info['cellphone'];
			
			$list[$k]['title'] = $request_info['title'];
			$list[$k]['when_str'] = $request_info['when_str'];
			$list[$k]['where_str'] = $request_info['where_str'];
			
			
			$list[$k]['time_format'] = yue_time_format_convert($val['add_time']);
		}
		
		return $list;
	}

	/*
	 * 获取需求已推送用户列表
	* @param int $user_id
	* @param bool $b_select_count
	* @param string $where_str 查询条件
	* @param string $order_by 排序
	* @param string $limit
	* @param string $fields 查询字段
	*
	* return array
	*/
	public function get_lead_list_by_request_id($request_id='',$b_select_count = false, $where_str = '', $limit = '0,10', $order_by = 'add_time DESC',  $fields = '*')
	{
		$request_id = (int)$request_id;
	
		if($where_str)
		{
			$where_request = "request_id={$request_id} and ";
			$where_str = $where_request.$where_str;
		}
		else
		{
			$where_str = "request_id={$request_id}";
		}
	
		$this->set_task_lead_tbl();
	
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
	
		return $this->fill_lead_detail_list($ret);
	
	}	
	
	/*
	 * 获取用户需求列表
	* @param int $user_id
	* @param bool $b_select_count
	* @param string $where_str 查询条件
	* @param string $order_by 排序
	* @param string $limit
	* @param string $fields 查询字段
	*
	* return array
	*/
	public function get_lead_list_by_user_id($user_id='',$b_select_count = false, $where_str = '', $limit = '0,10', $order_by = 'add_time DESC',  $fields = '*')
	{
		$user_id = (int)$user_id;
	
		if($where_str)
		{
			$where_user = "user_id={$user_id} and ";
			$where_str = $where_user.$where_str;
		}
		else
		{
			$where_str = "user_id={$user_id}";
		}
	
		$this->set_task_lead_tbl();
	
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
	
		return $this->fill_lead_detail_list($ret);
	
	}
	
	/*
	 * 获取用户有效需求列表
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array|int
	 */
	public function get_lead_list_valid_by_user_id($user_id='',$b_select_count = false, $where_str = '', $limit = '0,10', $order_by = 'add_time DESC',  $fields = '*')
	{
		$user_id = (int)$user_id;
		if( $user_id<1 )
		{
			return $b_select_count?0:array();
		}
		
		$cur_time = time();
		$sql_where = "user_id={$user_id} AND status=0 AND expire_time>{$cur_time}";
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		$this->set_task_lead_tbl();
		if ($b_select_count == true)
		{
			$ret = $this->findCount($sql_where);
		} 
		else
		{
			$ret = $this->findAll($sql_where, $limit, $order_by, $fields);
		}
		
		return $this->fill_lead_detail_list($ret);
	}
	
	/**
	 * 获取信息，以便提醒
	 * @param int $begin_time
	 * @param int $end_time
	 * @return array
	 */
	public function get_user_id_and_count_list_for_remind($begin_time, $end_time)
	{
		$begin_time = intval($begin_time);
		$end_time = intval($end_time);
		if( $begin_time<1 || $end_time<1 )
		{
			return array();
		}
		$cur_time = time();
		$where_str = "status=0 AND is_read=0 AND expire_time>{$cur_time} AND {$begin_time}<=add_time AND add_time<={$end_time}";
		$this->set_task_lead_tbl();
		$sql = "SELECT user_id,COUNT(user_id) AS count FROM {$this->_db_name}.{$this->_tbl_name} WHERE {$where_str} GROUP BY user_id ORDER BY user_id ASC";
		return $this->findBySql($sql);
	}
	
	/*
	 * 用户需求状态改为已读
	 * @param int $lead_id
	 * @param int $user_id
	 * @return int
	 */
	public function update_is_read($lead_id,$user_id)
	{
		$lead_id = intval($lead_id);
		$user_id = intval($user_id);
		$where_str = "user_id={$user_id} and lead_id={$lead_id} AND is_read=0";
		
		$this->set_task_lead_tbl();
		$update_data['is_read'] = 1;
		$update_data['read_time'] = time();
		return $this->update ( $update_data, $where_str );
	}
	
	/**
	 * 更新已报价
	 * @param int $user_id
	 * @param int $request_id
	 * @param array $more_info array('quotes_time'=>0)
	 * @return boolean
	 */
	public function update_lead_status_quotes($user_id, $request_id, $more_info=array())
	{
		$user_id = intval($user_id);
		$request_id = intval($request_id);
		if( $user_id<1 || $request_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => 8,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_lead_tbl();
		$affected_rows = $this->update($data, "user_id={$user_id} AND request_id={$request_id} AND status=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新报价数量
	 * @param int $request_id
	 * @param int $quotes_num
	 * @return boolean
	 */
	public function update_lead_quotes_num($request_id, $quotes_num)
	{
		$request_id = intval($request_id);
		$quotes_num = intval($quotes_num);
		if( $request_id<1 || $quotes_num<0 )
		{
			return false;
		}
		$data = array(
			'quotes_num' => $quotes_num,
		);
		$this->set_task_lead_tbl();
		$affected_rows = $this->update($data, "request_id={$request_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新过期时间
	 * @param int $request_id
	 * @param int $expire_time
	 * @return boolean
	 */
	public function update_lead_expire_time($request_id, $expire_time)
	{
		$request_id = intval($request_id);
		$expire_time = intval($expire_time);
		if( $request_id<1 || $expire_time<0 )
		{
			return false;
		}
		$data = array(
			'expire_time' => $expire_time,
		);
		$this->set_task_lead_tbl();
		$affected_rows = $this->update($data, "request_id={$request_id}");
		return $affected_rows>0?true:false;
	}
	
	/*
	 * 删除用户需求
	 * @param int $lead_id
	 * @param int $user_id
	 * @return int 
	 */
	public function delete_user_lead($lead_id,$user_id)
	{
		$where_str = "user_id = {$user_id} and lead_id={$lead_id}";
		
		$this->set_task_lead_tbl();
		$update_data['status']=2;
		return $this->update ( $update_data, $where_str );
	}
	
	
	/*
	 * 获取一条需求
	 * @param int $lead_id
	 * @return array
	 */
	public function get_lead_info($lead_id)
	{
		$this->set_task_lead_tbl();
		$ret = $this->find ( "lead_id={$lead_id}" );
		return $ret;
	}
	
	/*
	 * 获取一条需求
	 * @param int $lead_id
	 * @return array
	 */
	public function get_lead_by_lead_id($lead_id)
	{
		$lead_info = $this->get_lead_info($lead_id);
		if(empty($lead_info))
		{
			return array();
		}
		
		$lead_list = $this->fill_lead_detail_list(array($lead_info));
		$lead_detail_info = $lead_list[0];
		if( !is_array($lead_detail_info) ) $lead_detail_info = array();
		return $lead_detail_info;
		
	}

	
	/**
	 * 手动提交推送引导
	 * @param int $request_id
	 * @param int $user_id_str 用户id,可以用逗号分开,如"100251,123652"
	 * @return array
	 */
	public function submit_lead_by_artificial($request_id,$user_id_str,$auto=false)
	{
		$result = array('result'=>0, 'message'=>'');
		
		$request_id = intval($request_id);
		if( $request_id<1 or !preg_match ("/^[\d+,]+$/", $user_id_str) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$user_id_arr = explode(',',$user_id_str);
		$user_id_arr = array_filter($user_id_arr);
		
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -2;
			$result['message'] = '没有该需求问卷';
			return $result;
		}
		$from_user_id = intval($request_info['user_id']);
		$from_nickname = get_user_nickname_by_user_id($from_user_id);
		$expire_time = intval($request_info['expire_time']);
		
		foreach($user_id_arr as $user_id)
		{
			$user_id = intval($user_id);
			if( $user_id<1 || $user_id == $from_user_id) continue;			
			$data =  array(
				'user_id' => $user_id,
				//'profile_id' => $profile_info['profile_id'],
				'request_id' => $request_id,
				'from_user_id' => $from_user_id,
				'from_nickname' => $from_nickname,
				'status' => 0,
				'expire_time' => $expire_time,
				'add_time' => time(),
			);
			$lead_id = $this->add_lead($data);
			if( $lead_id<1 ) continue;
			
			if(!$auto)//如果不是自动成单的,发送消息
			{
				//触发事件
				$trigger_params = array('lead_id'=>$lead_id);
				$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
				$task_trigger_obj->lead_submit_after($trigger_params);
			}

		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;		
	}
	
	/**
	 * 提交推送引导
	 * @param int $request_id
	 * @return array
	 */
	public function submit_lead_by_request_id($request_id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		$request_id = intval($request_id);
		if( $request_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -2;
			$result['message'] = '参数错误';
			return $result;
		}
		$service_id = intval($request_info['service_id']);
		$from_user_id = intval($request_info['user_id']);
		$from_nickname = get_user_nickname_by_user_id($from_user_id);
		$expire_time = intval($request_info['expire_time']);
		$location_id = intval($request_info['location_id']);
		if( $location_id<1 )
		{
			//无地区信息调用用户注册信息地区属性,符合业务逻辑吗?
			$user_obj = POCO::singleton('pai_user_class');
			$user_info = $user_obj->get_user_info($from_user_id);
			$location_id = intval($user_info['location_id']);
		}
		//指定推送特殊人群服务
		$where_sp = '';
		if( in_array($service_id, array(1,3)) ) //服务类型是1,3的全推给综合ID为5的用户
		{
			$where_sp = "service_id =5";
		}
		elseif($service_id == 2)//服务类型是2的全推给综合ID为6的用户
		{
			$where_sp = "service_id =6";
		}

		//不推送给自己
		$where_str = "user_id!={$from_user_id} AND ".($where_sp?"(":"")."service_id={$service_id}";
		
		//指定地区
		if( $location_id>0 )
		{
			if( strlen($where_str)>0 ) $where_str .= ' AND ';
			$where_str .= "location_id='{$location_id}'";
		}
		//指定推送特殊人群服务
		if($where_sp)
		{
			$where_str .= ' OR '.$where_sp.')';
		}

		//日志 http://yp.yueus.com/logs/201502/03_task.txt
		pai_log_class::add_log($where_str, 'submit_lead_by_request_id_where', 'task');

		$task_profile_obj = POCO::singleton('pai_task_profile_class');
		$profile_list = $task_profile_obj->get_profile_list(0, 0, false, $where_str , 'profile_id ASC', '0,9999');			
		foreach ($profile_list as $profile_info)
		{
			$data =  array(
				'user_id' => $profile_info['user_id'],
				'profile_id' => $profile_info['profile_id'],
				'request_id' => $request_id,
				'from_user_id' => $from_user_id,
				'from_nickname' => $from_nickname,
				'status' => 0,
				'expire_time' => $expire_time,
				'add_time' => time(),
			);
			$lead_id = $this->add_lead($data);
			if( $lead_id<1 ) continue;
			
			//事件触发
			$trigger_params = array('lead_id'=>$lead_id);
			$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
			$task_trigger_obj->lead_submit_after($trigger_params);
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}
	
}
