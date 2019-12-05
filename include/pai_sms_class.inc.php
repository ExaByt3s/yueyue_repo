<?php
/**
 * ���š�У����
 * @author Henry
 */

class pai_sms_class extends POCO_TDG
{
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
	}
	
	/**
	 * ��ȡ��������
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
			'G_PAI_USER_REG_VERIFY' => '����ע����֤���ǣ�{verify_code}����Ǳ��˲�������ظ�TD���ٽ��ա��ͷ��绰��{service}',
			'G_PAI_USER_PASSWORD_VERIFY' => '���޸��������֤���ǣ�{verify_code}����Ǳ��˲�������ظ�TD���ٽ��ա��ͷ��绰��{service}',
			'G_PAI_USER_LOGIN_VERIFY'  => '{verify_code}��ԼԼ��֤�룬�����ڵ�¼�������֪���ˣ����ͷ��绰��{service}',
			'G_PAI_USER_BIND_VERIFY'  => '{verify_code}��ԼԼ��֤�룬�����ڸ����󶨣������֪���ˣ����ͷ��绰��{service}',
			'G_PAI_USER_PHONE_CHANGE' => '�������޸İ��ֻ��ţ��º���Ϊ{phone}��������Ǳ��˲�������������ϵ�ͷ���{service}',
			'G_ECPAY_USER_LOGIN_VERIFY'  => '{verify_code}��ԼԼ��֤�룬�����ڵ�¼�������֪���ˣ���֧��ϵͳ',
			'G_ECPAY_ACCOUNT_RECHARGE_VERIFY'  => '{verify_code}��ԼԼ��֤�룬�������˻���ֵ�������֪���ˣ���֧��ϵͳ',
			'G_ECPAY_ACCOUNT_WITHDRAW_VERIFY'  => '{verify_code}��ԼԼ��֤�룬�������˻����֣������֪���ˣ���֧��ϵͳ',
			'G_ECPAY_ACCOUNT_TRANSFER_VERIFY'  => '{verify_code}��ԼԼ��֤�룬�������˻�ת�ˣ������֪���ˣ���֧��ϵͳ',
			'G_PAI_RECHARGE_SUCCESS'  => '��ͨ��֧�����ɹ���ֵ {amount}Ԫ �����������뼰ʱ��ϵ���ͷ��绰��{service}',
			'G_PAI_WITHDRAW_VERIFY'   => '����������֤���ǣ�{verify_code}����Ǳ��˲�������ظ�TD���ٽ��ա��ͷ��绰��{service}',
			'G_PAI_WITHDRAW_SUBMIT'   => '����˺�{phone} �������ֵ� {amount}Ԫ����1���������ڵ��ˣ����������뼰ʱ��ϵ���ͷ��绰��{service}',
			'G_PAI_WITHDRAW_FAIL'     => '����˺�{phone} �������ֵ� {amount}Ԫ������ʧ�ܣ����¼ԼԼapp�鿴ԭ�����������뼰ʱ��ϵ���ͷ��绰��{service}',
			//'G_PAI_DATE_MT_AGREE'     => '��ϲ����ģ�ء�{mt_nickname}����ͬ��Լ�ģ��������룺{activity_code}��������ʱ�޷���ʾ��ά��ǩ����Ҳ���ṩ�����������ģ��ǩ��Ŷ',
			'G_PAI_DATE_MT_AGREE'     => '��ϲ����ģ�ء�{mt_nickname}����ͬ��Լ�ģ��������룺{activity_code}��������ʱ�޷���ʾ��ά��ǩ����Ҳ���ṩ�����������ģ�أ���ע�Ᵽ����š���������ϸ�ڣ������ڡ�ԼԼ����ģ�ع�ͨ�����������벦��{service}��',
			'G_PAI_DATE_MT_PENDING'   => '�ף���Ӱʦ[{pp_nickname}]����������һ����ֵ[{amount}Ԫ]��Լ�����룬�Ͻ���½app����ɣ�����׬Ǯ�Ļ��������ˡ�',
			'G_PAI_DATE_MT_IGNORE'    => '��Լ��ģ��{mt_nickname}�������ѹ��ڣ����ý��˻�������˻������������벦��{service}��',
			'G_PAI_DATE_CODE_NO_CHECKED' => '��Լ��ģ��{mt_nickname}����48Сʱδǩ����ϵͳ��ȡ��Լ�Ľ��ף����ý��˻�������˻������������벦��{service}��',
			'G_PAI_WAIPAI_ORG_CANCEL_REFUND_YUE' => '��μӵ�{event_title}���{table_num}���ѱ���֯��ȡ�����������˻����㡰ԼԼ��Ǯ����������鿴�����������벦��{service}',
			'G_PAI_WAIPAI_ORG_CANCEL_REFUND_THIRD' => '��μӵ�{event_title}���{table_num}���ѱ���֯��ȡ����������ԭ·���أ�������鿴�����������벦��{service}',
			'G_PAI_WAIPAI_CODE_NUMBER' => '��ϲ�㣬{event_title}���{table_num}�������ɹ���������ͨ��APP��ʾ��ά����ṩ�������룺{activity_code} ǩ������֯����ϵ��ʽ��{cellphone} ��������������ϵԼԼ�ͷ�{service}',
			'G_PAI_WAIPAI_CODE_NUMBER_B' => '��ϲ�㣬�ѳ�Ϊ{event_title}���{table_num}���ĺ򲹣�������ͨ��APP��ʾ��ά����ṩ�������룺{activity_code} ǩ������֯����ϵ��ʽ��{cellphone} ��������������ϵԼԼ�ͷ�{service}',
			'G_PAI_WAIPAI_CODE_NO_CHECKED' => '��μӵ�{event_title}���{table_num}���ѽ�������δ����ǩ����ϵͳ��ȡ�����ף����ü����˻�����ġ�ԼԼ��Ǯ����������鿴�����������벦��{service}',
			'G_PAI_WAIPAI_PAID_A' => '��ϲ�㣬{event_title}��ѱ����ɹ�����ע�Ᵽ����š�����ϸ�ڣ������ڡ�ԼԼ������֯�߹�ͨ�����������벦��{service}',
			'G_PAI_WAIPAI_PAID_B' => '��ϲ�㣬�ѳ�Ϊ{event_title}��ĺ򲹣���ע�Ᵽ����š�����ϸ�ڣ������ڡ�ԼԼ������֯�߹�ͨ�����������벦��{service}',
			'G_PAI_USER_REG_COUPON' => '��ϲ��ע��ɹ���ԼԼ�������{amount}����������˺ţ�3�µװ汾����֮���¼app����ʹ�á�',
			'G_PAI_WAIPAI_UNPAID' => '��μӵ�{event_title}���������Ͽ���ɸ��ѣ�ȥ�μӻ��~',
			'G_PAI_WAIPAI_SHARE_SUCCESS' => '��ϲ�����ɹ���{amount}�Ѿ��������ԼԼ�˺� ��3�µװ汾����֮���¼app����ʹ�á�',
			'G_PAI_WAIPAI_SHARE_PAID' => '��ϲ�㣬������ͨ����ķ����ѳɹ��������Ϊ�˸�л��ķ���ԼԼ�Ѿ����ֽ�{amount}Ԫ���ֵ����Ǯ��������������Լ�� ��3�µװ汾����֮���¼app����ʹ�á�',
			'G_PAI_WAIPAI_FINISH_COUPON' => '��μӵ�{event_title}���Ļ�ѽ�����POCO��ͬ�������ԼԼ�������Ļ�Żݴ��������{phone}���ֻ��˻���������ԼԼ��ȡ��app.yueus.com',
			'G_PAI_TOPIC_MEETING_VERIFY' => '{verify_code}��ԼԼ��֤�룬�����ڱ����������֪���ˣ����ͷ��绰��{service}',
			'G_PAI_TOPIC_MEETING_SUCCESS' => '���ѳɹ�����5��17�ŵġ�������+��Ӱ���O2O�߷���̳ - {event_title}����{enroll_num}�ŵ�����Ʊ��������齫����������䣬���Ժ���ա���ʱƾ������Ʊ����Ž���ǩ������������, �벦��{service}',
			
			'G_PAI_TASK_SELLER_REG' => 'ԼԼ�Ѿ�����ע���˷������˺ţ��˺�{cellphone} ����{password} ��¼ԼԼ��ַ www.yueus.com/task ȥ�����ɡ�ԼԼ�ͷ��绰{service}',
			'G_PAI_TASK_LEAD_SUBMIT_SELLER' => 'ԼԼ��������һ���µ�������ᡣ��� {url} �鿴����',
			'G_PAI_TASK_QUOTES_PAY_COINS_BUYER' => '����ԼԼ�Ϸ�����{service_name}�����Ѿ����˷������ۡ���ȥ������',
			'G_PAI_TASK_QUOTES_READ_SELLER' => '{buyer_nickname}�Ѿ��鿴����ı��ۣ�������� {url} ȥ������',
			'G_PAI_TASK_QUOTES_HIRE_BUYER' => '���Ѿ���{seller_nickname}��ɶ���������Ta��ϵ��ȷ��ѡ����ٽ��з�����֧��',
			'G_PAI_TASK_QUOTES_HIRE_SELLER' => '{buyer_nickname}�Ѿ�ѡ�����㣬��ȥ��Ta��ϵ�ɡ�������� {url} �鿴��������',
			'G_PAI_TASK_QUOTES_PAY_SELLER' => '{buyer_nickname}�Ѿ�֧�������{pay_amount}Ԫ��������ɹ�������TaҪ��������',
			'G_PAI_TASK_LEAD_REMIND_SELLER' => '����{lead_count}���µ�������ᡣ��� {url} �鿴����',
			'G_PAI_TASK_LEAD_REMIND_SELLER_B' => '�㱨�۵������û����µĲ�����������ȥ��������鿴����� {url} �鿴����',
			'G_PAI_TASK_LEAD_REMIND_SELLER_C' => '����{lead_count}���µ�������ᡣ�㱨�۵������û����µĲ�����������ȥ��������鿴����� {url} �鿴����',
			'G_PAI_TASK_QUOTES_FINISH_BUYER' => 'ԼԼ�Ѿ��ռ������з���������ı��ۣ�ȥѡ��һ����ɽ��װɡ�',
			
			//http://s.yueus.com/normal_certificate_choose.php
			'G_PAI_MALL_SELLER_CERTIFICATE_BASIC_FAIL' => '����{date}�����ԼԼ�̼һ�����֤����˲�ͨ��������Ե�¼ s.yueus.com/xz.php �����ύ���롣',
			
			//http://s.yueus.com/normal_certificate_basic.php
			'G_PAI_MALL_SELLER_CERTIFICATE_BASIC_SUCCESS' => '����{date}�����ԼԼ�̼һ�����֤��ͨ��������Ե�¼ s.yueus.com/rz.php ���з�����֤��',
			
			//http://s.yueus.com/normal_certificate_basic.php
			'G_PAI_MALL_SELLER_CERTIFICATE_SERVICE_FAIL' => '����{date}�����ԼԼ�̼ҷ�����֤����˲�ͨ��������Ե�¼ s.yueus.com/rz.php �����ύ���롣',
			
			//http://s.yueus.com/goods_list.php
			'G_PAI_MALL_SELLER_CERTIFICATE_SERVICE_SUCCESS' => '����{date}�����ԼԼ�̼ҷ�����֤��ͨ��������Ե�¼ s.yueus.com/fw.php ������ӷ���',
			
			//�����֧�����̼�1Сʱ��û�в���
			'G_PAI_MALL_ORDER_WAIT_CONFIRM_REMIND_SELLER' => '��ã��û�[{buyer_nickname}]��֧����һ�ʼ�ֵ[{amount}Ԫ]�Ķ������Ͻ���¼app����ɣ�����׬Ǯ�Ļ��������ˡ�',
			
			//������������ѯ����Ҫʱ�����̼�Ҫȥ�����̼Ұ�APP
			'G_PAI_MALL_SELLER_CHAT' => '�𾴵��û������ã����ڽ���{datetime}�յ�һ���û���ѯ������ԼԼ�汾�������̼��û���ģ�ء���Ӱʦ����ѵʦ�ȣ����лظ����ӵ��������ذ�װԼԼ�̼Ұ�app�������������н��ס�������½���µ�ַ�����ذ�װԼԼ�̼Ұ棨http://s.yueus.com������л�����������ϡ�',
			
			'G_PAI_MALL_ORDER_CLOSE_WAIT_SIGN_FOR_SYSTEM_SELLER' => '����һ�ʶ����ѱ��رգ������ţ�{order_sn}����������������ϵԼԼ�ͷ���{service}', //ϵͳ�رն����������̼�
			'G_PAI_MALL_ORDER_CLOSE_WAIT_SIGN_FOR_SYSTEM_BUYER' => '��Ķ����ѱ��رգ������ţ�{order_sn}�����Ѹ�����Զ�ת������˻�������ա�������������ϵԼԼ�ͷ�{service}', //ϵͳ�رն���������������

			'G_PAI_MALL_ORDER_BUY_ACTIVITY_SUCCESS' => '��ϲ�㣬{activity_name}� [{stage_title}]�����ɹ�������ͨ��APP��ʾ��ά����������룺{activity_code} ǩ������֯����ϵ��ʽ��{cellphone} ��������������ϵԼԼ�ͷ�{service}',
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
	 * ���Ͷ���
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
		
		//��ȡ��������
		$content = $this->get_sms_content($group_key, $data);
		if( !preg_match('/^1\d{10}$/isU', $phone) || strlen($content)<1 )
		{
			return false;
		}
		
		//���Ͷ���
		include_once(G_YUEYUE_ROOT_PATH . "/system_service/sms_service/poco_app_common.inc.php");
		//2015-11-13����11�л���16 update by ��ʯ��
		$product_type = 16; //Ĭ��0��10΢��֪ͨ�࣬11΢����֤���࣬12΢��Ӫ���࣬16������֤���࣬18������֤����
		$more_info = array( 'user_id'=>$user_id );
		$sms_obj = POCO::singleton('class_sms_v2');
		return $sms_obj->save_and_send_sms($phone, $content, $product_type);
	}
	
	/**
	 * ����У�������
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
		
		//��ȡ��������
		$content = $this->get_sms_content($group_key, $data);
		if( !preg_match('/^1\d{10}$/isU', $phone) || strlen($content)<1 )
		{
			return false;
		}
		
		//������֤��
		include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');
		$verify_code_obj = POCO::singleton('phone_verify_code_class');
		$more_info = array('user_id' => $user_id);
		//2015-11-13����11�л���16 update by ��ʯ��
		$product_type = 16; //Ĭ��0��10΢��֪ͨ�࣬11΢����֤���࣬12΢��Ӫ���࣬16������֤���࣬18������֤����
		$ret = $verify_code_obj->send_verify_code($phone, $group_key, $content, 600, $more_info, $product_type);
		if( $ret['code']==1 || $ret['code']==-5)
		{
			//���͹���Ƶ���������Ҳ���سɹ�
			return true;
		}
		return false;
	}
	
	/**
	 * У��
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
	 * ɾ��
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
	 * ����ע��У�������
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
	 * У��ע��У����
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
	 * ɾ��ע��У����
	 * @param string $phone
	 * @return boolean
	 */
	public function del_phone_reg_verify_code($phone)
	{
		$group_key = 'G_PAI_USER_REG_VERIFY';
		return $this->del_verify_code($phone, $group_key);
	}
	
	/**
	 * ���Ͱ�У�������
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
	 * У���У����
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
	 * ɾ����У����
	 * @param string $phone
	 * @return boolean
	 */
	public function del_phone_bind_verify_code($phone)
	{
		$group_key = 'G_PAI_USER_BIND_VERIFY';
		return $this->del_verify_code($phone, $group_key);
	}
	
	/**
	 * ��������У�������
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
	 * У������У����
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
	 * ɾ������У����
	 * @param string $phone
	 * @return boolean
	 */
	public function del_withdraw_verify_code($phone)
	{
		$group_key = 'G_PAI_WITHDRAW_VERIFY';
		return $this->del_verify_code($phone, $group_key);
	}
	
	/**
	 * �������ֻ���ʱ������֪ͨ���ɵ��ֻ�����
	 * @param string $phone
	 * @param array $data phone�º���
	 * @return boolean
	 */
	public function send_notice_by_change_bind($phone, $data, $user_id=0)
	{
		$group_key = 'G_PAI_USER_PHONE_CHANGE';
		return $this->send_sms($phone, $group_key, $data, $user_id);
	}
	
	/**
	 * ��������֪ͨ
	 * @param string $phone
	 * @param array $data datetime������ʱ�� amount���ֽ��
	 * @param int $user_id
	 * @return boolean
	 */
	public function send_withdraw_notice($phone, $data, $user_id=0)
	{
		//����Ҫ����
		return false;
	}
	
	/**
	 * ��֯��ȡ�����Ļ��֪ͨ�������Ѿ��˿�
	 * ���жϽ���״̬����Ҫȡ�����׺��ٵ��ô˽ӿ�
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
		
		//��ȡ���׼�¼
		$trade_info = $payment_obj->get_trade_info_by_enroll_id($enroll_id);
		if( empty($trade_info) || $trade_info['status']!=7 )
		{
			return false;
		}
		
		//����Ƿ�����
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
			//���ֻ�ȫ��ʹ��ԼԼǮ��
			
			//�˻�ԼԼǮ��
			$group_key = 'G_PAI_WAIPAI_ORG_CANCEL_REFUND_YUE';
		}
		elseif( $is_third==1 )
		{
			//ȫ��ʹ�õ�������ûʹ��ԼԼǮ��
			$recharge_info = $payment_obj->get_recharge_info($recharge_id);
			if( !empty($recharge_info) && $recharge_info['status']==1 )
			{
				$third_code = trim($recharge_info['third_code']);
				if( in_array($third_code, array('tenpay_wxpub')) )
				{
					//�˻ص�����
					$group_key = 'G_PAI_WAIPAI_ORG_CANCEL_REFUND_THIRD';
				}
				else 
				{
					//�˻�ԼԼǮ��
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
