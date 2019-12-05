<?php
/*
 * 约拍手机交互类
 */

class pai_mobile_method_class extends POCO_TDG {
    
	/**
	 * 构造函数
	 *
	 */
	public function __construct() {
		$this->setServerId(101);
		$this->setDBName('pai_nfc_db');
		$this->setTableName('yuepai_mobile_info_tbl');
	}
    
    public function get_mobile_info_by_user_id($user_id){
        $user_id = (int)$user_id;
        if($user_id)
        {
            $where_str = "pocoid = $user_id";
            return $this->find($where_str);
        }
    }
}
	