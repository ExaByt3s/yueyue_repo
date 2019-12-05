<?php
/**
 * �̼һ�Ա�ȼ���
 * 
 * @author ljl
 */

class pai_mall_seller_member_level_class extends POCO_TDG
{
	
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
	private function set_mall_seller_tbl()
	{
		$this->setTableName('mall_seller_tbl');
	}
    
    private function set_mall_seller_profile_tbl()
	{
		$this->setTableName('mall_seller_profile_tbl');
	}
    
    private function set_mall_comment_seller_tbl()
    {
        $this->setTableName('mall_comment_seller_tbl');
    }
    
    private function set_mall_goods_tbl()
    {
        $this->setTableName('mall_goods_tbl');
    }
    
    private function set_mall_goods_statistical_tbl()
    {
        $this->setTableName('mall_goods_statistical_tbl');
    }
    
    /**
     * ��ȡ�����̼ҵ�user_id
     * @return type
     */
    public function seller_get_all_seller()
    {
        $this->set_mall_seller_tbl();
        return $this->findAll("status='1'",'0,100000',"seller_id desc","user_id");
    }
    
    /**
     * �̼ҳɹ�������
     * @param type $where
     * @return int
     */
    public function seller_succes_total_order($where='')
    { 
        if( ! $where )
        {
            return 0;
        }
        $sql = "select count(*) as total from $this->_db_name.mall_order_tbl $where";
        $rs = $this->query($sql);
        return (int)$rs['0']['total'];
    }
    
    /**
     * ��ȡĳ����Ʒid,�ܶ���������
     * @param type $goods_id
     * @return boolean
     */
    public function seller_get_goods_id_finish_order_number_and_money($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $this->set_mall_goods_statistical_tbl();
        $row = $this->find("goods_id='$goods_id'");
        $total_number = (int)$row['bill_finish_num'];
        $total_money = (int)$row['prices'];
        return array('total_number'=>$total_number,'total_money'=>$total_money);
    }
    /**
     * �̼ҳɹ������ܽ��
     * @param type $where
     * @return int
     */
    public function seller_succes_total_money_order($where='')
    {
        if( ! $where )
        {
            return 0;
        }
        $sql = "select sum(total_amount) as total_money from $this->_db_name.mall_order_tbl $where";
        $rs = $this->query($sql);
        return (int)$rs['0']['total_money'];
    }
            
    
    /**
     * ����goods_type_id_tbl
     * @param type $goods_id
     * @return boolean
     */
    public function seller_update_goods_type_id_tbl($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
		$obj = POCO::singleton('pai_mall_goods_class');
        return $obj->batch_insert_or_update_goods_type_id_tbl($goods_id);
    }
    
