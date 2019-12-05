<?php
/*
 * 城市操作类
 */

class pai_city_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_city_tbl' );
	}
	
	/*
	 * 获取城市
	 * 
	 * return array
	 */
	public function get_city_list()
	{
		
		$sql = "select word from pai_db.pai_city_tbl group by word order by word asc";
		$word_info = db_simple_getdata ( $sql, false, 101 );
		
		foreach ( $word_info as $k => $val )
		{
			$word = $val ['word'];
			$sql = "select city,location_id from pai_db.pai_city_tbl where word='{$word}'";
			$city = db_simple_getdata ( $sql, false, 101 );
			$new_arr [$k] ['word'] = strtoupper ( $word );
			$new_arr [$k] ['city'] = $city;
		}
		
		return $new_arr;
	}

}

?>