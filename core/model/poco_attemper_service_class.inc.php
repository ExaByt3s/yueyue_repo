<?
/**
 * 调度队列服务器接口
 * 
 * @author Tony
 */

 
class poco_attemper_service_class
{
	var $server_conf = array(
	0=>array("ip"=>"172.18.5.13","port"=>9933),		//正常 172.18.5.13 port:9933
	1=>array("ip"=>"172.18.5.13","port"=>9934),		//推广pa 172.18.5.13 port:9934
	2=>array("ip"=>"172.18.5.13","port"=>9930),	//做缩略图用的
	//2=>array("ip"=>"172.18.5.15","port"=>9930),
	3=>array("ip"=>"172.18.5.13","port"=>9936),	//图片改名调度，高优先级
	4=>array("ip"=>"172.18.5.13","port"=>9937),	//图片改名调度，普通先级
	5=>array("ip"=>"172.18.5.15","port"=>9934),	////推广pa 172.18.5.15 port:9934
	6=>array("ip"=>"113.107.204.201","port"=>9937),	////RM慢处理删图 121.9.211.201 9937
	7=>array("ip"=>"172.18.5.13","port"=>9940), //POCO图讯生成队列
	8=>array("ip"=>"172.18.5.15","port"=>9941), //poco窝慢调度
	9=>array("ip"=>"172.18.5.15","port"=>9942), //poco窝快调度
	);
	
	var $server_host = "";
	var $server_port = "";
	var $time_out = "";

	var $sock_result = "";

	var $fp = null;

	var $last_err = "";

	function poco_attemper_service_class($server_id=0,$time_out=30 )
	{
		if (!empty($this->server_conf[$server_id]))
		{
			$server_host = 	$this->server_conf[$server_id]["ip"];
			$server_port = 	$this->server_conf[$server_id]["port"];
		}
		else
		{
			$server_host = 	$this->server_conf[0]["ip"];
			$server_port = 	$this->server_conf[0]["port"];
		}
		
		$this->server_host= $server_host;
		$this->server_port= $server_port*1;
		$this->time_out= $time_out*1;
	}
	
	/**
	* 调试输出如果
	* @access private
	*/
	function _trace($var,$title="")
	{
		global $_debug;
		if (empty($_debug))
		{
			$_debug = $_REQUEST["_debug"];
		}

		if ($_debug)
		{
			echo "【_trace ".__CLASS__." ".$title."：";
			var_dump($var);
			echo "】<br />\r\n";
		}
	}

	function _connect()
	{
		@fclose($this->fp);
		$this->fp = @fsockopen($this->server_host,$this->server_port,$errno, $errstr, $this->time_out);
		if (!$this->fp)
		{
			$this->last_err = "打开sock错误：$errstr ($errno)<br>\n";
			return false;
		}

		return true;
	}


