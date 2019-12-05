<?php
/*
 * 模板操作类
 * xiao xiao
 * 2015-1-16
 */

class pai_template_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_template_tbl' );
	}
	
	/*
     *获取模板列表
     *@param boolean $b_select_count 是否获取总数
     *@parma string $where_str 查询条件
     *@param sting $order_by 排序
     *@param string $limit 查询条数
     *@param string $fields 字段名
	*/
	public function get_template_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str);
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}


	/*
     * 更新模板信息
     *@param array $data
     *@param int $id
     *return boolean 
	*/
	public function update_template_info_by_id($data, $id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
		}
		$where_str = "id = {$id}";
		$ret = $this->update($data, $where_str);
		return $ret;
	}

	/*
     * 插入数据
     *
	*/
	public function insert_template_by_data($data)
	{
		if (empty($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':添加数据不能为空' );
		}
		$ret = $this->insert($data);
		return $ret;
	}

	/*
     * 删除数据成功
	*/
	public function delete_template_info_by_id($id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
		}
		$where_str = "id = {$id}";
		$ret = $this->delete($where_str);
		return $ret;
	}
	/*
	 * 获取模板基本信息
	 * @param int $user_id 用户id
	 * return array
	 */
	public function get_template_info_by_id($id)
	{
		$id = intval($id);
		if (empty ($id ))
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
		}
		$where_str = "id = {$id}";
		$ret = $this->find($where_str);
		return $ret;
	}
	
	
}

?>