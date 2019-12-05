<?php
/**
 * �ű����
 * @author kidney
 */
/*
ʹ�÷���:
$poco_log_obj = new poco_log_class('mypoco_v3_t');//��ʼ��
$poco_log_obj->create_log();//����

//����
xxxxxxxxxxxxxxx

$poco_log_obj->write_log('Hello');//д��Log�ļ�����
......
*/

if( !class_exists('poco_log_class', false) )
{
	class poco_log_class
	{
		private $script_name;		//�ű���
		private $log_base_path;
		private $log_path;			//��־Ŀ¼
		private $log_filename;		//��־�ļ���
		private $last_run_time;		//�������ʱ��
		private $user_id;			//�û�ID

		/**
		 * ���캯��
		 * @param string $app_path 		·��
		 * @param string $script_name 	��ؽű��ļ���
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
		 * ����
		 */
		function microtime_float()
		{
		    list($usec, $sec) = explode(" ", microtime());
		    return ((float)$usec + (float)$sec);
		}
		
		/**
		 * ����·��
		 * @param string $data_arr
		 * @return string
		 */
		public function set_log_path($app_path)
		{
			$app_path = trim($app_path);
			if ( $app_path=='' )
			{
				die(__CLASS__.'::'.__FUNCTION__.'::���·��������');
			}
			$this->log_path = $this->log_base_path.$app_path.'/'.date('Y/md/H', time()).'/';
		}
		
		/**
		 * �����ļ���
		 * @param string $path
		 * @return bool
		 */
		private function create_dir($path='')
		{
			if ( $path=='' ) return false;
			
			return is_dir($path) || ( $this->create_dir(dirname($path)) && mkdir($path, 0777) );
		}
		
		/**
		 * ������־
		 *
		 * @param array $data_arr
		 * @return bool
		 */
		public function create_log()
		{
			global $_INPUT;
			
			//�ж��Ƿ���ڸ��ļ�
			if ( !file_exists($this->log_path) )
			{
				$this->create_dir($this->log_path);//�����ļ���
			}
			
			$temp_arr = explode('.', $this->microtime_float());
			$this->log_filename = 'record_'.$this->user_id.'_'.date('H_i_s', $temp_arr[0]).'_'.$temp_arr[1].'.log';//�����ļ���
			unset($temp_arr);
			
			$fp = fopen($this->log_path.$this->log_filename, 'w');//���ļ�
			if ( (bool)$fp!=false )
			{
				$temp_arr = explode('.', $this->microtime_float());
				$time_str = date('Y-m-d H:i:s', $temp_arr[0]);//2010-12-14 11:45:38
				$time_str .= '.'.$temp_arr[1];//�������  .2269
				
				//������load
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
				fwrite($fp, $default_content);//д��
				fclose($fp);//�ر��ļ�
				unset($default_content, $time_str, $temp_arr);
				
				$this->last_run_time = $this->microtime_float();
			}
		}
		
		/**
		 * д��־����
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
			
			$fp = fopen($this->log_path.$this->log_filename, 'a+');//���ļ�
			if ( (bool)$fp!=false )
			{
				/*** ��ʱ ***/
				$time_end = $this->microtime_float();
				$time_consuming = $time_end - $this->last_run_time;
				
				$this->last_run_time = $time_end;
				/*** ��ʱ ***/
	
				$temp_arr = explode('.', $time_end);
				$time_str = date('H:i:s', $temp_arr[0]);//2010-12-14 11:45:38
				$time_str .= '.'.$temp_arr[1];//�������  .2269
				
				if ( is_array($content) ) $content = var_export($content, true);//����Ļ��ͽ������л�
				$temp_content = $time_str." [".round($time_consuming, 4)."] \t".$content."\r\n";
				fwrite($fp, $temp_content);
				fclose($fp);//�ر��ļ�
				unset($temp_content, $temp_arr);
			}
		}
		
		/**
		 * ɾ����־
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
				return unlink($temp_path);//ɾ��log
			}
			return false;
		}
		
		/**
		 * ��������־
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
				return rename($this->log_path.$old_name, $this->log_path.$new_name);//ɾ��log
			}
		}
		
		/**
		 * ����ű����ʱ
		 *
		 * @return bool
		 */
		public function complete()
		{
			return $this->delete_log();//ɾ��
		}
	}
}
?>