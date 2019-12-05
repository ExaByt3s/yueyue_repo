<?php
/*
 * 评价排名操作类
 */

class pai_comment_score_rank_class extends POCO_TDG
{
	
	private $cache_key = "YUEYUE_INTERFACE_COMMENT_SCORE_RANK__";
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_comment_score_rank_tbl' );
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
	public function get_comment_rank_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_comment_rank_info($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		return $ret;
	}
	
	/*
	 * 获取排名
	 * 
	 * @param int $user_id 
	 */
	public function get_comment_score_rank($user_id)
	{
		
		$cache_key = $this->get_comment_rank_cache_key ( $user_id );
		$rank_num = POCO::getCache ( $cache_key );
		
		if (! $rank_num)
		{
			$ret = $this->get_comment_rank_info ( $user_id );
			$rank_num = $ret['num'];
			$cache_time = 3600;
			POCO::setCache ( $cache_key, $rank_num, array ('life_time' => $cache_time ) );
		}
		
		return $rank_num;
	}
	
	/*
	 * 获取评价平均分
	 * @param int $user_id 
	 */
	public function get_comment_rank($location_id,$limit='0,6'){
	
		$bad_list_obj = POCO::singleton ( 'pai_bad_class' );
	
		$where_str = "1 AND is_bad_list=0";
		$where_str .= " AND user_id NOT IN (100040, 102686, 102688, 102506, 102325) ";
			
		if($location_id){
			$where_str .= " AND location_id LIKE '{$location_id}%'";
		}
		$where_str .= " AND role='model'";
		$ret = $this->get_comment_rank_list ( false, $where_str, 'num DESC,user_id ASC', $limit );
		
		foreach ( $ret as $k => $val )
		{
			$ret [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
			$ret [$k] ['user_icon'] = get_user_icon ($val ['user_id'], 165 );
		}
		return $ret;
	}
	
	public function get_comment_rank_cache_key($user_id)
	{
		return $this->cache_key . $user_id;
	}
}

?>