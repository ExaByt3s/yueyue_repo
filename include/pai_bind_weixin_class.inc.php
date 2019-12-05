<?php
/*
 * ΢�Ű󶨲�����
 */

class pai_bind_weixin_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_bind_weixin_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_bind($insert_data)
	{
		
		if (empty ( $insert_data['user_id'] ) || empty ( $insert_data['open_id'] ))
		{
			trace ( "open_id,user_id����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		$this->insert ( $insert_data);
		
		return $this->get_affected_rows();
	
	}
    
   
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $user_id
	 * @return bool
	 */
	public function update_user($data, $user_id)
	{
		$user_id = (int)$user_id;
		if (empty ( $user_id ) || empty ( $data['open_id'] ))
		{
			trace ( "open_id,user_id����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->update($data, $where_str);
	}
	
	
	public function delete_user($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			trace ( "user_id����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete ( $where_str );
	}
	

	/*
	 * ����YUE ID��ȡ����Ϣ
	 * @param int $user_id
	 * @return array
	 */
	public function get_bind_info_by_user_id($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		return $ret;
	}
	

	/*
	 * ����OPEN ID��ȡ����Ϣ
	 * @param string $open_id
	 * @return array
	 */
	public function get_bind_info_by_open_id($open_id)
	{
		$ret = $this->find ( "open_id='{$open_id}'" );
		return $ret;
	}
	
	
	/*
	 * ����OPEN ID��ȡ����Ϣ
	 * @param int $user_id
	 * @param string $open_id
	 * @return array
	 */
	public function get_bind_info_by_user_id_and_open_id($user_id,$open_id)
	{
		$ret = $this->find ( "user_id={$user_id} and open_id='{$open_id}'" );
		return $ret;
	}
	
	

		
	function upload_icon($user_id,$pic_url)
	{
        $pic_obj = POCO::singleton('pai_pic_class');
        return $pic_obj->upload_user_icon($pic_url,$user_id);
	}
	
	
	

}

?>