    /**
     * �̼ҵȼ�
     * @param type $seller_user_id
     * @return boolean
     */
    public function seller_level($seller_user_id)
    {
        $seller_user_id = (int)$seller_user_id;
        if( ! $seller_user_id )
        {
            return false;
        }
        
        $seller_level_point_config = pai_mall_load_config('level_seller_point');
        $seller_level_condition = pai_mall_load_config('level_seller_order_condition');
        
        $this->set_mall_seller_tbl();
        //����̼�������ҵ���͵ľ�ֱ�Ӹ���1.1��
        $seller_company = $this->find("user_id='{$seller_user_id}' and basic_type='company'");
        
        if( ! empty($seller_company) )
        {
            $company_point = $seller_level_point_config['company'];
            
            //ԭ�еķ�ֵ�������õķ�ֵ�ͽ��и���,����������÷�ֵ�Ͳ�����
            if($seller_company['seller_level_point'] < $company_point)
            {
                $this->update(array('seller_level_point'=>$company_point),"user_id='{$seller_user_id}'");
                $this->set_mall_seller_profile_tbl();
                $this->update(array('seller_level_point'=>$company_point),"user_id='{$seller_user_id}'");
            }
            
            unset($seller_company['seller_level_point']);
            unset($company_point);
            unset($seller_user_id);
            
            return true;
        }
        
        $where = "where seller_user_id='{$seller_user_id}' and status='8'";
        $succes_order_total = $this->seller_succes_total_order($where);
        //���ڳɹ��Ķ�����
        $now_success_order_total = $succes_order_total;
        
        foreach($seller_level_condition as $k => $v)
        {
            $key_arys = explode(',',$k);
            if( ! empty($key_arys) )
            {
                $key_s = $key_arys['0'];
                $key_e = $key_arys['1'];
                if($now_success_order_total >= $key_s && $now_success_order_total <= $key_e)
                {
                   $now_seller_level = $seller_level_condition[$k];
                   $this->set_mall_seller_tbl();
                   $old_one = $this->find("user_id='$seller_user_id' and status='1'");
                   //���ԭ�еĻ�Ա�ȼ����������������ֻ���µȼ������·���
                   if($old_one['seller_level_point'] > $seller_level_point_config[$now_seller_level])
                   {
                       $update_data = array(
                           'seller_level'=>$now_seller_level
                       );
                   }else
                   {    
                       $update_data = array(
                            'seller_level'=>$now_seller_level,
                            'seller_level_point'=>$seller_level_point_config[$now_seller_level]
                       );
                   }
                   
                   $this->update($update_data,"user_id='{$seller_user_id}'");
                   $this->set_mall_seller_profile_tbl();
                   $this->update($update_data,"user_id='{$seller_user_id}'");
                   
                   unset($update_data);
                   unset($key_arys);
                   unset($key_e);
                   unset($key_s);
                   unset($old_one);
                   
                   break;
                }
                
            }
            
        }
        return true;
    }
    
    /**
     * ǰ����ɹ�������
     * @param type $seller_user_id
     * @return type
     */
    public function seller_before_7_days_succes_order_total($seller_user_id)
    {
        $s = strtotime(date('Y-m-d',strtotime('-7days')));
        $e = strtotime(date('Y-m-d'))+86400;
        
        $where = "where pay_time>=$s and pay_time<=$e and seller_user_id='$seller_user_id' and status='8'";
        
        return $this->seller_succes_total_order($where);
    }
    
    /**
     * ǰ�������۷���
     * @param type $seller_user_id
     * @return int
     */
    public function seller_before_7_days_comment_point($seller_user_id)
    {
        $seller_user_id = (int)$seller_user_id;
        if( ! $seller_user_id )
        {
            return 0;
        }
        
        $this->set_mall_comment_seller_tbl();
        
        $s = strtotime(date('Y-m-d',strtotime('-7days')));
        $e = strtotime(date('Y-m-d'))+86400;
        $where = "to_user_id='{$seller_user_id}' and add_time >=$s and add_time <=$e";
        
        $total = $this->findCount($where);
        
        if($total == 0)
        {
            return 0;
        }
        
        $rs = $this->query("select sum(overall_score) as total_point from $this->_db_name.mall_comment_seller_tbl where $where");
        
        if( ! empty($rs['0']['total_point']) )
        {
            $total_point = $rs['0']['total_point'];
        }else
        {
            $total_point = 0;
        }
        
        $avg_point = $total_point/$total;
        
        return round($avg_point,1);
        
    }
    
    /**
     * ǰ����ظ��ٷֱ�
     * @param type $seller_user_id
     */
    public function seller_before_7_days_reply_percent($seller_user_id,$seconds)
    {
        $seller_reply_obj = POCO::singleton('pai_sendserver_seller_reply_class');
        return $seller_reply_obj->get_seven_reply_by_seller_id($seller_user_id,$seconds);
    }
    
