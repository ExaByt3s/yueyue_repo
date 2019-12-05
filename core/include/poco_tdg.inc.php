<?php
/**
 * POCO_TDG POCO���ݿ������
 * 
 * @author ERLDY <erldy@126.com>
 * @package core
 */

if (!class_exists("POCO_TDG", false))
{
	class POCO_TDG
	{
		//���ݿ���
		protected $_db_name;
		
		//��������
		protected $_tbl_name;
		
		//������ID
		protected $_server_id = false;	
		
		/**
		 * ���캯������ʼ����������
		 *
		 * @param string $tbl_name   ��ʼ����������
		 * @param string $db_name    ��ʼ���������ݿ�
		 * @param int    $server_id  ��ʼ�����ݿ������ID
		 */
		public function __construct($tbl_name = '', $db_name = '', $server_id = null)
		{	
			// ����Ӧ����Ŀ�����ݿ������ID
			if (!empty($server_id)) 
			{
				$this->setServerId($server_id);
			}
			// ����Ӧ����Ŀ�����ݿ�����
			if (!empty($db_name)) 
			{
				$this->setDBName($db_name);
			}
			// ���ò����ı���
			if (!empty($tbl_name)) 
			{
				$this->setTableName($tbl_name);
			}	
		}
		
		/**
		 * ���÷�����ID
		 *
		 * @param int $server_id
		 */
		protected function setServerId($server_id)
		{
			$this->_server_id = $server_id;
		}
		
		/**
		 * ���ò������ݿ���
		 *
		 * @param string $db_name
		 */
		protected function setDBName($db_name)
		{
			$this->_db_name = $db_name;
		}
		
		/**
		 * ���ò�������
		 *
		 * @param string $tbl_name
		 */
		protected function setTableName($tbl_name)
		{
			$this->_tbl_name = $tbl_name;
		}
		
		/**
		 * ���ص�ǰ����������ID
		 *
		 * @return int
		 */
		public function getServerId()
		{
			return $this->_server_id;
		}
		
		/**
		 * ���ص�ǰ�������ݿ���
		 *
		 * @return int
		 */
		public function getDBName()
		{
			return $this->_db_name;
		}
		
		/**
		 * ���ص�ǰ��������
		 *
		 * @return string
		 */
		public function getTableName()
		{
			return $this->_tbl_name;
		}
		
		/**
		 * ��ѯ���з��������ļ�¼���ݣ�����һ���������м�¼�Ķ�ά���飬ʧ��ʱ���� false
		 *
		 * @param string $where      ��ѯ����
		 * @param string $sort       ����
		 * @param string $limit      ��ѯ��¼��
		 * @param mixed $fields      ��ѯ�ֶ�
		 *
		 * @return array
		 */
		public function findAll($where = null, $limit = null, $sort = null, $fields = '*')
		{
			// ��ѯ����
			$whereby = $where != '' ? "WHERE {$where}" : '';
			// ��������
			$sortby = $sort != '' ? " ORDER BY {$sort}" : '';
			// ���� $limit
			if (!empty($limit) && preg_match("/^\d+,\d+$/i", $limit))
			{
				list($length, $offset) = explode(',', $limit);
			} 
			else 
			{
				//������limit ����
				$length = 0;
				$offset = 1000;
			}
			
			// ����������ѯ���ݵ� SQL ���
			$sql = "SELECT {$fields} FROM {$this->_db_name}.{$this->_tbl_name} {$whereby} {$sortby}";
			// ���� $length �� $offset ���������Ƿ�ʹ���޶�������Ĳ�ѯ
			if (null !== $length || null !== $offset)
			{
				$sql = sprintf('%s LIMIT %d,%d', $sql, $length, (int)$offset);
			}
			// ��ѯ
			$rows = db_simple_getdata($sql, false, $this->_server_id);
			return $rows;
		}
		
		/**
		 * ���ط��������ĵ�һ����¼���ݣ���ѯû�н������ false
		 *
		 * @param string $where ��ѯ����
		 * @param string $sort  ��������
		 * @param mixed $fields ��ѯ�ֶ�
		 *
		 * @return array
		 */
		public function find($where, $sort = null, $fields = '*')
		{
			// ��ѯ����
			$whereby = $where != '' ? "WHERE {$where}" : '';
			// ��������
			$sortby = $sort != '' ? " ORDER BY {$sort}" : '';
			// ����������ѯ���ݵ� SQL ���
			$sql = "SELECT {$fields} FROM {$this->_db_name}.{$this->_tbl_name} {$whereby} {$sortby} LIMIT 0,1";
			// ��ѯ
			$row = db_simple_getdata($sql, true, $this->_server_id);
			return $row;
		}
		
		/**
		 * ֱ��ʹ�� sql ����ȡ��¼
		 *
		 * @param string $sql
		 *
		 * @return array
		 */
		public function findBySql($sql)
		{
			if (empty($sql)) 
			{
				throw new App_Exception('SQL����쳣������');
			}
			// ��ѯ
			$rows = db_simple_getdata($sql, false, $this->_server_id);
			return $rows;
		}
		
		/**
		 * ͳ�Ʒ��������ļ�¼������
		 *
		 * @param string $where  ��ѯ����
		 * @param string $fields ͳ���ֶ�
		 *
		 * @return int
		 */
		public function findCount($where = null, $fields = '*')
		{
			// ��ѯ����
			$whereby = $where != '' ? "WHERE {$where}" : '';
			
			$sql = "SELECT COUNT({$fields}) AS c FROM {$this->_db_name}.{$this->_tbl_name} {$whereby}";
			$tmp = db_simple_getdata($sql, true, $this->_server_id);
			return (int)$tmp['c'];
		}
		
		/**
		 * �������ݵ����ݿ�
		 *
		 * @param array $row          �������������
		 * @param string $insert_type �������ݷ�ʽ��REPLACE�滻���룬IGNORE��ֹ�ظ�����
		 *
		 * @return int  
		 */
		public function insert($row, $insert_type = "")
		{
			$insert_str = db_arr_to_update_str($row);
			
			if ($insert_type=="REPLACE") 
			{
				$sql = "REPLACE INTO {$this->_db_name}.{$this->_tbl_name} SET {$insert_str}";
			}
			elseif ($insert_type=="IGNORE")
			{
				$sql = "INSERT IGNORE INTO {$this->_db_name}.{$this->_tbl_name} SET {$insert_str}";
			}
			else 
			{
				$sql = "INSERT INTO {$this->_db_name}.{$this->_tbl_name} SET {$insert_str}";
			}
			db_simple_getdata($sql, true, $this->_server_id);
			$id = db_simple_get_insert_id();
			return $id;
		}
		
		
		
		/**
		 * ���±����ݺ���
		 *
		 * @param array $row    ���µ���������
		 * @param string $where ��������
		 * 
		 * @return mixed
		 */
		public function update($row, $where) 
		{
			if (empty($where)) 
			{
				throw new App_Exception('������������Ϊ�գ�����');
			}
			$update_str = db_arr_to_update_str($row);
			
			if (empty($update_str)) 
			{
				throw new App_Exception('�������ݲ���Ϊ�գ�����');
			}

			$sql = " UPDATE {$this->_db_name}.{$this->_tbl_name} SET {$update_str} WHERE {$where}";

			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * ���¼�¼��ָ���ֶΣ����ظ��µļ�¼����
		 *
		 *
		 * @param mixed $where  ��������
		 * @param string $field �����ֶ�
		 * @param mixed $value  ���µ�ֵ
		 *
		 * @return int
		 */
		public function updateField($where, $field, $value)
		{
			if (empty($where)) 
			{
				throw new App_Exception('������������Ϊ�գ�����');
			}
			
			if (empty($field)) 
			{
				throw new App_Exception('�����ֶβ���Ϊ�գ�����');
			}
			
			$sql = " UPDATE {$this->_db_name}.{$this->_tbl_name} SET {$field} = '{$value}' WHERE {$where}";

			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * ���ӷ��������ļ�¼��ָ���ֶε�ֵ�����ظ��µļ�¼����
		 *
		 * @param mixed $where  ��������
		 * @param string $field ָ���ֶ�
		 * @param int $incr     ���ӵ�ֵ
		 *
		 * @return mixed
		 */
		public function incrField($where, $field, $incr = 1)
		{
			// ��������
			$whereby = $where != '' ? "WHERE {$where}" : '';
			$incr = (int)$incr;
			$sql = "UPDATE {$this->_db_name}.{$this->_tbl_name} SET {$field} = {$field} + {$incr} {$whereby}";
			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * ��С���������ļ�¼��ָ���ֶε�ֵ�����ظ��µļ�¼����
		 *
		 * @param mixed $where  ��������
		 * @param string $field ָ���ֶ�
		 * @param int $decr     ���ٵ�ֵ
		 *
		 * @return mixed
		 */
		public function decrField($where, $field, $decr = 1)
		{
			// ��������
			$whereby = $where != '' ? "WHERE {$where}" : '';
			$incr = (int)$decr;
			$sql = "UPDATE {$this->_db_name}.{$this->_tbl_name} SET {$field} = {$field} - {$incr} {$whereby}";
			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * ɾ��������
		 * 
		 * @param string $where ɾ������
		 * 
		 * @return bool
		 */
		public function delete($where)
		{
			// ɾ����������Ϊ��
			if (empty($where)) 
			{
				throw new App_Exception('ɾ����������Ϊ�գ�����');
			}
			$sql  = " DELETE FROM {$this->_db_name}.{$this->_tbl_name} WHERE {$where}";
			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * ֱ��ִ�� SQL ���
		 *
		 * @param string $sql
		 * 
		 * @return mixed
		 */
		public function query($sql)
		{
			 if (empty($sql)) 
			{
				throw new App_Exception('SQL����쳣������');
			}
			return db_simple_getdata($sql, false, $this->_server_id);;
		}
		
		/**
		 * ��ȡ�������ID
		 *
		 * @return int
		 */
		public function get_last_insert_id()
		{
			return db_simple_get_insert_id();
		}

		/**
		 * ��ȡ����޸ļ�¼��
		 *
		 * @return int
		 */
		public function get_affected_rows()
		{
			return db_simple_get_affected_rows();
		}
	}
}
?>