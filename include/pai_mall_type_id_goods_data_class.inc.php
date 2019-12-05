<?php
/**
 * 类别的商品类
 * 
 * @author ljl
 */

class pai_mall_type_id_goods_data_class extends POCO_TDG
{
    
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}
    
    
    /**
     *  获取原来的商品的情况
     * @param type $goods_id
     * @return boolean
     */
    public function get_org_goods_info($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $goods_obj = POCO::singleton('pai_mall_goods_class');
        return $goods_obj->get_goods_info($goods_id);
    }
    
    
    /**
     * 复制某个商品id到某个用户id
     * @param type $goods_id
     * @param type $user_id
     * @return boolean
     */
    public function copy_goods_info_to_user_id($goods_id,$user_id)
    {
       $goods_id = (int)$goods_id;
       $user_id = (int)$user_id;
       if( ! $goods_id || ! $user_id )
       {
           return false;
       }
       $org_goods_info = $this->get_org_goods_info($goods_id);
       
       if(empty($org_goods_info))
       {
           return false;
       }
       
       $pacing_new_goods_id_ary = array();
       //组装新goods_id的数组
       $packing_new_goods_id_ary = $this->packing_new_goods_id_ary($org_goods_info,$user_id);
       
       if( ! empty($packing_new_goods_id_ary) )
       {
          $this->type_id_good_data_insert($packing_new_goods_id_ary);
       } 
       
    }
    
    /**
     * 组装新的goods_id的数据
     * @param type $org_ary
     * @param type $user_id
     * @return type
     */
    public function packing_new_goods_id_ary($org_ary,$user_id)
    {
        $unit = array();
        if( ! empty($org_ary) )
        {
            $store_id = 0;
            $seller_obj = POCO::singleton('pai_mall_seller_class');
            $store_id = $seller_obj->get_seller_store_id($user_id);
            if( ! (int)$store_id )
            {
                return false;
            }
            $unit['action'] = 'add';
            $unit['type_id'] = $org_ary['goods_data']['type_id'];
            $unit['store_id'] = $store_id;
            $default_data_unit = array();
            if( ! empty($org_ary['default_data']) )
            {
                foreach($org_ary['default_data'] as $k => $v)
                {
                    $default_data_unit[$v['key']] = $v['value'];
                }
            }
            $unit['default_data'] = $default_data_unit;
            
            //属性
            $system_unit = $system_specical = array();
            
            if( ! empty($org_ary['system_data']) )
            {
                foreach($org_ary['system_data'] as $k => $v)
                {
                    //如果是摄影服务的三个套餐 就特殊处理
//                    if( in_array($v['key'],array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd')) )
//                    {
//                        $v['value'] = unserialize($v['value']);
//                        foreach($v['value'] as $dk => $dv)
//                        {
//                            $system_specical[$dv['key']] = $dv['value'];
//                        }
//                        $v['value'] = $system_specical;
//                        $system_unit[$v['key']] = $v['value'];
//                    }
					if(in_array($v['key'],array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
					{
                        $v['value'] = unserialize($v['value']);
                        foreach($v['value'] as $dk => $dv)
                        {
                            $system_specical[$dv['key']] = $dv['value'];
                        }
                        $v['value'] = $system_specical;
                        $system_unit[$v['key']] = $v['value'];
					}
					else
                    {
                        $v['value'] = explode(",",$v['value']);
                        if( ! empty($v['value']) )
                        {
                            if(count($v['value']) == 1)
                            {
                                $system_unit[$v['key']] = $v['value']['0'];
                                //如果有子数据
                                if( ! empty($v['child_data']) )
                                {
                                    foreach($v['child_data'] as $ck => $cv)
                                    {
                                        if($cv['value'] !='')
                                        {
                                            $system_unit[$v['value']['0']] = $cv['value'];
                                        }
                                    }
                                }
                            }else
                            {
                                foreach($v['value'] as $vk => $vv)
                                {
                                    $system_unit[$v['key']][] = $vv;
                                }
                            }
                        }
                    }
                    
                    
                    
                }
            }
            $unit['system_data'] = $system_unit;
            
            //图片
            $img_unit = array();
            if( ! empty($org_ary['goods_data']['img']) )
            {
                foreach($org_ary['goods_data']['img'] as $k => $v)
                {
                    $img_unit[] = array('img_url'=>$v['img_url']);
                }
            }
            $unit['img'] = $img_unit;
            
            
            //price_de
            if(in_array($org_ary['goods_data']['type_id'],array(40,31,3,12,43)) )
            {
                //价钱
                $prices_de_unit = array();
                if( ! empty( $org_ary['goods_data']['prices_de'] ) )
                {
                    foreach($org_ary['goods_data']['prices_de'] as $k => $v)
                    {
                        $prices_de_unit[$v['type_id']] = $v['prices'];
                    }
                }
                $unit['prices_de'] = $prices_de_unit;
            }
            
            //price_diy 
            if($org_ary['goods_data']['type_id'] == 41)
            {
                $price_diy_unit = array();
                if( ! empty( $org_ary['goods_data']['prices_de'] ) )
                {
                    foreach($org_ary['goods_data']['prices_de'] as $k => $v)
                    {
                        $price_diy_unit_key = '';
                        $price_diy_unit_key = time().rand(100000,999999);
                        $price_diy_unit[$price_diy_unit_key] = array(
                            'name'=>$v['name'],
                            'time_s'=>date('Y-m-d H:i:s',$v['time_s']),
                            'time_e'=>date('Y-m-d H:i:s',$v['time_e']),
                            'stock_num'=>$v['stock_num'],
                            'prices'=>$v['prices'],
                        );
                    }
                }
                
                $unit['prices_diy'] = $price_diy_unit;
            }
            
            //活动类的
            if($org_ary['goods_data']['type_id'] == 42)
            {
                //领队
                $contact_data_unit = array();
                if( ! empty($org_ary['contact_data']))
                {
                    foreach($org_ary['contact_data'] as $k => $v)
                    {
                        $contact_data_unit_key = '';
                        $contact_data_unit_key = time().rand(100000,999999);
                        $contact_data_unit[$contact_data_unit_key] = array(
                            'name'=>$v['data']['name'],
                            'phone'=>$v['data']['phone'],
                        );
                    }
                }
                $unit['contact_data'] = $contact_data_unit;
                
                //price_diy
                $price_diy_unit = array();
                if( ! empty($org_ary['prices_data']))
                {
                    foreach($org_ary['prices_data'] as $pk => $pv)
                    {
                        $price_diy_unit_key = '';
                        $price_diy_unit_key = time().rand(100000,999999);
                        
                        $detail_unit = array();
                        if( ! empty($pv['prices_list_data']) )
                        {
                            foreach($pv['prices_list_data'] as $plk => $plv )
                            {
                                $detail_unit['name'][] = $plv['name'];
                                $detail_unit['prices'][] = $plv['prices'];
                            }
                        }
                        
                        $price_diy_unit[$price_diy_unit_key] = array(
                            'name'=>$pv['name'],
                            'time_s'=>date('Y-m-d H:i:s',$pv['time_s']),
                            'time_e'=>date('Y-m-d H:i:s',$pv['time_e']),
                            'stock_num'=>$pv['stock_num'],
                            'detail'=>$detail_unit,
                        );
                    }
                    
                    $unit['prices_diy'] = $price_diy_unit;
                }
            }
            
        }
        
        return $unit;
    }
    
	
    /**
     * @param type $data 数据
     * @param type $is_pass 是否自动通过审核 1为自动 0为不自动
     * @param type $is_show 是否自动上架    1为自动 0为不自动
     */
	public function type_id_good_data_insert($data= array(),$is_pass = 0,$is_show = 0)
	{
        
        if( ! empty($data) )
        {
                
            $goods_obj = POCO::singleton('pai_mall_goods_class');

            $rs = $goods_obj->add_goods_test($data);

            if( (int)$rs['result'] > 0 )
            {
                //是否自动通过审核
                if($is_pass)
                {
                    $goods_status_rs =$goods_obj->change_goods_status($rs['result'],1,$v['user_id']);
                    if($goods_status_rs['result']!=1)
                    {
                        dump($goods_status_rs);
                    }
                }

                //是否自动上架
                if($is_show)
                {
                    $goods_show_status_rs = $goods_obj->user_change_goods_show_status($rs['result'],1,$v['user_id']);
                    if($goods_show_status_rs['result'] != 1)
                    {
                        dump($goods_show_status_rs);
                    }
                }
                
                dump("goods_id:".$rs['result']);
                return $rs['result'];
                
            }else
            {
                dump($rs);
            }
               
                
            
        }
        
	}
    
    
    
    
}
