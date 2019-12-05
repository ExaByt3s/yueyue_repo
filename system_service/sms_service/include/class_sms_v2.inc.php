<?php
/**
 * ����ͨ��
 * ΢���ӿڣ�ɽ���ٷ�ͨ����Ϣ�������޹�˾
 * ����ӿ�
 * @author Henry
 * @copyright 2014-12-24
 */

class class_sms_v2
{
	//΢�����Žӿ�
	private $ww_sms_url = 'http://60.28.200.162/submitdata/service.asmx';
	//private $ww_sms_url = 'http://cf.lmobile.cn/submitdata/service.asmx';
	
	//΢�����Žӿ�
	private $ww_mms_url = 'http://60.28.200.162/submitdata/mmswebinterface.asmx';
	//private $ww_mms_url = 'http://cf.lmobile.cn/submitdata/mmswebinterface.asmx';
	
	//������Žӿ�
	//private $bw_sms_url = 'http://111.13.132.36:8080';
	//private $bw_sms_url = 'http://service5.baiwutong.com:8080';
	private $bw_sms_url = 'http://service2.hbsmservice.com:8080';
	
	//������Žӿ�
	private $suma_sms_url = 'http://120.132.132.102/WS';
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
	}
	
	/**
	 * ��ȡͨ����Ϣ
	 * @param int $product_type ͨ�����ͣ�10΢��֪ͨ�࣬11΢����֤���࣬12΢��Ӫ���࣬16������֤���࣬18������֤����
	 * @return array
	 */
	private function get_product_info($product_type)
	{
		$product_type = intval($product_type);
		
		$product_list = array(
			
			//Ħ��ʱ��΢��
			//֪ͨ�࣬���ñ���ģ�� ����������ʱ
			10 => array(
				'api' => 'ww',
				'sname' => 'dlgzmhsh',
				'spwd' => 'AhPA7dx7',
				'scorpid' => '',
				'sprdid' => '1012808',
				'suffix' => '��ԼԼʱ����̡�',
			),
			
			//Ħ��ʱ��΢��
			//Ҫ����ģ�壬һ������ʱ
			11 => array(
				'api' => 'ww',
				'sname' => 'dlgzmhsh',
				'spwd' => 'AhPA7dx7',
				'scorpid' => '',
				'sprdid' => '1012818',
				'suffix' => '��ԼԼʱ����̡�',
			),
			
			//Ħ��ʱ��΢��
			//Ӫ���࣬Ҫ����ģ��
			12 => array(
				'api' => 'ww',
				'sname' => 'dlgzmhsh',
				'spwd' => 'AhPA7dx7',
				'scorpid' => '',
				'sprdid' => '1012812',
				'suffix' => '��ԼԼʱ����̡�',
			),
				
			//Ħ��ʱ������
			//����Ҫ����ģ�壬һ������ʱ
			16 => array(
				'api' => 'suma',
				'sname' => 'yueus',
				'spwd' => 'yueus888',
				'scorpid' => '',
				'sprdid' => '',
				'suffix' => '��ԼԼʱ����̡�',
			),
			
			//Ħ��ʱ������
			//Ҫ����ģ�壬һ������ʱ
			18 => array(
				'api' => 'bw',
				'sname' => '6ecg005',
				'spwd' => '6ecgwe',
				'scorpid' => '',
				'sprdid' => '1069yd',
				'suffix' => '',	//�Է��ӿڻ��Զ�����ǩ��
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
	 * GET����
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
	 * POST����
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
	 * ��xml���ת��������
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
	 * ΢�����ŷ���
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
	 * ΢����ѯ�˻����
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
	 * ������ŷ���
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
	 * �����ѯ�˻����
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
	 * ������ŷ���
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
	 * �����ѯ�˻����
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
	 * ��ȡ ���� get��������Ϣ
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

		// ��ʽ������
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
	 * ��ѯ�˻����
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
	 * ���沢ֱ�ӷ��Ͷ���
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
		
		//����ǩ��
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
	 * ��ȡ�����б�
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