    /**
     * �̼��Ƽ�
     * @param type $seller_user_id
     * @return boolean
     */
    public function seller_is_recommend($seller_user_id)
    {
        //�����콻��
        $before_7_days_succes_order_total = $this->seller_before_7_days_succes_order_total($seller_user_id);
        if($before_7_days_succes_order_total < 1)
        {
            $this->seller_update_is_recommend($seller_user_id, 0);
            return false;
        }
        
        //���������۷���
        $before_7_days_comment_point = $this->seller_before_7_days_comment_point($seller_user_id);
        if($before_7_days_comment_point < 4.5)
        {
            $this->seller_update_is_recommend($seller_user_id, 0);
            return false;
        }
        
        //������ظ��ٷֱ� �Ȳ���
//        $before_7_days_reply_percent = $this->seller_before_7_days_reply_percent($seller_user_id,600);
//        if($before_7_days_reply_percent < 0.7)
//        {
//            $this->seller_update_is_recommend($seller_user_id, 0);
//            return false;
//        }
        
        $this->seller_update_is_recommend($seller_user_id, 1);
        return true;
    }
    
    /**
     * �����̼��Ƽ�
     * @param type $seller_user_id
     * @param type $is_recommend
     * @return boolean
     */
    public function seller_update_is_recommend($seller_user_id,$is_recommend)
    {
        $this->set_mall_seller_tbl();
        $this->update(array('is_recommend '=>$is_recommend),"user_id='{$seller_user_id}'");
        
        $this->set_mall_seller_profile_tbl();
        $this->update(array('is_recommend'=>$is_recommend),"user_id='{$seller_user_id}'");
        
        return true;
        
    }
    
    /**
     * �����̼Ҽ�¼
     * @param type $seller_user_id
     * @return boolean
     */
    public function seller_find_one($seller_user_id)
    {
        $seller_user_id = (int)$seller_user_id;
        if( ! $seller_user_id )
        {
            return false;
        }
        
        $this->set_mall_seller_tbl();
        return $this->find("user_id='$seller_user_id'");
    }
    
    //��Ʒ�����۷�������Ʒ����ֵ
    public function seller_goods_total_point_and_goods_statistical_step($seller_user_id)
    {
        $seller_user_id = (int)$seller_user_id;
        if( ! $seller_user_id )
        {
            return false;
        }
        
        //һ�� ���� ....
        $seller_goods_list = $this->seller_get_goods_list($seller_user_id);
        if( ! empty($seller_goods_list) )
        {
            foreach($seller_goods_list as $k => $v)
            {
                $res[$v['goods_id']] = $this->seller_get_comment_total_order($v['user_id'], $v['goods_id']);
            }
        }
        
        //�ܵ���
        $start_time = strtotime(date('Y-m-d',strtotime('-30days')));
        $end_time = strtotime(date('Y-m-d'))+86400;
        $type_id = -1;
        $reject_order_goods_list_total = $this->seller_get_reject_order_goods_list_total($type_id,$seller_user_id, $start_time, $end_time);
        if( ! empty($reject_order_goods_list_total) )
        {
            foreach($reject_order_goods_list_total as $k => $v)
            {
                $res_reject[$v['goods_id']] = $v;
            }
        }
        
        $seller_one = $this->seller_find_one($seller_user_id);
        //�Ƽ�
        if($seller_one['is_recommend'] == 1)
        {
            $is_recommend = 3;
        }else
        {
            $is_recommend = 0;
        }
        //�ȼ�����
        $seller_level_point = (int)$seller_one['seller_level_point'];
        
        foreach($res as $k => $v)
        {
            if( ! empty($v['1']) )
            {
                $first = $v['1']['count'];
            }else
            {
                $first = 0;
            }
            
            if( ! empty($v['2']) )
            {
                $second = $v['2']['count'];
            }else
            {
                $second = 0;
            }
            
            if( ! empty($v['3']) )
            {
                $third = $v['3']['count'];
            }else
            {
                $third = 0;
            }
            
            if( ! empty($v['4']) )
            {
                $fourth = $v['4']['count'];
            }else
            {
                $fourth = 0;
            }
            
            if( ! empty($v['5']) )
            {
                $fifth = $v['5']['count'];
            }else
            {
                $fifth = 0;
            }
            
            //�Ǽ��ĳ˷�ϵ��-2 -1 0 1 2 
            //-2(�ܵ���)
            
            //��Ʒ�ľܵ���
            if( ! empty($res_reject[$k]) )
            {
               $reject_total = $res_reject[$k]['count'];
            }else
            {
               $reject_total = 0;
            }
            
            $comment_total = $first+$second+$third+$fourth+$fifth;
            if($comment_total == 0)
            {
               $total_comment_point = 0;
            }else
            {
               $total_comment_point = ( $first*(-2)+$second*(-1)+$third*0+$fourth*1+$fifth*2+(-2)*$reject_total )/$comment_total;
            
               $total_comment_point = round($total_comment_point,1);
            }
            
            $this->set_mall_goods_tbl();
            $this->update(array('total_comment_point'=>$total_comment_point),"goods_id='{$k}'");
            
            $goods_total_point = $total_comment_point;
            $statistical_step = 2*$seller_level_point+$is_recommend*$seller_level_point+$goods_total_point;
            $statistical_step = round($statistical_step,2);

            //������ֵ���µ�ͳ�Ʊ�
            $this->set_mall_goods_statistical_tbl();
            $this->update(array('step'=>$statistical_step),"goods_id='{$k}'");

            $this->seller_update_goods_type_id_tbl($k);

            unset($statistical_step);
            unset($goods_total_point);
            
            unset($total_comment_point);
            unset($reject_total);
            unset($comment_total);
            unset($first);
            unset($second);
            unset($third);
            unset($fourth);
            unset($fifth);
        }
        
        return true;
        
    }
    
