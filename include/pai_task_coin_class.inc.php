<?php
/**
 * 生意卡
 * @author Henry
 * @copyright 2015-04-09
 */

class pai_task_coin_class extends POCO_TDG
{
	private $give_code_list = array(
		
		//测试
		'SELLER_TEST' => array(
			'subject' => '赠送生意卡-测试',
			'package' => array( 'coins'=>100 ),
		),
		
		//商家新注册
		'SELLER_REG' => array(
			'subject' => '赠送生意卡-新注册',
			'package' => array( 'coins'=>20 ),
		),
		
		//商家每天登录
		'SELLER_LOGIN_TODAY' => array(
			'subject' => '赠送生意卡-每天登录',
			'package' => array( 'coins'=>4 ),
		),
		
		//red特批赠送
		'SELLER_Y2015M05D21' => array(
			'subject' => '赠送生意卡',
			'package' => array( 'coins'=>50 ),
		),
		
	);
	
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
	private function set_task_coin_tbl()
	{
		$this->setTableName('task_coin_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_task_coin_balance_tbl()
	{
		$this->setTableName('task_coin_balance_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_task_coin_buy_tbl()
	{
		$this->setTableName('task_coin_buy_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_task_coin_give_tbl()
	{
		$this->setTableName('task_coin_give_tbl');
	}
	
	/**
	 * 新增
	 * @param array $data
	 * @return boolean
	 */
	private function add_coin($data)
	{
		if( empty($data) )
		{
			return false;
		}
		$this->set_task_coin_tbl();
		$this->insert($data, 'IGNORE');
		return true;
	}
	
	/**
	 * 新增
	 * @param array $data
	 * @return int
	 */
	private function add_buy($data)
	{
		if( empty($data) )
		{
			return 0;
		}
		$this->set_task_coin_buy_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 修改
	 * @param array $data
	 * @param int $buy_id
	 * @return bool
	 */
	private function update_buy($data, $buy_id)
	{
		$buy_id = intval($buy_id);
		if( !is_array($data) || empty($data) || $buy_id<1 )
		{
			return false;
		}
		$this->set_task_coin_buy_tbl();
		$this->update($data, "buy_id={$buy_id}");
		return true;
	}
	
	/**
	 * 更新已支付
	 * @param int $buy_id
	 * @param array $more_info array('buy_time'=>0)
	 * @return boolean
	 */
	private function update_buy_paid($buy_id, $more_info=array())
	{
		$buy_id = intval($buy_id);
		if( $buy_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => 8,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_coin_buy_tbl();
		$affected_rows = $this->update($data, "buy_id={$buy_id} AND status=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取
	 * @param int $buy_id
	 * @return array
	 */
	public function get_buy_info($buy_id)
	{
		//检查参数
		$buy_id = intval($buy_id);
		if( $buy_id<1 )
		{
			return array();
		}
		//获取
		$this->set_task_coin_buy_tbl();
		return $this->find("buy_id='{$buy_id}'");
	}
	
	/**
	 * 累积生意卡
	 * @param int $user_id
	 * @param double $coins
	 * @return bool
	 */
	private function margin_coin_balance($user_id, $coins)
	{
		$user_id = intval($user_id);
		$coins = number_format($coins*1, 2, '.', '')*1;
		if( $user_id<1 || $coins==0 )
		{
			return false;
		}
		$where_str = '';
		if( $coins<0 )
		{
			$where_str = ' AND balance>=' . abs($coins);
		}
		$this->set_task_coin_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET balance=balance+{$coins} WHERE user_id={$user_id}{$where_str}");
		$affected_rows = $this->get_affected_rows();
		if( $affected_rows<1 )
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 获取
	 * @param int $user_id
	 * @return array
	 */
	public function get_coin_info($user_id)
	{
		//检查参数
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return array();
		}
		//获取
		$this->set_task_coin_tbl();
		$info = $this->find("user_id='{$user_id}'");
		
		//自动初始化
		if( empty($info) )
		{
			$data = array(
				'user_id' => $user_id,
			);
			$this->add_coin($data);
			
			//获取
			$this->set_task_coin_tbl();
			$info = $this->find("user_id='{$user_id}'");
		}
		
		return $info;
	}
	
	/**
	 * 新增
	 * @param array $data
	 * @return int
	 */
	private function add_coin_balance($data)
	{
		if( empty($data) )
		{
			return false;
		}
		$this->set_task_coin_balance_tbl();
		return $this->insert($data);
	}
	
	/**
	 * 新增
	 * @param array $data
	 * @return int
	 */
	private function add_give($data)
	{
		if( empty($data) )
		{
			return 0;
		}
		$this->set_task_coin_give_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 更新已发放
	 * @param int $give_id
	 * @param array $more_info array('lately_time'=>0)
	 * @return boolean
	 */
	private function update_give_status($give_id, $more_info=array())
	{
		$give_id = intval($give_id);
		if( $give_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_coin_give_tbl();
		$affected_rows = $this->update($data, "give_id={$give_id} AND status=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新不发放
	 * @param int $give_id
	 * @param array $more_info array('lately_time'=>0)
	 * @return boolean
	 */
	private function update_ungive_status($give_id, $more_info=array())
	{
		$give_id = intval($give_id);
		if( $give_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'status' => 2,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_coin_give_tbl();
		$affected_rows = $this->update($data, "give_id={$give_id} AND status=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取
	 * @param string $give_code
	 * @param int $user_id
	 * @param int $ref_id
	 * @return array
	 */
	private function get_give_info($give_code, $user_id, $ref_id)
	{
		$give_code = trim($give_code);
		$user_id = intval($user_id);
		$ref_id = intval($ref_id);
		if( strlen($give_code)<1 || $user_id<1 )
		{
			return array();
		}
		$where_str = "give_code=:x_give_code AND user_id={$user_id} AND ref_id={$ref_id}";
		sqlSetParam($where_str, 'x_give_code', $give_code);
		$this->set_task_coin_give_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 获取列表
	 * @param int $status -1表示不限制
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_give_list($status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$status = intval($status);
		
		//整理查询条件
		$sql_where = '';
		
		if( $status>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "status={$status}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_task_coin_give_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 累积生意卡
	 * @param int $user_id
	 * @param double $coins
	 * @param string $subject
	 * @param array $more_info array('reason_type'=>'', 'reason_rid'=>0, 'remark'=>'', 'add_time'=>0)
	 * @return array
	 */
	public function margin_balance($user_id, $coins, $subject, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'');
		
		$user_id = intval($user_id);
		$coins = number_format($coins*1, 2, '.', '')*1;
		$subject = trim($subject);
		if( !is_array($more_info) ) $more_info = array();
		if( $user_id<1 || $coins==0 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$reason_type = trim($more_info['reason_type']);
		$reason_rid = trim($more_info['reason_rid']);
		$remark = trim($more_info['remark']);
		$add_time = intval($more_info['add_time']);
		if( $add_time<1 ) $add_time = time();
		
		$coin_info = $this->get_coin_info($user_id);
		if( empty($coin_info) )
		{
			$result['result'] = -2;
			$result['message'] = '参数错误';
			return $result;
		}
		$balance = $coin_info['balance'] + $coins;
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		$ret = $this->margin_coin_balance($user_id, $coins);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -3;
			$result['message'] = '失败';
			return $result;
		}
		
		$data = array(
			'user_id' => $user_id,
			'coins' => $coins,
			'balance' => $balance,
			'reason_type' => $reason_type,
			'reason_rid' => $reason_rid,
			'subject' => $subject,
			'remark' => $remark,
			'add_time' => $add_time,
		);
		$id = $this->add_coin_balance($data);
		if( $id<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}
	
	/**
	 * 提交购买
	 * @param int $user_id
	 * @param double $amount
	 * @param double $coins
	 * @param string $subject
	 * @param int $quotes_id
	 * @param array $more_info array('remark'=>'')
	 * @return array array('result'=>0, 'message'=>'', 'buy_id'=>0)
	 */
	public function submit_buy($user_id, $amount, $coins, $subject, $quotes_id=0, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'buy_id'=>0);
		
		$user_id = intval($user_id);
		$amount = number_format($amount*1, 2, '.', '')*1;
		$coins = number_format($coins*1, 2, '.', '')*1;
		$subject = trim($subject);
		$quotes_id = intval($quotes_id);
		if( !is_array($more_info) ) $more_info = array();
		if( $user_id<1 || $coins<=0 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$remark = trim($more_info['remark']);
		
		$data = array(
			'user_id' => $user_id,
			'subject' => $subject,
			'amount' => $amount,
			'coins' => $coins,
			'quotes_id' => $quotes_id,
			'remark' => $remark,
			'status' => 0,
			'add_time' => time(),
		);
		$buy_id = $this->add_buy($data);
		if( $buy_id<1 )
		{
			$result['result'] = -2;
			$result['message'] = '保存失败';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['buy_id'] = $buy_id;
		return $result;
	}
	
	/**
	 * 提交支付
	 * @param int $buy_id
	 * @param double $available_balance 页面当前余额
	 * @param int $is_available_balance 是否使用余额，0否 1是
	 * @param string $third_code 支付方式 alipay，当用户使用余额全额支付时可为空
	 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
	 * @param string $notify_url
	 * @return array
	 */
	public function submit_pay_buy($buy_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url='')
	{
		$result = array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'');
		
		$buy_id = intval($buy_id);
		$available_balance = number_format($available_balance*1, 2, '.', '')*1;
		$is_available_balance = intval($is_available_balance);
		$third_code = trim($third_code);
		$redirect_url = trim($redirect_url);
		$notify_url = trim($notify_url);
		
		//检查参数
		if( $buy_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取购买信息
		$buy_info = $this->get_buy_info($buy_id);
		if( empty($buy_info) )
		{
			$result['result'] = -2;
			$result['message'] = '参数错误';
			return $result;
		}
		$status = intval($buy_info['status']);
		if( $status!=0 )
		{
			$result['result'] = -3;
			$result['message'] = '状态错误';
			return $result;
		}
		$amount = $buy_info['amount']*1;
		
		$payment_info = array(
			'third_code' => $third_code, //第三方支付方式
			'subject' => '约约-生意卡购买', //商品名称
			'body' => '', //商品描述
			'amount' => $amount, //支付金额
			'channel_return' => $redirect_url,
			'channel_notify' => $notify_url,
			'channel_merchant' => '',
		);
		$pai_payment_obj = POCO::singleton('pai_payment_class');
		$payment_ret = $pai_payment_obj->submit_payment('coin_buy', $buy_id, $payment_info);
		if( $payment_ret['error']!==0 )
		{
			$result['result'] = -4;
			$result['message'] = $payment_ret['message'];
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['payment_no'] = $payment_ret['payment_no'];
		$result['request_data'] = $payment_ret['request_data'];
		return $result;
	}
	
	/**
	 * 购买生意卡
	 * @param array $buy_info
	 * @return boolean
	 */
	public function pay_buy($buy_info)
	{
		if( !is_array($buy_info) || empty($buy_info) )
		{
			return false;
		}
		$buy_id = intval($buy_info['buy_id']);
		$user_id = intval($buy_info['user_id']);
		$coins = $buy_info['coins']*1;
		$subject = trim($buy_info['subject']);
		$remark = trim($buy_info['remark']);
		if( $buy_id<1 || $user_id<1 || $coins<=0 )
		{
			return false;
		}
		
		$cur_time = time();
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		$more_info = array('buy_time'=>$cur_time);
		$ret = $this->update_buy_paid($buy_id, $more_info);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			return false;
		}
		
		//充值生意卡
		$more_info = array('reason_type'=>'coin_buy', 'reason_rid'=>$buy_id, 'remark'=>$remark, 'add_time'=>$cur_time);
		$margin_ret = $this->margin_balance($user_id, $coins, $subject, $more_info);
		if( $margin_ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			return false;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		return true;
	}
	
	/**
	 * 购买生意卡，根据支付信息
	 * @param array $payment_info
	 * @return array
	 */
	public function pay_buy_by_payment_info($payment_info)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		if( !is_array($payment_info) || empty($payment_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		$channel_module = trim($payment_info['channel_module']);
		$payment_status = intval($payment_info['status']);
		if( $channel_module!='coin_buy' || $payment_status!=8 )
		{
			$result['result'] = -2;
			$result['message'] = '支付错误';
			return $result;
		}
		$payment_no = trim($payment_info['payment_no']);
		$buy_id = intval($payment_info['channel_rid']); //购买ID
		$third_total_fee = $payment_info['third_total_fee']*1; //实收金额
		
		//获取购买信息
		$buy_info = $this->get_buy_info($buy_id);
		if( empty($buy_info) )
		{
			$result['result'] = -4;
			$result['message'] = '购买为空';
			return $result;
		}
		
		//已充值
		if( $buy_info['status']==8 )
		{
			if( $payment_no==$buy_info['payment_no'] )
			{
				$result['result'] = 1;
				$result['message'] = '成功';
				return $result;
			}
			else
			{
				$result['result'] = -5;
				$result['message'] = '重复支付';
				return $result;
			}
		}
		if( $buy_info['status']!=0 )
		{
			$result['result'] = -6;
			$result['message'] = '状态错误';
			return $result;
		}
		
		//检查金额
		if($third_total_fee<=0 || $buy_info['amount']!=$third_total_fee)
		{
			$result['result'] = -7;
			$result['message'] = '金额错误';
			return $result;
		}
		
		//购买
		$ret = $this->pay_buy($buy_info);
		if( !$ret )
		{
			$result['result'] = -8;
			$result['message'] = '购买错误';
			return $result;
		}
		
		$this->update_buy(array('payment_no'=>$payment_no), $buy_id);
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}
	
	/**
	 * 提交发放
	 * @param string $give_code 发放标识
	 * @param int $user_id 用户ID
	 * @param int $ref_id 关联ID，用于处理一个用户可以发放多次
	 * @param array $more_info
	 * @return int
	 */
	public function submit_give($give_code, $user_id, $ref_id, $more_info=array())
	{
		$give_code = trim($give_code);
		$user_id = intval($user_id);
		$ref_id = intval($ref_id);
		if( !is_array($more_info ) ) $more_info = array();
		if( strlen($give_code)<1 || $user_id<1 || $ref_id<0 )
		{
			return 0;
		}
		
		//发放标识未配置
		if( !array_key_exists($give_code, $this->give_code_list) )
		{
			return 0;
		}
		
		//检查是否已存在
		$give_info = $this->get_give_info($give_code, $user_id, $ref_id);
		if( !empty($give_info) )
		{
			return 0;
		}
		
		//保存入库
		$data = array(
			'give_code' => $give_code,
			'user_id' => $user_id,
			'ref_id' => $ref_id,
			'add_time' => time(),
		);
		$give_id = $this->add_give($data);
		
		//日志 http://yp.yueus.com/logs/201504/03_task_coin.txt
		pai_log_class::add_log(array('data'=>$data, 'give_id'=>$give_id), 'submit_give', 'task_coin');
		
		return $give_id;
	}
	
	/**
	 * 发放
	 * @param array $give_info
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function give_by_info($give_info)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		if( !is_array($give_info) || empty($give_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$give_id = intval($give_info['give_id']);
		$give_code = trim($give_info['give_code']);
		$user_id = intval($give_info['user_id']);
		$ref_id = intval($give_info['ref_id']);
		$status = intval($give_info['status']);
		
		//判断状态
		if( $status!=0 )
		{
			$result['result'] = -2;
			$result['message'] = '状态错误';
			return $result;
		}
		
		//发放标识未配置
		if( !array_key_exists($give_code, $this->give_code_list) )
		{
			$result['result'] = -3;
			$result['message'] = '发放标识未配置';
			return $result;
		}
		
		//获取配置信息
		$give_code_info = $this->give_code_list[$give_code];
		if( !is_array($give_code_info) ) $give_code_info = array();
		$subject = trim($give_code_info['subject']);
		$package = $give_code_info['package'];
		if( !is_array($package) ) $package = array();
		$coins = $package['coins']*1;
		$message = $give_code_info['message'];
		if( !is_array($message) ) $message = array();
		if( empty($give_code_info) || empty($package) || $coins<=0 )
		{
			$result['result'] = -4;
			$result['message'] = '发放标识配置错误';
			return $result;
		}
		
		$cur_time = time();
		
		//获取卖家信息
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$seller_info = $task_seller_obj->get_seller_info($user_id);
		if( empty($seller_info) || $user_id<1 )
		{
			//$more_info = array('lately_time'=>$cur_time);
			//$this->update_ungive_status($give_id, $more_info);
			
			$result['result'] = -5;
			$result['message'] = '卖家不存在';
			return $result;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		$more_info = array('lately_time'=>$cur_time, 'coins'=>$coins);
		$ret = $this->update_give_status($give_id, $more_info);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '状态失败';
			return $result;
		}
		
		//提交购买信息
		$remark = "give_id={$give_id}";
		$submit_ret = $this->submit_buy($user_id, 0, $coins, $subject, 0, array('remark'=>$remark));
		if( $submit_ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -7;
			$result['message'] = '发放失败';
			return $result;
		}
		
		//获取购买信息
		$buy_info = $this->get_buy_info($submit_ret['buy_id']);
		if( empty($buy_info) )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -8;
			$result['message'] = '发放失败';
			return $result;
		}
		
		//支付购买信息
		$ret = $this->pay_buy($buy_info);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -9;
			$result['message'] = '发放失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}

}
