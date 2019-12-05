<?php
/**
 * 微信公众号类
 * @author Henry
 */

class pai_weixin_pub_class extends POCO_TDG
{
	/**
	 * 微信绑定ID
	 * @var int
	 */
	private $bind_id = 0;
	
	/**
	 * 应用ID
	 * @var string
	 */
	private $app_id = '';
	
	/**
	 * 应用密钥
	 * @var string
	 */
	private $app_secret = '';
	
	/**
	 * 网页授权跳回链接
	 * @var string
	 */
	private $redirect_uri = '';
	
	/**
	 * 客服号码
	 * @var string
	 */
	private $service = '4000-82-9003';
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->bind_id = 1;
		$this->app_id = POCO_APP_PAI::ini('weixin_pub/app_id');
		$this->app_secret = POCO_APP_PAI::ini('weixin_pub/app_secret');
		$this->redirect_uri = POCO_APP_PAI::ini('weixin_pub/redirect_uri');
	}
	
	/**
	 * 指定微信用户表
	 */
	private function set_weixin_user_tbl()
	{
		$this->setServerId(101);
		$this->setDBName('pai_db');
		$this->setTableName('pai_weixin_user_tbl');
	}
	
	/**
	 * 指定微信模板消息表
	 */
	private function set_weixin_template_message_tbl()
	{
		$this->setServerId(101);
		$this->setDBName('pai_log_db');
		$this->setTableName('pai_weixin_template_message_tbl');
	}
	
	/**
	 * GBK转成UTF-8
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
	 * UTF-8转成GBK
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
	 * 发出HTTP请求
	 * @param string $url
	 * @param string $method GET|POST
	 * @param string|array $postfields 这个参数可以通过urlencoded后的字符串类似'para1=val1&para2=val2&...'或使用一个以字段名为键值，字段数据为值的数组。如果value是一个数组，Content-Type头将会被设置成multipart/form-data。
	 * @param array $headers 例如 array('Content-type: text/plain', 'Content-length: 100')
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
	 * 保存微信用户
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
	 * 通过user_id获取微信用户的基本信息
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
	 * 获取微信用户
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
	 * 获取最近一次关注
	 * @param int $bind_id
	 * @param string $open_id
	 */
	public function get_receive_info_by_subscribe($open_id)
	{
		$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
		return $weixin_helper_obj->get_receive_info_by_subscribe($this->bind_id, $open_id);
	}
	
	/**
	 * 获取access_token
	 * @param string $b_cache
	 * @return string
	 */
	public function get_access_token($b_cache=true)
	{
		$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
		return $weixin_helper_obj->wx_get_access_token($this->bind_id, $b_cache);
	}
	
	/**
	 * 获取网页授权链接
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
	 * 获取网页授权信息
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
		
		//保存cache
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
	 * 获取网页授权信息，来自缓存
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
	 * 检查授权信息
	 * @param string $open_id
	 * @param string $access_token 网页授权接口调用凭证
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
	 * 获取用户信息
	 * @param string $open_id
	 * @param string $access_token 网页授权接口调用凭证
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
	 * 模板消息发送，根据用户ID
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
				//微信绑定成功通知
				'template_id' => '8Ds4za-KyjydcDBxX8hKQ7_Wd-xyAN8IM3OhVXP5Yco',
				'data' => array(
					'first' => array('value'=>'恭喜你绑定成功！', 'color'=>''),
					'keyword1' => array('value'=>'{nickname}', 'color'=>''),
					'keyword2' => array('value'=>'{cellphone}', 'color'=>''),
					'keyword3' => array('value'=>'约拍、提醒等', 'color'=>''),
					'remark' => array('value'=>'欢迎使用约约！', 'color'=>''),
				),
			),
			
			//模特拒绝约拍邀请
			'G_PAI_WEIXIN_DATE_REFUSED' => array(
				//约拍被拒通知
				'template_id' => 'rzGAKpoO7xPZOJGwRQEq9QDT9ZxIkWtIp9adySVTOws',
				'data' => array(
					'first' => array('value'=>'你好，模特【{nickname}】没接受你的约拍邀请。', 'color'=>''),
					'keyword1' => array('value'=>'{datetime}', 'color'=>''),
					'keyword2' => array('value'=>'{address}', 'color'=>''),
					'keyword3' => array('value'=>'{reason}', 'color'=>''),
					'remark' => array('value'=>'模特【{nickname}】没有接受你的约拍邀请，退款将在2个工作日内原路退回。更多选择，拨打{service}，客服帮你约~', 'color'=>''),
				),
			),
			
			//模特接受约拍邀请
			'G_PAI_WEIXIN_DATE_ACCEPTED' => array(
				//报名成功通知
				'template_id' => 'o5RZJVZfpjrAP-tfJCHg_xK7_UhqFb7El8BQ61Q2yoc',
				'data' => array(
					'first' => array('value'=>'你好，模特【{nickname}】已同意了你的约拍申请', 'color'=>''),
					'keynote1' => array('value'=>'私拍', 'color'=>''),
					'keynote2' => array('value'=>'{datetime}', 'color'=>''),
					'keynote3' => array('value'=>'{address}', 'color'=>''),
					'remark' => array('value'=>"点击查看约拍二维码。\r\n请在活动当天出示此二维码或数字密码给模特进行扫码确认。如有问题，请拔打客服电话{service}", 'color'=>''),
				),
			),
			
			//签到成功
			'G_PAI_WEIXIN_CODE_CHECKED' => array(
				//订单状态更新
				'template_id' => 'o0LVFa5Dfm__ZEpmDG-MHjZi_cJM3gwEOZE4X6BY8tU',
				'data' => array(
					'first' => array('value'=>'你好，模特【{nickname}】已签到成功。祝你约拍愉快', 'color'=>''),
					'OrderSn' => array('value'=>'{order_no}', 'color'=>''),
					'OrderStatus' => array('value'=>'已签到', 'color'=>''),
					'remark' => array('value'=>'', 'color'=>''),
				),
			),
			
			/*
			//模特确认完成
			'G_PAI_WEIXIN_DATE_FINISHED' => array(
				//服务完成通知
				'template_id' => '9UhK82XxNZ973sZ2SEvKDmI0SMa97IJ0zPslb83TtcU',
				'data' => array(
					'first' => array('value'=>'恭喜你，完成了与模特【{nickname}】的约拍', 'color'=>''),
					'keynote1' => array('value'=>'已完成', 'color'=>''),
					'keynote2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'点击这里可以评价模特【{nickname}】的服务', 'color'=>''),
				),
			),
			*/
			
			//模特评价后
			'G_PAI_WEIXIN_MT_CMT' => array(
				//评价完成结果通知
				'template_id' => '2hwsvJHRCYjaHPVHMjF2tCF-ARkqZQq99YqHJ0qp_0Y',
				'data' => array(
					'first' => array('value'=>'你好，模特【{nickname}】已对你作出评价', 'color'=>''),
					'Content1' => array('value'=>'', 'color'=>''),
					'Good' => array('value'=>'{datetime} 约拍', 'color'=>''),
					'remark' => array('value'=>'详情请点击查看。', 'color'=>''),
				),
			),
			
			//约拍超时触发自动取消约拍并退款
			//摄影师提交约拍，模特没操作，超时
			//摄影师提交约拍，模特接受，投影师申请退款，模特没有操作，超时
			'G_PAI_WEIXIN_DATE_IGNORE' => array(
				//订单取消通知
				'template_id' => 'rhwN3X2zeEZq319OorcKkXIZ4OqslNDWWsoStLCqfhs',
				'data' => array(
					'first' => array('value'=>"你好，模特超时没有操作，约拍已取消并退款。\r\n模特昵称：{nickname}", 'color'=>''),
					'keyword1' => array('value'=>'{order_no}', 'color'=>''),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'退款将在2个工作日内原路退回，详情可下载“约约”APP查看。有任何问题可致电客服：{service}', 'color'=>''),
				),
			),
			
			/*
			//模特发起私聊，摄影师没有APP
			'G_PAI_WEIXIN_MT_CHAT' => array(
				//来访提醒通知
				'template_id' => 'FRCbpPEowXaiAhrcH_PittvtM7_sJ1_-7bBE6Jvp61s',
				'data' => array(
					'first' => array('value'=>'模特【{nickname}】 给你发来一条信息，马上下载app查看并与模特进一步沟通吧。', 'color'=>''),
					'user' => array('value'=>'{nickname}  ID：{user_id}', 'color'=>''),
					'reason' => array('value'=>'约拍详情沟通', 'color'=>''),
					'address' => array('value'=>'“约约”app', 'color'=>''),
					'remark' => array('value'=>'马上下载app查看消息，并与模特进一步沟通吧，有任何问题可致电客服：{service}', 'color'=>''),
				),
			),
			*/
			
			//商家发起私聊，消费者没有APP
			'G_PAI_WEIXIN_MT_CHAT' => array(
				//系统通知
				'template_id' => 'Em5smvXurTpRkZw4BIHEhku5A0hNGhtkv_1jJvyeiQ4',
				'data' => array(
					'first' => array('value'=>'商家【{nickname}】给你发来一条信息，请在APP中进行查看', 'color'=>''),
					'keyword1' => array('value'=>'{nickname}  ID：{user_id}', 'color'=>''),
					'keyword2' => array('value'=>'马上下载app查看，并与商家进一步沟通吧。', 'color'=>''),
					'remark' => array('value'=>'有任何问题可致电客服：{service}', 'color'=>''),
				),
			),
			
			//模特同意取消
			'G_PAI_WEIXIN_DATE_CANCEL_ACCEPTED' => array(
				//订单取消通知
				'template_id' => 'rhwN3X2zeEZq319OorcKkXIZ4OqslNDWWsoStLCqfhs',
				'data' => array(
					'first' => array('value'=>"你好，模特【{nickname}】已同意取消约拍。\r\n模特昵称：{nickname}", 'color'=>''),
					'keyword1' => array('value'=>'{order_no}', 'color'=>''),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'退款将在2个工作日内原路退回，详情可下载“约约”APP查看。有任何问题可致电客服：{service}', 'color'=>''),
				),
			),
			
			/*
			//模特不同意取消
			'G_PAI_WEIXIN_DATE_CANCEL_REFUSED' => array(
				//约拍取消申请驳回通知
				'template_id' => 'j7qAP2jpIKecNC4Iyb9x1aTH62gwSGMOXUCghGEDDGg',
				'data' => array(
					'first' => array('value'=>'你好，模特【{nickname}】不同意取消约拍。', 'color'=>''),
					'keyword1' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'快选择下载“约约”app与模特进一步沟通吧，有任何问题可致电客服：{service}。', 'color'=>''),
				),
			),
			*/
			
			//强制退款成功
			'G_PAI_WEIXIN_DATE_CANCEL_FORCE' => array(
				//退款成功通知
				'template_id' => 'HJXQWIG_R24bjizpTR260weq_c9V6Tkg8YCc9Dg2HEM',
				'data' => array(
					'first' => array('value'=>'你好，强制退款已成功，约拍费用的70%已退回到你的“约约”账户中。', 'color'=>''),
					'orderProductPrice' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'{nickname}', 'color'=>''),
					'orderName' => array('value'=>'{order_no}', 'color'=>''),
					'remark' => array('value'=>'退款将在2个工作日内原路退回，详情可下载“约约”APP查看。有任何问题可致电客服：{service}。', 'color'=>''),
				),
			),
			
			//双方没有签到
			'G_PAI_WEIXIN_CODE_NO_CHECKED' => array(
				//订单取消通知
				'template_id' => 'rhwN3X2zeEZq319OorcKkXIZ4OqslNDWWsoStLCqfhs',
				'data' => array(
					'first' => array('value'=>'你约拍模特【{nickname}】，已48小时未签到，系统已取消约拍交易。', 'color'=>''),
					'keyword1' => array('value'=>'{order_no}', 'color'=>''),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'退款将在2个工作日内原路退回，详情可下载“约约”APP查看。有任何问题可致电客服：{service}', 'color'=>''),
				),
			),
			
			//约拍24小时外的退款通知
			'G_PAI_WEIXIN_DATE_CANCEL_IMMEDIATELY' => array(
				//退款成功通知
				'template_id' => 'HJXQWIG_R24bjizpTR260weq_c9V6Tkg8YCc9Dg2HEM',
				'data' => array(
					'first' => array('value'=>'你好，退款申请已成功，约拍费用已退还到你的“约约”账户', 'color'=>''),
					'orderProductPrice' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'{nickname}', 'color'=>''),
					'orderName' => array('value'=>'{order_no}', 'color'=>''),
					'remark' => array('value'=>'退款将在2个工作日内原路退回，详情可下载“约约”APP查看。有任何问题可致电客服：{service}', 'color'=>''),
				),
			),
			
			//离约拍时间还有1小时,提醒双方 即时签到
			'G_PAI_WEIXIN_CODE_PREV' => array(
				//订单状态更新
				'template_id' => 'o0LVFa5Dfm__ZEpmDG-MHjZi_cJM3gwEOZE4X6BY8tU',
				'data' => array(
					'first' => array('value'=>'你好，你与模特【{nickname}】的约拍还有1小时就开始了。', 'color'=>''),
					'OrderSn' => array('value'=>'{order_no}', 'color'=>''),
					'OrderStatus' => array('value'=>'未签到', 'color'=>''),
					'remark' => array('value'=>'祝你约拍愉快。', 'color'=>''),
				),
			),
			
			//付款成功通知（跳转下载页）
			'G_PAI_WEIXIN_DATE_PAID' => array(
				//订单支付成功
				'template_id' => 'QFZasU33pkkMC2Ro3MF1J9ENmc4Eb9d-h1y1FET9dis',
				'data' => array(
					'first' => array('value'=>'恭喜你，约拍费用已支付成功。', 'color'=>''),
					'orderMoneySum' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'模特【{nickname}】', 'color'=>''),
					'Remark' => array('value'=>'请耐心等待模特回应，或下载“约约”app私信Ta吧。如果迫不及待，拨打{service}，我们帮你！', 'color'=>''),
				),
			),
			
			//V2实名认证审核通过
			'G_PAI_WEIXIN_CREDIT2_PASSED' => array(
				//会员资料审核提醒
				'template_id' => 'KEDfYbwLODQ99bLnPG_1dunuXgX2qYVPIX-QTg1U5gM',
				'data' => array(
					'first' => array('value'=>'你提交的V2认证资料已完成审核。', 'color'=>''),
					'keyword1' => array('value'=>'通过', 'color'=>'#FF0000'),
					'keyword2' => array('value'=>'审核资料符合要求', 'color'=>''),
					'remark' => array('value'=>'你现在已经是V2等级的摄影身份了，快去约拍吧。', 'color'=>''),
				),
			),
			
			//V2实名认证审核不通过
			'G_PAI_WEIXIN_CREDIT2_REFUSED' => array(
				//会员资料审核提醒
				'template_id' => 'KEDfYbwLODQ99bLnPG_1dunuXgX2qYVPIX-QTg1U5gM',
				'data' => array(
					'first' => array('value'=>'你提交的V2认证资料已完成审核。', 'color'=>''),
					'keyword1' => array('value'=>'未通过', 'color'=>'#FF0000'),
					'keyword2' => array('value'=>'身份证照片模糊', 'color'=>''),
					'remark' => array('value'=>'请你补充并修改资料后重新提交，谢谢。', 'color'=>''),
				),
			),
			
			//外拍付款成功通知（跳转下载页），正选
			'G_PAI_WEIXIN_WAIPAI_PAID_A' => array(
				//订单支付成功
				'template_id' => 'QFZasU33pkkMC2Ro3MF1J9ENmc4Eb9d-h1y1FET9dis',
				'data' => array(
					'first' => array('value'=>'恭喜你成为活动正选用户，外拍活动费用已支付成功。', 'color'=>''),
					'orderMoneySum' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'活动【{event_title}】', 'color'=>''),
					'Remark' => array('value'=>'请耐心等待组织者回应，或下载“约约”app私信Ta吧。如果迫不及待，拨打{service}，我们帮你！', 'color'=>''),
				),
			),
			
			//外拍付款成功通知（跳转下载页），候补
			'G_PAI_WEIXIN_WAIPAI_PAID_B' => array(
				//订单支付成功
				'template_id' => 'QFZasU33pkkMC2Ro3MF1J9ENmc4Eb9d-h1y1FET9dis',
				'data' => array(
					'first' => array('value'=>'恭喜你成为活动候补人员，外拍活动费用已支付成功。', 'color'=>''),
					'orderMoneySum' => array('value'=>'{amount}', 'color'=>'#FF0000'),
					'orderProductName' => array('value'=>'活动【{event_title}】', 'color'=>''),
					'Remark' => array('value'=>'请耐心等待组织者回应，或下载“约约”app私信Ta吧。如果迫不及待，拨打{service}，我们帮你！', 'color'=>''),
				),
			),
			
			//外拍签到成功（跳转订单详情页）
			'G_PAI_WEIXIN_WAIPAI_CODE_CHECKED' => array(
				//订单状态更新
				'template_id' => 'o0LVFa5Dfm__ZEpmDG-MHjZi_cJM3gwEOZE4X6BY8tU',
				'data' => array(
					'first' => array('value'=>'你好，活动【{event_title}】已签到成功。祝你拍摄愉快', 'color'=>''),
					'OrderSn' => array('value'=>'{order_no}', 'color'=>''),
					'OrderStatus' => array('value'=>'已签到', 'color'=>''),
					'remark' => array('value'=>'', 'color'=>''),
				),
			),
			
			//外拍活动确认完成（跳转评价页面）
			'G_PAI_WEIXIN_WAIPAI_FINISHED' => array(
				//服务完成通知
				'template_id' => '9UhK82XxNZ973sZ2SEvKDmI0SMa97IJ0zPslb83TtcU',
				'data' => array(
					'first' => array('value'=>'恭喜你，完成了活动【{event_title}】的拍摄', 'color'=>''),
					'keynote1' => array('value'=>'已完成', 'color'=>''),
					'keynote2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'点击这里可以评价组织者【{nickname}】的服务', 'color'=>''),
				),
			),
			
			//外拍，没被扫码,系统自动全额退款（跳转下载页）
			'G_PAI_WEIXIN_WAIPAI_CODE_NO_CHECKED' => array(
				//订单取消通知
				'template_id' => 'rhwN3X2zeEZq319OorcKkXIZ4OqslNDWWsoStLCqfhs',
				'data' => array(
					'first' => array('value'=>'你参加的活动【{event_title}】，组织者已确认活动结束，你仍未签到，系统已取消约拍交易。', 'color'=>''),
					'keyword1' => array('value'=>'{order_no}', 'color'=>''),
					'keyword2' => array('value'=>'{datetime}', 'color'=>''),
					'remark' => array('value'=>'退款将在2个工作日内原路退回，详情可下载“约约”APP查看。有任何问题可致电客服：{service}', 'color'=>''),
				),
			),
			
			//分享后
			'G_PAI_WEIXIN_WAIPAI_SHARE_SUCCESS' => array(
				//优惠劵领取成功通知
				'template_id' => 'KDG4KAONZBtkauLnuhyw56FI2hRMhRny99jG1DLn2kw',
				'data' => array(
					'first' => array('value'=>'恭喜你分享成功。{amount}已经打入你的约约账号', 'color'=>''),
					'keyword1' => array('value'=>'约约优惠劵', 'color'=>''),
					'keyword2' => array('value'=>'{coupon_sn}', 'color'=>''),
					'keyword3' => array('value'=>'{end_date}', 'color'=>''),
					'remark' => array('value'=>'3月底版本升级之后登录app即可使用。', 'color'=>''),
				),
			),
			
			//分享后有人报名
			'G_PAI_WEIXIN_WAIPAI_SHARE_PAID' => array(
				//优惠劵领取成功通知
				'template_id' => 'KDG4KAONZBtkauLnuhyw56FI2hRMhRny99jG1DLn2kw',
				'data' => array(
					'first' => array('value'=>'恭喜你，你朋友通过你的分享，已成功报名活动。为了感谢你的分享，约约已经将{amount}元现金返现到你的钱包，可用于其他约拍。', 'color'=>''),
					'keyword1' => array('value'=>'约约优惠劵', 'color'=>''),
					'keyword2' => array('value'=>'{coupon_sn}', 'color'=>''),
					'keyword3' => array('value'=>'{end_date}', 'color'=>''),
					'remark' => array('value'=>'3月底版本升级之后登录app即可使用。', 'color'=>''),
				),
			),
			
			//系统升级维护通知
			'G_PAI_WEIXIN_SYSTEM_UPGRADE' => array(
				//系统升级维护通知
				'template_id' => 'iNqWaa0N-K5rDGHZebbyz5FNZn_lBXxf0zQv-Z6O5_o',
				'data' => array(
					'first' => array('value'=>'', 'color'=>''),
					'keyword1' => array('value'=>"亲爱的约约er，约约将于近期华丽升级，新版即将发布。在此期间，约约公众号的交易功能暂停使用，给您带来不便，敬请谅解。新版更新后也会第一时间进行通知~\r\n注：外拍活动可以照常使用！", 'color'=>''),
					'keyword2' => array('value'=>'2015年8月3日', 'color'=>''),
					'keyword3' => array('value'=>'请等候系统通知', 'color'=>''),
					'remark' => array('value'=>'', 'color'=>''),
				),
			),
			
			//商城订单消息提醒
			'G_PAI_WEIXIN_MALL_ORDER_STATUS' => array(
				//订单消息提醒
				'template_id' => '0Rb5BVNvQMfe3yoxlUTfDjgYKPD-l80NCd8JOqBkzhg',
				'data' => array(
					'first' => array('value'=>'{first}', 'color'=>''),
					'keyword1' => array('value'=>'{goods_name}', 'color'=>''),
					'keyword2' => array('value'=>'{total_amount}', 'color'=>'#FF0000'),
					'keyword3' => array('value'=>'{status_str}', 'color'=>''),
					'remark' => array('value'=>'{remark}', 'color'=>''),
				),
			),
			
			//系统通知
			'G_PAI_WEIXIN_SYSTEM_NOTICE' => array(
				//系统通知
				'template_id' => 'Em5smvXurTpRkZw4BIHEhku5A0hNGhtkv_1jJvyeiQ4',
				'data' => array(
					'first' => array('value'=>'{title}', 'color'=>''),
					'keyword1' => array('value'=>'约约', 'color'=>''),
					'keyword2' => array('value'=>'{content}', 'color'=>''),
					'remark' => array('value'=>'有任何问题可致电客服：{service}', 'color'=>''),
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
