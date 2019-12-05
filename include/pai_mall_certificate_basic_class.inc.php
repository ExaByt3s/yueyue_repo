<?php
/**
 * 商家申请
 * 
 * @author ljl
 */

class pai_mall_certificate_basic_class extends POCO_TDG
{
	var $debug = false;
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
	public function set_db_test()
	{
		$this->setDBName('mall_test_db');
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
	private function set_mall_certificate_basic_tbl()
	{
		$this->setTableName('mall_certificate_basic_tbl');
	}
    
    private function set_mall_seller_tbl()
	{
		$this->setTableName('mall_seller_tbl');
	}
    
    private function set_mall_certificate_service_tbl()
    {
        $this->setTableName('mall_certificate_service_tbl');
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
	public function get_seller_list($b_select_count = false, $where_str = '', $order_by = 'basic_id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_mall_certificate_basic_tbl();
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
    
    /**
     * 获取详情
     * @param type $id
     * @return boolean
     */
    public function get_info($id)
    {
        $id = (int)$id;
        if( ! $id )
        {
            return false;
        }
        $this->set_mall_certificate_basic_tbl();
        return $this->find("basic_id='$id'");
    }
    
    /**
     * 更新备注
     * @param type $basic_id
     * @param type $remark
     * @return boolean
     */
    public function update_remark($basic_id,$remark)
    {
        $basic_id = (int)$basic_id;
        $remark = addslashes($remark);
        if( ! $basic_id || ! $remark)
        {
            return false;
        }
        $this->set_mall_certificate_basic_tbl();
        return $this->update(array('remark'=>$remark),"basic_id='$basic_id'");
    }
    
    /**
     * 更新状态
     * @param type $id
     * @param type $status
     * @return type
     */
    public function change_status($id,$status,$operator_id)
    {
        $this->set_mall_certificate_basic_tbl();
        $rs = $this->update(array('status'=>$status,'update_time'=>time(),'operator_id'=>$operator_id),"basic_id='$id'");
       
        return $rs;
    }
    
    /**
     * 检查能否申请个人或者企业
     * @param type $user_id
     * 
     */
    public function check_can_add($user_id)
    {
        $this->set_mall_certificate_basic_tbl();
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        
        $rs = $this->find("user_id='$user_id' and (status='0' or status='1') and (basic_type='person' or basic_type='company')");
        
        if($rs)
        {
           return $rs;
        }else
        {
            return false;
        }
        
    }
    
    /**
     * 根据用户id 拿状态
     * @param type $user_id
     * @return type
     */
    public function get_person_status_by_user_id($user_id)
    {
        $this->set_mall_certificate_basic_tbl();
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return array('status'=>-1,'msg'=>'参数不正确');
        }
        $rs = $this->find("user_id='$user_id' and ( basic_type='person' or basic_type='company' )",'basic_id desc');
        if($rs)
        {
            if($rs['status'] == 0)
            {
                return array('status' => 0,'msg'=>'未审核','basic_type'=>$rs['basic_type']);
            }else if($rs['status'] == 1)
            {
                return array('status' => 1,'msg'=>'通过','basic_type'=>$rs['basic_type']);
            }else if($rs['status'] == 2 )
            {
                return array('status' => 2,'msg'=>'不通过','basic_type'=>$rs['basic_type']);
            }
        }else
        {
            return array('status'=>-2,'msg'=>'没找到纪录');
        }
        
    }
    
    /**
     * 更新用户的手机号
     * @param type $user_id
     */
    public function update_user_phone($user_id)
    {
        $this->set_mall_certificate_basic_tbl();
        $mall_user_obj = POCO::singleton('pai_user_class');
        $phone =  $mall_user_obj->get_phone_by_user_id($user_id);
        $this->update(array('phone'=>$phone),"user_id='$user_id'");
    }
    
    /**
     * 检查身份证是否已经提交过
     * @param type $user_id
     * @param type $id_card
     * @return boolean
     */
    public function check_id_card_is_have($id_card)
    {
        $id_card = addslashes($id_card);
        if( ! $id_card )
        {
            return false;
        }
        $this->set_mall_certificate_basic_tbl();
        $basic_one = $this->find("( company_id_card='$id_card' or person_id_card='$id_card' ) and ( status='1' or status='0' )");
        if( $basic_one )
        {
            return true;
        }
        return false;
    }
    
	/**
	 * 添加商家申请
	 * @param array $data
	 * @return int 1成功 -1资料不全 -2插入失败 -3没有user_id -4已经申请过
	 */
	public function add_seller_sq($post)
	{
        
        $this->set_mall_certificate_basic_tbl();
        $mall_user_obj = POCO::singleton('pai_user_class');
        
        //一个类只能申请一次的判断
        
        $has_one = $this->check_can_add($post['user_id']);
        
        if( $has_one )
        {
            return array('status'=>-4,'msg'=>'已经申请过了');
        }
        
        //非空判断
        foreach($post as $k => $v)
        {
            if( $v === '' )
            {
                return array('status'=>-1,'msg'=>'资料不全');
            }
        }
        
        //身份证不能重复提交
        if($post['basic_type'] == 'person')
        {
            $id_card = addslashes($post['person_id_card']);
        }else if($post['basic_type'] == 'company')
        {
            $id_card = addslashes($post['company_id_card']);
        }
        $id_card_one = $this->check_id_card_is_have($id_card);
        if($id_card_one)
        {
            return array('status'=>-5,'msg'=>'身份证号重复提交了');
        }
        
        //数据过滤
        if($post['basic_type'] == 'person')
        {
            $data['person_true_name'] = addslashes($post['person_true_name']);
            $data['person_nick'] = addslashes($post['person_nick']);
            $data['person_area_id'] = (int)$post['person_area_id'];
            $data['person_zone_id'] = (int)$post['person_zone_id'];
            $data['person_id_card'] = addslashes($post['person_id_card']);
            $data['basic_type'] = addslashes($post['basic_type']);
            $data['user_id'] = (int)$post['user_id'];
            $data['phone'] = $mall_user_obj->get_phone_by_user_id($data['user_id']);
            $data['add_time'] = time();
            $data['status'] = 0;
            $data['brand_img_url'] = addslashes($post['brand_img_url']);
            $data['heads_img_url'] = addslashes($post['heads_img_url']);
            $data['tails_img_url'] = addslashes($post['tails_img_url']);
            
        }else if($post['basic_type'] == 'company')
        {
            $data['company_name'] = addslashes($post['company_name']);
            $data['company_license_num'] = addslashes($post['company_license_num']);
            $data['company_place'] = addslashes($post['company_place']);
            $data['company_date_line'] = strtotime($post['company_date_line']);
            $data['company_bank_id'] = (int)$post['company_bank_id'];
            $data['company_bank_area_id'] = (int)$post['company_bank_area_id'];
            $data['company_bank_city_id'] = (int)$post['company_bank_city_id'];
            $data['company_bank_name'] = addslashes($post['company_bank_name']);
            $data['company_card_num'] = addslashes($post['company_card_num']);
            $data['company_true_name'] = addslashes($post['company_true_name']);
            $data['company_id_card'] = addslashes($post['company_id_card']);
            $data['company_contact_name'] = addslashes($post['company_contact_name']);
            $data['company_contact_phone'] = addslashes($post['company_contact_phone']);
            $data['company_brand_name'] = addslashes($post['company_brand_name']);
            $data['basic_type'] = addslashes($post['basic_type']);
            $data['user_id'] = (int)$post['user_id'];
            $data['phone'] = $mall_user_obj->get_phone_by_user_id($data['user_id']);
            $data['add_time'] = time();
            $data['status'] = 0;
            $data['company_license_img_url'] = addslashes($post['company_license_img_url']);
            $data['brand_img_url'] = addslashes($post['brand_img_url']);
            $data['heads_img_url'] = addslashes($post['heads_img_url']);
            $data['tails_img_url'] = addslashes($post['tails_img_url']);
        }
        
        $id = $this->insert($data,'REPLACE');
        if($id)
        {
            $_add_data['add_user'] = '';
            $_add_data['company_num'] = 1;
            $_add_data['introduce'] = get_user_nickname_by_user_id($data['user_id'])." 的介绍";
            $_add_data['user_id'] = $data['user_id'];
            $_add_data['basic_type'] = $data['basic_type'];
            $_add_data['name'] = get_user_nickname_by_user_id($data['user_id']);
            $_add_data['basic_id'] = $id;
            $_add_data['avatar'] = get_user_icon($data['user_id'], 165);
            $_add_data['status'] = 3;
            $this->insert_seller($_add_data);
            unset($data);
            return array('status'=>1,'msg'=>'添加成功');
        }else
        {
            return array('status'=>-2,'msg'=>'添加失败');
        }
    }
    
    
    /**
     * 加入商家
     * @param type $data
     * @return type
     */
    public function insert_seller($_add_data)
    {
        $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
        return $mall_seller_obj->add_seller($_add_data);
    }
    
    /**
     * 身份证号码检查是否重复
     * @param type $id_card
     * @param type $basic_type
     * @return type
     */
    public function id_card_check($id_card)
    {
        $this->set_mall_certificate_basic_tbl();
        if( ! $id_card )
        {
            return array(
                'status' => '-1',
                'msg' =>'参数不对'
            );
        }
        $id_card = addslashes($id_card);
        
		$rs = $this->find("( status = '1' or status = '0') and (  person_id_card='$id_card' or company_id_card='$id_card' ) ");
		
        if($rs)
        {
            return array('status'=>1,'msg'=>'数据库已有');
        }else
        {
            return array('status'=>0,'msg'=>'数据库没有');
        }
        
    }


    /**
     * 批量开通服务
     * @param type $user_id 10009
     * @param type $type_id 31,40
     * @return type
     */
    public function batch_open_service($user_id,$basic_type=1,$data=array())
    {
        $user_id = (int)$user_id;
        /*$type_ary = explode(",",$type_id);
        if( empty($type_ary) )
        {
            return array('status'=>-3,'msg'=>'要开通的服务不能为空');
        }*/
        if( ! $user_id )
        {
            return array('status'=>-1,'msg'=>'参数不对');
        }

        //插入基础表
        $basic_id = $this->batch_insert_basic($user_id,$basic_type);

        if( is_int($basic_id) && $basic_id > 0)
        {
            //插入商家
            $this->batch_insert_seller($user_id,$basic_id,$data,$basic_type);

            //插入服务表
            //$this->batch_insert_service($user_id, $type_ary, $basic_id);

            //开通相应的服务
            //$this->batch_update_seller_type_id($user_id, $type_ary);
        }else
        {
            return $basic_id;
        }

        //代表成功
        return array('status'=>1);
    }


    /**
     * 批量插入基础验证
     * @param type $user_id
     */
    public function batch_insert_basic($user_id,$basic_type=1)
    {
        $this->set_mall_certificate_basic_tbl();
        $mall_user_obj = POCO::singleton('pai_user_class');

        $basic_one = $this->find("user_id = '$user_id' and status = '1' ","basic_id desc");
        if( ! empty($basic_one) )
        {
            return array('status'=>-2,'msg'=>'这个user_id:'.$user_id.",已经通过验证认证");
        }
        $insert_baisc_data['user_id'] = $user_id;
        if($basic_type==1)
        {
            $insert_baisc_data['basic_type'] = 'person';
        }
        else
        {
            $insert_baisc_data['basic_type'] = 'company';
        }

        $insert_baisc_data['add_time'] = time();
        $insert_baisc_data['update_time'] = time();
        $insert_baisc_data['status'] = 1;
        $insert_baisc_data['phone'] = $mall_user_obj->get_phone_by_user_id($user_id);

        $basic_id = $this->insert($insert_baisc_data);
        if( ! $basic_id )
        {
            return array('status'=>-5,'msg'=>'这个user_id:'.$user_id.",插入基础表失败");
        }
        unset($insert_baisc_data);

        return $basic_id;

    }

    /**
     * 插入商家表
     * @param type $user_id
     * @param type $basic_id
     */
    public function batch_insert_seller($user_id,$basic_id,$data=array(),$basic_type=1)
    {
        $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
        $this->debug?$mall_seller_obj->set_db_test():"";//开启调试

        $_add_data['add_user'] = $yue_login_id;
        $_add_data['company_num'] = 1;
        $_add_data['introduce'] = get_user_nickname_by_user_id($user_id)." 的介绍";
        $_add_data['user_id'] = $user_id;
        if($basic_type==1)
        {
            $_add_data['basic_type'] = 'person';
        }
        else
        {
            $_add_data['basic_type'] = 'company';
        }

        $_add_data['name'] = get_user_nickname_by_user_id($user_id);
        $_add_data['basic_id'] = $basic_id;
        $_add_data['introduce'] = $data['introduce'];
        $_add_data['avatar'] = $data['avatar'];
        $_add_data['cover'] = $data['cover'];

        $sell_id = $mall_seller_obj->add_seller($_add_data);
        if( $sell_id < 0 )
        {
            return array('status' =>-4,'msg'=>'这个user_id:'.$user_id.',basic_id:'.$basic_id.'插入商家表失败');
        }
        unset($_add_data);
    }

    /**
     * 批量插入服务表
     * @param type $user_id
     * @param type $type_id
     */
    public function batch_insert_service($user_id,$type_ary,$basic_id)
    {
        $service_static_arys = array(
            31=>'model',
            40=>'cameror',
            5=>'teacher',
            3=>'dresser',
            12=>'studio',
            41=>'diet',
            43=>'other',
        );

        foreach($type_ary as $k => $v)
        {
            $this->set_mall_certificate_service_tbl();
            $mall_user_obj = POCO::singleton('pai_user_class');

            $_service_data['service_type'] = $service_static_arys[$v];
            $_service_data['basic_id'] = $basic_id;
            $_service_data['user_id'] = $user_id;
            $_service_data['operator_id'] = $yue_login_id;
            $_service_data['status'] = 1;
            $_service_data['add_time'] = time();
            $_service_data['update_time'] = time();
            $_service_data['phone'] = $mall_user_obj->get_phone_by_user_id($user_id);
            $service_id = $this->insert($_service_data);
            if( ! $service_id )
            {
                return array('status' =>-6,'msg'=>'这个user_id:'.$user_id.',basic_id:'.$basic_id.'插入服务表失败');
            }
            unset($_service_data);
        }
    }

    /**
     * 批量更新type_id
     * @param type $user_id
     * @param type $type_ary
     */
    public function batch_update_seller_type_id($user_id,$type_ary)
    {
        $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
        $this->debug?$mall_seller_obj->set_db_test():"";//开启调试
        $mall_user_obj = POCO::singleton('pai_user_class');
        $pai_sms_obj = POCO::singleton('pai_sms_class');

        $seller_info = $mall_seller_obj->get_seller_info($user_id,2);

        $phone = $mall_user_obj->get_phone_by_user_id($user_id);
		
		$pro_type_id = explode(',', $seller_info['seller_data']['profile']['0']['type_id']);
		
		if(!array_diff($type_ary,$pro_type_id))
		{
			return true;
		}

		$res = $mall_seller_obj->update_seller_profile_type_id($seller_info['seller_data']['profile']['0']['seller_profile_id'],$type_ary);

		if($res['result'] == 1)
		{
			/*            //发送成功短信
						if( ! empty($phone))
						{
							$group_key = 'G_PAI_MALL_SELLER_CERTIFICATE_SERVICE_SUCCESS';
							$data = array(
								'date' => date('m月d日', time()),
							);
							//$ret = $pai_sms_obj->send_sms($phone, $group_key, $data);
						}
			*/
		}
		else
		{
			return array('status' =>-7,'msg'=>'这个user_id:'.$user_id.',type_id:'.implode(',',$type_ary).'更新type_id失败');
		}
        unset($res);
        unset($phone);
        unset($seller_info);
    }
	
}
