<?php
/**
 * 测试类
 * 
 * @author ljl
 */

class A extends POCO_TDG
{
    public function test()
    {
        return (1+1)."A";
    }
}

class B extends POCO_TDG
{
    public function test()
    {
        return (1*1)."B";
    }
}

class pai_mall_test_ljl_class extends POCO_TDG
{
    
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}
	
	/**
	 *
	 */
	private function set_mall_goods_tbl()
	{
		$this->setTableName('mall_goods_tbl');
	}
    
    private function set_mall_direct_order_config_tbl()
    {
        $this->setTableName('mall_direct_order_config_tbl');
    }
    
    
    private function set_mall_goods_prices_tbl()
    {
        $this->setTableName('mall_goods_prices_tbl');
    }
    
    private function set_mall_seller_tbl()
    {
        $this->setTableName('mall_seller_tbl');
    }
    
    private function set_mall_admin_acl_tbl()
    {
        $this->setTableName('mall_admin_acl_tbl');
    }
    
    /**
     * 更新所有的子级父级
     */
    public function update_all_parents_childs()
    {
        $this->set_mall_admin_acl_tbl();
        $all_data = $this->get_acl_cate();
        foreach($all_data as $v)
        {
            $update_data = array();
            
            $parents_list = $this->get_all_parents($v['id']);
            $child_list = $this->get_all_childs($v['id']);
            //数组反序
            $parents_list = array_reverse($parents_list);
            $child_list = array_reverse($child_list);
            
            $update_data['children_list'] = implode(",",$child_list);
            $update_data['parent_list'] = implode(",",$parents_list);
            $this->update($update_data,"id='{$v['id']}'");
        }
        
    }
    
    /**
     * 权限操作无限级分类
     * @param type $pid
     * @param type $level
     * @param type $res
     * @return type
     */
    public function get_acl_cate($pid = 0,$level = 0 ,$res = array())
    {
        $pid = (int)$pid;
        $this->set_mall_admin_acl_tbl();
        $sql = "parent_id='$pid' ";
		$data = $this->findAll($sql,'','id asc');
        foreach($data as $v)
        {
            $v['level'] = $level;
            $res[] = $v;
            $res = $this->get_acl_cate( $v['id'],$level+1,$res); 
		}
		return $res;
    }
    
    /**
     * 获取所有子级
     * @param type $id
     * @param type $res
     * @return type
     */
    public function get_all_childs($id,$res = array())
    {
        $this->set_mall_admin_acl_tbl();
        $data = $this->findAll("parent_id='$id' ",'','id ASC','id');
        foreach($data as $v)
        {
			$res[] = $v['id'];
            $res = $this->get_all_childs($v['id'],$res); 
		}
		
		return $res;
    }
    
    /**
     * 获取所有父级
     * @param type $id
     * @param type $res
     * @return type
     */
    public function get_all_parents($id,$res = array())
    {
         $this->set_mall_admin_acl_tbl();
         $data = $this->findAll("id='$id'",'','id asc','parent_id');
         foreach($data as $v)
         {
             $res[] = $v['parent_id'];
             $res = $this->get_all_parents($v['parent_id'],$res);
         }
		 
         return $res;
    }
    
    public function insert_admin_acl()
    {
        exit;
        $admin_power_config = pai_mall_load_config('admin_power');
        ljl_dump($admin_power_config);
        
        $this->set_mall_admin_acl_tbl();
        foreach($admin_power_config as $k => $v)
        {
                $unit = array();
                $unit = array(
                  'name'=>$k,
                  'parent_id'=>0,
                  'val'=>$k,  
                );
                $id = $this->insert($unit,"IGNORE");
                if( $id )
                {
                    foreach($v as $vk => $vv)
                    {
                        $unit = array();
                        $unit = array(
                            'name'=>$vv['name'],
                            'parent_id'=>$id,
                            'val'=>$vv['file'],
                        );
                        $id = $this->insert($unit,"IGNORE");
                        if( $id )
                        {
                            foreach($vv['action'] as $vak => $vav)
                            {
                                $unit = array();
                                $unit = array(
                                    'name'=>$vav['name'],
                                    'parent_id'=>$id,
                                    'val'=>$vav['action_type'],
                                );
                               $this->insert($unit,"IGNORE");
                            }
                            
                        }
                    }
                }
        }
        return true;
        
        
    }
    
    public function format_goods_data($goods_info)
    {
        
        if(empty($goods_info))
        {
            return false;
        }
        
        $task_goods_obj = POCO::singleton('pai_mall_goods_class');
        if($goods_info['goods_data']['type_id'] == 42)
        {
            
            //新数据
            $new_data = $task_goods_obj->get_mall_goods_check($goods_info['goods_data']['goods_id']);
            
            
            //default_data 数据新旧重组
            
//            [default_data] => Array
//        (
//            [titles] => 拍细佬哥
//            [location_id] => 101008009
//            [content] => <p><img class="" src="http://image19-d.yueus.com/yueyue/20151120/20151120091701_605460_129338_55949.jpg?400x400_120"/><img class="" src="http://image19-d.yueus.com/yueyue/20151120/20151120091701_960892_129338_55950.jpg?640x360_120"/><img class="" src="http://image19-d.yueus.com/yueyue/20151120/20151120091702_299772_129338_55951.png?726x543_130"/><img class="" src="http://image19-d.yueus.com/yueyue/20151120/20151120091703_21265_129338_55952.png?800x799_130"/><img class="" src="http://image19-d.yueus.com/yueyue/20151120/20151120091704_642135_129338_55953.png?370x439_130"/></p>
//            [lng_lat] => 124.826805,45.116287
//        )
            if( ! empty($new_data['default_data']) )
            {
                foreach($new_data['default_data'] as $k => $v)
                {
                    foreach($goods_info['default_data'] as $ko => $vo)
                    {
                        if($vo['key'] == $k)
                        {
                            $goods_info['default_data'][$ko]['value'] = $v;
                            break;
                        }
                    }
                }
            }
            
            //contact_data 数据新旧重组
//             [contact_data] => Array
//        (
//            [48f41e2c13b5c1182666e6b6d665f890] => Array
//                (
//                    [name] => 的说法
//                    [phone] => 254654344
//                )
//
//            [5cf4e99c29dc6edc6ea46917584cf264] => Array
//                (
//                    [name] => 363发电公司
//                    [phone] => 242543213
//                )
//
//            [8f40ca553f9dc27f6460a3c57c536c2f] => Array
//                (
//                    [name] =>   个地方官
//                    [phone] => 323332323
//                )
//
//        )
            if( ! empty($new_data['contact_data']) )
            {
                foreach($new_data['contact_data'] as $k => $v)
                {
                    foreach($goods_info['contact_data'] as $ko => $vo)
                    {
                        if($k == $vo['name'])
                        {
                            $goods_info['contact_data'][$ko]['data']['name']= $v['name'];
                            $goods_info['contact_data'][$ko]['data']['phone']= $v['phone'];
                            break;
                        }
                    }
                }
            }
            
            //image_data 新旧数据重组
//             [img] => Array
//        (
//            [0] => Array
//                (
//                    [img_url] => http://image19-d.yueus.com/yueyue/20151120/20151120091643_484879_129338_55948_260.jpg?690x388_120
//                )
//
//        )
            if( ! empty($new_data['img']) )
            {
                $goods_info['image_data']['value'] = $new_data['img'];
            }
            
            //system_data 新旧数据重组
//            [system_data] => Array
//        (
//            [39059724f73a9969845dfe4146c5660e] => d947bf06a885db0d477d707121934ff8
//            [d947bf06a885db0d477d707121934ff8] => bca82e41ee7b0833588399b1fcd177c7
//            [00ec53c4682d36f5c4359f4ae7bd7ba1] => 101029001
//            [7a614fd06c325499f1680b9896beedeb] => 越秀区
//            [4734ba6f3de83d861c3176a6273cac6d] => 注意数据在商家APP端＼消费者APP＼PC端的显示。
//        )
            if( ! empty($new_data['system_data']) )
            {
                foreach($new_data['system_data'] as $k => $v)
                {
                    foreach($goods_info['system_data'] as $ko => $vo)
                    {
                        //如果是摄影外拍
                        if($k == 'd947bf06a885db0d477d707121934ff8' )
                        {
                            //如果是主题类别
                            if($vo['key'] == '39059724f73a9969845dfe4146c5660e')
                            {
                                foreach($vo['child_data'] as $ck => $cv)
                                {
                                    foreach($cv['child_data'] as $cvk => $cvv)
                                    {
                                        unset($goods_info['system_data'][$ko]['child_data'][$ck]['child_data'][$cvk]['is_select']);
                                        if($v == $cvv['key'])
                                        {
                                            $goods_info['system_data'][$ko]['child_data'][$ck]['child_data'][$cvk]['is_select'] = 1;
                                        }
                                    }
                                    if($cv['key'] == $k)
                                    {
                                        $goods_info['system_data'][$ko]['child_data'][$ck]['value'] = $v;
                                    }
                                }
                            }
                            
                        }
                        if($vo['key'] == $k)
                        {
                            $goods_info['system_data'][$ko]['value'] = $v;
                            break;
                        }
                    }
                }
            }
            
            
            return $goods_info;
            
            
        }else
        {
            return $goods_info;
        }
        
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
    
    
    public function test($obj)
    {
        return $obj->test();
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
    
    
    public function copy_goods_info_to_user_id($goods_id,$user_id)
    {
       $goods_id = (int)$goods_id;
       $user_id = (int)$user_id;
       if( ! $goods_id || ! $user_id )
       {
           return false;
       }
       $org_goods_info = $this->get_org_goods_info($goods_id);
       ljl_dump($org_goods_info);
       if(empty($org_goods_info))
       {
           return false;
       }
       
       $pacing_new_goods_id_ary = array();
       //组装新goods_id的数组
       $packing_new_goods_id_ary = $this->packing_new_goods_id_ary($org_goods_info,$user_id);
       ljl_dump($packing_new_goods_id_ary);
       
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
                    if( in_array($v['key'],array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd')) )
                    {
                        $v['value'] = unserialize($v['value']);
                        foreach($v['value'] as $dk => $dv)
                        {
                            $system_specical[$dv['key']] = $dv['value'];
                        }
                        $v['value'] = $system_specical;
                        $system_unit[$v['key']] = $v['value'];
                    }else
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
    
    public function type_id_good_data_insert($data= array(),$is_pass = 0,$is_show = 0)
	{
        
        if( ! empty($data) )
        {
                
            $goods_obj = POCO::singleton('pai_mall_goods_class');

            $rs = $goods_obj->add_goods($data);

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
    
    
    public function insert_test()
    {
        exit('no');
        $this->set_mall_goods_tbl();
        $rs = $this->findAll("user_id='110757' or user_id ='128808' or user_id='129093'");
        foreach($rs as $k => $v)
        {
            $unit = array();
            $unit['type_id'] = $v['type_id'];
            $unit['goods_id'] = $v['goods_id'];
            $unit['location_id'] = 101029002;
            $unit['service_time'] = 1451577599;
            $unit['add_time'] = 1451577599;
            $this->set_mall_direct_order_config_tbl();
            $this->insert($unit);
        }
        exit('ok');
    }
    
    
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
            'max_price'=>$max_price,
            'min_price'=>$min_price
        );
    }
    
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
        
        $total_show = $ing_show = $has_join_num = $min_price = $max_price = 0;
        $prices_list = $price_arys = array();
        if( ! empty($goods_info['goods_data']['prices_de']))
        {
            $total_show = count($goods_info['goods_data']['prices_de']);
            
            foreach($goods_info['goods_data']['prices_de'] as $k => $v)
            {
                if(time() > $v['time_s'])
                {
                    $ing_show++;
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
            'total_show'=>$total_show,
            'ing_show'=>$ing_show,
            'has_join_num'=>$has_join_num,
            'min_price'=>$min_price,
            'max_price'=>$max_price
        );
        
        return $rs;
        
    }
    
    
    
    public function get_goods_property_number($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
    }
    //2124194
    public function do_update($goods_id)
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
                            $price_list_ary[$kp] = $vp;
                        }
                    }
                    
                }else
                {
                    if( ! empty($v['type_id']))
                    {
                         $price_list_ary[$v['type_id']] = array(
                            'prices'=>$v['prices'],
                         );
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
    
    public function excel_upload()
    {
        
    }
    
    public function read_excel()
    {
        
    }
    
    
    
    
    
}
