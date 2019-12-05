<?php
/**
 * Hai
 * 提现帐号绑定操作类
 * 20150113
 *
 */

class pai_bind_account_class extends POCO_TDG
{
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId( 101 );
		$this->setDBName( 'pai_db' );
		$this->setTableName ( 'pai_bind_account_tbl' );
	}
	
	/**
	 * 切换审核表
	 */
	private function set_bind_account_check_tbl()
	{
		$this->setTableName('pai_bind_account_check_tbl');
	}

	/**
	 * 切换正式表
	 */
	private function set_bind_account_tbl()
	{
		$this->setTableName('pai_bind_account_tbl');
	}

	/**
     * 构建where
     *
     * @param array $search_arr
     * @return string
    */
    private function create_where( $search_arr ){

		$where_str = '1';
		if( !empty($search_arr['id']) ){

            if( !is_array($search_arr['id']) )
              	$search_arr['id'] = array($search_arr['id']);
            $id_gather  = implode(',',$search_arr['id']); 
            $where_str .= " AND id IN( $id_gather )";
        
        }
		if( !empty($search_arr['user_id']) ){

            if( !is_array($search_arr['user_id']) )
              	$search_arr['user_id'] = array($search_arr['user_id']);
            $user_id_gather  = implode(',',$search_arr['user_id']); 
            $where_str 		.= " AND user_id IN( $user_id_gather )";
        
        }        
    	if( isset($search_arr['type'])&&$search_arr['type']!=='' ){

            $where_str .= " AND type='{$search_arr['type']}'";
        
        }
        if( !empty($search_arr['real_name']) )
        {
        	$where_str .= " AND real_name='".mysql_escape_string($search_arr['real_name'])."'";
        }
        if( !empty($search_arr['third_account']) )
        {
        	$where_str .= " AND third_account='".mysql_escape_string($search_arr['third_account'])."'";
        }
        if( !empty($search_arr['min_add_time']) ){

             $where_str .= " AND add_time>={$search_arr['min_add_time']}";

        }
        if( !empty($search_arr['max_add_time']) ){

             $search_arr['max_add_time'] += 86400;
             $where_str                  .= " AND add_time<{$search_arr['max_add_time']}";
        }
     	if( isset($search_arr['status'])&&$search_arr['status']!=='' ){

            $where_str .= " AND status={$search_arr['status']}";
        
        }             
        return $where_str;

    }

	/**
	 * 执行绑定用户
	 * 
	 * @param array  $check_data
	 * array(
	 *  'user_id' =>'',
	 *	'type' 	  =>'', 暂支持alipay_account[支付宝账号]
	 *  'real_name'=>'',
	 *	'third_account' =>''
	 * ) 
	 * 
	 * @return bool 
	 */
	private function add_check($check_data)
	{
		
		$check_data['user_id'] = intval($check_data['user_id']);	
		if (empty ( $check_data['user_id'] ))
		{
			
			trace("非法参数 user_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;	

		}
		if (empty ( $check_data['type'] ))
		{
			trace("非法参数 type 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		if (empty ( $check_data['real_name'] ))
		{
			trace("非法参数 real_name 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}		
		if (empty ( $check_data['third_account'] ))
		{
			trace("非法参数 third_account 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$check_data['add_time'] = time();
		$this->set_bind_account_check_tbl();
		return $this->insert( $check_data ,'REPLACE');
	
	}

	/**
	 * 更新
	 *
	 * @param  int|array $id    
	 * @param  int $status 状态 0待审核 1已审核 2审核不通过 3冻结 4解绑
	 * @param  int $update_for_status 需要更新的状态字段值  防止误更新
	 * @param  array $more_info 备注
	 * @return bool
	 */
	private function update_check_status($id, $status, $update_for_status, $more_info=array())
	{
		if (empty($id)) 
		{
			trace("非法参数 id 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		if( !is_array($id) )
		{
			$id = array($id);
		}
		if( !is_array($more_info) ) $more_info = array();
		
        $id_gather = implode(',',$id);
  		$update_for_status = intval($update_for_status);        
        $where_str = " id IN({$id_gather}) AND status={$update_for_status}";
        
        $more_info['status'] = $status;
		$this->set_bind_account_check_tbl();
		$ret = $this->update($more_info, $where_str);
		return $ret;
	}

	/**
     * 锁定充值记录  当跳转到支付宝时   记录冻结，避免多次支付。
     *
     * @param array $id 
     * @return 
     */
     public function set_lock_status($id){

        return $this->update_check_status($id,3,0);

     }

    /**
     * 解锁充值记录
     *
     * @param array $id 
     * @return 
     */
     public function set_unlock_status($id){

       return $this->update_check_status($id,0,3);

     }

	/**
	 * 审核成功
	 *
	 * @param int $id    
	 * @return bool
	 */
	public function check_apply($id, $remark=''){

		$id = (int)$id;
		if (empty($id))
		{
			trace("非法参数 id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		
		$apply_info	= $this->get_check_info($id);
		if( empty($apply_info) )
		{
			trace("非法参数 id 找不到关联数据",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		
		$ret = $this->update_check_status($id, 1, 0, array('remark'=>$remark,'check_time'=>time()));
		if (empty($ret))
		{
			trace("通过审核出错",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
			return false;
		}
		
		$bind_data = array(
			'id' => $apply_info['id'],
			'user_id' => $apply_info['user_id'],
			'type' => $apply_info['type'],
			'real_name' => $apply_info['real_name'],
			'third_account' => $apply_info['third_account'],
			'third_name' => $apply_info['third_name'],
			'pic' => $apply_info['pic'],
			'add_time' => time(),
		);
		$ret = $this->_add_bind($bind_data);
		if (empty($ret))
		{
			trace("插入正式表状态出错",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		
		if( $apply_info['type']=='alipay_account' )
		{
			$content 	= "你已经成功绑定支付宝。";
			$url 	 	= '';
			send_message_for_10002( $apply_info['user_id'], $content, $url );
		}
		
		return true;
	}

	/**
	 * 取消审核
	 *
	 * @param int $id    
	 * @return bool
	 */
	public function cancel_apply($id, $remark=''){

		$id = (int)$id;
		if (empty($id)) 
		{
			trace("非法参数 id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		
		$apply_info	= $this->get_check_info($id);
		if( empty($apply_info) ){

			trace("非法参数 id 找不到关联数据",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;			

		}
		$ret = $this->update_check_status($id, 2, 0, array('remark'=>$remark,'check_time'=>time()));
		if (empty($ret)) 
		{
			trace("取消审核出错",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		
		if( $apply_info['type']=='alipay_account' )
		{
			$content 	= "支付宝绑定未能成功。建议你检查支付宝信息输入是否正确，并确保支付宝处于正常状态下，再次尝试绑定。";
			$url 	 	= '';
			send_message_for_10002( $apply_info['user_id'], $content, $url );
		}
		
		return true;
	}

	/**
	 * 获取绑定信息 
	 *
	 * @param int $id
	 * @return bool
	 */
	public function get_check_info($id)
	{
		$id = ( int ) $id;
		if (empty($id)) 
		{
			trace("非法参数 id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_bind_account_check_tbl();
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}

	/**
	 * 通过用户ID获取绑定信息 
	 *
	 * @param int $id
	 * @return bool
	 */
	public function get_check_info_by_user_id($user_id,$type)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			trace("非法参数 user_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		if (empty($type)) 
		{
			trace("非法参数 type 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_bind_account_check_tbl();
		$where_str = " user_id = {$user_id} AND type='{$type}' ";
		$info = $this->find ($where_str,' id DESC');
		return $info;
	
	}

	/**
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * @return array
	 */
	public function get_check_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_bind_account_check_tbl();
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
     * 取数据列表
     *
     * @param string $search_arr    查询条件数组 array('id'=>'','user_id'=>'','type'=>'','min_add_time'=>'','max_add_time'=>'','status'=>'')
     * @param bool   $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit        查询条数
     * @param string $order_by     排序条件
     * @return array|int
     */
    public function get_check_list_by_search($search_arr, $b_select_count = false, $limit = '0,10', $order_by = 'id DESC', $fields = '*')
    {
        $this->set_bind_account_check_tbl();
        $where_str = $this->create_where( $search_arr );
		$list 	   = $this->get_check_list($b_select_count,$where_str,$order_by,$limit );
		return $list;

    }
    
    /**
     * 绑定用户，先进审核表
     * @param array $bind_data
     * @return bool
     */
    public function add_bind($check_data)
    {
    	$user_id = intval($check_data['user_id']);
    	$type = trim($check_data['type']);
    	if( $user_id<1 || strlen($type)<1 )
    	{
    		return false;
    	}
    	
    	//若有待审核记录，则返回false
    	$check_info = $this->get_check_info_by_user_id($user_id, $type);
    	if( !empty($check_info) && ($check_info['status']==0 || $check_info['status']==1) )
    	{
    		return false;
    	}
    	
    	return $this->add_check($check_data);
    }

    /********************************check表类结束***********************************************/
 	/**
	 * 绑定用户
	 * 
	 * @param array  $bind_data
	 * array(
	 *  'user_id' =>'',
	 *  'real_name'=>'',
	 *	'type' 	  =>'', 暂支持alipay_account[支付宝账号]
	 *	'third_account' =>''
	 * ) 
	 * 
	 * @return bool 
	 */
	private function _add_bind($bind_data)
	{
		$bind_data['id'] = intval($bind_data['id']);
		if (empty ( $bind_data['id'] ))
		{
			trace("非法参数 id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
			return false;
		}
		
		$bind_data['user_id'] = intval($bind_data['user_id']);	
		if (empty ( $bind_data['user_id'] ))
		{
			trace("非法参数 user_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;	
		}
		
		if (empty ( $bind_data['type'] ))
		{
			trace("非法参数 type 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		
		if (empty ( $bind_data['third_account'] ))
		{
			trace("非法参数 third_account 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$bind_data['add_time'] = time();
		$this->set_bind_account_tbl();
		$this->insert ( $bind_data ,'REPLACE');
		return true;
	} 

    /**
	 * 通过关联删除用户绑定
	 * 
	 * @param int 	 $user_id 用户ID
	 * @param string $type    类型
	 * @return bool 
	 */
    private function del_bind_by_rel($user_id,$type)
    {
        $user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			trace("非法参数 user_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		if (empty($type)) 
		{
			trace("非法参数 type 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_bind_account_tbl();
		$where_str = " user_id = {$user_id} AND type='{$type}'";
		return $this->delete($where_str);
        
    }
    
    /**
	 * 通过ID删除用户绑定
	 * 
	 * @param int    $id
	 * @return bool 
	 */
    private function del_bind($id)
    {
        $id = (int)$id;
		if (empty($id)) 
		{
			trace("非法参数 id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_bind_account_tbl();
		$where_str = " id={$id} ";
		return $this->delete($where_str);
 
    }
    
    /**
     * 解除绑定
     * @param int $id
     * @param string $remark
     * @return boolean
     */
    public function del_bind_by_id($id, $remark='')
    {
    	$id = intval($id);
    	if( $id<1 )
    	{
    		return false;
    	}
    	
    	$bind_info = $this->get_bind_info($id);
    	if( empty($bind_info) )
    	{
    		return false;
    	}
    	
    	$ret = $this->update_check_status($id, 4, 1, array('remark'=>$remark,'unbind_time'=>time()));
    	if (empty($ret))
    	{
    		trace("解绑出错",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
    		return false;
    	}
    	
    	$this->del_bind($id);
    	
    	if( $bind_info['type']=='alipay_account' )
    	{
    		$content 	= "你已成功解绑，进入绑定页面可重新绑定其他支付宝账号。";
    		$url 	 	= '';
    		send_message_for_10002( $bind_info['user_id'], $content, $url );
    	}
    	
    	return true;
    }
	
	/**
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * @return array
	 */
	public function get_bind_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		$this->set_bind_account_tbl();
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
     * 取数据列表
     *
     * @param string $search_arr    查询条件数组 array('id'=>'','user_id'=>'','type'=>'','min_add_time'=>'','max_add_time'=>'')
     * @param bool   $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit        查询条数
     * @param string $order_by     排序条件
     * @return array|int
     */
    public function get_bind_list_by_search($search_arr, $b_select_count = false, $limit = '0,10', $order_by = 'id DESC', $fields = '*')
    {
        $this->set_bind_account_tbl();
        $where_str = $this->create_where( $search_arr );
		$list 	   = $this->get_bind_list($b_select_count,$where_str,$order_by,$limit );
		return $list;

    }

	/**
	 * 获取绑定信息 
	 *
	 * @param int $id
	 * @return bool
	 */
	public function get_bind_info($id)
	{
		$id = ( int ) $id;
		if (empty($id)) 
		{
			trace("非法参数 id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_bind_account_tbl();
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}

	/**
	 * 通过用户ID获取绑定信息 
	 *
	 * @param int $id
	 * @return bool
	 */
	public function get_info_by_user_id($user_id,$type)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			trace("非法参数 user_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		if (empty($type)) 
		{
			trace("非法参数 type 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_bind_account_tbl();
		$where_str = " user_id = {$user_id} AND type='{$type}' ";
		$info = $this->find ($where_str);
		return $info;
	
	}

	/**
	 * 获取通过审核的支付宝账号 
	 *
	 * @param  int $user_id
	 * @return string
	 */
	public function get_alipay_account_by_user_id($user_id){

		$info = $this->get_info_by_user_id($user_id,'alipay_account');
		return $info['third_account'];
	}

	/**
	 * 获取绑定状态 
	 *
	 * @param  int 	  $user_id 用户ID
	 * @param  string $type    类型 暂支持alipay_account[支付宝账号]
	 * @return int    -1 未绑定 0 待审核 1已审核 2审核不通过
	 */
	public function get_bind_status($user_id, $type)
	{
		$ret = array();
		
		$user_id = intval($user_id);
		if (empty($user_id)) 
		{
			trace("非法参数 user_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		
		$type = trim($type);
		if (empty($type)) 
		{
			trace("非法参数 type 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		
		//正式表
		$info = $this->get_info_by_user_id($user_id, $type);
		if( !empty($info) )
		{
			$ret['status'] = 1;
			$ret['third_account'] = $info['third_account'];
			$ret['pic'] = $info['pic'];
			return $ret;
		}
		
		//审核表
		$check_info = $this->get_check_info_by_user_id($user_id, $type);
		if( empty($check_info) )
		{
			$ret['status'] = -1;
			return $ret;
		}
		
		//状态 0待审核 1已审核 2审核不通过 3冻结 4解绑
		$status = intval($check_info['status']);
		if( in_array($status, array(0,3)) )
		{
			$ret['status'] 		  = 0;
			$ret['third_account'] = $check_info['third_account'];
			$ret['pic'] = $info['pic'];
			return $ret;
		}
		elseif( in_array($status, array(2)) )
		{
			$ret['status'] 		  = 2;
			$ret['third_account'] = $check_info['third_account'];
			$ret['pic'] = $info['pic'];
			return $ret;
		}
		
		$ret['status'] = -1;
		return $ret;
	}

}

?>