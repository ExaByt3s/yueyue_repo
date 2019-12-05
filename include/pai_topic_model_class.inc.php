<?php

class pai_topic_model_class extends POCO_TDG
{
 	/**
	 * 构造函数
	 *
	 */
	public function __construct($topic_id = 1)
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_topic_db' );
		$this->setTableName ( 'pai_topic_1_tbl' );
	}  
    
    public function pai_settablename($table_name)
    {
        $this->setTableName($table_name);
    }
    
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_topic($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data );
	
	}
    
    public function del_topic($id)
    {
        $id = (int)$id;
        if($id)
        {
            $sql_str = "DELETE FROM pai_topic_db.pai_topic_1_tbl WHERE id=$id";
            db_simple_getdata($sql_str, TURE, 101);
            return true;    
        }else{
            return false;
        }
        
    } 
    
	public function get_topic_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
    
	public function get_topic_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}
}
?>