<?php
/**
 * ������־
 * 
 * @author KOKO
 * log type����˵�� 
 * 1��ͷ��4λ����ʾ������� ,��1001:�������;1002:�����ⷢ(ȫ);1003:�����ⷢ(�ֶ�);1004:ɾ������;1005:�޸Ĺ���ʱ��
 */

class pai_task_admin_log_class extends POCO_TDG
{

	private $type_name = array(
		                       1001=>'�������ͨ��',
		                       1002=>'�����ⷢ(�ֶ�)',
		                       1003=>'�����ⷢ(ȫ)',
		                       1004=>'ɾ������',
		                       1005=>'�޸Ĺ���ʱ��',
		                       1006=>'������˲�ͨ��',
		                       1007=>'ȷ�ϴ��',
		                       1008=>'��ע',		                       
		                       1009=>'ϵͳ�Զ��ɵ�',
							   
		                       2001=>'��Ʒ���',		                       
		                       2002=>'��Ʒ���¼�',		                       
		                       2003=>'�����Ʒ',		                       
		                       2004=>'�޸���Ʒ',		                       
		                       2005=>'ɾ����Ʒ',
		                       2006=>'��Ʒ���ͨ��',		                       
		                       2007=>'��Ʒ��˲�ͨ��',
		                       2008=>'��Ʒ�ϼ�',
		                       2009=>'��Ʒ�¼�',
		                       2010=>'��༭���ͨ��',
		                       2011=>'��༭��˲�ͨ��',

		                       3001=>'����̼�',
		                       3002=>'�޸��̼�',
		                       3003=>'ɾ���̼�',
		                       3004=>'�޸�����',
		                       3005=>'�̼�����',

		                       4001=>'�����ر�',
		                       4002=>'��������',
		                       4003=>'����ǩ��',
		                       );

	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_log_db');
	}
	
	/**
	 * ָ����
	 */
	private function set_task_admin_log_tbl()
	{
		$this->setTableName('task_admin_log_tbl');
	}
	
	/**
	 * ��ȡ���һ��
	 * @param array $data
	 * @return array
	 */
	public function get_log_by_type_last($data)
	{
		$last = array();
		$return = $this->get_log_by_type($data);
		if($return)
		{
			$last = $return[0];			
		}
		return $last;		
	}
	
	/**
	 * ��ȡ
	 * @param array $data
	 * @return array
	 */
	public function get_log_by_type($data)
	{
		$list = array();
		$type_id = (int)$data['type_id'];
		$action_type = (int)$data['action_type'];
		$action_id = (int)$data['action_id'];

		$this->set_task_admin_log_tbl();
		$where_str =1;
		$where_str .=$action_type?" AND `action_type` = {$action_type}":"";
		$where_str .=$type?" AND `type_id` = {$type_id}":"";
		$where_str .=$action_id?" AND `action_id` = {$action_id}":"";
		$list = $this->findAll($where_str,'','id desc');
		if($list)
		{
			foreach($list as $key => $val)
			{
				$list[$key]['type_name'] = $this->type_name[$val['type_id']];
			}
		}
		return $list;
	}
	
	
	/*
	 * ��ȡ�б�
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_log_list($b_select_count = false, $where_str = '', $order_by = 'add_time DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_task_admin_log_tbl();
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}

	
	/**
	 * ���log
	 * @param string $admin_id
	 * @param string $type_id
	 * @param string $action_type 
	 * @param string $detail
	 * @param string $note
	 * @param string $action_id
	 * @return bool
	 */
	public function add_log($admin_id,$type_id,$action_type,$details=array(),$note='',$action_id=0)
	{
		$data = array(
			'admin_id' => (int)$admin_id,
			'type_id' => (int)$type_id,
			'action_type' => (int)$action_type,
			'action_id' => (int)$action_id,
			'note' => $note,
			'add_time' => time(),
			'details' => serialize($details),	
		);
		$this->set_task_admin_log_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}
}
