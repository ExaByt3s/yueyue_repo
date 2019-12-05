<?php
/*
 * 头衔等级审核操作类
 */

class pai_rank_audit_class extends POCO_TDG {
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct() {
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_rank_audit_tbl' );
	}
	
	/*
	 * 插入数据
	 * 
	 */
	public function add_info($insert_data) {
	
		if (empty ( $insert_data )) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		$insert_data ['add_time'] = time ();
		
		return $this->insert ( $insert_data, "IGNORE" );
	}
	
	/*
	 * 更新数据
	 * 
	 */
	public function update_user($update_data, $id) {
		$id = ( int ) $id;
	
		if (empty ( $update_data )) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		if (empty ( $id )) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
		}

	
		$where_str = "id = {$id}";
		return $this->update ( $update_data, $where_str );
	}
	
	/*
	 * 获取数据
	 */
	public function get_info($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*') {
		
		if ($b_select_count == true) {
			$ret = $this->findCount ( $where_str );
		} else{
			$ret = $this->findAll ( $where_str, $limit, $order_by ,$fields);
		}
		return $ret;
	}
	
}

?>