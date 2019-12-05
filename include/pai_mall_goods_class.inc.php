<?php
/**
 * 商品
 * 
 * @author KOKO
 */

class pai_mall_goods_class extends POCO_TDG
{	
	public $debug = false;
	public $_redis_cache_name_prefix = "G_YUEUS_MALL_GOODS";
    
	public $_type_update_data = array(
	                                  3 => array(
									             'goods_detail'=>array(68),
                                                 'goods_price'=>array(152),
									             'profile_detail'=>array(),
												 'scope_id'=>true,
												 'online'=>true,
												 ),
	                                  5 => array(
									             'goods_detail'=>array(62,133,148,317,382),
                                                 'goods_price'=>array(),
									             'profile_detail'=>array('t_teacher','t_experience'),
												 'scope_id'=>false,
												 'online'=>true,
												 ),
	                                  12=> array(
									             'goods_detail'=>array(17,19,20),
                                                 'goods_price'=>array(35),
									             'profile_detail'=>array(),
												 'scope_id'=>true,
												 'online'=>true,
												 ),
	                                  31=> array(
									             'goods_detail'=>array(46,58),
                                                 'goods_price'=>array(75),
									             'profile_detail'=>array('m_bwh','m_cup','m_cups','m_height','m_level','m_weight','m_sex'),
												 'scope_id'=>true,
												 'online'=>true,
												 ),
	                                  40=> array(
									             'goods_detail'=>array(90,99,102,106,289),
                                                 'goods_price'=>array(309),
									             'profile_detail'=>array('p_experience','p_goodat'),
												 'scope_id'=>true,
												 'online'=>true,
												 ),
	                                  41=> array(
									             'goods_detail'=>array(219,220,250),
                                                 'goods_price'=>array(),
									             'profile_detail'=>array('ms_experience','ms_certification','ms_forwarding'),
												 'scope_id'=>true,
												 'online'=>true,
												 ),
	                                  42=> array(
									             'goods_detail'=>array(270),
                                                 'goods_price'=>array(),
									             'profile_detail'=>array(),
												 'scope_id'=>true,
												 'online'=>true,
												 ),
									  43=>array(
												'goods_detail'=>array(278,440),
                                                'goods_price'=>array(279),
												'profile_detail'=>array(),
												'scope_id'=>true,
												'online'=>true
												),
									  );
	
/*	public $_goods_typeid_31_att = array(
										 array(
												 'name'  =>'套餐名称',
												 'key'   =>'name',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'拍摄时长',
												 'key'   =>'95',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'小时',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'底片张数',
												 'key'   =>'96',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'张',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'修片时间',
												 'key'   =>'editphoto_time',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'天',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'精修张数',
												 'key'   =>'97',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'张',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'服装数目',
												 'key'   =>'98',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'套',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'化妆',
												 'key'   =>'99',
												 'mess'  =>'',
												 'input'  =>1,
												 'child_data'=>array(
												                     array('name'=>'提供','key'=>'提供'),
																	 array('name'=>'不提供','key'=>'不提供')
																	 ),//100 101
												 'mess'=>'',
												 'value'=>'提供',
												 ),
										   array(
												 'name'  =>'原片',
												 'key'   =>'102',
												 'mess'  =>'',
												 'input'  =>1,
												 'child_data'=>array(
												                     array('name'=>'全送','key'=>'全送'),
																	 array('name'=>'选送','key'=>'选送'),
																	 array('name'=>'不送','key'=>'不送')
																	 ),//103 104 105
												 'mess'=>'',
												 'value'=>'全送',
												 ),
										   array(
												 'name'  =>'相册',
												 'key'   =>'106',
												 'mess'  =>'',
												 'input'  =>1,
												 'child_data'=>array(
												                     array('name'=>'提供','key'=>'提供'),
																	 array('name'=>'不提供','key'=>'不提供')
																	 ),//107 108
												 'mess'=>'',
												 'value'=>'提供',
												 ),
										   array(
												 'name'  =>'相册内容',
												 'key'   =>'photo_content',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										 );
	
	public $_goods_typeid_444_att = array(
										   array(
												 'name'  =>'所需材料',
												 'key'   =>'re_material',
												 'mess'  =>'',
												 'input'  =>4,
												 'mess'=>'',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'开课人数',
												 'key'   =>'classes_num',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'人起',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'课程时长',
												 'key'   =>'classes_time',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'小时',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'上课时间',
												 'key'   =>'date_time',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'预约时间',
												 'key'   =>'booking_time',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										 );
	
	public $_goods_typeid_445_att = array(
										   array(
												 'name'  =>'商品尺寸',
												 'key'   =>'goods_size',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'商品材质',
												 'key'   =>'goods_material',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'预约时间',
												 'key'   =>'booking_time',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										   array(
												 'name'  =>'上课时间',
												 'key'   =>'custom_time',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										 );
	
	public $_goods_typeid_446_att = array(
										   array(
												 'name'  =>'预约时间',
												 'key'   =>'booking_time',
												 'mess'  =>'',
												 'input'  =>3,
												 'mess'=>'',
												 'value'=>'',
												 ),
										 );
	
*/	
	var $_tempgoods_num = 10;
	public $_is_official_user = array(100832,109650);
	
    
    //goods goods_statistical 能插入的字段
    public $_can_insert = array(
        'goods_id',
        'titles',
        'keyword',
        'profile_id',
        'service_id',
        'type_id',
        'brand_id',
        'seller_id',
        'company_id',
        'store_id',
        'sex',
        'user_id',
        'location_id',
        'lng_lat',
        'prices',
        'unit',
        'detail_list',
        'prices_list',
        'status',
        'is_show',
        'images',
        'introduction',
        'content',
        'prompt',
        'video',
        'demo',
        'belong_user',
        'add_time',
        'update_time',
        'audit_time',
        'onsale_time',
        'is_black',
        'version',
        'stock_type',
        'stock_num_total',
        'stock_num',
        'buy_num',
        'total_overall_score',
        'total_match_score',
        'total_manner_score',
        'total_quality_score',
        'total_comment_point',
        'review_times',
        'pv',
        'uv',
        'bill_pay_num',
        'old_bill_finish_num',
        'bill_finish_num',
        'bill_buy_num',
        'follow_num',
        'prices',
        'step',
        'view_num',
		'seller_name',
		'is_official',
		'edit_status',
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
	private function set_mall_goods_tbl()
	{
		$this->setTableName('mall_goods_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_goods_detail_tbl()
	{
		$this->setTableName('mall_goods_detail_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_goods_img_tbl()
	{
		$this->setTableName('mall_goods_img_tbl');
	}
	
	/**
	 * 
	 */
	private function set_mall_goods_statistical_tbl()
	{
		$this->setTableName('mall_goods_statistical_tbl');
	}
	

	/**
	 * 
	 */
	private function set_mall_goods_prices_tbl()
	{
		$this->setTableName('mall_goods_prices_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_goods_version_tbl()
	{
		$this->setTableName('mall_goods_version_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_goods_updata_log_tbl()
	{
		$this->setTableName('mall_goods_updata_log_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_goods_scope_tbl()
	{
		$this->setTableName('mall_goods_scope_tbl');
	}
        
    private function set_mall_seller_profile_tbl()
	{
		$this->setTableName('mall_seller_profile_tbl');
	}
    
    private function set_mall_seller_profile_detail_tbl()
	{
		$this->setTableName('mall_seller_profile_detail_tbl');
	}

    private function set_mall_goods_check_tbl()
    {
        $this->setTableName('mall_goods_check_tbl');
    }

    private function set_mall_activity_review_tbl()
    {
        $this->setTableName('mall_activity_review_tbl');
    }

	/*
	 * 商品列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_goods_list($b_select_count = false, $where_str = '', $order_by = 'update_time DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_mall_goods_tbl();
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
	 * 商品列表
	 * @param bool $b_select_count
	 * @param string $where_str 
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * return array
	 */
	public function get_goods_list_for_42($b_select_count = false, $where_str = '', $order_by = 'update_time DESC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName('mall_goods_42_tbl');;
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
	 * 添加商品
	 * @param array $data
	 * @return int
	 */
	public function add_goods_old($data,$user_id=0)
	{
		$store_id = (int)$data['store_id'];
		$type_id = (int)$data['type_id'];
		$user_id = (int)$user_id;
		if(!($store_id and $type_id))//参数错误
		{
			$return = array(
							'result'=>-1,
							'message'=>'参数错误',
							);
			return $return;
		}
		$task_type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_data = $task_type_obj->get_type_info($type_id);
		$task_obj = POCO::singleton('pai_mall_seller_class');
		$this->debug?$task_obj->set_db_test():"";//开启调试
		$info = $task_obj->get_store_info($store_id);
		////////////////////
		$seller_info = $task_obj->get_seller_info($info[0]['user_id'],2);
		if($seller_info['seller_data']['status'] == 3 and $seller_info['seller_data']['goods_num'] >= $this->_tempgoods_num)//临时商家限制
		{
			$return = array(
							'result'=>-6,
							'message'=>'超出商品总数，不能添加',
							);
			return $return;
		}
		if($data['default_data']['location_id'] == '')
		{
			$data['default_data']['location_id'] = $data['system_data']['44f683a84163b3523afe57c2e008bc8c'] == '03afdbd66e7929b125f8597834fa83a4'?0:$seller_info['seller_data']['profile'][0]['location_id'];//地区
		}
		////////////////////
		if($info[0]['status']!=1)//店铺已关闭
		{
			$return = array(
							'result'=>-2,
							'message'=>'店铺已关闭',
							);
			return $return;
		}
		if($user_id and $info[0]['user_id'] != $user_id)//无权限操作
		{
			$return = array(
							'result'=>-3,
							'message'=>'无权限操作',
							);
			return $return;
		}
		$type_id_array = explode(',',$info[0]['type_id']);
		if(!in_array($type_id,$type_id_array))//超类型范围操作
		{
			$return = array(
							'result'=>-4,
							'message'=>'商品类型错误',
							);
			return $return;
		}
		//system_data
		$detail = array();
		$detail_list = '';
		if($data['system_data'])
		{
			$type_md5_array = array();
			foreach($data['system_data'] as $key => $val)
			{
				if(is_array($val))
				{
					foreach($val as $val_de)
					{
						$detail[] = array(
										'detail_type'=>1,
										'name'=>$key,
										'data'=>$val_de,
										);
						$type_md5_array[] = md5($key."|".$val_de);
					}
				}
				else
				{
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>$val,
									);
					$type_md5_array[] = md5($key."|".$val);
				}
			}
			/////////
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
			if($type_md5)
			{
				$detail_list_array = array();
				foreach($type_md5 as $val)
				{
					$detail_list_array[] = $val['id'];
				}
				$detail_list = implode(',',$detail_list_array);
			}
			/////////
		}
		if($data['diy_data'])
		{
			foreach($data['diy_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>2,
								'name'=>$key,
								'data'=>$val,
								);
			}
		}
		//组合
		if($data['combination_data'])
		{
			foreach($data['combination_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>3,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//联系信息
		if($data['contact_data'])
		{
			foreach($data['contact_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>4,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//
		$img = $data['img'];
		//exit;
		$prices_de = $data['prices_de'];
		$prices_default = sprintf('%.2f',$data['default_data']['prices']);
		if($prices_default <= 0)
		{
			$min_prices_de = $prices_de;
			$p_data = array_filter($min_prices_de)?min(array_filter($min_prices_de)):0;
			$prices_default = sprintf('%.2f',$p_data);
		}
		if($prices_default <= 0)
		{
			$return = array(
							'result'=>-5,
							'message'=>'商品价格不能为零',
							);
			return $return;
		}
		foreach($prices_de as $key => $val)
		{
			$prices = sprintf('%.2f',$val);
			$prices_de[$key] = $prices;

		}
		$prices_list = $prices_de?serialize($prices_de):"";
		
		$belong_user = $seller_info['seller_data']['service_belong'][$type_id]?$seller_info['seller_data']['service_belong'][$type_id]:0;
		$data_sql = array(
						  'user_id' => $info[0]['user_id'],
						  'seller_id' => $info[0]['seller_id'],
						  'profile_id' => $seller_info['seller_data']['profile'][0]['seller_profile_id'],
						  'company_id' => $info[0]['company_id'],
						  'store_id' => $store_id,
						  'type_id' => $type_id,
						  'buy_num' => $type_data['buy_num'],
						  'stock_type' => $type_data['stock_type'],
						  //'stock_type' => (int)$data['default_data']['stock_type'],
						  'stock_num' => $type_data['stock_type']==1?(int)$data['default_data']['stock_num']:0,
						  'brand_id' => $data['default_data']['brand_id'],
						  'location_id' => $data['default_data']['location_id']?$data['default_data']['location_id']:0,
						  'titles' => $data['default_data']['titles'],
						  'prices' => $prices_default,
						  'unit' => $data['default_data']['unit'],
						  'prices_list'=>$prices_list,
						  'detail_list'=>$detail_list,
						  'images' => $img[0]['img_url'],
						  'status' => 0,
						  //'content' => $data['default_data']['content'],
						  'content' => str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['default_data']['content']),//在线编辑器
						  'video' => $data['default_data']['video'],
						  'demo' => $data['default_data']['demo'],
						  'prompt' => $data['default_data']['prompt'],
						  'introduction' => $data['default_data']['introduction'],
						  'is_show' => 2,
						  'add_time' => time(),
						  'version' => date('YmdHis').rand(100000,999999),
						  'is_black'=>$info[0]['is_black'],		
						  'belong_user'=>$belong_user,			
						  );
		$this->set_mall_goods_tbl();
		$goods_id = $this->insert($data_sql);
		
		if($goods_id)
		{
			//////
			$where_str = "status!=3 and seller_id='".$info[0]['seller_id']."'";
			$ret = $this->findCount ($where_str);			
			$seller_obj = POCO::singleton('pai_mall_seller_class');
			$data = array(
			              'goods_num'=>$ret,
						  );
			$return = $seller_obj->update_seller_statistical($info[0]['seller_id'],$data);
			//////
			$this->add_goods_statistical($goods_id);
			$this->add_goods_detail($goods_id,$detail);
			$this->add_goods_img($goods_id,$img);
			$add_user = $user_id?$user_id:$info[0]['user_id'];
			$this->add_goods_prices($goods_id,$prices_de,$add_user,$type_id);
			$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$type_id);
		}
		$return = array(
						'result'=>$goods_id,
						'message'=>'添加商品成功',
						);
		return $return;
	}
	
	
	/**
	 * 添加商品
	 * @param array $data
	 * @return int
	 */
	public function add_goods_test($data,$user_id=0,$preview=false)
	{
		$store_id = (int)$data['store_id'];
		$type_id = (int)$data['type_id'];
		$user_id = (int)$user_id;
		if(!$preview and !($store_id and $type_id))//参数错误
		{
			$return = array(
							'result'=>-1,
							'message'=>'参数错误',
							);
			return $return;
		}
		$task_type_obj = POCO::singleton('pai_mall_goods_type_class');
		$task_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
		$type_data = $task_type_obj->get_type_info($type_id);
		$task_obj = POCO::singleton('pai_mall_seller_class');
		$this->debug?$task_obj->set_db_test():"";//开启调试
		$info = $task_obj->get_store_info($store_id);
		////////////////////
		$seller_info = $task_obj->get_seller_info($info[0]['user_id'],2);
		if(!$preview and $seller_info['seller_data']['status'] == 3 and $seller_info['seller_data']['goods_num'] >= $this->_tempgoods_num)//临时商家限制
		{
			$return = array(
							'result'=>-6,
							'message'=>'未通过审核,暂时只能发'.$this->_tempgoods_num.'个商品',
							);
			return $return;
		}
		if($data['default_data']['location_id'] == '')
		{
			$data['default_data']['location_id'] = $data['system_data']['44f683a84163b3523afe57c2e008bc8c'] == '03afdbd66e7929b125f8597834fa83a4'?0:$seller_info['seller_data']['profile'][0]['location_id'];//地区
		}
		////////////////////
		if(!$preview and $info[0]['status']!=1)//店铺已关闭
		{
			$return = array(
							'result'=>-2,
							'message'=>'店铺已关闭',
							);
			return $return;
		}
		if($user_id and $info[0]['user_id'] != $user_id)//无权限操作
		{
			$return = array(
							'result'=>-3,
							'message'=>'无权限操作',
							);
			return $return;
		}
		$type_id_array = explode(',',$info[0]['type_id']);
		echo $store_id;
		print_r($type_id_array);
		print_r($type_id);
		if(!$preview and $type_id!=42 and !in_array($type_id,$type_id_array))//超类型范围操作
		{
			$return = array(
							'result'=>-4,
							'message'=>'没开通该服务类型商品权限',
							);
			return $return;
		}
		//system_data
		$detail = array();
		$detail_list = '';
		if($data['system_data'])
		{
			$type_md5_array = array();
			foreach($data['system_data'] as $key => $val)
			{
//				if(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd')))
//				{
//					foreach($this->_goods_typeid_31_att as $key_de => $val_de)
//					{
//						$this->_goods_typeid_31_att[$key_de]['value'] = $val[$val_de['key']];
//					}
//					$detail[] = array(
//									'detail_type'=>1,
//									'name'=>$key,
//									'data'=>serialize($this->_goods_typeid_31_att),
//									);
//				}
//				elseif(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
				if(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
				{
					if($key === '758874998f5bd0c393da094e1967a72b')
					{
						$goods_att_typeid = 314;
					}
					elseif($key === 'ad13a2a07ca4b7642959dc0c4c740ab6')
					{
						$goods_att_typeid = 315;
					}
					elseif($key === '3fe94a002317b5f9259f82690aeea4cd')
					{
						$goods_att_typeid = 316;
					}
					elseif($key === '550a141f12de6341fba65b0ad0433500')
					{
						$goods_att_typeid = 444;
					}
					elseif($key === '67f7fb873eaf29526a11a9b7ac33bfac')
					{
						$goods_att_typeid = 445;
					}
					elseif($key === '1a5b1e4daae265b790965a275b53ae50')
					{
						$goods_att_typeid = 446;
					}
					$goods_typeid_att = $task_type_attribute_obj->get_property_info($goods_att_typeid);
					$goods_typeid_att_detail = unserialize($goods_typeid_att['detail_data']);
					if($goods_typeid_att_detail)
					{
						foreach($goods_typeid_att_detail as $key_de => $val_de)
						{
							$goods_typeid_att_detail[$key_de]['value'] = $val[$val_de['key']];
						}
					}
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>serialize($goods_typeid_att_detail),
									);
				}
				elseif(is_array($val))
				{
					foreach($val as $val_de)
					{
						$detail[] = array(
										'detail_type'=>1,
										'name'=>$key,
										'data'=>$val_de,
										);
						$type_md5_array[] = md5($key."|".$val_de);
					}
				}
				else
				{
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>$val,
									);
					$type_md5_array[] = md5($key."|".$val);
				}
			}
			/////////
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
			if($type_md5)
			{
				$detail_list_array = array();
				foreach($type_md5 as $val)
				{
					$detail_list_array[] = $val['id'];
				}
				$detail_list = implode(',',$detail_list_array);
			}
			/////////
		}
		if($data['diy_data'])
		{
			foreach($data['diy_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>2,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//组合
		if($data['combination_data'])
		{
			foreach($data['combination_data'] as $key => $val)
			{
				if($val['images'])
				{
					foreach($val['images'] as $key_de => $val_de)
					{
						$val['images'][$key_de]['src'] = $key_de;
					}
				}
				$detail[] = array(
								'detail_type'=>3,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//联系信息
		if($data['contact_data'])
		{
			foreach($data['contact_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>4,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//
		$img = $data['img'];
		//exit;
		$prices_de = $data['prices_de'];
		$prices_diy = $data['prices_diy'];
		if($prices_diy)
		{
			if(in_array($type_data['stock_type'],array(4,5,7)))
			{
				$data['default_data']['stock_num']=0;
			}
			foreach($prices_diy as $key => $val)
			{				
				if(in_array($type_data['stock_type'],array(7)))
				{					
					foreach($val['detail']['prices'] as $key_de => $val_de)
					{
						$prices_t_id = time().rand(10000,99999);
						$prices_diy[$key]['detail']['type_id'][$key_de] = $prices_t_id;
						$prices_de[$prices_t_id] = $val_de;
					}
					$prices_diy[$key]['prices']=min(array_filter($val['detail']['prices']));
				}
				else
				{
					$prices_de[$key] = $val['prices'];
				}
				$data['default_data']['stock_num']+=(int)$val['stock_num'];
			}
			
		}
		$prices_default = sprintf('%.2f',$data['default_data']['prices']);
		if($prices_default <= 0)
		{
			$min_prices_de = $prices_de;
			$p_data = array_filter($min_prices_de)?min(array_filter($min_prices_de)):0;
			$prices_default = sprintf('%.2f',$p_data);
		}
		if($prices_default <= 0)
		{
			$return = array(
							'result'=>-5,
							'message'=>'商品价格不能为零',
							'data'=>$data,
							);
			return $return;
		}
		foreach($prices_de as $key => $val)
		{
			$prices = sprintf('%.2f',$val);
			$prices_de[$key] = $prices;
		}
		$prices_list = $prices_de?serialize($prices_de):"";
		
		$belong_user = $seller_info['seller_data']['service_belong'][$type_id]?$seller_info['seller_data']['service_belong'][$type_id]:0;
		$data_sql = array(
						  'user_id' => $info[0]['user_id'],
						  'is_official'=>in_array($info[0]['user_id'],$this->_is_official_user)?1:0,
						  'seller_id' => $info[0]['seller_id'],
						  'profile_id' => $seller_info['seller_data']['profile'][0]['seller_profile_id'],
						  'company_id' => $info[0]['company_id'],
						  'store_id' => $store_id,
						  'type_id' => $type_id,
						  'buy_num' => $type_data['buy_num'],
						  'stock_type' => $type_data['stock_type'],
						  //'stock_type' => (int)$data['default_data']['stock_type'],
						  'stock_num_total' => in_array($type_data['stock_type'],array(1,4,5,7))?(int)$data['default_data']['stock_num']:0,
						  'stock_num' => in_array($type_data['stock_type'],array(1,4,5,7))?(int)$data['default_data']['stock_num']:0,
						  'brand_id' => $data['default_data']['brand_id'],
						  'location_id' => $data['default_data']['location_id']?$data['default_data']['location_id']:0,
						  'lng_lat' => $data['default_data']['lng_lat'],
						  'titles' => trim($data['default_data']['titles']),
						  'prices' => $prices_default,
						  'unit' => $data['default_data']['unit'],
						  'prices_list'=>$prices_list,
						  'detail_list'=>$detail_list,
						  'images' => $img[0]['img_url'],
						  'status' => 0,
						  //'content' => $data['default_data']['content'],
						  'content' => str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['default_data']['content']),//在线编辑器
						  'video' => $data['default_data']['video'],
						  'demo' => $data['default_data']['demo'],
						  'prompt' => $data['default_data']['prompt'],
						  'introduction' => $data['default_data']['introduction'],
						  'is_show' => 2,
						  'add_time' => time(),
						  'version' => date('YmdHis').rand(100000,999999),
						  'is_black'=>$info[0]['is_black'],		
						  'belong_user'=>$belong_user,			
						  );
		//print_r($data);
		//print_r($detail);
		//print_r($data_sql);
		//print_r($prices_de);
		//exit;
		if($preview)
		{
			$return = $data_sql;
			foreach((array)$prices_de as $key => $val)
			{
				$prices_de_format[] = array(
				                            'type_id'=>$key,
				                            'prices'=>$val,
											);
			}
			$return['detail'] = $detail;
			$return['img'] = $img;
			$return['prices_de'] = $prices_diy?$prices_diy:$prices_de_format;
			return $return;
		}
		$this->set_mall_goods_tbl();
		$goods_id = $this->insert($data_sql);
		
		if($goods_id)
		{
			//////
			$where_str = "status!=3 and seller_id='".$info[0]['seller_id']."'";
			$ret = $this->findCount ($where_str);			
			$seller_obj = POCO::singleton('pai_mall_seller_class');
			$data = array(
			              'goods_num'=>$ret,
						  );
			$return = $seller_obj->update_seller_statistical($info[0]['seller_id'],$data,false);
			//////
			$this->add_goods_statistical($goods_id);
			$this->add_goods_detail($goods_id,$detail);
			$this->add_goods_img($goods_id,$img);
			$add_user = $user_id?$user_id:$info[0]['user_id'];
			if(in_array($type_data['stock_type'],array(5,7)))
			{
				$this->add_goods_prices_diy($goods_id,$prices_diy,$add_user,$type_id,$type_data['stock_type']);
			}
			else
			{
				if($type_data['stock_type'] == 4)
				{
					$other_data = $data['prices_de_detail'];
				}
				$this->add_goods_prices($goods_id,$prices_de,$add_user,$type_id,$other_data);
			}
			$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$type_id);
		}
		$return = array(
						'result'=>$goods_id,
						'message'=>'添加商品成功',
						);
		return $return;
	}
	
	
	/**
	 * 添加商品
	 * @param array $data
	 * @return int
	 */
	public function add_goods($data,$user_id=0,$preview=false)
	{
		$store_id = (int)$data['store_id'];
		$type_id = (int)$data['type_id'];
		$user_id = (int)$user_id;
//		if(in_array($type_id,array(5,43)))//参数错误
//		{
//			$return = array(
//							'result'=>-1,
//							'message'=>'暂停品类服务',
//							);
//			return $return;
//		}
		if(!$preview and !($store_id and $type_id))//参数错误
		{
			$return = array(
							'result'=>-1,
							'message'=>'参数错误',
							);
			return $return;
		}
		$task_type_obj = POCO::singleton('pai_mall_goods_type_class');
		$task_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
		$type_data = $task_type_obj->get_type_info($type_id);
		$task_obj = POCO::singleton('pai_mall_seller_class');
		$this->debug?$task_obj->set_db_test():"";//开启调试
		$info = $task_obj->get_store_info($store_id);
		////////////////////
		$seller_info = $task_obj->get_seller_info($info[0]['user_id'],2);
		if(!$preview and $seller_info['seller_data']['status'] == 3 and $seller_info['seller_data']['goods_num'] >= $this->_tempgoods_num)//临时商家限制
		{
			$return = array(
							'result'=>-6,
							'message'=>'未通过审核,暂时只能发'.$this->_tempgoods_num.'个商品',
							);
			return $return;
		}
		if($data['default_data']['location_id'] == '')
		{
			$data['default_data']['location_id'] = $data['system_data']['44f683a84163b3523afe57c2e008bc8c'] == '03afdbd66e7929b125f8597834fa83a4'?0:$seller_info['seller_data']['profile'][0]['location_id'];//地区
		}
		////////////////////
		if(!$preview and $info[0]['status']!=1)//店铺已关闭
		{
			$return = array(
							'result'=>-2,
							'message'=>'店铺已关闭',
							);
			return $return;
		}
		if($user_id and $info[0]['user_id'] != $user_id)//无权限操作
		{
			$return = array(
							'result'=>-3,
							'message'=>'无权限操作',
							);
			return $return;
		}
		$type_id_array = explode(',',$info[0]['type_id']);
		if(!$preview and $type_id!=42 and !in_array($type_id,$type_id_array))//超类型范围操作
		{
			$return = array(
							'result'=>-4,
							'message'=>'没开通该服务类型商品权限',
							);
			return $return;
		}
		//system_data
		$detail = array();
		$detail_list = '';
		if($data['system_data'])
		{
			$type_md5_array = array();
			foreach($data['system_data'] as $key => $val)
			{
//				if(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd')))
//				{
//					foreach($this->_goods_typeid_31_att as $key_de => $val_de)
//					{
//						$this->_goods_typeid_31_att[$key_de]['value'] = $val[$val_de['key']];
//					}
//					$detail[] = array(
//									'detail_type'=>1,
//									'name'=>$key,
//									'data'=>serialize($this->_goods_typeid_31_att),
//									);
//				}
//				elseif(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
				if(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
				{
					if($key === '758874998f5bd0c393da094e1967a72b')
					{
						$goods_att_typeid = 314;
					}
					elseif($key === 'ad13a2a07ca4b7642959dc0c4c740ab6')
					{
						$goods_att_typeid = 315;
					}
					elseif($key === '3fe94a002317b5f9259f82690aeea4cd')
					{
						$goods_att_typeid = 316;
					}
					elseif($key === '550a141f12de6341fba65b0ad0433500')
					{
						$goods_att_typeid = 444;
					}
					elseif($key === '67f7fb873eaf29526a11a9b7ac33bfac')
					{
						$goods_att_typeid = 445;
					}
					elseif($key === '1a5b1e4daae265b790965a275b53ae50')
					{
						$goods_att_typeid = 446;
					}
					$goods_typeid_att = $task_type_attribute_obj->get_property_info($goods_att_typeid);
					$goods_typeid_att_detail = unserialize($goods_typeid_att['detail_data']);
					if($goods_typeid_att_detail)
					{
						foreach($goods_typeid_att_detail as $key_de => $val_de)
						{
							$goods_typeid_att_detail[$key_de]['value'] = $val[$val_de['key']];
						}
					}
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>serialize($goods_typeid_att_detail),
									);
				}
				elseif(is_array($val))
				{
					foreach($val as $val_de)
					{
						$detail[] = array(
										'detail_type'=>1,
										'name'=>$key,
										'data'=>$val_de,
										);
						$type_md5_array[] = md5($key."|".$val_de);
					}
				}
				else
				{
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>$val,
									);
					$type_md5_array[] = md5($key."|".$val);
				}
			}
			/////////
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
			if($type_md5)
			{
				$detail_list_array = array();
				foreach($type_md5 as $val)
				{
					$detail_list_array[] = $val['id'];
				}
				$detail_list = implode(',',$detail_list_array);
			}
			/////////
		}
		if($data['diy_data'])
		{
			foreach($data['diy_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>2,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//组合
		if($data['combination_data'])
		{
			foreach($data['combination_data'] as $key => $val)
			{
				if($val['images'])
				{
					foreach($val['images'] as $key_de => $val_de)
					{
						$val['images'][$key_de]['src'] = $key_de;
					}
				}
				$detail[] = array(
								'detail_type'=>3,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//联系信息
		if($data['contact_data'])
		{
			foreach($data['contact_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>4,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//
		$img = $data['img'];
		//exit;
		$prices_de = $data['prices_de'];
		$prices_diy = $data['prices_diy'];
		if($prices_diy)
		{
			if(in_array($type_data['stock_type'],array(4,5,7)))
			{
				$data['default_data']['stock_num']=0;
			}
			foreach($prices_diy as $key => $val)
			{				
				if(in_array($type_data['stock_type'],array(7)))
				{					
					foreach($val['detail']['prices'] as $key_de => $val_de)
					{
						$prices_t_id = time().rand(10000,99999);
						$prices_diy[$key]['detail']['type_id'][$key_de] = $prices_t_id;
						$prices_de[$prices_t_id] = $val_de;
					}
					$prices_diy[$key]['prices']=min(array_filter($val['detail']['prices']));
				}
				else
				{
					$prices_de[$key] = $val['prices'];
				}
				$data['default_data']['stock_num']+=(int)$val['stock_num'];
			}
			
		}
		$prices_default = sprintf('%.2f',$data['default_data']['prices']);
		if($prices_default <= 0)
		{
			$min_prices_de = $prices_de;
			$p_data = array_filter($min_prices_de)?min(array_filter($min_prices_de)):0;
			$prices_default = sprintf('%.2f',$p_data);
		}
		if($prices_default <= 0)
		{
			$return = array(
							'result'=>-5,
							'message'=>'商品价格不能为零',
							'data'=>$data,
							);
			return $return;
		}
		foreach($prices_de as $key => $val)
		{
			$prices = sprintf('%.2f',$val);
			$prices_de[$key] = $prices;
		}
		$prices_list = $prices_de?serialize($prices_de):"";
		
		$belong_user = $seller_info['seller_data']['service_belong'][$type_id]?$seller_info['seller_data']['service_belong'][$type_id]:0;
		$data_sql = array(
						  'user_id' => $info[0]['user_id'],
						  'is_official'=>in_array($info[0]['user_id'],$this->_is_official_user)?1:0,
						  'seller_id' => $info[0]['seller_id'],
						  'profile_id' => $seller_info['seller_data']['profile'][0]['seller_profile_id'],
						  'company_id' => $info[0]['company_id'],
						  'store_id' => $store_id,
						  'type_id' => $type_id,
						  'buy_num' => $type_data['buy_num'],
						  'stock_type' => $type_data['stock_type'],
						  //'stock_type' => (int)$data['default_data']['stock_type'],
						  'stock_num_total' => in_array($type_data['stock_type'],array(1,4,5,7))?(int)$data['default_data']['stock_num']:0,
						  'stock_num' => in_array($type_data['stock_type'],array(1,4,5,7))?(int)$data['default_data']['stock_num']:0,
						  'brand_id' => $data['default_data']['brand_id'],
						  'location_id' => $data['default_data']['location_id']?$data['default_data']['location_id']:0,
						  'lng_lat' => $data['default_data']['lng_lat'],
						  'titles' => trim($data['default_data']['titles']),
						  'prices' => $prices_default,
						  'unit' => $data['default_data']['unit'],
						  'prices_list'=>$prices_list,
						  'detail_list'=>$detail_list,
						  'images' => $img[0]['img_url'],
						  'status' => 0,
						  //'content' => $data['default_data']['content'],
						  'content' => str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['default_data']['content']),//在线编辑器
						  'video' => $data['default_data']['video'],
						  'demo' => $data['default_data']['demo'],
						  'prompt' => $data['default_data']['prompt'],
						  'introduction' => $data['default_data']['introduction'],
						  'is_show' => 2,
						  'add_time' => time(),
						  'version' => date('YmdHis').rand(100000,999999),
						  'is_black'=>$info[0]['is_black'],		
						  'belong_user'=>$belong_user,			
						  );
		//print_r($data);
		//print_r($detail);
		//print_r($data_sql);
		//print_r($prices_de);
		//exit;
		if($preview)
		{
			$return = $data_sql;
			foreach((array)$prices_de as $key => $val)
			{
				$prices_de_format[] = array(
				                            'type_id'=>$key,
				                            'prices'=>$val,
											);
			}
			$return['detail'] = $detail;
			$return['img'] = $img;
			$return['prices_de'] = $prices_diy?$prices_diy:$prices_de_format;
			return $return;
		}
		$this->set_mall_goods_tbl();
		$goods_id = $this->insert($data_sql);
		
		if($goods_id)
		{
			//////
			$where_str = "status!=3 and seller_id='".$info[0]['seller_id']."'";
			$ret = $this->findCount ($where_str);			
			$seller_obj = POCO::singleton('pai_mall_seller_class');
			$data = array(
			              'goods_num'=>$ret,
						  );
			$return = $seller_obj->update_seller_statistical($info[0]['seller_id'],$data,false);
			//////
			$this->add_goods_statistical($goods_id);
			$this->add_goods_detail($goods_id,$detail);
			$this->add_goods_img($goods_id,$img);
			$add_user = $user_id?$user_id:$info[0]['user_id'];
			if(in_array($type_data['stock_type'],array(5,7)))
			{
				$this->add_goods_prices_diy($goods_id,$prices_diy,$add_user,$type_id,$type_data['stock_type']);
			}
			else
			{
//				if($type_data['stock_type'] == 4)
//				{
//					$other_data = $data['prices_de_detail'];
//				}
				$other_data = $data['prices_de_detail'];
				$this->add_goods_prices($goods_id,$prices_de,$add_user,$type_id,$other_data);
			}
			$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$type_id);
		}
		$return = array(
						'result'=>$goods_id,
						'message'=>'添加商品成功',
						);
		return $return;
	}
	
	/**
	 * 添加修改记录
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function add_goods_update_log($goods_id,$type_id,$type=1,$update=true)
	{
		$goods_id = (int)$goods_id;
		$type_id = (int)$type_id;
		if($goods_id and in_array($type,array(1,2)))
		{
			$this->set_mall_goods_updata_log_tbl();
			$data = array(
						  'goods_id'=>$goods_id,
						  'type_id'=>$type_id,
						  'update_type'=>$type,
						  'update_time'=>time(),
						  );
			$this->insert($data);
			$update?$this->batch_insert_or_update_goods_type_id_tbl($goods_id):"";
			//$this->set_goods_cache($goods_id);
		}		
		return true;
	}
	
	/**
	 * 添加修改记录
	 * @param int $seller_id
	 * @return bool
	 */
	public function add_goods_update_log_for_seller($seller_id)
	{
		$seller_id = (int)$seller_id;
		if($seller_id)
		{
			$this->set_mall_goods_tbl();
			$where = "seller_id = '".$seller_id."'";
			$goods_list = $this->findAll($where);
			if($goods_list)
			{
				foreach($goods_list as $val)
				{					
					$this->exec_cmd_pai_mall_synchronous_goods($val['goods_id'],$val['type_id'],2);
				}
			}		    
		}
		return true;
	}
	
	/**
	 * 获取类型主ID
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function get_goods_type_attribute_maintype($attribute_id,$data)
	{
		foreach($data as $val)
		{
			if($val['id'] == $attribute_id)
			{
				if($val['parents_id']!=0)
				{
					$id = $this->get_goods_type_attribute_maintype($val['parents_id'],$data);
				}
				else
				{
					$id = $attribute_id;
				}
			}
		}		
		return $id;
	}
	
	/**
	 * 添加商品明细
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function add_goods_detail($goods_id,$data)
	{
		$goods_id = (int)$goods_id;
		if($goods_id and $data)
		{
			$this->set_mall_goods_detail_tbl();
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_attribute = $mall_goods_type_attribute_obj->get_type_attribute_cate(0);
			$type_attribute_format = array();
			foreach($type_attribute as $val)
			{
				$type_attribute_format[md5($val['id'])] = $val;
			}
			foreach($data as $val)
			{
				if(in_array($val['detail_type'],array(3,4)))
				{
					$val['name'] = md5($goods_id."_".time().rand(10000,99999)."_".$val['name']);
				}
				$data_sql = array(
							'goods_id' => $goods_id,
							'detail_type' => $val['detail_type'],
							'name' => $val['name'],
							'data' => $val['data'],
							'goods_type_id' => $type_attribute_format[$val['name']]['id'],
							//'type_id' => $type_attribute_format[$val['name']]['id'],
							'type_id' => $this->get_goods_type_attribute_maintype($type_attribute_format[$val['name']]['id'],$type_attribute),
							//'data_type_attribute_id' => $type_attribute_format[$val['data']]['id']?$type_attribute_format[$val['data']]['id']:$val['data'],
							'data_type_attribute_id' => $type_attribute_format[$val['data']]['id'],
							'data_type_id' => $type_attribute_format[$val['name']]['data_type_id'],
							);
				if($type_attribute_format[$val['name']]['data_type_id'] == 2)
				{
					$data['data_format']=strtotime($val['data']);
				}
				$type_md5_array = array(md5($val['name']."|".$val['data']));	
				$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
				if($type_md5)
				{
					$data['data_key']=$type_md5[0]['id'];
				}
				$this->insert($data_sql);
			}
		    $this->set_goods_att_demo($goods_id);
		}
		return true;
	}
	
	/**
	 * 添加商品明细
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function format_goods_detail($data,$type_data)
	{
		foreach($data as $key => $val)
		{
			if($type_data[$val['data']]['parents_list'])
			{
				$p_list = explode(',',$type_data[$val['data']]['parents_list']);
				print_r($p_list);
				print_r($val);
				print_r($type_data[$val['data']]);
			}
		}
		
		return $data;
	}
	
	/**
	 * 添加商品明细
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function add_goods_detail_test($goods_id,$data)
	{
		$goods_id = (int)$goods_id;
		if($goods_id and $data)
		{
			$this->set_mall_goods_detail_tbl();
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_attribute = $mall_goods_type_attribute_obj->get_type_attribute_cate(0);
			$type_attribute_format = array();
			foreach($type_attribute as $val)
			{
				$type_attribute_format[md5($val['id'])] = $val;
			}
			$re_data = $this->format_goods_detail($data,$type_attribute_format);
			print_r($re_data);
			exit;
			print_r($type_attribute_format);
			foreach($data as $val)
			{
				if(in_array($val['detail_type'],array(3,4)))
				{
					$val['name'] = md5($goods_id."_".time().rand(10000,99999)."_".$val['name']);
				}
				$data_sql = array(
							'goods_id' => $goods_id,
							'detail_type' => $val['detail_type'],
							'name' => $val['name'],
							'data' => $val['data'],
							'goods_type_id' => $type_attribute_format[$val['name']]['id'],
							//'type_id' => $type_attribute_format[$val['name']]['id'],
							'type_id' => $this->get_goods_type_attribute_maintype($type_attribute_format[$val['name']]['id'],$type_attribute),
							//'data_type_attribute_id' => $type_attribute_format[$val['data']]['id']?$type_attribute_format[$val['data']]['id']:$val['data'],
							'data_type_attribute_id' => $type_attribute_format[$val['data']]['id'],
							'data_type_id' => $type_attribute_format[$val['name']]['data_type_id'],
							);
				if($type_attribute_format[$val['name']]['data_type_id'] == 2)
				{
					$data['data_format']=strtotime($val['data']);
				}
				$type_md5_array = array(md5($val['name']."|".$val['data']));	
				$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
				if($type_md5)
				{
					$data['data_key']=$type_md5[0]['id'];
				}
				print_r($val);
				print_r($data_sql);
				//$this->insert($data_sql);
			}
		    //$this->set_goods_att_demo($goods_id);
		}
		return true;
	}
	
	/**
	 * 添加商品统计
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function add_goods_statistical($goods_id)
	{
		$goods_id = (int)$goods_id;
		$this->set_mall_goods_statistical_tbl();
		$data = array(
					  'goods_id' => $goods_id,
					  );
		$this->insert($data);
		return true;
	}

	/**
	 * 添加商品图片
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function add_goods_img($goods_id,$data)
	{
		$goods_id = (int)$goods_id;
		if($goods_id and $data)
		{
			$this->set_mall_goods_img_tbl();
			foreach($data as $val)
			{
				if($val['img_url'])
				{
					$data = array(
								'goods_id' => $goods_id,
								'img_url' => $val['img_url'],
								);				
					$this->insert($data);
				}
			}
		}
		return true;
	}

	/**
	 * 添加商品价格策略
	 * @param int $goods_id
	 * @param array $data
	 * @param int $type_id
	 * @param int $other_data
	 * @return bool
	 */
	private function add_goods_prices($goods_id,$data,$add_user,$type_id,$other_data=array())
	{
		$goods_id = (int)$goods_id;
		$type_id = (int)$type_id;
		$add_user = (int)$add_user;
		if($goods_id and $data)
		{
			$scope_data = $this->get_goods_prices_scope_list($type_id);
			$this->set_mall_goods_prices_tbl();
			$stock_num = 0;
			foreach($data as $key => $val)
			{
				$prices = sprintf('%.2f',$val);
				$scope_id = $this->get_goods_prices_scope_id($scope_data,$prices);
				if($prices > 0)
				{
					$data_sql = array(
									  'goods_id' => $goods_id,
									  'type_id' => $key,
									  'scope_id' => $scope_id,
									  'prices' => $prices,
									  'add_time' => time(),
									  'add_user' => $add_user,
									  );
					if($other_data)
					{
						$data_sql['stock_num_total'] = (int)$other_data[$key]['stock_num'];
						$data_sql['stock_num'] = (int)$other_data[$key]['stock_num'];
						$data_sql['buy_num'] = (int)$other_data[$key]['buy_num'];
						$data_sql['time_s'] = strtotime($other_data[$key]['time_s']);
						$data_sql['time_e'] = strtotime($other_data[$key]['time_e']);
						$stock_num += (int)$other_data[$key]['stock_num'];
					}
					$this->insert($data_sql);
				}
			}
			$this->set_mall_goods_tbl();
			$update_stock = array(
								  'stock_num'=>$stock_num,
								  'stock_num_total'=>$stock_num,
								  );
			$this->update($update_stock, "goods_id={$goods_id}");
		}
		return true;
	}


	/**
	 * 添加商品价格
	 * @param int $goods_id
	 * @param array $data
	 * @param int $type_id
	 * @param int $stock_type
	 * @return bool
	 */
	private function check_goods_prices_type_id($type_id)
	{
		$re_type_id = 0;
		if($type_id)
		{
			$this->set_mall_goods_prices_tbl();
			while(!$re_type_id)
			{
				$re = $this->find("type_id='$type_id'");
				if(!$re)
				{
					$re_type_id = $type_id;
				}
				else
				{
					$type_id = time().rand(100000,999999);					
				}
			}			
		}
		return $re_type_id;
	}


	/**
	 * 添加商品价格
	 * @param int $goods_id
	 * @param array $data
	 * @param int $type_id
	 * @param int $stock_type
	 * @return bool
	 */
	private function add_goods_prices_diy($goods_id,$data,$add_user,$type_id,$stock_type='')
	{
		$goods_id = (int)$goods_id;
		$type_id = (int)$type_id;
		$add_user = (int)$add_user;
		if($goods_id and $data)
		{
			$scope_data = $this->get_goods_prices_scope_list($type_id);
			$this->set_mall_goods_prices_tbl();
			$stock_num = 0;
			foreach($data as $key => $val)
			{
				$new_key = $this->check_goods_prices_type_id($key);
				$prices = sprintf('%.2f',$val['prices']);
				$scope_id = $this->get_goods_prices_scope_id($scope_data,$prices);
				if($stock_type == 7 and $val['detail'])
				{
					$prices_list = array();
					$scope_id_arr = array();
					foreach($val['detail']['prices'] as $key_de => $val_de)
					{
						if($val['detail']['prices'][$key_de]>0)
						{
							$scope_id_arr[] = $this->get_goods_prices_scope_id($scope_data,$val['detail']['prices'][$key_de]);
							$p_key = strval($val['detail']['type_id'][$key_de]);
							$prices_list[$p_key] = array(
														'name' => $val['detail']['name'][$key_de],
														'prices' => sprintf('%.2f',$val['detail']['prices'][$key_de]),														
														'stock_num' => $val['detail']['stock_num'][$key_de],
														'stock_num_total' => $val['detail']['stock_num'][$key_de],
														);
						}
					}
					if($scope_id_arr)
					{
						$scope_id = implode(',',array_unique($scope_id_arr));
					}
				}
				if($prices > 0)
				{
					$time_s = strtotime($val['time_s']);
					$time_e = strtotime($val['time_e']);
					$data_sql = array(
									  'goods_id' => $goods_id,
									  'prices_type' => $val['prices_type']?$val['prices_type']:2,
									  'type_id' => $new_key,
									  'scope_id' => $scope_id,
									  'prices' => $prices,
									  'prices_list' => serialize($prices_list),
									  'name' => $val['name'],
									  'mess' => $val['mess'],
									  'stock_num_total' => isset($val['stock_num_total'])?(int)$val['stock_num_total']:(int)$val['stock_num'],
									  'stock_num' => (int)$val['stock_num'],
									  'buy_num' => (int)$val['buy_num'],
									  'time_s' =>$time_s,
									  'time_e' =>$time_e,
									  'week' =>$time_s?date('N',$time_s):0,
									  'prices_status' => (int)$val['prices_status'],
									  'add_time' => time(),
									  'add_user' => $add_user,
									  );
					$stock_num += (int)$val['stock_num'];
					$this->insert($data_sql);
				}
			}
			$this->set_mall_goods_tbl();
			$update_stock = array(
								  'stock_num'=>$stock_num,
								  'stock_num_total'=>$stock_num,
								  );
			$this->update($update_stock, "goods_id={$goods_id}");
			return true;
		}
		return false;
	}


	/**
	 * 删除活动服务价格
	 * @param int $goods_id
	 * @param int $type_id
	 * @return array
	 */
	public function delete_goods_prices_detail_for_42($goods_id,$type_id)
	{
		$goods_id = (int)$goods_id;
		$return = array(
						'result'=>-1,
						'message'=>'参数错误',
						);
		if($goods_id)
		{
			//查看订单列表是否有订单
			$return = array(
							'result'=>-2,
							'message'=>'无相关数据,删除失败',
							);
			//
			$this->set_mall_goods_prices_tbl();
			$where = "goods_id='{$goods_id}' and type_id='".pai_mall_change_str_in($type_id)."'";
			$prices_data = $this->find($where);
			if($prices_data)
			{
				if($prices_data['stock_num'] != $prices_data['stock_num_total'])
				{
					$return = array(
									'result'=>-3,
									'message'=>'已有人下单,不允许删除',
									);
					return $return;
				}
				elseif($prices_data['time_s']<time())
				{
					$return = array(
									'result'=>-4,
									'message'=>'已经过期,不允许删除',
									);
					return $return;
				}
			}
			$this->delete($where);
			$this->update_goods_prices_data($goods_id);
			$this->exec_cmd_pai_mall_synchronous_goods($goods_id,42,2);
			$return = array(
							'result'=>1,
							'message'=>'删除成功',
							);
		}
		return $return;
	}


	/**
	 * 修改服务价格
	 * @param int $goods_id
	 * @param array $data
	 * @param int $add_user
	 * @return bool
	 */
	public function update_goods_prices_detail_for_42($goods_id,$data,$add_user,$editaction=false)
	{
		$return = array(
						'result'=>-1,
						'message'=>'参数错误',
						);
		$goods_id = (int)$goods_id;
		$add_user = (int)$add_user;
		if($goods_id and $data)
		{
			//
			$goods_data = $this->get_goods_info($goods_id);
			$type_id = $goods_data['goods_data']['type_id'];
			$stock_type = $goods_data['goods_data']['stock_type'];
			$prices_diy = $data['prices_diy'];
			if($prices_diy and $type_id == 42 and $stock_type == 7)
			{
				foreach($prices_diy as $key => $val)
				{
					foreach($val['detail']['prices'] as $key_de => $val_de)
					{
						$prices_t_id = strlen($key_de)<14?time().rand(10000,99999):$key_de;
						$prices_diy[$key]['detail']['type_id'][$key_de] = $prices_t_id;
						$val['detail']['type_id'][$key_de] = $prices_t_id;
					}
					$prices_diy[$key]['prices']=min(array_filter($val['detail']['prices']));
					
                    //select
					$this->set_mall_goods_prices_tbl();
					$where = "goods_id='{$goods_id}' and type_id='".pai_mall_change_str_in($key)."'";
					$prices_detail = $this->find($where);
					//print_r($key);
					//print_r($val);
					//print_r($prices_detail);
					//edit
					if($prices_detail)
					{
						$return = array(
										'result'=>-3,
										'message'=>'活动已开始,不能修改',
										);
						if($prices_detail['time_e']>time())
						{
							$return = array(
											'result'=>-4,
											'message'=>'小于报名人数,不能修改',
											);
							//print_r($val);											
							if($prices_detail['stock_num'] == $prices_detail['stock_num_total'])
							{
								$return = array(
												'result'=>1,
												'message'=>'修改成功',
												);
								$where_del = "prices_id='".$prices_detail['prices_id']."'";
								$this->delete($where_del);
								$prices_diy[$key]['prices_status'] = $prices_detail['prices_status'];
								$prices_diy_add = array($key => $prices_diy[$key]);
								$this->add_goods_prices_diy($goods_id,$prices_diy_add,$add_user,$goods_data['goods_data']['type_id'],$goods_data['goods_data']['stock_type']);	
								$this->update_goods_prices_data($goods_id);
								$this->exec_cmd_pai_mall_synchronous_goods($goods_id,42,2);
							}
							else
							{
								$has_num = $prices_detail['stock_num_total']-$prices_detail['stock_num'];
								if($val['stock_num']>=$has_num)
								{
									$return = array(
													'result'=>1,
													'message'=>'修改成功',
													);
									if($editaction)
									{
										$prices_diy[$key]['stock_num_total'] = $val['stock_num'];
										$prices_diy[$key]['stock_num'] =  $val['stock_num']-$has_num;
										$prices_diy[$key]['prices_status'] =  $prices_detail['prices_status'];
										$prices_diy[$key]['buy_num'] =  $val['buy_num'];
										
										$prices_diy_add = array($key => $prices_diy[$key]);
										$where_del = "prices_id='".$prices_detail['prices_id']."'";
										$this->delete($where_del);
										$this->add_goods_prices_diy($goods_id,$prices_diy_add,$add_user,$goods_data['goods_data']['type_id'],$goods_data['goods_data']['stock_type']);
									}
									else
									{
										$update_data['stock_num_total'] = $val['stock_num'];
										$update_data['stock_num'] = $val['stock_num']-$has_num;
										$update_data['buy_num'] =  $val['buy_num'];
										
										$update_data['add_time'] = time();
										$update_data['add_user'] = $add_user;
										$where = "goods_id='{$goods_id}' and type_id='".pai_mall_change_str_in($key)."'";
										$this->update($update_data,$where);
									}
									$this->update_goods_prices_data($goods_id);
									$this->exec_cmd_pai_mall_synchronous_goods($goods_id,42,2);
								}
							}
						}
					}
					else
					{
						$return = array(
										'result'=>-2,
										'message'=>'没有该场次信息',
										);
					}
					//
				}
			}
		}
		return $return;
	}

	/**
	 * 获取服务类型属性
	 * @param string $service_id
	 * @return array
	 */	
	public function get_service_attribute($service_id)
	{
		$service_id = (int)$service_id;
		$task_obj = POCO::singleton('pai_task_service_class');
		$service_info = $task_obj->get_service_info($service_id);
		$service_info['attribute'] = unserialize($service_info['attribute']);
		return $service_info;
	}

	/**
	 * 获取商品统计
	 * @param string $goods_id
	 * @return array
	 */	
	public function get_goods_statistical($goods_id)
	{
		$goods_id = (int)$goods_id;
		if(!$goods_id)
		{
			return array();
		}
		$where_str = "goods_id='{$goods_id}'";
		$this->set_mall_goods_statistical_tbl();
		$goods_info = $this->find($where_str);
		if(!$goods_info)
		{
			return array();
		}
		$goods_info['bill_finish_num'] = (string)($goods_info['bill_finish_num']+$goods_info['old_bill_finish_num']);
		$goods_info['bill_buy_num'] = (string)($goods_info['bill_buy_num']+$goods_info['old_bill_finish_num']);
		$goods_info['bill_pay_num'] = (string)$goods_info['bill_pay_num'];
		return $goods_info;
	}

	/**
	 * 获取服务类型属性
	 * @param string $service_id
	 * @return array
	 */	
	public function get_goods_type_attribute($type_id)
	{
		$type_id = (int)$type_id;
		$return = array();
		if($type_id)
		{
			$task_type_obj = POCO::singleton('pai_mall_goods_type_class');
			$type_data = $task_type_obj->get_type_info($type_id);	
			$return['type_data'] = $type_data;
			$task_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$info = $task_obj->get_type_attribute_by_goods_type_id($type_id,0);
			//print_r($info);
			foreach($info as $key => $val)
			{
				$child_data = array();
				if($val['child_data'])
				{
					foreach($val['child_data'] as $val_de)
					{
						$child_data_2 = array();
						if($val_de['child_data'])
						{
							foreach($val_de['child_data'] as $val_de_2)
							{
								$child_data_2[] = array(
														 'id' => $val_de_2['id'],
														 'name' => $val_de_2['name'],
														 'key' => md5($val_de_2['id']),
														 'type' => 1,
														 'mess' => $val_de_2['message'],
														 'input' => $val_de_2['input_type_id'],
														 'is_prices' => $val['is_prices'],
														  );
							}
						}
						$child_data[] = array(
											 'id' => $val_de['id'],
											 'name' => $val_de['name'],
											 'key' => md5($val_de['id']),
											 'type' => 1,
											 'mess' => $val_de['message'],
											 'input' => $val_de['input_type_id'],
											 'is_prices' => $val['is_prices'],
											 'child_data' => $child_data_2,
											  );
					}
				}
				$att_data = array(
								 'id' => $val['id'],
								 'name' => $val['name'],
								 'key' => md5($val['id']),
								 'type' => 1,
								 'mess' => $val['message'],
								 'input' => $val['input_type_id'],
								 'is_prices' => $val['is_prices'],
								 'child_data' => $child_data,
								  );
				$return['attribute'][] = $att_data;
			}
		}
		return $return;
	}
	
	/**
	 * 检测商品是否特殊
	 * @param string $service_id
	 * @return bool
	 */	
	public function check_goods_special($goods_id)
	{
		$goods_id = (int)$goods_id;
		if(!$goods_id)
		{
			return false;
		}
		$where_str = "goods_id='{$goods_id}'";
		$this->set_mall_goods_tbl();
		$goods_info = $this->find($where_str);
		if(!$goods_info or $goods_info['seller_id'] != 0)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 设置商品信息缓存
	 * @param string $goods_id
	 * @return array
	 */	
	private function set_goods_cache($goods_id)
	{
		$goods_id = (int)$goods_id;
		$return = $this->get_goods_info_by_sql($goods_id);
		if($return)
		{
			$cache_key = $this->_redis_cache_name_prefix."_INFO_".$goods_id;
			POCO::setCache($cache_key, $return, array('life_time'=>604800));
		}
		return $return;
	}
	
	
	/**
	 * 删除商品信息缓存
	 * @param string $goods_id
	 * @return array
	 */	
	private function del_goods_cache($goods_id)
	{
		$goods_id = (int)$goods_id;
		$cache_key = $this->_redis_cache_name_prefix."_INFO_".$goods_id;
		POCO::deleteCache($cache_key);
		return true;
	}
	
	/**
	 * 获取商品信息
	 * @param string $goods_id
	 * @return array
	 */	
	private function get_goods_cache($goods_id)
	{
		$return = array();
		$cache_key = $this->_redis_cache_name_prefix."_INFO_".(int)$goods_id;
		$return = POCO::getCache($cache_key);
		return $return;
	}
	
	/**
	 * 获取商品信息
	 * @param string $goods_id
	 * @return array
	 */	
	public function get_goods_info($goods_id)
	{
		$goods_id = (int)$goods_id;
		if(!$goods_id)
		{
			return array();
		}
		//
		$return = $this->get_goods_cache($goods_id);
		if($return)
		{
			return $return;
		}
		//		
		$return = $this->set_goods_cache($goods_id);
		
		return $return;
	}
	
	/**
	 * 获取商品信息
	 * @param string $service_id
	 * @return array
	 */	
	public function get_goods_info_by_sql($goods_id)
	{
		$goods_id = (int)$goods_id;
		if(!$goods_id)
		{
			return array();
		}
		$where_str = "goods_id={$goods_id}";
		$this->set_mall_goods_tbl();
		$goods_info = $this->find($where_str);
		if(!$goods_info)
		{
			return array();
		}
		
		$goods_info['detail'] = $this->get_goods_detail($goods_id);
		$goods_info['img'] = $this->get_goods_img($goods_id);
		$goods_info['prices_de'] = $this->get_goods_prices_list($goods_id,$goods_info['type_id']);
		$goods_info['statistical'] = $this->get_goods_statistical($goods_id);
		//$goods_info['comment'] = $this->get_goods_comment($goods_id);		
		$return = $this->show_goods_data($goods_info['type_id'],$goods_info);		
		return $return;
	}
	
	
	
	/**
	 * 获取商品信息
	 * @param string $service_id
	 * @return array
	 */	
	public function get_goods_info_for_search($goods_id)
	{
		$goods_id = (int)$goods_id;
		if(!$goods_id)
		{
			return array();
		}
		$where_str = "goods_id='{$goods_id}'";
		$this->set_mall_goods_tbl();
		$goods_info = $this->find($where_str);
		if(!$goods_info)
		{
			return array();
		}
		$goods_info['detail'] = $this->get_goods_detail($goods_id);
		$return = $this->show_goods_data_for_search($goods_info['type_id'],$goods_info);
		return $return;
	}
	
	/**
	 * 获取商品价格
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function get_goods_prices_list($goods_id,$type_id='')
	{
		$goods_id = (int)$goods_id;
		$re = array();
		if($goods_id)
		{
			$this->set_mall_goods_prices_tbl();
			$where_str = "goods_id='{$goods_id}'";
			$order_by = $type_id==42?'name asc,time_s asc,prices_id asc':'prices_id asc';
			$re = $this->findAll ( $where_str,'',$order_by );
		}
		return $re;
	}
	
	/**
	 * 获取商品图片
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function get_goods_img($goods_id)
	{
		$goods_id = (int)$goods_id;
		$re = array();
		if($goods_id)
		{
			$this->set_mall_goods_img_tbl();
			$where_str = "goods_id='{$goods_id}'";
			$order_by = 'goods_img_id asc';
			$re = $this->findAll ( $where_str,'',$order_by );
		}
		return $re;
	}

	/**
	 * 获取商品详细属性
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function get_goods_detail($goods_id)
	{
		$goods_id = (int)$goods_id;
		$re = array();
		if($goods_id)
		{
			$this->set_mall_goods_detail_tbl();
			$where_str = "goods_id='{$goods_id}'";
			$order_by = 'detail_type asc,goods_detail_id asc';
			$re = $this->findAll($where_str,'',$order_by);
		}
		return $re;
	}

	/**
	 * 展示添加信息
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function show_goods_data_for_search($type_id,$data=array())
	{
		$type_id = (int)$type_id;
		$attribute = $this->get_goods_type_attribute($type_id);//系统属性
		$new_default_data = array();
		if($data)
		{
			foreach($new_default_data as $key => $val)
			{
				$new_default_data[$key]['value'] = $data[$val['key']];
			}
		}
		if($attribute['attribute'])
		{
			$system_data = array();
			$diy_data = array();
			$combination_data = array();
			$contact_data = array();
			$detail = array();
			foreach($data['detail'] as $val)
			{
				$detail[md5($val['name'])][] = $val['data'];//属性多选
				//$detail[md5($val['name'])] = $val;
			}
			foreach($attribute['attribute'] as $val)
			{
				$val['value'] = is_array($detail[md5($val['key'])])?implode(',',$detail[md5($val['key'])]):$detail[md5($val['key'])];//属性多选
				//$val['value'] = $detail[md5($val['key'])]['data'];
				if($val['input'] == 2 and $val['is_prices'] == 1)
				{
					$prices_data[] = $val;
				}
				else
				{
					if($val['type'] == 1)
					{
						if($val['child_data'])
						{
							$value_array = $detail[md5($val['key'])];
							foreach($val['child_data'] as $key_de => $val_de)
							{
								if(in_array($val_de['key'],$value_array))
								{
									$val['child_data'][$key_de]['is_select']= true;
								}
								if($val_de['child_data'])//三级
								{
									$value_array_2 = $detail[md5($val_de['key'])];
									foreach($val_de['child_data'] as $key_de_2 => $val_de_2)
									{
										if(in_array($val_de_2['key'],$value_array_2))
										{
											$val['child_data'][$key_de]['child_data'][$key_de_2]['is_select']= true;
										}
										$val['child_data'][$key_de]['child_data'][$key_de_2]['value']= is_array($detail[md5($val_de_2['key'])])?implode(',',$detail[md5($val_de_2['key'])]):$detail[md5($val_de_2['key'])];
									}
								}
								$val['child_data'][$key_de]['value']= is_array($detail[md5($val_de['key'])])?implode(',',$detail[md5($val_de['key'])]):$detail[md5($val_de['key'])];
							}
							$system_data[] = $val;
						}
						else
						{
							$system_data[] = $val;
						}
					}
					elseif($val['type'] == 2)
					{
						$diy_data[] = $val;
					}
				}
			}
		}
		//
		foreach($system_data as $val)
		{
			if($val['input']==1 or $val['input']==2)
			{
				$goods_att[$val['id']] = '';
				if($val['child_data'])
				{
					foreach($val['child_data'] as $val_de)//2
					{
						if($val_de['is_select'])
						{
							$goods_att[$val['id']] .= '['.$val_de['name']."]";
							if($val_de['child_data'] and $val_de['is_select'])
							{
								$goods_att[$val['id']] .= '(';
								$att_name_2 = array();
								foreach($val_de['child_data'] as $val_de_2)//3
								{
									if($val_de_2['is_select'])
									{
										$att_name_2[] = $val_de_2['name'];
									}
								}
								$goods_att[$val['id']] .= implode('|',$att_name_2);
								$goods_att[$val['id']] .= ')';
							}
							$goods_att[$val['id']] .= '<br>';
						}
					}
				}
			}
			else
			{
				$goods_att[$val['id']] = $val['value'];
			}
		}
		$return_data['goods_data'] = $data;
		$return_data['default_data'] = $new_default_data;
		$return_data['system_data'] = $system_data;
		$return_data['goods_att'] = $goods_att;
		$img=array();
		
		return $return_data;
	}

	/**
	 * 保存预览信息
	 * @param array $data
	 * @return bool
	 */
	public function set_goods_data_for_temp($data)
	{
		$type_id = (int)$data['type_id'];
		$store_id = (int)$data['store_id'];
		$cache_id = $data['cache_id'];
		$goods_data = $this->add_goods($data,'',true);
		$goods_info = $this->show_goods_data($type_id,$goods_data);
		/////////////////
		$data = array();
		$data['goods_data'] = $goods_info['goods_data'];
		$data['prices_data_list'] = $goods_info['prices_data_list'];
		foreach((array)$goods_info['default_data'] as $val)
		{
			$data['default_data'][$val['key']] = $val;
		}
		foreach((array)$goods_info['system_data'] as $val)
		{
			$data['system_data'][$val['key']] = $val;
		}
		foreach((array)$goods_info['diy_data'] as $val)
		{
			$data['diy_data'][$val['key']] = $val;
		}
		foreach((array)$goods_info['prices_data'] as $val)
		{
			$data['prices_data'][$val['key']] = $val;
		}
		foreach((array)$goods_info['goods_prices_list'] as $val)
		{
			$data['goods_prices_list'][$val['key']] = $val;
		}
		$return = array(
						'result'=>1,
						'message'=>'成功',
						'data'=>$data,
						);
		$cache_key = $this->_redis_cache_name_prefix."_GOODSTEMP_".$cache_id;
		POCO::setCache($cache_key, $return, array('life_time'=>86400));
		return true;
	}

	/**
	 * 展示预览信息
	 * @param int $cache_id
	 * @return array()
	 */
	public function show_goods_data_for_temp($cache_id)
	{
		$return = array();
		$cache_key = $this->_redis_cache_name_prefix."_GOODSTEMP_".$cache_id;
		$return = POCO::getCache($cache_key);
		return $return;
	}

	/**
	 * 展示添加信息
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	public function show_goods_data($type_id,$data=array())
	{
		$type_id = (int)$type_id;
		$default_data = pai_mall_load_config("goods_default_attribute");
		$attribute = $this->get_goods_type_attribute($type_id);//系统属性
		$new_default_data = array();
		foreach($default_data as $key => $val)
		{
			if($val['key'] == 'stock_num')
			{
				if($data)
				{
					if($data['stock_type'] !=1)
					{
						continue;
					}
				}
				elseif($attribute['type_data']['stock_type'] != 1)
				{
					continue;
				}
			}
			$new_default_data[] = $val;
		}
		if($data)
		{
			foreach($new_default_data as $key => $val)
			{
				$new_default_data[$key]['value'] = $data[$val['key']];
			}
		}
		$combination_data = array();
		$contact_data = array();
		if($attribute['attribute'])
		{
			$system_data = array();
			$diy_data = array();
			$detail = array();
			foreach((array)$data['detail'] as $val)
			{
				$detail[md5($val['name'])][] = $val['data'];//属性多选
				//$detail[md5($val['name'])] = $val;
			}
			foreach($attribute['attribute'] as $val)
			{
				$val['value'] = is_array($detail[md5($val['key'])])?implode(',',$detail[md5($val['key'])]):$detail[md5($val['key'])];//属性多选
				//$val['value'] = $detail[md5($val['key'])]['data'];
				if($val['input'] == 2 and $val['is_prices'] == 1)
				{
					$prices_data[] = $val;
				}
				else
				{
					if($val['type'] == 1)
					{
						if($val['child_data'])
						{
							$value_array = $detail[md5($val['key'])];
							foreach($val['child_data'] as $key_de => $val_de)
							{
								if(in_array($val_de['key'],(array)$value_array))
								{
									$val['child_data'][$key_de]['is_select']= true;
								}
								if($val_de['child_data'])//三级
								{
									$value_array_2 = $detail[md5($val_de['key'])];
									foreach($val_de['child_data'] as $key_de_2 => $val_de_2)
									{
										if(in_array($val_de_2['key'],(array)$value_array_2))
										{
											$val['child_data'][$key_de]['child_data'][$key_de_2]['is_select']= true;
										}
										$val['child_data'][$key_de]['child_data'][$key_de_2]['value']= is_array($detail[md5($val_de_2['key'])])?implode(',',$detail[md5($val_de_2['key'])]):$detail[md5($val_de_2['key'])];
									}
								}
								$val['child_data'][$key_de]['value']= is_array($detail[md5($val_de['key'])])?implode(',',$detail[md5($val_de['key'])]):$detail[md5($val_de['key'])];
							}
							$system_data[] = $val;
						}
						else
						{
							$system_data[] = $val;
						}
					}
					elseif($val['type'] == 2)
					{
						$diy_data[] = $val;
					}
				}
			}
			/////////////////
			if($prices_data)
			{
				$prices_data_list = $this->get_goods_prices_list_data($prices_data);
			}
			if($prices_data_list and $data['prices_de'])
			{
				$goods_prices_list = array();
				$prices_detail = array();
				foreach($data['prices_de'] as $val)
				{
					$prices_detail[$val['type_id']] = $val;
				}
				foreach($prices_data_list as $key => $val)
				{
					$val['value'] = $prices_detail[$val['key']]['prices'];
					$val['stock_num_total'] = $prices_detail[$val['key']]['stock_num_total'];
					$val['stock_num'] = $prices_detail[$val['key']]['stock_num'];
					$val['buy_num'] = $prices_detail[$val['key']]['buy_num'];
					$val['time_s'] = $prices_detail[$val['key']]['time_s'];
					$val['time_e'] = $prices_detail[$val['key']]['time_e'];
					$prices_data_list[$key] = $val;
					if($val['value'])
					{
						$goods_prices_list[] = $val;
						$goods_prices_type[] = $val['key'];
					}
				}
				
			}
		}
		//
		$task_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
		foreach($system_data as $key => $val)
		{
			if($val['input']==1 or $val['input']==2)
			{
				$goods_att[$val['id']] = '';
				if($val['child_data'])
				{
					foreach($val['child_data'] as $val_de)//2
					{
						if($val_de['is_select'])
						{
							$goods_att[$val['id']] .= '['.$val_de['name']."]";
							if($val_de['child_data'] and $val_de['is_select'])
							{
								$goods_att[$val['id']] .= '(';
								$att_name_2 = array();
								foreach($val_de['child_data'] as $val_de_2)//3
								{
									if($val_de_2['is_select'])
									{
										$att_name_2[] = $val_de_2['name'];
									}
								}
								$goods_att[$val['id']] .= implode('|',$att_name_2);
								$goods_att[$val['id']] .= ')';
							}
							$goods_att[$val['id']] .= '<br>';
						}
					}
				}
			}
			else
			{
				$goods_att[$val['id']] = $val['value'];
			}
			//
//			if(in_array($val['id'],array(314,315,316)))
//			{
//				$typeid_31_att = array();
//				if(!$data)
//				{
//					$system_data[$key]['value'] = serialize($this->_goods_typeid_31_att);
//					$type_id_att_31 = $this->_goods_typeid_31_att;
//				}
//				else
//				{
//					if($system_data[$key]['value'])
//					{
//						$type_id_att_31 = unserialize($system_data[$key]['value']);					
//					}
//					else
//					{
//						$type_id_att_31 = $this->_goods_typeid_31_att;
//					}
//				}
//				foreach((array)$type_id_att_31 as $val_att)
//				{
//					$typeid_31_att[md5($val_att['key'])] = $val_att;
//				}
//				$system_data[$key]['format_data'] = $typeid_31_att;
//			}
//			elseif(in_array($val['id'],array(314,315,316,444,445,446)))
			if(in_array($val['id'],array(314,315,316,444,445,446)))
			{
				$goods_typeid_att = $task_type_attribute_obj->get_property_info($val['id']);
				$goods_typeid_att_detail = unserialize($goods_typeid_att['detail_data']);
				
				$typeid_att = array();
				if(!$data)
				{
					$system_data[$key]['value'] = $goods_typeid_att['detail_data'];
					$type_id_att_arr = $goods_typeid_att_detail;
				}
				else
				{
					if($system_data[$key]['value'])
					{
						$type_id_att_arr = unserialize($system_data[$key]['value']);					
					}
					else
					{
						$type_id_att_arr = $goods_typeid_att_detail;
					}
				}
				foreach((array)$type_id_att_arr as $val_att)
				{
					$typeid_att[md5($val_att['key'])] = $val_att;
				}
				$system_data[$key]['format_data'] = $typeid_att;
			}
			//
		}
		////////////自定义
		$task_type_obj = POCO::singleton('pai_mall_goods_type_class');		
		if($data)
		{
			$goods_stock_type_id = $data['stock_type'];
		}
		else
		{
			$type_data = $task_type_obj->get_type_info($type_id);
			$goods_stock_type_id = $type_data['stock_type'];
		}
		if(in_array($goods_stock_type_id,array(4,5,7)))
		{
			if(in_array($goods_stock_type_id,array(5,7)))
			{
				$goods_prices_list = array();
				$goods_prices_type = array();
				if($data['prices_de'])
				{
					$goods_over_status = 1;
					foreach($data['prices_de'] as $key => $val)
					{
						$data['prices_de'][$key]['value'] = $val['prices'];
						$data['prices_de'][$key]['num'] = $val['buy_num'];
						$data['prices_de'][$key]['key'] = $val['type_id'];
						$data['prices_de'][$key]['id'] = $val['type_id'];
						$data['prices_de'][$key]['name_val'] = $val['name'];
						$data['prices_de'][$key]['status'] = $val['time_e']<time()?2:1;
						$val['time_e']>time()?$goods_over_status = 0:'';
						if(in_array($goods_stock_type_id,array(7)))
						{
							$stock_prices_list = unserialize($val['prices_list']);
							if($stock_prices_list)
							{
								$stock_prices_list_format = array();
								foreach($stock_prices_list as $key_de => $val_de)
								{
									$goods_prices_type[] = $key_de;
									$stock_prices_list_format[] = array('id'=>$key_de)+$val_de;
								}
							}							
							$data['prices_de'][$key]['prices_list_data'] = $stock_prices_list_format;
						}
						else
						{
							$goods_prices_type[] = $val['type_id'];
						}
						//$val['key'] = $val['type_id'];
						$goods_prices_list[] = $data['prices_de'][$key];
					}
				}
				$prices_data = $data['prices_de'];
				$prices_data_list = $data['prices_de'];
				foreach((array)$data['detail'] as $val)
				{
					if($val['detail_type'] == 3)
					{
						$val['data'] = unserialize($val['data']);
						$combination_data[] = $val;
					}
					elseif($val['detail_type'] == 4)
					{
						$val['data'] = unserialize($val['data']);
						$contact_data[] = $val;
					}
				}
			}
		}
		////////////
		//$return_data['service_data'] = $attribute;
		//$data['content'] = str_replace(array('&lt;','&gt;'),array('<','>'),$data['content']);//在线编辑器

        if($data['review_times'])
        {
            $score = sprintf('%.1f', ceil($data['total_overall_score'] / $data['review_times'] * 2) / 2);
        }
        else
        {
            $score = 5;
        }

        $data['average_score'] = $score;
		$data['goods_over_status'] = $goods_over_status;//1全部结束,0未结束

		$return_data['goods_data'] = $data;

		$return_data['default_data'] = $new_default_data;
		$return_data['system_data'] = $system_data;
		$return_data['diy_data'] = $diy_data;
		$return_data['combination_data'] = $combination_data;
		$return_data['contact_data'] = $contact_data;
		$return_data['prices_data'] = $prices_data;
		$return_data['prices_data_list'] = $prices_data_list;
		$return_data['goods_prices_list'] = $goods_prices_list;
		$return_data['goods_prices_type'] = $goods_prices_type;
		$return_data['goods_att'] = $goods_att;
		$return_data['type_stock_type'] = $attribute['type_data']['stock_type'];
		$img=array();
		
		foreach((array)$data['img'] as $val)
		{
			//$img[] = $val['img_url'];
			$img[] = array(
			               'img_url'=>$val['img_url'],
						   );
		}
		$return_data['image_data'] = array(
										   'name'  =>'图片',
										   'key'   =>'img_url',
										   'input'  =>2,
										  // 'value'  =>implode("\r\n",$img),
										   'value'  =>$img,
										   );
		return $return_data;
	}
	
	/**
	 * 获取商品价格列表
	 * @param array $prices_data
	 * @return array
	 */
	public function get_goods_prices_list_data($prices_data)
	{
		$prices_data_list = array();
		$prices_count = count($prices_data);
		$id=array();
		reset($prices_data);
		$start_num = 1;
		$p_data = current($prices_data);
		foreach($p_data['child_data'] as $val)
		{
			$id[$val['id']] = array(
									'id'=>$val['id'],
									'num'=>1,
									'type_id'=>array($val['id']),
									'name'=>$p_data['name'].":".$val['name'],
									'name_type'=>$p_data['name'],
									'name_val'=>$val['name'],
									);
		}
		if($prices_count == 1)
		{
			$prices_data_list = array_values($id);
			foreach($prices_data_list as $key => $val)
			{
				$type_id_array = $val['type_id'];
				sort($type_id_array);
				$val['key'] = implode(',',$type_id_array);
				$prices_data_list[$key] = $val;
			}
			return $prices_data_list;
		}
		$i = 1;
		while(next($prices_data))
		{
			$i++;
			$p_data = current($prices_data);					
			foreach($id as $key => $val)
			{						
				foreach($p_data['child_data'] as $val_de)
				{
					$id[$key.",".$val_de['id']] =  array(
														'num'=>$id[$key]['num']+1,
														'type_id'=>array_merge($id[$key]['type_id'],array($val_de['id'])),
														'name'=>$id[$key]['name']." + ".$p_data['name'].":".$val_de['name'],
														);							
					if($i == $prices_count and $id[$key.",".$val_de['id']]['num'] == $prices_count)
					{
						$prices_data_list[] = array(
													'id' => $key.",".$val_de['id'],
													'type_id' => $id[$key.",".$val_de['id']]['type_id'],
													'num' => $id[$key.",".$val_de['id']]['num'],
													'name' => $id[$key.",".$val_de['id']]['name'],
													);
					}
				}
			}
		}
		foreach($prices_data_list as $key => $val)
		{
			$type_id_array = $val['type_id'];
			sort($type_id_array);
			$val['key'] = implode(',',$type_id_array);
			$prices_data_list[$key] = $val;
		}
		return $prices_data_list;
	}
	
	/**
	 * 修改商品
	 * @param int $goods_id
	 * @param array $data
	 * @return array
	 */
	public function update_goods_for_42($goods_id,$data,$user_id=0)
	{
        $return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		$goods_id = (int)$goods_id;
		$goods_info = $this->get_goods_info($goods_id);
		if(!$goods_info)
		{
			return $return;
		}
		////////////////////
		$detail = array();
		$detail_list = '';
		if($data['system_data'])
		{
			$type_md5_array = array();
			foreach($data['system_data'] as $key => $val)
			{
				if(is_array($val))
				{
					foreach($val as $val_de)
					{
						$detail[] = array(
										'detail_type'=>1,
										'name'=>$key,
										'data'=>$val_de,
										);
						$type_md5_array[] = md5($key."|".$val_de);
					}
				}
				else
				{
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>$val,
									);
					$type_md5_array[] = md5($key."|".$val);
				}
			}
			/////////
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
			if($type_md5)
			{
				$detail_list_array = array();
				foreach($type_md5 as $val)
				{
					$detail_list_array[] = $val['id'];
				}
				$detail_list = implode(',',$detail_list_array);
			}
			/////////
		}
		if($data['diy_data'])
		{
			foreach($data['diy_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>2,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//组合
		if($data['combination_data'])
		{
			foreach($data['combination_data'] as $key => $val)
			{
				if($val['images'])
				{
					$images = array();
					foreach($val['images'] as $key_de => $val_de)
					{
						$images[] = array('src'=>$val_de);
					}
				}
				$val['images'] = $images;
				$detail[] = array(
								'detail_type'=>3,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//联系信息
		if($data['contact_data'])
		{
			foreach($data['contact_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>4,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}		
		$img = $data['img'];
		
		$data_sql = array(
						  'location_id' => $data['default_data']['location_id'],
						  'titles' => trim($data['default_data']['titles']),
						  'lng_lat' => trim($data['default_data']['lng_lat']),
						  'detail_list'=>$detail_list,
						  'images' => $img[0]['img_url'],
						  'content' => str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['default_data']['content']),//在线编辑器
						  'edit_status' => 2,
						  'status' => 1,
						  'update_time' => time(),
						  'version' => date('YmdHis').rand(100000,999999),						
						  );
		if($goods_info['goods_data']['user_id'] == 109650)
		{
			//print_r($data);
			//print_r($detail);
			//print_r($data_sql);
			//exit;
		}
		if($user_id == 0)
		{
			$data_sql['is_auto_accept'] = $data['default_data']['is_auto_accept']?(int)$data['default_data']['is_auto_accept']:0;
			$data_sql['is_auto_sign'] = $data['default_data']['is_auto_sign']?(int)$data['default_data']['is_auto_sign']:0;
		}
		
		$this->set_mall_goods_tbl();
		$this->update($data_sql, "goods_id={$goods_id}");
		
		$this->update_goods_detail($goods_id,$detail);
		$this->update_goods_img($goods_id,$img);
		
		/////////////////////////
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
		$this->change_goods_show_num($goods_info['goods_data']['seller_id']);
		$this->add_goods_version($goods_id,TASK_ADMIN_USER_ID);
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	
	/**
	 * 修改商品
	 * @param int $goods_id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function update_goods($goods_id,$data,$user_id=0)
	{
        $return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$goods_info = $this->get_goods_info($goods_id);
		
		if(!$goods_info)
		{
			return $return;
		}
//		if(in_array($goods_info['goods_data']['type_id'],array(5,43)))//参数错误
//		{
//			$return = array(
//							'result'=>-1,
//							'message'=>'暂停品类服务',
//							);
//			return $return;
//		}
		if($user_id and $goods_info['goods_data']['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		////////////////////
		$task_obj = POCO::singleton('pai_mall_seller_class');
		$task_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
		$this->debug?$task_obj->set_db_test():"";//开启调试
		$seller_info = $task_obj->get_seller_info($goods_info['goods_data']['user_id'],2);
		/////////////
		$info = $task_obj->get_store_info($goods_info['goods_data']['store_id']);
		$type_id_array = explode(',',$info[0]['type_id']);
		if($user_id and $goods_info['goods_data']['type_id']!=42 and !in_array($goods_info['goods_data']['type_id'],$type_id_array))//超类型范围操作
		{
			$return = array(
							'result'=>-4,
							'message'=>'没开通该服务类型商品权限',
							);
			return $return;
		}
		/////////////
		if($data['default_data']['location_id'] == '')
		{
			$data['default_data']['location_id'] = $data['system_data']['44f683a84163b3523afe57c2e008bc8c'] == '03afdbd66e7929b125f8597834fa83a4'?0:$seller_info['seller_data']['profile'][0]['location_id'];//地区
		}
		////////////////////
		$detail = array();
		$detail_list = '';
		if($data['system_data'])
		{
			$type_md5_array = array();
			foreach($data['system_data'] as $key => $val)
			{
//				if(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd')))
//				{
//					foreach($this->_goods_typeid_31_att as $key_de => $val_de)
//					{
//						$this->_goods_typeid_31_att[$key_de]['value'] = $val[$val_de['key']];
//					}
//					$detail[] = array(
//									'detail_type'=>1,
//									'name'=>$key,
//									'data'=>serialize($this->_goods_typeid_31_att),
//									);
//				}
//				elseif(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
				if(in_array($key,array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
				{
					if($key === '758874998f5bd0c393da094e1967a72b')
					{
						$goods_att_typeid = 314;						
					}
					elseif($key === 'ad13a2a07ca4b7642959dc0c4c740ab6')
					{
						$goods_att_typeid = 315;						
					}
					elseif($key === '3fe94a002317b5f9259f82690aeea4cd')
					{
						$goods_att_typeid = 316;						
					}
					elseif($key === '550a141f12de6341fba65b0ad0433500')
					{
						$goods_att_typeid = 444;						
					}
					elseif($key === '67f7fb873eaf29526a11a9b7ac33bfac')
					{
						$goods_att_typeid = 445;						
					}
					elseif($key === '1a5b1e4daae265b790965a275b53ae50')
					{
						$goods_att_typeid = 446;						
					}
					$goods_typeid_att = $task_type_attribute_obj->get_property_info($goods_att_typeid);
					$goods_typeid_att_detail = unserialize($goods_typeid_att['detail_data']);
					if($goods_typeid_att_detail)
					{
						foreach($goods_typeid_att_detail as $key_de => $val_de)
						{
							$goods_typeid_att_detail[$key_de]['value'] = $val[$val_de['key']];
						}
					}					
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>serialize($goods_typeid_att_detail),
									);
				}
				elseif(is_array($val))
				{
					foreach($val as $val_de)
					{
						$detail[] = array(
										'detail_type'=>1,
										'name'=>$key,
										'data'=>$val_de,
										);
						$type_md5_array[] = md5($key."|".$val_de);
					}
				}
				else
				{
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>$val,
									);
					$type_md5_array[] = md5($key."|".$val);
				}
			}
			/////////
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
			if($type_md5)
			{
				$detail_list_array = array();
				foreach($type_md5 as $val)
				{
					$detail_list_array[] = $val['id'];
				}
				$detail_list = implode(',',$detail_list_array);
			}
			/////////
		}
		if($data['diy_data'])
		{
			foreach($data['diy_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>2,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//组合
		if($data['combination_data'])
		{
			foreach($data['combination_data'] as $key => $val)
			{
				if($val['images'])
				{
					$images = array();
					foreach($val['images'] as $key_de => $val_de)
					{
						$images[] = array('src'=>$val_de);
					}
				}
				$val['images'] = $images;
				$detail[] = array(
								'detail_type'=>3,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//联系信息
		if($data['contact_data'])
		{
			foreach($data['contact_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>4,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		
		$img = $data['img'];
		/////////////////
		$prices_de = $data['prices_de'];
		$prices_diy = $data['prices_diy'];
		if($prices_diy)
		{
			if(in_array($goods_info['goods_data']['stock_type'],array(4,5,7)))
			{
				$data['default_data']['stock_num']=0;
			}
			foreach($prices_diy as $key => $val)
			{
				if(in_array($goods_info['goods_data']['stock_type'],array(7)))
				{					
					foreach($val['detail']['prices'] as $key_de => $val_de)
					{
						$prices_t_id = strlen($key_de)<14?time().rand(10000,99999):$key_de;
						$prices_diy[$key]['detail']['type_id'][$key_de] = $prices_t_id;
						$prices_de[$prices_t_id] = $val_de;
					}
					$prices_diy[$key]['prices']=min(array_filter($val['detail']['prices']));
				}
				else
				{
					$prices_de[$key] = $val['prices'];
				}
				$data['default_data']['stock_num']+=(int)$val['stock_num'];
				//$prices_de[$key] = $val['prices'];
				//$data['default_data']['stock_num']+=(int)$val['stock_num'];
			}
		}
		$prices_default = sprintf('%.2f',$data['default_data']['prices']);
		if($prices_default <= 0)
		{
			$min_prices_de = $prices_de;
			$p_data = array_filter($min_prices_de)?min(array_filter($min_prices_de)):0;
			$prices_default = sprintf('%.2f',$p_data);
		}
		if($prices_default <= 0)
		{
			$return = array(
							'result'=>-5,
							'message'=>'商品价格不能为零',
							);
			return $return;
		}
		foreach($prices_de as $key => $val)
		{
			$prices = sprintf('%.2f',$val);
			$prices_de[$key] = $prices;
		}
		/////////////////
		$prices_list = serialize($prices_de);
		$data_sql = array(
		                  /*
							'titles' => $data['titles'],
							'prices' => $data['prices'],
							'unit' => $data['unit'],
							//'status' => $goods_info['goods_data']['status']!=3?0:$goods_info['goods_data']['status'],
							'content' => $data['content'],
							'video' => $data['video'],
							'demo' => $data['demo'],
							'prompt' => $data['prompt'],						
							'introduction' => $data['introduction'],
							'add_time' => time(),
							'version' => date('YmdHis').rand(100000,999999),
						  */
						  'stock_num_total' => in_array($goods_info['goods_data']['stock_type'],array(1,4,5))?(int)$data['default_data']['stock_num']:0,
						  'stock_num' => in_array($goods_info['goods_data']['stock_type'],array(1,4,5))?(int)$data['default_data']['stock_num']:0,
						  'brand_id' => $data['default_data']['brand_id'],
						  'location_id' => $data['default_data']['location_id']?$data['default_data']['location_id']:0,
						  'lng_lat' => $data['default_data']['lng_lat'],
						  'titles' => trim($data['default_data']['titles']),
						  'prices' => $prices_default,
						  'unit' => $data['default_data']['unit'],
						  'prices_list'=>$prices_list,
						  'detail_list'=>$detail_list,
						  'images' => $img[0]['img_url'],
						  //'content' => $data['default_data']['content'],
						  'content' => str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['default_data']['content']),//在线编辑器
						  'video' => $data['default_data']['video'],
						  'demo' => $data['default_data']['demo'],
						  'prompt' => $data['default_data']['prompt'],
						  'introduction' => $data['default_data']['introduction'],
						  'status' => 0,
						  //'is_show' => 2,
						  'update_time' => time(),
						  'version' => date('YmdHis').rand(100000,999999),						
						  );
		/*
		////////////////////////////////////临时处理状态,上下架
		if($goods_info['goods_data']['status'] == 2)
		{
			$data_sql['status'] = 0;
		}
		////////////////////////////////////
		*/
		if($user_id == 0)
		{
			$data_sql['is_auto_accept'] = $data['default_data']['is_auto_accept']?(int)$data['default_data']['is_auto_accept']:0;
			$data_sql['is_auto_sign'] = $data['default_data']['is_auto_sign']?(int)$data['default_data']['is_auto_sign']:0;
		}
		if($goods_info['goods_data']['user_id'] == 109650)
		{
			//print_r($data);
			//print_r($detail);
			//print_r($data_sql);
			//print_r($prices_diy);
			//exit;
		}
		if($goods_info['goods_data']['is_show'] == 1)
		{
			$add_version = $this->add_goods_version($goods_id,TASK_ADMIN_USER_ID);
		}

		$this->set_mall_goods_tbl();
		$this->update($data_sql, "goods_id={$goods_id}");
		
		$this->update_goods_detail($goods_id,$detail);
		$this->update_goods_img($goods_id,$img);
		
		$add_user = $user_id?$user_id:$goods_info['goods_data']['user_id'];
		//$this->update_goods_prices($goods_id,$prices_de,$add_user,$goods_info['goods_data']['type_id']);//
		///////////////////////
		//$task_type_obj = POCO::singleton('pai_mall_goods_type_class');
		//$type_data = $task_type_obj->get_type_info($goods_info['goods_data']['type_id']);
		if(in_array($goods_info['goods_data']['stock_type'],array(5,7)))//$type_data['stock_type'] == 5
		{
			$this->update_goods_prices_diy($goods_id,$prices_diy,$add_user,$goods_info['goods_data']['type_id'],$goods_info['goods_data']['stock_type']);
		}
		else
		{
//			if($goods_info['goods_data']['stock_type'] == 4)
//			{
//				$other_data = $data['prices_de_detail'];
//			}
			$other_data = $data['prices_de_detail'];
			$this->update_goods_prices($goods_id,$prices_de,$add_user,$goods_info['goods_data']['type_id'],$other_data);
		}
		/////////////////////////
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
		$this->change_goods_show_num($goods_info['goods_data']['seller_id']);
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	
	
	/**
	 * 修改商品
	 * @param int $goods_id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function update_goods_old($goods_id,$data,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$goods_info = $this->get_goods_info($goods_id);
		
		if(!$goods_info)
		{
			return $return;
		}
		if($user_id and $goods_info['goods_data']['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		////////////////////
		$task_obj = POCO::singleton('pai_mall_seller_class');
		$this->debug?$task_obj->set_db_test():"";//开启调试
		$seller_info = $task_obj->get_seller_info($goods_info['goods_data']['user_id'],2);
		if($data['default_data']['location_id'] == '')
		{
			$data['default_data']['location_id'] = $data['system_data']['44f683a84163b3523afe57c2e008bc8c'] == '03afdbd66e7929b125f8597834fa83a4'?0:$seller_info['seller_data']['profile'][0]['location_id'];//地区
		}
		////////////////////
		$detail = array();
		$detail_list = '';
		if($data['system_data'])
		{
			$type_md5_array = array();
			foreach($data['system_data'] as $key => $val)
			{
				if(is_array($val))
				{
					foreach($val as $val_de)
					{
						$detail[] = array(
										'detail_type'=>1,
										'name'=>$key,
										'data'=>$val_de,
										);
						$type_md5_array[] = md5($key."|".$val_de);
					}
				}
				else
				{
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>$val,
									);
					$type_md5_array[] = md5($key."|".$val);
				}
			}
			/////////
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
			if($type_md5)
			{
				$detail_list_array = array();
				foreach($type_md5 as $val)
				{
					$detail_list_array[] = $val['id'];
				}
				$detail_list = implode(',',$detail_list_array);
			}
			/////////
		}
		if($data['diy_data'])
		{
			foreach($data['diy_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>2,
								'name'=>$key,
								'data'=>$val,
								);
			}
		}
		//组合
		if($data['combination_data'])
		{
			foreach($data['combination_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>3,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//联系信息
		if($data['contact_data'])
		{
			foreach($data['contact_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>4,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		
		$img = $data['img'];
		$prices_de = $data['prices_de'];
		$prices_default = sprintf('%.2f',$data['default_data']['prices']);
		if($prices_default <= 0)
		{
			$min_prices_de = $prices_de;
			$p_data = array_filter($min_prices_de)?min(array_filter($min_prices_de)):0;
			$prices_default = sprintf('%.2f',$p_data);
		}
		if($prices_default <= 0)
		{
			$return = array(
							'result'=>-3,
							'message'=>'商品价格不能为零',
							);
			return $return;
		}
		foreach($prices_de as $key => $val)
		{
			$prices = sprintf('%.2f',$val);
			$prices_de[$key] = $prices;
		}
		$prices_list = serialize($prices_de);
		$data_sql = array(
		                  /*
							'titles' => $data['titles'],
							'prices' => $data['prices'],
							'unit' => $data['unit'],
							//'status' => $goods_info['goods_data']['status']!=3?0:$goods_info['goods_data']['status'],
							'content' => $data['content'],
							'video' => $data['video'],
							'demo' => $data['demo'],
							'prompt' => $data['prompt'],						
							'introduction' => $data['introduction'],
							'add_time' => time(),
							'version' => date('YmdHis').rand(100000,999999),
						  */
						  'stock_num' => $goods_info['goods_data']['stock_type']==1?(int)$data['default_data']['stock_num']:0,
						  'brand_id' => $data['default_data']['brand_id'],
						  'location_id' => $data['default_data']['location_id']?$data['default_data']['location_id']:0,
						  'titles' => $data['default_data']['titles'],
						  'prices' => $prices_default,
						  'unit' => $data['default_data']['unit'],
						  'prices_list'=>$prices_list,
						  'detail_list'=>$detail_list,
						  'images' => $img[0]['img_url'],
						  //'content' => $data['default_data']['content'],
						  'content' => str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data['default_data']['content']),//在线编辑器
						  'video' => $data['default_data']['video'],
						  'demo' => $data['default_data']['demo'],
						  'prompt' => $data['default_data']['prompt'],
						  'introduction' => $data['default_data']['introduction'],
						  'status' => 0,
						  'is_show' => 2,
						  'update_time' => time(),
						  'version' => date('YmdHis').rand(100000,999999),						
						  );
		/*
		////////////////////////////////////临时处理状态,上下架
		if($goods_info['goods_data']['status'] == 2)
		{
			$data_sql['status'] = 0;
		}
		////////////////////////////////////
		*/
		$this->set_mall_goods_tbl();
		$this->update($data_sql, "goods_id={$goods_id}");
		
		$this->update_goods_detail($goods_id,$detail);
		$this->update_goods_img($goods_id,$img);
		
		$add_user = $user_id?$user_id:$goods_info['goods_data']['user_id'];
		$this->update_goods_prices($goods_id,$prices_de,$add_user,$goods_info['goods_data']['type_id']);
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
		$this->change_goods_show_num($goods_info['goods_data']['seller_id']);
		$return = array(
		                'result'=>1,
						'message'=>'修改信息成功',
						);
		return $return;
	}
	
		
	/**
	 * 批量更新商品地区
	 * @param int $seller_id
	 * @param array $old_location_id //旧地区
	 * @param array $new_location_id //新地区id
	 * @return bool
	 */
	public function update_goods_location_id($seller_id,$old_location_id,$new_location_id)
	{
		$seller_id = (int)$seller_id;
		$old_location_id = (int)$old_location_id;
		$new_location_id = (int)$new_location_id;
		if($seller_id and $old_location_id != $new_location_id)
		{
			$this->set_mall_goods_tbl();
			$up_sql = "update {$this->_db_name}.{$this->_tbl_name} set location_id=REPLACE(location_id, '{$old_location_id}', '{$new_location_id}') where seller_id='{$seller_id}' and type_id NOT IN (5,12,41,42,43)";
			$this->query($up_sql);
		}
		return true;
	}
	
		
	/**
	 * 更新统计
	 * @param int $goods_id
	 * @param array $data(pv,uv,bill_buy_num,bill_finish_num,bill_pay_num,prices)
	 * @return bool
	 */
	public function update_goods_statistical($goods_id,$data)
	{
		$goods_id = (int)$goods_id;
		$goods_info = $this->get_goods_info($goods_id);
				
		if(!$goods_info)
		{
			return false;
		}
		
		$pv = (int)$data['pv'];
		$uv = (int)$data['uv'];
		$view_num = (int)$data['view_num'];
		$bill_buy_num = (int)$data['bill_buy_num'];
		$bill_finish_num = (int)$data['bill_finish_num'];
		$bill_pay_num = (int)$data['bill_pay_num'];
		$prices = $data['prices']?sprintf('%.2f',$data['prices']):0;
		$follow_num = (int)$data['follow_num'];
		$step = (int)$data['step'];
		
		$update_sql = array();
		$pv?$update_sql[] = "pv=pv+{$pv}":"";
		$uv?$update_sql[] = "uv=uv+{$uv}":"";
		$view_num?$update_sql[] = "view_num=view_num+{$view_num}":"";
		$bill_buy_num?$update_sql[] = "bill_buy_num=bill_buy_num+{$bill_buy_num}":"";
		$bill_finish_num?$update_sql[] = "bill_finish_num=bill_finish_num+{$bill_finish_num}":"";
		$bill_pay_num?$update_sql[] = "bill_pay_num=bill_pay_num+{$bill_pay_num}":"";
		$prices?$update_sql[] = "prices=prices+{$prices}":"";
		$follow_num?$update_sql[] = "follow_num=follow_num+{$follow_num}":"";	
		$step?$update_sql[] = "step=step+{$step}":"";	
		
		if($update_sql)
		{
			$this->set_mall_goods_statistical_tbl();
			$up_sql = "update {$this->_db_name}.{$this->_tbl_name} set ".implode(',',$update_sql)." where goods_id='{$goods_id}'";
			$this->query($up_sql);
			$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
			$seller_obj = POCO::singleton('pai_mall_seller_class');
			$seller_obj->update_seller_statistical($goods_info['goods_data']['seller_id'],$data,false);
		}				
		return true;
	}
	
		
	/**
	 * 获取价格区间列表
	 * @param int $type_id 品类
	 * @return array
	 */
	public function get_goods_prices_scope_list($type_id)
	{
		$type_id = (int)$type_id;
		$re = array();
		if($type_id)
		{
			$this->set_mall_goods_scope_tbl();
			$where = "type_id={$type_id}";
			$re = $this->findAll($where,'','step,id asc');
		}
		return $re;
	}
	
	
		
	/**
	 * 获取价格区间id
	 * @param int $type_id 品类
	 * @param array $prices
	 * @return int
	 */
	public function get_goods_prices_scope_id($scope_data,$prices)
	{
		if($prices > 0)
		{
			foreach($scope_data as $val)
			{
				if($prices >= $val['min_prices'] and $prices <= $val['max_prices'])
				{
					return $val['id'];
				}
			}
		}
		return 0;
	}
	
		
	/**
	 * 更新价格策略
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function update_goods_prices_diy($goods_id,$data,$add_user,$goods_type_id,$stock_type='')
	{
		$goods_id = (int)$goods_id;
		if($goods_id)
		{
			$this->set_mall_goods_prices_tbl();
			$this->delete("goods_id='{$goods_id}'");			
			$this->add_goods_prices_diy($goods_id,$data,$add_use,$goods_type_id,$stock_type);
		}
		return true;
	}
		
	/**
	 * 更新价格策略
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function update_goods_prices_old2($goods_id,$data,$add_user,$goods_type_id)
	{
		$goods_id = (int)$goods_id;
		$goods_type_id = (int)$goods_type_id;
		if($goods_id and $goods_type_id and $data)
		{
			$this->set_mall_goods_prices_tbl();
			$where = "goods_id={$goods_id}";
			$re = $this->findAll($where);
			$goods_prices = array();
			if($re)
			{
				foreach($re as $val)
				{
					$goods_prices[$val['type_id']] = $val;
				}
			}
			$scope_data = $this->get_goods_prices_scope_list($goods_type_id);
			$this->set_mall_goods_prices_tbl();
			foreach($data as $key => $val)
			{				
				$prices = sprintf('%.2f',$val);
				$scope_id = $this->get_goods_prices_scope_id($scope_data,$prices);
				if($prices >= 0)
				{
					$data_sql = array();
					if($goods_prices[$key])///////更新
					{
						$data_sql = array(
										  'prices' => $prices,
										  'scope_id'=> $scope_id,
										  'add_time' => time(),
										  'add_user' => $add_user,
										  );
						$this->update($data_sql,"prices_id=".$goods_prices[$key]['prices_id']);						
					}
					else///////插入
					{
						
						$data_sql = array(
										  'goods_id' => $goods_id,
										  'scope_id'=>$scope_id,
										  'type_id' => $key,
										  'prices' => $prices,
										  'add_time' => time(),
										  'add_user' => $add_user,
										  );				
						$this->insert($data_sql);					
					}
				}
			}
		}
		return true;
	}
	
		
	/**
	 * 更新价格策略
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function update_goods_prices($goods_id,$data,$add_user,$goods_type_id,$other_data=array())
	{
		$goods_id = (int)$goods_id;
		if($goods_id)
		{
			$this->set_mall_goods_prices_tbl();
			$this->delete("goods_id='{$goods_id}'");			
			$this->add_goods_prices($goods_id,$data,$add_user,$goods_type_id,$other_data);
		}
		return true;
	}
	
	/**
	 * 更新明细
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function update_goods_detail($goods_id,$data)
	{
		$goods_id = (int)$goods_id;
		if($goods_id)
		{
			$this->set_mall_goods_detail_tbl();
			$this->delete("goods_id='{$goods_id}'");
			$this->add_goods_detail($goods_id,$data);
		}
		return true;
	}
	
	/**
	 * 更新明细
	 * @param int $goods_id
	 * @return bool
	 */
	private function set_goods_att_demo($goods_id)
	{
		$goods_id = (int)$goods_id;
		if($goods_id)
		{
			$task_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_all = $task_obj->get_all_type_attribute_by_goods_type_id();
			$type_all_data = array();
			foreach($type_all as $val)
			{
				$type_all_data[$val['id']] = $val;
			}
			$this->set_mall_goods_detail_tbl();
			$goods_att = $this->findAll("goods_id='{$goods_id}'");
			$goods_info = $this->get_goods_info($goods_id);
			$type_name = $this->get_goods_typename_for_type_id($goods_info['goods_data']['type_id']);
			$demo = $type_name;
			if($goods_att)
			{
				foreach($goods_att as $val)
				{
					if($val['data_type_attribute_id'])
					{
						$demo .="|".$type_all_data[$val['data_type_attribute_id']]['name'];
					}
					elseif(in_array($val['type_id'],array(320)))
					{
						$demo .="|".$val['data'];
					}
				}
			}
			$this->set_mall_goods_tbl();
			$this->update(array('demo'=>$demo),"goods_id='{$goods_id}'");
			return true;
		}
		return false;
	}
	
	/**
	 * 更新图片
	 * @param int $goods_id
	 * @param array $data
	 * @return bool
	 */
	private function update_goods_img($goods_id,$data)
	{
		$goods_id = (int)$goods_id;
		if($goods_id)
		{
			$this->set_mall_goods_img_tbl();
			$this->delete("goods_id='{$goods_id}'");
			$this->add_goods_img($goods_id,$data);
		}
		return true;
	}
	
	/**
	 * 修改状态
	 * @param int $goods_id
	 * @param int $status
	 * @return array
	 */
	public function change_goods_status($goods_id,$status,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		$goods_id = (int)$goods_id;
		$status = (int)$status;
		$goods_info = $this->get_goods_info($goods_id);
		if(!$goods_info)
		{
			return $return;
		}
        
		//////临时商家,不能审核
		$task_obj = POCO::singleton('pai_mall_seller_class');
        $this->debug?$task_obj->set_db_test():"";//开启调试
		$seller_info = $task_obj->get_seller_info($goods_info['goods_data']['user_id'],2);
		if($seller_info['seller_data']['status'] == 3 and $seller_info['seller_data']['goods_num'] >= $this->_tempgoods_num)//临时商家限制
		{
			$return = array(
							'result'=>-6,
							'message'=>'临时商家,不能审核',
							);
			return $return;
		}
		else
        {
			$service_obj = POCO::singleton("pai_mall_certificate_service_class");
			$service_status = $service_obj->get_service_open_or_not($goods_info['goods_data']['user_id'],$goods_info['goods_data']['type_id'],false);
			if($goods_info['goods_data']['type_id']!=42 and !$service_status )
			{
				$return = array(
								'result'=>-7,
								'message'=>'服务没开通,不能审核',
				);
				return $return;
			}
        }
        
		//////
		if($user_id and $goods_info['goods_data']['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		if(!in_array($status,array(1,2,3)) or $user_id and !in_array($status,array(0,3)))
		{
			$return = array(
							'result'=>-3,
							'message'=>'参数错误',
							);
			return $return;
		}
		$data_sql = array(
						  'status' => $status,
						  'audit_time' => time(),
						  );		  
		if($status == 1)
		{
			$add_version = $this->add_goods_version($goods_id,TASK_ADMIN_USER_ID);
			if(!$add_version)
			{
				$return = array(
								'result'=>-4,
								'message'=>'无法添加版本库,修改失败',
								);
				return $return;
			}
			$data_sql['is_show']=1;//审核通过,自动上架
			$data_sql['onsale_time'] = time();//审核通过,自动上架
		}
		elseif($status == 2)
		{
			$data_sql['is_show']=2;
			$data_sql['onsale_time'] = time();
		}
		elseif($status == 3)
		{
			$data_sql['is_show']=2;
			$data_sql['onsale_time'] = time();
			$cms_del_obj = POCO::singleton('pai_cms_parse_class');
			$cms_del_obj->del_goods_id($goods_id);
		}
		$this->set_mall_goods_tbl();
		$this->update($data_sql, "goods_id={$goods_id}");
		$this->change_goods_show_num($goods_info['goods_data']['seller_id']);
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
		if($data_sql['is_show'] == 1)
		{
			$this->exec_cmd_pai_mall_follow_user_showtime($goods_info['goods_data']['user_id'],$goods_id);
		}
		$return = array(
		                'result'=>1,
						'message'=>'修改成功',
						);
		return $return;
	}
	
	
	/**
	 * 批量审核不通过商品
	 * @param int $seller_id
	 * @param int $type_id
	 * @param int $status
	 * @return bool
	 */
	public function change_goods_status_by_type_id($seller_id,$type_id,$status=2)
	{
		$seller_id = (int)$seller_id;
		$type_id = (int)$type_id;
		$status = (int)$status;
		if(!$seller_id or !$type_id or !$status)
		{
			return false;
		}
		$where = "seller_id={$seller_id} and type_id={$type_id}";
		$data_sql = array(
						  'onsale_time' => time(),
						  'is_show' => 2,
						  'audit_time' => time(),
						  'status' => $status,
						  );
		$this->set_mall_goods_tbl();
		$this->update($data_sql,$where);
		//////////
		$this->change_goods_show_num($seller_id);
		//////////
		return true;
	}
	
	
	/**
	 * 批量下架商店商品
	 * @param int $goods_id
	 * @param int $status
	 * @return array
	 */
	public function change_goods_show_status_by_store_id($store_id,$type_id='')
	{
		$store_id = (int)$store_id;
		if(!$store_id)
		{
			return false;
		}
		$where = "store_id={$store_id}";
		if($type_id)
		{
			$type_id_array = explode(',',$type_id);
			foreach($type_id_array as $key => $val)
			{
				if(!(int)$val)
				{
					unset($type_id_array);
				}
			}
			$type_id_str = implode(',',$type_id_array);
			if($type_id_str)
			{
				$where .= " AND type_id in ({$type_id_str})";
			} 
		}
		$data_sql = array(
						  'is_show' => 2,
						  'onsale_time' => time(),
						  );
		$this->set_mall_goods_tbl();
		$this->update($data_sql,$where);
		//////////
		$goods_data = $this->find("store_id='{$store_id}'");
		$this->change_goods_show_num($goods_data['seller_id']);
		//////////
		return true;
	}
	
	
	/**
	 * 商品上下架
	 * @param int $goods_id
	 * @param int $status
	 * @return array
	 */
	public function change_goods_show_status($goods_id,$status,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		$goods_id = (int)$goods_id;
		$status = (int)$status;
		$goods_info = $this->get_goods_info_by_sql($goods_id);
		if(!$goods_info)
		{
			return $return;
		}
		//////临时商家,不能审核
		$task_obj = POCO::singleton('pai_mall_seller_class');
		$this->debug?$task_obj->set_db_test():"";//开启调试
		$seller_info = $task_obj->get_seller_info($goods_info['goods_data']['user_id'],2);
		if($seller_info['seller_data']['status'] == 3 and $status==1)//临时商家限制
		{
			$return = array(
							'result'=>-9,
							'message'=>'临时商家,不能操作',
							);
			return $return;
		}
		//////
		if($user_id and $goods_info['goods_data']['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
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
		if($status == 1)//上架条件判断
		{
			//商品是否通过审核
			//if($goods_info['goods_data']['status'] != 1)
			if(!in_array($goods_info['goods_data']['status'],array(0,1)))
			{
				$return = array(
								'result'=>-4,
								'message'=>'审核未通过,不允许上架',
								);
				return $return;
			}
			//店铺是否有效
			$task_seller_obj = POCO::singleton('pai_mall_seller_class');
			$this->debug?$task_seller_obj->set_db_test():"";//开启调试
			$stroe_info = $task_seller_obj->get_store_info($goods_info['goods_data']['store_id']);
			if($stroe_info[0]['status'] != 1)
			{
				$return = array(
								'result'=>-5,
								'message'=>'店铺已关闭,不允许上架',
								);
				return $return;
			}
			//是否需要有库存
			if($goods_info['goods_data']['stock_type'] == 1 and $goods_info['goods_data']['stock_num'] <= 0)
			{
				$return = array(
								'result'=>-6,
								'message'=>'没库存了,不允许上架',
								);
				return $return;
			}
			elseif(in_array($goods_info['goods_data']['stock_type'],array(4,5)))
			{
				$is_stock_error = true;
				if($goods_info['goods_data']['prices_de'])
				{
					foreach($goods_info['goods_data']['prices_de'] as $val)
					{
						if($val['stock_num']>0 and $val['prices']>0)
						{
							$is_stock_error = false;
							break;
						}
					}
				}
				//if($is_stock_error)
				if(false)
				{
					$return = array(
									'result'=>-8,
									'message'=>'库存错误,不允许上架',
									);
					return $return;
				}
			}
			//是否需要有价格策略
			if($goods_info['prices_data'] and !$goods_info['prices_data_list'])
			{
				$return = array(
								'result'=>-7,
								'message'=>'没有添加价格策略,不允许上架',
								);
				return $return;
			}
		}
		$data_sql = array(
						  'is_show' => $status,
						  'onsale_time' => time(),
						  );
		$this->set_mall_goods_tbl();
		$this->update($data_sql, "goods_id={$goods_id}");
		$this->change_goods_show_num($goods_info['goods_data']['seller_id']);
		if($status == 1)
		{
			$this->exec_cmd_pai_mall_follow_user_showtime($goods_info['goods_data']['user_id'],$goods_id);
		}
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
		$return = array(
		                'result'=>1,
						'message'=>'修改成功',
						);
		return $return;
	}
	
	
	/**
	 * 修改商家已上架商品数量
	 * @param int $goods_id
	 * @return array
	 */
	public function change_goods_show_num($seller_id)
	{
		$seller_id = (int)$seller_id;
		$return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		if($seller_id)
		{
			$this->set_mall_goods_tbl();
			$where_str = "is_show=1 and status!=3 and seller_id='{$seller_id}'";
			$ret = $this->findCount ($where_str);
			$seller_obj = POCO::singleton('pai_mall_seller_class');
			$data = array(
			              'onsale_num'=>$ret,
						  );
			$return = $seller_obj->update_seller_statistical($seller_id,$data,false);
		}
		return $return;
	}	
	
	
	/**
	 * 根据ID获取商品版本信息
	 * @param int $goods_version_id
	 * @return array
	 */
	public function get_goods_version_by_id($goods_version_id)
	{
		$goods_version_id = (int)$goods_version_id;
		$re = array();
		if($goods_version_id)
		{
			$this->set_mall_goods_version_tbl();
			$where_str = "goods_version_id='{$goods_version_id}'";
			$re = $this->find($where_str);
		}
		return $re;
	}
	
	/**
	 * 根据版本获取商品版本信息
	 * @param int $goods_id
	 * @param int $version
	 * @return array
	 */
	public function get_goods_version_by_version($goods_id,$version)
	{
		$re = array();
		$goods_id = (int)$goods_id;
		if(preg_match('/^\d{20}$/',$version) and $goods_id)
		{
			$this->set_mall_goods_version_tbl();
			$where_str = "goods_id='{$goods_id}' AND version='{$version}'";
			$re = $this->find($where_str);
		}
		return $re;
	}
	
	/**
	 * 添加商品版本信息-新版本号
	 * @param int $goods_id
	 * @param array $user_id
	 * @return bool
	 */
	private function add_goods_version_for_new($goods_id,$user_id=0)
	{
		$goods_id = (int)$goods_id;
		$re = false;
		if($goods_id)
		{
			$data_sql = array(
							  'update_time' => time(),
							  'version' => date('YmdHis').rand(100000,999999),						
							  );		
			$this->set_mall_goods_tbl();
			$this->update($data_sql, "goods_id={$goods_id}");
		}
		$re = $this->add_goods_version($goods_id,$user_id);
		return $re;
	}
	
	/**
	 * 添加商品版本信息
	 * @param int $goods_id
	 * @param array $user_id
	 * @return bool
	 */
	private function add_goods_version($goods_id,$user_id=0)
	{
		$goods_id = (int)$goods_id;
		if(!$goods_id)
		{
			return false;
		}
		$goods_info = $this->get_goods_info_by_sql($goods_id);
		if(!$goods_info)
		{
			return false;
		}
		$this->set_mall_goods_version_tbl();
		$data_sql = array(
		                  'goods_id'=>$goods_id,
						  'goods_info'=>serialize($goods_info),
						  'version'=>$goods_info['goods_data']['version'],
						  'add_time'=>time(),
						  'add_user'=>$user_id,
						  );
		$this->insert($data_sql);		
		return true;
	}
	
	
	/**
	 * 获取商品版本列表
	 * @param bool $b_select_count
	 * @param string $where_arr(goods_id,start_time,end_time)
	 * @param string $order_by
	 * @param string $limit 
	 * @param string $fields 
	 * @return array
	 */
	public function get_goods_version_list($b_select_count = false, $where_arr = array(), $order_by = 'goods_version_id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_mall_goods_version_tbl();
		$where_str = '';
		if($where_arr)
		{
			$goods_id = (int)$where_arr['goods_id'];
			$start_time = (int)strtotime($where_arr['start_time']);
			$end_time = (int)strtotime($where_arr['end_time']);
			
			$where_str .= $goods_id?" AND goods_id = '{$goods_id}'":"";
			$where_str .= $start_time?" AND add_time >= '{$start_time}'":"";
			$where_str .= $end_time?" AND add_time <= '{$end_time}'":"";
		}
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
	 * 修改商品库存
	 * @param int $goods_id
	 * @param int $nun 购买数量 负数减库存
	 * @param int $type_id 库存类型id
	 * @param int $activity_id prices明细id
	 * @return bool
	 */
	public function change_goods_stock($goods_id,$num,$type_id='',$activity_id='')
	{
		$goods_id = (int)$goods_id;
		$num = (int)$num;		
		if(!$goods_id and $num)
		{
			return -2;
		}
		$goods_info = $this->get_goods_info($goods_id);
		//print_r($goods_info);
		if(!$goods_info)
		{
			return -3;
		}
		if($goods_info['goods_data']['stock_type'] == 1)
		{
			$this->set_mall_goods_tbl();
			//$re = $this->update("stock_num=stock_num+{$num}", "goods_id={$goods_id}");
			$up_sql = "update {$this->_db_name}.{$this->_tbl_name} set stock_num=stock_num+{$num} where goods_id='{$goods_id}'";
			$this->query($up_sql);
			pai_log_class::add_log($up_sql, 'stock_detail', 'goods_stock');
		}
		elseif(in_array($goods_info['goods_data']['stock_type'],array(4,5,7)))
		{
			$this->set_mall_goods_prices_tbl();
			$p_detail = $this->find("goods_id='{$goods_id}' and type_id='".pai_mall_change_str_in($type_id)."'");
			if(!$p_detail)
			{
				pai_log_class::add_log($goods_id."|".$num."|".$type_id."|".$activity_id, 'bad_stock_detail', 'goods_stock');
				return -4;
			}
			$up_sql = "update {$this->_db_name}.{$this->_tbl_name} set stock_num=stock_num+{$num} where goods_id='{$goods_id}' and type_id='".pai_mall_change_str_in($type_id)."'";
			$this->query($up_sql);
			pai_log_class::add_log($up_sql, 'stock_detail', 'goods_stock');
			
			$this->set_mall_goods_tbl();
			$up_g_sql = "update {$this->_db_name}.{$this->_tbl_name} set stock_num=stock_num+{$num} where goods_id='{$goods_id}'";
			$this->query($up_g_sql);
			pai_log_class::add_log($up_g_sql, 'stock', 'goods_stock');
		}
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_data['goods_data']['type_id'],2);
		return 1;
	}
	
	
	/**
	 * 减少商品库存
	 * @param int $goods_id
	 * @param int $nun购买数量
	 * @return bool
	 */
	private function reduction_goods_stock($goods_id,$num)
	{
		$this->set_mall_goods_tbl();
		$up_sql = "update {$this->_db_name}.{$this->_tbl_name} set stock_num=stock_num-{$num} where goods_id='{$goods_id}'";
		$this->query($up_sql);
		$goods_data = $this->get_goods_info($goods_id);
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_data['goods_data']['type_id'],2);
		return true;
	}
	
	/**
	 * 减少商品库存
	 * @param int $goods_id
	 * @param int $type_id prices类型
	 * @param int $nun购买数量
	 * @return bool
	 */
	private function reduction_goods_stock_for_prices($goods_id,$type_id,$num)
	{
		$this->set_mall_goods_prices_tbl();
		$where = "stock_num>='{$num}' and goods_id='{$goods_id}' and type_id='".pai_mall_change_str_in($type_id)."'";
		$re = $this->find($where);
		if(!$re)
		{
			return false;
		}
		$up_sql = "update {$this->_db_name}.{$this->_tbl_name} set stock_num=stock_num-{$num} where goods_id='{$goods_id}' and type_id='".pai_mall_change_str_in($type_id)."'";
		$this->query($up_sql);
		$this->reduction_goods_stock($goods_id,$num);
		return true;
	}
	
	/**
	 * 获取价格
	 * @param int $goods_id
	 * @param array $data(num,type_id,activity_id) nun购买数量,type_id购买对应属性值,activity_id场次id
	 * @return int
	 */
	public function get_goods_prices($goods_id,$data)
	{
		$goods_id = (int)$goods_id;
		$activity_id = $data['activity_id'];
		if(!$goods_id)
		{
			$return = array(
							'result'=>-1,
							'message'=>'参数错误',
							);
			return $return;
		}
		$goods_info = $this->get_goods_info($goods_id);
		if(!$goods_info)
		{
			$return = array(
							'result'=>-2,
							'message'=>'没有该商品',
							);
			return $return;
		}
		$type_id = '';
		$prices = $goods_info['goods_data']['prices'];
		//if($goods_info['prices_data'] and in_array($goods_info['goods_data']['stock_type'],array(3,4,5,6,7)))
		//if($goods_info['prices_data'])//
		if($goods_info['prices_data'] and $goods_info['goods_data']['goods_id']!=2131541)
		{
			$type_id = $data['type_id'];
			if(!in_array($type_id,$goods_info['goods_prices_type']))
			{
				$return = array(
								'result'=>-9,
								'message'=>'没有该属性商品',
								);
				return $return;
			}
			if(in_array($goods_info['goods_data']['stock_type'],array(7)))
			{
//				foreach($goods_info['goods_prices_list'] as $val)
//				{
//					foreach($val['prices_list_data'] as $val_de)
//					{
//						if($val_de['id'] == $type_id)
//						{
//							$prices = $val_de['prices']; 
//							break;
//						}
//					}
//				}
				//
				$prices_type_id = '';
				foreach($goods_info['goods_prices_list'] as $val)
				{
					if($val['type_id'] == $activity_id)
					{
						foreach($val['prices_list_data'] as $val_de)
						{
							if($val_de['id'] == $type_id)
							{
								$prices = $val_de['prices']; 
								$prices_type_id = $val['type_id'];
								break;
							}
						}
						break;
					}
				}
				if($prices_type_id == '')
				{
					$return = array(
									'result'=>-9,
									'message'=>'活动参数错误,请刷新页面重试',
									);
					return $return;
				}
				//
			}
			else
			{
				foreach($goods_info['goods_prices_list'] as $val)
				{
					if($val['key'] == $type_id)
					{
						$prices = $val['value']; 
						break;
					}
				}
			}
/*			if(in_array($goods_info['goods_data']['stock_type'],array(7)))
			{
				foreach($goods_info['goods_prices_list'] as $val)
				{
					$stock_prices_list = unserialize($val['prices_list']);
					$prices = $stock_prices_list[$type_id]['prices']; 
				}
			}
			else
			{
				foreach($goods_info['goods_prices_list'] as $val)
				{
					if($val['key'] == $type_id)
					{
						$prices = $val['value']; 
						break;
					}
				}
			}
*/
		}
		/*
		$prices = $this->goods_prices_strategy($goods_id,$prices,$data);//促销价格
		*/
		$return = array(
						'result'=>1,
						'data'=>array(
						              'goods_id' => $goods_id,
						              'type_id' => $type_id,
						              'activity_id' => $data['activity_id'],
						              'prices' => $prices,
									  ),
						);
		return $return;
	}
	
	
	/**
	 * 检查是否能购买
	 * @param int $goods_id
	 * @param array $data(num,type_id,activity_id) nun购买数量,type_id购买对应属性值,activity_id场次id
	 * @return int
	 */
	public function check_can_buy($goods_id,$data)
	{
		$goods_id = (int)$goods_id;
		$activity_id = $data['activity_id'];
		if(!$goods_id)
		{
			$return = array(
							'result'=>-1,
							'message'=>'参数错误',
							);
			return $return;
		}
		$goods_info = $this->get_goods_info_by_sql($goods_id);
		if(!$goods_info)
		{
			$return = array(
							'result'=>-2,
							'message'=>'没有该商品',
							);
			return $return;
		}
		//商品是否通过上架
		//if($goods_info['goods_data']['is_show'] != 1)
		if($goods_info['goods_data']['is_show'] != 1 and $goods_info['goods_data']['type_id'] != 42)
		{
			$return = array(
							'result'=>-3,
							'message'=>'商品未上架',
							);
			return $return;
		}
		//店铺是否有效
		$task_seller_obj = POCO::singleton('pai_mall_seller_class');
		$this->debug?$task_seller_obj->set_db_test():"";
		$stroe_info = $task_seller_obj->get_store_info($goods_info['goods_data']['store_id']);
		if($stroe_info[0]['status'] != 1)
		{
			$return = array(
							'result'=>-4,
							'message'=>'店铺已关闭',
							);
			return $return;
		}
		//是否需要有库存
		$num = 1;
		if($goods_info['goods_data']['type_id'] == 5)//培训报名截止时间
		{
			if($goods_info['goods_att'][403] and strtotime($goods_info['goods_att'][403])<time())
			{
				$return = array(
								'result'=>-12,
								'message'=>'报名已截止,不能报名',
								);
				return $return;
			}
		}
		if($goods_info['goods_data']['stock_type'] == 1)
		{
			$num = (int)$data['num'];
			if(!$num)
			{
				$return = array(
								'result'=>-10,
								'message'=>'你所选择的购买数量有误，请重新选择',
								);
				return $return;
			}
			if($goods_info['goods_data']['stock_num'] <= 0)//无库存
			{
				$return = array(
								'result'=>-5,
								'message'=>'你所购买的服务已售完',
								);
				return $return;
			}
			if($num > $goods_info['goods_data']['stock_num'])//购买数量大于库存量
			{
				$return = array(
								'result'=>-6,
								'message'=>'你所购买的数量已超过剩余库存，请重新选择',
								);
				return $return;
			}
			if($goods_info['goods_data']['buy_num'] != 0 and $num>$goods_info['goods_data']['buy_num'])
			{
				$return = array(
								'result'=>-7,
								'message'=>'最多只能购买'.$goods_info['goods_data']['buy_num'].'件该商品',
								);
				return $return;
			}
			$re = $this->reduction_goods_stock($goods_id,$num);
			if(!$re)
			{
				$return = array(
								'result'=>-8,
								'message'=>'你所购买的服务，库存数存在问题',
								);
				return $return;
			}
		}
        //是否需要有价格策略
		$type_id = '';
		$prices = $goods_info['goods_data']['prices'];
		//if($goods_info['prices_data'] and in_array($goods_info['goods_data']['stock_type'],array(3,4,5,6,7)))
		//if($goods_info['prices_data'])
		if($goods_info['prices_data'] and $goods_info['goods_data']['goods_id']!=2131541)
		{
			$type_id = $data['type_id'];
			if(!in_array($type_id,$goods_info['goods_prices_type']))
			{
				$return = array(
								'result'=>-9,
								'message'=>'没有该属性商品',
								);
				return $return;
			}
			if(in_array($goods_info['goods_data']['stock_type'],array(7)))
			{
				$prices_type_id = '';
				foreach($goods_info['goods_prices_list'] as $val)
				{
					if($val['type_id'] == $activity_id)
					{
						foreach($val['prices_list_data'] as $val_de)
						{
							if($val_de['id'] == $type_id)
							{
								$prices = $val_de['prices']; 
								$prices_data = $val; 
								$prices_type_id = $val['type_id'];
								break;
							}
						}
						break;
					}
				}
				if($prices_type_id == '')
				{
					$return = array(
									'result'=>-9,
									'message'=>'活动参数错误,请刷新页面重试',
									);
					return $return;
				}
			}
			else
			{
				foreach($goods_info['goods_prices_list'] as $val)
				{
					if($val['key'] == $type_id)
					{
						$prices = $val['value']; 
						$prices_data = $val; 
						break;
					}
				}
			}
/*			foreach($goods_info['goods_prices_list'] as $val)
			{
				if($val['key'] == $data['type_id'])
				{
					$prices = $val['value']; 
					$prices_data = $val; 
					break;
				}
			}
*/			
            /////////////
			if(in_array($goods_info['goods_data']['stock_type'],array(4,5,7)))
			{
				$num = (int)$data['num'];
				if($num<=0)
				{
					$return = array(
									'result'=>-10,
									'message'=>'你所选择的购买数量有误，请重新选择',
									);
					return $return;
				}
				if((int)$prices_data['stock_num'] <= 0)//无库存
				{
					$return = array(
									'result'=>-5,
									'message'=>'你所购买的服务已售完',
									);
					return $return;
				}
				if($num > $prices_data['stock_num'])//购买数量大于库存量
				{
					$return = array(
									'result'=>-6,
									'message'=>'你所购买的数量已超过剩余库存，请重新选择',
									);
					return $return;
				}
				if($prices_data['buy_num'] != 0 and $num>$prices_data['buy_num'])
				{
					$return = array(
									'result'=>-7,
									'message'=>'最多只能购买'.$prices_data['buy_num'].'件该商品',
									);
					return $return;
				}
//				if($prices_data['time_e'] and $prices_data['time_e']<time())
//				{
//					$return = array(
//									'result'=>-11,
//									'message'=>'购买时间截止,不允许购买',
//									);
//					return $return;
//				}
				if(in_array($goods_info['goods_data']['stock_type'],array(7)))
				{
					$re = $this->reduction_goods_stock_for_prices($goods_id,$prices_type_id,$num);
				}
				else
				{
					$re = $this->reduction_goods_stock_for_prices($goods_id,$type_id,$num);
				}
				if(!$re)
				{
					$return = array(
									'result'=>-8,
									'message'=>'你所购买的服务，库存数存在问题',
									);
					return $return;
				}
			}
			////////////
		}
		/*
		$prices = $this->goods_prices_strategy($goods_id,$prices,$data);//促销价格
		*/
		$return = array(
						'result'=>1,
						'message'=>'定购成功',
						'data'=>array(
						              'goods_id' => $goods_id,
						              'num' => $num?$num:1,
						              'type_id' => $type_id,
									  'activity_id' => $data['activity_id'],
						              'prices' => $prices,
									  ),
						);
		return $return;
	}


    /*
     * 添加活动到审核表
     * @param int $goods_id
     * @param array $data
     */
    public function add_mall_goods_check($goods_id,$data)
    {
        $goods_id = (int)$goods_id;
        if(empty($goods_id) || empty($data))
        {
            return false;
        }

        //更改状态为未审核
        $this->update_goods_edit_status($goods_id,1);

        $insert_data['goods_id'] = $goods_id;
        $insert_data['data'] = serialize($data);
        $insert_data['update_time'] = time();

        $this->set_mall_goods_check_tbl();
        $this->insert($insert_data,'REPLACE');

        return true;
    }


    /*
     * 更新活动审核状态
     * @param int $goods_id
     * @param int $status  1未审核 2.审核通过 3.未通过
     */
    public function update_goods_edit_status($goods_id,$status)
    {
        $goods_id = (int)$goods_id;
        if(empty($goods_id) || !in_array($status,array(1,2,3)))
        {
            $result['result'] = -1;
            $result['message'] = "参数错误";
            return $result;
        }

        $goods_info = $this->get_goods_info_by_sql($goods_id);
        if(in_array($status,array(2,3)) and !in_array($goods_info['goods_data']['edit_status'],array(0,1)))
        {
            $result['result'] = -2;
            $result['message'] = "活动已被审核";
            return $result;
        }

        if($status==2)
        {
            //更新审核表内容到主表
			$data = $this->get_mall_goods_check($goods_id);
			$re = $this->update_goods_for_42($goods_id,$data);
        }

        $this->set_mall_goods_tbl();
        $update_data['edit_status'] = $status;
        $this->update($update_data, "goods_id={$goods_id}");

        $result['result'] = 1;
        $result['message'] = "审核成功";
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
        return $result;

    }


    /*
     * 获取活动审核内容
     * @param int $goods_id
     */
    public function get_mall_goods_check($goods_id)
    {
        $goods_id = (int)$goods_id;

        $this->set_mall_goods_check_tbl();
        $ret = $this->find("goods_id={$goods_id}");

        return unserialize($ret['data']);
    }


    /*
     * 添加/编辑活动回顾
     * @param int $goods_id
     * @param int $user_id 发布人ID
     * @param string $content
     */
    public function add_activity_review($goods_id,$user_id,$content)
    {
        $goods_id = (int)$goods_id;
        $user_id = (int)$user_id;

        $type_id_arr = array(42);

        if(empty($goods_id) || empty($user_id) || empty($content))
        {
            $result['result'] = -1;
            $result['message'] = "参数错误";
            return $result;
        }

        $goods_info = $this->get_goods_info($goods_id);

        if($goods_info['goods_data']['user_id']!=$user_id)
        {
            $result['result'] = -1;
            $result['message'] = "你不是该活动的组织者";
            return $result;
        }

        $activity_info = POCO::singleton('pai_mall_api_class')->get_goods_id_activity_info($goods_id);
        if($activity_info['is_have_end']!=1)
        {
            $result['result'] = -1;
            $result['message'] = "活动还没结束，请稍后再发布";
            return $result;
        }


        if(!in_array($goods_info['goods_data']['type_id'],$type_id_arr))
        {
            $result['result'] = -1;
            $result['message'] = "活动类型错误";
            return $result;
        }

        $this->set_mall_activity_review_tbl();


        $insert_data['goods_id'] = $goods_id;
        $insert_data['content'] = $content;
        $insert_data['add_time'] = time();

        $this->insert($insert_data,'REPLACE');

        $result['result'] = 1;
        $result['message'] = "发布成功";
        return $result;

    }


    /*
     * 获取活动回顾
     * @param int $goods_id
     */
    public function get_activity_review($goods_id)
    {
        $goods_id = (int)$goods_id;

        if(empty($goods_id))
        {
            return false;
        }

        $this->set_mall_activity_review_tbl();

        return $this->find("goods_id={$goods_id}");
    }
    
    /**
     * 更新商品价钱的数据
     * @param type $goods_id
     * @return boolean
     */
    public function update_goods_prices_data($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $this->set_mall_goods_prices_tbl();
        $org_data = $this->findAll("goods_id='$goods_id'");
        
        if( ! empty($org_data) )
        {
            $price_ary = $price_list_ary = array();
            $sum_stock_num = $sum_stock_num_total = '';
            foreach($org_data as $k => $v)
            {
                $unit_price_list = array();
                $price_ary[] = $v['prices'];
                $sum_stock_num += $v['stock_num'];
                $sum_stock_num_total+= $v['stock_num_total'];
                if( ! empty($v['prices_list']) )
                {
                    $unit_price_list = unserialize($v['prices_list']);
                    
                    if( ! empty($unit_price_list) )
                    {
                        foreach($unit_price_list as $kp => $vp)
                        {
                            $price_list_ary[$kp] = $vp['prices'];
                        }
                    }
                    
                }else
                {
                    if( ! empty($v['type_id']))
                    {
                         $price_list_ary[$v['type_id']] = $v['prices'];
                    }
                }
                
                
            }
            
            if( ! empty($price_ary) )
            {
                $price_min = min(array_filter($price_ary));
            }
            
            $update_data = array(
              'stock_num'=>$sum_stock_num,
              'stock_num_total'=>$sum_stock_num_total,
              'prices'=>$price_min,
              'prices_list'=>  serialize($price_list_ary),  
                
            );
            $this->set_mall_goods_tbl();
            $rs = $this->update($update_data, "goods_id='$goods_id'");
            
            //放出变量
            unset($update_data);
            unset($sum_stock_num);
            unset($sum_stock_num_total);
            unset($price_ary);
            unset($price_min);
            unset($price_list_ary);
            
            return $rs;
            
        }
        
    }
    
    /**
     * 获取活动情况
     * @param type $goods_id
     * @return boolean|int
     */
    public function get_goods_id_activity_info($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $goods_obj = POCO::singleton('pai_mall_goods_class');
        $goods_info = $goods_obj->get_goods_info($goods_id);
        
        //dump($goods_info);
        //总共多少场，在进行有几场，有多人参与，价钱低到高
        
        $total_show = $ing_show = $has_join_num = $min_price = $max_price = $ing_show_has_person = 0;
        $prices_list = $price_arys = array();
        
        if( ! empty($goods_info['goods_data']['prices_de']))
        {
            $total_show = count($goods_info['goods_data']['prices_de']);
            
            foreach($goods_info['goods_data']['prices_de'] as $k => $v)
            {
                $now_time = time();
                if( $now_time < $v['time_e'] )
                {
                    $ing_show++;
                    $has_join_num = (int)$v['stock_num_total'] - (int)$v['stock_num'];
                    if($has_join_num > 0)
                    {
                        $ing_show_has_person = true;
                    }
                }
                
                $prices_list = unserialize($v['prices_list']);
                
                if( ! empty($prices_list) )
                {
                    foreach($prices_list as $pk => $vk)
                    {
                        $price_arys[] = $vk['prices'];
                    }
                }
            }
        }
        if( ! empty($price_arys) )
        {
            $min_price = min(array_filter($price_arys));
            $max_price = max(array_filter($price_arys));
        }
        if( ! empty($goods_info['goods_data']) )
        {
            $has_join_num = (int)$goods_info['goods_data']['stock_num_total'] - (int)$goods_info['goods_data']['stock_num'];
            if($has_join_num < 0 )
            {
                $has_join_num = 0;
            }
        }
        
        $rs = array(
            'total_show'=>$total_show, //总场次
            'ing_show'=>$ing_show,//正在进行中
            'has_join_num'=>$has_join_num,//已经参加人数
            'min_price'=>$min_price,//最小价
            'max_price'=>$max_price,//最大价
            'ing_show_has_person'=>$ing_show_has_person, //进行中是否有人参与
        );
        
        return $rs;
        
    }
    
    /**
     * 获取商品场次的大小价
     * @param type $goods_id
     * @param type $type_id
     * @return boolean
     */
    public function get_goods_id_screenings_price_max_and_min($goods_id,$type_id)
    {
        $goods_id = (int)$goods_id;
        $type_id = (int)$type_id;
        if( ! $goods_id || ! $type_id )
        {
            return false;
        }
        $goods_obj = POCO::singleton('pai_mall_goods_class');
        $goods_info = $goods_obj->get_goods_info($goods_id);
        $price_ary = array();
        $min_price = $max_price = 0;
        $has_join = 0;
        $total_num = 0;
        $activity_name = '';
        $screenings_name = '';
        if( ! empty($goods_info['goods_data']['prices_de']))
        {
            foreach($goods_info['goods_data']['prices_de'] as $k => $v)
            {
                if($v['type_id'] == $type_id)
                {
                    foreach($v['prices_list_data'] as $pk => $pv)
                    {
                        $price_ary[] = $pv['prices'];
                    }
                    $has_join= $v['stock_num_total'] - $v['stock_num'];
                    if($has_join < 0)
                    {
                        $has_join = 0;
                    }
                    $total_num = $v['stock_num_total'];
                    $activity_name = $goods_info['goods_data']['titles'];
                    $screenings_name = $v['name'];
                    
                    break;
                }
            }
        }
        
        $max_price = max(array_filter($price_ary));
        $min_price = min(array_filter($price_ary));
        //只有一个价的时候
        if(count($price_ary) == 1)
        {
            $min_price = 0;
        }
        
        return array(
            'max_price'=>$max_price,//最大价
            'min_price'=>$min_price,//最小价
            'has_join'=>$has_join,//已经参与人数
            'total_num'=>$total_num,//总库存
            'activity_name'=>$activity_name,//活动名称
            'screenings_name'=>$screenings_name,//场次名称
        );
    }
    
	
///////////////////////////////////////////////////////////////////////////////////////////////////////////	
	/**
	 * 商家添加商品信息
	 * @param int $data	
	 * @param int $user_id
	 * @return int
	 */	
	public function user_add_goods($data,$user_id)
	{
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-3,
						'message'=>'参数错误',
						);
		if($user_id)
		{
			$return = $this->add_goods($data,$user_id);
/*			if($return['result'] > 0)
			{
				$return = $this->change_goods_status($return['result'],1);
			}
*/		}
		return $return;
	}
	
	/**
	 * 商家删除商品信息
	 * @param int $goods_id	
	 * @param int $user_id
	 * @return int
	 */	
	public function user_del_goods($goods_id,$user_id)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-2,
						'message'=>'参数错误',
						);
		if($goods_id and $user_id)
		{
			$return = $this->change_goods_status($goods_id,3,$user_id);
		}
		return $return;
	}
	
	/**
	 * 商家获取商品信息
	 * @param int $type_id	
	 * @return array
	 */	
	public function user_show_goods_data($type_id)
	{
		$type_id = (int)$type_id;
		$return = array(
						'result'=>-1,
						'message'=>'参数错误',
						);
		if($type_id)
		{
			$goods_info = $this->show_goods_data($type_id);
			$data = array();
			$data['contact_data'] = $goods_info['contact_data'];
			foreach($goods_info['default_data'] as $val)
			{
				$data['default_data'][$val['key']] = $val;
			}
			foreach($goods_info['system_data'] as $val)
			{
				$data['system_data'][$val['key']] = $val;
			}
			foreach($goods_info['diy_data'] as $val)
			{
				$data['diy_data'][$val['key']] = $val;
			}
			foreach($goods_info['prices_data'] as $val)
			{
				$data['prices_data'][$val['key']] = $val;
			}
			$return = array(
							'result'=>1,
							'message'=>'成功',
							'data'=>$data,
							);
		}
		return $return;
	}
	
	/**
	 * 商家获取商品信息
	 * @param int $goods_id	
	 * @param int $user_id
	 * @param bool $newdata 活动审核数据
	 * @return array
	 */	
	public function user_get_goods_info($goods_id,$user_id,$newdata=false)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-1,
						'message'=>'参数错误',
						);
		if($goods_id and $user_id)
		{
			$goods_info = $this->get_goods_info_by_sql($goods_id,$user_id);
			if(!$goods_info or $goods_info['goods_data']['status'] == 3)
			{
				$return = array(
								'result'=>-2,
								'message'=>'没有该商品信息',
								);
			}
			elseif($user_id != $goods_info['goods_data']['user_id'])
			{
				$return = array(
								'result'=>-3,
								'message'=>'无权限',
								);
			}
			else
			{
				//新旧数据重组
				if($newdata)
				{
                	$goods_info = $this->format_goods_data($goods_info);
                }
				$data = array();
				$data['goods_data'] = $goods_info['goods_data'];
				$data['prices_data_list'] = $goods_info['prices_data_list'];
				$data['contact_data'] = $goods_info['contact_data'];
				foreach($goods_info['default_data'] as $val)
				{
					$data['default_data'][$val['key']] = $val;
				}
				foreach($goods_info['system_data'] as $val)
				{
					$data['system_data'][$val['key']] = $val;
				}
				foreach($goods_info['diy_data'] as $val)
				{
					$data['diy_data'][$val['key']] = $val;
				}
				foreach($goods_info['prices_data'] as $val)
				{
					$data['prices_data'][$val['key']] = $val;
				}
				foreach($goods_info['goods_prices_list'] as $val)
				{
					$data['goods_prices_list'][$val['key']] = $val;
				}
				$return = array(
								'result'=>1,
								'message'=>'成功',
								'data'=>$data,
								);
			}
		}
		return $return;
	}
    
    /**
     * 活动新旧数据重组
     * @param type $goods_info
     * @return boolean
     */
    public function format_goods_data($goods_info)
    {
        if(empty($goods_info))
        {
            return false;
        }        
        
        if($goods_info['goods_data']['type_id'] == 42 && ($goods_info['goods_data']['edit_status'] == 1 || $goods_info['goods_data']['edit_status'] == 3) )
        {
            //新数据
            $new_data = $this->get_mall_goods_check($goods_info['goods_data']['goods_id']);
            
            //新标题
            if( ! empty($new_data['default_data']['titles']) )
            {
                $goods_info['goods_data']['new_titles'] = $new_data['default_data']['titles']; //新标题
            }
            
            //default_data 数据新旧重组
            if( ! empty($new_data['default_data']) )
            {
				foreach($goods_info['default_data'] as $ko => $vo)
				{
                    if( ! empty($new_data['default_data'][$vo['key']]) )
                    {
                        $goods_info['default_data'][$ko]['value'] = $new_data['default_data'][$vo['key']];
                    }
					
				}
            }
            
            //contact_data 数据新旧重组
            if( ! empty($new_data['contact_data']) )
            {
                unset($goods_info['contact_data']);
                $i = 0;
                foreach($new_data['contact_data'] as $k => $v)
                {
                    $goods_info['contact_data'][$i] = array(
                        'name'=>$k,
                        'data'=>$v,
                    );
                    $i++;
                }
                
            }
            
            //image_data 新旧数据重组
            if( ! empty($new_data['img']) )
            {
                $goods_info['image_data']['value'] = $new_data['img'];
            }
            
            //system_data 数据重组
            if( ! empty($new_data['system_data']) )
            {
                foreach($goods_info['system_data'] as $ko => $vo)
                {
                    if( ! empty($vo['child_data']) )
                    {
                        foreach($vo['child_data'] as $vock => $vocv )
                        {
                            if( ! empty($vocv['child_data']) )
                            {
                                foreach($vocv['child_data'] as $vocck => $voccv)
                                {
                                    unset($goods_info['system_data'][$ko]['child_data'][$vock]['child_data'][$vocck]['is_select']);
                                    
                                    //如果是摄影外拍
                                    if($new_data['system_data']['d947bf06a885db0d477d707121934ff8'] == $voccv['key'])
                                    {
                                        $goods_info['system_data'][$ko]['child_data'][$vock]['child_data'][$vocck]['is_select'] = 1;
                                        $goods_info['system_data'][$ko]['child_data'][$vock]['value'] = $voccv['key'];
                                    }
                                    
                                }
                            }
                        }
                    }
                    
                    if( ! empty( $new_data['system_data'][$vo['key']] ) )
                    {
                        $goods_info['system_data'][$ko]['value'] = $new_data['system_data'][$vo['key']];
                    }
                        
                }
            }        
        }
		return $goods_info;
        
    }
	
	
	/**
	 * 用户修改商品逐条信息
	 * @param int $goods_id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function user_update_goods_for_detail($goods_id,$data,$user_id=0)
	{
		$return = array(
		                'result'=>-1,
						'message'=>'没有该商品信息',
						);
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$goods_info = $this->get_goods_info($goods_id);
		
		if(!$goods_info)
		{
			return $return;
		}
		if($user_id and $goods_info['goods_data']['user_id'] != $user_id)
		{
			$return = array(
							'result'=>-2,
							'message'=>'无权限修改该商品',
							);
			return $return;
		}
		
		//////////////////////
		$detail = array();
		$detail_list = '';
		if($data['system_data'])
		{
			$type_md5_array = array();
			foreach($data['system_data'] as $key => $val)
			{
				if(is_array($val))
				{
					foreach($val as $val_de)
					{
						$detail[] = array(
										'detail_type'=>1,
										'name'=>$key,
										'data'=>$val_de,
										);
						$type_md5_array[] = md5($key."|".$val_de);
					}
				}
				else
				{
					$detail[] = array(
									'detail_type'=>1,
									'name'=>$key,
									'data'=>$val,
									);
					$type_md5_array[] = md5($key."|".$val);
				}
			}
			/////////
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id($type_md5_array);
			if($type_md5)
			{
				$detail_list_array = array();
				foreach($type_md5 as $val)
				{
					$detail_list_array[] = $val['id'];
				}
				$detail_list = implode(',',$detail_list_array);
			}
			/////////
		}
		if($data['diy_data'])
		{
			foreach($data['diy_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>2,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//组合
		if($data['combination_data'])
		{
			foreach($data['combination_data'] as $key => $val)
			{
				if($val['images'])
				{
					foreach($val['images'] as $key_de => $val_de)
					{
						$val['images'][$key_de]['src'] = $key_de;
					}
				}
				$detail[] = array(
								'detail_type'=>3,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//联系信息
		if($data['contact_data'])
		{
			foreach($data['contact_data'] as $key => $val)
			{
				$detail[] = array(
								'detail_type'=>4,
								'name'=>$key,
								'data'=>serialize($val),
								);
			}
		}
		//////////////////////
		if($detail)//更新附加属性
		{
			$this->set_mall_goods_detail_tbl();
			foreach($detail as $val)
			{
				$del_sql = "name = '".pai_mall_change_str_in($val['name'])."' and goods_id='{$goods_id}'";
				$this->delete($del_sql);				
			}
			$this->add_goods_detail($goods_id,$detail);
		}
		//更新主表
		$data_sql = array(
						  'update_time' => time(),
						  'version' => date('YmdHis').rand(100000,999999),						
						  );
		$detail_list?$data_sql['detail_list'] = $detail_list:'';
		$this->set_mall_goods_tbl();		
		$this->update($data_sql, "goods_id={$goods_id}");
		//
		
		if($goods_info['goods_data']['is_show'] == 1)
		{
			$add_version = $this->add_goods_version($goods_id,$user_id);
		}
		$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
		$return = array(
						'result'=>$goods_id,
						'message'=>'修改成功',
						);
		return $return;
	}
	
 
	
	/**
	 * 修改商品价格
	 * @param int $goods_id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function user_update_goods_prices($goods_id,$data,$user_id)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-2,
						'message'=>'无权限修改该商品',
						);
		if($goods_id and $user_id)
		{
			$goods_info = $this->get_goods_info($goods_id);
			if($goods_info['goods_data']['user_id'] != $user_id)
			{
				return $return;
			}
			//////////
			$prices_de = $data['prices_de'];
			$prices_diy = $data['prices_diy'];
			if($prices_diy)
			{
				if(in_array($goods_info['goods_data']['stock_type'],array(4,5)))
				{
					$data['default_data']['stock_num']=0;
				}
				foreach($prices_diy as $key => $val)
				{
					$prices_de[$key] = $val['prices'];
					$data['default_data']['stock_num']+=(int)$val['stock_num'];
				}
			}
			$prices_default = sprintf('%.2f',$data['default_data']['prices']);
			if($prices_default <= 0)
			{
				$min_prices_de = $prices_de;
				$p_data = array_filter($min_prices_de)?min(array_filter($min_prices_de)):0;
				$prices_default = sprintf('%.2f',$p_data);
			}
			if($prices_default <= 0)
			{
				$return = array(
								'result'=>-5,
								'message'=>'商品价格不能为零',
								);
				return $return;
			}
			foreach($prices_de as $key => $val)
			{
				$prices = sprintf('%.2f',$val);
				$prices_de[$key] = $prices;
			}
			$prices_list = serialize($prices_de);
			$data_sql = array(
							  'prices' => $prices_default,
							  'prices_list'=>$prices_list,
							  'update_time' => time(),
							  'version' => date('YmdHis').rand(100000,999999),						
							  );			
			$this->set_mall_goods_tbl();
			$this->update($data_sql, "goods_id={$goods_id}");			
			$add_user = $user_id?$user_id:$goods_info['goods_data']['user_id'];
			///////////////////////
			if($prices_de)//价格明细
			{
				if(in_array($goods_info['goods_data']['stock_type'],array(5,7)))
				{
					$return = $this->update_goods_prices_diy($goods_id,$prices_diy,$add_user,$goods_info['goods_data']['type_id'],$goods_info['goods_data']['stock_type']);
				}
				else
				{
					if($goods_info['goods_data']['stock_type'] == 4)
					{
						$other_data = $data['prices_de_other'];
					}			
					$return = $this->update_goods_prices($goods_id,$prices_de,$add_user,$goods_info['goods_data']['type_id'],$other_data);
				}				
			}
			/////////////////////////
			if($goods_info['goods_data']['is_show'] == 1)
			{
				$add_version = $this->add_goods_version($goods_id,$user_id);
			}
			$this->exec_cmd_pai_mall_synchronous_goods($goods_id,$goods_info['goods_data']['type_id'],2);
			$return = array(
							'result'=>$goods_id,
							'message'=>'修改成功',
							);
			/////////			
		}
		return $return;
	}
    
	
	/**
	 * 修改商品
	 * @param int $goods_id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function user_update_goods($goods_id,$data,$user_id)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-2,
						'message'=>'无权限修改该商品',
						);
		if($goods_id and $user_id)
		{
			//$goods_info = $this->get_goods_info($goods_id);
			$return = $this->update_goods($goods_id,$data,$user_id);
			if($return['result'] == 1)
			{				
				////////////////////////////////////临时处理状态,上下架
/*				if($goods_info['goods_data']['status'] == 1)
				{
					$return = $this->change_goods_status($goods_id,1);
				}
				elseif($goods_info['goods_data']['status'] == 2)
				{
					$return = $this->change_goods_status($goods_id,0);
				}
*/				////////////////////////////////////
				$this->exec_cmd_pai_mall_follow_user_showtime($user_id,$goods_id);
			}
		}
		return $return;
	}
	
	/**
	 * 商家商品上下架
	 * @param int $goods_id
	 * @param int $status
	 * @param int $user_id
	 * @return array
	 */
	public function user_change_goods_show_status($goods_id,$status,$user_id=0)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$status = (int)$status;
		$return = array(
						'result'=>-2,
						'message'=>'无权限修改该商品',
						);
		if($goods_id and $user_id)
		{
			$goods_data = $this->get_goods_info_by_sql($goods_id);
			if($goods_data['goods_data']['type_id'] == 42 and $goods_data['goods_data']['stock_num_total']!=$goods_data['goods_data']['stock_num'] and $status == 2)
			{
				if($goods_data['goods_data']['prices_de'])
				{
					foreach($goods_data['goods_data']['prices_de'] as $val)
					{
						if($val['time_s']>time() and $val['stock_num_total']!=$val['stock_num'])
						{
							$return = array(
											'result'=>-10,
											'message'=>'活动已有人购买,不能下架',
											);
							return $return;
						}
					}
				}
			}
			$return = $this->change_goods_show_status($goods_id,$status,$user_id);
		}
		return $return;
	}
	
	
	/**
	 * 修改活动
	 * @param int $goods_id
	 * @param array $data
	 * @param int $user_id
	 * @return array
	 */
	public function user_update_goods_for_42($goods_id,$data,$user_id)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-2,
						'message'=>'修改失败',
						);
		if($goods_id and $user_id)
		{
			$goods_info = $this->get_goods_info($goods_id);
			if($goods_info['goods_data']['user_id'] == $user_id and $goods_info['goods_data']['type_id'] == 42)
			{
				$re = $this->add_mall_goods_check($goods_id,$data);
				if($re)
				{
					$return = array(
									'result'=>1,
									'message'=>'修改成功,请等待审核',
									);
				}
			}
		}
		return $return;
	}

	/**
	 * 删除活动价格
	 * @param int $goods_id
	 * @param int $user_id
	 * @return array
	 */
	public function user_delete_goods_prices_detail_for_42($goods_id,$type_id,$user_id)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-3,
						'message'=>'无权限修改该商品',
						);
		if($goods_id and $user_id)
		{
			$goods_info = $this->get_goods_info($goods_id);
			if($goods_info['goods_data']['user_id'] == $user_id and $goods_info['goods_data']['type_id'] == 42)
			{
				$return = $this->delete_goods_prices_detail_for_42($goods_id,$type_id);
				if($return['result'] == 1)
				{
					$this->add_goods_version_for_new($goods_id,$user_id);
					$this->batch_insert_or_update_goods_type_id_tbl($goods_id);
				}
			}
		}
		return $return;
	}

	/**
	 * 修改活动价格
	 * @param int $goods_id
	 * @param int $user_id
	 * @param array $data
	 * @return array
	 */
	public function user_update_goods_prices_detail_for_42($goods_id,$data,$user_id)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-10,
						'message'=>'无权限修改该商品',
						);
		if($goods_id and $user_id)
		{
			$goods_info = $this->get_goods_info($goods_id);
			if($goods_info['goods_data']['user_id'] == $user_id and $goods_info['goods_data']['type_id'] == 42)
			{
				$return = $this->update_goods_prices_detail_for_42($goods_id,$data,$user_id);
				if($return['result'] == 1)
				{
					$this->add_goods_version_for_new($goods_id,$user_id);
					$this->batch_insert_or_update_goods_type_id_tbl($goods_id);
				}
			}
		}
		return $return;
	}

	/**
	 * 添加活动价格
	 * @param int $goods_id
	 * @param int $user_id
	 * @param array $data
	 * @return array
	 */
	public function user_add_goods_prices_detail_for_42($goods_id,$data,$user_id)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-10,
						'message'=>'添加失败',
						);
		if($goods_id and $user_id)
		{
			$goods_info = $this->get_goods_info($goods_id);
			if($goods_info['goods_data']['user_id'] == $user_id and $goods_info['goods_data']['type_id'] == 42)
			{
				foreach($data['prices_diy'] as $key => $val)
				{				
					foreach($val['detail']['prices'] as $key_de => $val_de)
					{
						$prices_t_id = strlen($key_de)<14?time().rand(10000,99999):$key_de;
						$data['prices_diy'][$key]['detail']['type_id'][$key_de] = $prices_t_id;
					}
					$data['prices_diy'][$key]['prices']=min(array_filter($val['detail']['prices']));
				}
				$return = $this->add_goods_prices_diy($goods_id,$data['prices_diy'],$user_id,$goods_info['goods_data']['type_id'],$goods_info['goods_data']['stock_type']);
				if($return)
				{
					$this->update_goods_prices_data($goods_id);
					$this->exec_cmd_pai_mall_synchronous_goods($goods_id,42,2);
					$this->batch_insert_or_update_goods_type_id_tbl($goods_id);
					$this->add_goods_version_for_new($goods_id,$user_id);
					$return = array(
									'result'=>1,
									'message'=>'添加成功',
									);
				}
			}
		}
		return $return;
	}

	/**
	 * 用户检查是否能修改商品
	 * @param int $goods_id
	 * @param int $user_id
	 * @return array
	 */
	public function user_check_can_update_goods($goods_id,$user_id)
	{
		$goods_id = (int)$goods_id;
		$user_id = (int)$user_id;
		$return = array(
						'result'=>-1,
						'message'=>'无权限修改该商品',
						);
		if($goods_id and $user_id)
		{
			$goods_info = $this->get_goods_info_by_sql($goods_id);
			if($goods_info['goods_data']['user_id'] == $user_id)
			{
				if($goods_info['goods_data']['type_id'] == 42)
				{
					$return = array(
									'result'=>-2,
									'message'=>'活动已结束,无法修改',
									);
					$this->set_mall_goods_prices_tbl();
					$re = $this->findAll("goods_id='{$goods_id}' and time_e>=".time());
					if($re)
					{
						$return = array(
										'result'=>1,
										'message'=>'可以修改',
										);
					}
					//print_r($re);
				}
			}
		}
		return $return;
	}

	/**
	 * 用户店铺商品类型列表
	 * @param int $user_id
	 * @return array
	 */
	public function user_goods_type_list($user_id)
	{
		$user_id = (int)$user_id;
		$seller_obj = POCO::singleton('pai_mall_seller_class');
		$this->debug?$seller_obj->set_db_test():"";//开启调试
		$store_info = $seller_obj->get_seller_info($user_id,2);
		$show_type = explode(',',$store_info['seller_data']['company'][0]['store'][0]['type_id']);
		$type_list_name = array();
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_list = $type_obj->get_type_cate(2);
		$type_list_name = array();
		foreach($type_list as $key => $val)
		{
			$val['show'] = in_array($val['id'],$show_type)?true:false;
			$type_list_name[$val['id']] = $val;
		}
		return $type_list_name;
	}

	/**
	 * 用户商品列表
	 * @param int $user_id
	 * @param array $data(status,show,type_id,keyword)
	 * @return array
	 */
	public function user_goods_list($user_id,$data,$b_select_count = false, $order_by = 'goods_id DESC', $limit = '0,10', $fields = '*')
	{
		$user_id = (int)$user_id;
		$status = is_numeric($data['status'])?$data['status']:10;//6表示0和2;7表示0和1
		$action_type = (int)$data['action_type'];
		$show = (int)$data['show'];
		$type_id = $data['type_id'];
		$keyword = $data['keyword'];
		$location_id = $data['location_id'];
		$list = array();
		if($user_id)
		{
			$where = "user_id='{$user_id}' AND status!=3";
			if($status == 6)
			{
				$where .= " AND (status = 0 or status = 2)";
			}
			elseif($status == 7)
			{
				$where .= " AND (status = 0 or status = 1)";
			}
			else
			{
				$where .= (is_numeric($data['status']) and $status != 10)?" AND status = '{$status}'":"";
			}
			$where .= $show?" AND is_show = '{$show}'":"";
			$where .= $type_id?" AND type_id in (".pai_mall_change_str_in($type_id).")":"";
			$where .= $location_id?" AND location_id in (".pai_mall_change_str_in($location_id).")":"";
			$where .= $keyword?" AND titles like '%".pai_mall_change_str_in($keyword)."%'":"";
			if($data['promotion_search'])
			{
				$promotion_search = array();
				if($data['promotion_search']['not_location_type_id'])
				{
					$promotion_search[] = "type_id in (".pai_mall_change_str_in($data['promotion_search']['not_location_type_id']).")";
				}
				if($data['promotion_search']['type_id'])
				{
					$promotion_search[] = "(type_id in (".pai_mall_change_str_in($data['promotion_search']['type_id']).")".($data['promotion_search']['location_id']?" AND location_id in (".pai_mall_change_str_in($data['promotion_search']['location_id']).")":"").")";
				}
				if($promotion_search)
				{
					$where .= " AND (".implode(' OR ',$promotion_search).")";
				}
			}
			$fun_name = "get_goods_list";
			if($action_type)
			{
				$where .= " AND type_id = 42";
				$fun_name = "get_goods_list_for_42";
				switch($action_type)
				{
					case 1:
						$where .= " AND e_time > '".time()."' AND is_show = 1";
					break;
					case 2:
						$where .= " AND e_time < '".time()."' AND is_show = 1";
					break;
					case 3:
						$where .= " AND status = 1 AND is_show = 2";
					break;
					case 4:
						$where .= " AND (edit_status in (1,3) or status in (0,2))";
					break;
					case 5:
						$where .= " AND is_show = 1";
					break;
				}
				//echo $where;
			}
			else
			{
				$where .= " AND type_id != 42";
			}
			if($b_select_count)
			{
				$list = $this->$fun_name(true, $where);
			}
			else
			{
				$list = $this->$fun_name(false, $where, $order_by, $limit);
			}
		}
		return $list;
	}
	
///////////////////////前台方法//////////////////////////////////////////////////	
	/**
	 * 消费者获取商品信息
	 * @param int $goods_id	
	 * @return array
	 */	
	public function get_goods_info_by_goods_id($goods_id)
	{
		$goods_id = (int)$goods_id;
		$return = array(
						'result'=>-1,
						'message'=>'参数错误',
						);
		if($goods_id)
		{
			$goods_info = $this->get_goods_info($goods_id);
			if(!$goods_info or $goods_info['goods_data']['status'] == 3)
			{
				$return = array(
								'result'=>-2,
								'message'=>'没有该商品信息',
								);
			}
			else
			{
				$data = array();
				$data['goods_data'] = $goods_info['goods_data'];
				$data['prices_data_list'] = $goods_info['prices_data_list'];
				$data['contact_data'] = $goods_info['contact_data'];
				$data['combination_data'] = $goods_info['combination_data'];
				foreach((array)$goods_info['default_data'] as $val)
				{
					$data['default_data'][$val['key']] = $val;
				}
				foreach((array)$goods_info['system_data'] as $val)
				{
					$data['system_data'][$val['key']] = $val;
				}
				foreach((array)$goods_info['diy_data'] as $val)
				{
					$data['diy_data'][$val['key']] = $val;
				}
				foreach((array)$goods_info['prices_data'] as $val)
				{
					$data['prices_data'][$val['key']] = $val;
				}
				foreach((array)$goods_info['goods_prices_list'] as $val)
				{
					$data['goods_prices_list'][$val['key']] = $val;
				}
				$return = array(
								'result'=>1,
								'message'=>'成功',
								'data'=>$data,
								);
			}
			if($goods_info and $goods_info['goods_data']['is_show'] != 1 and $goods_info['goods_data']['status'] != 3)
			{
				$return = array(
								'result'=>-3,
								'message'=>'没上架',
								'data'=>$data,
								);
			}
		}
		return $return;
	}
	
    

	/**
	 * 用户商品品类名称
	 * @param int $type_id
	 * @return str
	 */
	public function get_goods_typename_for_type_id($type_id)
	{
		$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
        $type_list = $task_goods_type_obj->get_type_cate();
		foreach($type_list as $val)
		{
			if($val['id']==$type_id)
			{
				return $val['name'];
			}
		}
		return false;
	}

	/**
	 * 根据商品属性ID返回商品属性数据
	 * @param array $data 商品数据
	 * @param array $type_id_arr 要返回的属性id
	 * @return array
	 */
	public function get_goods_type_name($data=array(),$type_id_arr=array())
	{
		$return = array();
		$data = (array)$data;
		$type_id_arr = (array)$type_id_arr;
		if($data['goods_data']['detail'] and $type_id_arr)
		{
			foreach($data['goods_data']['detail'] as $val)
			{
				if(in_array($val['type_id'],$type_id_arr))
				{
					$return[$val['type_id']] = $val['data'];
				}
			}
		}
		return $return;
	}
	

	/**
	 * 搜索商品列表
	 * @param array $data(type_id,store_id,location_id,detail_type_id,keyword)
	 * @return array
	 */
	public function search_goods_list($data,$b_select_count = false, $order_by = 'goods_id DESC', $limit = '0,10')
	{
		$store_id = (int)$data['store_id'];
		$type_id = (int)$data['type_id'];
		$location_id = (int)$data['location_id'];
		$keyword = $data['keyword'];
		if($data['detail_type_id'])
		{
			$mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
			$type_md5 = $mall_goods_type_attribute_obj->get_id_by_type_id((array)$data['detail_type_id']);
			$detail_id = $type_md5[0]['id'];
		}
		$list = array();
		$where = "is_show=1 and status!=3";		
		$where .= $store_id?" AND store_id = '{$store_id}'":"";
		$where .= $type_id?" AND type_id = '{$type_id}'":"";
		$where .= $location_id?" AND location_id in (0,{$location_id})":"";
		//$where .= $location_id?" AND (location_id =0 OR FIND_IN_SET('{$location_id}',location_id))":"";
		$where .= $detail_id?" AND FIND_IN_SET('{$detail_id}',detail_list)":"";
		//$where .= $keyword?" AND titles like '%".pai_mall_change_str_in($keyword)."%'":"";
		if($keyword)
		{
			if(is_numeric($keyword))
			{
				$where .=" AND (titles like '%".pai_mall_change_str_in($keyword)."%' OR user_id = '".(int)$keyword."' OR goods_id = '".(int)$keyword."')";
			}
			else
			{
				$where .=" AND (titles like '%".pai_mall_change_str_in($keyword)."%' OR introduction like '%".pai_mall_change_str_in($keyword)."%')";
				$where .=" AND is_black=0";		
			}
		}
		else
		{
			$where .= " AND is_black=0";		
		}
		if($data['debug'])
		{
			echo $where;
		}
		if($b_select_count)
		{
			$list = $this->get_goods_list(true, $where);
		}
		else
		{
			$list = $this->get_goods_list(false, $where, $order_by, $limit);
		}
		return $list;
	}
	
	/**
	 * 统计商品点击量
	 * @param int $goods_id
	 * @param int $num
	 * @return bool
	 */
	public function user_update_goods_view_num($goods_id,$num=1)
	{
		$goods_id = (int)$goods_id;
		if($goods_id)
		{
			$data['view_num'] = $num;
			$this->update_goods_statistical($goods_id,$data);
			return true;
		}
		return false;
	}
	

	/**
	 * 前台搜索商品列表
	 * @param array $data(type_id,location_id,detail_type_id,keyword)
	 * @return array
	 */
	public function user_search_goods_list($data,$limit = '0,10')
	{
		$data['is_show']=1;
		$data['is_black']=0;
		return $this->search_goods_list_by_fulltext($data,$limit);
	}

	/**
	 * 搜索商品列表
	 * @param array $data(type_id,location_id,detail_type_id,keyword)
	 * @return array
	 */
	public function search_goods_list_by_fulltext($data,$limit = '0,10')
	{
		if($data['front_time'])
		{
			switch($data['front_time'])
			{
				case "today":
				    $today_time = strtotime(date('Y-m-d'));
					$data['huo_add_time_s'] = $today_time;
					$data['huo_add_time_e'] = $today_time+86400;
				break;
				case "tomorrow":
				    $today_time = strtotime(date('Y-m-d'));
					$data['huo_add_time_s'] = $today_time+86401;
					$data['huo_add_time_e'] = $today_time+172800;
				break;
				case "weekend":
					$data['week'] = '6|7';
				break;
			}
		}
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
					$data["order"]=8;
					$data["order_type"]=1;
				break;
				case 4:
					$data["order"]=8;
					$data["order_type"]=2;
				break;
				case 5://人气
					$data["order"]=7;
					$data["order_type"]=1;
				break;
				case 6:
					$data["order"]=7;
					$data["order_type"]=2;
				break;
				case 7://评分
					$data["order"]=4;
					$data["order_type"]=1;
				break;
				case 8:
					$data["order"]=4;
					$data["order_type"]=2;
				break;
				case 9:
					$data["order"]=9;
					$data["order_type"]=1;
				break;
				default://综合
					$data["order"]=6;
					$data["order_type"]=1;
				break;
			}
		}
		//if(strpos($data["m_cup"],'E')!==false)
		if($data["m_cup"][0] == 'E')
		{
			$data["m_cup"] = 'E+';
		}
		if($data['detail'])
		{			
			foreach($data['detail'] as $key => $val)
			{
				$data["name_{$key}"] = $val;
			}
		}
		if($data['third'])
		{
			$task_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');			
			$third = $task_goods_type_attribute_obj->get_third_parent(array('third'=>$data['third']));
			if($third['detail'])
			{
				foreach($third['detail'] as $key => $val)
				{
					$data["name_{$key}"] = $val;
				}
			}
		}
		////////////////价格
		$data['prices'] = array();
		if($data['price_s']!="")
		{
			$data['prices'][] = (int)$data['price_s'];
		}
		if($data['price_e']!="")
		{
			$data['prices'][] = (int)$data['price_e'];
		}
		if($data['price_s_e']!="")
		{
			$price_s_e = explode(',',$data['price_s_e']);
			$data['prices'][] = (int)$price_s_e[0];
			$data['prices'][] = (int)$price_s_e[1];
		}
		//$data['prices'] = array_filter($data['prices']);
		sort($data['prices']);
		//////////////id
		if($data["goods_id"])
		{
			$old_data = $data;
			unset($data);
			$data["goods_id"] = $old_data['goods_id'];
			$data["type_id"] = $old_data['type_id'];
			$data["is_black"] = $old_data['is_black'];
			$data["is_show"] = $old_data['is_show'];
			$data["location_id"] = $old_data['location_id'];
			$data["search_type"] = $old_data['search_type'];
			if(is_array($data["goods_id"]))
			{
				$data['search_type'] = "sql";
			}
		}
		if(is_numeric($data["keywords"]))
		{
			$old_data = $data;
			unset($data);
			$data["goods_id"] = $old_data["keywords"];
			$data["type_id"] = $old_data["type_id"];
			$data["debug"] = $old_data["debug"];
			//$data["is_black"] = $old_data['is_black'];
			//$data["is_show"] = $old_data['is_show'];
			$limit = '0,1';
			$data['search_type'] = "sql";
		}
		////////////////地区
		if($data["area_id"])
		{
			$querys["location_id"] = "*,0,*|*,".$data["area_id"].",*";
		}
		elseif($data["location_id"])
		{
			$querys["location_id"] = "*,0,*|*,".$data["location_id"].",*";
		}
		elseif($data["city"])
		{
			$querys["location_id"] = "*,0,*|*,".$data["city"]."*";
		}
		
		$data["keywords"]?$querys["keywords"] = $data["keywords"]:"";//关键字 content,introduction
		isset($data["is_black"]) and in_array($data["is_black"],array(0,1))?$querys["is_black"] = $data["is_black"]:"";//类型
		$data["type_id"]?$querys["type_id"] = $data["type_id"]:"";//类型
		$data["goods_id"]?$querys["goods_id"] = $data["goods_id"]:"";//goodsid
		$data["brand_id"]?$querys["brand_id"] = $data["brand_id"]:"";//品牌
		//isset($data["status"]) and $data["status"]!=10?$querys["goods_status"] = $data["status"]:"";//审核状态
		isset($data["status"]) and $data["status"]!=10?$querys["status"] = $data["status"]:"";//审核状态
		$data["is_show"]?$querys["is_show"] = $data["is_show"]:"";//上架
		$data["seller_id"]?$querys["seller_id"] = $data["seller_id"]:"";//商家
		$data["store_id"]?$querys["store_id"] = $data["store_id"]:"";//店铺
		$data["user_id"]?$querys["user_id"] = $data["user_id"]:"";//用户
		$data["view_num"]?$querys["view_num"] = (int)$data["view_num"]:"";
		isset($data["is_official"]) and in_array($data["is_official"],array(0,1))?$querys["is_official"] = (int)$data["is_official"]:"";
		$data["edit_status"]?$querys["edit_status"] = (int)$data["edit_status"]:"";
		
		//strtotime($data["add_time"])?$querys["add_time"] = strtotime($data["add_time"]).','.(strtotime($data["add_time"])+86400):"";//添加时间
		//strtotime($data["begin_time"]) and $data["is_show"]==1?$querys["onsale_time"] = strtotime($data["begin_time"]).','.(strtotime($data["end_time"])+86400):"";//上架时间
/*		strtotime($data["begin_time"])?$querys["onsale_time"] = strtotime($data["begin_time"]).','.(strtotime($data["end_time"])+86400):"";//上架时间
		strtotime($data["add_time_s"])?$querys["add_time"] = strtotime($data["add_time_s"]).','.(strtotime($data["add_time_e"])+86400):"";//添加时间 
		strtotime($data["audit_time_s"])?$querys["audit_time"] = strtotime($data["audit_time_s"]).','.(strtotime($data["audit_time_e"])+86400):"";//审核时间 audit_time
		strtotime($data["update_time_s"])?$querys["update_time"] = strtotime($data["update_time_s"]).','.(strtotime($data["update_time_e"])+86400):"";//修改时间 
*/		//
		strtotime($data["begin_time"])?$querys["onsale_time"] = strtotime($data["begin_time"]).(strtotime($data["end_time"])?','.(strtotime($data["end_time"])+86400):""):"";//上架时间
		strtotime($data["add_time_s"])?$querys["add_time"] = strtotime($data["add_time_s"]).(strtotime($data["add_time_e"])?','.(strtotime($data["add_time_e"])+86400):""):"";//添加时间 
		strtotime($data["audit_time_s"])?$querys["audit_time"] = strtotime($data["audit_time_s"]).(strtotime($data["audit_time_e"])?','.(strtotime($data["audit_time_e"])+86400):""):"";//审核时间 audit_time
		strtotime($data["update_time_s"])?$querys["update_time"] = strtotime($data["update_time_s"]).(strtotime($data["update_time_e"])?','.(strtotime($data["update_time_e"])+86400):""):"";//修改时间 
		//
		
		$data["prices_prices"]?$querys["prices_prices"] = $data["prices_prices"]:"";//价格
		/////////////////////////
        //模特服务(ID:31)
        $data["name_46"]?$querys["name_46"] = $data["name_46"]:"";//类型数据data
        $data["name_58"]?$querys["name_58"] = $data["name_58"]:"";//类型数据data
        $data["name_75"]?$querys["name_75"] = $data["name_75"]:"";//类型数据data
        $data["m_bwh"]?$querys["m_bwh"] = $data["m_bwh"]:"";//类型数据data
        $data["m_cup"]?$querys["m_cup"] = $data["m_cup"]:"";//类型数据data
        $data["m_cups"]?$querys["m_cups"] = $data["m_cups"]:"";//类型数据data
        $data["m_height"]?$querys["m_height"] = $data["m_height"]:"";//类型数据data
        $data["m_level"]?$querys["m_level"] = $data["m_level"]:"";//类型数据data
        $data["m_weight"]?$querys["m_weight"] = $data["m_weight"]:"";//类型数据data
        $data["m_sex"]?$querys["m_sex"] = $data["m_sex"]:"";//类型数据data
        //
        //影棚租赁(ID:12)
        $data["name_17"]?$querys["name_17"] = $data["name_17"]:"";//类型数据data
        $data["name_19"]?$querys["name_19"] = $data["name_19"]:"";//类型数据data
        $data["name_20"]?$querys["name_20"] = $data["name_20"]:"";//类型数据data
        $data["name_35"]?$querys["name_35"] = $data["name_35"]:"";//类型数据data
		
        //
        //化妆服务(ID:3  )
        $data["name_68"]?$querys["name_68"] = $data["name_68"]:"";//类型数据data
        $data["name_152"]?$querys["name_152"] = $data["name_152"]:"";//类型数据data
        //
        //摄影培训(ID:5  )
        $data["name_62"]?$querys["name_62"] = $data["name_62"]:"";//类型数据data
        $data["name_133"]?$querys["name_133"] = $data["name_133"]:"";//类型数据data
        $data["name_148"]?$querys["name_148"] = $data["name_148"]:"";//类型数据data
        $data["name_317"]?$querys["name_317"] = $data["name_317"]:"";//类型数据data
        $data["name_382"]?$querys["name_382"] = $data["name_382"]:"";//类型数据data
        $data["t_teacher"]?$querys["t_teacher"] = $data["t_teacher"]:"";//类型数据data
        $data["t_experience"]?$querys["t_experience"] = $data["t_experience"]:"";//类型数据data
        //
        //摄影服务(ID:40)
        $data["name_90"]?$querys["name_90"] = $data["name_90"]:"";//类型数据data
        $data["name_99"]?$querys["name_99"] = $data["name_99"]:"";//类型数据data
        $data["name_102"]?$querys["name_102"] = $data["name_102"]:"";//类型数据data
        $data["name_106"]?$querys["name_106"] = $data["name_106"]:"";//类型数据data
        $data["name_289"]?$querys["name_289"] = $data["name_289"]:"";//类型数据data
        $data["name_309"]?$querys["name_309"] = $data["name_309"]:"";//类型数据data
        $data["p_experience"]?$querys["p_experience"] = $data["p_experience"]:"";//类型数据data
        $data["p_goodat"]?$querys["p_goodat"] = $data["p_goodat"]:"";//类型数据data
		//
        //约美食(ID:41)
        $data["name_219"]?$querys["name_219"] = $data["name_219"]:"";//类型数据data
        $data["name_220"]?$querys["name_220"] = $data["name_220"]:"";//类型数据data
        $data["name_250"]?$querys["name_250"] = $data["name_250"]:"";//类型数据data
        $data["ms_experience"]?$querys["ms_experience"] = $data["ms_experience"]:"";//类型数据data
        $data["ms_certification"]?$querys["ms_certification"] = $data["ms_certification"]:"";//类型数据data
        $data["ms_forwarding"]?$querys["ms_forwarding"] = $data["ms_forwarding"]:"";//类型数据data
		//
        //约其他(ID:43)
        $data["name_278"]?$querys["name_278"] = $data["name_278"]:"";//类型数据data
        $data["name_279"]?$querys["name_279"] = $data["name_279"]:"";//类型数据data
        $data["name_440"]?$querys["name_440"] = $data["name_440"]:"";//类型数据data
		//
        //约活动(ID:42)
        $data["name_270"]?$querys["name_270"] = $data["name_270"]:"";//类型数据data
        $data["week"]?$querys["week"] = $data["week"]:"";//类型数据data
		strtotime($data["huo_add_time_s"])?$querys["time_s"] = strtotime($data["huo_add_time_s"]).(strtotime($data["huo_add_time_e"])?','.(strtotime($data["huo_add_time_e"])+86400):""):"";//修改时间 
		//
		/////////////////////////公用
		$data["profile_sex"]?$querys["profile_sex"] = $data["profile_sex"]:"";
		/////////////////////////
		//$data["order"]?$querys["order"] = $data["order"]:$querys["order"] = 'goods_id DESC 3';//排序方法 默认请填写
		$querys["order_by"] = $data["order"]?$data["order"]:3;
		$querys["order_type"] = $data["order_type"]==2?"ASC":"DESC";
		
		$querys["limit"] = $limit?$limit:"0,20";
		$querys["order_prefix"] = '';
		$querys["order_prefix_sql"] = '';
        switch($querys["type_id"])
		{
			case 31:
				$action_name = 'actions.MallFunction.searchMallModel';
				$data["prices_list"]?$querys["scope_id"] = $data["prices_list"]:"";//价格
			break;
			case 12:
				$action_name = 'actions.MallFunction.searchMallRent';
				$data["prices_list"]?$querys["scope_id"] = $data["prices_list"]:"";//价格
			break;
			case 3:
				$action_name = 'actions.MallFunction.searchMallDressing';
				//$data["prices"]?$querys["prices"] = implode(',',$data["prices"]):"";//价格
				$data["prices_list"]?$querys["scope_id"] = $data["prices_list"]:"";//价格
			break;
			case 5:
				$action_name = 'actions.MallFunction.searchMallCultivate';
				$data["prices"]?$querys["prices"] = implode(',',$data["prices"]):"";//价格
				if($querys["name_62"] == 63)
				{
					unset($querys["location_id"]);
				}
			break;
			case 40:
				$action_name = 'actions.MallFunction.searchMallShoot';
				//$data["prices"]?$querys["prices"] = implode(',',$data["prices"]):"";//价格
				$data["prices_list"]?$querys["scope_id"] = $data["prices_list"]:"";//价格
			break;
			case 41:
				$action_name = 'actions.MallFunction.searchMallFoot';
				//$data["prices"]?$querys["prices"] = implode(',',$data["prices"]):"";//价格
				$data["prices_list"]?$querys["scope_id"] = $data["prices_list"]:"";//价格
			break;
			case 42:
				$action_name = 'actions.MallFunction.searchMallEvent';
				//$data["prices"]?$querys["prices"] = implode(',',$data["prices"]):"";//价格
				$data["prices_list"]?$querys["scope_id"] = $data["prices_list"]:"";//价格
				$querys["order_prefix"] = 'is_over ASC 4,';
				$querys["order_prefix_sql"] = 'is_over ASC,';
			break;
			case 43:
				$action_name = 'actions.MallFunction.searchMallOrder';
				//$data["prices"]?$querys["prices"] = implode(',',$data["prices"]):"";//价格
				$data["prices_list"]?$querys["scope_id"] = $data["prices_list"]:"";//价格
			break;
			default:
				$action_name = 'actions.MallFunction.searchMallNew';
				$data["prices"]?$querys["prices"] = implode(',',$data["prices"]):"";//价格
			break;	
		}
		
	
		if($data["debug"])
		{
			//$data['search_type'] = "sql";
			$querys["debug"]=$data["debug"];
			$querys["debug_action"]=$data["debug"];
			print_r($data);
			print_r($querys);
			echo $order_by_type."<br>";
			echo $action_name."<br>";
		}
		
/*		$task_obj = POCO::singleton('pai_mall_seller_class');
		$system_conf = $task_obj->get_system_config();
		$fulltext = $system_conf['FULLTEXT_GOODS'];	
*/
		$fulltext = 1;	
		if(!$fulltext or $data['search_type'] == "sql")
		{
			$return = $this->search_goods_list_for_sql($data,$querys);
			return $return;
		}
        $return = $this->search_goods_list_for_fulltext($action_name,$querys);
		return $return;
	}
	
	

	/**
	 * 搜索商品列表
	 * @param array $data(type_id,location_id,detail_type_id,keyword)
	 * @return array
	 */
	public function search_goods_list_for_fulltext($action_name,$querys)
	{
		switch($querys["order_by"])
		{
			case 1:
				$querys["order"] = $querys["order_prefix"].'goods_id '.$querys["order_type"].' 3';//商品id
			break;
			case 2:
				$querys["order"] = $querys["order_prefix"].'new_bill_finish_num '.$querys["order_type"].' 4,goods_id DESC 3';//完成量
			break;
			case 3:
				$querys["order"] = $querys["order_prefix"].'bill_pay_num '.$querys["order_type"].' 4,goods_id DESC 3';//购买量
			break;
			case 4:
				$querys["order"] = $querys["order_prefix"].'total_average_score '.$querys["order_type"].' 5,goods_id DESC 3';//评价
			break;
			case 5:
				$querys["order"] = $querys["order_prefix"].'update_time '.$querys["order_type"].' 4,goods_id DESC 3';//更新
			break;
			case 6:
				$querys["order"] = $querys["order_prefix"].'_SCORE,step '.$querys["order_type"].' 5,goods_id DESC 3';//更新
				//$querys["order"] = 'step '.$querys["order_type"].' 5,goods_id DESC 3';//更新
			break;
			case 7:
				$querys["order"] = $querys["order_prefix"].'view_num '.$querys["order_type"].' 4,goods_id DESC 3';//更新
			break;
			case 8:
				$querys["order"] = $querys["order_prefix"].'prices '.$querys["order_type"].' 4,goods_id DESC 3';//更新
			break;
			case 9:
				$querys["order"] = $querys["order_prefix"].'add_time '.$querys["order_type"].' 4,goods_id DESC 3';//更新
			break;
			default:
				$querys["order"] = $querys["order_prefix"].'bill_buy_num '.$querys["order_type"].' 4,goods_id DESC 3';
			break;
		}
		include_once(G_YUEYUE_ROOT_PATH . "/core/include/fulltext_search_helper/lucoservice/real_search_client.class.php");
		$fulltext_config = array(
		                         'conf'=>'mall',
								 );
		if($querys["debug_action"] == 'mall_test')
		{
			$fulltext_config = array(
									 'conf'=>'mall_test',
									 );
			if($action_name == 'actions.MallFunction.searchMallNew')
			{
				$action_name = 'actions.TestFunction.searchMallNew';
				$querys["order"] = '_SCORE,step '.$querys["order_type"].' 5,goods_id DESC 3';//更新
			}
		}
		$lucoclient_server_conf = $GLOBALS['LUCOCLIENT_SERVER_CONFIG'][$fulltext_config['conf']];
		$client = new LucoClient($lucoclient_server_conf['host'], $lucoclient_server_conf['port']);
		
		$res = $client ->searchFun($action_name,$querys);
		$client->close();
		if($querys["debug"])
		{
			print_r($action_name);
			print_r($querys);
			//print_r($res->resultRow);
		}
		//print_r($querys);
		//echo $action_name;
		$return = array(
		                'total'=>$res->total,
		                'data'=>$res->resultRow,
						);
		return $return;
	}
	

	/**
	 * 搜索商品列表
	 * @param array $data(type_id,location_id,detail_type_id,keyword)
	 * @return array
	 */
	private function search_goods_list_for_sql($data,$querys)
	{
		$where = "1";
		$re = $this->select_which_tbl($querys["type_id"]);
		if($re)
		{
			$querys["view_num"]?$where .= " AND `view_num`>=".intval($querys["view_num"]):"";
		}
		///////////////////////////////////
		if($data["area_id"])
		{
			//$data["location_id"] = "(FIND_IN_SET('0',location_id) OR FIND_IN_SET('".substr($data["location_id"],0,6)."',location_id) OR FIND_IN_SET('".$data["location_id"]."',location_id))";
			$data["location_id"] = "(FIND_IN_SET('0',location_id) OR FIND_IN_SET('".$data["area_id"]."',location_id))";
		}
		elseif($data["location_id"])
		{
			//$data["location_id"] = "(FIND_IN_SET('0',location_id) OR FIND_IN_SET('".substr($data["location_id"],0,6)."',location_id) OR FIND_IN_SET('".$data["location_id"]."',location_id))";
			$data["location_id"] = "(FIND_IN_SET('0',location_id) OR FIND_IN_SET('".$data["location_id"]."',location_id))";
		}
		elseif($data["city"])
		{
			$data["location_id"] = "(FIND_IN_SET('0',location_id) OR FIND_IN_SET('".$data["city"]."',location_id))";
		}
		//////////////////////////////////
		$data["location_id"]?$querys["location_id"]=$data["location_id"]:"";
        switch($querys["type_id"])
		{
			case 31:
				//模特服务(ID:31)
				$querys["name_46"]?$where .= " AND `name_46`='".intval($querys["name_46"])."'":"";
				$querys["name_58"]?$where .= " AND `name_58`='".intval($querys["name_58"])."'":"";
				$querys["name_75"]?$where .= " AND FIND_IN_SET('".intval($querys["name_75"])."',name_75)":"";
				$querys["m_bwh"]?$where .= " AND `m_bwh`='".pai_mall_change_str_in($querys["m_bwh"])."'":"";
				$querys["m_cup"]?$where .= " AND `m_cup`='".pai_mall_change_str_in($querys["m_cup"])."'":"";
				$querys["m_cups"]?$where .= " AND `m_cups`='".pai_mall_change_str_in($querys["m_cups"])."'":"";
				$querys["m_sex"]?$where .= " AND `m_sex`='".pai_mall_change_str_in($querys["m_sex"])."'":"";
				if($querys["m_height"])
				{
					$m_height = explode(',',$querys["m_height"]);
					$where .= " AND `m_height`>=".intval($m_height[0]);
					$m_height[1]?$where .= " AND `m_height`<=".intval($m_height[1])."":"";
				}
				$querys["m_level"]?$where .= " AND `m_level`>=".intval($querys["m_level"])."":"";
				if($querys["m_weight"])
				{
					$m_weight = explode(',',$querys["m_weight"]);
					$where .= " AND `m_weight`>=".intval($m_weight[0]);
					$m_weight[1]?$where .= " AND `m_weight`<=".intval($m_weight[1])."":"";
				}
				$querys["scope_id"]?$where .= " AND FIND_IN_SET('".intval($querys["scope_id"])."',scope_id)":"";//价格	
			break;
			case 12:
				//影棚租赁(ID:12)
				$querys["name_17"]?$where .= " AND `name_17`='".intval($querys["name_17"])."'":"";
				//$querys["name_19"]?$where .= " AND `name_19`='".intval($querys["name_19"])."'":"";
				if($querys["name_19"])
				{
					$name_19 = explode(',',$querys["name_19"]);
					$where .= " AND `name_19`>=".intval($name_19[0]);
					$name_19[1]?$where .= " AND `name_19`<=".intval($name_19[1])."":"";
				}
	
				$querys["name_20"]?$where .= " AND `name_20`='".intval($querys["name_20"])."'":"";
				$querys["name_35"]?$where .= " AND FIND_IN_SET('".intval($querys["name_35"])."',name_35)":"";
				$querys["scope_id"]?$where .= " AND FIND_IN_SET('".intval($querys["scope_id"])."',scope_id)":"";//价格	
			break;
			case 3:
				//化妆服务(ID:3  )
				$querys["name_68"]?$where .= " AND `name_68`='".intval($querys["name_68"])."'":"";
				$querys["name_152"]?$where .= " AND FIND_IN_SET('".intval($querys["name_152"])."',name_152)":"";
/*				if($querys["prices"])
				{
					$prices = explode(',',$querys["prices"]);
					$where .= " AND `prices`>=".intval($prices[0]);
					$prices[1]?$where .= " AND `prices`<=".intval($prices[1])."":"";
				}
*/	
				$querys["scope_id"]?$where .= " AND FIND_IN_SET('".intval($querys["scope_id"])."',scope_id)":"";//价格	
			break;
			case 5:
				//摄影培训(ID:5  )
				$querys["name_62"]?$where .= " AND `name_62`='".intval($querys["name_62"])."'":"";
				$querys["name_133"]?$where .= " AND FIND_IN_SET('".intval($querys["name_133"])."',name_133)":"";
				$querys["name_382"]?$where .= " AND FIND_IN_SET('".intval($querys["name_382"])."',name_382)":"";
				$querys["name_148"]?$where .= " AND `name_148`='".intval($querys["name_148"])."'":"";
				$querys["name_317"]?$where .= " AND `name_317`='".intval($querys["name_317"])."'":"";
				$querys["t_teacher"]?$where .= " AND `t_teacher`='".pai_mall_change_str_in($querys["t_teacher"])."'":"";
				$querys["t_experience"]?$where .= " AND `t_experience`='".pai_mall_change_str_in($querys["t_experience"])."'":"";
				if($querys["name_62"] == 63)
				{
					unset($querys["location_id"]);
				}
				if($querys["prices"])
				{
					$prices = explode(',',$querys["prices"]);
					$where .= " AND `prices`>=".intval($prices[0]);
					$prices[1]?$where .= " AND `prices`<=".intval($prices[1])."":"";
				}
			break;
			case 40:
				//摄影服务(ID:40)
				$querys["name_90"]?$where .= " AND FIND_IN_SET('".intval($querys["name_90"])."',name_90)":"";
				//$querys["name_90"]?$where .= " AND `name_90`='".intval($querys["name_90"])."'":"";
				$querys["name_99"]?$where .= " AND `name_99`='".intval($querys["name_99"])."'":"";
				$querys["name_102"]?$where .= " AND `name_102`='".intval($querys["name_102"])."'":"";
				$querys["name_106"]?$where .= " AND `name_106`='".intval($querys["name_106"])."'":"";
				$querys["name_289"]?$where .= " AND FIND_IN_SET('".intval($querys["name_289"])."',name_289)":"";
				$querys["name_309"]?$where .= " AND FIND_IN_SET('".intval($querys["name_309"])."',name_309)":"";
				$querys["p_experience"]?$where .= " AND `p_experience`='".pai_mall_change_str_in($querys["p_experience"])."'":"";
				$querys["p_goodat"]?$where .= " AND `p_goodat`='".pai_mall_change_str_in($querys["p_goodat"])."'":"";
				$querys["scope_id"]?$where .= " AND FIND_IN_SET('".intval($querys["scope_id"])."',scope_id)":"";//价格	
			break;
			case 41:
				//约美食(ID:41)
				$querys["name_219"]?$where .= " AND `name_219`='".intval($querys["name_219"])."'":"";
				$querys["name_220"]?$where .= " AND `name_220`='".intval($querys["name_220"])."'":"";
				$querys["name_250"]?$where .= " AND `name_250`='".intval($querys["name_250"])."'":"";
				$querys["ms_experience"]?$where .= " AND `ms_experience`='".pai_mall_change_str_in($querys["ms_experience"])."'":"";
				$querys["ms_certification"]?$where .= " AND `ms_certification`='".pai_mall_change_str_in($querys["ms_certification"])."'":"";
				$querys["ms_forwarding"]?$where .= " AND `ms_forwarding`='".pai_mall_change_str_in($querys["ms_forwarding"])."'":"";
				$querys["scope_id"]?$where .= " AND FIND_IN_SET('".intval($querys["scope_id"])."',scope_id)":"";//价格	
			break;
			case 43:
				//约美食(ID:41)
				$querys["name_278"]?$where .= " AND FIND_IN_SET('".intval($querys["name_278"])."',name_278)":"";
				$querys["name_279"]?$where .= " AND FIND_IN_SET('".intval($querys["name_279"])."',name_279)":"";
				$querys["name_440"]?$where .= " AND name_440 ='".intval($querys["name_440"])."'":"";
				$querys["scope_id"]?$where .= " AND FIND_IN_SET('".intval($querys["scope_id"])."',scope_id)":"";//价格	
			break;
			default:
				$querys["type_id"]?$where .= " AND `type_id`='".intval($querys["type_id"])."'":"";
				if($querys["prices"])
				{
					$prices = explode(',',$querys["prices"]);
					$where .= " AND `prices`>=".intval($prices[0]);
					$prices[1]?$where .= " AND `prices`<=".intval($prices[1])."":"";
				}
			break;	
		}
		
		//////
		$querys["keywords"]?$where .= " AND (titles like '%".pai_mall_change_str_in($querys["keywords"])."%' or introduction like '%".pai_mall_change_str_in($querys["keywords"])."%' or demo like '%".pai_mall_change_str_in($querys["keywords"])."%' or keyword like '%".pai_mall_change_str_in($querys["keywords"])."%' or content like '%".pai_mall_change_str_in($querys["keywords"])."%')":"";
		$querys["location_id"]?$where .= " AND ".$querys["location_id"]:"";
		isset($querys["is_black"]) and in_array($querys["is_black"],array(0,1))?$where .= " AND `is_black`='".intval($querys["is_black"])."'":"";
		//isset($querys["goods_status"]) and $querys["goods_status"]!=10?$where .= " AND `status`='".intval($querys["goods_status"])."'":"";
		isset($querys["status"]) and $querys["status"]!=10?$where .= " AND `status`='".intval($querys["status"])."'":"";
		$querys["is_show"]?$where .= " AND `is_show`='".intval($querys["is_show"])."'":"";
		$querys["user_id"]?$where .= " AND `user_id`='".intval($querys["user_id"])."'":"";
		$querys["goods_id"]?(is_array($querys["goods_id"])?$where .= " AND `goods_id` in (".pai_mall_change_str_in(implode(',',$querys["goods_id"])).")":$where .= " AND `goods_id`='".intval($querys["goods_id"])."'"):"";
		//////
		if($querys["onsale_time"])
		{
			$onsale_time = explode(',',$querys["onsale_time"]);
			$where .= " AND `onsale_time`>=".intval($onsale_time[0]);
			$onsale_time[1]?$where .= " AND `onsale_time`<=".intval($onsale_time[1])."":"";
		}
		if($querys["add_time"])
		{
			$add_time = explode(',',$querys["add_time"]);
			$where .= " AND `add_time`>=".intval($add_time[0]);
			$add_time[1]?$where .= " AND `add_time`<=".intval($add_time[1])."":"";
		}
		if($querys["audit_time"])
		{
			$audit_time = explode(',',$querys["audit_time"]);
			$where .= " AND `audit_time`>=".intval($audit_time[0]);
			$audit_time[1]?$where .= " AND `audit_time`<=".intval($audit_time[1])."":"";
		}
		if($querys["update_time"])
		{
			$update_time = explode(',',$querys["update_time"]);
			$where .= " AND `update_time`>=".intval($update_time[0]);
			$update_time[1]?$where .= " AND `update_time`<=".intval($update_time[1])."":"";
		}
		//
		switch($querys["order_by"])
		{
			case 1:
				$querys["order"] = $querys["order_prefix_sql"].'goods_id DESC';//商品id
			break;
			case 2:
				$querys["order"] = $querys["order_prefix_sql"].'new_bill_finish_num '.$querys["order_type"].',goods_id DESC';//完成量
			break;
			case 3:
				$querys["order"] = $querys["order_prefix_sql"].'bill_pay_num '.$querys["order_type"].',goods_id DESC';//购买量
			break;
			case 4:
				$querys["order"] = $querys["order_prefix_sql"].'total_average_score '.$querys["order_type"].',goods_id DESC';//评价
			break;
			case 5:
				$querys["order"] = $querys["order_prefix_sql"].'update_time '.$querys["order_type"].',goods_id DESC';//更新
			break;
			case 6:
				$querys["order"] = $querys["order_prefix_sql"].'step '.$querys["order_type"].',goods_id DESC';//等级
			break;
			case 7:
				$querys["order"] = $querys["order_prefix_sql"].'view_num '.$querys["order_type"].',goods_id DESC';//人气
			break;
			case 8:
				$querys["order"] = $querys["order_prefix_sql"].'prices '.$querys["order_type"].',goods_id DESC';//价格
			break;
			case 9:
				$querys["order"] = $querys["order_prefix_sql"].'add_time '.$querys["order_type"].',goods_id DESC';//更新
			break;
			default:
				$querys["order"] = $querys["order_prefix_sql"].'bill_buy_num '.$querys["order_type"].',goods_id DESC';
			break;
		}
		if($data["debug"])
		{
			print_r($querys);
			echo $where;
		}
		if(!$re)
		{
			$this->set_mall_goods_tbl();
			$querys["order"] = 'goods_id DESC';
		}
		$total = $this->findCount($where);
		$resultRow = $this->findAll($where, $querys["limit"], $querys["order"]);
		if(!$re and $resultRow)
		{
			foreach($resultRow as $key => $val)
			{
				$goods_statistical = $this->get_goods_statistical($val['goods_id']);
				$goods_statistical?$resultRow[$key]+=$goods_statistical:"";
			}
		}
		
		$return = array(
		                'total'=>$total,
		                'data'=>$resultRow,
						);
		//print_r($return);
		return $return;
	}

	/*
	 * 根据type_id选择不同的库操作
	 * @param int $type_id
	 * @return bool
	 */
    private function select_which_tbl($type_id)
    {
		$type_data = $this->_type_update_data[$type_id];
		if($type_data['online'])
		{
			$fun_name = "mall_goods_".$type_id."_tbl";
			$this->setTableName($fun_name);
			return true;
		}
		else
		{
			return false;
		}

	}
	
	/*
	 * 分享最终页文案
	 */
	public function get_share_text($goods_id)
	{
        $goods_id = (int)$goods_id;

		$info=$this->get_goods_info($goods_id);
		$goods_title = $info['goods_data']['titles'];
		$share_img = $info['goods_data']['images'];
		

		$title = "{$goods_title} | 约约";

		$content = '上约约，给你的生活加点料吧。';

		$sina_content = "{$goods_title}，上约约，给你的生活加点料吧。";
		$share_url = 'http://www.yueus.com/goods/'.$goods_id;
		
		$url = str_replace("image19-d.yueus.com","image19.yueus.com",$share_img);
		
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


    /*
     * 列表分享
     */
    public function get_goods_list_share_text($seller_user_id,$type_id,$list_title,$share_img)
    {
        $seller_user_id = (int)$seller_user_id;
        $type_id = (int)$type_id;

        $title = "{$list_title} | 约约";

        $content = '这里有好多达人，快来看看吧';

        $sina_content = "{$list_title}，这里有好多达人，快来看看吧。";
        $share_url = 'http://www.yueus.com/list/'.$seller_user_id;

        if(!$share_img)
        {
            $share_img = "http://www.yueus.com/yue_admin/images/logo.png";
        }

        $url = str_replace("image19-d.yueus.com","image19.yueus.com",$share_img);

        $share_text['title'] = $title;
        $share_text['content'] = $content;
        $share_text['sina_content'] = $sina_content.' '.$share_url;
        $share_text['remark'] = '';
        $share_text['url'] = $share_url;
        $share_text['img'] = $url;
        $share_text['user_id'] = $seller_user_id;
        $share_text['qrcodeurl'] = $share_url;

        return $share_text;
    }

    /*
     * 分享列表页文案
     */
    public function get_list_share_text($pid,$return_query,$list_title,$share_img)
    {
        $pid = (int)$pid;

        $title = "{$list_title} | 约约";

        $content = "在约约，找到你最需要的服务。";

        $sina_content = "{$list_title}，在约约，找到你最需要的服务。";

        $share_url = 'http://www.yueus.com/list/'.$return_query.'/'.$pid;
        
        if(!$share_img)
        {
            $share_img = "http://www.yueus.com/yue_admin/images/logo.png";
        }

        $url = str_replace("image19-d.yueus.com","image19.yueus.com",$share_img);

        $share_text['title'] = $title;
        $share_text['content'] = $content;
        $share_text['sina_content'] = $sina_content.' '.$share_url;
        $share_text['remark'] = '';
        $share_text['url'] = $share_url;
        $share_text['img'] = $url;
        $share_text['qrcodeurl'] = $share_url;

        return $share_text;
    }
    
    /**
     * 组装商品列表前台的数据
     * @param type $list
     * @return type
     */
    public function goods_data_for_front_packing($list)
    {
        $mall_seller_obj 	= POCO::singleton('pai_mall_seller_class');
		$task_goods_obj 	= POCO::singleton('pai_mall_goods_class');
        $mall_api_obj 	= POCO::singleton('pai_mall_api_class');
        $default_cover 	= $mall_seller_obj->_seller_cover; 
        
        if( ! empty($list['data']) )
        {
            foreach($list['data'] as $k => &$v)
            {
				//print_r($v);
                $name = get_seller_nickname_by_user_id($v['user_id']);
                $cover = empty($v['images']) ? $default_cover : $v['images'];
                $price_str = sprintf('%.2f', $v['prices']);
                $prices_list = unserialize($v['prices_list']);
                if (!empty($prices_list))
                {
                    $min = 0;
                    foreach ($prices_list as $pv) {
                        $pv = intval($pv);
                        if ($pv <= 0) {
                            continue;
                        }
                        $min = ($min > 0 && $min < $pv) ? $min : $pv;
                    }
                    if ($min > 0) {
                        $price_str = sprintf('%.2f', $min) . '元 起';
                    }
                }
                
                if($v['review_times'])
                {
                    $score = sprintf('%.1f', ceil($v['total_overall_score'] / $v['review_times'] * 2) / 2);
                }
                else
                {
                    $score = "5.0";
                }
                $buy_num = $v['bill_pay_num'];
                
                $v['seller'] = $name ? $name : '商家';
				$v['titles'] = '[' . $task_goods_obj->get_goods_typename_for_type_id($v[type_id]) .  ']' . preg_replace('/&#\d+;/', '', $v['titles']);
				$v['images'] =  yueyue_resize_act_img_url($cover, '640');
				$v['link']  =  'yueyue://goto?goods_id=' . $v['goods_id'] . '&pid=1220102&type=inner_app';
				$v['prices'] = '￥' . $price_str;
				$v['buy_num'] = $buy_num > 0 ? '已有' . $buy_num . '人购买' : $name;
				$v['step'] = $score . ' 分';
				//$v['bill_finish_num'] = '已售:' . ($v['old_bill_finish_num'] + $v['bill_pay_num']);
                $v['bill_finish_num'] = '已售:' . $buy_num;
                $v['seller_img'] =  get_seller_user_icon($v['user_id']);
                

            }
        }
        
        return $list;
    }
    
//////////////////////////异步/////////////////////////////////////////////////////////////	
	/*
	 * 同步商品数量
	 */	
	public function exec_cmd_pai_mall_synchronous_goods($goods_id,$type_id,$type=1,$cache=true)
	{
		$cache?$this->del_goods_cache($goods_id):"";
		//$this->add_goods_update_log($goods_id,$type_id,$type);
		//return true;
		$pai_gearman_obj = POCO::singleton('pai_gearman_class');		
		$cmd_type = 'pai_mall_synchronous_goods';		
		$cmd_params = array(
		                    'goods_id' => $goods_id,
							'type_id' => $type_id,
							'type' => $type,
							);
		$send_rst = $pai_gearman_obj->send_cmd($cmd_type,$cmd_params);
		return true;
	}	
	
	/*
	 * 同步收藏夹上架时间
	 */	
	public function exec_cmd_pai_mall_follow_user_showtime($user_id,$goods_id)
	{
		$pai_gearman_obj = POCO::singleton('pai_gearman_class');		
		$cmd_type = 'pai_mall_follow_user_showtime';		
		$cmd_params = array(
		                    'user_id' => $user_id,
							'goods_id' => $goods_id,
							);
		$send_rst = $pai_gearman_obj->send_cmd($cmd_type,$cmd_params);
		return true;
	}

	
	/*
	 * 同步POCO活动
	 */	
	public function synchronous_poco_event($goods_id)
	{
		$goods_data = $this->get_goods_info($goods_id);
		$event_obj = POCO::singleton ( 'event_details_class' );		
		$data['title'] = $goods_data['goods_data']['titles']; //标题
		$type_name = 'photo';
		foreach((array)$goods_data['goods_data']['detail'] as $val)
		{
			if($val['type_id'] == 270)
			{
				if(in_array($val['data_type_attribute_id'],array(372)))
				{
					$type_name = "food";
					break;
				}
				elseif(in_array($val['data_type_attribute_id'],array(373)))
				{
					$type_name = "pet";
					break;
				}
			}
		}
		$data['type_icon'] = $type_name ; //分类标识 摄影为photo,美食为food
		//
		$f_time = 0;
		$e_time = 0;
		foreach((array)$goods_data['goods_data']['prices_de'] as $val_p)
		{
			if(!$f_time)
			{
				$f_time = $val_p['time_s'];
			}
			elseif($val_p['time_s']<$f_time)
			{
				$f_time = $val_p['time_s'];
			}
			if(!$e_time)
			{
				$e_time = $val_p['time_e'];
			}
			elseif($val_p['time_e']>$e_time)
			{
				$e_time = $val_p['time_e'];
			}
		}
		//
		$data['start_time'] = $f_time;//开始时间
		$data['end_time'] = $e_time;//结束时间
		$data['cover_image'] = yueyue_resize_act_img_url($goods_data['goods_data']['images'], 145);//封面图，尺寸传145
		$data['location_id'] = $goods_data['goods_data']['location_id'];//地区ID
		$data['address'] = $goods_data['goods_att'][272];//地址
		$data['budget'] = $goods_data['goods_data']['prices'];//价格
		$data['content'] = $goods_data['goods_data']['content'];//活动内容
		$data['goods_id'] = $goods_id;//
		$data['join_count'] = $goods_data['goods_data']['stock_num_total']-$goods_data['goods_data']['stock_num'];;//已参加人数
		$data['limit_num'] = $goods_data['goods_data']['stock_num_total'];//总参加人数
			
		//添加
		$ret=$event_obj->add_synchronous_event($data);
		$re[] = $data;
		$re[] = $ret;
		pai_log_class::add_log($re, 'synchronous_poco_event', 'synchronous_poco_event');	
		return true;
	}	
//////////////////////////榜单/////////////////////////////////////////////////////////////	
	/*
	 * 新品榜单
	 */	
	function newgoods_list($data,$limit='0,10')
	{
		$type_id = (int)$data['type_id'];
		$data['order'] = 1;
		if($type_id)
		{
			$re = $this->user_search_goods_list($data,$limit);
			return $re;
		}
		return array();
	}

	/*
	 * 热销榜单
	 */	
	function hotgoods_list($data,$limit='0,10')
	{
		$seller_obj = POCO::singleton ( 'pai_mall_order_class' );
		$type_id = (int)$data['type_id'];
		$start_time = strtotime($data['start_time'])?strtotime($data['start_time']):time()-604800;
		$end_time = strtotime($data['end_time'])?strtotime($data['end_time']):time();
		$goods_list = $seller_obj->get_goods_sales_ranking($type_id, $start_time, $end_time, '0,100');
		$return = array();
		$goods = array();
		if($goods_list)
		{
			$goods_id = array();
			foreach($goods_list as $val)
			{
				$goods_id[] = $val['goods_id'];
				$goods[$val['goods_id']] = $val;
			}
			$data['goods_id'] = $goods_id;
			$return = $this->user_search_goods_list($data,$limit);
			foreach($return['data'] as $key => $val)
			{
				$return['data'][$key]['sales_count'] = $goods[$val['goods_id']]['sales_count'];
			}
			aasort($return,array("-sales_count"));
		}
		return $return;
	}

//////////////////////////工具/////////////////////////////////////////////////////////////	
	/*
	 * 更新活动是否结束
	 */	
	function synchronous_goods_42()
	{
		$goods_list = $this->query("select * from mall_db.mall_goods_42_tbl");
		foreach($goods_list as $val)
		{
			$is_over = 0;
			if($val['e_time']<time())
			{
				$is_over = 1;
			}
			$sql = "update mall_db.mall_goods_42_tbl set is_over = {$is_over} where goods_id='".$val['goods_id']."';";
			//echo $sql."<br>";
			$this->query($sql);
			$this->add_goods_update_log($val['goods_id'],42,2,false);
		}
		return true;
	}
	
	/*
	 * 同步商品数量
	 */	
	function synchronous_goodsnum()
	{
		$seller = $this->query("select * from mall_db.mall_seller_tbl");
		foreach($seller as $val)
		{
			$goods_num = $this->query("select count(*) as num from mall_db.mall_goods_tbl where seller_id='".$val['seller_id']."'");
			if($goods_num[0]['num']>0)
			{
				$seller_sql = "update mall_db.mall_seller_tbl set goods_num = '".$goods_num[0]['num']."' where seller_id='".$val['seller_id']."';";
				$profile_sql = "update mall_db.mall_seller_profile_tbl set goods_num = '".$goods_num[0]['num']."' where seller_id='".$val['seller_id']."';";
				$this->query($seller_sql);
				$this->query($profile_sql);
				echo $val['seller_id'].'-->'.$goods_num[0]['num'].'--------ok<br>';
			}
		}
	}
	
	/*
	 * 同步商品上架数量
	 */	
	function synchronous_shownum()
	{
		$seller = $this->query("select * from mall_db.mall_seller_tbl");
		foreach($seller as $val)
		{
			$goods_num = $this->query("select count(*) as num from mall_db.mall_goods_tbl where is_show = 1 and seller_id='".$val['seller_id']."'");
			if($goods_num[0]['num']>0)
			{
				$seller_sql = "update mall_db.mall_seller_tbl set onsale_num = '".$goods_num[0]['num']."' where seller_id='".$val['seller_id']."';";
				$profile_sql = "update mall_db.mall_seller_profile_tbl set onsale_num = '".$goods_num[0]['num']."' where seller_id='".$val['seller_id']."';";
				$this->query($seller_sql);
				$this->query($profile_sql);
				echo $val['seller_id'].'-->'.$goods_num[0]['num'].'--------ok<br>';
			}
		}
	}
	
	/*
	 * 批量修改商品价格区间
	 */	
	function synchronous_goodsprices()
	{
		$s_p[3] = $this->get_goods_prices_scope_list(3);
		$s_p[5] = $this->get_goods_prices_scope_list(5);
		$s_p[12] = $this->get_goods_prices_scope_list(12);
		$s_p[31] = $this->get_goods_prices_scope_list(31);
		$s_p[40] = $this->get_goods_prices_scope_list(40);
		$s_p[41] = $this->get_goods_prices_scope_list(41);
		$s_p[43] = $this->get_goods_prices_scope_list(43);
		$seller = $this->query("select p.prices_id,p.goods_id,g.goods_id as gd,g.type_id,p.scope_id,p.prices from mall_db.mall_goods_prices_tbl as p,mall_db.mall_goods_tbl as g where p.goods_id=g.goods_id");
		foreach($seller as $val)
		{
			//echo $val['prices_id']."-->".$val['goods_id']."-->".$val['type_id']."-->".$val['gd']."-->".$val['prices']."-->".$val['scope_id']."<br>";
			$scope_id = $this->get_goods_prices_scope_id($s_p[$val['type_id']],$val['prices']);
			$up_sql = "update mall_db.mall_goods_prices_tbl set scope_id = '".$scope_id."' where prices_id='".$val['prices_id']."'";
			$this->query($up_sql);
			echo $up_sql."<br>";
		}		
	}
	
	/*
	 * 获取商品附加属性
	 */	
	function get_allgoods_att_id_for_46()
	{
		$goods_data = $this->query("SELECT g.goods_id,g.user_id,p.name,p.data,p.goods_type_id FROM mall_db.mall_goods_detail_tbl AS p,mall_db.mall_goods_tbl AS g WHERE p.goods_id=g.goods_id AND p.goods_type_id=46 order by p.goods_id desc");
		return $goods_data;
	}
	
	/*
	 * 批量更新商品价格
	 */	
	function update_allgoods_prices()
	{
		$goods_data = $this->query("SELECT p.goods_id,MIN(p.prices) as prices_s,MAX(p.prices) as prices_e FROM mall_db.mall_goods_prices_tbl AS p group by p.goods_id");
		foreach($goods_data as $val)
		{
			if($val['prices_s']>0)
			{
				echo "最小<br>";
				$gd = $this->query("SELECT prices,type_id from mall_db.mall_goods_tbl WHERE goods_id='".$val['goods_id']."' limit 0,1");
				if($gd[0]['prices']!=$val['prices_s'])
				{
					$up_sql = "update mall_db.mall_goods_tbl set prices = '".$val['prices_s']."' where goods_id='".$val['goods_id']."'";
					//$this->query($up_sql);
					echo $up_sql."<br>";
					//$this->exec_cmd_pai_mall_synchronous_goods($val['goods_id'],$gd[0]['type_id'],2);
				}
			}
			else
			{
				echo "价格零<br>";
				$gd = $this->query("SELECT prices FROM mall_db.mall_goods_prices_tbl where goods_id='".$val['goods_id']."' order by prices asc");
				foreach($gd as $val_de)
				{
					if($val_de['prices']>0)
					{
//						echo $val_de['prices']."<br>";
						$gd2 = $this->query("SELECT prices,type_id from mall_db.mall_goods_tbl WHERE goods_id='".$val['goods_id']."' limit 0,1");
						if($gd2[0]['prices']!=$val_de['prices'])
						{
							$up_sql = "update mall_db.mall_goods_tbl set prices = '".$val_de['prices']."' where goods_id='".$val['goods_id']."'";
							//$this->query($up_sql);
							echo $up_sql."<br>";
							//$this->exec_cmd_pai_mall_synchronous_goods($val['goods_id'],$gd2[0]['type_id'],2);
						}
						break;
					}
				}
				//print_r($gd);
				//exit;
				//$goods_data = $this->query("SELECT p.goods_id,MAX(p.prices) as prices_s FROM mall_db.mall_goods_prices_tbl AS p where p.goods_id=".$val['goods_id']." group by p.goods_id");
				//echo "最大<br>";
				//$up_sql = "update mall_db.mall_goods_tbl set prices = '".$val['prices_e']."' where goods_id='".$val['goods_id']."'";
				//$this->query($up_sql);
				//echo $up_sql."<br>";
			}
		}
	}
    
        
    /**
     * 插入与更新goods_type_id_tbl;
     * @param type $goods_id 商品id
     * @param type $type_id 类型id
     * @return boolean
     */
    public function batch_insert_or_update_goods_type_id_tbl($goods_id,$type_id=null)
    {
        $where_goods_id = '';
        $where_type_id = '';
        $goods_id = (int)$goods_id;
        $type_id = (int)$type_id;
        if($goods_id > 0)
        {
            $where_goods_id = " and g.goods_id='{$goods_id}'";
        }
		else if($type_id > 0)
        {
            $where_type_id = " and g.type_id='$type_id'";
        }
		else
        {
            return false;
        }
        $sql = "select s.*,g.*,s.prices as statistical_prices from mall_db.mall_goods_tbl as g left join mall_db.mall_goods_statistical_tbl as s on g.goods_id=s.goods_id where 1 $where_type_id $where_goods_id order by g.goods_id desc";
		$rs = $this->query($sql);
        if( ! empty( $rs ) )
        {
            foreach($rs as $k => $v)
            {
                $can_insert_arys = $this->_can_insert;
                foreach($v as $kv => $vv)
                {
                    if( ! in_array($kv,$can_insert_arys) )
                    {
                        //没有的字段就unset掉，防止sql报错
                        unset($v[$kv]);
                    }
					if(in_array($kv,array('location_id')))
					{
						if($v[$kv] != '')
						{
							if($kv == 'location_id')
							{
								$location_arr = explode(',',$v[$kv]);
								$location_str = array();
								foreach($location_arr as $val_lid)
								{
									if(strlen($val_lid)>9)
									{
										$location_str[] = substr($val_lid,0,6);
										$location_str[] = substr($val_lid,0,9);
										$location_str[] = $val_lid;
										//$location_str.=substr($val_lid,0,6).','.substr($val_lid,0,9).','.$val_lid;
									}
									elseif(strlen($val_lid)>6)
									{
										$location_str[] = substr($val_lid,0,6);
										$location_str[] = $val_lid;
										//$location_str.=substr($val_lid,0,6).','.$val_lid;
									}
									else
									{
										$location_str[] = $val_lid;
										//$location_str.=$val_lid;
									}
								}
								$v[$kv] = implode(',',array_unique($location_str));
							}
							$v[$kv] = ','.$v[$kv].',';
						}
 					}
					if($kv == "bill_finish_num")
					{
						$v['new_bill_finish_num'] = $v['old_bill_finish_num']+$v['bill_finish_num'];
 					}
                }
                $re = $this->select_which_tbl($v['type_id']);
				if(!$re)
				{
					return false;
				}
                $name_res = $this->batch_insert_property($v['goods_id'],$v['type_id']);
                $type_detail_arys = $this->_type_update_data[$v['type_id']]['goods_detail'];
                if( ! empty($type_detail_arys))
                {
                    foreach($type_detail_arys as $kd => $vd)
                    {
                        $v["name_{$vd}"] = implode(',', $name_res[$vd]);
						if(in_array($vd,array(90,133,289,278,270,382)))
						{
							$v["name_{$vd}"]?$v["name_{$vd}"] = ','.$v["name_{$vd}"].',':"";
						}
                    }
                }
                
                $detail_res = $this->batch_insert_seller_profile_detail($v['profile_id'],$v['type_id']);
                if( ! empty($detail_res) )
                {
                    $v = array_merge($v,$detail_res);
                }                
                //profile_sex
                $profile_sex_res = $this->batch_insert_profile_sex($v['profile_id']);
                if( ! empty($profile_sex_res) )
                {
                    $v['profile_sex'] = $profile_sex_res['profile_sex'];
                    $v['seller_name'] = $profile_sex_res['name'];
                }
				//exit;                
                //价格区间
                if( ! empty($this->_type_update_data[$v['type_id']]['scope_id']) )
                {
                    $scope_id_res = $this->batch_insert_scope($v['goods_id']);
                    if( ! empty($scope_id_res['scope_id']) )
                    {
                        $v['scope_id'] = implode(",", $scope_id_res['scope_id']);
						$v['scope_id'] = ','.$v['scope_id'].',';
                    }
                }
                if( ! empty($this->_type_update_data[$v['type_id']]['goods_price']))
                {
                    $goods_price_str = $this->batch_insert_goods_price($v['goods_id']);
                    $id = $this->_type_update_data[$v['type_id']]['goods_price'][0];
                    $v["name_{$id}"] =  $goods_price_str['price_str']?','.$goods_price_str['price_str'].',':"";
                }
                //活动时间
				if($v['type_id']==42)
				{
					$goods_price_str = $this->batch_insert_goods_price($v['goods_id']);
                    $v['time_list'] = serialize($goods_price_str['data']);
                    $v['f_time'] = 0;
					$v['e_time'] = 0;
					$v['is_over'] = 0;
					foreach($goods_price_str['data'] as $val_p)
					{
						if(!$v['f_time'])
						{
							$v['f_time'] = $val_p['time_s'];
						}
						elseif($val_p['time_s']<$v['f_time'])
						{
							$v['f_time'] = $val_p['time_s'];
						}
						if(!$v['e_time'])
						{
							$v['e_time'] = $val_p['time_e'];
						}
						elseif($val_p['time_e']>$v['e_time'])
						{
							$v['e_time'] = $val_p['time_e'];
						}
					}
					if($v['e_time']<time())
					{
						$v['is_over'] = 1;
					}
				}
				//
                $re = $this->select_which_tbl($v['type_id']);  
				if(!$re)
				{
					return false;
				}
                $this->insert($v,"REPLACE");
                unset($v);                
            }
        }
		else 
        {
            return false;
        }
        return true;      
    }
    
    /**
     * 批量查找产品价钱的属性id
     * @param type $goods_id
     * @return type
     */
    function batch_insert_goods_price($goods_id)
    {
        $price_str = '';
        $this->set_mall_goods_prices_tbl();
        $res = $this->findAll("goods_id='$goods_id'","","time_s asc,prices asc");
        foreach($res as $k => &$v)
        {
            $price_str .= $v['type_id'].",";
        }        
        if($price_str)
        {
            $price_str = substr($price_str,0,-1);
        }
        $return = array(
		                'price_str' => $price_str,
						'data' => $res,
						);
        return $return;
    }
    
	/**
	 * 获取商品属性 name_46 and name_58....
	 * @param type $goods_id 商品id
	 * @param type $type_id 类型id
	 * @return array
	 */
	function batch_insert_property($goods_id,$type_id)
	{
		$this->set_mall_goods_detail_tbl();
		
		$type_id_arys = $this->_type_update_data[$type_id]['goods_detail'];
		
		$type_id_where = implode(",",$type_id_arys);
		if( ! empty($type_id_where) )
		{
			$result = $this->findAll("goods_id='$goods_id' and type_id in ( $type_id_where ) ");
			foreach($result as $k => $v)
			{
				foreach($type_id_arys as $ka => $va)
				{
					if($v['type_id'] == $va)
					{
                        if( ! empty($v['data_type_attribute_id']))
                        {
                            $res[$va][] = $v['data_type_attribute_id'];
                        }else
                        {
                            //如果为空的应该是单项输入框,就输入data
                            $res[$va][] = $v['data'];
                        }
						
					}
					$res[$va] = array_unique($res[$va]);
				}
			}
		}		
		return $res;
	}
    
	/**
	 * 获取商品个人资料属性 m_cup,m_bwh...
	 * @param type $profile_id
	 * @param type $type_id 类型id
	 * @return array
	 */
    function batch_insert_seller_profile_detail($profile_id,$type_id)
    {
        $detail_res = array();
        
        $this->set_mall_seller_profile_detail_tbl();
        
		$name_where_array = array();
		if($this->_type_update_data[$type_id]['profile_detail'])
		{
			foreach($this->_type_update_data[$type_id]['profile_detail'] as $val)
			{
				$name_where_array[] = "'".$val."'";
			}
		}
        $name_where = implode(',',$name_where_array);
        
        if( ! empty($name_where) )
        {
            $rs = $this->findAll("profile_id='$profile_id' and name in ($name_where)");        
            if( ! empty($rs) )
            {
                foreach($rs as $k => $v)
                {
                    $detail_res[$v['name']] = $v['data'];
                }
            }
        }
        
        return $detail_res;
    }
    
	/**
	 * 获取商品性别属性
	 * @param type $profile_id
	 * @return array
	 */
    function batch_insert_profile_sex($profile_id)
    {
        $this->set_mall_seller_profile_tbl();
        $one = $this->find("seller_profile_id='$profile_id'");
        return array('profile_sex'=>$one['sex'],'name'=>$one['name']);
    }
    
	/**
	 * 获取商品价格区间
	 * @param type $goods_id
	 * @return array
	 */
    function batch_insert_scope($goods_id)
    {
        $this->set_mall_goods_prices_tbl();
        $rs = $this->findAll("goods_id='$goods_id'");
        if( ! empty($rs) )
        {
            foreach($rs as $k => $v)
            {
				if(is_numeric($v['scope_id']))
				{
					$scope_res['scope_id'][] = $v['scope_id'];
				}
				else
				{
					$scope_id = explode(',',$v['scope_id']);
					if($scope_id)
					{
						foreach($scope_id as $val)
						{
							$scope_res['scope_id'][] = $val;
						}
					}
				}
            }
            if( ! empty($scope_res) )
            {
                $scope_res['scope_id'] = array_unique($scope_res['scope_id']);
                return $scope_res;
            }
        }
    } 
	
    
	/*
	 * 同步商品数据
	 */	
	public function synchronous_goods($type_id='')
	{
		$type_id = (int)$type_id;
		//$sql = "SELECT g.`goods_id`,g.`type_id`,l.`goods_id` as l_goods_id,g.`add_time`,l.`add_time` as l_add_time,g.`update_time`,l.`update_time` as l_update_time,g.`audit_time`,l.`audit_time` as l_audit_time,g.`onsale_time`,l.`onsale_time` as l_onsale_time FROM mall_db.`mall_goods_".$type_id."_tbl` AS l,mall_db.`mall_goods_tbl` AS g WHERE g.`goods_id`=l.`goods_id` AND (g.`add_time`!=l.`add_time` OR g.`update_time`!=l.`update_time` OR g.`audit_time` !=l.`audit_time` OR g.`onsale_time`!=l.`onsale_time`);";
		//$sql = "SELECT s.*,d.type_id FROM mall_db.`mall_goods_statistical_tbl` as s,mall_db.`mall_goods_tbl` as d where s.goods_id=d.goods_id and s.old_bill_finish_num>0";
		//$sql = "SELECT * FROM mall_db.`mall_goods_tbl` where type_id=42";
		//$sql = "SELECT * FROM mall_db.`mall_goods_tbl` where goods_id in (2122762,2123875,2125891,2128487,2128429,2122759,2124012,2121983,2119984,2122759,2124274,2128429,2120195,2122762,2122763)";
		$sql = "SELECT * FROM mall_db.`mall_goods_tbl` where type_id = 43";
		$re = $this->query($sql);
		//$seller_obj = POCO::singleton("pai_mall_order_class");
		foreach($re as $val)
		{
			//$this->del_goods_cache($val['goods_id']);
			//$pay_num = $seller_obj->get_order_pay_num($val['goods_id']);
			//print_r($pay_num);
			echo $val['goods_id']."------".$val['type_id']."------".$pay_num['total']."------".$val['old_bill_finish_num']."<br>";
			//print_r($val);
			//$sql_up = "update mall_db.`mall_goods_statistical_tbl` set bill_pay_num=".$pay_num['total']."+old_bill_finish_num where goods_id='".$val['goods_id']."'";
			//echo $sql_up."<br>";
			//$this->query($sql_up);
			//$re = $this->set_goods_att_demo($val['goods_id']);
			//$this->exec_cmd_pai_mall_synchronous_goods($val['goods_id'],$val['type_id'],2);
			echo "----ok<br>";
			//if($pay_num['total'])
			//exit;
		}
		echo "<br>over<br>";
	}
	
	

    
}
