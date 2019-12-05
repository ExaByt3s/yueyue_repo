<?php
/*
 * 模特拍摄风格操作类
 */

class pai_model_style_v2_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_style_v2_tbl' );
	}
	
	/*
	 * 添加模特拍摄风格
	 * 
	 * @param int    $user_id 用户ID
	 * @param array  $style_arr 拍摄风格   逗号分隔传过来    如：  清新,田园
	 * @param array  $price_arr 拍摄价格
	 * @param array  $hour_arr 拍摄价格类型  传2或4
	 * @param array  $continue_price_arr 续拍价格
	 * 
	 * return bool 
	 */
	public function add_model_style($user_id, $style_arr)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		
		if (empty ( $style_arr ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':拍摄风格数组不能为空' );
		}
		
		//把原有的删除再重新添加新的风格和价格
		$this->del_model_style ( $user_id );
		
		foreach ( $style_arr['main'] as $k => $style_str)
		{
			$style_detail_arr = explode(",",$style_str['style']);
					
			foreach($style_detail_arr as $style)
			{
				$insert_data ['user_id'] = $user_id;
				$insert_data ['group_id'] = 1;
				$insert_data ['style'] = $style;
				$insert_data ['price'] = $style_str['price'];
				$insert_data ['hour'] = $style_str['hour'];
				$insert_data ['continue_price'] = $style_str['continue_price'];
				$this->insert ( $insert_data );
			}
		}
		
		foreach ( $style_arr['other'] as $k => $style_str)
		{
			$style_detail_arr = explode(",",$style_str['style']);
			
			$group_id = $k+2;
					
			foreach($style_detail_arr as $style)
			{
				$insert_data ['user_id'] = $user_id;
				$insert_data ['group_id'] = $group_id;
				$insert_data ['style'] = $style;
				$insert_data ['price'] = $style_str['price'];
				$insert_data ['hour'] = $style_str['hour'];
				$insert_data ['continue_price'] = $style_str['continue_price'];
				$this->insert ( $insert_data );
			}
		}
		return true;
	}
	
	/*
	 * 获取模特拍摄风格
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_model_style_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,15', $fields = '*')
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
	 * 获取模特拍摄风格（已组合数据）
	 */
	public function get_model_style_combo($user_id)
	{
		$user_id = (int)$user_id;
		$group_arr = $this->get_model_style_group($user_id);
		
		foreach($group_arr as $k=>$val)
		{
			$group_id = $val['group_id'];
			
			$where = "user_id={$user_id} and group_id={$group_id}";
			$style_arr = $this->get_model_style_list(false, $where,'id ASC');
	
			if($group_id==1)
			{
				foreach($style_arr as $style)
				{
					$style_detail_arr[] = $style['style'];
				}	
				
				$ret['main'][$k]['style'] = implode(" ",$style_detail_arr);
				$ret['main'][$k]['price'] = $style_arr[0]['price'];
				$ret['main'][$k]['continue_price'] = $style_arr[0]['continue_price'];
				$ret['main'][$k]['hour'] = $style_arr[0]['hour'];
				
			}else
			{
				foreach($style_arr as $style)
				{
					$style_detail_arr[] = $style['style'];
				}
				
				$other_k = $k-1;
				if($other_k<0) 
				{
					$other_k = 0;
				}
				
				$ret['other'][$other_k]['style'] = implode(" ",$style_detail_arr);
				$ret['other'][$other_k]['price'] = $style_arr[0]['price'];
				$ret['other'][$other_k]['continue_price'] = $style_arr[0]['continue_price'];
				$ret['other'][$other_k]['hour'] = $style_arr[0]['hour'];
				
			}
			unset($style_detail_arr);
		}
		
		return $ret;
	}
	
	public function get_model_style_group($user_id)
	{
		$user_id = (int)$user_id;
		$sql = "select group_id from pai_db.pai_model_style_v2_tbl where user_id={$user_id} group by group_id order by group_id asc";
		return $this->findBySql($sql);
	}
	
	/*
	 * 删除模特拍摄风格
	 * 
	 * @param int $user_id
	 */
	public function del_model_style($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * 根据用户ID获取模特拍摄风格
	 * 
	 * @param int $user_id
	 * 
	 * return array
	 */
	public function get_model_style_by_user_id($user_id, $fields = '*')
	{
		$alter_price_obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
		$alter_price_log_obj = POCO::singleton ( 'pai_alter_price_log_class' );
		
		$alter_price_detail = $alter_price_obj->get_alter_price_user_detail($user_id);
		
		$user_id = ( int ) $user_id;
		$ret = $this->get_model_style_list ( false, "user_id={$user_id}", 'id ASC', '0,20', $fields );
		$i=0;
		foreach($ret as $val)
		{
			$style_arr = explode(" ",trim($val['style']));
			foreach($style_arr as $style_val)
			{
				$new_ret[$i]= $val;
				$new_ret[$i]['style'] = $style_val;
				$new_ret[$i]['old_price'] = '';
				$new_ret[$i]['old_hour'] = '';
				
				
				$md5_key = md5($val['user_id'].$style_val);
				
				//检查是否有改价记录
				if($alter_price_detail[$md5_key])
				{
					$new_ret[$i]['old_price'] = $val['price'];
					$new_ret[$i]['old_hour'] = $val['hour'];
					
					$alter_type = $alter_price_detail[$md5_key]['alter_type'];
					$type_value = $alter_price_detail[$md5_key]['type_value'];
					
					$ret_alter = $alter_price_log_obj->change_alter_price($alter_type,$type_value,$val['price'],$val['hour']);
					
					$new_ret[$i]['price'] = $ret_alter['price'];
					$new_ret[$i]['hour'] = $ret_alter['hour'];
					
				}
				$i++;
			}
			unset($style_arr);
			
		}
		return $new_ret;
	}
	
	
	public function get_model_style_by_user_id_test($user_id, $fields = '*')
	{
		$alter_price_obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
		$alter_price_log_obj = POCO::singleton ( 'pai_alter_price_log_class' );
		
		$alter_price_detail = $alter_price_obj->get_alter_price_user_detail($user_id);
		
		$user_id = ( int ) $user_id;
		$ret = $this->get_model_style_list ( false, "user_id={$user_id}", 'id ASC', '0,20', $fields );
		$i=0;
		foreach($ret as $val)
		{
			$style_arr = explode(" ",trim($val['style']));
			foreach($style_arr as $style_val)
			{
				$new_ret[$i]= $val;
				$new_ret[$i]['style'] = $style_val;
				$new_ret[$i]['old_price'] = '';
				$new_ret[$i]['old_hour'] = '';
				
				$md5_key = md5($val['user_id'].$style_val);
				
				//检查是否有改价记录
				if($alter_price_detail[$md5_key])
				{
					$new_ret[$i]['old_price'] = $val['price'];
					$new_ret[$i]['old_hour'] = $val['hour'];
					
					$alter_type = $alter_price_detail[$md5_key]['alter_type'];
					$type_value = $alter_price_detail[$md5_key]['type_value'];
					
					$ret_alter = $alter_price_log_obj->change_alter_price($alter_type,$type_value,$val['price'],$val['hour']);
					
					$new_ret[$i]['price'] = $ret_alter['price'];
					$new_ret[$i]['hour'] = $ret_alter['hour'];
				}
				$i++;
			}
			unset($style_arr);
			
		}
		return $new_ret;
	}	
}

?>