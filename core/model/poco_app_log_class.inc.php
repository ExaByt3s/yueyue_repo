<?php

/**
 * 接口公共类
 *
 * @author extory
 * @date 2012-03-28
 * 
 */

class poco_app_log_class extends POCO_TDG
{
	var $xml = '';
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId(13);
		$this->setDBName('m_service_db');
		$this->setTableName('app_services_api_log_tbl');
	}

	/**
	 * 析构函数
	 * 
	 */
	public function __destruct(){}

	/**
	 * 记录日志
	 * @param $params 输入参数
	 * @param $content 调试内容
	 * @param $run_time 运行时间
	 * @param $level 调试级别
	 * @param $user_id 用户ID
	 * @param $trace 调试信息
	 * @return $string
	 */
	public function _log( $params , $content, $run_time , $level = 0 , $user_id = 0 , $trace = '')
	{
		global $login_id;
		if( empty($user_id) )
		{
			$user_id = $login_id;
		}

		$trace_info_arr = empty($trace) ? print_r(debug_backtrace(),true) : $trace;
		$log_arr = array(
						'params'	=> $params,
						'add_time'	=> date('Y-m-d H:i:s'),
						'content'	=> $content,
						'level'		=> $level,
						'user_id'	=> $user_id,
						'run_time'	=> $run_time,
						'trace'		=> $trace_info_arr
		);
		$this -> insert( $log_arr );
	}

}



?>