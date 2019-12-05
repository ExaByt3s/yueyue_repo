<?php
/**
 * 分类属性
 * 
 * @author ljl
 */

class pai_mall_goods_type_attribute_class extends POCO_TDG
{
	public $_redis_cache_name_prefix = "G_YUEUS_MALL_GOODS_TYPE_ATT";
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
	private function set_mall_goods_type_tbl()
	{
		$this->setTableName('mall_goods_type_tbl');
	}	

	/**
	 * 
	 */
	private function set_mall_goods_type_attribute_tbl()
	{
		$this->setTableName('mall_goods_type_attribute_tbl');
	}
    
    private function set_mall_goods_type_attribute_key_tbl()
    {
        $this->setTableName('mall_goods_type_attribute_key_tbl');
    }
    
    /**
     * 根据5值去获取相应的name
     * @param type $md5_key
     * @return null
     */
   public function get_name_by_md5_key($md5_key)
    {
        $this->set_mall_goods_type_attribute_tbl();
        $all_list = $this->findAll("status='1'");
        
        foreach($all_list as $k => $v)
        {
            $match_data[md5($v['id'])] = $v['name'];
        }
        
        $md5_key = trim($md5_key);
        
        if( ! empty($match_data[$md5_key]) )
        {
            return $match_data[$md5_key];
        }else
        {
            return null;
        }
    }
    
    /**
     * 根据key 值去找name
     * @param type $key
     * @return null
     */
    public function get_name_by_key($key)
    {
        $default_config = pai_mall_load_config('goods_default_attribute');
        foreach($default_config as $k => $v)
        {
            $match_data[$v['key']] = $v['name'];
        }
        if($match_data[$key])
        {
            return $match_data[$key];
        }else
        {
            return null;
        }
    }
	
	/**
     * 获取所有父
     */
	public function get_all_parents($id,$res = array())
	{
		$this->set_mall_goods_type_attribute_tbl();
         $data = $this->findAll("id='$id' and status=1",'','step asc','parents_id');
         foreach($data as $v)
         {
             $res[] = $v['parents_id'];
             $res = $this->get_all_parents($v['parents_id'],$res);
         }
		 
         return $res;
	}
	
	/**
     * 获取所有子
     */
	public function get_all_childs($id,$res = array())
	{
		$this->set_mall_goods_type_attribute_tbl();
        $data = $this->findAll("parents_id='$id' and status=1",'','step ASC','id');
        foreach($data as $v)
        {
			$res[] = $v['id'];
            $res = $this->get_all_childs($v['id'],$res); 
		}
		
		return $res;
	}
	
	/**
     * 更新所有的子级父级
     */
    public function update_all_parents_childs()
    {
        $this->set_mall_goods_type_attribute_tbl();
        $all_data = $this->get_type_attribute_cate();
        foreach($all_data as $v)
        {
            $update_data = array();
            $parents_list = array();
            $child_list = array();
            $parents_list = $this->get_all_parents($v['id']);
            $child_list = $this->get_all_childs($v['id']);
            //数组反序
            $child_list = array_reverse($child_list);
            $parents_list = array_reverse($parents_list);
            
            $update_data['child_list'] = implode(",",$child_list);
            $update_data['parents_list'] = implode(",",$parents_list);
            $update_data['add_time'] = time();
            $this->update($update_data,"id='{$v['id']}'");
        }
    }
    
    /**
     * 批量更新插入attribute_key表
     */
    public function batch_insert_attribute_key()
    {
        //获取所有的顶级 ,输入类型为单选多选的
        $this->set_mall_goods_type_attribute_tbl();
        $parents = $this->findAll("parents_id='0' and (input_type_id='1' or input_type_id='2')");
        
        foreach($parents as $k => $v)
        {
            $childs = $this->get_type_attribute_cate($v['id'], 0 ,array(),$v['goods_type_id']);

             foreach($childs as $ck => $cv)
             {
                 $type_id = $cv['parents_id']. "|" . $cv['id'];
                 $key_val = md5(md5($cv['parents_id']). "|" . md5($cv['id']));
                 $this->set_mall_goods_type_attribute_key_tbl();
                 $key_one = $this->find("key_val='{$key_val}'");
                 
                 if( ! $key_one )
                 {
                     $this->insert(array('key_val'=>$key_val,'type_id'=>$type_id));
                     unset($type_id);
                     unset($key_val);
                     unset($key_one);
                 }
             }
        }
    }
    
