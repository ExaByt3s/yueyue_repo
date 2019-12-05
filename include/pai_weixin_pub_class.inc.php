<?php
/**
 * ΢�Ź��ں���
 * @author Henry
 */

class pai_weixin_pub_class extends POCO_TDG
{
	/**
	 * ΢�Ű�ID
	 * @var int
	 */
	private $bind_id = 0;
	
	/**
	 * Ӧ��ID
	 * @var string
	 */
	private $app_id = '';
	
	/**
	 * Ӧ����Կ
	 * @var string
	 */
	private $app_secret = '';
	
	/**
	 * ��ҳ��Ȩ��������
	 * @var string
	 */
	private $redirect_uri = '';
	
	/**
	 * �ͷ�����
	 * @var string
	 */
	private $service = '4000-82-9003';
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->bind_id = 1;
		$this->app_id = POCO_APP_PAI::ini('weixin_pub/app_id');
		$this->app_secret = POCO_APP_PAI::ini('weixin_pub/app_secret');
		$this->redirect_uri = POCO_APP_PAI::ini('weixin_pub/redirect_uri');
	}
	
	/**
	 * ָ��΢���û���
	 */
	private function set_weixin_user_tbl()
	{
		$this->setServerId(101);
		$this->setDBName('pai_db');
		$this->setTableName('pai_weixin_user_tbl');
	}
	
	/**
	 * ָ��΢��ģ����Ϣ��
	 */
	private function set_weixin_template_message_tbl()
	{
		$this->setServerId(101);
		$this->setDBName('pai_log_db');
		$this->setTableName('pai_weixin_template_message_tbl');
	}
	
	/**
	 * GBKת��UTF-8
	 * @param string|array $str
	 */
	private function gbk_to_utf8($str)
	{
		if( is_string($str) )
		{
			$str = iconv('gbk', 'utf-8', $str);
		}
		elseif( is_array($str) )
		{
			foreach ($str as $key=>$val)
			{
				$str[$key] = $this->gbk_to_utf8($val);
			}
		}
		return $str;
	}
	
	/**
	 * UTF-8ת��GBK
	 * @param string|array $str
	 */
	private function utf8_to_gbk($str)
	{
		if( is_string($str) )
		{
			$str = iconv('utf-8', 'gbk//IGNORE', $str);
		}
		elseif( is_array($str) )
		{
			foreach ($str as $key=>$val)
			{
				$str[$key] = $this->utf8_to_gbk($val);
			}
		}
		return $str;
	}
	
	/**
	 * ����HTTP����
	 * @param string $url
	 * @param string $method GET|POST
	 * @param string|array $postfields �����������ͨ��urlencoded����ַ�������'para1=val1&para2=val2&...'��ʹ��һ�����ֶ���Ϊ��ֵ���ֶ�����Ϊֵ�����顣���value��һ�����飬Content-Typeͷ���ᱻ���ó�multipart/form-data��
	 * @param array $headers ���� array('Content-type: text/plain', 'Content-length: 100')
	 * @return string
	 */
	private function http($url, $method, $postfields=null, $headers=null)
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		
		if( is_array($headers) )
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		if($method == 'POST')
		{
			curl_setopt($ch, CURLOPT_POST, true);
			if( !empty($postfields) )
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			}
		}
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
	
	/**
	 * ����΢���û�
	 * @param array $user_info
	 * @return bool
	 */
	public function save_weixin_user($user_info)
	{
		$openid = trim($user_info['openid']);
		$nickname = trim($user_info['nickname']);
		$sex = intval($user_info['sex']);
		$language = trim($user_info['language']);
		$city = trim($user_info['city']);
		$province = trim($user_info['province']);
		$country = trim($user_info['country']);
		$headimgurl = trim($user_info['headimgurl']);
		$privilege = json_encode($user_info['privilege']);
		$unionid = trim($user_info['unionid']);
		$code = trim($user_info['code']);
		$access_token = trim($user_info['access_token']);
		$expires_in = intval($user_info['expires_in']);
		$refresh_token = trim($user_info['refresh_token']);
		$scope = trim($user_info['scope']);
		if( strlen($openid)<1 )
		{
			return false;
		}
		
		if( $scope=='snsapi_userinfo' )
		{
			$data = array(
				'openid' => $openid,
				'nickname' => $nickname,
				'sex' => $sex,
				'language' => $language,
				'city' => $city,
				'province' => $province,
				'country' => $country,
				'headimgurl' => $headimgurl,
				'privilege' => $privilege,
				'unionid' => $unionid,
				'appid' => $this->app_id,
				'code' => $code,
				'access_token' => $access_token,
				'expires_in' => $expires_in,
				'refresh_token' => $refresh_token,
				'scope' => $scope,
				'lately_time' => time(),
			);
		}
		else
		{
			$data = array(
				'openid' => $openid,
				'appid' => $this->app_id,
				'code' => $code,
				'access_token' => $access_token,
				'expires_in' => $expires_in,
				'refresh_token' => $refresh_token,
				'scope' => $scope,
				'lately_time' => time(),
			);
		}
		
		$user_tmp = $this->get_weixin_user($openid);
		if( empty($user_tmp) )
		{
			$this->set_weixin_user_tbl();
			$this->insert($data, 'IGNORE');
		}
		else
		{
			$where_str = "openid=:x_openid";
			sqlSetParam($where_str, 'x_openid', $openid);
			
			$this->set_weixin_user_tbl();
			$this->update($data, $where_str);
		}
		
		return true;
	}
	
	/**
	 * ͨ��user_id��ȡ΢���û��Ļ�����Ϣ
	 * @param string $openid
	 * @return array
	 */
	public function get_user_weixin_base_info_by_user_id($user_id)
	{
        $user_id = intval($user_id);
        if( $user_id<1 )
        {
            return array();
        }

        $bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
        $bind_info = $bind_weixin_obj->get_bind_info_by_user_id($user_id);
        $open_id = trim($bind_info['open_id']);

        $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
        $rst = $weixin_helper_obj->get_user_info($this->bind_id, $open_id);
        return $rst;
	}

	/**
	 * ��ȡ΢���û�
	 * @param string $openid
	 * @return array
	 */
	public function get_weixin_user($openid)
	{
		$openid = trim($openid);
		if( strlen($openid)<1 )
		{
			return array();
		}
		
		$where_str = "openid=:x_openid";
		sqlSetParam($where_str, 'x_openid', $openid);
		
		$this->set_weixin_user_tbl();
		$row = $this->find($where_str);
		
		return $row;
	}
	
	/**
	 * ��ȡ���һ�ι�ע
	 * @param int $bind_id
	 * @param string $open_id
	 */
	public function get_receive_info_by_subscribe($open_id)
	{
		$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
		return $weixin_helper_obj->get_receive_info_by_subscribe($this->bind_id, $open_id);
	}
	
	/**
	 * ��ȡaccess_token
	 * @param string $b_cache
	 * @return string
	 */
	public function get_access_token($b_cache=true)
	{
		$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
		return $weixin_helper_obj->wx_get_access_token($this->bind_id, $b_cache);
	}
	
	/**
	 * ��ȡ��ҳ��Ȩ����
	 * @param array $params array('mode'=>'', 'url'=>'')
	 * @param string $scope snsapi_userinfo|snsapi_base
	 * @param string $redirect_uri
	 * @param string $state
	 * @return string
	 */
	public function auth_get_authorize_url($params=array(), $scope='snsapi_userinfo', $redirect_uri='', $state='')
	{
		if( !is_array($params) ) $params = array();
		$scope = trim($scope);
		$redirect_uri = trim($redirect_uri);
		if( strlen($redirect_uri)<1 )
		{
			$redirect_uri = $this->redirect_uri;
		}
		$state = trim($state);
		
		$params_str = '';
		$params_sp = '';
		foreach ($params as $key=>$val)
		{
			$params_str .= $params_sp . $key . '=' . urlencode($val);
			$params_sp = '&';
		}
		if( strlen($params_str)>0 )
		{
			if( strpos($redirect_uri, '?')===false )
			{
				$redirect_uri = $redirect_uri . '?' . $params_str;
			}
			else
			{
				$redirect_uri = $redirect_uri . '&' . $params_str;
			}
		}
		
		$scope = urlencode($scope);
		$redirect_uri = urlencode($redirect_uri);
		$state = urlencode($state);
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
		
		return $url;
	}
	
	/**
	 * ��ȡ��ҳ��Ȩ��Ϣ
	 * @param string $code
	 * @return array
	 */
	public function auth_get_access_info($code)
	{
		$code = trim($code);
		if( strlen($code)<1 )
		{
			return array();
		}
		
		$code = urlencode($code);
		//$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
		$url = "https://101.226.90.58/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
		$ret_json = $this->http($url, 'GET');
		$ret_info = json_decode($ret_json, true);
		if( !is_array($ret_info) ) $ret_info = array();
		
		$access_token = trim($ret_info['access_token']);
		
		//����cache
		if( strlen($access_token)>0 )
		{
			$expires_in = intval($ret_info['expires_in']);
			$open_id = trim($ret_info['openid']);
			
			$cache_key  = 'pai_weixin_auth_access_token_'.  $this->app_id . '_' . md5($open_id);
			POCO::setCache($cache_key, $ret_info, array('life_time'=>$expires_in-200));
		}
		
		return $ret_info;
	}
	
	/**
	 * ��ȡ��ҳ��Ȩ��Ϣ�����Ի���
	 * @param string $open_id
	 * @return array
	 */
	public function auth_get_access_info_from_cache($open_id)
	{
		$open_id = trim($open_id);
		if( strlen($open_id)<1 )
		{
			return array();
		}
		
		$cache_key = 'pai_weixin_auth_access_token_'.  $this->app_id . '_' . md5($open_id);
		$cache_data = POCO::getCache($cache_key);
		if( !is_array($cache_data) ) $cache_data = array();
		
		return $cache_data;
	}
	
	/**
	 * �����Ȩ��Ϣ
	 * @param string $open_id
	 * @param string $access_token ��ҳ��Ȩ�ӿڵ���ƾ֤
	 * @return bool
	 */
	public function auth_check_access_info($open_id, $access_token)
	{
		$open_id = trim($open_id);
		$access_token = trim($access_token);
		
		if( strlen($open_id)<1 || strlen($access_token)<1 )
		{
			return false;
		}
		
		$open_id = urlencode($open_id);
		$access_token = urlencode($access_token);
		//$url = "https://api.weixin.qq.com/sns/auth?access_token={$access_token}&openid={$open_id}";
		$url = "https://101.226.90.58/sns/auth?access_token={$access_token}&openid={$open_id}";
		$ret_json = $this->http($url, 'GET');
		$ret_info = json_decode($ret_json, true);
		if( !is_array($ret_info) ) $ret_info = array();
		
		$errcode = trim($ret_info['errcode']);
		if( $errcode==='0' )
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * ��ȡ�û���Ϣ
	 * @param string $open_id
	 * @param string $access_token ��ҳ��Ȩ�ӿڵ���ƾ֤
	 * @return array
	 */
	public function auth_get_user_info($open_id, $access_token)
	{
		$open_id = trim($open_id);
		$access_token = trim($access_token);
		if( strlen($open_id)<1 || strlen($access_token)<1 )
		{
			return array();
		}
		
		$open_id = urlencode($open_id);
		$access_token = urlencode($access_token);
		//$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
		$url = "https://101.226.90.58/sns/userinfo?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
		$ret_json = $this->http($url, 'GET');
		$ret_info = json_decode($ret_json, true);
		if( !is_array($ret_info) ) $ret_info = array();
		
		if( isset($ret_info['errcode']) )
		{
			return array();
		}
		
		$ret_info = $this->utf8_to_gbk($ret_info);
		
		return $ret_info;
	}
	
	/**
	 * ģ����Ϣ���ͣ������û�ID
	 * @param int $user_id
	 * @param string $template_code
	 * @param array $data
	 * @param string $to_url
	 * @param string $top_color
	 * @return bool
	 */
	public function message_template_send_by_user_id($user_id, $template_code, $data, $to_url='', $top_color='')
	{
		$template_list = array(
			
			'G_PAI_WEIXIN_USER_BIND' => array(
				//΢�Ű󶨳ɹ�֪ͨ
				'template_id' => '8Ds4za-KyjydcDBxX8hKQ7_Wd-xyAN8IM3OhVXP5Yco',
				'data' => array(
					'first' => array('value'=>'��ϲ��󶨳ɹ���', 'color'=>''),
					'keyword1' => array('value'=>'{nickname}', 'color'=>''),
					'keyword2' => array('value'=>'{cellphone}', 'color'=>''),
					'keyword3' => array('value'=>'Լ�ġ����ѵ�', 'color'=>''),
					'remark' => array('value'=>'��ӭʹ��ԼԼ��', 'color'=>''),
				),
			),
			
			//ģ�ؾܾ�Լ������
			'G_PAI_WEIXIN_DATE_REFUSED' => array(
				//Լ�ı���֪ͨ
				'template_id' => 'rzGAKpoO7xPZOJGwRQEq9QDT9ZxIkWtIp9adySVTOws',
				'data' => array(
					'first' => array('value'=>'��ã�ģ�ء�{nickname}��û�������Լ�����롣', 'color'=>''),
					'keyword1' => array('value'=>'{datetime}', 'color'=>''),
					'keyword2' => array('value'=>'{address}', 'color'=>''),
					'keyword3' => array('value'=>'{reason}', 'color'=>''),
					'remark' => array('value'=>'ģ�ء�{nickname}��û�н������Լ�����룬�˿��2����������ԭ·�˻ء�����ѡ�񣬲���{service}���ͷ�����Լ~', 'color'=>''),
				),
			),
			
			//ģ�ؽ���Լ������
			'G_PAI_WEIXIN_DATE_ACCEPTED' => array(
				//�����ɹ�֪ͨ
				'template_id' => 'o5RZJVZfpjrAP-tfJCHg_xK7_UhqFb7El8BQ61Q2yoc',
				'data' => array(
					'first' => array('value'=>'��ã�ģ�ء�{nickname}����ͬ�������Լ������', 'color'=>''),
					'keynote1' => array('value'=>'˽��', 'color'=>''),
					'keynote2' => array('value'=>'{datetime}', 'color'=>''),
					'keynote3' => array('value'=>'{address}', 'color'=>''),
					'remark' => array('value'=>"����鿴Լ�Ķ�ά�롣\r\n���ڻ�����ʾ�˶�ά������������ģ�ؽ���ɨ��ȷ�ϡ��������⣬��δ�ͷ��绰{service}", 'color'=>''),
				),
			),
			
			//ǩ���ɹ�
			'G_PAI_WEIXIN_CODE_CHECKED' => array(
				//����״̬����
				'template_id' => 'o0LVFa5Dfm__ZEpmDG-MHjZi_cJM3gwEOZE4X6BY8tU',
				'data' => array(
					'first' => array('value'=>'��ã�ģ�ء�{nickname}����ǩ���ɹ���ף��Լ�����', 'color'=>''),
					'OrderSn' => array('value'=>'{order_no}', 'color'=>''),
					'OrderStatus' => array('value'=>'��ǩ��', 'color'=>''),
					'remark' => array('value'=>'', 'color'=>''),
				),
			),
			
			/*
			//ģ��ȷ�����
			'G_PAI_WEIXIN_DATE_FINISHED' => array(
				//�������֪ͨ
				'template_id' => '9UhK82XxNZ973sZ2SEvKDmI0SMa97IJ0zPslb83TtcU',
				'data' => array(
					'first' => array('value'=>'��ϲ�㣬�������ģ�ء�{nickname}����Լ��', 'color'=>''),
					'keynote1' => array('value'=>'�����', 'color'=>''),
					'keynote2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'��������������ģ�ء�{nickname}���ķ���', 'color'=>''),
				),
			),
			*/
			
			//ģ�����ۺ�
			'G_PAI_WEIXIN_MT_CMT' => array(
				//������ɽ��֪ͨ
				'template_id' => '2hwsvJHRCYjaHPVHMjF2tCF-ARkqZQq99YqHJ0qp_0Y',
				'data' => array(
					'first' => array('value'=>'��ã�ģ�ء�{nickname}���Ѷ�����������', 'color'=>''),
					'Content1' => array('value'=>'', 'color'=>''),
					'Good' => array('value'=>'{datetime} Լ��', 'color'=>''),
					'remark' => array('value'=>'���������鿴��', 'color'=>''),
				),
			),
			
			//Լ�ĳ�ʱ�����Զ�ȡ��Լ�Ĳ��˿�
			//��Ӱʦ�ύԼ�ģ�ģ��û��������ʱ
			//��Ӱʦ�ύԼ�ģ�ģ�ؽ��ܣ�ͶӰʦ�����˿ģ��û�в�������ʱ
			'G_PAI_WEIXIN_DATE_IGNORE' => array(
				//����ȡ��֪ͨ
				'template_id' => 'rhwN3X2zeEZq319OorcKkXIZ4OqslNDWWsoStLCqfhs',
				'data' => array(
					'first' => array('value'=>"��ã�ģ�س�ʱû�в�����Լ����ȡ�����˿\r\nģ���ǳƣ�{nickname}", 'color'=>''),
					'keyword1' => array('value'=>'{order_no}', 'color'=>''),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'�˿��2����������ԭ·�˻أ���������ء�ԼԼ��APP�鿴�����κ�������µ�ͷ���{service}', 'color'=>''),
				),
			),
			
			/*
			//ģ�ط���˽�ģ���Ӱʦû��APP
			'G_PAI_WEIXIN_MT_CHAT' => array(
				//��������֪ͨ
				'template_id' => 'FRCbpPEowXaiAhrcH_PittvtM7_sJ1_-7bBE6Jvp61s',
				'data' => array(
					'first' => array('value'=>'ģ�ء�{nickname}�� ���㷢��һ����Ϣ����������app�鿴����ģ�ؽ�һ����ͨ�ɡ�', 'color'=>''),
					'user' => array('value'=>'{nickname}  ID��{user_id}', 'color'=>''),
					'reason' => array('value'=>'Լ�����鹵ͨ', 'color'=>''),
					'address' => array('value'=>'��ԼԼ��app', 'color'=>''),
					'remark' => array('value'=>'��������app�鿴��Ϣ������ģ�ؽ�һ����ͨ�ɣ����κ�������µ�ͷ���{service}', 'color'=>''),
				),
			),
			*/
			
			//�̼ҷ���˽�ģ�������û��APP
			'G_PAI_WEIXIN_MT_CHAT' => array(
				//ϵͳ֪ͨ
				'template_id' => 'Em5smvXurTpRkZw4BIHEhku5A0hNGhtkv_1jJvyeiQ4',
				'data' => array(
					'first' => array('value'=>'�̼ҡ�{nickname}�����㷢��һ����Ϣ������APP�н��в鿴', 'color'=>''),
					'keyword1' => array('value'=>'{nickname}  ID��{user_id}', 'color'=>''),
					'keyword2' => array('value'=>'��������app�鿴�������̼ҽ�һ����ͨ�ɡ�', 'color'=>''),
					'remark' => array('value'=>'���κ�������µ�ͷ���{service}', 'color'=>''),
				),
			),
			
			//ģ��ͬ��ȡ��
			'G_PAI_WEIXIN_DATE_CANCEL_ACCEPTED' => array(
				//����ȡ��֪ͨ
				'template_id' => 'rhwN3X2zeEZq319OorcKkXIZ4OqslNDWWsoStLCqfhs',
				'data' => array(
					'first' => array('value'=>"��ã�ģ�ء�{nickname}����ͬ��ȡ��Լ�ġ�\r\nģ���ǳƣ�{nickname}", 'color'=>''),
					'keyword1' => array('value'=>'{order_no}', 'color'=>''),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'�˿��2����������ԭ·�˻أ���������ء�ԼԼ��APP�鿴�����κ�������µ�ͷ���{service}', 'color'=>''),
				),
			),
			
			/*
			//ģ�ز�ͬ��ȡ��
			'G_PAI_WEIXIN_DATE_CANCEL_REFUSED' => array(
				//Լ��ȡ�����벵��֪ͨ
				'template_id' => 'j7qAP2jpIKecNC4Iyb9x1aTH62gwSGMOXUCghGEDDGg',
				'data' => array(
					'first' => array('value'=>'��ã�ģ�ء�{nickname}����ͬ��ȡ��Լ�ġ�', 'color'=>''),
					'keyword1' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'��ѡ�����ء�ԼԼ��app��ģ�ؽ�һ����ͨ�ɣ����κ�������µ�ͷ���{service}��', 'color'=>''),
				),
			),
			*/
			
			//ǿ���˿�ɹ�
			'G_PAI_WEIXIN_DATE_CANCEL_FORCE' => array(
				//�˿�ɹ�֪ͨ
				'template_id' => 'HJXQWIG_R24bjizpTR260weq_c9V6Tkg8YCc9Dg2HEM',
				'data' => array(
					'first' => array('value'=>'��ã�ǿ���˿��ѳɹ���Լ�ķ��õ�70%���˻ص���ġ�ԼԼ���˻��С�', 'color'=>''),
					'orderProductPrice' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'{nickname}', 'color'=>''),
					'orderName' => array('value'=>'{order_no}', 'color'=>''),
					'remark' => array('value'=>'�˿��2����������ԭ·�˻أ���������ء�ԼԼ��APP�鿴�����κ�������µ�ͷ���{service}��', 'color'=>''),
				),
			),
			
			//˫��û��ǩ��
			'G_PAI_WEIXIN_CODE_NO_CHECKED' => array(
				//����ȡ��֪ͨ
				'template_id' => 'rhwN3X2zeEZq319OorcKkXIZ4OqslNDWWsoStLCqfhs',
				'data' => array(
					'first' => array('value'=>'��Լ��ģ�ء�{nickname}������48Сʱδǩ����ϵͳ��ȡ��Լ�Ľ��ס�', 'color'=>''),
					'keyword1' => array('value'=>'{order_no}', 'color'=>''),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'�˿��2����������ԭ·�˻أ���������ء�ԼԼ��APP�鿴�����κ�������µ�ͷ���{service}', 'color'=>''),
				),
			),
			
			//Լ��24Сʱ����˿�֪ͨ
			'G_PAI_WEIXIN_DATE_CANCEL_IMMEDIATELY' => array(
				//�˿�ɹ�֪ͨ
				'template_id' => 'HJXQWIG_R24bjizpTR260weq_c9V6Tkg8YCc9Dg2HEM',
				'data' => array(
					'first' => array('value'=>'��ã��˿������ѳɹ���Լ�ķ������˻�����ġ�ԼԼ���˻�', 'color'=>''),
					'orderProductPrice' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'{nickname}', 'color'=>''),
					'orderName' => array('value'=>'{order_no}', 'color'=>''),
					'remark' => array('value'=>'�˿��2����������ԭ·�˻أ���������ء�ԼԼ��APP�鿴�����κ�������µ�ͷ���{service}', 'color'=>''),
				),
			),
			
			//��Լ��ʱ�仹��1Сʱ,����˫�� ��ʱǩ��
			'G_PAI_WEIXIN_CODE_PREV' => array(
				//����״̬����
				'template_id' => 'o0LVFa5Dfm__ZEpmDG-MHjZi_cJM3gwEOZE4X6BY8tU',
				'data' => array(
					'first' => array('value'=>'��ã�����ģ�ء�{nickname}����Լ�Ļ���1Сʱ�Ϳ�ʼ�ˡ�', 'color'=>''),
					'OrderSn' => array('value'=>'{order_no}', 'color'=>''),
					'OrderStatus' => array('value'=>'δǩ��', 'color'=>''),
					'remark' => array('value'=>'ף��Լ����졣', 'color'=>''),
				),
			),
			
			//����ɹ�֪ͨ����ת����ҳ��
			'G_PAI_WEIXIN_DATE_PAID' => array(
				//����֧���ɹ�
				'template_id' => 'QFZasU33pkkMC2Ro3MF1J9ENmc4Eb9d-h1y1FET9dis',
				'data' => array(
					'first' => array('value'=>'��ϲ�㣬Լ�ķ�����֧���ɹ���', 'color'=>''),
					'orderMoneySum' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'ģ�ء�{nickname}��', 'color'=>''),
					'Remark' => array('value'=>'�����ĵȴ�ģ�ػ�Ӧ�������ء�ԼԼ��app˽��Ta�ɡ�����Ȳ�����������{service}�����ǰ��㣡', 'color'=>''),
				),
			),
			
			//V2ʵ����֤���ͨ��
			'G_PAI_WEIXIN_CREDIT2_PASSED' => array(
				//��Ա�����������
				'template_id' => 'KEDfYbwLODQ99bLnPG_1dunuXgX2qYVPIX-QTg1U5gM',
				'data' => array(
					'first' => array('value'=>'���ύ��V2��֤�����������ˡ�', 'color'=>''),
					'keyword1' => array('value'=>'ͨ��', 'color'=>'#FF0000'),
					'keyword2' => array('value'=>'������Ϸ���Ҫ��', 'color'=>''),
					'remark' => array('value'=>'�������Ѿ���V2�ȼ�����Ӱ����ˣ���ȥԼ�İɡ�', 'color'=>''),
				),
			),
			
			//V2ʵ����֤��˲�ͨ��
			'G_PAI_WEIXIN_CREDIT2_REFUSED' => array(
				//��Ա�����������
				'template_id' => 'KEDfYbwLODQ99bLnPG_1dunuXgX2qYVPIX-QTg1U5gM',
				'data' => array(
					'first' => array('value'=>'���ύ��V2��֤�����������ˡ�', 'color'=>''),
					'keyword1' => array('value'=>'δͨ��', 'color'=>'#FF0000'),
					'keyword2' => array('value'=>'���֤��Ƭģ��', 'color'=>''),
					'remark' => array('value'=>'���㲹�䲢�޸����Ϻ������ύ��лл��', 'color'=>''),
				),
			),
			
			//���ĸ���ɹ�֪ͨ����ת����ҳ������ѡ
			'G_PAI_WEIXIN_WAIPAI_PAID_A' => array(
				//����֧���ɹ�
				'template_id' => 'QFZasU33pkkMC2Ro3MF1J9ENmc4Eb9d-h1y1FET9dis',
				'data' => array(
					'first' => array('value'=>'��ϲ���Ϊ���ѡ�û������Ļ������֧���ɹ���', 'color'=>''),
					'orderMoneySum' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'���{event_title}��', 'color'=>''),
					'Remark' => array('value'=>'�����ĵȴ���֯�߻�Ӧ�������ء�ԼԼ��app˽��Ta�ɡ�����Ȳ�����������{service}�����ǰ��㣡', 'color'=>''),
				),
			),
			
			//���ĸ���ɹ�֪ͨ����ת����ҳ������
			'G_PAI_WEIXIN_WAIPAI_PAID_B' => array(
				//����֧���ɹ�
				'template_id' => 'QFZasU33pkkMC2Ro3MF1J9ENmc4Eb9d-h1y1FET9dis',
				'data' => array(
					'first' => array('value'=>'��ϲ���Ϊ�����Ա�����Ļ������֧���ɹ���', 'color'=>''),
					'orderMoneySum' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'���{event_title}��', 'color'=>''),
					'Remark' => array('value'=>'�����ĵȴ���֯�߻�Ӧ�������ء�ԼԼ��app˽��Ta�ɡ�����Ȳ�����������{service}�����ǰ��㣡', 'color'=>''),
				),
			),
			
			//����ǩ���ɹ�����ת��������ҳ��
			'G_PAI_WEIXIN_WAIPAI_CODE_CHECKED' => array(
				//����״̬����
				'template_id' => 'o0LVFa5Dfm__ZEpmDG-MHjZi_cJM3gwEOZE4X6BY8tU',
				'data' => array(
					'first' => array('value'=>'��ã����{event_title}����ǩ���ɹ���ף���������', 'color'=>''),
					'OrderSn' => array('value'=>'{order_no}', 'color'=>''),
					'OrderStatus' => array('value'=>'��ǩ��', 'color'=>''),
					'remark' => array('value'=>'', 'color'=>''),
				),
			),
			
			//���Ļȷ����ɣ���ת����ҳ�棩
			'G_PAI_WEIXIN_WAIPAI_FINISHED' => array(
				//�������֪ͨ
				'template_id' => '9UhK82XxNZ973sZ2SEvKDmI0SMa97IJ0zPslb83TtcU',
				'data' => array(
					'first' => array('value'=>'��ϲ�㣬����˻��{event_title}��������', 'color'=>''),
					'keynote1' => array('value'=>'�����', 'color'=>''),
					'keynote2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'����������������֯�ߡ�{nickname}���ķ���', 'color'=>''),
				),
			),
			
			//���ģ�û��ɨ��,ϵͳ�Զ�ȫ���˿��ת����ҳ��
			'G_PAI_WEIXIN_WAIPAI_CODE_NO_CHECKED' => array(
				//����ȡ��֪ͨ
				'template_id' => 'rhwN3X2zeEZq319OorcKkXIZ4OqslNDWWsoStLCqfhs',
				'data' => array(
					'first' => array('value'=>'��μӵĻ��{event_title}������֯����ȷ�ϻ����������δǩ����ϵͳ��ȡ��Լ�Ľ��ס�', 'color'=>''),
					'keyword1' => array('value'=>'{order_no}', 'color'=>''),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'�˿��2����������ԭ·�˻أ���������ء�ԼԼ��APP�鿴�����κ�������µ�ͷ���{service}', 'color'=>''),
				),
			),
			
			//�����
			'G_PAI_WEIXIN_WAIPAI_SHARE_SUCCESS' => array(
				//�Ż݄���ȡ�ɹ�֪ͨ
				'template_id' => 'KDG4KAONZBtkauLnuhyw56FI2hRMhRny99jG1DLn2kw',
				'data' => array(
					'first' => array('value'=>'��ϲ�����ɹ���{amount}�Ѿ��������ԼԼ�˺�', 'color'=>''),
					'keyword1' => array('value'=>'ԼԼ�Ż݄�', 'color'=>''),
					'keyword2' => array('value'=>'{coupon_sn}', 'color'=>''),
					'keyword3' => array('value'=>'{end_date}', 'color'=>''),
					'remark' => array('value'=>'3�µװ汾����֮���¼app����ʹ�á�', 'color'=>''),
				),
			),
			
			//��������˱���
			'G_PAI_WEIXIN_WAIPAI_SHARE_PAID' => array(
				//�Ż݄���ȡ�ɹ�֪ͨ
				'template_id' => 'KDG4KAONZBtkauLnuhyw56FI2hRMhRny99jG1DLn2kw',
				'data' => array(
					'first' => array('value'=>'��ϲ�㣬������ͨ����ķ����ѳɹ��������Ϊ�˸�л��ķ���ԼԼ�Ѿ���{amount}Ԫ�ֽ��ֵ����Ǯ��������������Լ�ġ�', 'color'=>''),
					'keyword1' => array('value'=>'ԼԼ�Ż݄�', 'color'=>''),
					'keyword2' => array('value'=>'{coupon_sn}', 'color'=>''),
					'keyword3' => array('value'=>'{end_date}', 'color'=>''),
					'remark' => array('value'=>'3�µװ汾����֮���¼app����ʹ�á�', 'color'=>''),
				),
			),
			
			//ϵͳ����ά��֪ͨ
			'G_PAI_WEIXIN_SYSTEM_UPGRADE' => array(
				//ϵͳ����ά��֪ͨ
				'template_id' => 'iNqWaa0N-K5rDGHZebbyz5FNZn_lBXxf0zQv-Z6O5_o',
				'data' => array(
					'first' => array('value'=>'', 'color'=>''),
					'keyword1' => array('value'=>"�װ���ԼԼer��ԼԼ���ڽ��ڻ����������°漴���������ڴ��ڼ䣬ԼԼ���ںŵĽ��׹�����ͣʹ�ã������������㣬�����½⡣�°���º�Ҳ���һʱ�����֪ͨ~\r\nע�����Ļ�����ճ�ʹ�ã�", 'color'=>''),
					'keyword2' => array('value'=>'2015��8��3��', 'color'=>''),
					'keyword3' => array('value'=>'��Ⱥ�ϵͳ֪ͨ', 'color'=>''),
					'remark' => array('value'=>'', 'color'=>''),
				),
			),
			
			//�̳Ƕ�����Ϣ����
			'G_PAI_WEIXIN_MALL_ORDER_STATUS' => array(
				//������Ϣ����
				'template_id' => '0Rb5BVNvQMfe3yoxlUTfDjgYKPD-l80NCd8JOqBkzhg',
				'data' => array(
					'first' => array('value'=>'{first}', 'color'=>''),
					'keyword1' => array('value'=>'{goods_name}', 'color'=>''),
					'keyword2' => array('value'=>'{total_amount}', 'color'=>'#FF0000'),
					'keyword3' => array('value'=>'{status_str}', 'color'=>''),
					'remark' => array('value'=>'{remark}', 'color'=>''),
				),
			),
			
			//ϵͳ֪ͨ
			'G_PAI_WEIXIN_SYSTEM_NOTICE' => array(
				//ϵͳ֪ͨ
				'template_id' => 'Em5smvXurTpRkZw4BIHEhku5A0hNGhtkv_1jJvyeiQ4',
				'data' => array(
					'first' => array('value'=>'{title}', 'color'=>''),
					'keyword1' => array('value'=>'ԼԼ', 'color'=>''),
					'keyword2' => array('value'=>'{content}', 'color'=>''),
					'remark' => array('value'=>'���κ�������µ�ͷ���{service}', 'color'=>''),
				),
			),
			
		);
		
		$user_id = intval($user_id);
		
		$template_code = trim($template_code);
		$template_info = $template_list[$template_code];
		
		if( !is_array($data) ) $data = array();
		$data['service'] = $this->service;
		$to_url = trim($to_url);
		$top_color = trim($top_color);
		
		if( $user_id<1 || strlen($template_code)<1 || !is_array($template_info) || empty($template_info) )
		{
			return false;
		}
		
		$template_id = trim($template_info['template_id']);
		if( strlen($template_id)<1 )
		{
			return false;
		}
		
		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
		$bind_info = $bind_weixin_obj->get_bind_info_by_user_id($user_id);
		$to_user = trim($bind_info['open_id']);
		if( strlen($to_user)<1 )
		{
			return false;
		}
		
		$template_data = $template_info['data'];
		if( !is_array($template_data) ) $template_data = array();
		foreach ($template_data as $key=>$info)
		{
			$value = trim($info['value']);
			foreach ($data as $k=>$v)
			{
				$value = preg_replace('/\{\s*'.$k.'\s*\}/isU', $v, $value);
			}
			$info['value'] = $value;
			$template_data[$key] = $info;
		}
		
		$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
		return $weixin_helper_obj->wx_message_template_send($this->bind_id, $to_user, $template_id, $template_data, $to_url, $top_color);
	}
	
}
