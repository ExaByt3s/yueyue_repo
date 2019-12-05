<?php
/**
 * POCO_TDG POCO数据库操作类
 * 
 * @author ERLDY <erldy@126.com>
 * @package core
 */

if (!class_exists("POCO_TDG", false))
{
	class POCO_TDG
	{
		//数据库名
		protected $_db_name;
		
		//操作表名
		protected $_tbl_name;
		
		//服务器ID
		protected $_server_id = false;	
		
		/**
		 * 构造函数，初始化操作对象
		 *
		 * @param string $tbl_name   初始化操作表名
		 * @param string $db_name    初始化操作数据库
		 * @param int    $server_id  初始化数据库服务器ID
		 */
		public function __construct($tbl_name = '', $db_name = '', $server_id = null)
		{	
			// 设置应用项目的数据库服务器ID
			if (!empty($server_id)) 
			{
				$this->setServerId($server_id);
			}
			// 设置应用项目的数据库名称
			if (!empty($db_name)) 
			{
				$this->setDBName($db_name);
			}
			// 设置操作的表名
			if (!empty($tbl_name)) 
			{
				$this->setTableName($tbl_name);
			}	
		}
		
		/**
		 * 设置服务器ID
		 *
		 * @param int $server_id
		 */
		protected function setServerId($server_id)
		{
			$this->_server_id = $server_id;
		}
		
		/**
		 * 设置操作数据库名
		 *
		 * @param string $db_name
		 */
		protected function setDBName($db_name)
		{
			$this->_db_name = $db_name;
		}
		
		/**
		 * 设置操作表名
		 *
		 * @param string $tbl_name
		 */
		protected function setTableName($tbl_name)
		{
			$this->_tbl_name = $tbl_name;
		}
		
		/**
		 * 返回当前操作服务器ID
		 *
		 * @return int
		 */
		public function getServerId()
		{
			return $this->_server_id;
		}
		
		/**
		 * 返回当前操作数据库名
		 *
		 * @return int
		 */
		public function getDBName()
		{
			return $this->_db_name;
		}
		
		/**
		 * 返回当前操作表名
		 *
		 * @return string
		 */
		public function getTableName()
		{
			return $this->_tbl_name;
		}
		
		/**
		 * 查询所有符合条件的记录数据，返回一个包含多行记录的二维数组，失败时返回 false
		 *
		 * @param string $where      查询条件
		 * @param string $sort       排序
		 * @param string $limit      查询记录数
		 * @param mixed $fields      查询字段
		 *
		 * @return array
		 */
		public function findAll($where = null, $limit = null, $sort = null, $fields = '*')
		{
			// 查询条件
			$whereby = $where != '' ? "WHERE {$where}" : '';
			// 处理排序
			$sortby = $sort != '' ? " ORDER BY {$sort}" : '';
			// 处理 $limit
			if (!empty($limit) && preg_match("/^\d+,\d+$/i", $limit))
			{
				list($length, $offset) = explode(',', $limit);
			} 
			else 
			{
				//必须有limit 限制
				$length = 0;
				$offset = 1000;
			}
			
			// 构造从主表查询数据的 SQL 语句
			$sql = "SELECT {$fields} FROM {$this->_db_name}.{$this->_tbl_name} {$whereby} {$sortby}";
			// 根据 $length 和 $offset 参数决定是否使用限定结果集的查询
			if (null !== $length || null !== $offset)
			{
				$sql = sprintf('%s LIMIT %d,%d', $sql, $length, (int)$offset);
			}
			// 查询
			$rows = db_simple_getdata($sql, false, $this->_server_id);
			return $rows;
		}
		
		/**
		 * 返回符合条件的第一条记录数据，查询没有结果返回 false
		 *
		 * @param string $where 查询条件
		 * @param string $sort  排序条件
		 * @param mixed $fields 查询字段
		 *
		 * @return array
		 */
		public function find($where, $sort = null, $fields = '*')
		{
			// 查询条件
			$whereby = $where != '' ? "WHERE {$where}" : '';
			// 处理排序
			$sortby = $sort != '' ? " ORDER BY {$sort}" : '';
			// 构造从主表查询数据的 SQL 语句
			$sql = "SELECT {$fields} FROM {$this->_db_name}.{$this->_tbl_name} {$whereby} {$sortby} LIMIT 0,1";
			// 查询
			$row = db_simple_getdata($sql, true, $this->_server_id);
			return $row;
		}
		
		/**
		 * 直接使用 sql 语句获取记录
		 *
		 * @param string $sql
		 *
		 * @return array
		 */
		public function findBySql($sql)
		{
			if (empty($sql)) 
			{
				throw new App_Exception('SQL语句异常，请检查');
			}
			// 查询
			$rows = db_simple_getdata($sql, false, $this->_server_id);
			return $rows;
		}
		
		/**
		 * 统计符合条件的记录的总数
		 *
		 * @param string $where  查询条件
		 * @param string $fields 统计字段
		 *
		 * @return int
		 */
		public function findCount($where = null, $fields = '*')
		{
			// 查询条件
			$whereby = $where != '' ? "WHERE {$where}" : '';
			
			$sql = "SELECT COUNT({$fields}) AS c FROM {$this->_db_name}.{$this->_tbl_name} {$whereby}";
			$tmp = db_simple_getdata($sql, true, $this->_server_id);
			return (int)$tmp['c'];
		}
		
		/**
		 * 插入数据到数据库
		 *
		 * @param array $row          插入的数据数组
		 * @param string $insert_type 插入数据方式：REPLACE替换插入，IGNORE防止重复插入
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
		 * 更新表数据函数
		 *
		 * @param array $row    更新的数据数组
		 * @param string $where 更新条件
		 * 
		 * @return mixed
		 */
		public function update($row, $where) 
		{
			if (empty($where)) 
			{
				throw new App_Exception('更新条件不能为空，请检查');
			}
			$update_str = db_arr_to_update_str($row);
			
			if (empty($update_str)) 
			{
				throw new App_Exception('更新内容不能为空，请检查');
			}

			$sql = " UPDATE {$this->_db_name}.{$this->_tbl_name} SET {$update_str} WHERE {$where}";

			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * 更新记录的指定字段，返回更新的记录总数
		 *
		 *
		 * @param mixed $where  更新条件
		 * @param string $field 更新字段
		 * @param mixed $value  更新的值
		 *
		 * @return int
		 */
		public function updateField($where, $field, $value)
		{
			if (empty($where)) 
			{
				throw new App_Exception('更新条件不能为空，请检查');
			}
			
			if (empty($field)) 
			{
				throw new App_Exception('更新字段不能为空，请检查');
			}
			
			$sql = " UPDATE {$this->_db_name}.{$this->_tbl_name} SET {$field} = '{$value}' WHERE {$where}";

			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * 增加符合条件的记录的指定字段的值，返回更新的记录总数
		 *
		 * @param mixed $where  更新条件
		 * @param string $field 指定字段
		 * @param int $incr     增加的值
		 *
		 * @return mixed
		 */
		public function incrField($where, $field, $incr = 1)
		{
			// 更新条件
			$whereby = $where != '' ? "WHERE {$where}" : '';
			$incr = (int)$incr;
			$sql = "UPDATE {$this->_db_name}.{$this->_tbl_name} SET {$field} = {$field} + {$incr} {$whereby}";
			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * 减小符合条件的记录的指定字段的值，返回更新的记录总数
		 *
		 * @param mixed $where  更新条件
		 * @param string $field 指定字段
		 * @param int $decr     减少的值
		 *
		 * @return mixed
		 */
		public function decrField($where, $field, $decr = 1)
		{
			// 更新条件
			$whereby = $where != '' ? "WHERE {$where}" : '';
			$incr = (int)$decr;
			$sql = "UPDATE {$this->_db_name}.{$this->_tbl_name} SET {$field} = {$field} - {$incr} {$whereby}";
			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * 删除表数据
		 * 
		 * @param string $where 删除条件
		 * 
		 * @return bool
		 */
		public function delete($where)
		{
			// 删除条件不能为空
			if (empty($where)) 
			{
				throw new App_Exception('删除条件不能为空，请检查');
			}
			$sql  = " DELETE FROM {$this->_db_name}.{$this->_tbl_name} WHERE {$where}";
			db_simple_getdata($sql, true, $this->_server_id);
			return (int)db_simple_get_affected_rows();
		}
		
		/**
		 * 直接执行 SQL 语句
		 *
		 * @param string $sql
		 * 
		 * @return mixed
		 */
		public function query($sql)
		{
			 if (empty($sql)) 
			{
				throw new App_Exception('SQL语句异常，请检查');
			}
			return db_simple_getdata($sql, false, $this->_server_id);;
		}
		
		/**
		 * 获取最后自增ID
		 *
		 * @return int
		 */
		public function get_last_insert_id()
		{
			return db_simple_get_insert_id();
		}

		/**
		 * 获取最后修改记录数
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