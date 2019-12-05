<?php
/*
 * ����POCO������
 */

class pai_relate_poco_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_relate_poco_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_info($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data );
	
	}
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_info($data, $user_id)
	{
		if (empty ( $data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':USER ID����Ϊ��' );
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->update ( $data, $where_str );
	}
	
	/*
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_relate_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	public function get_relate_info($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		return $ret;
	}
	
	/*
	 * ��ȡԼID������POCOID
	 * 
	 * @param int $user_id ԼԼ�û�ID
	 * return int
	 */
	public function get_relate_poco_id($user_id)
	{
        $user_id = (int)$user_id;
		$ret = $this->get_relate_info ( $user_id );
		return (int)$ret ['poco_id'];
	}
	
	/*
	 * ��ȡPOCO ID������ԼID
	 * 
	 * @param int $poco_id POCO ID
	 * return int
	 */
	public function get_relate_yue_id($poco_id)
	{
		$poco_id = ( int ) $poco_id;
		$ret = $this->find ( "poco_id={$poco_id}" );
		return (int)$ret ['user_id'];
	}
	
	/*
	 * ����������POCO�ʺ�
	 * 
	 * @param int $user_id ԼԼ�û�ID
	 * @param string $nickname  POCO�ǳ�
	 * @param string $password  POCO����
	 * return mix
	 */
	public function create_relate_poco_id($user_id)
	{
/*		if (empty ( $nickname ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ǳƲ���Ϊ��' );
		}
		$pwd_len = strlen ( $password );
		if ($pwd_len < 6 || $pwd_len >= 32)
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���������6λ����  32λ����' );
		}*/

	/*	$nickname = "ԼԼ�û�".$user_id;
        $param[] = 'member.register_member';
        $param[] = array($nickname, $password, '', '��',  0, '', null, null,11);
		$poco_id = curl_event_data('event_api_class','get_poco_excute',$param);*/

        $poco_id = $this->get_temp_poco_id();

        $poco_id = (int)$poco_id;
		if ($poco_id)
		{
			$insert_data ['user_id'] = $user_id;
			$insert_data ['poco_id'] = $poco_id;
			$this->add_info ( $insert_data,'IGNORE' );
			return $poco_id;
		} else
		{
			return false;
		}
	}
	
	/*
	 * ��ӹ�����POCO�ʺ�
	 * @param int $user_id ԼԼ�û�ID
	 * @param int $poco_id POCO ID 
	 * return int
	 */
	public function add_relate_poco_id($user_id, $poco_id)
	{
		$user_id = ( int ) $user_id;
		$poco_id = ( int ) $poco_id;
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		if (empty ( $poco_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':POCO ID����Ϊ��' );
		}
		
		$insert_data ['user_id'] = $user_id;
		$insert_data ['poco_id'] = $poco_id;
		$this->add_info ( $insert_data );
		return $poco_id;
	}


    public function get_temp_poco_id()
    {
        POCO_TRAN::begin(0);

        $sql = "SELECT poco_id FROM pai_db.pai_temp_poco_id_tbl WHERE status=0 limit 1 FOR UPDATE;";
        $poco_arr = db_simple_getdata($sql,true);
        $poco_id = (int)$poco_arr['poco_id'];

        $sql = "UPDATE pai_db.pai_temp_poco_id_tbl SET status=1 WHERE poco_id=$poco_id";
        db_simple_getdata($sql,true);

        POCO_TRAN::commmit(0);

        return $poco_id;
    }

}

?>