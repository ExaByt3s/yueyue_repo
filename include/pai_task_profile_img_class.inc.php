<?php
/*
 * ͼƬ������
 */

class pai_task_profile_img_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_task_db' );
		$this->setTableName ( 'task_profile_img_tbl' );
	}
	
	/*
	 * ���ͼƬ
	 * 
	 * @param int    $profile_id �̼�ID
	 * @param array  $pic_arr ͼƬ����
	 * 
	 * return bool 
	 */
	public function add_pic($profile_id, $pic_arr)
	{
		$profile_id = ( int ) $profile_id;
		
		if (empty ( $profile_id ))
		{
			trace ( "�̼�ID����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		if (empty ( $pic_arr ))
		{
			trace ( "ͼƬ���鲻��Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		//��ԭ�е�ɾ������������µ�ͼƬ
		$this->del_pic ( $profile_id );
		
		foreach ( $pic_arr as $pic )
		{
			if (! empty ( $pic ))
			{
				$pic = str_replace ( array("image16-d","image16-c"), "img16", $pic );
				$insert_data ['profile_id'] = $profile_id;
				$insert_data ['img'] = $pic;
				$insert_data ['add_time'] = time ();
				$this->insert ( $insert_data );
			}
		}
		
		return true;
	}
	

	/*
	 * ��ȡ�̼�ͼƬ
	 * @param int $profile_id
	 * @param string $limit
	 * 
	 * return array
	 */
	public function get_profile_pic($profile_id,$limit='0,20')
	{
		$profile_id = ( int ) $profile_id;
		
		$where_str = "profile_id={$profile_id}";
		$ret = $this->findAll ( $where_str, $limit, 'id desc', '*' );
		return $ret;
	}
	
	/*
	 * ɾ��ͼƬ
	 * 
	 * @param int $profile_id
	 * return bool
	 */
	public function del_pic($profile_id)
	{
		$profile_id = ( int ) $profile_id;
		if (empty ( $profile_id ))
		{
			trace ( "�̼�ID����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
		}
		
		$where_str = "profile_id = {$profile_id}";
		return $this->delete ( $where_str );
	}
	

	

	

	
	

}

?>