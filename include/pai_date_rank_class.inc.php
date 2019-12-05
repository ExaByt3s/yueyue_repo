<?php
/*
 * 约拍次数排名操作类
 */

class pai_date_rank_class extends POCO_TDG
{
	
	private $cache_key = "YUEYUE_INTERFACE_DATE_RANK___";
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_date_rank_tbl' );
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_rank($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data );
	
	}
	
	/*
	 * 获取排名
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_rank_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_rank_info($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		return $ret;
	}
	
	/*
	 * 获取热门模特拍片次数
	 * 
	 * @param int $model_user_id 
	 */
	public function count_model_take_photo_times($model_user_id)
	{
		
		$cache_key = $this->get_take_photo_times_cache_key ( $model_user_id );
		$times = POCO::getCache ( $cache_key );
		
		if (! $times)
		{
			$ret = $this->get_rank_info ( $model_user_id );
			$times = $ret ['num'];
			
			$cache_time = 3600;
			POCO::setCache ( $cache_key, $times, array ('life_time' => $cache_time ) );
		}
		
		return (int)$times;
	}
	
	/*
	 * 获取摄影师拍片次数
	 * 
	 * @param int $user_id
	 */
	public function count_cameraman_take_photo_times($user_id)
	{
		$ret = $this->get_rank_info ( $user_id );
		$times = $ret ['num'];
			
		return (int)$times;
	}
	
	function get_date_rank($location_id = 0, $limit = '0,6')
	{

		
		$where_str = "1 AND role='model' AND is_bad_list=0";
		
		
		if($location_id){
			$where_str .= " AND location_id LIKE '{$location_id}%'";
		}
		$ret = $this->get_rank_list ( false, $where_str, 'num DESC,user_id ASC', $limit );
		
		foreach ( $ret as $k => $val )
		{
			$ret [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
			$ret [$k] ['user_icon'] = get_user_icon ($val ['user_id'], 165 );
		}
		return $ret;
	}
	
	public function get_take_photo_times_cache_key($user_id)
	{
		return $this->cache_key . $user_id;
	}
	
	public function get_date_rank_cache_key($user_id)
	{
		return $this->cache_key . $user_id;
	}
}

?>