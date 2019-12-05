<?php
/**
 * 排行榜数据库操作类
 * @author Bowie
 */

class cms_db_class 
{
	var $_cms_tbl_name_arr = array("channel_tbl","issue_tbl","rank_tbl","record_tbl","record_tbl_last_issue","record_tbl_freeze");
	var $_db_server_id = 101;
	
	function cms_db_class($server_id=101) 
	{
		$this->set_server_id($server_id);
	}
	
	function set_server_id($server_id)
	{
		$this->_db_server_id = $server_id;
	}
	
	function _check_tbl_name($tbl_name)
	{
		if (!in_array($tbl_name, $this->_cms_tbl_name_arr))
		{
			die(__CLASS__." ERROR: tbl name erro tbl_name={$tbl_name}");
		}
	}
	
	function insert_cms($tbl_name, $insert_data, $b_ignore=false)
	{
		$this->_check_tbl_name($tbl_name);
		if (!is_array($insert_data))  die(__CLASS__."::".__FUNCTION__." ERROR: insert data is not array");

		$insert_str = db_arr_to_update_str($insert_data);
		
		if ($b_ignore) 
		{
			$sql = "INSERT IGNORE INTO pai_cms_db.cms_{$tbl_name} SET ".$insert_str;
		}
		else 
		{
			$sql = "INSERT INTO pai_cms_db.cms_{$tbl_name} SET ".$insert_str;
		}
		
		db_simple_getdata($sql,false,$this->_db_server_id);

		$insert_id =  db_simple_get_insert_id();

		return $insert_id;
	}
	
	function replace_cms($tbl_name, $insert_data)
	{
		$this->_check_tbl_name($tbl_name);
		if (!is_array($insert_data))  die(__CLASS__."::".__FUNCTION__." ERROR: insert date is not array");

		$insert_str = db_arr_to_update_str($insert_data);

		$sql = "REPLACE INTO pai_cms_db.cms_{$tbl_name} SET ".$insert_str;
		db_simple_getdata($sql,false,$this->_db_server_id);

		$insert_id =  db_simple_get_insert_id();

		return $insert_id;
	}
	
	function update_cms($tbl_name, $where_sql, $update_data)
	{
		$this->_check_tbl_name($tbl_name);
		
		$where_sql = trim($where_sql);
		if( strlen($where_sql)==0 ) die(__CLASS__."::".__FUNCTION__." ERROR: where_sql is empyt");

		if (is_array($update_data))
		{
			$update_str = db_arr_to_update_str($update_data);
		}
		elseif (is_string($update_data))
		{
			$update_str = $update_data;
		}
		else
		{
			die(__CLASS__."::".__FUNCTION__." ERROR: update_data = ".$update_data);
		}

		$sql = "UPDATE pai_cms_db.cms_{$tbl_name} SET ".$update_str;
		$sql.= " WHERE {$where_sql}";
		db_simple_getdata($sql,false,$this->_db_server_id);
		
		return db_simple_get_affected_rows();
	}
	
	function delete_cms($tbl_name, $where_sql)
	{
		$this->_check_tbl_name($tbl_name);
		
		$where_sql = trim($where_sql);
		if( strlen($where_sql)==0 ) die(__CLASS__."::".__FUNCTION__." ERROR: where_sql is empyt");
		
		$sql = "DELETE FROM pai_cms_db.cms_{$tbl_name} WHERE {$where_sql}";
		db_simple_getdata($sql,false,$this->_db_server_id);
		
		return db_simple_get_affected_rows();
	}
	
	function get_cms_info($tbl_name, $where_sql="", $select_str="*")
	{
		$this->_check_tbl_name($tbl_name);
		
		$where_sql = trim($where_sql);
		if( strlen($where_sql)==0 ) die(__CLASS__."::".__FUNCTION__." ERROR: where_sql is empyt");
		
		$sql = "SELECT {$select_str} FROM pai_cms_db.cms_{$tbl_name} WHERE {$where_sql}";
		$ret = db_simple_getdata($sql, true, $this->_db_server_id);
		return $ret;
	}
	
	function get_cms_list($tbl_name, $where_sql="",$select_str="*", $order_by="", $group_by="", $limit="")
	{
		$this->_check_tbl_name($tbl_name);
		
		$where_sql = trim($where_sql);
		if( strlen($where_sql)>0 ) $where_sql = "WHERE {$where_sql}";
		
		$sql = "SELECT {$select_str} FROM pai_cms_db.cms_{$tbl_name} {$where_sql}";
		$sql.= ($group_by) ? " GROUP BY {$group_by}" : "";
		
		if ($order_by) 
		{
			check_order_by($order_by);
			$sql.= " ORDER BY {$order_by}";
		}
		
		if($limit)
		{
			check_limit_str($limit);
			$sql.= " LIMIT {$limit}";
		}
		$ret = db_simple_getdata($sql, false, $this->_db_server_id);
		return $ret;
	}
	
	function get_cms_count($tbl_name, $where_sql, $group_by="")
	{
		$this->_check_tbl_name($tbl_name);
		
		$where_sql = trim($where_sql);
		if( strlen($where_sql)>0 ) $where_sql = "WHERE {$where_sql}";
		
		$sql = "SELECT COUNT(*) AS total FROM pai_cms_db.cms_{$tbl_name} {$where_sql}";
		$sql.= ($group_by) ? " GROUP BY {$group_by}" : "";
		
		if ($group_by) 
		{
			$ret = db_simple_getdata($sql, false, $this->_db_server_id);
			return count($ret);
		}
		else 
		{
			$ret = db_simple_getdata($sql, true, $this->_db_server_id);
			return $ret["total"];
		}
	}
}
?>