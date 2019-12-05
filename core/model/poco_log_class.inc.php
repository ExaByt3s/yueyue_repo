<?php
/**
 * 脚本监察
 * @author kidney
 */
/*
使用方法:
$poco_log_obj = new poco_log_class('mypoco_v3_t');//初始化
$poco_log_obj->create_log();//创建

//代码
xxxxxxxxxxxxxxx

$poco_log_obj->write_log('Hello');//写入Log文件内容
......
*/

if( !class_exists('poco_log_class', false) )
{
	class poco_log_class
	{
		private $script_name;		//脚本名
		private $log_base_path;
		private $log_path;			//日志目录
		private $log_filename;		//日志文件名
		private $last_run_time;		//最后运行时间
		private $user_id;			//用户ID

		/**
		 * 构造函数
		 * @param string $app_path 		路径
		 * @param string $script_name 	监控脚本文件名
		 * @return bool
		 */
		public function __construct($app_path = '', $script_name = '')
		{
			$script_name = trim($script_name);
			if ( $script_name=='' )
			{
				//$temp_arr = explode('/', $_SERVER['REQUEST_URI']);
				//$this->script_name = $temp_arr[count($temp_arr)-1];
				$this->script_name = $_SERVER['SCRIPT_FILENAME'];
				//unset($temp_arr);
			}
			else 
			{
				$this->script_name = $script_name;
			}
			
			$this->user_id = (int)$_COOKIE['member_id'];
			$this->log_base_path = G_POCO_APP_PATH . '/logs/';
			$this->set_log_path($app_path);
			return true;
		}
		
		/**
		 * 毫秒
		 */
		function microtime_float()
		{
		    list($usec, $sec) = explode(" ", microtime());
		    return ((float)$usec + (float)$sec);
		}
		
		/**
		 * 设置路径
		 * @param string $data_arr
		 * @return string
		 */
		public function set_log_path($app_path)
		{
			$app_path = trim($app_path);
			if ( $app_path=='' )
			{
				die(__CLASS__.'::'.__FUNCTION__.'::监控路径不完整');
			}
			$this->log_path = $this->log_base_path.$app_path.'/'.date('Y/md/H', time()).'/';
		}
		
		/**
		 * 创建文件夹
		 * @param string $path
		 * @return bool
		 */
		private function create_dir($path='')
		{
			if ( $path=='' ) return false;
			
			return is_dir($path) || ( $this->create_dir(dirname($path)) && mkdir($path, 0777) );
		}
		
		/**
		 * 增加日志
		 *
		 * @param array $data_arr
		 * @return bool
		 */
		public function create_log()
		{
			global $_INPUT;
			
			//判断是否存在该文件
			if ( !file_exists($this->log_path) )
			{
				$this->create_dir($this->log_path);//创建文件夹
			}
			
			$temp_arr = explode('.', $this->microtime_float());
			$this->log_filename = 'record_'.$this->user_id.'_'.date('H_i_s', $temp_arr[0]).'_'.$temp_arr[1].'.log';//创建文件名
			unset($temp_arr);
			
			$fp = fopen($this->log_path.$this->log_filename, 'w');//打开文件
			if ( (bool)$fp!=false )
			{
				$temp_arr = explode('.', $this->microtime_float());
				$time_str = date('Y-m-d H:i:s', $temp_arr[0]);//2010-12-14 11:45:38
				$time_str .= '.'.$temp_arr[1];//加入毫秒  .2269
				
				//服务器load
				$sys_load_arr = sys_getloadavg();
				
				$default_content = "#-----------------------------------------------------------\r\n";
				$default_content .= "# Script started at ".$time_str."\r\n";
				$default_content .= "# Server IP: ".$_SERVER['SERVER_ADDR']."\r\n";
				$default_content .= "# Server Load: ".$sys_load_arr[0]."\r\n";
				$default_content .= "# Script FileName: ".$this->script_name."\r\n";
				$default_content .= "# POCO ID: ".$this->user_id."\r\n";
				$default_content .= "# INPUT: ".var_export($_INPUT, true)."\r\n";
				$default_content .= "# REQUEST: ".var_export($_REQUEST, true)."\r\n";
				$default_content .= "# COOKIE: ".var_export($_COOKIE, true)."\r\n";
				$default_content .= "#-----------------------------------------------------------\r\n\r\n";
				fwrite($fp, $default_content);//写入
				fclose($fp);//关闭文件
				unset($default_content, $time_str, $temp_arr);
				
				$this->last_run_time = $this->microtime_float();
			}
		}
		
		/**
		 * 写日志内容
		 *
		 * @param array $data_arr
		 * @return bool
		 */
		public function write_log($content)
		{
			if ( $this->log_filename=='' )
			{
				return false;
			}
			
			$fp = fopen($this->log_path.$this->log_filename, 'a+');//打开文件
			if ( (bool)$fp!=false )
			{
				/*** 耗时 ***/
				$time_end = $this->microtime_float();
				$time_consuming = $time_end - $this->last_run_time;
				
				$this->last_run_time = $time_end;
				/*** 耗时 ***/
	
				$temp_arr = explode('.', $time_end);
				$time_str = date('H:i:s', $temp_arr[0]);//2010-12-14 11:45:38
				$time_str .= '.'.$temp_arr[1];//加入毫秒  .2269
				
				if ( is_array($content) ) $content = var_export($content, true);//数组的话就进行序列化
				$temp_content = $time_str." [".round($time_consuming, 4)."] \t".$content."\r\n";
				fwrite($fp, $temp_content);
				fclose($fp);//关闭文件
				unset($temp_content, $temp_arr);
			}
		}
		
		/**
		 * 删除日志
		 *
		 * @param array $data_arr
		 * @return bool
		 */
		public function delete_log()
		{
			if ( $this->log_filename=='' )
			{
				return false;
			}
			$temp_path = $this->log_path.$this->log_filename;
			if ( file_exists($temp_path) )
			{
				return unlink($temp_path);//删除log
			}
			return false;
		}
		
		/**
		 * 重命名日志
		 *
		 * @param array $data_arr
		 * @return bool
		 */
		public function rename_log($old_name='', $new_name='')
		{
			if ( trim($old_name)=='' || trim($new_name)=='' )
			{
				return false;
			}
			
			if ( file_exists($this->log_path.$old_name) )
			{
				return rename($this->log_path.$old_name, $this->log_path.$new_name);//删除log
			}
		}
		
		/**
		 * 如果脚本完成时
		 *
		 * @return bool
		 */
		public function complete()
		{
			return $this->delete_log();//删除
		}
	}
}
?>