<?php
/*
 * ͷ�εȼ���˲�����
 */

class pai_rank_audit_class extends POCO_TDG {
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct() {
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_rank_audit_tbl' );
	}
	
	/*
	 * ��������
	 * 
	 */
	public function add_info($insert_data) {
	
		if (empty ( $insert_data )) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		$insert_data ['add_time'] = time ();
		
		return $this->insert ( $insert_data, "IGNORE" );
	}
	
	/*
	 * ��������
	 * 
	 */
	public function update_user($update_data, $id) {
		$id = ( int ) $id;
	
		if (empty ( $update_data )) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		if (empty ( $id )) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}

	
		$where_str = "id = {$id}";
		return $this->update ( $update_data, $where_str );
	}
	
	/*
	 * ��ȡ����
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