<?php
/**
 * 商家
 * 
 * @author KOKO
 */

class pai_mall_seller_class extends POCO_TDG
{
	var $debug = false;
	public $_redis_cache_name_prefix = "G_YUEUS_MALL_SELLER";
	public $_seller_cover = 'http://image19-d.yueus.com/yueyue/20150918/20150918143119_994447_100000_12837.png?1242x498_130';

	public $_profile_type_id_data = array(
	                                  3 => array(
									             'prefix'=>'hz',
									             'show_key'=>array('hz_experience','hz_team','hz_goodat','hz_othergoodat'),
												 ),
	                                  5 => array(
									             'prefix'=>'t',
									             'show_key'=>array('t_teacher','t_experience'),
												 ),
	                                  12=> array(
									             'prefix'=>'yp',
									             'show_key'=>array('yp_can_photo','yp_lighter','yp_other_equitment','yp_background'),
												 ),
	                                  31=> array(
									             'prefix'=>'m',
									             'show_key'=>array('m_height','m_weight','m_bwh','m_cups','m_cup','m_level','m_experience','m_sex'),
												 ),
	                                  40=> array(
									             'prefix'=>'p',
									             'show_key'=>array('p_experience','p_goodat','p_team'),
												 ),
	                                  41=> array(
									             'prefix'=>'ms',
									             'show_key'=>array('ms_experience','ms_certification','ms_forwarding'),
												 ),
	                                  43=> array(
									             'prefix'=>'ot',
									             'show_key'=>array('ot_label','ot_otherlabel'),
												 ),
									  );

	/**
	 * 
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}
	
	/**
	 * 
	 */
	public function set_debug()
	{
		$this->debug = true;
		$this->set_db_test();
	}
	
	/**
	 * 
	 */
	public function set_db_test()
	{
		$this->setDBName('mall_test_db');
	}
	
	/**
	 *
	 */
	private function set_mall_seller_tbl()
	{
		$this->setTableName('mall_seller_tbl');
	}	
	
	/**
	 *
	 */
	private function set_mall_seller_profile_tbl()
	{
		$this->setTableName('mall_seller_profile_tbl');
	}	
	
	/**
	 *
	 */
	private function set_mall_seller_profile_detail_tbl()
	{
		$this->setTableName('mall_seller_profile_detail_tbl');
	}	
	
	/**
	 *
	 */
	private function set_mall_seller_profile_img_tbl()
	{
		$this->setTableName('mall_seller_profile_img_tbl');
	}	
	