    /**
     * 根据数组 type_id 拿id
     * @param type $type_id
     * @return boolean
     */
    public function get_id_by_type_id($type_id)
    {
        if( empty($type_id) )
        {
            return false;
        }
        foreach($type_id as $k => &$v)
        {
            $type_id[$k] = "'".$v."'";
        }
        $type_arys = implode(",", $type_id);
        
        $this->set_mall_goods_type_attribute_key_tbl();
        return $this->findAll("key_val in ( $type_arys )");
    }
    
    /**
     * 保存
     * @param type $data
     * @return type
     */
    public function do_save($post)
    {
        $this->set_mall_goods_type_attribute_tbl();
        
        //数据过滤
        $data['name'] = addslashes($post['name']);
        $data['parents_id'] = (int)$post['parents_id'];
        $data['status'] = 1;
        $data['step'] = (int)($post['step']);
        $data['input_type_id'] = (int)($post['input_type_id']);
        $data['goods_type_id'] = (int)$post['goods_type_id'];
        $data['add_time'] = time();
        $data['user_id'] = (int)$post['user_id'];
        $data['id'] = $post['id'] ? (int)$post['id'] : '';
        
        if( $data['id'] == '')
        {
            //do insert
            unset($data['id']);
            $id = $this->insert($data,"REPLACE");
            //批量插入属性
            $this->batch_insert_attribute_key();
			
			$this->update_all_parents_childs();
            
            //删除缓存数据
            $this->del_property_for_search_get_data();
			
            return $id;
        }else
        {
            //子级不能作为父级
            $get_child = $this->get_type_attribute_cate($data['id']);
            foreach($get_child as $k => $v){
                if($v['id']==$data['parents_id'])
                {
                    return false;
                }
            }
            //do update
            $id = $data['id'];
            unset($data['id']);
            $rs = $this->update($data,"id='$id'");
            //批量插入属性
            $this->batch_insert_attribute_key();
			
			$this->update_all_parents_childs();
            
            //删除缓存数据
            $this->del_property_for_search_get_data();
			
            return $rs;
            
        }
    }
    
    /**
     * 删除,有子级的不能删除
     * @param type $id
     * @return boolean
     */
    public function del_one($id)
    {
        $this->set_mall_goods_type_attribute_tbl();
        $one = $this->check_have_parent($id);
        
        if(empty($one))
        {
            $rs = $this->update(array('status'=>2,'add_time'=>time()),"id='$id'");
			$this->update_all_parents_childs();
            
            //删除缓存数据
            $this->del_property_for_search_get_data();
            
			return $rs;
        }else
        {
            return false;
        }
    }
    
    /**
     * 检查是否有父级
     * @param type $id
     * @return type
     */
    public function check_have_parent($id)
    {
        $this->set_mall_goods_type_attribute_tbl();
        return $this->find("parents_id='$id' and status='1'");
    }
    
    /**
     * 检查是否有下级
     * @param type $md5_id
     * @return type
     */
    public function check_have_child($md5_id)
    {
		$type_attribute = $this->get_type_attribute_cate(0);
		$type_attribute_format = array();
		foreach($type_attribute as $val)
		{
			$type_attribute_format[md5($val['id'])] = $val;
		}
		$id = $type_attribute_format[$md5_id]['id'];
        $this->set_mall_goods_type_attribute_tbl();
        $re = $this->findAll("parents_id='$id' and status='1'");
		$return =array();
		if($re)
		{
			foreach($re as $val)
			{
				$return[] = md5($val['id']);
			}
		}
		return $return;
		
    }
    
    /**
     * 拿属性情况
     * @param type $id
     */
    public function get_property_info($id)
    {
        $this->set_mall_goods_type_attribute_tbl();
        return $this->find("id='$id'");
    }
    
    
    /**
     * 无限级分类
     * @param type $pid
     * @param type $level
     * @param type $res
     * @param type $goods_type_id
     * @return type
     */
    public function get_type_attribute_cate($pid = 0, $level = 0 ,$res = array(),$goods_type_id=0)
    {
        $this->set_mall_goods_type_attribute_tbl();
        $where = "";
        if($goods_type_id)
        {
            $where .= " and goods_type_id='$goods_type_id'";
        }
        $data = $this->findAll("parents_id='$pid' and status='1' $where",'','step,id asc');
        foreach($data as $v)
        {
			$v['level'] = $level;
            $res[] = $v;
            $res = $this->get_type_attribute_cate( $v['id'], $level+1 , $res ,$goods_type_id); 
		}
		return $res;
    }
    