	function _send_sock($request_str)
	{
		if ($this->_connect())
		{
			if (!fwrite($this->fp,$request_str))
			{
				$this->last_err = "发送指令失败".__CLASS__."::".__FUNCTION__;
				return false;
			}

			$this->sock_result = "";

			while(!feof($this->fp))
			{
				$this->sock_result.= fgets($this->fp,128);
			}

			@fclose($this->fp);
			
			$this->_trace(__FUNCTION__." send:{$request_str}");
			return $this->sock_result;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 抓取一个网页并输出
	 *
	 * @param string $remote_file_url
	 * @param int $delay_run_time
	 * @param boolean $b_save_log
	 * @param boolean $b_ignore_err
	 * @param array $cookie_arr 		cookie数组
	 * @param int $max_thread_num 		本任务的最大线程数
	 * @param int $parent_task_id		父任务id
	 * @return int
	 */
	function add_task_remote_app($remote_file_url,$delay_run_time=0,$b_save_log=true,$b_ignore_err=false,$cookie_arr=null,$max_thread_num=0,$parent_task_id=0)
	{

		if (!preg_match("|^http://|",$remote_file_url))
		{
			$remote_file_url="http://".$remote_file_url;
		}
		
		//cookie
		if (!isset($cookie_arr))
		{
			$cookie_arr = $_COOKIE;
		}
		
		if (!empty($cookie_arr) && is_array($cookie_arr))
		{
			$cookie_header_str = "";
			foreach ($cookie_arr as $k=>$v)
			{
				$cookie_header_str.="{$k}=".urlencode($v).";";
			}
		}
		
		$param_str = "";
		if (!empty($cookie_header_str))
		{
			$param_str = "--header 'Cookie:{$cookie_header_str}'";
		}

		
		//$cmd="lynx -dump {$remote_file_url}";
		$cmd="wget --tries=1 '{$remote_file_url}' {$param_str} -O /dev/stdout -o /dev/stdout ";	
		
		return $this->add_task_shell($cmd,$delay_run_time,$b_save_log,$b_ignore_err,$parent_task_id,$max_thread_num);
	}


	/**
	 * 获取任务运行状态
	 *
	 * @param int $task_id
	 * @return int  返回任务状态，1: 任务正在运行
							  2：任务成功结束
							  3：任务临时失败
							  4：任务永久失败


	 */
	function get_task_status($task_id, & $running_times)
	{
		$task_id=$task_id*1;

		$request_str = "gettaskstatus\t";
		$request_str.="task_id={$task_id}\t";
		$request_str.="\n";

		$tmp = $this->_send_sock($request_str);

		if ($tmp!==false)
		{
			if (preg_match("/Ok\ttask_status=(\d+)\trunning_times=(\d+)?/",$tmp,$m))
			{
				$task_status= $m[1];
				$running_times= $m[2];
				return $task_status;
			}
			else if (preg_match("/Ok\ttask_status=(\d+)\texit_code=(\d+)?\terr_str=(.*)/",$tmp,$m))
			{
				$task_status= $m[1];
				$this->last_err =$m[2].":".$m[3];
				return $task_status;
			}
			else if (preg_match("/Ok\ttask_status=(\d+)/",$tmp,$m))
			{
				$task_status= $m[1];
				$this->last_err =$tmp;
				return $task_status;
			}
			else
			{
				$this->last_err =$tmp;
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * 或者任务执行的log
	 *
	 * @param int $task_id
	 * @param int $running_times	第几次运行的log  0：取最后一次，>0指定第几次
	 * @return string
	 */
	function get_task_log($task_id, $running_times=0)
	{
		$task_id=$task_id*1;
		$running_times=$running_times*1;

		$request_str = "gettasklog\t";
		$request_str.="task_id={$task_id}\t";
		$request_str.="running_times={$running_times}\t";
		$request_str.="ifdelete_taskandlog=0\t";
		$request_str.="\n";

		$tmp = $this->_send_sock($request_str);

		if ($tmp!==false)
		{
			if (preg_match("/Ok\r\n(.*)/s",$tmp,$m))
			{
				return $m[1];
			}
			else
			{
				$this->last_err =$tmp;
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 增加一个shell队列
	 *
	 * @param string $cmd				//linux shell命令
	 * @param int $delay_run_time		//延时，秒为单位
	 * @param boolean $b_save_log		//是否纪录log
	 * @param boolean $b_ignore_err		//是否忽略错误
	 * @param int $parent_task_id		//父级任务id，如果指定，则可以保证在父级任务完成后才执行本任务
	 * @param int $max_thread_num		//最大线程数
	 * @return int
	 */
	function add_task_shell($cmd,$delay_run_time=0,$b_save_log=true,$b_ignore_err=false,$parent_task_id=0,$max_thread_num=0)
	{
		$cmd=trim($cmd);

		$b_save_log=$b_save_log*1;
		$delay_run_time=$delay_run_time*1;
		$b_ignore_err=$b_ignore_err*1;
		$parent_task_id=$parent_task_id*1;

		$request_str = "addtask\t";
		$request_str.="ifsave_taskandlog={$b_save_log}\t";
		$request_str.="run_after_sec={$delay_run_time}\t";
		$request_str.="task_type=2\t";
		$request_str.="task_content={$cmd}\t";
		$request_str.="ignore_err={$b_ignore_err}\t";
		$request_str.="parent_id={$parent_task_id}";
		if($max_thread_num) $request_str.="\tmax_thread_num={$max_thread_num}";
		$request_str.="\n";

		$tmp = $this->_send_sock($request_str);

		if ($tmp!==false)
		{
			if (preg_match("/Ok\ttask_id=(\d+)/",$tmp,$m))
			{
				$task_id= $m[1];
				return $task_id;
			}
			else
			{
				$this->last_err =$tmp;
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
?>