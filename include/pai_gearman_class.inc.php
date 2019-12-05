<?php
/**
 * gearman����ַ���
 * @author Bryan
 */

class pai_gearman_class extends POCO_TDG
{
	/**
	 * client����
	 * @var object
	 */
	private static $gearman_client_obj = null;
	
	/**
	 * worker����
	 * @var object
	 */
	private static $gearman_worker_obj = null;
	
	/**
	 * gearman�������б�
	 * @var string
	 */
	private static $gearman_server_list = array();
		
	/**
	 * redis����
	 * @var object
	 */
	private static $redis_obj = null;
	
	/**
	 * redis������IP
	 * @var string
	 */
	private static $redis_server_ip = '';
	
	/**
	 * redis�������˿�
	 * @var int
	 */
	private static $redis_server_port = 0;
	
	/**
	 * redis��������
	 * @var int
	 */
	private static $redis_expire_seconds = 25200; //7��

	/**
	 * redis��������
	 * @var int
	 */
	private static $expire_days = 7;
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		self::$gearman_server_list[] = array(
			'server_ip' => POCO_APP_PAI::ini('pai_gearman/gearman_server_ip'),
			'server_port' => POCO_APP_PAI::ini('pai_gearman/gearman_server_port'),
		);
		self::$redis_server_ip = POCO_APP_PAI::ini('pai_gearman/redis_server_ip');
		self::$redis_server_port = POCO_APP_PAI::ini('pai_gearman/redis_server_port');
	}
	
	/**
	 * ��ȡclient����
	 * @return object
	 */
	private function get_gearman_client_obj()
	{
		//����Ƿ���ʵ����
		if( !is_null(self::$gearman_client_obj) )
		{
			return self::$gearman_client_obj;
		}
		
		//������Ƿ����
		if( !class_exists('GearmanClient') )
		{
			return null;
		}
		
		//ʵ��������
		$client_obj = new GearmanClient();
		foreach(self::$gearman_server_list as $server_info)
		{
			$client_obj->addServer($server_info['server_ip'], $server_info['server_port']);
		}
		$client_obj->setTimeout(5000); // ���ó�ʱ
		self::$gearman_client_obj = $client_obj;
		
		return self::$gearman_client_obj;
	}
	
	/**
	 * ��ȡworker����
	 * @return object
	 */
	public function get_gearman_worker_obj()
	{
		//����Ƿ���ʵ����
		if( !is_null(self::$gearman_worker_obj) )
		{
			return self::$gearman_worker_obj;
		}
		
		//������Ƿ����
		if( !class_exists('GearmanWorker') )
		{
			return null;
		}
		
		//ʵ��������
		$worker_obj = new GearmanWorker();
		$worker_obj->addOptions(GEARMAN_WORKER_NON_BLOCKING);
		foreach(self::$gearman_server_list as $server_info)
		{
			$worker_obj->addServer($server_info['server_ip'], $server_info['server_port']);
		}
		self::$gearman_worker_obj = $worker_obj;
		
		return self::$gearman_worker_obj;
	}
	
	/**
	 * ��ȡredis����
	 * @return object
	 */
	public function get_redis_obj()
	{
		//����Ƿ���ʵ����
		if( !is_null(self::$redis_obj) )
		{
			return self::$redis_obj;
		}
		
		//������Ƿ����
		if( !class_exists('redis') )
		{
			return null;
		}
		
		//ʵ��������
		$redis_obj = new redis();
		if( !$redis_obj->connect(self::$redis_server_ip, self::$redis_server_port) )
		{
            return null;
        }
		
		self::$redis_obj = $redis_obj;
		return self::$redis_obj;
	}
	
	/**
	 * ����
	 * @param string $cmd_str
	 * @return int ������ʾ����
	 */
	private function add_task($cmd_str)
	{
		//������
		$cmd_str = trim($cmd_str);
		if( strlen($cmd_str)<1 )
		{
			return -1;
		}
		
		//��ȡredis����
		$redis_obj = self::get_redis_obj();
		if( is_null($redis_obj) )
		{
			return -2;
		}
		
		//��ȡ����ID
		$task_id = $redis_obj->incr('TaskID'); // task_id��Сֵ��1
		if( $task_id<1 )
		{
			return -3;
		}
		
		//����
		$task_key = "Task:{$task_id}";
		$date_time = date('Ymd');
		$task_set_key = "Task:Set:{$date_time}";
		$rst = $redis_obj->multi()
			->hMset($task_key, array('task_id' => $task_id, 'command' => $cmd_str))
			->zadd($task_set_key, time(), $task_key)
			->expire($task_set_key, self::$redis_expire_seconds)
			->exec();
		if( $rst[0]!==true )
		{
			return -4;
		}
		
		return $task_id;
	}
	
	/**
	 * �ɹ�
	 * @param int $task_id
	 * @param int $code
	 * @param string $errmsg
	 * @return int
	 */
	private function success_task($task_id, $code, $errmsg)
	{
		$task_key = "Task:$task_id";
		
		//��ȡredis����
		$redis_obj = self::get_redis_obj();
		if( is_null($redis_obj) )
		{
			return -2;
		}
		
		//����
        $rc = $redis_obj->multi()
			->hMset($task_key, array('status' => $code, 'message' => $errmsg))
			->expire($task_key, self::$redis_expire_seconds)
			->exec();
        if( $rc[0]!==true )
		{
			return -4;
		}
		
		return 1;
	}
	
	/**
	 * ʧ��
	 * @param int $task_id
	 * @param int $code
	 * @param string $errmsg
	 * @return int
	 */
	private function fail_task($task_id, $code, $errmsg)
	{
		$task_key = "Task:$task_id";
		$date_time = date('Ymd');
		$task_failset_key = "Task:FailSet:$date_time";
		
		//��ȡredis����
		$redis_obj = self::get_redis_obj();
		if( is_null($redis_obj) )
		{
			return -2;
		}
		
		//����
		$rc = $redis_obj->multi()
			->hMset($task_key, array('status' => $code, 'message' => $errmsg))
			->expire($task_key, self::$redis_expire_seconds)
			->zadd($task_failset_key, time(), $task_key)
			->expire($task_failset_key, self::$redis_expire_seconds)
			->exec();
		if( $rc[0]!==true )
		{
			return -4;
		}
		
		return 1;
	}
	
	/**
	 * ��ȡtask�б�
	 * @return array|int
	 */
	public function get_task_all_list()
	{
		$tm = time();
		$start_tm = $tm - self::$expire_days * 86400;

		//��ȡredis����
		$redis_obj = self::get_redis_obj();
		if( is_null($redis_obj) )
		{
			return -2;
		}
		
		$r_mul = $redis_obj->multi();
		for( $i=self::$expire_days; $i>= 0; --$i )
		{
			$date_time = date('Ymd', $tm-$i*86400);
			$task_set_key = "Task:Set:$date_time";
			$r_mul->zRangeByScore($task_set_key, $start_tm, '+inf');
		}
		$rc = $r_mul->exec();
	
		$r_mul = $redis_obj->multi();
		foreach( $rc as $key_set_arr )
		{
			foreach( $key_set_arr as $key )
			{
				$r_mul->hgetall($key);
			}
		}
		$rc = $r_mul->exec();
		//var_dump($rc);
	
		$res = array();
		foreach( $rc as $task )
		{
			array_push($res, json_encode($task));
		}
		return $res;
	}

	/**
	 * ��ȡʧ���б����ԣ�
	 * @return array
	 */
	public function get_task_fail_list()
	{
		$tm = time();
		$start_tm = $tm - self::$expire_days * 86400;
	
		//��ȡredis����
		$redis_obj = self::get_redis_obj();
		if( is_null($redis_obj) )
		{
			return array();
		}

		$r_mul = $redis_obj->multi();
		for( $i=self::$expire_days; $i>=0; --$i )
		{
			$date_time = date('Ymd', $tm - $i * 86400);
			$task_set_key = "Task:FailSet:$date_time";
			$r_mul->zRangeByScore($task_set_key, $start_tm, '+inf');
		}
		$rc = $r_mul->exec();
		//var_dump($rc);
	
		$r_mul = $redis_obj->multi();
		foreach( $rc as $key_set_arr )
		{
			foreach( $key_set_arr as $key )
			{
				$r_mul->hgetall($key);
			}
		}
		$rc = $r_mul->exec();
		//var_dump($rc);
	
		$res = array();
		foreach( $rc as $task )
		{
			array_push($res, json_encode($task));
		}
		return $res;
	}
	
	/**
	 * ��������
	 * @param string $cmd_type ��������
	 * @param array $cmd_params �������
	 * @return array array('result'=>'', 'message'=>'', 'task_id'=>0)
	 */
	public function send_cmd($cmd_type, $cmd_params)
	{
		$send_rst = self::_send_cmd($cmd_type, $cmd_params);
		if( $send_rst['result']!=1 )
		{
			$log_arr = array(
				'cmd_type' => $cmd_type,
				'cmd_params' => $cmd_params,
				'send_rst' => $send_rst,
			);
			pai_log_class::add_log($log_arr, 'send_cmd', 'gearman_error');
		}
		return $send_rst;
	}
	
	/**
	 * ��������
	 * @param string $cmd_type ��������
	 * @param array $cmd_params ������� array('p1'=>'', 'p2'=>'', ... )
	 * @return array array('result'=>'', 'message'=>'', 'task_id'=>0)
	 */
	private function _send_cmd($cmd_type, $cmd_params)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$cmd_type = trim($cmd_type);
		if( strlen($cmd_type)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($cmd_params) ) $cmd_params = array();
		
		//��ȡclient����
		$client_obj = self::get_gearman_client_obj();
		if( is_null($client_obj) )
		{
			$result['result'] = -2;
			$result['message'] = '��ȡGearmanClient����ʧ��';
			return $result;
		}
		
		//����
		$cmd_arr = array (
			'cmd_type' => $cmd_type,
			'cmd_params' => $cmd_params,
		);
		$task_id = self::add_task(json_encode($cmd_arr));
		if( $task_id<0 )
		{
			$result['result'] = -3;
			$result['message'] = 'add_taskʧ��';
			return $result;
		}
		
		//�첽����
		$data = array (
			'task_id' => $task_id,
			'cmd_type' => $cmd_type,
			'cmd_params' => $cmd_params,
		);
		$job_handle = $client_obj->doBackground('receive_cmd', json_encode($data));
		if( $client_obj->returnCode()!=GEARMAN_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = 'doBackgroundʧ��';
			$result['task_id'] = $task_id;
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['task_id'] = $task_id;
		return $result;
	}
	
	/**
	 * ��������
	 * @param object $job_obj
	 * @return array array('result'=>'', 'message'=>'')
	 */
	public function receive_cmd($job_obj)
	{
		$receive_rst = self::_receive_cmd($job_obj);
		if( $receive_rst['result']!=1 )
		{
			$log_arr = array(
				'job_obj' => $job_obj,
				'receive_rst' => $receive_rst,
			);
			pai_log_class::add_log($log_arr, 'receive_cmd', 'gearman_error');
		}
		return $receive_rst;
	}
	
	/**
	 * ��������
	 * @param object $job_obj
	 * @return array array('result'=>'', 'message'=>'')
	 */
	public function _receive_cmd($job_obj)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$params_str = $job_obj->workload();
		$params = json_decode($params_str, true);
		if( !is_array($params) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$task_id = intval($params['task_id']);
		$cmd_type = trim($params['cmd_type']);
		if( $task_id<0 || strlen($cmd_type)<1 )
		{
			$result['result'] = -2;
			$result['message'] = 'task_id��cmd_type����';
			return $result;
		}
		$cmd_params = $params['cmd_params'];
		if( !is_array($cmd_params) ) $cmd_params = array();
		
		//��鷽���Ƿ����
		$method_name = 'exec_cmd_' . $cmd_type;
		if( !method_exists(__CLASS__, $method_name) )
		{
			self::fail_task($task_id, 1, "method {$method_name} not exists.");
			
			$result['result'] = -3;
			$result['message'] = "����{$method_name}������";
			return $result;
		}
		
		//��־
		$log_arr = array(
			'params' => $params,
			'method_name' => $method_name,
			'cmd_params' => $cmd_params,
		);
		pai_log_class::add_log($log_arr, '_receive_cmd call_user_func begin', 'gearman_info');
		
		$rst = call_user_func(array(__CLASS__, $method_name), $task_id, $cmd_type, $cmd_params); //ִ������
		
		//��־
		$log_arr = array(
			'method_name' => $method_name,
			'cmd_params' => $cmd_params,
			'rst' => $rst,
		);
		pai_log_class::add_log($log_arr, '_receive_cmd call_user_func end', 'gearman_info');
		
		self::success_task($task_id, 0, 'success.');
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * ԼԼ_�̳�_����_�ύ_��
	 * @param int $task_id
	 * @param string $cmd_type
	 * @param array $cmd_params
	 * @return boolean
	 */
	public static function exec_cmd_pai_mall_order_submit_after($task_id, $cmd_type, $cmd_params)
	{
		$trigger_params = array(
			'order_sn' => $cmd_params['order_sn'],
		);
		
		$log_arr = array(
			'task_id' => $task_id,
			'cmd_type'=> $cmd_type,
			'cmd_params' => $cmd_params,
			'job_obj' => '�ɹ�11',
		);
		pai_log_class::add_log($log_arr, 'exec_cmd_pai_mall_order_submit_after', 'gearman_order');
		
		//POCO::singleton('pai_mall_trigger_class')->submit_order_after($trigger_params);
		
		return true;
	}
	
	/**
	 * ԼԼ_�̳�_����_֧��_��
	 * @param int $task_id
	 * @param string $cmd_type
	 * @param array $cmd_params
	 * @return boolean
	 */
	public static function exec_cmd_pai_mall_order_pay_after($task_id, $cmd_type, $cmd_params)
	{
		$log_arr = array(
			'task_id' => $task_id,
			'cmd_type'=> $cmd_type,
			'cmd_params' => $cmd_params,
			'job_obj' => '�ɹ�22',
		);
		pai_log_class::add_log($log_arr, 'gearman_exec_cmd_pai_mall_order_pay_after', 'gearman_order');
		
		return true;
	}
	
	/**
	 * ��Ʒͬ���ϼ�ʱ��
	 * @param int $task_id
	 * @param string $cmd_type
	 * @param array $cmd_params
	 * @return boolean
	 */
	public static function exec_cmd_pai_mall_follow_user_addtime($task_id, $cmd_type, $cmd_params)
	{
		$user_id = $cmd_params['user_id'];
			
		$obj = POCO::singleton('pai_mall_follow_user_class');
		$re1 = $obj->update_seller_type_id($user_id);
		
		$log_arr = array(
			'task_id' => $task_id,
			'cmd_type'=> $cmd_type,
			'cmd_params' => $cmd_params,
			'job_obj1' => $re1,
			'job_obj2' => $re2,
			'date'=> date('Y-m-d H:i:s'),
			'time'=> time(),
			'user_id'=> $user_id,
		);
		pai_log_class::add_log($log_arr, 'exec_cmd_pai_mall_follow_user_addtime', 'follow_user');		
		return true;
	}
	
	/**
	 * ��Ʒͬ���ϼ�ʱ��
	 * @param int $task_id
	 * @param string $cmd_type
	 * @param array $cmd_params
	 * @return boolean
	 */
	public static function exec_cmd_pai_mall_follow_user_showtime($task_id, $cmd_type, $cmd_params)
	{
		$user_id = $cmd_params['user_id'];
		$goods_id = $cmd_params['goods_id'];
			
		$obj = POCO::singleton('pai_mall_follow_user_class');
		$re1 = $obj->update_seller_last_update_time($user_id);
		$re2 = $obj->update_goods_last_update_time($goods_id);
		
		$log_arr = array(
			'task_id' => $task_id,
			'cmd_type'=> $cmd_type,
			'cmd_params' => $cmd_params,
			'job_obj1' => $re1,
			'job_obj2' => $re2,
			'date'=> date('Y-m-d H:i:s'),
			'time'=> time(),
			'user_id'=> $user_id,
			'goods_id'=> $goods_id,
		);
		pai_log_class::add_log($log_arr, 'exec_cmd_pai_mall_follow_user_showtime', 'follow_user');		
		return true;
	}
	
	/**
	 * ��Ʒͬ��
	 * @param int $task_id
	 * @param string $cmd_type
	 * @param array $cmd_params
	 * @return boolean
	 */
	public static function exec_cmd_pai_mall_synchronous_goods($task_id, $cmd_type, $cmd_params)
	{
		$goods_id = $cmd_params['goods_id'];
		$type_id = $cmd_params['type_id'];
		$type = $cmd_params['type'];
		
		$obj = POCO::singleton('pai_mall_goods_class');
		$re = $obj->add_goods_update_log($goods_id,$type_id,$type);
		if($type_id == 42)
		{
			$obj->synchronous_poco_event($goods_id);
		}
		
		$log_arr = array(
			'task_id' => $task_id,
			'cmd_type'=> $cmd_type,
			'cmd_params' => $cmd_params,
			'job_obj' => $re,
			'date'=> date('Y-m-d H:i:s'),
			'time'=> time(),
			'cmd_type'=> $cmd_type,
			'goods_id'=> $goods_id,
			'type_id'=> $type_id,
			'type'=> $type,
		);
		pai_log_class::add_log($log_arr, 'exec_cmd_pai_mall_synchronous_goods', 'gearman_goods');		
		return true;
	}
}
