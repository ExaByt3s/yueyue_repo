<?php
/*
 * 活动码操作类
 */

class pai_activity_code_class extends POCO_TDG
{
	
	var $cache_key = 'PAI_SET_SCAN_CODE_CACHE_';
	var $code_error_cache_key = 'PAI_SCAN_ERROR_CODE_CACHE________';

    var $special_code = array(269630,409866,262120,412095,624112,604389,434307,600309,610069,876208,289061,623269,848238,298535,878245,648719,257902,480951,485920);
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_activity_code_tbl' );
	}
	
	/*
	 * 生成一个活动码
	 * @param int $event_publish_user_id  活动发布者ID
	 * @param int $event_id  活动ID
	 * @param int $enroll_id  报名ID
	 */
	public function create_code($event_publish_user_id, $event_id, $enroll_id)
	{
		
		define ( "G_DB_GET_REALTIME_DATA", 1 );
		
		$enroll_obj = POCO::singleton ( 'event_enroll_class' );
		
		static $i = 0;
		
		if (empty ( $event_publish_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':活动发布者ID不能为空' );
		}
		
		if (empty ( $event_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':活动ID不能为空' );
		}
		
		if (empty ( $enroll_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':报名ID不能为空' );
		}
		
		$sql = "select code from pai_db.pai_new_temp_code_tbl where is_used=0 limit 1";
		$code = db_simple_getdata ( $sql, true, 101 );
		
		$enroll_info = $enroll_obj->get_enroll_by_enroll_id ( $enroll_id );
		
		$enroll_user_id = ( int ) get_relate_yue_id ( $enroll_info ['user_id'] );
		
		$insert_data ['user_id'] = $event_publish_user_id;
		$insert_data ['event_id'] = $event_id;
		$insert_data ['enroll_id'] = $enroll_id;
		$insert_data ['enroll_user_id'] = $enroll_user_id;
		$insert_data ['code'] = $code ['code'];
		$insert_data ['add_time'] = time();
		
		$pai_config_obj = POCO::singleton ( 'pai_config_class' );

		$waipai_arr = $pai_config_obj->big_waipai_event_id_arr();
		
		//大外拍活动直接扫码
		if(in_array($event_id,$waipai_arr))
		{
			$insert_data ['is_checked'] = 1;
		}
		
		$insert_str = db_arr_to_update_str ( $insert_data );
		
		$sql = "insert ignore pai_db.pai_activity_code_tbl set " . $insert_str;
		db_simple_getdata ( $sql, false, 101 );
		$affected_rows = db_simple_get_affected_rows ();
		
		if ($affected_rows)
		{
			$sql = "update pai_db.pai_new_temp_code_tbl set is_used=1 where code=" . $code ['code'];
			db_simple_getdata ( $sql, false, 101 );
		}
		else
		{
			$i ++;
			if ($i > 20)
			{
				return false;
			}
			$this->create_code ( $event_publish_user_id, $event_id, $enroll_id );
		}
	
	}
	
	/*
	 * 生成多个活动码
	 * @param int $num 生成个数
	 * @param int $event_publish_user_id  活动发布者ID
	 * @param int $event_id  活动ID
	 * @param int $enroll_id  报名ID
	 */
	public function create_multi_code($num = 1, $event_publish_user_id, $event_id, $enroll_id)
	{
		$num = ( int ) $num;
		for($i = 0; $i < $num; $i ++)
		{
			$this->create_code ( $event_publish_user_id, $event_id, $enroll_id );
		}
	}
	
	/*
	 * 生成活动二维码
	 * @param int $event_id  活动ID
	 * @param int $enroll_id  报名ID
	 * 
	 * $return array 
	 */
	public function create_qr_code($event_id, $enroll_id)
	{
		if (empty ( $event_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':活动ID不能为空' );
		}
		
		if (empty ( $enroll_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':报名ID不能为空' );
		}
		
		$code_info = $this->get_code_by_enroll_id_by_status ( $enroll_id, 0 );
		
		foreach ( $code_info as $val )
		{
			$code = $val ['code'];
			
			$hash = qrcode_hash ( $event_id, $enroll_id, $code );
			
			$jump_url = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$event_id}&enroll_id={$enroll_id}&code={$code}&hash={$hash}";
			//$jump_url = urlencode ( $jump_url );
			//$url = "http://qr.liantu.com/api.php?w=300&el=l&text=" . $jump_url;
			//$url_arr [] = $url;
			$url_arr [] = $this->get_qrcode_img($jump_url);
		}
		 
		return $url_arr;
	}
	
	/*
	 * 获取数据
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_code_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	/*
	 * 更新验证码状态
	 * @param int $user_id 用户ID
	 * @param int $code 活动码
	 */
	public function update_code($user_id, $code)
	{
		$user_id = ( int ) $user_id;
		$code = ( int ) $code;
		
		if (empty ( $user_id ))
		{
			return false;
		}
		
		if (empty ( $code ))
		{
			return false;
		}
		
		$enroll_obj = POCO::singleton ( 'event_enroll_class' );
		$event_details_obj = POCO::singleton ( 'event_details_class' );
		$table_obj = POCO::singleton ( 'event_table_class' );
		$weixin_pub_obj = POCO::singleton ( 'pai_weixin_pub_class' );
		
		$where_str = "user_id={$user_id} and code = {$code} and is_end=0";
		
		$update_data ['is_checked'] = 1;
		$update_data ['update_time'] = time ();
		
		$ret = $this->update ( $update_data, $where_str );
		
		if ($ret)
		{
			$code_info = $this->get_code_list ( false, 'code=' . $code );
			
			$enroll_info = $enroll_obj->get_enroll_by_enroll_id ( $code_info [0] ['enroll_id'] );
			
			$model_user_id = $code_info [0] ['user_id'];
			$cameraman_user_id = $code_info [0] ['enroll_user_id'];
			
			$model_nickname = get_user_nickname_by_user_id ( $user_id );
			$cameraman_nickname = get_user_nickname_by_user_id ( $cameraman_user_id );
			
			$msg_obj = POCO::singleton ( 'pai_information_push' );
			
			$send_data ['media_type'] = 'card';
			$send_data ['card_style'] = 2;
			$send_data ['card_text1'] = '已和' . $cameraman_nickname . '签到,可以拍摄啦';
			$send_data ['card_title'] = '确认完成';
			
			$to_send_data ['media_type'] = 'card';
			$to_send_data ['card_style'] = 2;
			$to_send_data ['card_text1'] = '已成功签到,准备开始拍摄';
			$to_send_data ['card_title'] = '查看约拍详情';


            $table_arr = $table_obj->get_event_table_num_array($enroll_info['event_id']);
            $num = $table_arr[$enroll_info['table_id']];

            $event_info = $event_details_obj->get_event_by_event_id ( $enroll_info['event_id'] );
            $content = '你参加的“'.$event_info ['title'].'”活动第'.$num.'场已签到！';
            send_message_for_10002 ( $cameraman_user_id, $content );

			}
			
			return true;

	}
	
	/*
	 * 根据报名ID获取活动码
	 * 
	 * @param int    $enroll_id 报名ID
	 */
	public function get_code_by_enroll_id($enroll_id)
	{
		$enroll_id = ( int ) $enroll_id;
		
		if (empty ( $enroll_id ))
		{
			return false;
		}
		
		$where_str = "enroll_id={$enroll_id}";
		
		$ret = $this->get_code_list ( false, $where_str );
		return $ret;
	}
	
	/*
	 * 根据报名ID和签到状态获取活动码
	 * 
	 * @param int    $enroll_id 报名ID
	 * @param int    $is_checked 是否已签到
	 */
	public function get_code_by_enroll_id_by_status($enroll_id, $is_checked = 0)
	{
		$enroll_id = ( int ) $enroll_id;
		$is_checked = ( int ) $is_checked;
		if (empty ( $enroll_id ))
		{
			return false;
		}
		
		$where_str = "enroll_id={$enroll_id} and is_checked={$is_checked}";
		
		$ret = $this->get_code_list ( false, $where_str );
		return $ret;
	}
	
	
	/*
	 * 新版根据报名ID和签到状态获取活动码
	 * 
	 * @param int    $enroll_id 报名ID
	 * @param int    $is_checked 是否已签到
	 */
	public function get_new_code_by_enroll_id_by_status($enroll_id, $is_checked = 0)
	{
		$enroll_id = ( int ) $enroll_id;
		$is_checked = ( int ) $is_checked;
		if (empty ( $enroll_id ))
		{
			return false;
		}
		
		$where_str = "enroll_id={$enroll_id} and is_checked={$is_checked}";
		
		$ret = $this->get_act_code_view_list ( false, $where_str );
		return $ret;
	}
	
	/*
	 * 验证活动码
	 * @param int $user_id 组织者用户ID
	 * @param int $code 活动码
	 * 
	 * @return int   1为验证成功，-1为验证码无效或已被使用，-2为输入者非活动发布人，-3活动已结束，-4活动码输入错误过多
	 */
	public function verify_code($user_id, $code)
	{
		$user_id = ( int ) $user_id;
		$code = ( int ) $code;

		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		
		if (empty ( $code ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':活动码不能为空' );
		}
		
		$error_times = $this->get_code_error_cache($user_id);
		
		if($error_times>10)
		{
			return -4;
		}
		
		$where_str = "code={$code} and is_checked=0 and is_end=0";
		$ret = $this->get_code_list ( false, $where_str, 'id DESC', '0,1' );
		
		if ($ret)
		{
			$enroll_id = $ret [0] ['enroll_id'];
			$event_id = $ret [0] ['event_id'];
			
			$event_info = get_event_by_event_id ( $event_id );
			
			if ($event_info ['event_status'] != 0)
			{
				return - 3;
			}
			
			if ($event_info ['user_id'] == $user_id)
			{
				$this->update_code ( $user_id, $code );
				//设置已被扫的CACHE
				$this->set_scan_cache ( $code );

				return 1;
			}
			else
			{
				$this->set_code_error_cache($user_id);
				return - 2;
			}
		}
		else
		{
			$this->set_code_error_cache($user_id);
			return - 1;
		}
	}
	
	/*
	 * 根据报名ID统计已签到人数
 	 * @param bool $b_select 是否取总数
 	 * @param string $enroll_id 报名ID
 	 * @param string $limit 分页
 	 * @param bool $sum_mark  true 返回签到总人数   false返回报名记录数
	 */
	public function count_code_is_checked($b_select = false, $enroll_id, $limit = '0,10',$sum_mark=false)
	{
		if (empty ( $enroll_id ))
		{
			$enroll_id = 0;
		}
		
		$sql = "select count(*) as c,enroll_id from pai_db.pai_activity_code_tbl where enroll_id in ({$enroll_id}) and is_checked=1 group by enroll_id order by enroll_id asc ";
		
		if ($b_select == false)
		{
			$sql .= "limit {$limit}";
		}
		
		$ret = db_simple_getdata ( $sql, false, 101 );
		
		if ($b_select)
		{
			if($sum_mark)
			{
				foreach($ret as $val)
				{
					$count += $val['c'];
				}	
			}
			else
			{
				$count = count($ret);
			}
			
			return  $count;
		}
		else
		{
			return $ret;
		}
	
	}
	
	/*
	 * 根据报名ID检查活动码是否有至少一个被扫过
 	 * @param int $enroll_id 报名ID
 	 * @return bool
	 */
	public function check_code_scan($enroll_id)
	{
		$enroll_id = ( int ) $enroll_id;
		if (empty ( $enroll_id ))
		{
			return false;
		}
		
		$where_str = "enroll_id={$enroll_id} and is_checked=1";
		$ret = $this->get_code_list ( true, $where_str );
		
		if ($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	/*
	 * 根据报名ID获取已签到的签到时间
	 */
	public function get_scan_update_time($enroll_id)
	{
		$enroll_id = ( int ) $enroll_id;
		if (empty ( $enroll_id ))
		{
			return false;
		}
		
		$where_str = "enroll_id={$enroll_id} and is_checked=1";
		
		$ret = $this->find ( $where_str );
		return ( int ) $ret ['update_time'];
	
	}
	
	/*
	 * 根据报名ID检查是否已全部签到
 	 * @param int $enroll_id 报名ID
 	 * @return bool
	 */
	public function check_is_all_scan($enroll_id)
	{
		$enroll_id = ( int ) $enroll_id;
		if (empty ( $enroll_id ))
		{
			return false;
		}
		
		$where_str = "enroll_id={$enroll_id} and is_checked=0";
		$ret = $this->get_code_list ( true, $where_str );
		
		if ($ret)
		{
			return false;
		}
		else
		{
			return true;
		}
	
	}
	
	/*
	 * 根据enroll id获取最后签到的记录
	 */
	public function get_last_scan_by_enroll_id($enroll_id)
	{
		$ret = $this->get_code_list ( false, "enroll_id={$enroll_id}", 'id DESC', '0,1' );
		return $ret [0];
	}
	
	/*
	 * 根据活动ID检查活动码是否有至少一个被扫过
 	 * @param int $event_id 活动ID
 	 * @return bool
	 */
	public function check_event_code_scan($event_id)
	{
		$event_id = ( int ) $event_id;
		if (empty ( $event_id ))
		{
			return false;
		}
		
		$where_str = "event_id={$event_id} and is_checked=1";
		$ret = $this->get_code_list ( true, $where_str );
		
		if ($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * 删除活动码
	 * @param int $enroll_id 报名ID
	 * @return int
	 */
	public function delete_code($enroll_id)
	{
		$enroll_id = ( int ) $enroll_id;
		if (empty ( $enroll_id ))
		{
			return false;
		}
		
		$where_str = "enroll_id = {$enroll_id}";
		
		//备份一下
		$time = time ();
		$code_list = $this->get_code_list ( false, $where_str );
		
		foreach ( $code_list as $val )
		{
			$code = $val ['code'];
			$is_checked = $val ['is_checked'];
			$sql = "insert into pai_db.pai_activity_code_del_tbl set enroll_id={$enroll_id},code={$code},is_checked={$is_checked},add_time={$time}";
			db_simple_getdata ( $sql, false, 101 );
		}
		
		return $this->delete ( $where_str );
	}
	
	/*
	 * 设置签到CACHE
	 */
	public function set_scan_cache($code)
	{
		$cache_time = 3600;
		$cache_key = $this->cache_key . $code;
		POCO::setCache ( $cache_key, $code, array ('life_time' => $cache_time ) );
	}
	
	/*
	 * 获取签到CACHE
	 */
	public function get_scan_cache($code)
	{
		$cache_key = $this->cache_key . $code;
		$ret = POCO::getCache ( $cache_key );
		return ( int ) $ret;
	}
	
	/*
	 * 设置输入错误CACHE
	 */
	public function set_code_error_cache($user_id)
	{
		$cache_time = 1800;
		$cache_key = $this->code_error_cache_key . $user_id;
		$times = $this->get_code_error_cache($user_id);
		$set_times = $times+1;
		POCO::setCache ( $cache_key, $set_times, array ('life_time' => $cache_time ) );
	}
	
	/*
	 * 获取输入错误CACHE
	 */
	public function get_code_error_cache($user_id)
	{
		$cache_key = $this->code_error_cache_key . $user_id;
		$ret = POCO::getCache ( $cache_key );
		return ( int ) $ret;
	}
	
	/*
	 * 根据活动ID，用户ID判断是否扫码
	 */
	public function check_user_event_code_scan($event_id,$user_id)
	{
		$event_id = ( int ) $event_id;
		$user_id = ( int ) $user_id;
		
		$where_str = "event_id={$event_id} and enroll_user_id={$user_id} and is_checked=1";
		$ret = $this->find ( $where_str );
		if($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	} 
	
	public static function get_qrcode_img($url)
	{
		/*$gmclient= new GearmanClient();
	    $gmclient->addServers("172.18.5.216:9870");
	    do
	    {
	        $req_param['string_encode']=$url;
	        
	        $result= $gmclient->do("qrencode_string",json_encode($req_param) );
	    }
	    while($gmclient->returnCode() != GEARMAN_SUCCESS);
	    $ret = json_decode($result,true);
	    return $ret['result'];*/

		$greaman_obj = POCO::singleton('pai_gearman_base_class');
		$greaman_obj->connect('172.18.5.216', '9870');

		$req_param['string_encode'] = $url;
		$result = $greaman_obj->_do('qrencode_string', $req_param);
		return $result['result'];
	}
	
	public function get_code_info($code)
	{
		return $this->find("code={$code}");
	}
	
	public function get_available_code_info($code)
	{
		return $this->find("code={$code} and is_end=0");
	}
	
	/*
	 * 验证活动码
	 * @param int $user_id 组织者用户ID
	 * @param int $code 活动码
	 * 
	 * @return array
	 */
	public function event_verify_code($user_id, $code)
	{
		$user_id = ( int ) $user_id;
		$code = ( int ) $code;

		
		if (empty ( $user_id ) || empty ( $code ))
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		
		$error_times = $this->get_code_error_cache($user_id);
		
		if($error_times>10)
		{
			$result['result'] = -4;
			$result['message'] = '活动码错误次数过多，请稍后再试！';
			return $result;
		}
		
		$where_str = "code={$code} and is_end=0";
		$ret_arr = $this->get_code_list ( false, $where_str, 'id DESC', '0,1' );
		$ret = $ret_arr[0];
		
		if ($ret)
		{
		
			if ($ret ['user_id'] == $user_id)
			{
				if($ret['is_checked']==1)
				{
					$result['result'] = -4;
					$result['message'] = '你已签到过了';
					return $result;
				}
				
				$enroll_id = $ret ['enroll_id'];
				$event_id = $ret ['event_id'];
				$event_info = get_event_by_event_id ( $event_id );
					
				if ($event_info ['event_status'] != 0)
				{
					$result['result'] = -4;
					$result['message'] = '活动已结束';
					return $result;
				}
				
				$this->update_code ( $user_id, $code );

				$result['result'] = 1;
				$result['message'] = '验证成功';
				return $result;
			}
			else
			{
				$this->set_code_error_cache($user_id);
				
				$result['result'] = -4;
				$result['message'] = '你不是活动发布人';
				return $result;
			}
		}
		else
		{
			$this->set_code_error_cache($user_id);
			$result['result'] = -4;
			$result['message'] = '验证码无效';
			return $result;
		}
	}
	
	/*
	 * 验证二维码链接
	 */
	public function verify_qrcode_url($user_id,$url='',$version_type='')
	{
		$code_obj = POCO::singleton('pai_activity_code_class');
		$mall_order_obj = POCO::singleton('pai_mall_order_class');
		
	
		$user_id = (int)$user_id;
		if(empty($user_id) || empty($url))
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		$url = html_entity_decode($url);
		$url_array = parse_url($url);
		
		$link_arr = explode("&",$url_array['query']);
		
		foreach($link_arr as $link_val)
		{
			$param = explode("=",$link_val);
			$param_arr[$param[0]] = $param[1];
		}
		
		$code = (int)$param_arr['code'];
		$event_id = (int)$param_arr['event_id'];
		$enroll_id = (int)$param_arr['enroll_id'];
		$hash = $param_arr['hash'];
		
		$log_arr['param_arr'] = $param_arr;
		$log_arr['user_id'] = $user_id;
		pai_log_class::add_log($log_arr, 'code', 'code');
		
		$qrcode_hash = qrcode_hash($event_id,$enroll_id,$code);
		
		
		if($qrcode_hash != $hash){
			$result['result'] = -2;
			$result['message'] = 'HASH校验错误';
			return $result;
		}
		
		if(preg_match("/^2|^4|^6|^8/",$code) &&  !in_array($code, $this->special_code,true))
		{

			if($version_type=='merchant')
			{
				$result['result'] = -2;
				$result['message'] = '商家版不能扫活动二维码';
				return $result;
			}
			
			$ret = $code_obj->event_verify_code($user_id,$code);
			
			
			if($ret['result']==1){
				$result['result'] = 1;
				$result['message'] = '验证成功';
				$result['type'] = "event";
				$result['event_id'] = $event_id;
				
				return $result;
				
			}else{
				$result['result'] = $ret['result'];
				$result['message'] = $ret['message'];
				$result['type'] = "event";

				return $result;
			}
		}else
		{
			if($version_type=='customer')
			{
				$result['result'] = -2;
				$result['message'] = '消费者版不能扫商城二维码';
				return $result;
			}
			
			$ret = $mall_order_obj->sign_order($code, $user_id);
			
			if($ret['result']==1)
			{
				$result['result'] = 1;
				$result['message'] = '验证成功';
				$result['type'] = "mall";
				$result['order_type'] = $ret['order_type'];
				$result['order_sn'] = $ret['order_sn'];
				$result['activity_id'] = $ret['activity_id'];
				$result['stage_id'] = $ret['stage_id'];
			}
			else
			{
				$result['result'] = -4;
				$result['message'] = $ret['message'];
			}
			
			return $result;
		}
		
	}
	
	
	/*
	 * 商城验证码验证(合并活动和商城验证)
	 */
	public function verify_mall_code($user_id,$code)
	{
		$user_id = ( int ) $user_id;
		$code = ( int ) $code;
		
		$mall_order_obj = POCO::singleton('pai_mall_order_class');

        $log_arr['code'] = $code;
        $log_arr['user_id'] = $user_id;
		
		if (empty ( $user_id ) || empty ( $code ))
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		$error_times = $this->get_code_error_cache($user_id);
		
		if($error_times>10)
		{
			$result['result'] = -4;
			$result['message'] = '验证码错误次数过多，请稍后再试！';
			return $result;
		} 
		
		if(preg_match("/^2|^4|^6|^8/",$code) &&  !in_array($code, $this->special_code,true))
		{
			
			$ret = $this->event_verify_code($user_id,$code);
			
			if($ret['result']==1){
				$result['result'] = 1;
				$result['message'] = '验证成功';
				$result['type'] = "event";
				
				return $result;
				
			}else{
				$result['result'] = $ret['result'];
				$result['message'] = $ret['message'];
				$result['type'] = "event";

				return $result;
			}
		}else
		{
			$ret = $mall_order_obj->sign_order($code, $user_id);

            $log_arr['result'] = $ret;

            pai_log_class::add_log($log_arr, 'scan_code', 'scan_code');

			if($ret['result']==1)
			{
				$result['result'] = 1;
				$result['message'] = '验证成功';
				$result['type'] = "mall";
				//$result['order_id'] = $ret['order_id'];
				$result['order_sn'] = $ret['order_sn'];
			}
			else
			{
				$result['result'] = -4;
				$result['message'] = $ret['message'];
				if($ret['is_limit_error']==1)
				{
					$this->set_code_error_cache($user_id);
				}
			}
			
			return $result;
		}
	}
	
	
	public function get_act_code_view_list($b_select_count = false, $where_str = '', $order_by = 'add_time DESC', $limit = '', $fields = '*')
	{
		$this->setTableName ( 'pai_activity_code_view' );
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	/*
	 * 新版商城活动二维码列表
	 */
	public function get_new_act_ticket($user_id,$limit='0,10')
	{
        $user_id = (int)$user_id;
        $where_str = "enroll_user_id={$user_id} and is_checked=0 group by event_id,enroll_id";
        $code_arr = $this->get_act_code_view_list(false, $where_str, 'add_time DESC', $limit, "enroll_user_id,event_id,enroll_id,type");

        $mall_order_obj = POCO::singleton ( 'pai_mall_order_class' );

        //一次查询减少请求
        $new_code_arr=curl_event_data("event_api_class", "count_avaliable_act_ticket", array($code_arr));
        foreach($new_code_arr as $k=>$val)
        {
            $code = $this->get_new_code_by_enroll_id_by_status($val['enroll_id'], 0);
            $new_code_arr[$k]['code_arr'] = $code;
            $new_code_arr[$k]['nick_name']	  = get_user_nickname_by_user_id($val['enroll_user_id']);
        }
//print_r($new_code_arr);
        foreach($code_arr as $k=>$val)
        {
            if($val['type']!='activity_code')
            {
                $is_wait = $mall_order_obj->is_wait_sign_order($val['event_id']);

                if($is_wait)
                {
                    $order_info = $mall_order_obj->get_order_full_info_by_id($val['event_id']);

                    //获取未签到活动券
                    $code = $this->get_new_code_by_enroll_id_by_status($val['enroll_id'], 0);
                    $new_code_arr[$k]['code_arr'] = $code;
                    $new_code_arr[$k]['user_id']		  = $val['enroll_user_id'];
                    $new_code_arr[$k]['nick_name']	  = get_user_nickname_by_user_id($val['enroll_user_id']);
                    $new_code_arr[$k]['type'] = "mall_code";
                    if($order_info['type_id']==31)
                    {
                        $new_code_arr[$k]['title'] = "[" . $order_info['type_name'] . "]" . $order_info['detail_list'][0]['goods_name']." 模特:".$order_info['seller_name'];
                        $new_code_arr[$k]['start_time'] = date("Y-m-d",$order_info['detail_list'][0]['service_time']);
                    }
                    elseif($order_info['type_id']==42)
                    {
                        $goods_info = POCO::singleton('pai_mall_goods_class')->get_goods_info_by_goods_id($order_info['activity_list'][0]['activity_id']);
                        $new_code_arr[$k]['title'] = "[" . $order_info['type_name'] . "]" .$goods_info['data']['goods_data']['titles']." ".$order_info['activity_list'][0]['stage_title'];
                        $new_code_arr[$k]['start_time'] = date("Y-m-d",$order_info['activity_list'][0]['service_start_time']);
                    }
                    else {
                        $new_code_arr[$k]['title'] = "[" . $order_info['type_name'] . "]" . $order_info['detail_list'][0]['goods_name'];
                        $new_code_arr[$k]['start_time'] = date("Y-m-d",$order_info['detail_list'][0]['service_time']);
                    }
                }
            }
        }

        ksort($new_code_arr);
        return $new_code_arr;
	}
	
	/*
	 * 获取商城订单二维码详细数据
	 */
	public function get_new_act_ticket_detail($order_id)
	{
		$code_arr = $this->get_new_code_by_enroll_id_by_status($order_id,0);
		foreach($code_arr as $k=>$val)
		{
			$event_id = $val['event_id'];
			$enroll_id = $val['enroll_id'];
			$code = $val['code'];
			$hash = qrcode_hash ( $event_id, $enroll_id, $code );
			$jump_url = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$event_id}&enroll_id={$enroll_id}&code={$code}&hash={$hash}";
		
			$new_code_arr[$k]['qr_code_url'] = $jump_url;
			$new_code_arr[$k]['code'] = $code;
			$new_code_arr[$k]['name'] = "名称";
			
		}
		
		return $new_code_arr;
	}

}

?>