<?php
if(!defined('LOCATION_SYS'))
{
	exit('Access Denied');
}

if (!class_exists('IpLocation', false))
{


	class IpLocation {

		/**
* QQWry.Dat�ļ�ָ��
* @var resource
*/

		var $fp;

		/**
* ��һ��IP��¼��ƫ�Ƶ�ַ
*
* @var int
*/

		var $firstip;

		/**
* ���һ��IP��¼��ƫ�Ƶ�ַ
*
* @var int
*/

		var $lastip;

		/**
* IP��¼�����������������汾��Ϣ��¼��
*
* @var int
*/

		var $totalip;

		/**
* ���ض�ȡ�ĳ�������
*
* @access private
* @return int
*/
		var $g_gmclient;

		function getlong() {

			//����ȡ��little-endian�����4���ֽ�ת��Ϊ��������

			$result = unpack('Vlong', fread($this->fp, 4));

			return $result['long'];

		}

		/**
* ���ض�ȡ��3���ֽڵĳ�������
*
* @access private
* @return int
*/

		function getlong3() {

			//����ȡ��little-endian�����3���ֽ�ת��Ϊ��������

			$result = unpack('Vlong', fread($this->fp, 3).chr(0));

			return $result['long'];

		}

		/**
* ����ѹ����ɽ��бȽϵ�IP��ַ
*
* @access private
* @param string $ip
* @return string
*/

		function packip($ip) {

			// ��IP��ַת��Ϊ���������������PHP5�У�IP��ַ�����򷵻�False��

			// ��ʱintval��Flaseת��Ϊ����-1��֮��ѹ����big-endian������ַ���

			return pack('N', intval(ip2long($ip)));

		}

		/**
* ���ض�ȡ���ַ���
*
* @access private
* @param string $data
* @return string
*/

		function getstring($data = "") {

			$char = fread($this->fp, 1);

			while (ord($char) > 0) { // �ַ�������C��ʽ���棬��\0����

				$data .= $char; // ����ȡ���ַ����ӵ������ַ���֮��

				$char = fread($this->fp, 1);

			}

			return $data;

		}

		/**
* ���ص�����Ϣ
*
* @access private
* @return string
*/

		function getarea() {

			$byte = fread($this->fp, 1); // ��־�ֽ�

			switch (ord($byte)) {

				case 0: // û��������Ϣ

				$area = "";

				break;

				case 1:

				case 2: // ��־�ֽ�Ϊ1��2����ʾ������Ϣ���ض���

				fseek($this->fp, $this->getlong3());

				$area = $this->getstring();

				break;

				default: // ���򣬱�ʾ������Ϣû�б��ض���

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
				//д��־
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
* �������� IP ��ַ�������������ڵ�����Ϣ
*
* @access public
* @param string $ip
* @return array
*/

		function getlocation($ip)
		{
			//return false;
			$location['ip'] = gethostbyname($ip); // �����������ת��ΪIP��ַ

			if (is_object($this->g_gmclient) && !defined('G_GET_IP_LOCATION_BY_PACK'))
			{
				return $this->getlocation_by_gmclient($ip);
			}

			if (!$this->fp) return null; // ��������ļ�û�б���ȷ�򿪣���ֱ�ӷ��ؿ�

			$ip = $this->packip($location['ip']); // �������IP��ַת��Ϊ�ɱȽϵ�IP��ַ

			// ���Ϸ���IP��ַ�ᱻת��Ϊ255.255.255.255

			// �Է�����

			$l = 0; // �������±߽�

			$u = $this->totalip; // �������ϱ߽�

			$findip = $this->lastip; // ���û���ҵ��ͷ������һ��IP��¼��QQWry.Dat�İ汾��Ϣ��

			while ($l <= $u) { // ���ϱ߽�С���±߽�ʱ������ʧ��

				$i = floor(($l + $u) / 2); // ��������м��¼

				fseek($this->fp, $this->firstip + $i * 7);

				$beginip = strrev(fread($this->fp, 4)); // ��ȡ�м��¼�Ŀ�ʼIP��ַ

				// strrev����������������ǽ�little-endian��ѹ��IP��ַת��Ϊbig-endian�ĸ�ʽ

				// �Ա����ڱȽϣ�������ͬ��

				if ($ip < $beginip) { // �û���IPС���м��¼�Ŀ�ʼIP��ַʱ

					$u = $i - 1; // ���������ϱ߽��޸�Ϊ�м��¼��һ

				}

				else {

					fseek($this->fp, $this->getlong3());

					$endip = strrev(fread($this->fp, 4)); // ��ȡ�м��¼�Ľ���IP��ַ

					if ($ip > $endip) { // �û���IP�����м��¼�Ľ���IP��ַʱ

						$l = $i + 1; // ���������±߽��޸�Ϊ�м��¼��һ

					}

					else { // �û���IP���м��¼��IP��Χ��ʱ

						$findip = $this->firstip + $i * 7;

						break; // ���ʾ�ҵ�������˳�ѭ��

					}

				}

			}



			//��ȡ���ҵ���IP����λ����Ϣ

			fseek($this->fp, $findip);

			$location['beginip'] = long2ip($this->getlong()); // �û�IP���ڷ�Χ�Ŀ�ʼ��ַ

			$offset = $this->getlong3();

			fseek($this->fp, $offset);

			$location['endip'] = long2ip($this->getlong()); // �û�IP���ڷ�Χ�Ľ�����ַ

			$byte = fread($this->fp, 1); // ��־�ֽ�

			switch (ord($byte)) {

				case 1: // ��־�ֽ�Ϊ1����ʾ���Һ�������Ϣ����ͬʱ�ض���

				$countryOffset = $this->getlong3(); // �ض����ַ

				fseek($this->fp, $countryOffset);

				$byte = fread($this->fp, 1); // ��־�ֽ�

				switch (ord($byte)) {

					case 2: // ��־�ֽ�Ϊ2����ʾ������Ϣ�ֱ��ض���

					fseek($this->fp, $this->getlong3());

					$location['country'] = $this->getstring();

					fseek($this->fp, $countryOffset + 4);

					$location['area'] = $this->getarea();

					break;

					default: // ���򣬱�ʾ������Ϣû�б��ض���

					$location['country'] = $this->getstring($byte);

					$location['area'] = $this->getarea();

					break;

				}

				break;

				case 2: // ��־�ֽ�Ϊ2����ʾ������Ϣ���ض���

				fseek($this->fp, $this->getlong3());

				$location['country'] = $this->getstring();

				fseek($this->fp, $offset + 8);

				$location['area'] = $this->getarea();

				break;

				default: // ���򣬱�ʾ������Ϣû�б��ض���

				$location['country'] = $this->getstring($byte);

				$location['area'] = $this->getarea();

				break;

			}

			if ($location['country'] == " CZ88.NET") { // CZ88.NET��ʾû����Ч��Ϣ

				$location['country'] = "δ֪";

			}

			if ($location['area'] == " CZ88.NET") {

				$location['area'] = "";

			}

			return $location;

		}

		/**
	* ���캯������ QQWry.Dat �ļ�����ʼ�����е���Ϣ
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
				$this->g_gmclient->setTimeout(5000); // ���ó�ʱ
			}
			elseif (($this->fp = @fopen($filename, 'rb')) !== false)
			{

				$this->firstip = $this->getlong();

				$this->lastip = $this->getlong();

				$this->totalip = ($this->lastip - $this->firstip) / 7;

				//ע������������ʹ���ڳ���ִ�н���ʱִ��

				register_shutdown_function(array(&$this, '_IpLocation'));

			}
		}

		/**
* ����������������ҳ��ִ�н������Զ��رմ򿪵��ļ���
*
*/

		function _IpLocation() {

			fclose($this->fp);

		}

		/**
 * ��ȡIP
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