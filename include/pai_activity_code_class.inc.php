<?php
/*
 * ��������
 */

class pai_activity_code_class extends POCO_TDG
{
	
	var $cache_key = 'PAI_SET_SCAN_CODE_CACHE_';
	var $code_error_cache_key = 'PAI_SCAN_ERROR_CODE_CACHE________';

    var $special_code = array(269630,409866,262120,412095,624112,604389,434307,600309,610069,876208,289061,623269,848238,298535,878245,648719,257902,480951,485920);
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_activity_code_tbl' );
	}
	
	/*
	 * ����һ�����
	 * @param int $event_publish_user_id  �������ID
	 * @param int $event_id  �ID
	 * @param int $enroll_id  ����ID
	 */
	public function create_code($event_publish_user_id, $event_id, $enroll_id)
	{
		
		define ( "G_DB_GET_REALTIME_DATA", 1 );
		
		$enroll_obj = POCO::singleton ( 'event_enroll_class' );
		
		static $i = 0;
		
		if (empty ( $event_publish_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�������ID����Ϊ��' );
		}
		
		if (empty ( $event_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ID����Ϊ��' );
		}
		
		if (empty ( $enroll_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
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
		
		//�����Ļֱ��ɨ��
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
	 * ���ɶ�����
	 * @param int $num ���ɸ���
	 * @param int $event_publish_user_id  �������ID
	 * @param int $event_id  �ID
	 * @param int $enroll_id  ����ID
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
	 * ���ɻ��ά��
	 * @param int $event_id  �ID
	 * @param int $enroll_id  ����ID
	 * 
	 * $return array 
	 */
	public function create_qr_code($event_id, $enroll_id)
	{
		if (empty ( $event_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ID����Ϊ��' );
		}
		
		if (empty ( $enroll_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
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
	 * ��ȡ����
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
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
	 * ������֤��״̬
	 * @param int $user_id �û�ID
	 * @param int $code ���
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
			$send_data ['card_text1'] = '�Ѻ�' . $cameraman_nickname . 'ǩ��,����������';
			$send_data ['card_title'] = 'ȷ�����';
			
			$to_send_data ['media_type'] = 'card';
			$to_send_data ['card_style'] = 2;
			$to_send_data ['card_text1'] = '�ѳɹ�ǩ��,׼����ʼ����';
			$to_send_data ['card_title'] = '�鿴Լ������';


            $table_arr = $table_obj->get_event_table_num_array($enroll_info['event_id']);
            $num = $table_arr[$enroll_info['table_id']];

            $event_info = $event_details_obj->get_event_by_event_id ( $enroll_info['event_id'] );
            $content = '��μӵġ�'.$event_info ['title'].'�����'.$num.'����ǩ����';
            send_message_for_10002 ( $cameraman_user_id, $content );

			}
			
			return true;

	}
	
	/*
	 * ���ݱ���ID��ȡ���
	 * 
	 * @param int    $enroll_id ����ID
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
	 * ���ݱ���ID��ǩ��״̬��ȡ���
	 * 
	 * @param int    $enroll_id ����ID
	 * @param int    $is_checked �Ƿ���ǩ��
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
	 * �°���ݱ���ID��ǩ��״̬��ȡ���
	 * 
	 * @param int    $enroll_id ����ID
	 * @param int    $is_checked �Ƿ���ǩ��
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
	 * ��֤���
	 * @param int $user_id ��֯���û�ID
	 * @param int $code ���
	 * 
	 * @return int   1Ϊ��֤�ɹ���-1Ϊ��֤����Ч���ѱ�ʹ�ã�-2Ϊ�����߷ǻ�����ˣ�-3��ѽ�����-4�������������
	 */
	public function verify_code($user_id, $code)
	{
		$user_id = ( int ) $user_id;
		$code = ( int ) $code;

		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		if (empty ( $code ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��벻��Ϊ��' );
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
				//�����ѱ�ɨ��CACHE
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
	 * ���ݱ���IDͳ����ǩ������
 	 * @param bool $b_select �Ƿ�ȡ����
 	 * @param string $enroll_id ����ID
 	 * @param string $limit ��ҳ
 	 * @param bool $sum_mark  true ����ǩ��������   false���ر�����¼��
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
	 * ���ݱ���ID������Ƿ�������һ����ɨ��
 	 * @param int $enroll_id ����ID
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
	 * ���ݱ���ID��ȡ��ǩ����ǩ��ʱ��
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
	 * ���ݱ���ID����Ƿ���ȫ��ǩ��
 	 * @param int $enroll_id ����ID
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
	 * ����enroll id��ȡ���ǩ���ļ�¼
	 */
	public function get_last_scan_by_enroll_id($enroll_id)
	{
		$ret = $this->get_code_list ( false, "enroll_id={$enroll_id}", 'id DESC', '0,1' );
		return $ret [0];
	}
	
	/*
	 * ���ݻID������Ƿ�������һ����ɨ��
 	 * @param int $event_id �ID
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
	 * ɾ�����
	 * @param int $enroll_id ����ID
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
		
		//����һ��
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
	 * ����ǩ��CACHE
	 */
	public function set_scan_cache($code)
	{
		$cache_time = 3600;
		$cache_key = $this->cache_key . $code;
		POCO::setCache ( $cache_key, $code, array ('life_time' => $cache_time ) );
	}
	
	/*
	 * ��ȡǩ��CACHE
	 */
	public function get_scan_cache($code)
	{
		$cache_key = $this->cache_key . $code;
		$ret = POCO::getCache ( $cache_key );
		return ( int ) $ret;
	}
	
	/*
	 * �����������CACHE
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
	 * ��ȡ�������CACHE
	 */
	public function get_code_error_cache($user_id)
	{
		$cache_key = $this->code_error_cache_key . $user_id;
		$ret = POCO::getCache ( $cache_key );
		return ( int ) $ret;
	}
	
	/*
	 * ���ݻID���û�ID�ж��Ƿ�ɨ��
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
	 * ��֤���
	 * @param int $user_id ��֯���û�ID
	 * @param int $code ���
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
			$result['message'] = '��������';
			return $result;
		}
		
		
		$error_times = $this->get_code_error_cache($user_id);
		
		if($error_times>10)
		{
			$result['result'] = -4;
			$result['message'] = '������������࣬���Ժ����ԣ�';
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
					$result['message'] = '����ǩ������';
					return $result;
				}
				
				$enroll_id = $ret ['enroll_id'];
				$event_id = $ret ['event_id'];
				$event_info = get_event_by_event_id ( $event_id );
					
				if ($event_info ['event_status'] != 0)
				{
					$result['result'] = -4;
					$result['message'] = '��ѽ���';
					return $result;
				}
				
				$this->update_code ( $user_id, $code );

				$result['result'] = 1;
				$result['message'] = '��֤�ɹ�';
				return $result;
			}
			else
			{
				$this->set_code_error_cache($user_id);
				
				$result['result'] = -4;
				$result['message'] = '�㲻�ǻ������';
				return $result;
			}
		}
		else
		{
			$this->set_code_error_cache($user_id);
			$result['result'] = -4;
			$result['message'] = '��֤����Ч';
			return $result;
		}
	}
	
	/*
	 * ��֤��ά������
	 */
	public function verify_qrcode_url($user_id,$url='',$version_type='')
	{
		$code_obj = POCO::singleton('pai_activity_code_class');
		$mall_order_obj = POCO::singleton('pai_mall_order_class');
		
	
		$user_id = (int)$user_id;
		if(empty($user_id) || empty($url))
		{
			$result['result'] = -1;
			$result['message'] = '��������';
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
			$result['message'] = 'HASHУ�����';
			return $result;
		}
		
		if(preg_match("/^2|^4|^6|^8/",$code) &&  !in_array($code, $this->special_code,true))
		{

			if($version_type=='merchant')
			{
				$result['result'] = -2;
				$result['message'] = '�̼Ұ治��ɨ���ά��';
				return $result;
			}
			
			$ret = $code_obj->event_verify_code($user_id,$code);
			
			
			if($ret['result']==1){
				$result['result'] = 1;
				$result['message'] = '��֤�ɹ�';
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
				$result['message'] = '�����߰治��ɨ�̳Ƕ�ά��';
				return $result;
			}
			
			$ret = $mall_order_obj->sign_order($code, $user_id);
			
			if($ret['result']==1)
			{
				$result['result'] = 1;
				$result['message'] = '��֤�ɹ�';
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
	 * �̳���֤����֤(�ϲ�����̳���֤)
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
			$result['message'] = '��������';
			return $result;
		}
		
		$error_times = $this->get_code_error_cache($user_id);
		
		if($error_times>10)
		{
			$result['result'] = -4;
			$result['message'] = '��֤�����������࣬���Ժ����ԣ�';
			return $result;
		} 
		
		if(preg_match("/^2|^4|^6|^8/",$code) &&  !in_array($code, $this->special_code,true))
		{
			
			$ret = $this->event_verify_code($user_id,$code);
			
			if($ret['result']==1){
				$result['result'] = 1;
				$result['message'] = '��֤�ɹ�';
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
				$result['message'] = '��֤�ɹ�';
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
	 * �°��̳ǻ��ά���б�
	 */
	public function get_new_act_ticket($user_id,$limit='0,10')
	{
        $user_id = (int)$user_id;
        $where_str = "enroll_user_id={$user_id} and is_checked=0 group by event_id,enroll_id";
        $code_arr = $this->get_act_code_view_list(false, $where_str, 'add_time DESC', $limit, "enroll_user_id,event_id,enroll_id,type");

        $mall_order_obj = POCO::singleton ( 'pai_mall_order_class' );

        //һ�β�ѯ��������
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

                    //��ȡδǩ���ȯ
                    $code = $this->get_new_code_by_enroll_id_by_status($val['enroll_id'], 0);
                    $new_code_arr[$k]['code_arr'] = $code;
                    $new_code_arr[$k]['user_id']		  = $val['enroll_user_id'];
                    $new_code_arr[$k]['nick_name']	  = get_user_nickname_by_user_id($val['enroll_user_id']);
                    $new_code_arr[$k]['type'] = "mall_code";
                    if($order_info['type_id']==31)
                    {
                        $new_code_arr[$k]['title'] = "[" . $order_info['type_name'] . "]" . $order_info['detail_list'][0]['goods_name']." ģ��:".$order_info['seller_name'];
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
	 * ��ȡ�̳Ƕ�����ά����ϸ����
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
			$new_code_arr[$k]['name'] = "����";
			
		}
		
		return $new_code_arr;
	}

}

?>