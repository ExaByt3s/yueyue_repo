<?php
/*
 * 服务器回复log操作类
 * xiao xiao
 */

class pai_response_log_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 22 );
		$this->setDBName ( 'yueyue_log_tmp_db' );
		$this->setTableName ( 'sendserver_response_log_201503' );
	}


	/**
	 * [获取阅读的数据]
	 * @param  [time] $time [现在时间,时间戳]
	 * @return [boolean]       [false|true]
	 */
	public function get_response_by_time_v2($time = '')
	{
		if (empty($time)) 
		{
			$time = strtotime(date('Y-m-d', time()))-24*3600;
		}
		//选择数据表
		$this->selectTable($time);
		$end_time = $time + 24*3600;
		//echo $time;
		$where_str = "add_time BETWEEN {$time} AND {$end_time} AND type='open'";
		echo $where_str;
	    $count = $this->findCount($where_str);
	    $ret = $this->findAll($where_str,"0,{$count}", 'id DESC', '*');
	    //print_r($ret);exit;
	    if (!is_array($ret) || !$ret) {return false;}
	    $data  = array();
	    foreach ($ret as $key => $vo) 
	    {
	    	if ($key == 0) 
			{
				$data[$vo['uid']]['user_id']        = $vo['uid'];
				$data[$vo['uid']]['open_count']     = 1;
				$data[$vo['uid']]['person_count']   = (int)$vo['vid'] > 100000 ? 1 : 0;
				$data[$vo['uid']]['system_count']   = (int)$vo['vid'] > 100000 ? 0 : 1;
			}
			else
			{
				if (!isset($data[$vo['uid']])) 
				{
					$data[$vo['uid']]['user_id']        = $vo['uid'];
					$data[$vo['uid']]['open_count']     = 1;
					$data[$vo['uid']]['person_count']   = (int)$vo['vid'] > 100000 ? 1 : 0;
					$data[$vo['uid']]['system_count']   = (int)$vo['vid'] > 100000 ? 0 : 1;
				}
				else
				{
					$data[$vo['uid']]['open_count']     += 1;
					$data[$vo['uid']]['person_count']   += (int)$vo['vid'] > 100000 ? 1 : 0;
					$data[$vo['uid']]['system_count']   += (int)$vo['vid'] > 100000 ? 0 : 1;
				}
	        }
	    }
	    if(!$data || !is_array($data)){return false;}
	    //print_r($data);exit;
	    $response_open_day_obj = POCO::singleton('pai_response_open_day_class');
	    //选择数据库
	    $response_open_day_obj->selectTable($time);
	    //echo $time;exit;
	    $add_time = date('Y-m-d', $time);
	    //print_r($data);exit;
	    foreach ($data as $key_val => $val) 
	    {
			$user_id            = $val['user_id'];
			$data_insert['open_count'] = $val['open_count'];
			$data_insert['person_count'] = $val['person_count'];
			$data_insert['system_count'] = $val['system_count'];
			$data_insert['user_id']      = $user_id;
		    $data_insert['add_time']      = $add_time;
			//数据是否存在[存在修改]
	    	/*if ($response_open_day_obj->find_id_by_user_id($user_id, $add_time)) 
	    	{
	    		//echo "oki";
	    		$response_open_day_obj->update_info($user_id,$add_time, $data_insert);
	    	}
	    	//不存在插入
	    	else
	    	{
	    		//echo "bno";
				$data_insert['user_id']  = $user_id;
				$data_insert['add_time'] = $add_time;
	    	    $response_open_day_obj->insert_info($data_insert);
	    	}*/
	    	//var_dump($data_insert);
	    	$response_open_day_obj->insert_info($data_insert);
	    	//unset($data);
	    }
	    echo true;
	    //print_r($data);exit;
	}

	
	/**
	 * [获取阅读的数据]
	 * @param  [time] $time [现在时间,时间戳]
	 * @return [boolean]       [false|true]
	 */
	public function get_response_by_time($time = '')
	{
		if (empty($time)) 
		{
			$time = strtotime(date('Y-m-d', time()));
		}
		$end_time = $time + 24*3600;
		$where_str = "add_time BETWEEN {$time} AND {$end_time} AND type='open' GROUP BY uid";
		/*echo $where_str;
	    $count = $this->findCount($where_str);*/
	    $ret = $this->findAll($where_str,"0,10000", '', 'uid,count(id) AS c');
	    if (!is_array($ret) || !$ret) {return false;}
	    $response_open_day_obj = POCO::singleton('pai_response_open_day_class');
	    $data = array();
	    $response_open_day_obj->selectTable($time);
	    $add_time = date('Y-m-d', $time);
	    foreach ($ret as $key => $vo) 
	    {
			$user_id            = $vo['uid'];
			$data['open_count'] = $vo['c'];
			//数据是否存在[存在修改]
	    	if ($response_open_day_obj->get_open_count_by_user_add_time($user_id, $add_time)) 
	    	{
	    		$response_open_day_obj->update_info($user_id,$add_time, $data);
	    	}
	    	//不存在插入
	    	else
	    	{
				$data['user_id']  = $user_id;
				$data['add_time'] = $add_time;
	    	    $response_open_day_obj->insert_info($data);
	    	}
	    	//unset($data);
	    }
	    return true;
	}
	/**
	 * [获取三天前回复的数据]
	 * @param  [time] $time [现在时间,时间戳]
	 * @return [boolean]       [false|true]
	 */
	public function get_response_reply_by_before_time($time = '')
	{
		$response_open_obj = POCO::singleton('pai_response_open_day_class');
		if (empty($time)) 
		{
			$time = strtotime(date('Y-m-d', time()))-24*3600;
		}
		$this->selectTable($time);
		//$before_start_time = $time - 24*3*3600;
		$before_start_time = $time;
		$before_end_time   = $before_start_time + 24*3600;
		//var_dump($before_end_time);
		$where_str = "add_time BETWEEN {$before_start_time} AND {$before_end_time} AND type = 'reply' AND vid > 100000";
		//echo $where_str;
		$count = $this->findCount($where_str);
		//echo $where_str;exit;
		$ret = $this->findAll($where_str, "0,{$count}", "now_time DESC", "*");
		//print_r($ret);exit;
		if(!$ret || !is_array($ret)){return false;}
		$data = array();
		foreach ($ret as $key => $vo) 
		{
			if ($key == 0) 
			{
				$data[$vo['uid']]['user_id'] = $vo['uid'];
				$arr = $this->get_time_count($vo['interval_time']);
				$data[$vo['uid']]['5i']          = $arr['5i'];
				$data[$vo['uid']]['10i']         = $arr['10i'];
				$data[$vo['uid']]['20i']         = $arr['20i'];
				$data[$vo['uid']]['30i']         = $arr['30i'];
				$data[$vo['uid']]['1h']          = $arr['1h'];
				$data[$vo['uid']]['12h']         = $arr['12h'];
				$data[$vo['uid']]['24h']         = $arr['24h'];
				$data[$vo['uid']]['no_response'] = $arr['no_response'];
				/*$person_count = $response_open_obj->get_open_count_by_user_add_time($vo['uid'], date('Y-m-d', $before_start_time));
				$data[$vo['uid']]['person_count'] = $person_count;*/
				unset($arr);
			}
			else
			{
				if (!isset($data[$vo['uid']])) 
				{
					$data[$vo['uid']]['user_id'] = $vo['uid'];
					$arr = $this->get_time_count($vo['interval_time']);
				    $data[$vo['uid']]['5i']           = $arr['5i'];
				    $data[$vo['uid']]['10i']          = $arr['10i'];
				    $data[$vo['uid']]['20i']          = $arr['20i'];
				    $data[$vo['uid']]['30i']          = $arr['30i'];
				    $data[$vo['uid']]['1h']           = $arr['1h'];
				    $data[$vo['uid']]['12h']          = $arr['12h'];
				    $data[$vo['uid']]['24h']          = $arr['24h'];
				    $data[$vo['uid']]['no_response']  = $arr['no_response'];
				    /*$person_count = $response_open_obj->get_open_count_by_user_add_time($vo['uid'], date('Y-m-d', $before_start_time));
				    $data[$vo['uid']]['person_count'] = $person_count;*/
				}
				else
				{
					$arr = $this->get_time_count($vo['interval_time']);
				    $data[$vo['uid']]['5i']          += $arr['5i'];
				    $data[$vo['uid']]['10i']         += $arr['10i'];
				    $data[$vo['uid']]['20i']         += $arr['20i'];
				    $data[$vo['uid']]['30i']         += $arr['30i'];
				    $data[$vo['uid']]['1h']          += $arr['1h'];
				    $data[$vo['uid']]['12h']         += $arr['12h'];
				    $data[$vo['uid']]['24h']         += $arr['24h'];
				    $data[$vo['uid']]['no_response'] += $arr['no_response'];
				}
				unset($arr);
			}
			//print_r($data);
		}
		//print_r($data);
		if(!$data || !is_array($data)){return false;}
		$add_time = date('Y-m-d', $before_start_time);
		//echo $add_time;exit;
		//$response_open_day_obj = POCO::singleton('pai_response_open_day_class');
		$response_reply_day_day_obj = POCO::singleton('pai_response_reply_day_class');
		//$response_open_day_obj->selectTable($before_start_time);
		$response_reply_day_day_obj->selectTable($before_start_time);
		foreach ($data as $mykey => $val) 
		{
			//print_r($val);
			/*$open_count = $response_open_day_obj->get_open_count_by_user_add_time($val['user_id'], $add_time);
			echo $open_count;
			echo "<br/>";*/
			//操作24未返回的值
			//$data_insert['no_response'] = $open_count - $val['5i'] - $val['10i'] - $val['20i'] - $val['30i'] - $val['1h'] - $val['12h'] - $val['24h'];
			$data_insert['no_response'] = $val['no_response'];
			$data_insert['5i']       = $val['5i'];
			$data_insert['10i']      = $val['10i'];
			$data_insert['20i']      = $val['20i'];
			$data_insert['30i']      = $val['30i'];
			$data_insert['1h']       = $val['1h'];
			$data_insert['12h']      = $val['12h'];
			$data_insert['24h']      = $val['24h'];
			$data_insert['user_id']  = $val['user_id'];
			$data_insert['add_time'] = $add_time;
			//print_r($data_insert);exit;
			$info = $response_reply_day_day_obj->insert_info($data_insert);
			/*if ($response_reply_day_day_obj->find_id_by_user_add_time($val['user_id'], $add_time)) 
			{
				$info = $response_reply_day_day_obj->update_info($val['user_id'], $add_time,$data_insert);
			}
			else
			{
				$data_insert['user_id']  = $val['user_id'];
				$data_insert['add_time'] = $add_time;
				$info = $response_reply_day_day_obj->insert_info($data_insert);
			}*/
		}
		return true;
	}

	/**
	 * 获取各时间段的个数
	 * @param  [int] $interval_time  [时间间隔]
	 * @return [array]               [$data]
	 */
	public function get_time_count($interval_time = 0)
	{
		$data = array
		(
			'5i'          => 0, 
			'10i'         => 0, 
			'20i'         => 0, 
			'30i'         => 0, 
			'1h'          => 0, 
			'12h'         => 0, 
			'24h'         => 0,
			'no_response' => 0 
		);
		if ($interval_time >= 0 && $interval_time <= 5 * 60) 
		{
			$data['5i'] = 1;
		}
		//10i
		elseif ($interval_time > 5 * 60 && $interval_time <= 10 *60) 
		{
			$data['10i'] = 1;
		}
		//20i
		elseif ($interval_time > 10 * 60 && $interval_time <= 20 *60) 
		{
			$data['20i'] = 1;
		}
		//30i
		elseif ($interval_time > 20 * 60 && $interval_time <= 30 *60) 
		{
			$data['30i'] = 1;
		}
		//1h
		elseif ($interval_time > 30 * 60 && $interval_time <= 60 *60) 
		{
			$data['1h'] = 1;
		}
		//12h
		elseif ($interval_time > 60 * 60 && $interval_time <= 12 * 60 *60) 
		{
			$data['12h'] = 1;
		}
		//24h
		elseif ($interval_time > 12 * 60 *60 && $interval_time <= 24 * 60 *60) 
		{
			$data['24h'] = 1;
		}
		//no_response
		elseif ($interval_time > 24 * 60 *60) 
		{
			$data['no_response'] = 1;
		}
		return $data;
	}

	/**
	 * 
	 * @param  string $time [时间]
	 * @return [void]       [没有返回数据]
	 */
	public function selectTable($time = '')
	{
		if (empty($time)) 
		{
			$time = time();
		}
		$time = date('Ym', $time);
		$tablename = "sendserver_response_log_{$time}";
		//echo $tablename;
		$res = $this->query("SHOW TABLES FROM yueyue_log_tmp_db LIKE '{$tablename}'");
		if (empty($res) || !is_array($res)) 
		{
			//return false;
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数据表不存在' );
			
		}
		$this->setTableName ( $tablename );
	}


}

?>