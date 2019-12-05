<?php
/*
 * 榜单活动表
 */

class pai_rank_event_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_rank_event_tbl' );
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_info($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data );
	
	}
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $app_id
	 * @return bool 
	 */
	public function update_info($data, $id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}
	 
	
	/**
	 *  删除
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete_info($id, $b_select_count = false)
	{   
		if ($b_select_count == true) 
		{
			$sql = "DELETE FROM `pai_db`.`pai_rank_event_tbl`";
			return $this->query($sql);
		}
		else
		{
			$id = (int)$id;
			if (empty($id)) 
			{
				throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
			}
			$where_str = "id = {$id}";
			return $this->delete($where_str);
		}
		
	}
	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_rank_event_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,20', $fields = '*')
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

	/*
	 * 获取
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_serialize_rank_event_list($where_str='')
	{
		$total_count  = $this->findCount ( $where_str );
		$where_str = "1";
		$ret = $this->findAll($where_str, "0,{$total_count}", "sort_order DESC,id DESC");
		return serialize($ret);
	}
	
	public function get_rank_event_info($id)
	{
		$id = ( int ) $id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}

	/*
	* 获取榜单数据本地使用_V2
    * @param int $location_id 地区ID
    *  @param int $role 角色 ''|'model'|'cameraman'
    * return arrray $data|false
	*/
	public function get_rank_event_by_location_id_v2($location_id = 0 , $role = '')
	{
        $where_str = "1";
        if ($location_id > 0)
        {
            $location_id = (int)$location_id;
            $where_str .= " AND location_id = {$location_id}";
        }
        if ($role)
        {
            $where_str .= " AND (role = '{$role}' OR role = '')";
        }
		$total = $this->findCount ($where_str);
		$ret = $this->findAll ( $where_str, '0,{$total}', 'sort_order DESC,id DESC', '*' );
		include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");
		$cms_system_obj = POCO::singleton ( 'cms_system_class' );
		$arr = array();
		foreach ($ret as $key => $vo) 
		{
			$rank_info = $cms_system_obj->get_rank_info_by_rank_id($vo['rank_id']);
			$arr[$vo['rank_id']][0] = $rank_info['rank_name'];
			$arr[$vo['rank_id']][1] = $vo['id'];
			//echo $vo['unit'];
			if($vo['unit'] == 4){$arr[$vo['rank_id']][2] = '备注';}
			elseif($vo['unit'] == 3){$arr[$vo['rank_id']][2] = '小时';}
			elseif($vo['unit'] == 2){$arr[$vo['rank_id']][2] = '分';}
			elseif($vo['unit'] == 1){$arr[$vo['rank_id']][2] = '魅力';}
		    else{$arr[$vo['rank_id']][2] = '';}
			$arr[$vo['rank_id']][3] = $vo['url'];
			//$arr[$vo['rank_id']][4] = $vo['app_sort'] == 1 ? 'place_number ASC' : $vo['app_sort'] == 2 ? 'place_number DESC' : '';
			if($vo['app_sort'] == 2)     {$arr[$vo['rank_id']][4] = 'place_number DESC';}
			elseif($vo['app_sort'] == 1) {$arr[$vo['rank_id']][4] = 'place_number ASC';}
			else{$arr[$vo['rank_id']][4] = '';}
			$arr[$vo['rank_id']][5] = $vo['dmid'];
			unset($rank_info);
		}
		unset($ret);
		if ($arr) 
		{
			return $arr;
		}
		return false;
	}

	/*
	* 获取榜单数据
    * @param int $location_id 地区ID
    *  @param int $role 角色 ''|'model'|'cameraman'
    * return arrray $data|false
	*/
	public function get_rank_event_by_location_id($location_id, $role = '')
	{
		$location_id = (int)$location_id;
		if (empty($location_id)) 
		{
			return false;
		}
		$where_str = "location_id = {$location_id}";
		if ($role) 
		{
			$where_str .= " AND (role = '{$role}' OR role = '')";
		}
		$ret = $this->findAll ( $where_str, '0,20', 'sort_order DESC,id DESC', '*' );
		include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");
		$cms_system_obj = POCO::singleton ( 'cms_system_class' );
		$arr = array();
		foreach ($ret as $key => $vo) 
		{
			$rank_info = $cms_system_obj->get_rank_info_by_rank_id($vo['rank_id']);
			$arr[$vo['rank_id']][0] = $rank_info['rank_name'];
			$arr[$vo['rank_id']][1] = $vo['id'];
			//echo $vo['unit'];
			if($vo['unit'] == 4){$arr[$vo['rank_id']][2] = '备注';}
			elseif($vo['unit'] == 3){$arr[$vo['rank_id']][2] = '小时';}
			elseif($vo['unit'] == 2){$arr[$vo['rank_id']][2] = '分';}
			elseif($vo['unit'] == 1){$arr[$vo['rank_id']][2] = '魅力';}
		    else{$arr[$vo['rank_id']][2] = '';}
			$arr[$vo['rank_id']][3] = $vo['url'];
			//$arr[$vo['rank_id']][4] = $vo['app_sort'] == 1 ? 'place_number ASC' : $vo['app_sort'] == 2 ? 'place_number DESC' : '';
			if($vo['app_sort'] == 2)     {$arr[$vo['rank_id']][4] = 'place_number DESC';}
			elseif($vo['app_sort'] == 1) {$arr[$vo['rank_id']][4] = 'place_number ASC';}
			else{$arr[$vo['rank_id']][4] = '';}
			$arr[$vo['rank_id']][5] = $vo['dmid'];
			unset($rank_info);
		}
		unset($ret);
		if ($arr) {
            return $arr;
        }
		return false;
	}

	/*
	* 获取榜单数据
    *  @param int $id
    * return arrray $data|false
	*/
	public function get_rank_event_appinfo($id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			return false;
		}
		$where_str = "id = {$id}";
		$ret = $this->find($where_str);
		//print_r($ret);
		if ($ret) 
		{
			//单位
			if($ret['unit'] == 4){$ret['unit'] = '备注';}
			elseif($ret['unit'] == 3){$ret['unit'] = '小时';}
			elseif($ret['unit'] == 2){$ret['unit'] = '分';}
			elseif($ret['unit'] == 1){$ret['unit'] = '魅力';}
		    else{$ret['unit'] = '';}
		    //排序
		    if($ret['app_sort'] == 2){$ret['app_sort'] = 'place_number DESC';}
			elseif($ret['app_sort'] == 1) {$ret['app_sort'] = 'place_number ASC';}
			else{$ret['app_sort'] = '';}
		}
		//print_r($ret);
		return $ret;
	}
	
}

?>