<?php
if(!defined('LOCATION_SYS'))
{
	exit('Access Denied');
}

if (!class_exists('IpLocation', false))
{


	class IpLocation {

		/**
* QQWry.Dat文件指针
* @var resource
*/

		var $fp;

		/**
* 第一条IP记录的偏移地址
*
* @var int
*/

		var $firstip;

		/**
* 最后一条IP记录的偏移地址
*
* @var int
*/

		var $lastip;

		/**
* IP记录的总条数（不包含版本信息记录）
*
* @var int
*/

		var $totalip;

		/**
* 返回读取的长整型数
*
* @access private
* @return int
*/
		var $g_gmclient;

		function getlong() {

			//将读取的little-endian编码的4个字节转化为长整型数

			$result = unpack('Vlong', fread($this->fp, 4));

			return $result['long'];

		}

		/**
* 返回读取的3个字节的长整型数
*
* @access private
* @return int
*/

		function getlong3() {

			//将读取的little-endian编码的3个字节转化为长整型数

			$result = unpack('Vlong', fread($this->fp, 3).chr(0));

			return $result['long'];

		}

		/**
* 返回压缩后可进行比较的IP地址
*
* @access private
* @param string $ip
* @return string
*/

		function packip($ip) {

			// 将IP地址转化为长整型数，如果在PHP5中，IP地址错误，则返回False，

			// 这时intval将Flase转化为整数-1，之后压缩成big-endian编码的字符串

			return pack('N', intval(ip2long($ip)));

		}

		/**
* 返回读取的字符串
*
* @access private
* @param string $data
* @return string
*/

		function getstring($data = "") {

			$char = fread($this->fp, 1);

			while (ord($char) > 0) { // 字符串按照C格式保存，以\0结束

				$data .= $char; // 将读取的字符连接到给定字符串之后

				$char = fread($this->fp, 1);

			}

			return $data;

		}

		/**
* 返回地区信息
*
* @access private
* @return string
*/

		function getarea() {

			$byte = fread($this->fp, 1); // 标志字节

			switch (ord($byte)) {

				case 0: // 没有区域信息

				$area = "";

				break;

				case 1:

				case 2: // 标志字节为1或2，表示区域信息被重定向

				fseek($this->fp, $this->getlong3());

				$area = $this->getstring();

				break;

				default: // 否则，表示区域信息没有被重定向

				$area = $this->getstring($byte);

				break;

			}

			return $area;

		}

		function gearman_do($func_name,$param,$timeout=10000)
		{
			define("GEARMAN_DO_SUC", 1);
			define("GEARMAN_DO_FAIL", 0);

			$retry_time=0;

			$max_retry=0;

			$ret_val=GEARMAN_DO_FAIL;
			$break_while=0;

			$this->g_gmclient->setTimeout($timeout);
			do
			{
				$result = $this->g_gmclient->do($func_name, $param);

				switch($this->g_gmclient->returnCode())
				{
					case GEARMAN_WORK_DATA:
						#echo "Data: $result\n";
						break;
					case GEARMAN_WORK_STATUS:
						list($numerator, $denominator)= $this->g_gmclient->doStatus();
						#echo "Status: $numerator/$denominator complete\n";
						break;
					case GEARMAN_WORK_FAIL:
						#echo "Failed\n";
						#exit;
						#echo "fail! retry $retry_time";
						if ($max_retry<$retry_time++) {
							#echo "retry fail return";
							$break_while = 1;
							//break;
						}
						break;
					case GEARMAN_SUCCESS:
						#echo "success\n";
						$break_while = 1;
						$ret_val = GEARMAN_DO_SUC;
						break;
					case GEARMAN_TIMEOUT:
						#echo "timeout please retry!\n";
						#exit;
						#echo "timeout retry $retry_time";
						if ($retry_time++>$max_retry) {
							$ret_val = GEARMAN_DO_FAIL;
							#echo "retry fail return";
							$result = "TIMEOUT!";
							$break_while = 1;
							//break;
						}
						break;
					default:
						#echo "RET: " . $gmclient->returnCode() . "\n";
						$break_while = 1;
						$ret_val = GEARMAN_DO_FAIL;
						$result = "UNKNOW ERROR!";
						break;
						#exit;
				}
				if ($break_while){
					break;
				}
			}
			while($this->g_gmclient->returnCode() != GEARMAN_SUCCESS);
			return array($ret_val,$result);
		}

		function getlocation_by_gmclient($ip)
		{
			if (empty($ip)) return false;

			$param['ip'] = $ip;
			
			$t_begin= microtime(true);
			$result = $this->gearman_do("qqwry_get_ip",json_encode($param),500);
			
			$t_end=microtime(true);
			$use_time = $t_end-$t_begin;

			//log
			if($use_time > 0.5 && false)
			{
				//写日志
			}

			if( empty($result[0]) )
			{
				return false;
			}

			$recmd_ret = json_decode($result[1]);
			$recmd = $recmd_ret->cmd_values;
			$recmd_all = json_decode($recmd);

			foreach ( $recmd_all as $values )
			{
				$add = split(",",$values->address);

				$ret['country'] = pack('H*', $add[0]);
				$ret['area'] = pack('H*', $add[1]);
			}

			$ret['ip'] = $ip;

			return $ret;
		}

		/**
* 根据所给 IP 地址或域名返回所在地区信息
*
* @access public
* @param string $ip
* @return array
*/

		function getlocation($ip)
		{
			//return false;
			$location['ip'] = gethostbyname($ip); // 将输入的域名转化为IP地址

			if (is_object($this->g_gmclient) && !defined('G_GET_IP_LOCATION_BY_PACK'))
			{
				return $this->getlocation_by_gmclient($ip);
			}

			if (!$this->fp) return null; // 如果数据文件没有被正确打开，则直接返回空

			$ip = $this->packip($location['ip']); // 将输入的IP地址转化为可比较的IP地址

			// 不合法的IP地址会被转化为255.255.255.255

			// 对分搜索

			$l = 0; // 搜索的下边界

			$u = $this->totalip; // 搜索的上边界

			$findip = $this->lastip; // 如果没有找到就返回最后一条IP记录（QQWry.Dat的版本信息）

			while ($l <= $u) { // 当上边界小于下边界时，查找失败

				$i = floor(($l + $u) / 2); // 计算近似中间记录

				fseek($this->fp, $this->firstip + $i * 7);

				$beginip = strrev(fread($this->fp, 4)); // 获取中间记录的开始IP地址

				// strrev函数在这里的作用是将little-endian的压缩IP地址转化为big-endian的格式

				// 以便用于比较，后面相同。

				if ($ip < $beginip) { // 用户的IP小于中间记录的开始IP地址时

					$u = $i - 1; // 将搜索的上边界修改为中间记录减一

				}

				else {

					fseek($this->fp, $this->getlong3());

					$endip = strrev(fread($this->fp, 4)); // 获取中间记录的结束IP地址

					if ($ip > $endip) { // 用户的IP大于中间记录的结束IP地址时

						$l = $i + 1; // 将搜索的下边界修改为中间记录加一

					}

					else { // 用户的IP在中间记录的IP范围内时

						$findip = $this->firstip + $i * 7;

						break; // 则表示找到结果，退出循环

					}

				}

			}



			//获取查找到的IP地理位置信息

			fseek($this->fp, $findip);

			$location['beginip'] = long2ip($this->getlong()); // 用户IP所在范围的开始地址

			$offset = $this->getlong3();

			fseek($this->fp, $offset);

			$location['endip'] = long2ip($this->getlong()); // 用户IP所在范围的结束地址

			$byte = fread($this->fp, 1); // 标志字节

			switch (ord($byte)) {

				case 1: // 标志字节为1，表示国家和区域信息都被同时重定向

				$countryOffset = $this->getlong3(); // 重定向地址

				fseek($this->fp, $countryOffset);

				$byte = fread($this->fp, 1); // 标志字节

				switch (ord($byte)) {

					case 2: // 标志字节为2，表示国家信息又被重定向

					fseek($this->fp, $this->getlong3());

					$location['country'] = $this->getstring();

					fseek($this->fp, $countryOffset + 4);

					$location['area'] = $this->getarea();

					break;

					default: // 否则，表示国家信息没有被重定向

					$location['country'] = $this->getstring($byte);

					$location['area'] = $this->getarea();

					break;

				}

				break;

				case 2: // 标志字节为2，表示国家信息被重定向

				fseek($this->fp, $this->getlong3());

				$location['country'] = $this->getstring();

				fseek($this->fp, $offset + 8);

				$location['area'] = $this->getarea();

				break;

				default: // 否则，表示国家信息没有被重定向

				$location['country'] = $this->getstring($byte);

				$location['area'] = $this->getarea();

				break;

			}

			if ($location['country'] == " CZ88.NET") { // CZ88.NET表示没有有效信息

				$location['country'] = "未知";

			}

			if ($location['area'] == " CZ88.NET") {

				$location['area'] = "";

			}

			return $location;

		}

		/**
	* 构造函数，打开 QQWry.Dat 文件并初始化类中的信息
	*
	* @param string $filename
	* @return IpLocation
	*/
		function IpLocation($filename = "")
		{
			if($filename=="") $filename = G_LOCATION_PATH."include/QQWry.Dat";


			if( class_exists('GearmanClient', false) )
			{
				$this->g_gmclient = new GearmanClient();
				//$this->g_gmclient->addServers("172.18.5.15:9411,172.18.5.182:9411");
				$this->g_gmclient->addServers("172.18.5.9:9411,172.18.5.9:9412");
				$this->g_gmclient->setTimeout(5000); // 设置超时
			}
			elseif (($this->fp = @fopen($filename, 'rb')) !== false)
			{

				$this->firstip = $this->getlong();

				$this->lastip = $this->getlong();

				$this->totalip = ($this->lastip - $this->firstip) / 7;

				//注册析构函数，使其在程序执行结束时执行

				register_shutdown_function(array(&$this, '_IpLocation'));

			}
		}

		/**
* 析构函数，用于在页面执行结束后自动关闭打开的文件。
*
*/

		function _IpLocation() {

			fclose($this->fp);

		}

		/**
 * 获取IP
 *
 * @return string
 */
		function getIP()
		{
			global $_SERVER;
			if (getenv('HTTP_CLIENT_IP')) {
				$ip = getenv('HTTP_CLIENT_IP');
			} else if (getenv('HTTP_X_FORWARDED_FOR')) {
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			} else if (getenv('REMOTE_ADDR')) {
				$ip = getenv('REMOTE_ADDR');
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}

	}
}

#$Ip = new IpLocation();
#$location=$Ip->getlocation("121.32.2.208");
#print_r($location);
#echo $location['country']." ".$location['area']."\n";

?>