    //��Ʒ��������δ������
    public function seller_goods_total_point_and_goods_statistical_step_and_seller_step_new($seller_user_id)
    {
        $seller_user_id = (int)$seller_user_id;
        if( ! $seller_user_id )
        {
            return false;
        }
        
        //��Ʒһ�� ���� ....
        $seller_goods_list = $this->seller_get_goods_list($seller_user_id);
        if( ! empty($seller_goods_list) )
        {
            foreach($seller_goods_list as $k => $v)
            {
                $res[$v['goods_id']] = $this->seller_get_comment_total_order($v['user_id'], $v['goods_id'],false);
            }
        }
        
        
        foreach($res as $k => $v)
        {
            if( ! empty($v['1']) )
            {
                $first = $v['1']['count'];
            }else
            {
                $first = 0;
            }
            
            if( ! empty($v['2']) )
            {
                $second = $v['2']['count'];
            }else
            {
                $second = 0;
            }
            
            if( ! empty($v['3']) )
            {
                $third = $v['3']['count'];
            }else
            {
                $third = 0;
            }
            
            if( ! empty($v['4']) )
            {
                $fourth = $v['4']['count'];
            }else
            {
                $fourth = 0;
            }
            
            if( ! empty($v['5']) )
            {
                $fifth = $v['5']['count'];
            }else
            {
                $fifth = 0;
            }
            
            //�̼��Ǽ�����
            $first_total += $first;
            $second_total += $second;
            $third_total += $third;
            $fourth_total += $fourth;
            $fifth_total += $fifth;
            
            //��Ʒ��������  һ�Ƕ�����*0.1+���Ƕ�����*0.2+���Ƕ�����*0.3+���Ƕ�����*0.4+���Ƕ�����*0.5
            $total_comment_point = $first*0.1+$second*0.2+$third*0.3+$fourth*0.4+$fifth*0.5;
            
            $this->set_mall_goods_tbl();
            //��Ʒ��������
            $this->update(array('total_comment_point'=>$total_comment_point),"goods_id='{$k}'");
            
            $row_total_number_and_money = $this->seller_get_goods_id_finish_order_number_and_money($k);
            
            //��������
            $success_total_order = $row_total_number_and_money['total_number'];
            
            //�̼Ҷ�������
            $total_success_total_order += $success_total_order;
            
            //��Ʒ����ֵ��ʽ  ���״���*����Ȩ��+����ֵ*����Ȩ��
            $statistical_step = $success_total_order*0.1+$total_comment_point*1;
            //$statistical_step = round($statistical_step,2);

            //����Ʒ����ֵ���µ�ͳ�Ʊ�
            $this->set_mall_goods_statistical_tbl();
            $this->update(array('step'=>$statistical_step),"goods_id='{$k}'");

            $this->seller_update_goods_type_id_tbl($k);

            unset($statistical_step);
            unset($total_comment_point);
            unset($total_success_total_order);
            unset($first);
            unset($second);
            unset($third);
            unset($fourth);
            unset($fifth);
        }
        
        //�����̼�����
        $seller_comment_point = $first_total*0.1+$second_total*0.2+$third_total*0.3+$fourth_total*0.4+$fifth_total*0.5;
        $seller_level_point = $total_success_total_order*0.1+$seller_comment_point*1;
        $this->set_mall_seller_tbl();
        $this->update(array('seller_level_point'=>$seller_level_point), "user_id='$seller_user_id'");
        unset($fifth_total);
        unset($second_total);
        unset($third_total);
        unset($fourth_total);
        unset($fifth_total);
        unset($seller_comment_point);
        unset($seller_level_point);
        unset($total_success_total_order);
        
        return true;
        
    }
    
    
    /**
     * ��ȡĳ��goods_id һ�Ƕ��ǵȵȶ������ж���
     * @param type $seller_user_id
     * @param type $goods_id
     * @return boolean|int
     */
    public function seller_get_comment_total_order($seller_user_id,$goods_id,$only_user_id=false)
    {
        $seller_user_id = (int)$seller_user_id;
        $goods_id = (int)$goods_id;
        if( ! $seller_user_id )
        {
            return false;
        }
        
        if( ! $only_user_id )
        {
            if( ! $goods_id)
            {
                return false;
            }
        }
        
        $this->set_mall_comment_seller_tbl();
        $res = array();
        if( ! $only_user_id )
        {
           $rs = $this->findAll("to_user_id='{$seller_user_id}' and goods_id='{$goods_id}'"); 
        }else
        {
           $rs = $this->findAll("to_user_id='{$seller_user_id}'"); 
        }
        
        if( ! empty($rs) )
        {
            $ary_keys = array(1,2,3,4,5);
            foreach($rs as $k => $v)
            {
                foreach($ary_keys as $kk => $vk)
                {
                    if($v['overall_score']==$vk)
                    {
                        $res[$vk]['count']+=1;
                    }
                }
            }
        }
        
        return $res;
        
    }
    
    /**
     * ��ȡ�̼Ҿܵ�������Ʒ�б�
     * @param type $type_id
     * @param type $user_id
     * @param type $start_time
     * @param type $end_time
     * @return type
     */
    public function seller_get_reject_order_goods_list_total($type_id, $user_id, $start_time, $end_time)
    {
        $order_obj = POCO::singleton('pai_mall_order_class');
        
        return $order_obj->get_order_refuse_by_lasting($type_id,$user_id,$start_time,$end_time);
    }
    
    
    /**
     * ��ȡ�̼���������Ʒ
     * @param type $seller_user_id
     * @param type $status
     * @return boolean
     */
    public function seller_get_goods_list($seller_user_id)
    {
        $seller_user_id = (int)$seller_user_id;
        if( ! $seller_user_id )
        {
            return false;
        }
        $this->set_mall_goods_tbl();
        //return $this->findAll("user_id='$seller_user_id' and status='$status'", null,'goods_id desc', 'user_id,goods_id');
        return $this->findAll("user_id='$seller_user_id'", null,'goods_id desc', 'user_id,goods_id');
    }
    
}
