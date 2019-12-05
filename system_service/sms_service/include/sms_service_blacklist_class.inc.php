<?php
/**
 * �û��ֻ�����Ϣ������
 *
 * @author ��ʯ��
 */

class sms_service_blacklist_class extends POCO_TDG
{
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(false);
		$this->setDBName('sms_service_db');
	}
	
	/**
	 * ָ����������
	 */
	private function set_sms_blacklist_tbl()
	{
		$this->setTableName('sms_blacklist_tbl');
	}

	/**
	 * ָ��ָ���
	 */
	private function set_sms_cmd_tbl()
	{
		$this->setTableName('sms_cmd_tbl');
	}

	/**
	 * ͨ����Ϣ���ݻ�ȡָ����Ϣ
	 *
	 * @param int $cmd_val
	 * @return bool
	 */
	public function get_cmd_info_by_val($cmd_val)
	{
		if (empty($cmd_val))
		{
			return false;
		}
		$this->set_sms_cmd_tbl();
		$ret = $this->find("cmd_val='{$cmd_val}'");
		return $ret;
	}

	/**
	 * ͨ��ָ��ID��ȡ��Ϣ
	 *
	 * @param int $cmd_id
	 * @return bool
	 */
	public function get_cmd_info($cmd_id)
	{
		$cmd_id = ( int ) $cmd_id;
		if (empty($cmd_id))
		{
			trace("�Ƿ����� cmd_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
			return false;
		}
		$this->set_sms_cmd_tbl();
		$ret = $this->find("cmd_id={$cmd_id}");
		return $ret;
	}

	/**
	 * ��ָ���������ָ����Ϣ
	 * @param $insert_data
	 * @param string $insert_type
	 * @return bool|int
	 */
	public function add_cmd($insert_data,$insert_type='IGNORE')
	{
		if( !is_array($insert_data) || empty($insert_data) )
		{
			return false;
		}
		$this->set_sms_cmd_tbl();
		return $this->insert($insert_data ,$insert_type);
	}

	/**
	 * ͨ��IDɾ��
	 *
	 * @param int    $cmd_id
	 * @return bool
	 */
	public function del_cmd($cmd_id)
	{
		$cmd_id = (int)$cmd_id;
		if (empty($cmd_id))
		{
			trace("�Ƿ����� cmd_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
			return false;
		}
		$this->set_sms_cmd_tbl();
		$where_str = " cmd_id={$cmd_id} ";
		return $this->delete($where_str);

	}

	/**
	 * ����ָ���
	 * @param array $data
	 * @param int   $cmd_id
	 * @return int
	 */
	public function modify_cmd($data,$cmd_id)
	{
		if( !is_array($data) || empty($data) || empty($cmd_id) )
		{
			return false;
		}
		$where_str = " cmd_id = {$cmd_id} ";
		$this->set_sms_cmd_tbl();
		$ret = $this->update($data,$where_str);
		return $ret;

	}

	/**
	 * ��ȡ��������Ϣ
	 * @param int $cellphone
	 * @return array
	 */
	public function get_blacklist_info($cellphone)
	{
		if ( !preg_match('/^1\d{10}$/', $cellphone) )
		{
			return array();
		}
		$this->set_sms_blacklist_tbl();
		return $this->find("cellphone={$cellphone}");
	}

	/**
	 * ��� ����������
	 * @param $cellphone
	 * @return int
	 */
	private function add($cellphone)
	{
		if ( !preg_match('/^1\d{10}$/', $cellphone) )
		{
			return 0;
		}
		$this->set_sms_blacklist_tbl();

		$data = array('cellphone' => $cellphone, 'add_time' => time());
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * ɾ����������Ϣ
	 * @param int $cellphone
	 * @return array
	 */
	private function del($cellphone)
	{
		if ( !preg_match('/^1\d{10}$/', $cellphone) )
		{
			return array();
		}
		$this->set_sms_blacklist_tbl();
		return $this->delete("cellphone={$cellphone}");
	}


	/**
	 * �����������Ϣ
	 * @param $data
	 * @return bool
	 */
	public function sms_blacklist($data)
	{
		$ret = false;
		$cmd_val = strtoupper($data['message']);
		$cellphone = $data['cellphone'];
		// ��ȡָ���б�
		$cmd_list = $this->get_cmd_info_by_val($cmd_val);
		// ָ���
		$action = isset($cmd_list['exec_val'])?$cmd_list['exec_val']:'';

		//�ж϶����Ƿ�Ϊ��  �� �ж�����Ƿ��д˷���
		if (!empty($action) && in_array($action,get_class_methods ( 'sms_service_blacklist_class' )))
		{
			$ret = $this->$action($cellphone);
		}

		return $ret;
	}

}
