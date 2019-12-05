<?php
/**
 * �̼ҷ�������
 * 
 * @author ljl
 */

class pai_mall_certificate_service_class extends POCO_TDG
{
	
    
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}
    
    private function set_certificate_basic_tbl()
	{
		$this->setTableName('mall_certificate_basic_tbl');
	}

	private function set_certificate_service_tbl()
	{
		$this->setTableName('mall_certificate_service_tbl');
	}
    
    private function set_mall_certificate_service_cameror_tbl()
	{
		$this->setTableName('mall_certificate_service_cameror_tbl');
	}
	
	private function set_mall_certificate_service_dresser_tbl()
	{
		$this->setTableName('mall_certificate_service_dresser_tbl');
	}
	
	private function set_mall_certificate_service_img_tbl()
	{
		$this->setTableName('mall_certificate_service_img_tbl');
	}
    
    private function set_mall_certificate_service_model_tbl()
	{
		$this->setTableName('mall_certificate_service_model_tbl');
	}
	
	private function set_mall_certificate_service_studio_tbl()
	{
		$this->setTableName('mall_certificate_service_studio_tbl');
	}
	
	private function set_mall_certificate_service_teacher_tbl()
	{
		$this->setTableName('mall_certificate_service_teacher_tbl');
	}
    
    private function set_mall_goods_tbl()
    {
        $this->setTableName('mall_goods_tbl');
    }
    
    private function set_mall_goods_type_tbl()
    {
        $this->setTableName('mall_goods_type_tbl');
    }
	
	private function set_mall_certificate_service_diet_tbl()
	{
		$this->setTableName('mall_certificate_service_diet_tbl');
	}
    
    private function set_mall_certificate_service_other_tbl()
	{
		$this->setTableName('mall_certificate_service_other_tbl');
	}
    
    private function set_mall_seller_tbl()
    {
        $this->setTableName('mall_seller_tbl');
    }
    
    private function set_mall_seller_profile_tbl()
    {
        $this->setTableName('mall_seller_profile_tbl');
    }
    
    
    
    /**
     * ���������б�
     * @param type $what_servie
     * @param type $b_select_count
     * @param type $where_str
     * @param type $order_by
     * @param type $limit
     * @param type $fields
     * @return type
     */
	public function get_service_list($b_select_count = false, $where_str = '', $order_by = 'i.service_id DESC', $limit = '0,10', $fields = '*',$is_export=false)
	{
		if ($b_select_count == true)
		{
            $sql = "select count(*) as total "
                    . "from {$this->_db_name}.mall_certificate_service_tbl as i "
                    . " $where_str";
            $rs = $this->query($sql);
            $ret = $rs['0']['total'];
		} else
		{
            //���˺�����������
            if($is_export) 
            {
                $sql = "select i.* "
                    . " from {$this->_db_name}.mall_certificate_service_tbl as i left join {$this->_db_name}.mall_seller_tbl as s on i.user_id=s.user_id "
                    . " $where_str and s.is_black!=1 order by $order_by desc limit $limit";
            }else 
            {
                $sql = "select i.* "
                    . " from {$this->_db_name}.mall_certificate_service_tbl as i "
                    . " $where_str order by $order_by desc limit $limit";
            }
            
            $ret = $this->query($sql);
        }
		return $ret;
	}
    
    /**
     * ����user_id��ȡ�̼һ�������
     * @param type $user_id
     * @return boolean
     */
    public function get_basic_info_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        $this->set_certificate_basic_tbl();
        return $this->find("user_id='$user_id'",'basic_id desc');
    }
    
    
    /**
     * ����û����������Ƿ��Ѿ���ͨ
     * @param type $user_id
     * @param type $service_type
     */
    public function check_user_service_is_check($user_id,$service_type)
    {
        $this->set_certificate_service_tbl();
        $user_id = (int)$user_id;
        $service_type = addslashes($service_type);
        if( ! $user_id || ! $service_type)
        {
            return false;
        }
        return $this->find("user_id='$user_id' and service_type='$service_type' and status='1'");
    }
    
    /**
     * ����״̬
     * @param type $id
     * @param type $status
     * @return type
     */
    public function change_status( $id,$status,$operator_id )
    {
        $this->set_certificate_service_tbl();
        return $this->update(array('status'=>$status,'update_time'=>time(),'operator_id'=>$operator_id),"service_id='$id'");
    }
    
    /**
     * ��ȡ��������
     * @param type $id
     * @param type $what_service
     * @return boolean
     */
    public function get_info($id,$what_service)
    {
        $id = (int)$id;
        $what_service = addslashes($what_service);
        $what_service = trim($what_service);
        
        if( ! $id || ! $what_service )
        {
            return false;
        }
        
        $static_ary = array('teacher','model','studio','cameror','other','diet','dresser');
        
        //���еķ��񣬷�������ͨ��������
        if( in_array($what_service, $static_ary) )
        {
             $sql = "select i.*,s.* from {$this->_db_name}.mall_certificate_service_tbl as i "
            . "left join {$this->_db_name}.mall_certificate_service_{$what_service}_tbl as s on i.service_id=s.service_id "
            . "where i.service_id='$id'";
            return $this->query($sql);
        }else
        {
            //�·���ֱ��ͨ��������Ϳ��Բ鵽
            $rs = $this->get_service_info($id);
            if( ! empty($rs['service_author_content']) )
            {
                $rs['service_author_content_data'] = unserialize($rs['service_author_content']);
            }
            
            $rs['service_params_data'] = $this->show_get_info($rs);
            $res[] = $rs;
            return $res;
        }
    }
    
    /**
     * ����չʾ��
     * @param type $data
     * @return type
     */
    public function show_get_info($data)
    {
        $field_config = pai_mall_load_config('certificate_service_field');
        $data['service_params'] = unserialize($data['service_params']);
        if( ! empty($field_config[$data['service_type']]) )
        {
            foreach($field_config[$data['service_type']] as $k => $v)
            {
                $res[] = array('text'=>$v['text'],'value'=>$data['service_params']['ser'][$k]);
            }
        }
        return $res;
    }
    
    /**
     * ��ȡ����Ļ�������
     * @param type $id
     * @return boolean
     */
    public function get_service_info($id)
    {
        $id = (int)$id;
        if( ! $id )
        {
            return false;
        }
        
        $this->set_certificate_service_tbl();
        
        return $this->find("service_id='$id'");
        
    }
    
    
    /**
     * ��ȡ�����ͼƬ
     * @param type $service_id
     * @return null
     */
    public function get_service_imgs($service_id)
    {
        $service_id = (int)$service_id;
        if( ! $service_id )
        {
            return null;
        }
        $this->set_mall_certificate_service_img_tbl();
        
        return $this->findAll("service_id='$service_id'");
    }
    
    /**
     * ���user_id�Ƿ��л�����֤
     * @param type $id
     * @return boolean
     */
    public function check_basic_is_have($user_id)
    {
        $this->set_certificate_basic_tbl();
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        return $this->find("user_id='$user_id' and ( basic_type='person' or basic_type='company' )","basic_id desc");
    }
    
    /**
     * ����Ƿ��Ѿ������
     * @param type $user_id
     * @return boolean
     */
    public function check_has_add($user_id,$service_type)
    {
        $this->set_certificate_service_tbl();
        $user_id = (int)$user_id;
        $service_type = addslashes($service_type);
        
        if( ! $user_id || ! $service_type )
        {
            return false;
        }
        
        $rs = $this->find("user_id='$user_id' and ( status='0' or status='1') and service_type='$service_type'");
        if($rs)
        {
            return false;
        }else
        {
            return true;
        }
    }
    
    /**
     * ͨ��user_id�������״̬ 
     * @param type $user_id
     * @return array status 1 ͨ��,2��ͨ��,0δ���,-2û����ؼ�¼,-3�̼�״̬ʧЧ
     */
    public function get_service_status_by_user_id($user_id,$bool=false,$type='rz')
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return array('status'=>'-1','msg'=>'��������ȷ');
        }
        $status_total_arys = array();
		
		if($bool)
		{
			$service_type = array('model','cameror','studio','teacher','dresser','diet','other');
		}else
		{
			$service_type = array('model','cameror','studio','teacher','dresser','diet','other','activity');
		}
        
        $service_blong = array(
            'model'=>'���ܷ���',
            'teacher'=>'���ܷ���',
            'studio'=>'��ҵ����',
            'dresser'=>'��ҵ����',
            'cameror'=>'���ַ���',
            'diet'=>'�������',
            'other'=>'��������',
            'activity'=>'���ַ���'
        );
        
        $type_id_service_type_config = pai_mall_load_config('certificate_service_type_id_service_type');
        //��ֵ����
        $service_type_id = array_flip($type_id_service_type_config);
        
        //������̼ҿ�ͨ����type_id �� ״̬
        $seller_type_id_and_status = $this->get_seller_type_id_and_status($user_id);
        if( ! empty($seller_type_id_and_status) )
        {
            $type_id_ary = $seller_type_id_and_status['type_id'];
            $seller_status = $seller_type_id_and_status['seller_status'];
        }else
        {
            $type_id_ary = array();
            $seller_status = '';
        }
        
        //��֤�˵��ύ���
        
        foreach($service_type as $v)
        {
            $type_id = $service_type_id[$v];
            $this->set_certificate_service_tbl();
            $rs = $this->find("user_id='$user_id' and service_type='$v'","service_id desc");
            //��֤�˽�Ϸ���˷��ط���״̬
            $is_have_type_id = in_array($type_id,$type_id_ary) ? true : false;
            
            if( ! $rs)
            {
                if($seller_status == 1 || $seller_status == 3)
                {
                    if($is_have_type_id)
                    {
                        $status_total_arys[] = array('service_type'=>$v,'status'=>'1','msg'=>'ͨ��','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>true); 
                    }else
                    {
                        $status_total_arys[] = array('service_type'=>$v,'status'=>'-2','msg'=>'û����ؼ�¼','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                    }
                }else if($seller_status == 2)
                {
                     $status_total_arys[] = array('service_type'=>$v,'status'=>'-3','msg'=>'�̼�״̬ʧЧ','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                }else
                {
                     $status_total_arys[] = array('service_type'=>$v,'status'=>'-2','msg'=>'û����ؼ�¼','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                }
                
            }else if($rs['status'] == 1)
            {
                if($seller_status == 1 || $seller_status == 3)
                {
                    if($is_have_type_id)
                    {
                        $status_total_arys[] = array('service_type'=>$v,'status'=>'1','msg'=>'ͨ��','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>true); 
                    }
                    else
                    {
                        //��̨��ͨ����״̬��Ϊ2
                        $this->change_status( $rs['service_id'],2,115203);
                        //��ӱ�ע
                        $this->update_remark($rs['service_id'],'�̼ҷ���״̬��ͨ��');
                        $status_total_arys[] = array('service_type'=>$v,'status'=>'2','msg'=>'��ͨ��','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                    }
                }else if($seller_status == 2)
                {
                     $status_total_arys[] = array('service_type'=>$v,'status'=>'-3','msg'=>'�̼�״̬ʧЧ','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                }else
                {
                    //��̨��ͨ����״̬��Ϊ2
                    $this->change_status( $rs['service_id'],2,115203);
                    //��ӱ�ע
                    $this->update_remark($rs['service_id'],'�̼ҷ���״̬û����ؼ�¼');
                    $status_total_arys[] = array('service_type'=>$v,'status'=>'2','msg'=>'��ͨ��','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                }
                 
            }else if($rs['status'] == 0)
            {
                if($seller_status == 1 || $seller_status == 3)
                {
                    if($is_have_type_id)
                    {
                         $status_total_arys[] = array('service_type'=>$v,'status'=>'0','msg'=>'δ���','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>true);
                    }
                    else
                    {
                        $status_total_arys[] = array('service_type'=>$v,'status'=>'0','msg'=>'δ���','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                    }
                }else if($seller_status == 2)
                {
                    $status_total_arys[] = array('service_type'=>$v,'status'=>'-3','msg'=>'�̼�״̬ʧЧ','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                }else
                {
                     $status_total_arys[] = array('service_type'=>$v,'status'=>'0','msg'=>'δ���','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                }
                 
            }else if($rs['status'] == 2)
            {
                if($seller_status == 1 || $seller_status == 3)
                {
                    if($is_have_type_id)
                    {
                        $status_total_arys[] = array('service_type'=>$v,'status'=>'1','msg'=>'ͨ��','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>true); 
                    }
                    else 
                    {
                        $status_total_arys[] = array('service_type'=>$v,'status'=>'2','msg'=>'��ͨ��','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                    }
                    
                }else if($seller_status == 2)
                {
                    $status_total_arys[] = array('service_type'=>$v,'status'=>'-3','msg'=>'�̼�״̬ʧЧ','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                }else
                {
                    $status_total_arys[] = array('service_type'=>$v,'status'=>'2','msg'=>'��ͨ��','service_belong'=>$service_blong[$v],'type_id'=>$type_id,'seller_status'=>$seller_status,'can_add_goods'=>false);
                }
                 
            }
        }
        
        //�������Ʒ�༭��,�ֻҪ������һ��Ʒ��ͨ����,�Ϳ��Ա༭��Ʒ
        if($type == 'goods')
        {
            $activity_can_past = false;
            foreach($status_total_arys as $k => &$v)
            {
                if($v['service_type'] != 'activity' && $v['status'] == 1)
                {
                    $activity_can_past = true;
                }
                if($activity_can_past)
                {
                    if($v['service_type'] == 'activity')
                    {
                        if($v['status']  != 1)
                        {
                            $v['org_status'] = $v['status'];
                        }else
                        {
                            $v['org_status'] = 1;
                        }
                        
                        $v['status'] = 1;
                        $v['msg'] = 'ͨ��';
                        $v['seller_status'] = 1;
                        $v['can_add_goods'] = 1;
                        break;
                        
                    }
                }
            }
        }
        
        return $status_total_arys;
        
    }
    
    /**
     * ��ȡ��Ƿ��Ѿ���ͨ��ֻҪ����һ��Ʒ�࿪ͨ,����Ϊ��ͨ��
     * @param type $user_id
     * @return boolean
     */
    public function check_user_activity_open_or_not($user_id)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        $all_status = $this->get_service_status_by_user_id($user_id);
        $service_status = false;
        if( ! empty($all_status) )
        {
            foreach($all_status as $k => $v)
            {
                if($v['status'] == 1)
                {
                    $service_status = true;
                    break;
                }
            }
        }
        
        return $service_status;
        
    }
    
    
    /**
     * �ж�ĳ�����Ƿ��Ѿ���ͨ
     * @param type $user_id
     * @param type $type_id
     * @return boolean
     */
    public function get_service_open_or_not($user_id,$type_id,$bool=true)
    {
        $all_status = $this->get_service_status_by_user_id($user_id,$bool);
        if(  ! empty($all_status) )
        {
            foreach($all_status as $k => $v)
            {
                $all_service_status[$v['type_id']] = $v;
            }
            if( ! empty($all_service_status[$type_id]))
            {
                if($all_service_status[$type_id]['status'] == 1)
                {
                    return true;
                }else
                {
                    if($type_id == 42)
                    {
                        return $this->check_user_activity_open_or_not($user_id);
                    }
                    
                    return false;
                }
            }else
            {
                if($type_id == 42)
                {
                    return $this->check_user_activity_open_or_not($user_id);
                }
                return false;
            }
        }
        return false;
    }
    
   /**
    * ��ȡ�̼ҿ�ͨ�ķ���type_id �� �̼�״̬
    * @param type $user_id
    * @return type
    */
   public function get_seller_type_id_and_status($user_id)
   {
       $user_id = (int)$user_id;
       if( ! $user_id )
       {
           return array();
       } 
       $this->set_mall_seller_tbl();
       $seller_one = $this->find("user_id='$user_id'");
       if( empty($seller_one) )
       {
           return array();
       }
       $seller_status = $seller_one['status'];
       $this->set_mall_seller_profile_tbl();
       $seller_profile_one = $this->find("user_id='$user_id'");
       if(empty($seller_profile_one))
       {
           $type_id_ary = array();
       }
       $type_id_str = $seller_profile_one['type_id'];
       if( ! empty($type_id_str) )
       {
           $type_id_ary = explode(',', $type_id_str);
       }else 
       {
           $type_id_ary = array();
       }
       return array('type_id'=>$type_id_ary,'seller_status'=>$seller_status);
       
       
   } 
   
   /**
    * ���ط�������
    * @return type
    */
   public function get_service_type()
   {
       return array(
            '��ҵ����',  
            '���ܷ���',  
            '���ַ���',  
            '�������',  
            '��������',  
       );
   }
   
    /**
	 * ����̼ҷ�������
	 * @param array $data
	 * @return int 1 �ɹ� -1 ���ϲ����� -2ֻ������һ�� -3������֤idû���ҵ���¼ -4 ��������
	 */
	public function add_service_sq($post)
	{
        //����Ƿ��Ѿ�ͨ��������֤
        $basic_one = $this->check_basic_is_have($post['user_id']);
        if( ! $basic_one )
        {
            return array('status'=>-3,'msg'=>'������֤idû���ҵ���¼');
        }
        
        //ֻ������һ��
        $has_one = $this->check_has_add($post['user_id'],$post['service_type']);
        
        if( ! $has_one )
        {
            return array('status'=>-2,'msg'=>'�Ѿ��������');
        }
        
        //����Ƿ�ǿ�
        foreach($post as $k => $v)
        {
            if($k == 'do_well_other' || $k == 'other_other_identifine' || $k == 'diet_web_name' || $k == 'past_activity_content')
            {
                continue;
            }
            if( $v === '')
            {
                return array( 'status'=> -1,'msg'=>'���ϲ�ȫ' );
            }
        }
        
        $mall_user_obj = POCO::singleton('pai_user_class');
        
        //�Ȳ��������
        $this->set_certificate_service_tbl();
        
        $data_index['service_type'] = addslashes($post['service_type']);
        $data_index['basic_id'] = (int)$basic_one['basic_id'];
        if($basic_one['basic_type'] == 'person')
        {
            $data_index['city_id'] = (int)$basic_one['person_zone_id'];
        }else if($baisc_one['basic_type'] == 'company')
        {
            $data_index['city_id'] = (int)$basic_one['company_bank_city_id'];
        }
        
        $data_index['user_id'] = (int)$post['user_id'];
        $data_index['operator_id'] = $post['operator_id'];
        $data_index['status'] = 0;
        $data_index['phone'] = $mall_user_obj->get_phone_by_user_id($data_index['user_id']);
        $data_index['add_time'] = time();
        
        //�·���Ʒ��,���л����浽һ���ֶ�
        if( ! in_array( $data_index['service_type'],array('teacher','model','studio','cameror','other','diet','dresser') ) )
        {
            $field_config = pai_mall_load_config('certificate_service_field');
            if( ! empty($field_config[$data_index['service_type']]) )
            {
                $ser = array();
                foreach($field_config[$data_index['service_type']] as $k => $v)
                {
                    $ser['ser'][$k] = $v['type']=='str'? addslashes($post[$k]): (int)$post[$k];
                }
                
                //����Ĳ���
                if( ! empty($ser) )
                {
                    $data_index['service_params'] = serialize($ser);
                }
                //�������Ʒ��
                //��Ʒ������ĸ�ʽ array(
                            //array('text'=>'���','value'=>'����'),
                            //array('text'=>'ͼƬ',value=>"<img src='abc.jpg/><img src='edf.jpg'/>'"),
                //)
                if( ! empty($post['service_author_content']) )
                {
                    $data_index['service_author_content'] = serialize($post['service_author_content']);
                }
               
                
            }
            
        }
        
        $service_id = $this->insert($data_index,"REPLACE");
        
        //�ٲ���������
        $this->insert_service_detail($post,$service_id);
        
        //�ٲ�ͼƬ��
        $this->insert_service_img($post,$service_id);
        
        //�����̼ҷ���
        $this->update_seller_profile_type_id($data_index['user_id'], $data_index['service_type']);
        
        unset($data_index);
        unset($ser);
        
        return array( 'status' => 1,'msg'=>'��ӳɹ�' );
        
    }
    
    /**
     * 
     * �����̼�״̬ 
     * @param type $user_id
     * @return boolean
     */
    public function update_seller_status($user_id,$status)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        $this->set_mall_seller_tbl();
        return $this->update(array('status'=>$status),"user_id='{$user_id}'");
    }
    
    /**
     * ͬ����֤���ϵ��̼����� 
     * @param type $service_id
     * @param type $service_type
     * @param type $profile_id
     */
    public function update_rz_info_to_seller($service_id,$service_type,$profile_id)
    {
        $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
        //��ȡ��֤���������
        $service_detail = $this->get_info($service_id,$service_type);
        
        //�����Լ��ʳ,��Ҫ����֤�ύ�����ύ��������������,���µ��̼ҿ�ͨ�ķ���ȥ
        if($service_type == 'diet')
        {
            //���������ļ�
            $identification_config = pai_mall_load_config('certificate_diet_identification');
            $max_forward_config = pai_mall_load_config('certificate_diet_max_forward');
            $diet_years_config = pai_mall_load_config('certificate_diet_years');

            //����תΪ������ʾ
            $max_forward = $max_forward_config[$service_detail['0']['diet_max_forward']];
            $identification = $identification_config[$service_detail['0']['diet_identification']];
            $diet_years = $diet_years_config[$service_detail['0']['diet_years']];
            $data =  array(
                array('key'=>'ms_experience','data'=>$diet_years),
                array('key'=>'ms_certification','data'=>$identification),
                array('key'=>'ms_forwarding','data'=>$max_forward),
            );
            
        }else if($service_type == 'studio')
        {
            //ͬ��Ӱ���ַ���̼���������
            $studio_area_config = pai_mall_load_config('certificate_studio_area');
            $area_data = $studio_area_config[$service_detail['0']['studio_area']];
            
            $yp_place_data = get_poco_location_name_by_location_id($service_detail['0']['location_id']).' '.$service_detail['0']['studio_place'];
            
            $can_photo_config = pai_mall_load_config('certificate_studio_can_photo');
            $background_ary = explode(',', $service_detail['0']['can_photo_type']);
            $background_str = '';
            foreach($background_ary as $k => &$v)
            {
                $background_str .= $can_photo_config[$v].",";
            }
            
            $yp_background_data = substr($background_str,0,-1);
            
            $photo_type_config = pai_mall_load_config('certificate_studio_photo_type');
            $photo_type_ary = explode(',', $service_detail['0']['photo_type']);
            $photo_type_str = '';
            foreach($photo_type_ary as $k => &$v)
            {
                $photo_type_str .= $photo_type_config[$v].",";
            }
            $yp_can_photo_data = substr($photo_type_str,0,-1);
            
            $lighter_config = pai_mall_load_config('certificate_studio_lighter');
            $lighter_ary = explode(',', $service_detail['0']['lighter']);
            $lighter_str = '';
            foreach($lighter_ary as $k => &$v)
            {
                $lighter_str .= $lighter_config[$v].",";
            }
            $yp_lighter_data = substr($lighter_str, 0,-1);
            
            $other_config = pai_mall_load_config('certificate_studio_other');
            $other_ary = explode(',', $service_detail['0']['other']);
            $other_str = '';
            foreach($other_ary as $k => &$v)
            {
                $other_str .= $other_config[$v].",";
            }
            
            $yp_other_equitment_data = substr($other_str,0,-1);
            
            $data = array(
                array('key'=>'yp_area','data'=>$area_data),
                array('key'=>'yp_place','data'=>$yp_place_data),
                array('key'=>'yp_background','data'=>$yp_background_data),
                array('key'=>'yp_can_photo','data'=>$yp_can_photo_data ),
                array('key'=>'yp_lighter','data'=>$yp_lighter_data ),
                array('key'=>'yp_other_equitment','data'=>$yp_other_equitment_data ),
            );
            
        }else if($service_type == 'dresser')
        {
             $years_config = pai_mall_load_config('certificate_common_years');
             $team_num_config = pai_mall_load_config('certificate_dresser_team_num');
             $do_well_config = pai_mall_load_config('certificate_dresser_do_well');
             
             $hz_experience_data = $years_config[$service_detail['0']['years']];
             $hz_team_data = $team_num_config[$service_detail['0']['team_num']];
             $good_at = $service_detail['0']['do_well'];
             $good_at_ary = explode(",",$good_at);
             $hz_goodat_data = '';
             foreach($good_at_ary as $k => &$v)
             {
                 $hz_goodat_data .=$do_well_config[$v].",";
             }   
             $hz_goodat_data = substr($hz_goodat_data, 0, -1);
             $hz_othergoodat_data = $service_detail['0']['do_well_other'];
             
             $order_way_config = pai_mall_load_config('certificate_dresser_order_way');
             $hz_order_way_data = $order_way_config[$service_detail['0']['order_way']];
             
             $has_place_config = pai_mall_load_config('certificate_dresser_has_place');
             $hz_place_data = $has_place_config[$service_detail['0']['has_place']];
             
            $data = array(
                 array('key'=>'hz_experience','data'=>$hz_experience_data),
                 array('key'=>'hz_team','data'=>$hz_team_data),
                 array('key'=>'hz_goodat','data'=>$hz_goodat_data),
                 array('key'=>'hz_othergoodat','data'=>$hz_othergoodat_data),
                 array('key'=>'hz_order_way','data'=>$hz_order_way_data),
                 array('key'=>'hz_place','data'=>$hz_place_data),
             );
        }else if($service_type == 'other')
        {
            $other_identifine_label_config = pai_mall_load_config('certificate_other_identifine_label');
            
            $other_identifine = $service_detail['0']['other_identifine'];
            $other_identifine_ary = explode(',',$other_identifine);
            $ot_label_data = '';
            foreach($other_identifine_ary as $k => &$v)
            {
                $ot_label_data .= $other_identifine_label_config[$v].",";
            }
            $ot_label_data = substr($ot_label_data, 0,-1);
            $ot_otherlabel_data = $service_detail['0']['other_other_identifine'];
            $data = array(
                array('key'=>'ot_label','data'=>$ot_label_data),
                array('key'=>'ot_otherlabel','data'=>$ot_otherlabel_data),
            );
        }else if($service_type == 'teacher')
        {
            $teacher_type_config = pai_mall_load_config('certificate_teacher_type');
            $teacher_years_config =  pai_mall_load_config('certificate_teacher_years');
            
            $t_teacher_data = $teacher_type_config[$service_detail['0']['teacher_type']];
            $t_experience_data = $teacher_years_config[$service_detail['0']['years']];
            
            $class_way_config = pai_mall_load_config('certificate_teacher_class_way');
            $teacher_way_data = $class_way_config[$service_detail['0']['class_way']];

            $data = array(
                array('key'=>'t_teacher','data'=>$t_teacher_data),
                array('key'=>'t_experience','data'=>$t_experience_data),
                array('key'=>'t_way','data'=>$teacher_way_data)
            );
        }else if($service_type == 'model')
        {
            $m_height_data = $service_detail['0']['height'];
            $m_weight_data = $service_detail['0']['weight'];
            $m_bwh_data = $service_detail['0']['bust'].'-'.$service_detail['0']['waist'].'-'.$service_detail['0']['hips'];
            $cup_str = $service_detail['0']['cup_type'];
            $cup_ary = explode("|",$cup_str);
            $m_cups_data = $cup_ary['0'];
            $m_cup_data = $cup_ary['1'];
            
            $sex_config = pai_mall_load_config('certificate_common_sex');
            $sex_data = $sex_config[$service_detail['0']['sex']];

            $data = array(
                array('key'=>'m_height','data'=>$m_height_data),
                array('key'=>'m_weight','data'=>$m_weight_data),
                array('key'=>'m_bwh','data'=>$m_bwh_data),
                array('key'=>'m_cups','data'=>$m_cups_data),
                array('key'=>'m_cup','data'=>$m_cup_data),
                array('key'=>'m_sex','data'=>$sex_data),
            );
        }else if($service_type == 'cameror')
        {
            $years_config = pai_mall_load_config('certificate_common_years');
            $p_experience_data = $years_config[$service_detail['0']['years']];
            
            $order_income_config = pai_mall_load_config('certificate_cameror_order_income');
            $order_income_data = $order_income_config[$service_detail['0']['order_income']];
            
            $team_config = pai_mall_load_config('certificate_cameror_team');
            $team_data = $team_config[$service_detail['0']['team']];
            
            $data = array(
                array('key'=>'p_experience','data'=>$p_experience_data),
                array('key'=>'p_order_income','data'=>$order_income_data),
                array('key'=>'p_team','data'=>$team_data),
            );
        }else if($service_type == 'activity')
        {
            $service_info = $this->get_service_info($service_id);
            if( ! empty($service_info) )
            {
                $service_params = unserialize($service_info['service_params']);
                
                //ͬ�����ҽ���
                $user_id = $service_info['user_id'];
                $introduce = $service_params['ser']['introduce'];
                $this->set_mall_seller_tbl();
                $this->update(array('introduce'=>$introduce),"user_id='$user_id'");
                $this->set_mall_seller_profile_tbl();
                $this->update(array('introduce'=>$introduce),"user_id='$user_id'");
                
                $data = array();
                //ͬ������
                $data = array(
                    array('key'=>'ev_goodat','data'=>$service_params['ser']['do_well']),
                    array('key'=>'ev_other','data'=>$service_params['ser']['do_well_other']),
                );
                $mall_seller_obj->update_seller_profile_detail_for_att($profile_id,$data);
               
            }
            return true;
        
        }
        
        
        $mall_seller_obj->update_seller_profile_detail_for_att($profile_id,$data);
        unset($data);
        return true;
    }
    
    /**
     * �����̼ҷ���
     * @param type $user_id
     * @param type $service_type
     * @return boolean
     */
    public function update_seller_profile_type_id($user_id,$service_type)
    {
        $user_id = (int)$user_id;
        $service_type = addslashes($service_type);
        
        if( ! $user_id || ! $service_type )
        {
            return false;
        }
        
        $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
        
        $seller_info = $mall_seller_obj->get_seller_info($user_id,2);
        
        $type_id_service_type_config = pai_mall_load_config('certificate_service_type_id_service_type');
        //��ֵ����
        $service_type_id_ary = array_flip($type_id_service_type_config);
           
        $type_ary[] = $service_type_id_ary[$service_type];

        $res = $mall_seller_obj->update_seller_profile_type_id($seller_info['seller_data']['profile']['0']['seller_profile_id'],$type_ary);
        
        //״̬������Ч����Ч������ʱ
        if($seller_info['seller_data']['status'] != 1 && $seller_info['seller_data']['status'] != 2)
        {
            //��ֻ����ʱ�̼�
            $this->update_seller_status($user_id,3);
        }
		
        return true;
    }
    
    /**
     * �����û����ֻ���
     * @param type $user_id
     */
    public function update_user_phone($user_id)
    {
        $this->set_certificate_service_tbl();
        $mall_user_obj = POCO::singleton('pai_user_class');
        $phone =  $mall_user_obj->get_phone_by_user_id($user_id);
        $this->update(array('phone'=>$phone),"user_id='$user_id'");
    }
    
    /**
     * ���³���id
     * @param type $user_id
     * @param type $city_id
     */
    public function update_city_id($user_id,$city_id)
    {
        $this->set_certificate_service_tbl();
        $this->update(array('city_id'=>$city_id),"user_id='$user_id'");
    }
    
    /**
     * ������������
     * @param type $post
     * @return boolean
     */
    public function insert_service_detail($post,$service_id)
    {
        if($post['service_type']=='model')
        {
            $this->set_mall_certificate_service_model_tbl();
            $data_service['service_id'] = $service_id;
            $data_service['sex'] = (int)$post['sex'];
            $data_service['height'] = (int)$post['height'];
            $data_service['weight'] = (int)$post['weight'];
            $data_service['bust'] = (int)$post['bust'];
            $data_service['waist'] = (int)$post['waist'];
            $data_service['hips'] = (int)$post['hips'];
            $data_service['cup_type'] = addslashes($post['cup_type']);
            $data_service['self_desc'] = addslashes($post['self_desc']);
            $data_service['weixin_qq'] = addslashes($post['weixin_qq']);
            
        }else if($post['service_type']=='studio')
        {
            $this->set_mall_certificate_service_studio_tbl();
            $data_service['service_id'] = $service_id;
            $data_service['location_id'] = (int)$post['location_id'];
            $data_service['studio_place'] = addslashes($post['studio_place']);
            $data_service['studio_desc'] = addslashes($post['studio_desc']);
            $data_service['studio_area'] = addslashes($post['studio_area']);
            $data_service['studio_area_input'] = addslashes($post['studio_area_input']);
            $data_service['can_photo_type'] = addslashes($post['can_photo_type']);
            $data_service['photo_type'] = addslashes($post['photo_type']);
            $data_service['lighter'] = addslashes($post['lighter']);
            $data_service['other'] = addslashes($post['other']);
            
        }else if($post['service_type'] == 'dresser')
        {
            $this->set_mall_certificate_service_dresser_tbl();
            $data_service['service_id'] = $service_id;
            $data_service['years'] = (int)$post['years'];
            $data_service['has_place'] = (int)$post['has_place'];
            $data_service['dresser_desc'] = addslashes($post['dresser_desc']);
            $data_service['order_way'] = (int)$post['order_way'];
            $data_service['team_num'] = (int)($post['team_num']);
            $data_service['do_well'] = addslashes($post['do_well']);
            $data_service['do_well_other'] = addslashes($post['do_well_other']);
        }else if($post['service_type'] == 'teacher')
        {
            $this->set_mall_certificate_service_teacher_tbl();
            $data_service['service_id'] = $service_id;
            $data_service['years'] = (int)$post['years'];
            $data_service['course_special'] = addslashes($post['course_special']);
            $data_service['can_learn'] = addslashes($post['can_learn']);
            $data_service['society_num'] = addslashes($post['society_num']);
            $data_service['class_way'] = (int)$post['class_way'];
            
            $data_service['teacher_type'] = (int)$post['teacher_type'];
            $data_service['teacher_train_type'] = (int)$post['teacher_train_type'];
            
            $data_service['teacher_num'] = (int)$post['teacher_num'];
            $data_service['student_num'] = (int)$post['student_num'];
            $data_service['author_content'] = serialize($post['author_content']);
        }else if($post['service_type'] == 'cameror')
        {
            $this->set_mall_certificate_service_cameror_tbl();
            $data_service['service_id'] = $service_id;
            $data_service['years'] = (int)$post['years'];
            $data_service['self_desc'] = addslashes($post['self_desc']);
            $data_service['often_equipment'] = addslashes($post['often_equipment']);
            
            $data_service['zone_num'] = addslashes($post['zone_num']);
            $data_service['order_income'] = (int)$post['order_income'];
            $data_service['team'] = (int)$post['team'];
            
            $data_service['society_num'] = addslashes($post['society_num']);
            $data_service['author_content'] = serialize($post['author_content']);
        }else if($post['service_type'] == 'diet')
		{
			$this->set_mall_certificate_service_diet_tbl();
			$data_service['service_id'] = $service_id;
            $data_service['diet_job'] = (int)$post['diet_job'];
            $data_service['diet_years'] = (int)$post['diet_years'];
            $data_service['diet_max_forward'] = (int)$post['diet_max_forward'];
            $data_service['diet_identification'] = (int)$post['diet_identification'];
			$data_service['diet_web_name'] = addslashes($post['diet_web_name']);
			$data_service['diet_media_address'] = addslashes($post['diet_media_address']);
			$data_service['diet_self_desc'] = addslashes($post['diet_self_desc']);
        }else if($post['service_type'] == 'other')
		{
			$this->set_mall_certificate_service_other_tbl();
			$data_service['service_id'] = $service_id;
            $data_service['other_self_desc'] = addslashes($post['other_self_desc']);
            $data_service['other_identifine'] = addslashes($post['other_identifine']);
            $data_service['other_other_identifine'] = addslashes($post['other_other_identifine']);
            $data_service['other_job'] = addslashes($post['other_job']);
            $data_service['other_service_desc'] = addslashes($post['other_service_desc']);
		}
        else
        {
            return array('status'=>-4,'msg'=>'��������');
        }
//        if($post['user_id']==115203)
//        {
//            print_r($data_service);
//            exit;
//        }
        $id = $this->insert($data_service,"REPLACE");
        unset($data_service);
        return $id;
    }
    
    /**
     * �������ͼƬ
     * @param type $post
     */
    public function insert_service_img($post,$service_id)
    {
        
        $this->set_mall_certificate_service_img_tbl();
        
        if( ! empty($post['img']) )
        {
            foreach($post['img'] as $k => $v)
            {
                $data_pic['service_id'] = $service_id;
                $data_pic['img_type'] = $v['img_type'];
                $data_pic['img_url'] = $v['img_url'];
                $this->insert($data_pic,"REPLACE");
                unset($data_pic);
            }
        }
        
        return true;
    }
    
    /**
     * ���±�ע
     * @param type $service_id
     * @param type $remark
     * @return boolean
     */
    public function update_remark($service_id,$remark)
    {
        $service_id = (int)$service_id;
        $remark = addslashes($remark);
        
        if( ! $service_id || ! $remark)
        {
            return false;
        }
        
        $this->set_certificate_service_tbl();
        
        return $this->update(array('remark'=>$remark),"service_id='{$service_id}'");
    }
    
    /**
     * ��ȡ�û��Ĳ�����
     * @param type $type_id
     * @param type $user_id
     * @return boolean
     */
    public function get_user_option_name($type_id,$user_id)
    {
        $type_id = (int)$type_id;
        $user_id = (int)$user_id;
        if( ! $type_id || ! $user_id)
        {
            return false;
        }
		$this->set_mall_goods_type_tbl();
		$type_one = $this->find("id='{$type_id}'");
		if( ! empty($type_one) )
		{
			$this->set_certificate_service_tbl();
			$service_one = $this->find("user_id='{$user_id}' and service_type='{$type_one['service_type']}' and status=1",'service_id desc');
            if( ! empty($service_one) )
            {
                return get_user_nickname_by_user_id($service_one['operator_id']);
            }
            
        }
        return false;
        
    }
    
    /**
     * ��ȡӰ��ĵ���id,���ַ
     * @param type $user_id
     * @return boolean
     */
    public function get_loation_id_and_place($user_id)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        $this->set_certificate_service_tbl();
        $service_one = $this->find("user_id='{$user_id}' and service_type='studio'",'service_id desc');
        if( ! empty($service_one) )
        {
            $service_id = $service_one['service_id'];
            $this->set_mall_certificate_service_studio_tbl();
            $studio_one = $this->find("service_id='{$service_id}'");
            if( ! empty($studio_one) )
            {
                return array('location_id'=>$studio_one['location_id'],'studio_place'=>$studio_one['studio_place']);
            }
        }
        
        return false;
    }
    
    
}
