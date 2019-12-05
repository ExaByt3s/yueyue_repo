<?php
/*
 * 积分排名操作类
 */

class pai_score_rank_class extends POCO_TDG
{
	
	private $cache_key = "YUEYUE_INTERFACE_SCORE_RANK____";
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_score_rank_tbl' );
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
	 * 获取积分排名
	 * 
	 * @param int $user_id 
	 */
	public function get_score_rank($user_id)
	{
		$user_id = (int) $user_id;
		$sql = "select recently_score as score,level from pai_score_db.pai_user_score_tbl where user_id={$user_id}";
		$ret = db_simple_getdata($sql,true,101);
		return $ret;
	}
	
	
	function get_score_rank_list($location_id = 0, $limit = '0,6')
	{
		
		$where_str = "1 AND score>100 and is_bad_list=0 ";
		$where_str .= " AND user_id NOT IN (100040, 102686, 102688, 102506, 102325, 102654, 102014, 102616, 101921, 102688) ";
			
		if($location_id){
			$where_str .= " AND location_id LIKE '{$location_id}%'";
		}
		
		$ret = $this->get_rank_list ( false, $where_str, 'score DESC,user_id ASC', $limit );
		
		foreach ( $ret as $k => $val )
		{
			$ret [$k] ['num'] = $val['score'];
			$ret [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
			$ret [$k] ['user_icon'] = get_user_icon ($val ['user_id'], 165 );
		}
		return $ret;
	}
	
	private function get_rank_cache_key($user_id)
	{
		return $this->cache_key . $user_id;
	}
}

?>