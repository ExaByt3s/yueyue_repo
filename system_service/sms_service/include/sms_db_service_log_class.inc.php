<?php
/**
 * M������ʾ��
 *
 * @author erldy
 */

class sms_db_service_log_class extends POCO_TDG 
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		//���ݿ���
		$this->setServerId(false);
		//������ID
		$this->setDBName('sms_service_db');
		//��������
		$this->setTableName('sms_send_log_tbl');
	}
	
	/**
	 * ��������
	 *
	 * @param array $data
	 * @return bool
	 */
	public function add_app_info($data)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		return $this->insert($data);
	}
	
	/**
	 * ִ���ض����
	 *
	 * @param array $data
	 * @return bool
	 */
	public function process_app_info($stmt)
	{
		if (empty($stmt)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		return $this->query($stmt);
	}
	/**
	 * ����Ӧ������
	 *
	 * @param array $data
	 * @param int $app_id
	 * @return bool
	 */
	public function update_app_info($data, $app_id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		$app_id = (int)$app_id;
		if (empty($app_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':APPID����Ϊ��');
		}
		
		$where_str = "sms_id = {$app_id}";
		return $this->update($data, $where_str);
	}
	
	/**
	 * ����Ӧ���������
	 *
	 * @param int $app_id
	 * @param int $incr
	 * @return bool
	 */
	public function update_app_user_add_count($app_id, $incr = 1)
	{
		$app_id = (int)$app_id;
		if (empty($app_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':APPID����Ϊ��');
		}
		$where_str = "app_id = {$app_id}";
		$incr = (int)$incr;
		if ($incr > 0) 
		{
			return $this->incrField($where_str, 'user_add_count', $incr);
		}
		else 
		{
			return $this->decrField($where_str, 'user_add_count', abs($incr));
		}
	}
	
	/**
	 * ����Ӧ��Ϊ�Ƽ�Ӧ��
	 *
	 * @param int $app_id
	 * @return bool
	 */
	public function set_app_commend($app_id)
	{
		$app_id = (int)$app_id;
		if (empty($app_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':APPID����Ϊ��');
		}
		
		$where_str = "app_id = {$app_id}";
		return $this->updateField($where_str, 'commend', '1');
	}
	
	/**
	 * ����Ӧ��Ϊ����
	 *
	 * @param int $app_id
	 * @return bool
	 */
	public function set_app_hidden($app_id)
	{
		$app_id = (int)$app_id;
		if (empty($app_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':APPID����Ϊ��');
		}
		
		$where_str = "app_id = {$app_id}";
		return $this->updateField($where_str, 'hidden', '1');
	}
	
	/**
	 *  ɾ��Ӧ������
	 *
	 * @param int $app_id
	 * @return bool
	 */
	public function del_app_info($app_id)
	{
		$app_id = (int)$app_id;
		if (empty($app_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':APPID����Ϊ��');
		}
		
		$where_str = "app_id = {$app_id}";
		return $this->delete($where_str);
	}
	
	/**
	 * ��ȡָ��Ӧ�õ���ϸ��Ϣ
	 *
	 * @param int $app_id
	 * @return array
	 */
	public function get_app_info($app_id)
	{
		$app_id = (int)$app_id;
		if (empty($app_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':APPID����Ϊ��');
		}
		return $this->find(" app_id = {$app_id}");
	}
	
	/**
	 * ��ȡָ��Ӧ�õ���ϸ��Ϣ
	 *
	 * @param string $app_name
	 * @return array
	 */
	public function get_app_info_by_name($app_name)
	{
		if (empty($app_name)) 
		{
			return false;
		}
		return $this->find(" app_name = '{$app_name}'");
	}
	
	/**
	 * ȡ�б�
	 *
	 * @param string $where_str    ��ѯ����
	 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
	 * @param string $limit        ��ѯ����
	 * @param string $order_by     ��������
	 * @return array|int
	 */
	public function get_apps_list($where_str = '', $b_select_count = false, $limit = '0,10', $order_by = 'rank DESC')
	{
		if ($b_select_count == true) $rows = $this->findCount($where_str);
		else $rows = $this->findAll($where_str, $limit, $order_by);
		return $rows;
	}
	
	/**
	 * ȡ�б�
	 *
	 * @param string $where_str    ��ѯ����
	 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
	 * @param string $limit        ��ѯ����
	 * @param string $order_by     ��������
	 * @return array|int
	 */
	public function get_all_apps_list($where_str = '', $b_select_count = false, $limit = '0,10', $order_by = 'rank DESC')
	{
		if ($b_select_count == true) $rows = $this->findCount($where_str);
		else $rows = $this->findAll($where_str, $limit, $order_by);
		return $rows;
	}
	
	/**
	 * ȡӦ���Ƽ��б�
	 *
	 * @param int $user_id �û�ID
	 * @param int $limit ȡ��������
	 * @return array
	 */
	public function get_apps_commend_list($user_id = 0, $limit = 10)
	{
		$user_id = (int)$user_id;
		$limit = (int)$limit;
		$myapps_user_obj = POCO::singleton('myapps_user_class', $user_id);
		$myapps_list = $myapps_user_obj->get_user_apps_list(true, false, '');
		$apps_id = array();
		$where_str = '';
		if (!empty($myapps_list)) 
		{
			foreach ($myapps_list as $item) 
			{
				$apps_id[] = $item['app_id'];
			}
			$apps_id_str = implode(',', $apps_id);
			$where_str = "app_id NOT IN ({$apps_id_str})";
		}
		unset($myapps_list);
		$where_str = empty($where_str) ? "hidden = '0' AND commend = '1'" : "{$where_str} AND hidden = '0' AND commend = '1'";
		return $this->findAll($where_str, "0,{$limit}", 'rank DESC');
	}
}

?>