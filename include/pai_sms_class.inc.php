<?php
/**
 * 短信、校验类
 * @author Henry
 */

class pai_sms_class extends POCO_TDG
{
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
	}
	
	/**
	 * 获取短信内容
	 * @param string $group_key
	 * @param array $data
	 * @return string
	 */
	private function get_sms_content($group_key, $data)
	{
		$group_key = trim($group_key);
		
		if( !is_array($data) ) $data = array();
		$data['service'] = $this->service;
		
		$tpl_list = array(
			'G_PAI_USER_REG_VERIFY' => '您的注册验证码是：{verify_code}。如非本人操作，请回复TD不再接收。客服电话：{service}',
			'G_PAI_USER_PASSWORD_VERIFY' => '您修改密码的验证码是：{verify_code}。如非本人操作，请回复TD不再接收。客服电话：{service}',
			'G_PAI_USER_LOGIN_VERIFY'  => '{verify_code}（约约验证码，仅用于登录，请勿告知他人），客服电话：{service}',
			'G_PAI_USER_BIND_VERIFY'  => '{verify_code}（约约验证码，仅用于更换绑定，请勿告知他人），客服电话：{service}',
			'G_PAI_USER_PHONE_CHANGE' => '您正在修改绑定手机号，新号码为{phone}。如果不是本人操作，请马上联系客服：{service}',
			'G_ECPAY_USER_LOGIN_VERIFY'  => '{verify_code}（约约验证码，仅用于登录，请勿告知他人），支付系统',
			'G_ECPAY_ACCOUNT_RECHARGE_VERIFY'  => '{verify_code}（约约验证码，仅用于账户充值，请勿告知他人），支付系统',
			'G_ECPAY_ACCOUNT_WITHDRAW_VERIFY'  => '{verify_code}（约约验证码，仅用于账户提现，请勿告知他人），支付系统',
			'G_ECPAY_ACCOUNT_TRANSFER_VERIFY'  => '{verify_code}（约约验证码，仅用于账户转账，请勿告知他人），支付系统',
			'G_PAI_RECHARGE_SUCCESS'  => '你通过支付宝成功充值 {amount}元 ，如有疑问请及时联系，客服电话：{service}',
			'G_PAI_WITHDRAW_VERIFY'   => '您的提现验证码是：{verify_code}。如非本人操作，请回复TD不再接收。客服电话：{service}',
			'G_PAI_WITHDRAW_SUBMIT'   => '你的账号{phone} 申请提现的 {amount}元将在1个工作日内到账，如有疑问请及时联系，客服电话：{service}',
			'G_PAI_WITHDRAW_FAIL'     => '你的账号{phone} 申请提现的 {amount}元的提现失败，请登录约约app查看原因，如有疑问请及时联系，客服电话：{service}',
			//'G_PAI_DATE_MT_AGREE'     => '恭喜您，模特“{mt_nickname}”已同意约拍，数字密码：{activity_code}，若拍摄时无法出示二维码签到，也可提供此数字密码给模特签到哦',
			'G_PAI_DATE_MT_AGREE'     => '恭喜您，模特“{mt_nickname}”已同意约拍，数字密码：{activity_code}，若拍摄时无法出示二维码签到，也可提供此数字密码给模特，请注意保存短信。更多拍摄细节，建议在“约约”与模特沟通，如有疑问请拨打{service}。',
			'G_PAI_DATE_MT_PENDING'   => '亲，摄影师[{pp_nickname}]给您发送了一单价值[{amount}元]的约拍邀请，赶紧登陆app处理吧，别让赚钱的机会溜走了。',
			'G_PAI_DATE_MT_IGNORE'    => '你约拍模特{mt_nickname}的申请已过期，费用将退还到你的账户。如有疑问请拨打{service}。',
			'G_PAI_DATE_CODE_NO_CHECKED' => '你约拍模特{mt_nickname}，已48小时未签到，系统已取消约拍交易，费用将退还到你的账户。如有疑问请拨打{service}。',
			'G_PAI_WAIPAI_ORG_CANCEL_REFUND_YUE' => '你参加的{event_title}活动第{table_num}场已被组织者取消，费用已退还到你“约约”钱包，请留意查看。如有疑问请拨打{service}',
			'G_PAI_WAIPAI_ORG_CANCEL_REFUND_THIRD' => '你参加的{event_title}活动第{table_num}场已被组织者取消，费用已原路返回，请留意查看。如有疑问请拨打{service}',
			'G_PAI_WAIPAI_CODE_NUMBER' => '恭喜你，{event_title}活动第{table_num}场报名成功，到场可通过APP出示二维码或提供数字密码：{activity_code} 签到，组织者联系方式：{cellphone} 。如有疑问请联系约约客服{service}',
			'G_PAI_WAIPAI_CODE_NUMBER_B' => '恭喜你，已成为{event_title}活动第{table_num}场的候补，到场可通过APP出示二维码或提供数字密码：{activity_code} 签到，组织者联系方式：{cellphone} 。如有疑问请联系约约客服{service}',
			'G_PAI_WAIPAI_CODE_NO_CHECKED' => '你参加的{event_title}活动第{table_num}场已结束，你未进行签到，系统已取消交易，费用即将退还到你的“约约”钱包，请留意查看。如有疑问请拨打{service}',
			'G_PAI_WAIPAI_PAID_A' => '恭喜你，{event_title}活动已报名成功，请注意保存短信。更多活动细节，建议在“约约”与组织者沟通，如有疑问请拨打{service}',
			'G_PAI_WAIPAI_PAID_B' => '恭喜你，已成为{event_title}活动的候补，请注意保存短信。更多活动细节，建议在“约约”与组织者沟通，如有疑问请拨打{service}',
			'G_PAI_USER_REG_COUPON' => '恭喜你注册成功，约约赠送你的{amount}已送至你的账号，3月底版本升级之后登录app即可使用。',
			'G_PAI_WAIPAI_UNPAID' => '你参加的{event_title}活动还有名额，赶快完成付费，去参加活动噢~',
			'G_PAI_WAIPAI_SHARE_SUCCESS' => '恭喜你分享成功。{amount}已经打入你的约约账号 ，3月底版本升级之后登录app即可使用。',
			'G_PAI_WAIPAI_SHARE_PAID' => '恭喜你，你朋友通过你的分享，已成功报名活动。为了感谢你的分享，约约已经将现金{amount}元返现到你的钱包，可用于其他约拍 ，3月底版本升级之后登录app即可使用。',
			'G_PAI_WAIPAI_FINISH_COUPON' => '你参加的{event_title}外拍活动已结束。POCO联同合作伙伴约约送上外拍活动优惠大礼包到你{phone}的手机账户，请下载约约领取：app.yueus.com',
			'G_PAI_TOPIC_MEETING_VERIFY' => '{verify_code}（约约验证码，仅用于报名，请勿告知他人），客服电话：{service}',
			'G_PAI_TOPIC_MEETING_SUCCESS' => '你已成功报名5月17号的“互联网+摄影跨界O2O高峰论坛 - {event_title}”。{enroll_num}张电子门票及峰会详情将发至你的邮箱，请稍后查收。届时凭电子门票或短信进行签到。如有疑问, 请拨打{service}',
			
			'G_PAI_TASK_SELLER_REG' => '约约已经帮你注册了服务者账号，账号{cellphone} 密码{password} 登录约约网址 www.yueus.com/task 去看看吧。约约客服电话{service}',
			'G_PAI_TASK_LEAD_SUBMIT_SELLER' => '约约提醒你有一个新的生意机会。点击 {url} 查看详情',
			'G_PAI_TASK_QUOTES_PAY_COINS_BUYER' => '你在约约上发布的{service_name}需求已经有人发来报价。快去看看吧',
			'G_PAI_TASK_QUOTES_READ_SELLER' => '{buyer_nickname}已经查看了你的报价，点击链接 {url} 去看看吧',
			'G_PAI_TASK_QUOTES_HIRE_BUYER' => '你已经与{seller_nickname}达成订单。请与Ta联系，确认选择后，再进行服务金的支付',
			'G_PAI_TASK_QUOTES_HIRE_SELLER' => '{buyer_nickname}已经选中了你，快去跟Ta联系吧。点击链接 {url} 查看订单详情',
			'G_PAI_TASK_QUOTES_PAY_SELLER' => '{buyer_nickname}已经支付服务金{pay_amount}元。认真完成工作后，向Ta要个好评吧',
			'G_PAI_TASK_LEAD_REMIND_SELLER' => '你有{lead_count}个新的生意机会。点击 {url} 查看详情',
			'G_PAI_TASK_LEAD_REMIND_SELLER_B' => '你报价的需求用户有新的操作反馈，请去订单详情查看。点击 {url} 查看详情',
			'G_PAI_TASK_LEAD_REMIND_SELLER_C' => '你有{lead_count}个新的生意机会。你报价的需求用户有新的操作反馈，请去订单详情查看。点击 {url} 查看详情',
			'G_PAI_TASK_QUOTES_FINISH_BUYER' => '约约已经收集了所有符合你需求的报价，去选择一个达成交易吧。',
			
			//http://s.yueus.com/normal_certificate_choose.php
			'G_PAI_MALL_SELLER_CERTIFICATE_BASIC_FAIL' => '你于{date}申请的约约商家基本认证，审核不通过，请电脑登录 s.yueus.com/xz.php 重新提交申请。',
			
			//http://s.yueus.com/normal_certificate_basic.php
			'G_PAI_MALL_SELLER_CERTIFICATE_BASIC_SUCCESS' => '你于{date}申请的约约商家基本认证已通过，请电脑登录 s.yueus.com/rz.php 进行服务认证。',
			
			//http://s.yueus.com/normal_certificate_basic.php
			'G_PAI_MALL_SELLER_CERTIFICATE_SERVICE_FAIL' => '你于{date}申请的约约商家服务认证，审核不通过，请电脑登录 s.yueus.com/rz.php 重新提交申请。',
			
			//http://s.yueus.com/goods_list.php
			'G_PAI_MALL_SELLER_CERTIFICATE_SERVICE_SUCCESS' => '你于{date}申请的约约商家服务认证已通过，请电脑登录 s.yueus.com/fw.php 继续添加服务。',
			
			//买家已支付，商家1小时内没有操作
			'G_PAI_MALL_ORDER_WAIT_CONFIRM_REMIND_SELLER' => '你好，用户[{buyer_nickname}]已支付了一笔价值[{amount}元]的订单，赶紧登录app处理吧，别让赚钱的机会溜走了。',
			
			//当有消费者咨询，必要时提醒商家要去下载商家版APP
			'G_PAI_MALL_SELLER_CHAT' => '尊敬的用户：您好！您于今日{datetime}收到一条用户咨询。由于约约版本升级，商家用户（模特、摄影师、培训师等）进行回复、接单，需下载安装约约商家版app，才能正常进行交易。请您登陆以下地址并下载安装约约商家版（http://s.yueus.com），感谢您的理解与配合。',
			
			'G_PAI_MALL_ORDER_CLOSE_WAIT_SIGN_FOR_SYSTEM_SELLER' => '你有一笔订单已被关闭（订单号：{order_sn}），如有疑问请联系约约客服：{service}', //系统关闭订单，提醒商家
			'G_PAI_MALL_ORDER_CLOSE_WAIT_SIGN_FOR_SYSTEM_BUYER' => '你的订单已被关闭（订单号：{order_sn}），已付款项将自动转入你的账户，请查收。如有疑问请联系约约客服{service}', //系统关闭订单，提醒消费者

			'G_PAI_MALL_ORDER_BUY_ACTIVITY_SUCCESS' => '恭喜你，{activity_name}活动 [{stage_title}]报名成功，到场通过APP出示二维码或数字密码：{activity_code} 签到，组织者联系方式：{cellphone} 。如有疑问请联系约约客服{service}',
		);
		
		if( !array_key_exists($group_key, $tpl_list) )
		{
			return '';
		}
		
		$content = trim($tpl_list[$group_key]);
		foreach ($data as $key=>$val)
		{
			$content = preg_replace('/\{\s*' . $key . '\s*\}/isU', $val, $content);
		}
		
		return $content;
	}
	
	/**
	 * 发送短信
	 * @param string $phone
	 * @param string $group_key
	 * @param string $data
	 * @param int $user_id
	 * @return boolean
	 */
	public function send_sms($phone, $group_key, $data, $user_id=0)
	{
		$phone = trim($phone);
		
		$group_key = trim($group_key);
		$group_key = strtoupper($group_key);
		if( !is_array($data) ) $data = array();
		
		$user_id = intval($user_id);
		
		//获取短信内容
		$content = $this->get_sms_content($group_key, $data);
		if( !preg_match('/^1\d{10}$/isU', $phone) || strlen($content)<1 )
		{
			return false;
		}
		
		//发送短信
		include_once(G_YUEYUE_ROOT_PATH . "/system_service/sms_service/poco_app_common.inc.php");
		//2015-11-13号由11切换到16 update by 黄石汉
		$product_type = 16; //默认0，10微网通知类，11微网验证码类，12微网营销类，16速码验证码类，18百悟验证码类
		$more_info = array( 'user_id'=>$user_id );
		$sms_obj = POCO::singleton('class_sms_v2');
		return $sms_obj->save_and_send_sms($phone, $content, $product_type);
	}
	
	/**
	 * 发送校验码短信
	 * @param string $phone
	 * @param string $group_key
	 * @param array $data
	 * @return boolean
	 */
	public function send_verify_code($phone, $group_key, $data, $user_id=0)
	{
		$phone = trim($phone);
		
		$group_key = trim($group_key);
		$group_key = strtoupper($group_key);
		if( !is_array($data) ) $data = array();
		
		$user_id = intval($user_id);
		
		//获取短信内容
		$content = $this->get_sms_content($group_key, $data);
		if( !preg_match('/^1\d{10}$/isU', $phone) || strlen($content)<1 )
		{
			return false;
		}
		
		//发送验证码
		include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');
		$verify_code_obj = POCO::singleton('phone_verify_code_class');
		$more_info = array('user_id' => $user_id);
		//2015-11-13号由11切换到16 update by 黄石汉
		$product_type = 16; //默认0，10微网通知类，11微网验证码类，12微网营销类，16速码验证码类，18百悟验证码类
		$ret = $verify_code_obj->send_verify_code($phone, $group_key, $content, 600, $more_info, $product_type);
		if( $ret['code']==1 || $ret['code']==-5)
		{
			//发送过于频繁的情况，也返回成功
			return true;
		}
		return false;
	}
	
	/**
	 * 校验
	 * @param string $phone
	 * @param string $group_key
	 * @param string $verify_code
	 * @param int $user_id
	 * @param boolean $b_del_verify_code
	 * @return boolean
	 */
	public function check_verify_code($phone, $group_key, $verify_code, $user_id=0, $b_del_verify_code=true)
	{
		$phone = trim($phone);
		$group_key = trim($group_key);
		$verify_code = trim($verify_code);
		$user_id = intval($user_id);
		
		if( !preg_match('/^1\d{10}$/isU', $phone) || strlen($group_key)<1 || strlen($verify_code)<1 )
		{
			return false;
		}
		
		include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');
		$verify_code_obj = POCO::singleton('phone_verify_code_class');
		$ret = $verify_code_obj->check_verify_code($phone, $group_key, $verify_code, $b_del_verify_code);
		if( $ret['code']!=1 )
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 删除
	 * @param string $phone
	 * @param string $group_key
	 * @return boolean
	 */
	public function del_verify_code($phone, $group_key)
	{
		$phone = trim($phone);
		$group_key = trim($group_key);
		
		if( !preg_match('/^1\d{10}$/isU', $phone) || strlen($group_key)<1 )
		{
			return false;
		}
		
		include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');
		$verify_code_obj = POCO::singleton('phone_verify_code_class');
		return $verify_code_obj->del_verify_code($phone, $group_key);
	}
	
	/**
	 * 发送注册校验码短信
	 * @param string $phone
	 * @param int $user_id
	 * @return boolean
	 */
	public function send_phone_reg_verify_code($phone, $user_id=0)
	{
		$group_key = 'G_PAI_USER_REG_VERIFY';
		$data = array();
		return $this->send_verify_code($phone, $group_key, $data, $user_id);
	}
	
	/**
	 * 校验注册校验码
	 * @param string $phone
	 * @param string $verify_code
	 * @param int $user_id
	 * @param boolean $b_del_verify_code
	 * @return boolean
	 */
	public function check_phone_reg_verify_code($phone, $verify_code, $user_id=0, $b_del_verify_code=true)
	{
		$group_key = 'G_PAI_USER_REG_VERIFY';
		return $this->check_verify_code($phone, $group_key, $verify_code, $user_id, $b_del_verify_code);
	}
	
	/**
	 * 删除注册校验码
	 * @param string $phone
	 * @return boolean
	 */
	public function del_phone_reg_verify_code($phone)
	{
		$group_key = 'G_PAI_USER_REG_VERIFY';
		return $this->del_verify_code($phone, $group_key);
	}
	
	/**
	 * 发送绑定校验码短信
	 * @param string $phone
	 * @param int $user_id
	 * @return boolean
	 */
	public function send_phone_bind_verify_code($phone, $user_id=0)
	{
		$group_key = 'G_PAI_USER_BIND_VERIFY';
		$data = array();
		return $this->send_verify_code($phone, $group_key, $data, $user_id);
	}
	
	/**
	 * 校验绑定校验码
	 * @param string $phone
	 * @param string $verify_code
	 * @param int $user_id
	 * @param boolean $b_del_verify_code
	 * @return boolean
	 */
	public function check_phone_bind_verify_code($phone, $verify_code, $user_id=0, $b_del_verify_code=true)
	{
		$group_key = 'G_PAI_USER_BIND_VERIFY';
		return $this->check_verify_code($phone, $group_key, $verify_code, $user_id, $b_del_verify_code);
	}
	
	/**
	 * 删除绑定校验码
	 * @param string $phone
	 * @return boolean
	 */
	public function del_phone_bind_verify_code($phone)
	{
		$group_key = 'G_PAI_USER_BIND_VERIFY';
		return $this->del_verify_code($phone, $group_key);
	}
	
	/**
	 * 发送提现校验码短信
	 * @param string $phone
	 * @param int $user_id
	 * @return boolean
	 */
	public function send_withdraw_verify_code($phone, $user_id=0)
	{
		$group_key = 'G_PAI_WITHDRAW_VERIFY';
		$data = array();
		return $this->send_verify_code($phone, $group_key, $data, $user_id);
	}
	
	/**
	 * 校验提现校验码
	 * @param string $phone
	 * @param string $verify_code
	 * @param int $user_id
	 * @param boolean $b_del_verify_code
	 * @return boolean
	 */
	public function check_withdraw_verify_code($phone, $verify_code, $user_id=0, $b_del_verify_code=true)
	{
		$group_key = 'G_PAI_WITHDRAW_VERIFY';
		return $this->check_verify_code($phone, $group_key, $verify_code, $user_id, $b_del_verify_code);
	}
	
	/**
	 * 删除提现校验码
	 * @param string $phone
	 * @return boolean
	 */
	public function del_withdraw_verify_code($phone)
	{
		$group_key = 'G_PAI_WITHDRAW_VERIFY';
		return $this->del_verify_code($phone, $group_key);
	}
	
	/**
	 * 更换绑定手机号时，发送通知给旧的手机号码
	 * @param string $phone
	 * @param array $data phone新号码
	 * @return boolean
	 */
	public function send_notice_by_change_bind($phone, $data, $user_id=0)
	{
		$group_key = 'G_PAI_USER_PHONE_CHANGE';
		return $this->send_sms($phone, $group_key, $data, $user_id);
	}
	
	/**
	 * 发送提现通知
	 * @param string $phone
	 * @param array $data datetime年月日时分 amount数字金额
	 * @param int $user_id
	 * @return boolean
	 */
	public function send_withdraw_notice($phone, $data, $user_id=0)
	{
		//不需要发了
		return false;
	}
	
	/**
	 * 组织者取消外拍活动，通知报名者已经退款
	 * 有判断交易状态，需要取消交易后，再调用此接口
	 * @param string $phone
	 * @param array $data array('event_title'=>'')
	 * @param int $enroll_id
	 * @return boolean
	 */
	public function send_org_cancel_refund_notice($phone, $data, $enroll_id)
	{
		$phone = trim($phone);
		if( !is_array($data) ) $data = array();
		$enroll_id = intval($enroll_id);
		
		if( strlen($phone)<1 || $enroll_id<1 )
		{
			return false;
		}
		
		$payment_obj = POCO::singleton('pai_payment_class');
		
		//获取交易记录
		$trade_info = $payment_obj->get_trade_info_by_enroll_id($enroll_id);
		if( empty($trade_info) || $trade_info['status']!=7 )
		{
			return false;
		}
		
		//检查是否外拍
		$channel_module = trim($trade_info['channel_module']);
		if( $channel_module!='waipai' )
		{
			return false;
		}
		
		$is_balance = intval($trade_info['is_balance']);
		$is_third = intval($trade_info['is_third']);
		$recharge_id = intval($trade_info['recharge_id']);
		
		$group_key = '';
		if( $is_balance==1 )
		{
			//部分或全额使用约约钱包
			
			//退回约约钱包
			$group_key = 'G_PAI_WAIPAI_ORG_CANCEL_REFUND_YUE';
		}
		elseif( $is_third==1 )
		{
			//全额使用第三方，没使用约约钱包
			$recharge_info = $payment_obj->get_recharge_info($recharge_id);
			if( !empty($recharge_info) && $recharge_info['status']==1 )
			{
				$third_code = trim($recharge_info['third_code']);
				if( in_array($third_code, array('tenpay_wxpub')) )
				{
					//退回第三方
					$group_key = 'G_PAI_WAIPAI_ORG_CANCEL_REFUND_THIRD';
				}
				else 
				{
					//退回约约钱包
					$group_key = 'G_PAI_WAIPAI_ORG_CANCEL_REFUND_YUE';
				}
			}
		}
		
		if( strlen($group_key)<1 )
		{
			return false;
		}
		
		return $this->send_sms($phone, $group_key, $data);
	}
}
