<?php
/**
 * 留言
 * @author Henry
 * @copyright 2015-04-09
 */

class pai_task_message_class extends POCO_TDG
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
	private function set_task_message_tbl()
	{
		$this->setTableName('task_message_tbl');
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_message($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_task_message_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 设置已查看
	 * @param int $message_id
	 * @param array $more_info array('read_time'=>0)
	 * @return boolean
	 */
	public function update_message_read($message_id, $more_info=array())
	{
		$message_id = intval($message_id);
		if( $message_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_read' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_message_tbl();
		$affected_rows = $this->update($data, "message_id={$message_id} AND is_read=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取信息
	 * @param int $message_id
	 * @return array
	 */
	public function get_message_info($message_id)
	{
		$message_id = intval($message_id);
		if( $message_id<1 )
		{
			return array();
		}
		$this->set_task_message_tbl();
		return $this->find("message_id={$message_id}");
	}
	
	/**
	 * 获取列表
	 * @param int $quotes_id
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_message_list($quotes_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$quotes_id = intval($quotes_id);
		
		//整理查询条件
		$sql_where = '';
		
		if( $quotes_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "quotes_id={$quotes_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_task_message_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 提交留言
	 * @param int $from_user_id
	 * @param int $quotes_id
	 * @param string $message_type 留言类型
	 * @param string $message_content
	 * @param array $more_info array('pay_amount'=>0)
	 * @return array
	 * @tutorial
	 * 留言类型
	 * message留言，quotes报价，read_quotes查看报价，read_profile查看资料，hired雇佣
	 * declined谢绝，earnest支付定金，refund_coins退还生意卡，review评价
	 */
	public function submit_message($from_user_id, $quotes_id, $message_type, $message_content='', $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'message_id'=>0);
		
		$from_user_id = intval($from_user_id);
		$quotes_id = intval($quotes_id);
		$message_type = trim($message_type);
		$message_content = trim($message_content);
		if( !is_array($more_info) ) $more_info = array();
		
		if( $from_user_id<1 || $quotes_id<1 || strlen($message_type)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取报价信息
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -2;
			$result['message'] = '参数错误';
			return $result;
		}
		$request_id = intval($quotes_info['request_id']);
		$seller_user_id = intval($quotes_info['user_id']);
		
		//获取需求信息
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -3;
			$result['message'] = '参数错误';
			return $result;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		//初始化对方用户ID
		$to_user_id = 0;
		if( $from_user_id==$seller_user_id )
		{
			$to_user_id = $buyer_user_id;
		}
		elseif( $from_user_id==$buyer_user_id )
		{
			$to_user_id = $seller_user_id;
		}
		if( $to_user_id<1 )
		{
			$result['result'] = -4;
			$result['message'] = '参数错误';
			return $result;
		}
		$from_user_nickname = get_user_nickname_by_user_id($from_user_id);
		//$to_user_nickname = get_user_nickname_by_user_id($to_user_id);
		
		$cur_time = time();
		
		$quotes_content = '';
		switch ($message_type)
		{
			case 'quotes': //报价
				$price_tmp = trim($quotes_info['price']);
				$price_str_tmp = '￥' . ((ceil($price_tmp)==$price_tmp)?$price_tmp*1:$price_tmp);
				$quotes_content = "报价估价：{$price_str_tmp}";
				break;
			case 'read_quotes': //查看报价
				$message_content = "{$from_user_nickname} 已经查看了你的报价";
				break;
			case 'read_profile': //查看资料
				$message_content = "{$from_user_nickname} 已经查看了你的个人资料";
				break;
			case 'hired': //雇佣
				$message_content = "恭喜你，{$from_user_nickname}已经确认了你的报价，快去和Ta联系吧。";
				break;
			case 'declined': //谢绝
				$message_content = "很遗憾 {$from_user_nickname}并没有选择雇佣你";
				break;
			case 'earnest': //支付定金
				$pay_amount_tmp = trim($more_info['pay_amount']);
				$message_content = "{$from_user_nickname} 已经支付了服务金，服务金金额为：￥{$pay_amount_tmp}";
				break;
			case 'review': //评价
				$cur_time_str = date('Y-m-d H:i:s', $cur_time);
				$message_content = "{$from_user_nickname}在{$cur_time_str}评价了你";
				break;
			case 'refund_coins': //退还生意卡
				$message_content = "由于{$from_user_nickname}在48小时内未查看你的报价，你的生意卡已经被退回至你的账户。";
				break;
			case 'message': //留言
				 break;
			default:
				break;
		}
		
		$data = array(
			'message_type' => $message_type,
			'quotes_id' => $quotes_id,
			'request_id' => $request_id,
			'from_user_id' => $from_user_id,
			'to_user_id' => $to_user_id,
			'quotes_content' => $quotes_content,
			'message_content' => $message_content,
			'add_time' => $cur_time,
		);
		$message_id = $this->add_message($data);
		if( $message_id<1 )
		{
			$result['result'] = -5;
			$result['message'] = '保存失败';
			return $result;
		}
		
		//是否重要，排前、加粗（卖家）
		if( $to_user_id==$seller_user_id )
		{
			$task_quotes_obj->update_quotes_important($quotes_id, 1);
		}
		
		//事件触发
		$trigger_params = array('message_id'=>$message_id);
		$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
		$task_trigger_obj->message_submit_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['message_id'] = $message_id;
		return $result;
	}
	
	/**
	 * 补充详细信息
	 * @param array $list
	 * @return array
	 */
	private function fill_message_detail_list($list)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		
		$cut_time = time();
		
		foreach($list as $key=>$info)
		{
			$message_type = trim($info['message_type']);
			$from_user_id = intval($info['from_user_id']);
			$from_user_nickname = get_user_nickname_by_user_id($from_user_id);
			$from_user_icon = get_user_icon($from_user_id);
			
			$to_user_id = intval($info['to_user_id']);
			$to_user_nickname = get_user_nickname_by_user_id($to_user_id);
			$to_user_icon = get_user_icon($to_user_id);
			
			$add_time = intval($info['add_time']);
			$add_time_str = yue_time_format_convert($add_time);
			
			$info['from_user_nickname'] = $from_user_nickname;
			$info['from_user_icon'] = $from_user_icon;
			$info['to_user_nickname'] = $to_user_nickname;
			$info['to_user_icon'] = $to_user_icon;
			$info['add_time_str'] = $add_time_str;
			$list[$key] = $info;
		}
		
		return $list;
	}
	
	/**
	 * 获取最近留言信息
	 * @param int $quotes_id
	 * @return array
	 */
	public function get_message_info_lately_by_quotes_id($quotes_id)
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return array();
		}
		$where_str = "message_type IN ('message', 'quotes')";
		$message_list = $this->get_message_list($quotes_id, false, $where_str, 'message_id DESC', '0,1');
		$message_detail_list = $this->fill_message_detail_list($message_list);
		$message_detail_info = $message_detail_list[0];
		if( !is_array($message_detail_info) ) $message_detail_info = array();
		return $message_detail_info;
	}
	
	/**
	 * 获取最近动态信息
	 * @param int $user_id
	 * @param int $quotes_id
	 * @return array
	 */
	public function get_feed_info_lately_by_user_id($user_id, $quotes_id)
	{
		$user_id = intval($user_id);
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return array();
		}
		$where_str = "message_type NOT IN ('message', 'quotes') AND to_user_id={$user_id}";
		$message_list = $this->get_message_list($quotes_id, false, $where_str, 'message_id DESC', '0,1');
		$message_detail_list = $this->fill_message_detail_list($message_list);
		$message_detail_info = $message_detail_list[0];
		if( !is_array($message_detail_info) ) $message_detail_info = array();
		return $message_detail_info;
	}
	
	/**
	 * 获取留言列表，根据用户ID
	 * @param int $user_id
	 * @param int $quotes_id
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @return array|int
	 */
	public function get_message_list_by_user_id($user_id, $quotes_id, $b_select_count=false, $order_by='message_id ASC', $limit='0,20')
	{
		$user_id = intval($user_id);
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return array();
		}
		$where_str = "(message_type IN ('message', 'quotes') OR (message_type NOT IN ('message', 'quotes') AND to_user_id={$user_id}))";
		$message_list = $this->get_message_list($quotes_id, $b_select_count, $where_str, $order_by, $limit);
		return $this->fill_message_detail_list($message_list);
	}
	
	/**
	 * 获取未查看的留言数量
	 * @param int $user_id
	 * @param int $quotes_id
	 * @return int
	 */
	public function get_message_count_unread($user_id, $quotes_id)
	{
		$user_id = intval($user_id);
		$quotes_id = intval($quotes_id);
		if( $user_id<1 || $quotes_id<1 )
		{
			return 0;
		}
		$where_str = "to_user_id={$user_id} AND is_read=0";
		return $this->get_message_list($quotes_id, true, $where_str);
	}
	
}