	public function get_type_attribute_by_goods_type_id($type_id,$parents_id)
	{
		$this->set_mall_goods_type_attribute_tbl();
		$return = $this->findAll("goods_type_id='{$type_id}' and status='1' and parents_id='{$parents_id}'",'','step,id asc');
		foreach($return as $key => $val)
		{
			$return[$key]['child_data'] = $this->get_type_attribute_by_goods_type_id($type_id,$val['id']);
		}
		return $return;				
	}
    
    
	public function get_all_type_attribute_by_goods_type_id($type_id='')
	{
		$type_id = (int)$type_id;
		$this->set_mall_goods_type_attribute_tbl();
		if($type_id)
		{
			$return = $this->findAll("goods_type_id='{$type_id}'",'','step,id asc');
		}
		else
		{
			$return = $this->findAll();
		}
		return $return;				
	}
    
    
    /**
     * 拿第三级的父类,并组装数组
     * @param type $arys
     * @return type
     */
    public function get_third_parent($arys)
    {
        $detail = array();
        if( ! empty($arys['third']) )
        {
            foreach($arys['third'] as $k => $v)
            {
                $this->set_mall_goods_type_attribute_tbl();
                $info = $this->get_property_info($k);//只支持最多三级
                $detail['detail'][$info['parents_id']] = $v;
            }
        }
        
        return $detail;
        
    }
    
    /**
     * 删除属性搜索显示,取数据
     * @param type $type_id
     * @return boolean
     */
    public function del_property_for_search_get_data()
    {
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_data = $type_obj->get_type_cate(0);
		foreach($type_data as $val)
		{
			
			$cache_key = $this->_redis_cache_name_prefix."_SEARCH_TYPE_".$val['id'];
			POCO::deleteCache($cache_key);
		}
    }
    
    /**
     * 属性搜索显示,取数据
     * @param type $type_id
     * @return boolean
     */
    public function property_for_search_get_data($type_id=31,$is_test=false)
    {
		$type_id = (int)$type_id;
        
        //线上的做缓存
        if( ! $is_test )
        {
            $cache_key = $this->_redis_cache_name_prefix."_SEARCH_TYPE_".$type_id;
            $return = POCO::getCache($cache_key);
            if($return)
            {
                return $return;
            }
        }
		
        
        if($is_test)
        {
            $property_for_search_config = pai_mall_load_config('property_for_search_test');
        }else
        {
            $property_for_search_config = pai_mall_load_config('property_for_search');
        }
        
        
        if(empty($property_for_search_config[$type_id]))
        {
            return false;
        }
        foreach($property_for_search_config[$type_id] as $k => $v)
        {
            if( empty($v['data']) )
            {
                $parents_id = $v['function_param']['parent_id'];
                $child_data = $this->get_type_attribute_by_goods_type_id($type_id, $parents_id);
                
                if( ! empty($child_data) )
                {
                    foreach($child_data as $ck => $cv)
                    {
                        //对于摄影服务90,培训的133,382，450 活动的270属性要再取多层子
                        if($parents_id == 90 || $parents_id==133 || $parents_id == 270 || $parents_id ==382 || $parents_id==450)
                        {
                            $cc_data = $this->get_type_attribute_by_goods_type_id($type_id,$cv['id']);
                            if( ! empty($cc_data) )
                            {
                                foreach($cc_data as $cck => $ccv)
                                {
                                    $cc_ary['data'][$ccv['id']]= array('key'=>$ccv['id'],'val'=>$ccv['name']);
                                }
                                $cc_ary['name'] = "third[{$cv['id']}]";
                                $cc_ary['select_type'] = 1;
                            }
							array_unshift($cc_ary['data'],array('key'=>'','val'=>'全部'));
                            $child_ary[$cv['id']] = array(
                                'key'=>$cv['id'],
                                'val'=>$cv['name'],
                                'child_data'=>$cc_ary,                                
                            );
							
                            unset($cc_data);
                            unset($cc_ary);
                           
                        }
						else
                        {
                            $child_ary[$cv['id']] = array('key'=>$cv['id'],'val'=>$cv['name']);
                        }
                        
                    }
                }  
                $property_for_search_config[$type_id][$k]['data']=$child_ary;
                
				array_unshift ($property_for_search_config[$type_id][$k]['data'],array('key'=>'','val'=>'全部'));
                unset($parents_id);
                unset($child_data);
                unset($child_ary);
            }
			else
			{
				array_unshift ($property_for_search_config[$type_id][$k]['data'],array('key'=>'','val'=>'全部'));
			}
            
        }
        //线上的才做缓存
		if( ! $is_test )
        {
            POCO::setCache($cache_key, $property_for_search_config[$type_id], array('life_time'=>86400));
        }
		
        return $property_for_search_config[$type_id];       
    }
    

	
}
