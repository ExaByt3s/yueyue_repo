<?php
/**
 * 短信通道
 * 微网接口，山东百分通联信息技术有限公司
 * 百悟接口
 * @author Henry
 * @copyright 2014-12-24
 */

class class_sms_v2
{
	//微网短信接口
	private $ww_sms_url = 'http://60.28.200.162/submitdata/service.asmx';
	//private $ww_sms_url = 'http://cf.lmobile.cn/submitdata/service.asmx';
	
	//微网彩信接口
	private $ww_mms_url = 'http://60.28.200.162/submitdata/mmswebinterface.asmx';
	//private $ww_mms_url = 'http://cf.lmobile.cn/submitdata/mmswebinterface.asmx';
	
	//百悟短信接口
	//private $bw_sms_url = 'http://111.13.132.36:8080';
	//private $bw_sms_url = 'http://service5.baiwutong.com:8080';
	private $bw_sms_url = 'http://service2.hbsmservice.com:8080';
	
	//速码短信接口
	private $suma_sms_url = 'http://120.132.132.102/WS';
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
	}
	
	/**
	 * 获取通道信息
	 * @param int $product_type 通道类型，10微网通知类，11微网验证码类，12微网营销类，16速码验证码类，18百悟验证码类
	 * @return array
	 */
	private function get_product_info($product_type)
	{
		$product_type = intval($product_type);
		
		$product_list = array(
			
			//摩幻时，微网
			//通知类，不用报备模板 ，可能有延时
			10 => array(
				'api' => 'ww',
				'sname' => 'dlgzmhsh',
				'spwd' => 'AhPA7dx7',
				'scorpid' => '',
				'sprdid' => '1012808',
				'suffix' => '【约约时间电商】',
			),
			
			//摩幻时，微网
			//要报备模板，一般无延时
			11 => array(
				'api' => 'ww',
				'sname' => 'dlgzmhsh',
				'spwd' => 'AhPA7dx7',
				'scorpid' => '',
				'sprdid' => '1012818',
				'suffix' => '【约约时间电商】',
			),
			
			//摩幻时，微网
			//营销类，要报备模板
			12 => array(
				'api' => 'ww',
				'sname' => 'dlgzmhsh',
				'spwd' => 'AhPA7dx7',
				'scorpid' => '',
				'sprdid' => '1012812',
				'suffix' => '【约约时间电商】',
			),
				
			//摩幻时，速码
			//不需要报备模板，一般无延时
			16 => array(
				'api' => 'suma',
				'sname' => 'yueus',
				'spwd' => 'yueus888',
				'scorpid' => '',
				'sprdid' => '',
				'suffix' => '【约约时间电商】',
			),
			
			//摩幻时，百悟
			//要报备模板，一般无延时
			18 => array(
				'api' => 'bw',
				'sname' => '6ecg005',
				'spwd' => '6ecgwe',
				'scorpid' => '',
				'sprdid' => '1069yd',
				'suffix' => '',	//对方接口会自动带上签名
			),
			
		);
		
		$product_info = $product_list[$product_type];
		if( !is_array($product_info) )
		{
			$product_info = $product_list[16];
		}
		
		return $product_info;
	}
	
	/**
	 * GET请求
	 * @param array $data
	 * @param string $url
	 * @return string
	 */
	private function get($data, $url)
	{
		if( !empty($data) )
		{
			if( strpos($url, '?')===false )
			{
				$url = $url . '?' . http_build_query($data);
			}
			else
			{
				$url = $url . '&' . http_build_query($data);
			}
		}
		
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ci, CURLOPT_TIMEOUT, 20);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response = curl_exec($ci);
		curl_close($ci);
		
		return $response;
	}
	
	/**
	 * POST请求
	 * @param array $data
	 * @param string $url
	 * @return string
	 */
	private function post($data, $url)
	{
		if( !is_array($data) || empty($data) )
		{
			return '';
		}
		
		$postfields = '';
		$postfields_sp = '';
		foreach ($data as $key=>$val)
		{
			$postfields .= $postfields_sp . $key . '=' . rawurlencode($val);
			$postfields_sp = '&';
		}
		
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ci, CURLOPT_TIMEOUT, 20);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ci, CURLOPT_URL, $url);
		curl_setopt($ci, CURLOPT_POST, true);
		curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		$response = curl_exec($ci);
		curl_close($ci);
		
		return $response;
	}
	
	/**
	 * 将xml结果转换成数组
	 * @param string $xml
	 * @return array
	 */
	private function xml2arr($xml)
	{
		$arr = array();
		$ret = simplexml_load_string($xml);
		if( $ret===false)
		{
			return $arr;
		}
		$arr = (array)$ret;
		return $arr;
	}
	
	/**
	 * 微网短信发送
	 * @param string $sdst
	 * @param string $smsg
	 * @param array $product_info
	 * @param string $ret
	 * @return bool
	 */
	private function ww_send($sdst, $smsg, $product_info, &$ret='')
	{
		$data = array(
			'sname' => $product_info['sname'],
			'spwd' => $product_info['spwd'],
			'scorpid' => $product_info['scorpid'],
			'sprdid' => $product_info['sprdid'],
			'sdst' => $sdst,
			'smsg' => iconv('GBK', 'UTF-8', $smsg),
		);
		$ret = $this->post($data, $this->ww_sms_url . '/g_Submit');
		$arr = $this->xml2arr($ret);
		if( !empty($arr) && $arr['State']==='0' )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 微网查询账户余额
	 * @param array $product_info
	 * @return int|false
	 */
	private function ww_remain($product_info)
	{
		$data = array(
			'sname' => $product_info['sname'],
			'spwd' => $product_info['spwd'],
			'scorpid' => $product_info['scorpid'],
			'sprdid' => $product_info['sprdid'],
		);
		$xml = $this->post($data, $this->ww_sms_url . '/Sm_GetRemain');
		$arr = $this->xml2arr($xml);
		if( !empty($arr) && $arr['State']==='0' )
		{
			return $arr['Remain']*1;
		}
		return false;
	}
	
	/**
	 * 百悟短信发送
	 * @param string $sdst
	 * @param string $smsg
	 * @param array $product_info
	 * @param string $ret
	 * @return bool
	 */
	private function bw_send($sdst, $smsg, $product_info, &$ret='')
	{
		$data = array(
			'corp_id' => $product_info['sname'],
			'corp_pwd' => $product_info['spwd'],
			'corp_service' => $product_info['sprdid'],
			'mobile' => $sdst,
			'msg_content' => $smsg,
		);
		$ret = $this->post($data, $this->bw_sms_url . '/sms_send2.do');
		$ret = trim($ret);
		
		$match = array();
		if( preg_match('/^0#(\d+)$/isU', $ret, $match) )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 百悟查询账户余额
	 * @param array $product_info
	 * @return int|false
	 */
	private function bw_remain($product_info)
	{
		$data = array(
			'corp_id' => $product_info['sname'],
			'corp_pwd' => $product_info['spwd'],
		);
		$ret = $this->post($data, $this->bw_sms_url . '/sms_count2.do');
		$ret = trim($ret);
		
		$match = array();
		if( preg_match('/^ok#([\d\.]+)$/isU', $ret, $match) )
		{
			return $match[1]*1;
		}
		return false;
	}
	
	/**
	 * 速码短信发送
	 * @param string $sdst
	 * @param string $smsg
	 * @param array $product_info
	 * @param string $ret
	 * @return bool
	 */
	private function suma_send($sdst, $smsg, $product_info, &$ret='')
	{
		$data = array(
			'CorpID' => $product_info['sname'],
			'Pwd' => $product_info['spwd'],
			'Mobile' => $sdst,
			'Content' => $smsg,
		);
		$ret = $this->post($data, $this->suma_sms_url . '/BatchSend2.aspx');
		$ret = trim($ret);
		if( $ret>0 )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 速码查询账户余额
	 * @param array $product_info
	 * @return int|false
	 */
	private function suma_remain($product_info)
	{
		$data = array(
			'CorpID' => $product_info['sname'],
			'Pwd' => $product_info['spwd'],
		);
		$ret = $this->get($data, $this->suma_sms_url . '/SelSum.aspx');
		$ret = trim($ret);
		if( $ret>=0 )
		{
			return $ret;
		}
		return false;
	}

	/**
	 * 获取 速码 get的上行信息
	 * @param array $product_info
	 * @return array
	 */
	private function suma_get_up_list($product_info)
	{
		$data = array(
			'CorpID' => $product_info['sname'],
			'Pwd' => $product_info['spwd'],
		);

		$ret = $this->get($data, $this->suma_sms_url . '/Get.aspx');

		$ret = trim($ret);
		if( strlen($ret)<1 || $ret == '-101' )
		{
			return array();
		}

		// 格式化数据
		$ret_arr =array();
		$exp1 = explode('||',$ret);
		foreach ($exp1 as $v)
		{
			if (!empty($v))
			{
				$exp2 = explode('#',$v);
				array_push ( $ret_arr ,  $exp2 );
			}
		}

		return $ret_arr;
	}
	
	/**
	 * 查询账户余额
	 * @param int $product_type
	 * @return int|false
	 */
	public function get_remain($product_type)
	{
		$product_info = $this->get_product_info($product_type);
		$ret = false;
		if( $product_info['api']=='ww' )
		{
			$ret = $this->ww_remain($product_info);
		}
		elseif( $product_info['api']=='bw' )
		{
			$ret = $this->bw_remain($product_info);
		}
		elseif( $product_info['api']=='suma' )
		{
			$ret = $this->suma_remain($product_info);
		}
		return $ret;
	}
	
	/**
	 * 保存并直接发送短信
	 * @param string $phone
	 * @param string $message
	 * @param int $product_type
	 * @param array $more_info array('user_id'=>'', 'send_from'=>'', 'level'=>'')
	 * @return boolean
	 */
	public function save_and_send_sms($phone, $message, $product_type=0, $more_info=array())
	{
		$phone = trim($phone);
		$message = trim($message);
		$product_type = intval($product_type);
		if( !is_array($more_info) ) $more_info = array();
		
		$user_id = trim($more_info['user_id']);
		$send_from = trim($more_info['send_from']);
		if( array_key_exists('level', $more_info) )
		{
			$level = intval($more_info['level']);
		}
		else
		{
			$level = 1;
		}
		
		//补充签名
		$product_info = $this->get_product_info($product_type);
		$message .= $product_info['suffix'];
		
		$app_obj = POCO::singleton('sms_send_list_class');
		$data = array(
			'user_id' => $user_id,
			'send_to' => $phone,
			'send_from' => $send_from,
			'message' => $message,
			'level' => $level,
			'add_time' => date('Y-m-d H:i:s', time()),
			'state' => 'handling',
			'type' => 'sms',
			'sms_api' => 1,
			'product_type' => $product_type,
		);
		$sms_id = $app_obj->insert($data);
		$sms_id = intval($sms_id);
		if( $sms_id<1 )
		{
			return false;
		}
		
		$ret = false;
		$err_str = '';
		if( $product_info['api']=='ww' )
		{
			$ret = $this->ww_send($phone, $message, $product_info, $err_str);
		}
		elseif( $product_info['api']=='bw' )
		{
			$ret = $this->bw_send($phone, $message, $product_info, $err_str);
		}
		elseif( $product_info['api']=='suma' )
		{
			$ret = $this->suma_send($phone, $message, $product_info, $err_str);
		}
		else
		{
			$err_str = 'no api';
		}
		
		$data['sms_id'] = $sms_id;
		$data['err_str'] = trim($err_str);
		if( $ret )
		{
			$data['state'] = 'finished';
			$logObj = POCO::singleton('sms_service_log_class');
			$logObj->insert($data);
		}
		else
		{
			$data['state'] = 'error';
			$errorObj = POCO::singleton('sms_service_error_class');
			$errorObj->insert($data);
		}
		$app_obj->delete("sms_id={$sms_id}");
		
		return $ret;
	}
	
	/**
	 * 获取上行列表
	 * @param int $product_type
	 * @return array
	 */
	public function get_up_list($product_type)
	{

		$product_info = $this->get_product_info($product_type);

		$ret = array();
		if( $product_info['api']=='suma' )
		{
			$ret = $this->suma_get_up_list($product_info);
		}
		return $ret;
	}
	
}
