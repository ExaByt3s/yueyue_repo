<?php
/**
 * Hai
 * �����ʺŰ󶨲�����
 * 20150113
 *
 */

class pai_bind_account_class extends POCO_TDG
{
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId( 101 );
		$this->setDBName( 'pai_db' );
		$this->setTableName ( 'pai_bind_account_tbl' );
	}
	
	/**
	 * �л���˱�
	 */
	private function set_bind_account_check_tbl()
	{
		$this->setTableName('pai_bind_account_check_tbl');
	}

	/**
	 * �л���ʽ��
	 */
	private function set_bind_account_tbl()
	{
		$this->setTableName('pai_bind_account_tbl');
	}

	/**
     * ����where
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
	 * ִ�а��û�
	 * 
	 * @param array  $check_data
	 * array(
	 *  'user_id' =>'',
	 *	'type' 	  =>'', ��֧��alipay_account[֧�����˺�]
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
			
			trace("�Ƿ����� user_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;	

		}
		if (empty ( $check_data['type'] ))
		{
			trace("�Ƿ����� type ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		if (empty ( $check_data['real_name'] ))
		{
			trace("�Ƿ����� real_name ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}		
		if (empty ( $check_data['third_account'] ))
		{
			trace("�Ƿ����� third_account ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$check_data['add_time'] = time();
		$this->set_bind_account_check_tbl();
		return $this->insert( $check_data ,'REPLACE');
	
	}

	/**
	 * ����
	 *
	 * @param  int|array $id    
	 * @param  int $status ״̬ 0����� 1����� 2��˲�ͨ�� 3���� 4���
	 * @param  int $update_for_status ��Ҫ���µ�״̬�ֶ�ֵ  ��ֹ�����
	 * @param  array $more_info ��ע
	 * @return bool
	 */
	private function update_check_status($id, $status, $update_for_status, $more_info=array())
	{
		if (empty($id)) 
		{
			trace("�Ƿ����� id ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
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
     * ������ֵ��¼  ����ת��֧����ʱ   ��¼���ᣬ������֧����
     *
     * @param array $id 
     * @return 
     */
     public function set_lock_status($id){

        return $this->update_check_status($id,3,0);

     }

    /**
     * ������ֵ��¼
     *
     * @param array $id 
     * @return 
     */
     public function set_unlock_status($id){

       return $this->update_check_status($id,0,3);

     }

	/**
	 * ��˳ɹ�
	 *
	 * @param int $id    
	 * @return bool
	 */
	public function check_apply($id, $remark=''){

		$id = (int)$id;
		if (empty($id))
		{
			trace("�Ƿ����� id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		
		$apply_info	= $this->get_check_info($id);
		if( empty($apply_info) )
		{
			trace("�Ƿ����� id �Ҳ�����������",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		
		$ret = $this->update_check_status($id, 1, 0, array('remark'=>$remark,'check_time'=>time()));
		if (empty($ret))
		{
			trace("ͨ����˳���",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
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
			trace("������ʽ��״̬����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		
		if( $apply_info['type']=='alipay_account' )
		{
			$content 	= "���Ѿ��ɹ���֧������";
			$url 	 	= '';
			send_message_for_10002( $apply_info['user_id'], $content, $url );
		}
		
		return true;
	}

	/**
	 * ȡ�����
	 *
	 * @param int $id    
	 * @return bool
	 */
	public function cancel_apply($id, $remark=''){

		$id = (int)$id;
		if (empty($id)) 
		{
			trace("�Ƿ����� id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		
		$apply_info	= $this->get_check_info($id);
		if( empty($apply_info) ){

			trace("�Ƿ����� id �Ҳ�����������",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;			

		}
		$ret = $this->update_check_status($id, 2, 0, array('remark'=>$remark,'check_time'=>time()));
		if (empty($ret)) 
		{
			trace("ȡ����˳���",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		
		if( $apply_info['type']=='alipay_account' )
		{
			$content 	= "֧������δ�ܳɹ�����������֧������Ϣ�����Ƿ���ȷ����ȷ��֧������������״̬�£��ٴγ��԰󶨡�";
			$url 	 	= '';
			send_message_for_10002( $apply_info['user_id'], $content, $url );
		}
		
		return true;
	}

	/**
	 * ��ȡ����Ϣ 
	 *
	 * @param int $id
	 * @return bool
	 */
	public function get_check_info($id)
	{
		$id = ( int ) $id;
		if (empty($id)) 
		{
			trace("�Ƿ����� id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_bind_account_check_tbl();
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}

	/**
	 * ͨ���û�ID��ȡ����Ϣ 
	 *
	 * @param int $id
	 * @return bool
	 */
	public function get_check_info_by_user_id($user_id,$type)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			trace("�Ƿ����� user_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		if (empty($type)) 
		{
			trace("�Ƿ����� type ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_bind_account_check_tbl();
		$where_str = " user_id = {$user_id} AND type='{$type}' ";
		$info = $this->find ($where_str,' id DESC');
		return $info;
	
	}

	/**
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
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
     * ȡ�����б�
     *
     * @param string $search_arr    ��ѯ�������� array('id'=>'','user_id'=>'','type'=>'','min_add_time'=>'','max_add_time'=>'','status'=>'')
     * @param bool   $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
     * @param string $limit        ��ѯ����
     * @param string $order_by     ��������
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
     * ���û����Ƚ���˱�
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
    	
    	//���д���˼�¼���򷵻�false
    	$check_info = $this->get_check_info_by_user_id($user_id, $type);
    	if( !empty($check_info) && ($check_info['status']==0 || $check_info['status']==1) )
    	{
    		return false;
    	}
    	
    	return $this->add_check($check_data);
    }

    /********************************check�������***********************************************/
 	/**
	 * ���û�
	 * 
	 * @param array  $bind_data
	 * array(
	 *  'user_id' =>'',
	 *  'real_name'=>'',
	 *	'type' 	  =>'', ��֧��alipay_account[֧�����˺�]
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
			trace("�Ƿ����� id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
			return false;
		}
		
		$bind_data['user_id'] = intval($bind_data['user_id']);	
		if (empty ( $bind_data['user_id'] ))
		{
			trace("�Ƿ����� user_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;	
		}
		
		if (empty ( $bind_data['type'] ))
		{
			trace("�Ƿ����� type ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		
		if (empty ( $bind_data['third_account'] ))
		{
			trace("�Ƿ����� third_account ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$bind_data['add_time'] = time();
		$this->set_bind_account_tbl();
		$this->insert ( $bind_data ,'REPLACE');
		return true;
	} 

    /**
	 * ͨ������ɾ���û���
	 * 
	 * @param int 	 $user_id �û�ID
	 * @param string $type    ����
	 * @return bool 
	 */
    private function del_bind_by_rel($user_id,$type)
    {
        $user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			trace("�Ƿ����� user_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		if (empty($type)) 
		{
			trace("�Ƿ����� type ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_bind_account_tbl();
		$where_str = " user_id = {$user_id} AND type='{$type}'";
		return $this->delete($where_str);
        
    }
    
    /**
	 * ͨ��IDɾ���û���
	 * 
	 * @param int    $id
	 * @return bool 
	 */
    private function del_bind($id)
    {
        $id = (int)$id;
		if (empty($id)) 
		{
			trace("�Ƿ����� id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_bind_account_tbl();
		$where_str = " id={$id} ";
		return $this->delete($where_str);
 
    }
    
    /**
     * �����
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
    		trace("������",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
    		return false;
    	}
    	
    	$this->del_bind($id);
    	
    	if( $bind_info['type']=='alipay_account' )
    	{
    		$content 	= "���ѳɹ���󣬽����ҳ������°�����֧�����˺š�";
    		$url 	 	= '';
    		send_message_for_10002( $bind_info['user_id'], $content, $url );
    	}
    	
    	return true;
    }
	
	/**
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
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
     * ȡ�����б�
     *
     * @param string $search_arr    ��ѯ�������� array('id'=>'','user_id'=>'','type'=>'','min_add_time'=>'','max_add_time'=>'')
     * @param bool   $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
     * @param string $limit        ��ѯ����
     * @param string $order_by     ��������
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
	 * ��ȡ����Ϣ 
	 *
	 * @param int $id
	 * @return bool
	 */
	public function get_bind_info($id)
	{
		$id = ( int ) $id;
		if (empty($id)) 
		{
			trace("�Ƿ����� id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_bind_account_tbl();
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}

	/**
	 * ͨ���û�ID��ȡ����Ϣ 
	 *
	 * @param int $id
	 * @return bool
	 */
	public function get_info_by_user_id($user_id,$type)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			trace("�Ƿ����� user_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		if (empty($type)) 
		{
			trace("�Ƿ����� type ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_bind_account_tbl();
		$where_str = " user_id = {$user_id} AND type='{$type}' ";
		$info = $this->find ($where_str);
		return $info;
	
	}

	/**
	 * ��ȡͨ����˵�֧�����˺� 
	 *
	 * @param  int $user_id
	 * @return string
	 */
	public function get_alipay_account_by_user_id($user_id){

		$info = $this->get_info_by_user_id($user_id,'alipay_account');
		return $info['third_account'];
	}

	/**
	 * ��ȡ��״̬ 
	 *
	 * @param  int 	  $user_id �û�ID
	 * @param  string $type    ���� ��֧��alipay_account[֧�����˺�]
	 * @return int    -1 δ�� 0 ����� 1����� 2��˲�ͨ��
	 */
	public function get_bind_status($user_id, $type)
	{
		$ret = array();
		
		$user_id = intval($user_id);
		if (empty($user_id)) 
		{
			trace("�Ƿ����� user_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		
		$type = trim($type);
		if (empty($type)) 
		{
			trace("�Ƿ����� type ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		
		//��ʽ��
		$info = $this->get_info_by_user_id($user_id, $type);
		if( !empty($info) )
		{
			$ret['status'] = 1;
			$ret['third_account'] = $info['third_account'];
			$ret['pic'] = $info['pic'];
			return $ret;
		}
		
		//��˱�
		$check_info = $this->get_check_info_by_user_id($user_id, $type);
		if( empty($check_info) )
		{
			$ret['status'] = -1;
			return $ret;
		}
		
		//״̬ 0����� 1����� 2��˲�ͨ�� 3���� 4���
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