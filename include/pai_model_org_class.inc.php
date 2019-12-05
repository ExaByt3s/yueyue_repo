<?php
/*
 * xiao xiao
 * 模特机构模型 审核模型
 */

class pai_model_org_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_org_putaway' );
	}

	/*
     * 搜索uid
     * $where_str
     * return array $ret;
	*/
	public function model_org_list($b_select_count = false, $where_str = '', $order_by = 'uid DESC', $limit = '0,10', $fields = '*')
	{

		if ($b_select_count == true) 
		{
			$ret = $this->findCount($where_str);
		}
		else
		{
			//var_dump($order_by);
			$ret = $this->findAll( $where_str, $limit, $order_by, $fields );
			//var_dump($ret);
		}
		return $ret;
	}

	/*
     * 插入数据
     *@param array $data
	*/
	public function model_org_insert_data($data)
	{
		//var_dump($data);
		if (empty($data) || !is_array($data))
		{			
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		$ret = $this->insert($data);
		return $ret;
	}

	/*
     * 下架 删除表数据
     *@param string delete_up_by_where_str
	*/
	public function delete_up_by_where_str($where_str)
	{
		if (empty($where_str))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':条件不能为空' );
		}
		$ret = $this->delete($where_str);
		return $ret;
	}


}

?>