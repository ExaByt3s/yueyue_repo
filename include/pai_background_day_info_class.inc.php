<?php
/*
 * 模特、摄影师、交易成功数据统计模型
 */

class pai_background_day_info_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		
	}
	
	/*
	 * 模特数据数量
	 */
	public function day_model_count($start_date = '', $end_date = '')
	{
		if (empty($start_date) || empty($end_date)) 
		{
			$start_date = date('Y-m-d', strtotime('-5 day'));
			$end_date   = date('Y-m-d', time());

		}
		$start_date = strtotime($start_date);
		$end_date   = strtotime($end_date);
		$sql = "SELECT COUNT(user_id) AS model_count,FROM_UNIXTIME(u.add_time,'%Y-%m-%d') AS add_time from `pai_db`.`pai_user_tbl` u  WHERE u.role = 'model' AND UNIX_TIMESTAMP(FROM_UNIXTIME(u.add_time,'%Y-%m-%d')) BETWEEN {$start_date} AND {$end_date} GROUP BY FROM_UNIXTIME(u.add_time,'%Y-%m-%d') DESC ";
		$this->_server_id = '101';
		return $this->deal_time_key($this->findBySql($sql));
	}
	
	/*
	 * 摄影师数量
	 */
	public function day_cameraman_count($start_date = '', $end_date = '')
	{
		if (empty($start_date) || empty($end_date)) 
		{
			$start_date = date('Y-m-d', strtotime('-5 day'));
			$end_date   = date('Y-m-d', time());

		}
		$start_date = strtotime($start_date);
		$end_date   = strtotime($end_date);
		$sql = "SELECT COUNT(user_id) AS cameraman_count,FROM_UNIXTIME(u.add_time,'%Y-%m-%d') AS add_time from `pai_db`.`pai_user_tbl` u  WHERE u.role = 'cameraman' AND UNIX_TIMESTAMP(FROM_UNIXTIME(u.add_time,'%Y-%m-%d')) BETWEEN {$start_date} AND {$end_date} GROUP BY FROM_UNIXTIME(u.add_time,'%Y-%m-%d') DESC";
		$this->_server_id = '101';
		return $this->deal_time_key($this->findBySql($sql));
	}
	
	/*
	 * 约拍成功数量
	 */
	public function day_date_confirm_count($start_date = '', $end_date = '')
	{
		if (empty($start_date) || empty($end_date)) 
		{
			$start_date = date('Y-m-d', strtotime('-5 day'));
			$end_date   = date('Y-m-d', time());

		}
		$start_date = strtotime($start_date);
		$end_date   = strtotime($end_date);
		$sql = "SELECT COUNT(date_id) AS confirm_count,FROM_UNIXTIME(u.update_time,'%Y-%m-%d') AS add_time from `event_db`.`event_date_tbl` u  WHERE date_status = 'confirm' AND UNIX_TIMESTAMP(FROM_UNIXTIME(u.update_time,'%Y-%m-%d')) BETWEEN {$start_date} AND {$end_date} GROUP BY FROM_UNIXTIME(u.update_time,'%Y-%m-%d') DESC";
		$this->_server_id = '0';
		return $this->deal_time_key($this->findBySql($sql));
	}


	/*
	* 时间处理
	*/
	public function deal_time_key($arr ='')
	{
		static $tmp_arr  = array();
		if (!is_array($arr) && empty($arr)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':arr必须为数组');
		}

		foreach ($arr as $key => $val) 
		{
			
			$tmp_arr[$val['add_time']] = $val;
		}
		return $tmp_arr;
		
	}

	/*
	* 数组合并
	*/
	public function combile_array_key($arr1, $arr2, $arr3)
	{
		$tmp_arr = array();
		if (is_array($arr1) && !empty($arr1)) 
		{
			foreach ($arr1 as $key => $val) 
			{
				$tmp_arr[] = array_merge($val, $arr2[$key], $arr3[$key]);
				unset($arr2[$key]);
				unset($arr3[$key]);
			}
			if (is_array($arr2) && !empty($arr2)) 
			{
				foreach ($arr2 as $key2 => $val2) 
				{
					$tmp_arr[] = array_merge($val2, $arr3[$key2]);
					unset($arr3[$key2]);
				}
			}
			if (is_array($arr3) && !empty($arr3)) 
			{
				foreach ($arr3 as $key3 => $val3) 
				{
					$tmp_arr[] = $val3;
				}
			}
		}

		elseif (!is_array($arr1) || empty($arr1)) 
		{
			if (is_array($arr2) && !empty($arr2)) 
			{
				foreach ($arr2 as $key2 => $val2) 
				{
					$tmp_arr[] = array_merge($val2, $arr3[$key2]);
					unset($arr3[$key2]);
				}
			}
			if (is_array($arr3) && !empty($arr3)) 
			{
				foreach ($arr3 as $key3 => $val3) 
				{
					$tmp_arr[] = $val3;
				}
			}
		}
		return $this->bg_day_assort($tmp_arr, 'add_time');
	}

	/*
     * 多维数组排序
	*/
	public function bg_day_assort($arr, $order = 'add_time')
	{
		$tmp_arr = array();
		if (!is_array($arr) && empty($arr)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':arr必须为数组');
		}

		$len = count($arr);
		for ($i = 0; $i < $len - 1 ; $i++) 
		{ 
			for ($j=0; $j < $len - 1 - $i ; $j ++) 
			{ 
				if (strtotime($arr[$j][$order]) < strtotime($arr[$j + 1][$order]) ) 
				{
					$tmp_arr     = $arr[$j];
					$arr[$j]     = $arr[$j + 1];
					$arr[$j + 1] = $tmp_arr;
				}
			}
		}
		return $arr;
	}

}

?>