	/**
	 *
	 */
	private function set_mall_seller_user_tbl()
	{
		$this->setTableName('mall_seller_user_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_company_tbl()
	{
		$this->setTableName('mall_company_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_store_tbl()
	{
		$this->setTableName('mall_store_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_seller_updata_log_tbl()
	{
		$this->setTableName('mall_seller_updata_log_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_system_config_tbl()
	{
		$this->setTableName('mall_system_config_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_fulltext_search_log_tbl()
	{
		$this->setTableName('mall_fulltext_search_log_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_seller_service_belong_tbl()
	{
		$this->setTableName('mall_seller_service_belong_tbl');
	}	


	/*
	 * 添加搜索记录
	 * @param array data('con','return');
	 * @param int type('name'=>'value');
	 * return bool
	 */
	public function add_search_log($param,$return,$type_id=1)
	{		
		if(in_array($type_id,array(1,2)))
		{
			$param_in = serialize($param);
			$data_sql = array(
							  'type_id' => $type_id,
							  'search_md5' => md5($param_in),
							  'search_con' => $param_in,
							  'search_data' => serialize($return),
							  'add_time' => time(),
							  );
			$this->set_mall_fulltext_search_log_tbl();
			$seller_id = $this->insert($data_sql,"REPLACE");
		}
		return true;
	}	


	/*
	 * 获取系统配置
	 * return array
	 */
	public function get_search_log($param,$type_id=1)
	{
		$return = array();
		if(in_array($type_id,array(1,2)))
		{
			$param_in = md5(serialize($param));
			$this->set_mall_fulltext_search_log_tbl();
			$re = $this->find("type_id='{$type_id}' and search_md5='{$param_in}'");
			$return = $re['search_data']?unserialize($re['search_data']):array();
		}
		return $return;
	}


	/*
	 * 获取系统配置
	 * return array
	 */
	public function get_system_config()
	{
		$config = array();
		$this->set_mall_system_config_tbl();
		$re = $this->findAll();
		if($re)
		{			
			foreach($re as $val)
			{
				$config[$val['con_name']] = $val['con_value'];
				
			}
		}
		return $config;
	}	


	/*
	 * 修改系统配置
	 * @param array array('name'=>'value');
	 * return bool
	 */
	public function set_system_config($data)
	{
		$this->set_mall_system_config_tbl();
		if($data)
		{			
			foreach($data as $key => $val)
			{
				$data_sql = array(
								  'con_value'=>$val,
								  );
				$this->update($data_sql, "con_name='".$key."'");
			}
		}
		return $config;
	}	


	/*
	 * 商家列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_seller_list($b_select_count = false, $where_str = '', $order_by = 'seller_id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_mall_seller_tbl();
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
	 * 商家资料列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_profile_list($b_select_count = false, $where_str = '', $order_by = 'seller_profile_id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_mall_seller_profile_tbl();
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
	
	
	/**
	 * 添加修改记录
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function add_seller_update_log($seller_id,$type=1,$update_goods = true)
	{
		$seller_id = (int)$seller_id;
		if($seller_id and in_array($type,array(1,2)))
		{
			$this->set_mall_seller_updata_log_tbl();
			$data = array(
						  'seller_id'=>$seller_id,
						  'update_type'=>$type,
						  'update_time'=>time(),
						  );
			$this->insert($data);
			if($type == 2 and $update_goods)
			{
				$goods_obj = POCO::singleton('pai_mall_goods_class');		
				$goods_obj->add_goods_update_log_for_seller($seller_id);
			}
		}
		return true;
	}
    
	
	/**
	 * 添加商家
	 * @param array $data
	 * @return int
	 */
	public function add_seller($data)
	{
		$user_id = (int)$data['user_id'];
		$company_num = (int)$data['company_num']?$data['company_num']:1;
		if($user_id)
		{
			$seller_info = $this->get_seller_info($user_id,2);
			if($seller_info)
			{
				return -1;//已经有,不能添加
			}
			$user_obj = POCO::singleton('pai_user_class');
			$user_info = $user_obj->get_user_info($user_id);			
			if(!$user_info)
			{
				return -3;//用户不存在
			}
			$data['location_id'] = $user_info['location_id'];
			$data['sex'] = 1;
		}
		else
		{
			return -2;//参数错误
		}
		$basic_type = in_array($data['basic_type'],array('company','person'))?$data['basic_type']:'company';
		$data_sql = array(
						  'user_id' => $user_id,
						  'basic_id' => (int)$data['basic_id'],
						  'location_id' => $data['location_id'],
						  'basic_type' => $basic_type,
						  'name' => trim($data['name']),
						  'status' => in_array($data['status'],array(1,2,3))?$data['status']:1,
						  'company_num' => $company_num?$company_num:1,
						  'introduce' => $data['introduce'],
						  'add_time' => time(),
						  'add_user' => $data['add_user'],						
						  );
		$this->set_mall_seller_tbl();
		$seller_id = $this->insert($data_sql);
		$this->add_seller_update_log($seller_id);
		$data['seller_id'] = $seller_id;
		$data['company_num'] = $data_sql['company_num'];
		$data['role_id'] = 1;
		$data['status'] = 1;
		$this->add_seller_profile($data);
		$this->add_seller_user($data);
		$company_id = $this->add_company($data);
		$data['company_id'] = $company_id;
		$this->add_store($data);
        return $seller_id;
	}
	
	
	/**
	 * 添加商家档案资料
	 * @param array $data
	 * @return int
	 */
	private function add_seller_profile($data)
	{
		$user_id = (int)$data['user_id'];
		$seller_id = (int)$data['seller_id'];
		$data = array(
					'seller_id' => $seller_id,
					'user_id' => $user_id ,
					'avatar' => $data['avatar']?$data['avatar']:$data['img'][0]['img_url'],
					'cover' => $data['cover']?$data['cover']:$data['img'][1]['img_url'],
					'name' => trim($data['name']),
					'sex' => $data['sex'],
					'location_id' => $data['location_id'],
					//'introduce' => $data['introduce'],
					'introduce' => str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['introduce']),					
					'add_time' => time(),
					'good_at' => $data['good_at'],
					'work_age' => (int)$data['work_age']?$data['work_age']:1,
					);
		$this->set_mall_seller_profile_tbl();
		$id = $this->insert($data);
		return $id;
	}
	
	
	/**
	 * 添加商家用户
	 * @param array $data
	 * @return int
	 */
	private function add_seller_user($data)
	{
		$user_id = (int)$data['user_id'];
		$seller_id = (int)$data['seller_id'];
		$role_id = (int)$data['role_id'];
		$status = (int)$data['status'];
		$data = array(
					'seller_id' => $seller_id,
					'user_id' => $user_id ,
					'role_id' => $role_id,
					'status' => $status,
					'add_time' => time(),
					);
		$this->set_mall_seller_user_tbl();
		$id = $this->insert($data);
		return $id;
	}
	
	/**
	 * 修改商家交易统计
	 * @param int $seller_id
	 * @param array $data
	 * @return array
	 */
	public function update_seller_statistical($seller_id,$data,$update_goods=true)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		$id = (int)$seller_id;
		$seller_info = $this->get_seller_info($id);		
		if(!$seller_info)
		{
			return $return;
		}
		$bill_buy_num = (int)$data['bill_buy_num'];
		$bill_finish_num = (int)$data['bill_finish_num'];
		$bill_pay_num = (int)$data['bill_pay_num'];
		isset($data['onsale_num'])?$onsale_num = (int)$data['onsale_num']:"";
		isset($data['goods_num'])?$goods_num = (int)$data['goods_num']:"";
		$prices = $data['prices']?sprintf('%.2f',$data['prices']):0;
		
		$update_sql = array();
		$bill_buy_num?$update_sql[] = "bill_buy_num=bill_buy_num+{$bill_buy_num}":"";
		$bill_finish_num?$update_sql[] = "bill_finish_num=bill_finish_num+{$bill_finish_num}":"";
		$bill_pay_num?$update_sql[] = "bill_pay_num=bill_pay_num+{$bill_pay_num}":"";
		is_numeric($onsale_num)?$update_sql[] = "onsale_num={$onsale_num}":"";
		is_numeric($goods_num)?$update_sql[] = "goods_num={$goods_num}":"";
		$prices?$update_sql[] = "prices=prices+{$prices}":"";
		
		if($update_sql)
		{
			$this->set_mall_seller_tbl();
			$up_sql = "update {$this->_db_name}.{$this->_tbl_name} set ".implode(',',$update_sql)." where seller_id='{$id}'";
			$this->query($up_sql);
			////////////////
			$profile_id = $seller_info['seller_data']['profile'][0]['seller_profile_id'];
			$this->set_mall_seller_profile_tbl();
			$up_sql = "update {$this->_db_name}.{$this->_tbl_name} set ".implode(',',$update_sql)." where seller_profile_id='{$profile_id}'";
			$this->query($up_sql);
			$this->add_seller_update_log($id,2,$update_goods);
		}
		$return = array(
						'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}	
	
	/**
	 * 修改商家
	 * @param int $goods_id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function update_seller($id,$data,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		$id = (int)$id;
		$user_id = (int)$user_id;
		$up_user_id = (int)$data['user_id'];
		$company_num = (int)$data['company_num']?$data['company_num']:1;
		$seller_info = $this->get_seller_info($id);		
		if(!$seller_info)
		{
			return $return;
		}
		if($user_id and $seller_info['seller_data']['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		if(!$user_id)//后台管理员操作
		{
			if($up_user_id)
			{
				$seller_info = $this->get_seller_info($up_user_id,2);
				if($seller_info and $seller_info['seller_data']['seller_id'] != $id)
				{
					$return = array(
									'result'=>-3,
									'message'=>'不能添加该用户,该用户已经有商家资料了',
									);
					return $return;
				}
			}
			else
			{
				$return = array(
								'result'=>-4,
								'message'=>'参数错误',
								);
				return $return;
			}
		}
		$info = array();
		$basic_type = in_array($data['basic_type'],array('company','person'))?$data['basic_type']:'company';
		$data_sql = array(
							'name' => trim($data['name']),
							'basic_id' => (int)$data['basic_id'],
							'basic_type' => $basic_type,
							'company_num' => $company_num ,
							'introduce' => $data['introduce'],
							'add_time' => time(),
							'add_user' => $data['add_user'],						
							);
		if(!$user_id)//后台管理员操作
		{
			$data_sql['user_id'] = $up_user_id;
		}					
		$this->set_mall_seller_tbl();
		$this->update($data_sql, "seller_id={$id}");
		$this->add_seller_update_log($id,2);
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}	
	
	/**
	 * 获取商家状态
	 * @param int $seller_id
	 * @param int $type
	 * @return int
	 */	
	public function get_seller_status($id,$type=1)
	{
		$id = (int)$id;
		if(!$id)
		{
			return array();
		}
		$where_str = $type == 1?"seller_id='{$id}'":"user_id='{$id}'";
		$this->set_mall_seller_tbl();
		$info = $this->find($where_str);
		if(!$info)
		{
			return 0;
		}
		return $info['status'];
	}	
	
	/**
	 * 获取商家信息/检测会员是否商家
	 * @param int $seller_id
	 * @param int $type
	 * @return array
	 */	
	public function get_seller_info($id,$type=1)
	{
		$id = (int)$id;
		if(!$id)
		{
			return array();
		}
		$where_str = $type == 1?"seller_id='{$id}'":"user_id='{$id}'";
		$this->set_mall_seller_tbl();
		$info = $this->find($where_str);
		if(!$info)
		{
			return array();
		}
		$info['company'] = $this->get_seller_company_info($info['seller_id'],2);
		//$info['store'] = $this->get_seller_store();
		$info['profile'] = $this->get_seller_profile($info['seller_id'],2);
		$info['service_belong'] = $this->get_seller_service_belong($info['seller_id']);
		//$info['user'] = $this->get_seller_user();
		$return = $this->show_seller_data($info);
		return $return;
	}	

	/**
	 * 添加商家服务所属管理者id
	 * @param int $seller_id
	 * @param int $type_id
	 * @param int $user_id
	 * @return array
	 */
	public function add_seller_service_belong($seller_id,$type_id,$user_id)
	{
		$seller_id = (int)$seller_id;
		$type_id = (int)$type_id;
		$user_id = (int)$user_id;
		if(!($seller_id and $type_id and $user_id))
		{
			return 0;
		}
		$this->set_mall_seller_service_belong_tbl();
		$where_str = "seller_id='{$seller_id}' and type_id='{$type_id}'";
		$info = $this->find($where_str);
		if(!$info)
		{
			$data_sql = array(
							  'seller_id' => $seller_id,
							  'type_id' => $type_id,
							  'user_id' => $user_id,
							  );
			$re = $this->insert($data_sql);
		}
		else
		{
			$re = $info['id'];
		}
		return $re;
	}	

	/**
	 * 获取商家服务所属管理者id
	 * @param int $user_id
	 * @return array
	 */
	public function get_seller_service_belong_by_userid($user_id)
	{
		$user_id = (int)$user_id;
		if(!$user_id)
		{
			return array();
		}
		$this->set_mall_seller_tbl();
		$where_str = "user_id='{$user_id}'";
		$info = $this->find($where_str);
		if($info)
		{
			return $this->get_seller_service_belong($info['seller_id']);
		}
		return array();
	}	

	/**
	 * 获取商家服务所属管理者id
	 * @param int $seller_id
	 * @return array
	 */
	public function get_seller_service_belong($seller_id)
	{
		$seller_id = (int)$seller_id;
		if(!$seller_id)
		{
			return array();
		}
		$this->set_mall_seller_service_belong_tbl();
		$where_str = "seller_id='{$seller_id}'";
		$info = $this->findAll($where_str);
		$belong = array();
		if($info)
		{
			foreach($info as $val)
			{
				$belong[$val['type_id']] = $val['user_id'];
			}
		}
		return $belong;
	}	

	/**
	 * 展示商家信息
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function show_seller_data($data=array())
	{
		$service_id = (int)$service_id;
		$default_data = pai_mall_load_config("seller_attribute");
		$return_data['seller_data'] = $data;
		$return_data['seller_data']['bill_finish_num'] = (string)($return_data['seller_data']['bill_finish_num']+$return_data['seller_data']['old_bill_finish_num']);
		$return_data['seller_data']['profile'][0]['cover']=$return_data['seller_data']['profile'][0]['cover']?$return_data['seller_data']['profile'][0]['cover']:$this->_seller_cover;
		$return_data['default_data'] = $default_data;		
		if($data)
		{
			foreach($return_data['default_data'] as $key => $val)
			{
				$return_data['default_data'][$key]['value'] = $data[$val['key']];
			}
		}
		return $return_data;
	}
	
	/**
	 * 获取商家档案资料
	 * @param int $seller_id
	 * @param int $type 1.id 2.seller_id
	 * @return array
	 */	
	public function get_seller_profile_for_search($id,$type=1)
	{
		$id = (int)$id;
		if(!$id)
		{
			return array();
		}
		$where_str = $type == 1?"seller_profile_id='{$id}'":"seller_id='{$id}'";
		$this->set_mall_seller_profile_tbl();
		$info = $this->findAll($where_str);
		if(!$info)
		{
			return array();
		}
		foreach($info as $val)
		{
			$val['detail'] = $this->get_seller_profile_detail($val['seller_profile_id']);
			$return[] = $this->show_seller_profile_data_for_search($val);
		}
		return $return;
	}
	
	
	/**
	 * 获取商家档案资料
	 * @param int $seller_id
	 * @param int $type 1.id 2.seller_id
	 * @return array
	 */	
	public function get_seller_profile($id,$type=1)
	{
		$id = (int)$id;
		if(!$id)
		{
			return array();
		}
		$where_str = $type == 1?"seller_profile_id='{$id}'":"seller_id='{$id}'";
		$this->set_mall_seller_profile_tbl();
		$info = $this->findAll($where_str);
		if(!$info)
		{
			return array();
		}
		foreach($info as $val)
		{
            $val['average_score'] = $val['review_times']?sprintf('%.1f', ceil($val['total_overall_score'] / $val['review_times'] * 2) / 2):0;//商家综合评价
			$val['avatar'] = get_seller_user_icon($val['user_id']);
			$val['cover'] = $val['cover']?$val['cover']:$this->_seller_cover;
			$val['img'] = $this->get_profile_img($val['seller_profile_id']);
			$val['detail'] = $this->get_seller_profile_detail($val['seller_profile_id']);
			$return[] = $this->show_seller_profile_data($val);
		}
		return $return;
	}
	
	
	/**
	 * 获取商品图片
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function get_profile_img($profile_id)
	{
		$profile_id = (int)$profile_id;
		$re = array();
		if($profile_id)
		{
			$this->set_mall_seller_profile_img_tbl();
			$where_str = "profile_id='{$profile_id}'";
			$order_by = 'profile_img_id asc';
			$re = $this->findAll ( $where_str,'',$order_by );
		}
		return $re;
	}
	

	/**
	 * 获取商家档案资料明细
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function get_seller_profile_detail($profile_id)
	{
		$this->set_mall_seller_profile_detail_tbl();
		$where_str = "profile_id='{$profile_id}'";
		$order_by = 'step asc,profile_detail_id asc';
		$re = $this->findAll($where_str,'',$order_by);
		return $re;
	}

	/**
	 * 展示商家信息
	 * @param array $data
	 * @return bool
	 */
	public function show_seller_profile_data_for_search($data=array())
	{
		$return_data = $data;
		if($data)
		{
			$detail_att = array();
			if($data['type_id'])
			{
				$return_data['att_data'] = $this->get_seller_profile_attribute($data['type_id']);
			}
			foreach($data['detail'] as $val)
			{
				$pro_detail[$val['name']]=$val;
			}
			foreach($return_data['att_data'] as $key => $val)
			{
				$return_data['att_data'][$key]['value'] = $pro_detail[$val['key']]['data'];
			}
			foreach($return_data['att_data'] as $val)
			{
				$return_data['att_data_format'][$val['key']] = $val;
			}
		}
		return $return_data;
	}

	/**
	 * 获取商家资料头像
	 * @param array $data
	 * @return bool
	 */
	public function get_seller_profile_icon($user_id)
	{
		$return = array();
		$user_id = (int)$user_id;
		if($user_id)
		{
			$this->set_mall_seller_profile_tbl();
			$where_str = "user_id='{$user_id}'";
			$re = $this->find($where_str);
			if($re)
			{
				$this->set_mall_seller_profile_tbl();
				$where_str = "seller_id='".$re['seller_id']."'";
				$re_pro = $this->find($where_str);
				$return = array(
								'name'=>$re_pro['name'],
								'avatar'=>$re_pro['avatar'],
								);
			}
		}		
		return $return;
	}

	/**
	 * 展示商家信息
	 * @param array $data
	 * @return bool
	 */
	public function show_seller_profile_data($data=array())
	{
		$default_data = pai_mall_load_config("seller_profile");
		$return_data = $data;
		$return_data['default_data'] = $default_data;
		if($data)
		{
			$detail_att = array();
			if($data['type_id'])
			{
				$return_data['att_data'] = $this->get_seller_profile_attribute($data['type_id']);
			}
			foreach($return_data['default_data'] as $key => $val)
			{
/*				if($val['key']=='avatar')
				{
					$return_data['default_data'][$key]['value'] = get_user_icon($data['user_id'], 165);
				}
				elseif($val['key']=='name')
				{
					$return_data['default_data'][$key]['value'] = get_user_nickname_by_user_id($data['user_id']);
				}
				else
				{
					$return_data['default_data'][$key]['value'] = $data[$val['key']];
				}
*/				$return_data['default_data'][$key]['value'] = $data[$val['key']];
			}
			foreach($data['detail'] as $val)
			{
				$pro_detail[$val['name']]=$val;
			}
			foreach((array)$return_data['att_data'] as $key => $val)
			{
				$val['value'] = $return_data['att_data'][$key]['value'] = $pro_detail[$val['key']]['data'];
				$return_data['att_data_format'][$val['key']] = $val;
				foreach($this->_profile_type_id_data as $key_de => $val_de)
				{
					$prefix = explode('_',$val['key']);
					if($prefix[0] == $val_de['prefix'])
					{
						$val['is_show'] = in_array($val['key'],$val_de['show_key'])?true:false;
						$this->_profile_type_id_data[$key_de]['data'][] = $val;
					}
				}
			}
			$return_data['att_data_for_type_id'] = $this->_profile_type_id_data;
			$img=array();
			foreach($data['img'] as $val)
			{
				$img[] = array(
							   'img_url'=>$val['img_url'],
							   );
			}
			$return_data['image_data'] = array(
											   'name'  =>'图片',
											   'key'   =>'img_url',
											   'input'  =>2,
											   'value'  =>$img,
											   );
		}
		return $return_data;
	}
	
	/**
	 * 修改商家用户昵称
	 * @param int $user_id
	 * @param int $name
	 * @return array
	 */
	public function change_seller_name($user_id,$name)
	{
		$user_id = (int)$user_id;
		if($user_id)
		{
			//去除同步更新用户资料
/*			$this->set_mall_seller_tbl();
			$data_sql = array('user_name'=>$name);
			$this->update($data_sql, "user_id={$user_id}");
			$this->set_mall_seller_profile_tbl();
			$data_sql = array('user_name'=>$name);
			$this->update($data_sql, "user_id={$user_id}");
*/
			$this->set_mall_seller_tbl();
			$data_sql = array('name'=>$name);
			$this->update($data_sql, "user_id={$user_id}");
        }
		return true;
	}
	
	/**
	 * 修改商家用户昵称
	 * @param int $user_id
	 * @param int $name
	 * @return array
	 */
	public function change_seller_user_name($user_id,$name)
	{
		$user_id = (int)$user_id;
		if($user_id)
		{
			$this->set_mall_seller_tbl();
			$data_sql = array('user_name'=>$name);
			$this->update($data_sql, "user_id={$user_id}");
			$this->set_mall_seller_profile_tbl();
			$data_sql = array('user_name'=>$name);
			$this->update($data_sql, "user_id={$user_id}");
        }
		return true;
	}
	
	/**
	 * 修改商家状态
	 * @param int $id
	 * @param int $status
	 * @return array
	 */
	public function change_seller_status($id,$status,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该商家信息',
						);
		$id = (int)$id;
		$status = (int)$status;
		$seller_info = $this->get_seller_info($id);
		if(!$seller_info)
		{
			return $return;
		}
		if($user_id and $seller_info['seller_data']['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商家信息',
							);
			return $return;
		}
		if(!in_array($status,array(1,2)))
		{
			$return = array(
							'result'=>-3,
							'message'=>'参数错误',
							);
			return $return;
		}
		$data_sql = array(
						  'status' => $status,
						  );
		if($status == 2)//取消的时候更新商家公司状态
		{
			//print_r($seller_info);
			foreach($seller_info['seller_data']['company'] as $val)
			{
				$this->change_seller_company_status($val['company_id'],2);
			}
		}
		$this->set_mall_seller_tbl();
		$this->update($data_sql, "seller_id={$id}");
		$this->add_seller_update_log($seller_info['seller_data']['seller_id'],2);
		$return = array(
		                'result'=>1,
						'message'=>'修改成功',
						);
		return $return;
	}
	
	
	/**
	 * 删除商家资料卡服务类型ID
	 * @param int $id
	 * @param array $type_array 类型
	 * @return array
	 */
	public function del_seller_profile_type_id($id,$type_array)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该信息',
						);
		$id = (int)$id;
		$profile_info = $this->get_seller_profile($id);		
		if(!$profile_info)
		{
			return $return;
		}
		$type_array = (array)$type_array;
		foreach($type_array as $key => $val)
		{
			if(!(int)$val)
			{
				unset($type_array[$key]);
			}
		}
		$old_type_array = explode(',',$profile_info[0]['type_id']);
		$new_type_array = array_diff($old_type_array,$type_array);
		$data_sql = array();		
		$data_sql['type_id'] = implode(',',$new_type_array);
		//
		$seller_info = $this->get_seller_info($profile_info[0]['seller_id']);
		$first_store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
		if($first_store_id)
		{
			$data_store_sql['type_id'] = $data_sql['type_id'];
			$this->set_mall_store_tbl();
			$this->update($data_store_sql, "store_id=".$first_store_id);
			/////
			$goods_obj = POCO::singleton('pai_mall_goods_class');
			foreach($type_array as $val)
			{				
				$goods_obj->change_goods_status_by_type_id($profile_info[0]['seller_id'],$val,2);
			}
			/////
		}
		//
		$this->set_mall_seller_profile_tbl();
		$this->update($data_sql, "seller_profile_id={$id}");
		$this->add_seller_update_log($profile_info[0]['seller_id'],2);
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	
	
	/**
	 * 修改商家资料卡服务类型ID
	 * @param int $id
	 * @param array $type_array 新加的类型数组
	 * @return array
	 */
	public function update_seller_profile_type_id($id,$type_array)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该信息',
						);
		$id = (int)$id;
		$profile_info = $this->get_seller_profile($id);		
		if(!$profile_info)
		{
			return $return;
		}
		$type_array = (array)$type_array;
		foreach($type_array as $key => $val)
		{
			if(!(int)$val)
			{
				unset($type_array[$key]);
			}
		}
		$old_type_array = explode(',',$profile_info[0]['type_id']);
		$new_type_array = array_filter(array_unique(array_merge($type_array,$old_type_array)));
		$data_sql = array();		
		$data_sql['type_id'] = implode(',',$new_type_array);
		//
		$seller_info = $this->get_seller_info($profile_info[0]['seller_id']);
		$first_store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
		if($first_store_id)
		{
			$data_store_sql['type_id'] = $data_sql['type_id'];
			$this->set_mall_store_tbl();
			$this->update($data_store_sql, "store_id=".$first_store_id);
		}
		//
		$this->set_mall_seller_profile_tbl();
		$this->update($data_sql, "seller_profile_id={$id}");
		$this->change_seller_status($profile_info[0]['seller_id'],1);//临时商家限制 变为正式商家
		//$this->add_seller_update_log($profile_info[0]['seller_id'],2);
		$this->exec_cmd_pai_mall_follow_user_addtime($profile_info[0]['user_id']);
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	
	
	/**
	 * 修改商家资料卡
	 * @param int $id
	 * @param array $data
	 * @param int $user_id
	 * @param int $update_detail 是否更新服务属性资料
	 * @return array
	 */
	public function update_seller_profile($id,$data,$user_id=0,$update_detail=true)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该信息',
						);
		$id = (int)$id;
		$user_id = (int)$user_id;
		$profile_info = $this->get_seller_profile($id);		
		if(!$profile_info)
		{
			return $return;
		}
		if($user_id and $profile_info[0]['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改',
							);
			return $return;
		}
		$data_sql = array(
						  'avatar' => $data['avatar']?$data['avatar']:$data['img'][0]['img_url'],
						  'cover' => $data['cover']?$data['cover']:$data['img'][1]['img_url'],
						  'name' => trim($data['name']),
						  'sex' => $data['sex'],
						  'location_id' => (int)$data['location_id']>=0?(int)$data['location_id']:$profile_info[0]['location_id'],
						  //'introduce' => $data['introduce'],
						  'introduce' => str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['introduce']),					
						  //'good_at' => $data['good_at'],
						  //'work_age' => $data['work_age'],
						  );
		if(!$user_id)
		{
			foreach($data['type_id'] as $key => $val)
			{
				if(!(int)$val)
				{
					unset($data['type_id'][$key]);
				}
			}
			$data_sql['type_id'] = implode(',',$data['type_id']);
			//
			$seller_info = $this->get_seller_info($profile_info[0]['seller_id']);
			$first_store_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
			$data_store_sql['type_id'] = $data_sql['type_id'];
			$this->set_mall_store_tbl();
			$this->update($data_store_sql, "store_id={$first_store_id}");
			///////////////////删除取消的服务类型商品状态
			$old_type_id = $seller_info['seller_data']['company'][0]['store'][0]['type_id'];
			$old_type_id_array = explode(',',$old_type_id);
			$type_array = array_diff($old_type_id_array,$data['type_id']);
			$goods_obj = POCO::singleton('pai_mall_goods_class');
			foreach($type_array as $val)
			{
				$goods_obj->change_goods_status_by_type_id($profile_info[0]['seller_id'],$val,2);
			}
			///////////////////
			
		}
		$this->set_mall_seller_profile_tbl();
		$this->update($data_sql, "seller_profile_id={$id}");
		$this->set_mall_seller_tbl();
		$this->update(array('location_id'=>$data_sql['location_id']),"seller_id=".$profile_info[0]['seller_id']);
		$this->add_seller_update_log($profile_info[0]['seller_id'],2);
		//更新商品地区
		$goods_obj = POCO::singleton('pai_mall_goods_class');		
		$goods_obj->update_goods_location_id($profile_info[0]['seller_id'],$profile_info[0]['location_id'],$data['location_id']);
/*		//去除同步更新用户资料
		//更新用户表昵称
		$user_obj =  POCO::singleton('pai_user_class');
		$user_obj->update_nickname($profile_info[0]['user_id'],$data['name']);
*/		/////////////////

		if($update_detail)
		{
			$info = array();
			$type_id_str = $data_sql['type_id']?$data_sql['type_id']:$profile_info[0]['type_id'];
			$service_attribute = $this->get_seller_profile_attribute($type_id_str);
			foreach($service_attribute as $val)
			{
				$val['data'] = $data[$val['key']];
				$info[] = $val;
			}
			$this->update_seller_profile_detail($id,$info);
		}
		$img = $data['img'];
		$this->update_profile_img($id,$img);
		//更新用户资料
		$chat_user_obj = POCO::singleton('pai_chat_user_info');
        $chat_user_obj->redis_get_user_info($user_id);
		
		$this->change_seller_name($profile_info[0]['user_id'],$data['name']);
		if($seller_info)
		{
			$this->exec_cmd_pai_mall_follow_user_addtime($seller_info['seller_data']['user_id']);
		}
		//
		//////////////////
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	

	/**
	 * 添加图片
	 * @param int $profile_id
	 * @param array $data
	 * @return bool
	 */
	private function add_profile_img($profile_id,$data)
	{
		$profile_id = (int)$profile_id;
		if($profile_id and $data)
		{
			$this->set_mall_seller_profile_img_tbl();
			foreach($data as $val)
			{
				if($val['img_url'])
				{
					$data = array(
								'profile_id' => $profile_id,
								'img_url' => $val['img_url'],
								);				
					$this->insert($data);
				}
			}
		}
		return true;
	}
	
	
	/**
	 * 更新图片
	 * @param int $profile_id
	 * @param array $data
	 * @return bool
	 */
	private function update_profile_img($profile_id,$data)
	{
		$profile_id = (int)$profile_id;
		if($profile_id)
		{
			$this->set_mall_seller_profile_img_tbl();
			$this->delete("profile_id='{$profile_id}'");
			$this->add_profile_img($profile_id,$data);
		}
		return true;
	}
	
	
	/**
	 * 获取profile的特有属性
	 * @param int $type_id_str
	 * @return array
	 */
	public function get_seller_profile_attribute($type_id_str)
	{
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		if($type_id_str)
		{
			$type_id = explode(',',$type_id_str);
			$detail_att = array();		
			if($type_id)
			{
				$type = $type_obj->get_type_info($type_id);
				$detail_att = array();
				foreach($type as $val)
				{
					$profile_att = unserialize($val['profile_att']);
					if($profile_att)
					{
						$detail_att = array_merge($detail_att,$profile_att);
					}
				}
			}
		}
		return $detail_att;
	}
	
	/**
	 * 更新特定profile detail明细
	 * @param int $profile_id
	 * @param array $data array(array('key'=>'ms_experience','data'=>'初入江湖'),array('key'=>'ms_experience','data'=>'初入江湖'))
	 * @return bool
	 */
	public function update_seller_profile_detail_for_att($profile_id,$data)
	{
		$profile_id = (int)$profile_id;
		if($profile_id)
		{
			$this->set_mall_seller_profile_detail_tbl();
			foreach($data as $val)
			{
				$del_sql = "name = '".pai_mall_change_str_in($val['key'])."' and profile_id='{$profile_id}'";
				$this->delete($del_sql);				
			}
			$this->add_seller_profile_detail($profile_id,$data);
		}
		return true;
	}
	
	/**
	 * 更新明细
	 * @param int $profile_detail_id
	 * @param array $data
	 * @return bool
	 */
	public function update_seller_profile_detail($profile_id,$data)
	{
		$profile_id = (int)$profile_id;
		if($profile_id)
		{
			$this->set_mall_seller_profile_detail_tbl();
			$this->delete("profile_id='{$profile_id}'");
			$this->add_seller_profile_detail($profile_id,$data);
		}
		return true;
	}
	
	/**
	 * 添加profile明细
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function add_seller_profile_detail($profile_id,$data)
	{
		$profile_id = (int)$profile_id;
		if($profile_id and $data)
		{
			$this->set_mall_seller_profile_detail_tbl();
			foreach($data as $key => $val)
			{
				$data = array(
							'profile_id' => $profile_id,
							'profile_detail_type' => 1,
							'name' => $val['key'] ,
							//'name' => $key ,
							'data' => is_array($val['data'])?implode(',',$val['data']):$val['data'],
							'data_type_id' => $val['data_type_id']?$val['data_type_id']:1,
							);
				$this->insert($data);
			}
		}
		return true;
	}

	
	/*
	 * 商家公司店铺列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_seller_store_list($b_select_count = false, $where_str = '', $order_by = 'store_id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_mall_store_tbl();
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str,$fields );
		} 
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}

	
	/*
	 * 商家公司列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_seller_company_list($b_select_count = false, $where_str = '', $order_by = 'company_id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_mall_company_tbl();
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


	/**
	 * 获取商家公司信息
	 * @param int $seller_id
	 * @param int $type 1.id 2.seller_id
	 * @return array
	 */	
	public function get_seller_company_info($id,$type=1)
	{
		$id = (int)$id;
		if(!$id)
		{
			return array();
		}
		$where_str = $type == 1?"company_id='{$id}'":"seller_id='{$id}'";
		$this->set_mall_company_tbl();
		$info = $this->findAll($where_str);
		if(!$info)
		{
			return array();
		}
		foreach($info as $val)
		{
			$val['store'] = $this->get_store_info($val['company_id'],3);
			$return[] = $this->show_seller_company_data($val);
		}
		return $return;
	}
	
	/**
	 * 展示商家公司信息
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function show_seller_company_data($data=array())
	{
		$default_data = pai_mall_load_config("seller_company");
		$return_data = $data;
		$return_data['default_data'] = $default_data;		
		if($data)
		{
			foreach($return_data['default_data'] as $key => $val)
			{
				$return_data['default_data'][$key]['value'] = $data[$val['key']];
			}
		}
		return $return_data;
	}
	
	
	/**
	 * 修改商家资料卡
	 * @param int $goods_id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function update_seller_company($id,$data,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该信息',
						);
		$id = (int)$id;
		$user_id = (int)$user_id;
		$info = $this->get_seller_company_info($id);		
		if(!$info)
		{
			return $return;
		}
		if($user_id and $info[0]['user_id']!= $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		$action_v = $this->check_seller_status($info[0]['seller_id']);//商家已经被关闭
		if(!$action_v)
		{
			$return = array(
							'result'=>-3,
							'message'=>'商家已经被关闭',
							);
			return $return;
		}
		
		$info = array();
		$data_sql = array(
						  'name' => trim($data['name']),
						  'linkman' => $data['linkman'],
						  'tel' => $data['tel'],
						  'fax' => $data['fax'],
						  'email' => $data['email'],
						  'address' => $data['address'],
						  'zipcode' => $data['zipcode'],
						  'demo' => $data['demo'],
						  );
		if(!$user_id)
		{
			$data_sql['store_num'] = (int)$data['store_num'];
		}
		$this->set_mall_company_tbl();
		$this->update($data_sql, "company_id={$id}");		
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	
	/**
	 * 修改商家公司状态
	 * @param int $id
	 * @param int $status
	 * @return array
	 */
	public function change_seller_company_status($id,$status,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该信息',
						);
		$id = (int)$id;
		$status = (int)$status;
		$info = $this->get_seller_company_info($id);
		if(!$info)
		{
			return $return;
		}
		if($user_id and $info[0]['user_id']!= $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		if($status == 1)
		{
			$action_v = $this->check_seller_status($info[0]['seller_id']);//商家已经被关闭
			if(!$action_v)
			{
				$return = array(
								'result'=>-3,
								'message'=>'商家已经被关闭',
								);
				return $return;
			}
		}
		if(!in_array($status,array(1,2)))
		{
			$return = array(
							'result'=>-3,
							'message'=>'参数错误',
							);
			return $return;
		}
		$data_sql = array(
						  'status' => $status,
						  );
		if($status == 2 and $info[0]['store'])
		{
			//更新店铺状态
			foreach($info[0]['store'] as $val)
			{
				$this->change_seller_store_status($val['store_id'],2);
			}
		}
		$this->set_mall_company_tbl();
		$this->update($data_sql, "company_id={$id}");
		$return = array(
		                'result'=>1,
						'message'=>'修改成功',
						);
		return $return;
	}
	
	/**
	 * 添加商家
	 * @param array $data
	 * @return int
	 */
	public function add_company($data,$user_id=0)
	{
		$seller_id = (int)$data['seller_id'];
		$store_num = (int)$data['store_num']?$data['store_num']:1;
		if($seller_id)
		{
			$seller_info = $this->get_seller_info($seller_id);
			if($user_id and $seller_info['seller_data']['user_id']!=$user_id)//无权限
			{
				return -1;
			}
			elseif(count($seller_info['seller_data']['company']) >= $seller_info['seller_data']['company_num'])//超出最大公司数量
			{
				return -3;
			}
			$action_v = $this->check_seller_status($seller_info['seller_data']['seller_id']);//商家已经被关闭
			if(!$action_v)
			{
				return -4;
			}			
		}
		else//参数错误
		{
			return -2;
		}
		$data = array(
					  'user_id' => $seller_info['seller_data']['user_id'],
					  'seller_id' => $seller_id,
					  'name' => trim($data['name']),
					  'linkman' => $data['linkman'],
					  'tel' => $data['tel'],
					  'fax' => $data['fax'],
					  'email' => $data['email'],
					  'address' => $data['address'],
					  'zipcode' => $data['zipcode'],
					  'add_time' => time(),
					  'add_user' => $data['add_user'],
					);
		$this->set_mall_company_tbl();
		$id = $this->insert($data);
		return $id;
	}
	
	/**
	 * 获取商家店铺资料
	 * @param int $id
	 * @param int $type 1.id 2.seller_id 3.company_id
	 * @return array
	 */	
	public function get_store_info($id,$type=1)
	{
		$id = (int)$id;
		if(!$id)
		{
			return array();
		}
		$where_str = $type == 1?"store_id='{$id}'":($type == 2?"seller_id='{$id}'":"company_id='{$id}'");
		$this->set_mall_store_tbl();
		$info = $this->findAll($where_str);
		if(!$info)
		{
			return array();
		}
		foreach($info as $val)
		{
			$return[] = $this->show_store_data($val);
		}
		return $return;
	}
	

	/**
	 * 展示店铺信息
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function show_store_data($data=array())
	{
		$default_data = pai_mall_load_config("seller_store");
		$return_data = $data;
		$return_data['default_data'] = $default_data;	
		if($data)
		{
			foreach($return_data['default_data'] as $key => $val)
			{
				$return_data['default_data'][$key]['value'] = $data[$val['key']];
			}
		}
		return $return_data;
	}
	
	
	/**
	 * 修改商家资料卡
	 * @param int $id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function update_store($store_id,$data,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该信息',
						);
		$store_id = (int)$store_id;
		$user_id = (int)$user_id;
		$info = $this->get_store_info($store_id);		
		if(!$info)
		{
			return $return;
		}
		if($user_id and $info[0]['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		$info = array();
		$data_sql = array(
						  'name' => trim($data['name']),
						  'address' => $data['address'],
						  'zipcode' => $data['zipcode'],
						  'tel' => $data['tel'],
						  'introduce' => $data['introduce'],
						  );
		if(!$user_id)
		{
			foreach($data['type_id'] as $key => $val)
			{
				if(!(int)$val)
				{
					unset($data['type_id'][$key]);
				}
			}
			$data_sql['type_id'] = implode(',',$data['type_id']);
			$this->change_goods_show_by_type_id($store_id,$data_sql['type_id']);
		}
		$this->set_mall_store_tbl();
		$this->update($data_sql, "store_id={$store_id}");
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	
	/**
	 * 修改店铺商品状态
	 * @param int $store_id
	 * @param array $type_id_str 服务类型,号隔开
	 * @return bool
	 */
	public function change_goods_show_by_type_id($store_id,$type_id_str)
	{
		$task_goods_obj = POCO::singleton('pai_mall_goods_class');
		$this->debug?$task_goods_obj->set_db_test():"";//开启调试
		$task_goods_obj->change_goods_show_status_by_store_id($store_id,$type_id_str);
		return true;
	}
	
	/**
	 * 修改店铺商品状态
	 * @param int $store_id
	 * @param array $type_id_str 服务类型,号隔开
	 * @return bool
	 */
	public function change_goods_show_by_store_id($store_id)
	{
		$task_goods_obj = POCO::singleton('pai_mall_goods_class');
		$this->debug?$task_goods_obj->set_db_test():"";//开启调试
		$task_goods_obj->change_goods_show_status_by_store_id($store_id);
		return true;
	}
	
	/**
	 * 添加商家
	 * @param array $data
	 * @param int $user_id
	 * @return int
	 */
	public function add_store($data,$user_id=0)
	{
		$company_id = (int)$data['company_id'];
		$user_id = (int)$user_id;
		if($company_id)
		{
			$company_info = $this->get_seller_company_info($company_id);
            if($user_id and $company_info[0]['user_id']!=$user_id)//无权限
			{
				return -1;
			}
			if(count($company_info[0]['store']) >= $company_info[0]['store_num'])//超出最店铺数量
			{
				return -3;
			}
			$action_v = $this->check_company_status($company_info[0]['company_id']);//商家已经被关闭
			
			if(!$action_v)
			{
				return -4;
			}
		}
		else//参数错误
		{
			return -2;
		}
		$seller_user_id = $company_info[0]['user_id'];
		$seller_id = $company_info[0]['seller_id'];
		$company_id = $company_info[0]['company_id'];
		$data_sql = array(
					  'user_id' => $seller_user_id,
					  'seller_id' => $seller_id,
					  'company_id' => $company_id,
					  'name' => trim($data['name']),
					  'address' => $data['address'],
					  'zipcode' => $data['zipcode'],
					  'tel' => $data['tel'],
					  'introduce' => $data['introduce'],
					  'add_time' => time(),
					  'add_user' => $data['add_user'],
					);
		if(!$user_id)
		{
			foreach($data['type_id'] as $key => $val)
			{
				if(!(int)$val)
				{
					unset($data['type_id'][$key]);
				}
			}
			$data_sql['type_id'] = implode(',',$data['type_id']);
		}
		$this->set_mall_store_tbl();
        
		$id = $this->insert($data_sql);
		return $id;
	}
	
	/**
	 * 修改商家店铺状态
	 * @param int $store_id
	 * @param int $status
	 * @return array
	 */
	public function change_seller_store_status($store_id,$status,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该信息',
						);
		$store_id = (int)$store_id;
		$status = (int)$status;
		$info = $this->get_store_info($store_id);
		if(!$info)
		{
			return $return;
		}
		if($user_id)
		{
			if($info[0]['user_id'] != $user_id)
			{
				$return = array(
								'result'=>-2,
								'message'=>'无权限修改该店铺',
								);
				return $return;
			}
		}
		$action_v = $this->check_company_status($info[0]['company_id']);//商家已经被关闭
		if(!$action_v)
		{
			$return = array(
							'result'=>-3,
							'message'=>'公司已经被关闭',
							);
			return $return;
		}
		if(!in_array($status,array(1,2)))
		{
			$return = array(
							'result'=>-3,
							'message'=>'参数错误',
							);
			return $return;
		}
		$data_sql = array(
						  'status' => $status,
						  );
		if($status == 2)
		{
			$this->change_goods_show_by_store_id($store_id);
		}
		$this->set_mall_store_tbl();
		$this->update($data_sql, "store_id={$store_id}");
		$return = array(
		                'result'=>1,
						'message'=>'修改成功',
						);
		return $return;
	}
	
	/*
	 * 机构启用店铺
	 * @param int $user_id 
	 * return array
	 */
	public function institutions_open_store_by_user_id($user_id)
	{
		$user_id = (int)$user_id;
		if($user_id)
		{
			$re = $this->get_seller_store_list_by_user_id($user_id);
			foreach($re as $val)
			{
				$this->change_seller_store_status($val['store_id'],1,$user_id);
			}
		}
		return true;
	}
	
	/*
	 * 机构关闭店铺
	 * @param int $user_id 
	 * return array
	 */
	public function institutions_close_store_by_user_id($user_id)
	{
		$user_id = (int)$user_id;
		if($user_id)
		{
			$re = $this->get_seller_store_list_by_user_id($user_id);
			foreach($re as $val)
			{
				$this->change_seller_store_status($val['store_id'],2,$user_id);
			}
		}
		return true;
	}
	
	/*
	 * 用户店铺列表
	 * @param int $user_id 
	 * return array
	 */
	public function get_seller_store_list_by_user_id($user_id)
	{
		$user_id = (int)$user_id;
		$ret = array();
		if($user_id)
		{
			$this->set_mall_store_tbl();
			$order_by = 'store_id desc';
			$where_str = "user_id='{$user_id}'";
			$ret = $this->findAll ( $where_str, '', $order_by);
		}
		return $ret;
	}
	
	/*
	 * 商家店铺列表
	 * @param int $seller_id
	 * return array
	 */
	public function get_seller_store_list_by_seller_id($seller_id)
	{
		$seller_id = (int)$seller_id;
		$ret = array();
		if($seller_id)
		{
			$this->set_mall_store_tbl();
			$order_by = 'store_id desc';
			$where_str = "seller_id='{$seller_id}'";
			$ret = $this->findAll ( $where_str, '', $order_by);
		}
		return $ret;
	}
//////////////////////////前台/////////////////////////////////////////////////////////////	
	/*
	 * 前台修改用户信息资料
	 * @param int $profile_id
	 * @param array $data(avatar,cover,name,sex,location_id,introduce,att=>array('m_height','m_weight'......)) //att品类属性
	 * @param int $user_id
	 * return array
	 */
	public function user_update_seller_profile($profile_id,$data,$user_id)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该信息',
						);
		$id = (int)$profile_id;
		$user_id = (int)$user_id;
		$profile_info = $this->get_seller_profile($id);		
		if(!$profile_info)
		{
			return $return;
		}
		if($user_id and $profile_info[0]['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		$data_sql = array();
		$data['avatar']?$data_sql['avatar'] = $data['avatar']:"";
		$data['cover']?$data_sql['cover'] = $data['cover']:"";
		$data['name']?$data_sql['name'] = $data['name']:"";
		$data['sex']?$data_sql['sex'] = $data['sex']:"";
		$data['location_id'] and $data['location_id']!=$profile_info[0]['seller_id']?$data_sql['location_id'] = (int)$data['location_id']:"";
		$data['introduce']?$data_sql['introduce'] = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['introduce']):"";
		if($data_sql)
		{
			$this->set_mall_seller_profile_tbl();
			$this->update($data_sql, "seller_profile_id={$id}");
			if($data_sql['location_id'])
			{
				$this->set_mall_seller_tbl();
				$this->update(array('location_id'=>$data_sql['location_id']),"seller_id=".$profile_info[0]['seller_id']);
				//更新商品地区
				$goods_obj = POCO::singleton('pai_mall_goods_class');		
				$goods_obj->update_goods_location_id($profile_info[0]['seller_id'],$profile_info[0]['location_id'],$data_sql['location_id']);
			}
			//更新用户资料
			$chat_user_obj = POCO::singleton('pai_chat_user_info');
			$chat_user_obj->redis_get_user_info($user_id);
			//
			$data_sql['name']?$this->change_seller_name($profile_info[0]['user_id'],$data_sql['name']):"";
		}
		$att = $data['att'];
		if($att)
		{
			$this->update_seller_profile_detail_for_att($profile_id,$att);		
		}
		$this->add_seller_update_log($profile_info[0]['seller_id'],2);
		//
		//////////////////
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	
	/*
	 * 后台数据整合
	 * @param int $seller_id
	 * return array
	 */
	public function get_seller_search_other_data($seller_id)
	{
		$return = array();
		$return['seller_data']['profile'] = $this->get_seller_profile_for_search($seller_id,2);
		$return['seller_data']['service_belong'] = $this->get_seller_service_belong($seller_id);
		return $return;
	}
	
	/*
	 * 公司店铺列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_seller_store_list_by_company_id($company_id)
	{
		$company_id = (int)$company_id;
		$ret = array();
		if($company_id)
		{
			$this->set_mall_store_tbl();
			$order_by = 'store_id desc';
			$where_str = "company_id='{$company_id}'";
			$ret = $this->findAll ( $where_str, '', $order_by);
		}
		return $ret;
	}
	
	/*
	 * 检测商家状态
	 * @param int $seller_id
	 * return bool
	 */
	public function check_seller_status($seller_id)
	{
		$seller_id = (int)$seller_id;
		$ret = false;
		if($seller_id)
		{
			$this->set_mall_seller_tbl();
			$where_str = "seller_id='{$seller_id}'";
			$re = $this->find($where_str);
			if($re['status'] == 1 || $re['status'] == 3)
			{
				$ret = true;
			}
		}
		return $ret;
	}
	
	/*
	 * 检测商家状态
	 * @param int $company_id
	 * return bool
	 */
	public function check_company_status($company_id)
	{
		$company_id = (int)$company_id;
		$ret = false;
		if($company_id)
		{
			$this->set_mall_company_tbl();
			$where_str = "company_id='{$company_id}'";
			$re = $this->find($where_str);
			if($re['status'] == 1 || $re['status'] == 3)
			{
				$ret = true;
			}
		}
		return $ret;
	}
	
	/*
	 * 根据用户id获取第一个公司第一个店铺的属性类型
	 * @param int $user_id
	 * return array
	 */
	public function get_store_type_id_by_user_id($user_id)
	{
		$user_id = (int)$user_id;
		if(!$user_id)
		{
			return array();
		}
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_list = $type_obj->get_type_cate(2);		
		$seller_info = $this->get_seller_info($user_id,2);
		if(!$seller_info)
		{
			return array();
		}
		$show_type = explode(',',$seller_info['seller_data']['company'][0]['store']['0']['type_id']);
		$type_list_name = array();
		foreach($type_list as $key => $val)
		{
			$type_list[$key]['show'] = in_array($val['id'],$show_type)?true:false;
			$type_list[$key]['selected'] = $val['id']==$type_id?true:false;
			$type_list_name[$val['id']] = $val;
		}
		return $type_list;
	}
	
	/*
	 * 获取商家开通服务id
	 * @param int $user_id
	 * return array
	 */
	public function get_first_profile_type_id_by_user_id($user_id)
	{
		$user_id = (int)$user_id;
		$type_id = '';
		if($user_id)
		{
			$seller_info = $this->get_seller_info($user_id,2);
			$type_id = $seller_info['seller_data']['profile'][0]['type_id'];
		}
		return $type_id;
	}
	
	/*
	 * 获取用户模特聊天等级
	 * @param int $user_id
	 * return array
	 */
	public function get_first_profile_m_level_by_user_id($user_id)
	{
		$user_id = (int)$user_id;
		if($user_id)
		{
			$seller_info = $this->get_seller_info($user_id,2);
			$att = $seller_info['seller_data']['profile'][0]['detail'];
			foreach($att as $val)
			{
				if($val['name'] == 'm_level')
				{
					$m_level = $val['data'];
					break;
				}
			}
		}
		$return = array(
		                'm_level' => $m_level,
		                'type_id' => $seller_info['seller_data']['profile'][0]['type_id'],
						);
		return $return;
	}
	
	/*
	 * 获取用户第一家店铺id
	 * @param int $user_id
	 * return bool
	 */
	public function get_first_store_id_by_user_id($user_id)
	{
		$stroe_id = 0;
		$user_id = (int)$user_id;
		if($user_id)
		{
			$seller_info = $this->get_seller_info($user_id,2);
			$stroe_id = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
		}
		return $stroe_id;
	}
	
	/*
	 * 获取商家列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 return array
	 */
	public function user_search_seller($b_select_count = false, $where_str = '', $order_by = 'seller_profile_id DESC', $limit = '0,10', $fields = '*')
	{
		$return = $this->get_profile_list($b_select_count, $where_str, $order_by, $limit, $fields);
		return $return;
	}
	
	
	/*
	 * 检测店铺状态
	 * @param int $store_id
	 * return bool
	 */
	public function check_store_status($store_id)
	{
		$store_id = (int)$store_id;
		$ret = false;
		if($store_id)
		{
			$this->set_mall_store_tbl();
			$where_str = "store_id='{$store_id}'";
			$re = $this->find($where_str);
			if($re['status'] == 1)
			{
				$ret = true;
			}
		}
		return $ret;
	}
	
	
	/**
	 * 前台搜索商家列表
	 * @param array $data(type_id,location_id,detail_type_id,keyword)
	 * @return array
	 */
	public function user_search_seller_list($data,$limit = '0,10')
	{
        $data['status']=1;
		$data['is_black']=0;
		if(!($data["order"] and $data["order_type"]))
		{
			switch($data['orderby'])//排序
			{
				case 1://销量
					$data["order"]=3;
					$data["order_type"]=1;
				break;
				case 2:
					$data["order"]=3;
					$data["order_type"]=2;
				break;
				case 3://价格
					$data["order"]=4;
					$data["order_type"]=1;
				break;
				case 4:
					$data["order"]=4;
					$data["order_type"]=2;
				break;
				default://综合
					$data["order"]=5;
					$data["order_type"]=1;
				break;
			}
		}
		
		$data["seller_onsale_num"]=$data["seller_onsale_num"]?$data["seller_onsale_num"]:1;
		return $this->search_seller_list_by_fulltext($data,$limit);
	}


	
	/**
	 * 搜索商家列表
	 * @param array $data(type_id,location_id,detail_type_id,keyword)
	 * @return array
	 */
	public function search_seller_list_by_sql($type_id)
	{
		$re = $this->query("SELECT t1.seller_id,t1.type_id FROM mall_db.mall_seller_profile_tbl AS t1 RIGHT JOIN mall_db.mall_seller_tbl AS t2 ON t1.seller_id=t2.seller_id WHERE FIND_IN_SET('".$type_id."',t1.`type_id`);");
		return $re;
	}


	
	/**
	 * 搜索商家列表
	 * @param array $data(type_id=3,location_id,detail_type_id,keyword)
	 * @return array
	 */
	public function search_seller_list_by_fulltext($data,$limit = '0,10')
	{
		//print_r($data);
		if($data['detail'])
		{			
			foreach($data['detail'] as $key => $val)
			{
				$data[$key] = $val;
			}
		}

		////////////////总交易价格
		$data['prices'] = array();
		if($data['total_money_s']!="")
		{
			$data['prices'][] = (int)$data['total_money_s'];
		}
		if($data['total_money_e']!="")
		{
			$data['prices'][] = (int)$data['total_money_e'];
		}
		//$data['prices'] = array_filter($data['prices']);
		sort($data['prices']);
		
		////////////////总交易次数
		$data['total_times'] = array();
		if($data['total_times_s']!="")
		{
			$data['total_times'][] = (int)$data['total_times_s'];
		}
		if($data['total_times_e']!="")
		{
			$data['total_times'][] = (int)$data['total_times_e'];
		}
		//$data['total_times'] = array_filter($data['total_times']);
		sort($data['total_times']);
		
		////////////////上架数
		$data['onsale_num'] = array();
		if($data['list_s']!="")
		{
			$data['onsale_num'][] = (int)$data['list_s'];
		}
		if($data['list_e']!="")
		{
			$data['onsale_num'][] = (int)$data['list_e'];
		}
		//$data['total_times'] = array_filter($data['total_times']);
		sort($data['onsale_num']);
		
		////////////////地区
		if($data["location_id"])
		{
			$data["location_id"] = "*,".substr($data["location_id"],0,6).",*|*,".$data["location_id"].",*";
		}
		else
		{
			if($data["city"])
			{
				$data["location_id"] = "*,".$data["city"]."*";
			}
		}
		//////////////id
		if(is_numeric($data["keywords"]))
		{
			$user_id = $data["keywords"];
			$status = $data['status'];
			unset($data);
			$data["user_id"] = $user_id;
			$data['status']=$status;
			$limit = '0,1';
		}
		
		include_once(G_YUEYUE_ROOT_PATH . "/core/include/fulltext_search_helper/lucoservice/real_search_client.class.php");
		$lucoclient_server_conf = $GLOBALS['LUCOCLIENT_SERVER_CONFIG']['mall'];
		$client = new LucoClient($lucoclient_server_conf['host'], $lucoclient_server_conf['port']);	
			
		$data["keywords"]?$querys["keywords"] = $data["keywords"]:"";//关键字 content,introduction
		isset($data["is_black"])?$querys["seller_is_black"] = $data["is_black"]:"";//黑名单
		$data["type_id"]?$querys["type_id"] = $data["type_id"]:"";//类型
		$data["user_id"]?$querys["seller_user_id"] = $data["user_id"]:"";//用户
		

		$data["seller_onsale_num"]?$querys["seller_onsale_num"] = implode(',',array($data["seller_onsale_num"])):"";//商家状态
		$data["status"]?$querys["seller_status"] = $data["status"]:"";//商家状态
		
		$data["location_id"]?$querys["seller_location_id"] = $data["location_id"]:"";//地区
		
		$data["prices"]?$querys["seller_prices"] = implode(',',$data["prices"]):"";//价格
		$data["total_times"]?$querys["seller_bill_finish_num"] = implode(',',$data["total_times"]):"";//订单完成数量
		$data["onsale_num"]?$querys["seller_onsale_num"] = implode(',',$data["onsale_num"]):"";//上架数
		strtotime($data["begin_time"])?$querys["seller_add_time"] = strtotime($data["begin_time"]).','.(strtotime($data["end_time"])+86400):"";//上架时间

		$data["rating"] and $data["type_id"]?$querys["rating"] = $data["type_id"]."-".$data["rating"]:"";//评级		
		
		if($data['operator_id'])
		{
			switch($data['operator_id'])
			{
				case "批量":
					$querys["seller_add_user"] = '0';
				break;
				case "其他":
					$querys["seller_add_user"] = '0,not';
				break;
				default:
					$querys["seller_add_user"] = (int)$data['operator_id'];
				break;
			}
		}

		/////////////////////////
		if($data["type_id"] == 3)//化妆(ID:3  )
		{			
			$data["hz_experience"]?$querys["hz_experience"] = $data["hz_experience"]:"";//类型数据data
			$data["hz_team"]?$querys["hz_team"] = $data["hz_team"]:"";//类型数据data
			$data["hz_place"]?$querys["hz_place"] = $data["hz_place"]:"";//类型数据data
			$data["hz_order_way"]?$querys["hz_order_way"] = $data["hz_order_way"]:"";//类型数据data
			$data["hz_goodat"]?$querys["hz_goodat"] = ','.$data["hz_goodat"].',':"";//类型数据data
		}
		if($data["type_id"] == 5)//摄影培训(ID:5  )
		{			
			$data["t_teacher"]?$querys["t_teacher"] = $data["t_teacher"]:"";//类型数据data
			$data["t_experience"]?$querys["t_experience"] = $data["t_experience"]:"";//类型数据data
		
		}
		if($data["type_id"] == 12)//影棚(ID:12  )
		{			
			$data["yp_area"]?$querys["yp_area"] = $data["yp_area"]:"";//类型数据data
			$data["yp_background"]?$querys["yp_background"] = $data["yp_background"]:"";//类型数据data
			$data["yp_can_photo"]?$querys["yp_can_photo"] = ','.$data["yp_can_photo"].',':"";//类型数据data
			$data["yp_lighter"]?$querys["yp_lighter"] =','. $data["yp_lighter"].',':"";//类型数据data
			$data["yp_other_equitment"]?$querys["yp_other_equitment"] = ','.$data["yp_other_equitment"].',':"";//类型数据data		
		}
		if($data["type_id"] == 31)//模特服务(ID:31)
		{			
			$data["m_cup"]?$querys["m_cup"] = $data["m_cup"]:"";//类型数据data
			$data["m_cups"]?$querys["m_cups"] = $data["m_cups"]:"";//类型数据data
			$data["m_height"]?$querys["m_height"] = $data["m_height"]:"";//类型数据data
			$data["m_level"]?$querys["m_level"] = $data["m_level"]:"";//类型数据data
			$data["m_sex"]?$querys["m_sex"] = $data["m_sex"]:"";//类型数据data
		}
		if($data["type_id"] == 40)//摄影服务(ID:40)
		{
			$data["p_experience"]?$querys["p_experience"] = $data["p_experience"]:"";//类型数据data
			$data["p_goodat"]?$querys["p_goodat"] = $data["p_goodat"]:"";//类型数据data
			$data["p_team"]?$querys["p_team"] = $data["p_team"]:"";//类型数据data
			$data["p_order_income"]?$querys["p_order_income"] = $data["p_order_income"]:"";//类型数据data
		}
		if($data["type_id"] == 41)//美食(ID:41)
		{
			$data["ms_experience"]?$querys["ms_experience"] = $data["ms_experience"]:"";//类型数据data
			$data["ms_certification"]?$querys["ms_certification"] = $data["ms_certification"]:"";//类型数据data
			$data["ms_forwarding"]?$querys["ms_forwarding"] = $data["ms_forwarding"]:"";//类型数据data
		}
		if($data["type_id"] == 43)//其他(ID:43)
		{
			$data["ot_label"]?$querys["ot_label"] = ','.$data["ot_label"].',':"";//类型数据data
		}
		//
		/////////////////////////
		$querys["limit"] = $limit?$limit:"0,20";
		
		$order_by_type = $data["order"]?$data["order"]:5;
		$querys["order_type"] = $data["order_type"]==2?"ASC":"DESC";
		
		switch($order_by_type)
		{
			case 1:
				$querys["order"] = 'seller_id '.$querys["order_type"].' 3';//商品id
			break;
			case 2:
				$querys["order"] = 'seller_bill_buy_num '.$querys["order_type"].' 4,seller_id DESC 3';//商品id
			break;
			case 3:
				$querys["order"] = 'seller_bill_finish_num '.$querys["order_type"].' 4,seller_id DESC 3';//商品id
			break;
			case 4:
				$querys["order"] = 'total_average_score '.$querys["order_type"].' 4,seller_id DESC 3';//评价
			break;
			case 5:
				$querys["order"] = '_SCORE,seller_seller_level_point '.$querys["order_type"].' 5,seller_id DESC 3';//评价
			break;
			default:
				$querys["order"] = 'seller_id DESC 3';
			break;
		}
		
		
		if($data["debug"])
		{
			print_r($data);
			print_r($querys);		
		}
		
/*		$system_conf = $this->get_system_config();
		$fulltext = $system_conf['FULLTEXT_SELLER'];	
*/		$fulltext = 1;	
		if(!$fulltext)
		{
			$return = $this->get_search_log($querys,2);
			return $return;
		}
		
		//$res = $client ->searchFun("actions.MallFunction.searchMallSeller",$querys);
		$res = $client ->searchFun("actions.MallFunction.searchMallSellerNewTest",$querys);
		
		//$res = $client ->searchFun("actions.MallFunction.searchMallSellerTest",$querys);
		//$res = $client ->searchFun("actions.MallFunction.searchTestSeller",$querys);
		$client->close();//把链接关
		$return = array(
		                'total'=>$res->total,
		                'data'=>$res->resultRow,
						);
		$this->add_search_log($querys,$return,2);
		return $return;
	}
	
	/*
	 * 商家分享文案
	 */
	public function get_share_text($user_id)
	{
		$user_id = (int)$user_id;
		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
	
		$nickname = get_seller_nickname_by_user_id($user_id);

		$title = "我是{$nickname},来约约找我吧 | 约约";

		$content = '买我的时间能干嘛？来了你就知道了。';

		$sina_content = "我是{$nickname}，{$content}";
		$share_url = 'http://www.yueus.com/mall/'.$user_id;
		$share_img = $user_icon_obj->get_seller_user_icon ( $user_id, 165 );
		
		$url = "http://www.yueus.com/output_img.php?img=".$share_img;
		
		$share_text['title'] = $title;
		$share_text['content'] = $content;
		$share_text['sina_content'] = $sina_content.' '.$share_url;
		$share_text['remark'] = '';
		$share_text['url'] = $share_url;
		$share_text['img'] = $url;
		$share_text['user_id'] = $user_id;
		$share_text['qrcodeurl'] = $share_url;
		
		return $share_text;
	}
	
//////////////////////////榜单/////////////////////////////////////////////////////////////	
	/*
	 * 7天内评论总分最高的商家
	 */	
	function seller_commenttop_list($before_day=7,$location_id=0,$type_id,$limit=10)
	{
		$seller_obj = POCO::singleton ( 'pai_mall_comment_class' );
		$seller_list = $seller_obj->seller_comment_rank_list($before_day,$limit*30);
		$return = array();
		$seller = array();
		$type_id = (int)$type_id;
		$limit = (int)$limit;
		if($seller_list)
		{
			$user_id = array();
			foreach($seller_list as $val)
			{
				$user_id[] = $val['user_id'];
				$seller[$val['user_id']] = $val;
			}
			$where = 'is_black=0 and user_id in ('.implode(',',$user_id).') and onsale_num>0';
			$type_id?$where .= ' and FIND_IN_SET ('.$type_id.',type_id)':"";
			$location_id?$where .= ' and location_id = '.(int)$location_id:'';
			$return = $this->user_search_seller(false,$where,'',"0,$limit");
			foreach($return as $key => $val)
			{
				$return[$key]['sum_score'] = $seller[$val['user_id']]['sum_score'];
			}
			aasort($return,array("-sum_score"));
		}
		return $return;
	}
	
//////////////////////////异步/////////////////////////////////////////////////////////////	
	/*
	 * 同步收藏夹添加时间
	 */	
	public function exec_cmd_pai_mall_follow_user_addtime($user_id)
	{
		$pai_gearman_obj = POCO::singleton('pai_gearman_class');		
		$cmd_type = 'pai_mall_follow_user_addtime';		
		$cmd_params = array(
		                    'user_id' => $user_id,
							);
		$send_rst = $pai_gearman_obj->send_cmd($cmd_type,$cmd_params);
		return true;
	}
	
//////////////////////////工具/////////////////////////////////////////////////////////////	
	/*
	 * 同步管理员
	 */	
	function synchronous_belong()
	{
		$seller = $this->query("select * from mall_db.`mall_certificate_service_tbl` WHERE service_type = 'model' AND operator_id!=0 and status=1");
		foreach($seller as $val)
		{
			$seller_id = $this->query("select * from mall_db.`mall_seller_tbl` WHERE user_id=".$val['user_id']);
			echo "user_id:".$val['user_id']."  seller_user_id:".$val['user_id']."  seller_id:".$seller_id[0]['seller_id']."operator_id:".$val['operator_id']."<br>";
			//$this->add_seller_service_belong($seller_id[0]['seller_id'],31,$val['operator_id']);
		}
		//$this->query("UPDATE mall_db.`mall_goods_tbl` AS g,mall_db.`mall_seller_service_belong_tbl` AS s SET g.`belong_user`=s.`user_id` WHERE s.`seller_id`=g.`seller_id` AND s.`user_id`!=g.`belong_user` AND g.`type_id`=31");
	}
	

	/**
	 * 保存预览信息
	 * @param array $data
	 * @return bool
	 */
	public function set_seller_data_for_temp($data)
	{
		$user_id = (int)$data['user_id'];
		$cache_id = $data['cache_id'];
		$return = $this->get_seller_info($user_id,2);
		if($return)
		{
			$return['seller_data']['avatar'] = $data['avatar']?$data['avatar']:$data['img'][0]['img_url'];
			$return['seller_data']['cover'] = $data['cover']?$data['cover']:$data['img'][1]['img_url'];
			$return['seller_data']['name'] = trim($data['name']);
			$return['seller_data']['sex'] = $data['sex'];
			$return['seller_data']['location_id'] = $data['location_id'];
			$return['seller_data']['introduce'] = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['introduce']);
			$return['seller_data']['profile'][0]['cover'] = $return['seller_data']['cover'];			
			foreach($return['seller_data']['profile'][0]['default_data'] as $key => $val)
			{				
				$return['seller_data']['profile'][0]['default_data'][$key]['value'] = is_array($data[$val['key']])?implode(',',$data[$val['key']]):$data[$val['key']];
			}
			foreach($return['seller_data']['profile'][0]['att_data'] as $key => $val)
			{
				$return['seller_data']['profile'][0]['att_data'][$key]['value'] = is_array($data[$val['key']])?implode(',',$data[$val['key']]):$data[$val['key']];
			}
		}
		/////////////////
		$cache_key = $this->_redis_cache_name_prefix."_PROFILETEMP_".$cache_id;
		POCO::setCache($cache_key, $return, array('life_time'=>86400));
		return true;
	}

	/**
	 * 展示预览信息
	 * @param int $cache_id
	 * @return array()
	 */
	public function show_seller_data_for_temp($cache_id)
	{
		$return = array();
		$cache_key = $this->_redis_cache_name_prefix."_PROFILETEMP_".$cache_id;
		$return = POCO::getCache($cache_key);
		return $return;
	}
    /**
     * 组装商家列表前台的数据
     * @param type $list
     * @return type
     */
    public function seller_data_for_front_packing($list)
    {
        $mall_seller_obj 	= POCO::singleton('pai_mall_seller_class');
		$task_goods_obj 	= POCO::singleton('pai_mall_goods_class');
        $default_cover 	= $mall_seller_obj->_seller_cover; 
        
        if( ! empty($list['data']) )
        {
            foreach($list['data'] as $k => &$v)
            {
                // 搜索商家
				$search_data = array(
//                'user_id' => $value['user_id'],
					'keywords' => $v['user_id'],
				);
				$result_list = $mall_seller_obj->user_search_seller_list($search_data, '0,1');

				$search_result = $result_list['data'][0];
				if (empty($search_result)) {
					continue;
				}
                $seller_id = $search_result['user_id'];
                $name = get_seller_nickname_by_user_id($seller_id);
                $buy_num = $v['bill_finish_num']+$v['old_bill_finish_num']; // 购买人数
                $cover = empty($search_result['cover']) ? $default_cover : $search_result['cover'];
                
                $v['goods_id'] =  0;
                $v['seller_user_id'] = $seller_id;
                $v['seller'] = empty($name) ? '商家' : $name; // 暂时作为商家名称
                $v['titles'] =  $buy_num > 0 ? '已售:' . $buy_num  : '已售:' . $buy_num; // preg_replace('/&#\d+;/', '', $search_result['seller_introduce']),
                $v['images'] = yueyue_resize_act_img_url($cover, '640');
                $v['link'] =  'yueyue://goto?seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app'; // 商家首页
                $v['prices'] =  empty($name) ? '商家' : $name; // 暂时作为商家名称
                $v['buy_num']  =  '提供' . $search_result['onsale_num'] . '个服务';
                $v['step'] = '5分';
            }
        }
        return $list;	
                            
    }
    
    /**
     * 属性
     * @param type $list
     * @return type
     */
    public function profile_detail()
    {
		$seller = $this->query("select * from mall_db.`mall_seller_profile_tbl` where type_id like '%31%'");
		$in=1;
		$this->set_mall_seller_profile_detail_tbl();
		foreach($seller as $val)
		{
			$seller_id = $this->query("select * from mall_db.`mall_seller_profile_detail_tbl` WHERE name='m_sex' and profile_id=".$val['seller_profile_id']);
			if($seller_id)
			{
				$in++;
				echo "yes:";
			}
			else
			{
				$in_data = array(
				'profile_id'=>$val['seller_profile_id'],
				'name'=>'m_sex',
				'data'=>'女',
				);
				//$this->insert($in_data);
				echo "no:";
			}
			echo "user_id:".$val['user_id']."  seller_user_id:".$val['type_id']."<br>";
			//$this->add_seller_service_belong($seller_id[0]['seller_id'],31,$val['operator_id']);
		}
		echo count($seller)."==>".$in."<br>";
	}
    
    /**
     * 更新商家评定等级
     * @param type $user_id
     * @param type $type_id
     * @param type $level
     * @return boolean
     */
    public function update_seller_rating($user_id,$type_id,$level)
    {
        $user_id = (int)$user_id;
        $type_id = (int)$type_id;
        $level = (int)$level;
        
        if( ! $user_id || ! $type_id || $level ==='' )
        {
            return false;
        }
        
        $seller_info = $this->get_seller_info($user_id,2);
        
        $this->set_mall_seller_tbl();
        $seller_one = $this->find("user_id='{$user_id}'");
        $now_rating = '';
        
        $seller_rating_config = pai_mall_load_config('seller_rating');
        $goods_obj = POCO::singleton('pai_mall_goods_class');
        $type_name = $goods_obj->get_goods_typename_for_type_id($type_id);
        $note = $type_name.',修改成'.$seller_rating_config[$type_id][$level]['text'];
        
        if( ! empty($seller_one) )
        {
            $seller_rating = $seller_one['rating'];
            
            if(empty($seller_rating))
            {
                $now_rating = $type_id.'-'.$level;
               
                $rs = $this->update(array('rating'=>$now_rating),"user_id='$user_id'");
                
                if( ! empty($seller_info) && $rs )
                {
                    $this->add_seller_update_log($seller_info['seller_data']['seller_id'],2,false);
                    //添加操作日志
                    global $yue_login_id;
                    global $_INPUT;
                    $task_log_obj = POCO::singleton('pai_task_admin_log_class');
                    $task_log_obj->add_log($yue_login_id,3005,2,$_INPUT,$note,$user_id);
                    
                }
                
                return $rs;
                
            }else
            {
                $rating_ary = explode(",",$seller_rating);
                
                //31-1,40-2
                $is_add_new = true;
                foreach($rating_ary as $k => $v)
                {
                    if( preg_match("/$type_id/",$v) )
                    {
                        $is_add_new = false;
                        $rating_ary[$k] = $type_id."-".$level;
                        break;
                    }
                }
                $now_rating = implode(",",$rating_ary);
                
                if( $is_add_new )
                {
                    $now_rating = $seller_rating.",".$type_id."-".$level;
                }
                
                $rs = $this->update(array('rating'=>$now_rating),"user_id='$user_id'");
                if( ! empty($seller_info) && $rs )
                {
                    $this->add_seller_update_log($seller_info['seller_data']['seller_id'],2,false);
                    
                    //添加操作日志
                    global $yue_login_id;
                    global $_INPUT;
                    $task_log_obj = POCO::singleton('pai_task_admin_log_class');
                    $task_log_obj->add_log($yue_login_id,3005,2,$_INPUT,$note,$user_id);
                }
                return true;
            }
        }
        return false;
    }
    /**
     * 获取商家的rating;
     * @param type $user_id
     * @return boolean
     */
    public function get_seller_rating($user_id)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        $this->set_mall_seller_tbl();
        $seller_one = $this->find("user_id='{$user_id}'");
        if( ! empty($seller_one) )
        {
            return $seller_one['rating'];
        }else
        {
            return false;
        }
    
    }
    
    /**
     * 获取店铺的id
     * @param type $user_id
     * @return boolean
     */
    public function get_seller_store_id($user_id)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        $this->set_mall_store_tbl();
        $store_one = $this->find("user_id='$user_id'",'store_id desc');
        if( ! empty($store_one) )
        {
            return (int)$store_one['store_id'];
        }else
        {
            return false;
        }
    }
    
    
	
	public function synchronous_seller()
	{
		$sql = "SELECT * FROM mall_db.`mall_seller_tbl`";
		$re = $this->query($sql);
		foreach($re as $val)
		{
			echo $val['seller_id']."----ok<br>";
			$this->add_seller_update_log($val['seller_id'],2,false);
		}
		echo "<br>over<br>";
	}
    
    /**
     * 添加品类黑名单
     * @param type $user_id
     * @param type $type_id
     * @return boolean
     */
    public function add_type_id_black_list($user_id,$type_id)
    {
        $user_id = (int)$user_id;
        $type_id = (int)$type_id;
        if( ! $user_id || ! $type_id )
        {
            return false;
        }
        $this->set_mall_seller_tbl();
        $seller_info = $this->find("user_id='{$user_id}'");
        $black_ary = array();
        if( ! empty($seller_info) )
        {
            $black_list = $seller_info['black_list'];
            
            if( ! empty($black_list) )
            {
                $black_ary = explode(",",$black_list);
                if(in_array($type_id,$black_ary) )
                {
                    $this->update(array('is_black'=>2),"user_id='{$user_id}'");
                    return true;
                }else
                {
                    $black_ary[] = $type_id;
                    $black_list_value = implode(",", $black_ary);
                    $this->update(array('black_list'=>$black_list_value,'is_black'=>2),"user_id='{$user_id}'");
                    return true;
                }
            }else
            {
                $this->update(array('black_list'=>$type_id,'is_black'=>2),"user_id='{$user_id}'");
                return true;
            }
            
        }else
        {
            return false;
        }
    }
    
    /**
     * 去除品类黑名单
     * @param type $user_id
     * @param type $type_id
     * @return boolean
     */
    public function remove_type_id_black_list($user_id,$type_id)
    {
        $user_id = (int)$user_id;
        $type_id = (int)$type_id;
        if( ! $user_id || ! $type_id)
        {
            return false;
        }
        $this->set_mall_seller_tbl();
        $seller_info = $this->find("user_id='{$user_id}'");
        if( ! empty($seller_info) )
        {
            $black_list = $seller_info['black_list'];
            if( ! empty($black_list) )
            {
                $black_ary = explode(',',$black_list);
                if( in_array($type_id,$black_ary) )
                {
                    $new_black_ary = array();
                    foreach($black_ary as $k => $v)
                    {
                        if($v == $type_id)
                        {
                            continue;
                        }
                        $new_black_ary[] = $v;
                    }
                    if(count($new_black_ary) == 0)
                    {
                        $this->update(array('is_black'=>0,'black_list'=>''), "user_id='{$user_id}'");
                        return true;
                    }else
                    {
                        $new_black_value = implode(",",$new_black_ary);
                        $this->update(array('is_black'=>2,'black_list'=>$new_black_value),"user_id='{$user_id}'");
                        return true;
                    }
                    
                    
                }else
                {
                   return true;
                }
            }else
            {
                return true;
            }
        }else
        {
            return false;
        }
    }
}
