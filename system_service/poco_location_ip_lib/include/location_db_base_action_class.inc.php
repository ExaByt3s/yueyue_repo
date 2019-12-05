<?php
/**
 * db_base_action_class 数据库基础操作类
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
		//操作表名
		protected $opt_tbl;

		//服务器ID
		protected $server_id = false;

		//最后一次错误代码
		public $errno = 0;

		//最后一次错误信息
		public $errmsg = '';

		/**
	 * 设置操作表名
	 *
	 * @param string $tbl_name
	 */
		protected function set_tbl_name($tbl_name)
		{
			$this->opt_tbl = $tbl_name;
		}

		/**
	 * 设置服务器ID
	 *
	 * @param int $server_id
	 */
		protected function set_server_id($server_id)
		{
			$this->server_id = $server_id;
		}

		/**
	 * 返回当前操作表名
	 *
	 * @return string
	 */
		public function get_tbl_name()
		{
			return $this->opt_tbl;
		}

		/**
	 * 返回当前操作服务器ID
	 *
	 * @return int
	 */
		public function get_server_id()
		{
			return $this->server_id;
		}

		/**
	 * 插入数据函数
	 * @access protected
	 * @param array $insert_arr 数据数组
	 * @param int	$id 最新插入ID
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
	 * 更新表数据函数
	 * 
	 * @access protected
	 * @param array $update_arr 要更新的字段数组
	 * @param string $update_tbl 要更新的表名 
	 * @param string $where_str 要更新的条件
	 */
		protected function _update_table_filed($update_arr, $where_str)
		{
			$update_str = db_arr_to_update_str($update_arr);

			$sql = " UPDATE member_db.{$this->opt_tbl} SET $update_str WHERE {$where_str}";

			db_simple_getdata($sql, false, $this->server_id);
			return true;
		}

		/**
	 * 删除表数据
	 * @access protected
	 * @param string $where_str 删除条件
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
	 * 查询指定结果函数
	 * @access protected
	 * @param string $where_str 查询条件
	 * @param string $select_str 查询字段
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
	 * 取数据列表操作
	 * @access protected
	 * @param bool $b_select_count true:返回总记录数 false:返回数据数组
	 * @param string $sql sql语句
	 * @param string $limit limit记录数
	 * @param string $order_by 排序条件
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
	 * 设置错误信息
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