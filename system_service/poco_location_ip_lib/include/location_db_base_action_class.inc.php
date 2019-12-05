<?php
/**
 * db_base_action_class ���ݿ����������
 * @author ERLDY
 *
 */
if(!defined('LOCATION_SYS'))
{
	exit('Access Denied');
}

if (!class_exists('location_db_base_action_class', false))
{

	class location_db_base_action_class
	{
		//��������
		protected $opt_tbl;

		//������ID
		protected $server_id = false;

		//���һ�δ������
		public $errno = 0;

		//���һ�δ�����Ϣ
		public $errmsg = '';

		/**
	 * ���ò�������
	 *
	 * @param string $tbl_name
	 */
		protected function set_tbl_name($tbl_name)
		{
			$this->opt_tbl = $tbl_name;
		}

		/**
	 * ���÷�����ID
	 *
	 * @param int $server_id
	 */
		protected function set_server_id($server_id)
		{
			$this->server_id = $server_id;
		}

		/**
	 * ���ص�ǰ��������
	 *
	 * @return string
	 */
		public function get_tbl_name()
		{
			return $this->opt_tbl;
		}

		/**
	 * ���ص�ǰ����������ID
	 *
	 * @return int
	 */
		public function get_server_id()
		{
			return $this->server_id;
		}

		/**
	 * �������ݺ���
	 * @access protected
	 * @param array $insert_arr ��������
	 * @param int	$id ���²���ID
	 */
		protected function _insert_table_filed($insert_arr)
		{
			$insert_str = db_arr_to_update_str($insert_arr);

			$sql = "INSERT INTO member_db.{$this->opt_tbl} SET {$insert_str}";

			db_simple_getdata($sql, false, $this->server_id);
			$id = db_simple_get_insert_id();
			return $id;
		}

		/**
	 * ���±����ݺ���
	 * 
	 * @access protected
	 * @param array $update_arr Ҫ���µ��ֶ�����
	 * @param string $update_tbl Ҫ���µı��� 
	 * @param string $where_str Ҫ���µ�����
	 */
		protected function _update_table_filed($update_arr, $where_str)
		{
			$update_str = db_arr_to_update_str($update_arr);

			$sql = " UPDATE member_db.{$this->opt_tbl} SET $update_str WHERE {$where_str}";

			db_simple_getdata($sql, false, $this->server_id);
			return true;
		}

		/**
	 * ɾ��������
	 * @access protected
	 * @param string $where_str ɾ������
	 * @return bool
	 */
		protected function _del_table_flied($where_str)
		{
			if (empty($where_str))
			{
				return false;
			}
			$sql  = " DELETE FROM member_db.{$this->opt_tbl} WHERE {$where_str}";
			db_simple_getdata($sql, false, $this->server_id);
			return true;
		}

		/**
	 * ��ѯָ���������
	 * @access protected
	 * @param string $where_str ��ѯ����
	 * @param string $select_str ��ѯ�ֶ�
	 * @return array
	 */
		protected function _get_table_row_info($where_str, $select_str = "*")
		{
			if (empty($where_str))
			{
				return false;
			}
			$sql = " SELECT {$select_str} FROM member_db.{$this->opt_tbl}";
			$sql .= " WHERE {$where_str} LIMIT 0,1";

			$tmp = db_simple_getdata($sql, true, $this->server_id);
			return $tmp;
		}

		/**
	 * ȡ�����б����
	 * @access protected
	 * @param bool $b_select_count true:�����ܼ�¼�� false:������������
	 * @param string $sql sql���
	 * @param string $limit limit��¼��
	 * @param string $order_by ��������
	 * @return array
	 */
		protected function _get_table_list($b_select_count, $sql, $limit = "0,10", $order_by = "")
		{
			if ($b_select_count)
			{
				$count_sql = preg_replace('|SELECT.*FROM|i','SELECT COUNT(1) AS count FROM', $sql);
				$tmp = db_simple_getdata($count_sql, true, $this->server_id);
				return $tmp["count"]*1;
			}

			if ($order_by)
			{
				check_order_by($order_by);
				$sql .= " ORDER BY ".$order_by;
			}

			if ($limit)
			{
				check_limit_str($limit);
				$sql .= " LIMIT ".$limit;
			}

			$rows = db_simple_getdata($sql, false, $this->server_id);
			return $rows;
		}

		/**
	 * ���ô�����Ϣ
	 *
	 * @param int $errno
	 * @param string $errmsg
	 * @return true
	 */
		public function return_error($errno = 0, $errmsg = '')
		{
			$this->errno = $errno;
			$this->errmsg =	$errmsg;

			return true;
		}
	}
}
?>