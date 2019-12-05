<?php
/*
 * ͼƬ������
 */

class pai_pic_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_pic_tbl' );
	}
	
	/*
	 * ���ͼƬ
	 * 
	 * @param int    $user_id �û�ID
	 * @param array  $pic_arr ͼƬ����
	 * 
	 * return bool 
	 */
	public function add_pic($user_id, $pic_arr)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $user_id ))
		{
			$result['result'] = -1;
			$result['message'] = "�û�ID����Ϊ��";
			return $result;
		}
		
		if (empty ( $pic_arr ))
		{
			$result['result'] = -1;
			$result['message'] = "ͼƬ���鲻��Ϊ��";
			return $result;
		}
		
		if (count ( $pic_arr ) > 15)
		{
			$result['result'] = -1;
			$result['message'] = "ͼƬ���ܴ���15��";
			return $result;
		}
		
		//��ԭ�е�ɾ������������µ�ͼƬ
		$this->del_pic ( $user_id );
		
		foreach ( $pic_arr as $pic )
		{
			if (! empty ( $pic ))
			{
				$pic = str_replace ( array("image16-d","image16-c"), "img16", $pic );
				$insert_data ['user_id'] = $user_id;
				$insert_data ['img'] = $pic;
				$insert_data ['add_time'] = time ();
				$this->insert ( $insert_data );
			}
		}
		
		$result['result'] = 1;
		$result['message'] = "���ɹ�";
		return $result;
	}
	
	/*
	 * ��ȡͼƬ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_pic_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_pic_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}
	
	/*
	 * ɾ��ͼƬ
	 * 
	 * @param int $user_id
	 * @param string $type
	 */
	public function del_pic($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			$result['result'] = -1;
			$result['message'] = "�û�ID����Ϊ��";
			return $result;
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * ����ͼƬIDɾͼƬ
	 */
	public function del_pic_by_id($pic_id)
	{
		$pic_id = ( int ) $pic_id;
		if(empty($pic_id))
		{
			return false;
		}
		$where_str = "id = {$pic_id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * ��ȡ�û�ͼƬ
	 * 
	 * @param int    $user_id �û�ID
	 * @param string $limit 
	 */
	public function get_user_pic($user_id, $limit = '0,15', $fields = '*')
	{
		$user_id = ( int ) $user_id;
		
		$where_str = "user_id={$user_id}";
		$ret = $this->get_pic_list ( false, $where_str, 'id ASC', $limit, $fields );
		
		return $ret;
	}
	
	/*
	 * ɾ����˲�ͨ����ͼƬ
	 * @param string $url
	 * return int
	 */
	public function del_audit_pic($user_id,$url='')
	{
	
		if(empty($user_id))
		{
			return false;
		}
		
		
		if(empty($url))
		{
			return false;
		}
		
		$user_id = (int)$user_id;
		
		
		$where_str = "user_id={$user_id} and img like '%{$url}%'";
		
		$pic_info = $this->find ( $where_str );
		
		$ret = $this->delete ( $where_str );
		
		
		$this->add_del_pic($user_id,$pic_info['img']);
		
		return $ret;
	}
	
	/*
	 * ����ɾ����ͼƬ
	 */
	public function add_del_pic($user_id,$url='')
	{

		if(empty($user_id))
		{
			return false;
		}
		
		if(empty($url))
		{
			return false;
		}
		
		global $yue_login_id;
		
		$user_id = (int)$user_id;
		
		$insert_data ['user_id'] = $user_id;
		$insert_data ['audit_user_id'] = $yue_login_id;
		$insert_data ['img'] = $url;
		$insert_data ['add_time'] = time ();
		
		$insert_str = db_arr_to_update_str($insert_data);
		$sql = "INSERT INTO pai_db.pai_pic_del_tbl SET ".$insert_str;
		return db_simple_getdata($sql,false,101);
	}


    /*
     * ͼƬ����
     */
    function download_pic($url,$user_id,&$save_dir) {

        $urls = array ($url );

        $user_id = (int)$user_id;

        $save_to = '/disk/data/htdocs232/poco/pai/_temp/';

        $mh = curl_multi_init ();

        foreach ( $urls as $i => $url ) {
            $save_dir = $save_to . date ( "dMYHis" ) . $user_id.rand(100000,999999).".jpg";
            if (! is_file ( $save_dir )) {
                $conn [$i] = curl_init ( $url );

                $fp [$i] = fopen ( $save_dir, "wb" );

                curl_setopt ( $conn [$i], CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-CN; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1" );
                //curl_setopt ( $conn [$i], CURLOPT_REFERER, "http://$host" );

                curl_setopt ( $conn [$i], CURLOPT_FILE, $fp [$i] );
                curl_setopt ( $conn [$i], CURLOPT_HEADER, 0 );
                curl_setopt ( $conn [$i], CURLOPT_CONNECTTIMEOUT, 600 );
                curl_setopt ( $conn [$i], CURLOPT_FOLLOWLOCATION, 1 );
                //curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 0);
                //curl_setopt($conn[$i], CURLOPT_VERBOSE, 0);

                curl_multi_add_handle ( $mh, $conn [$i] );
            }
        }

        do {
            $n = curl_multi_exec ( $mh, $active );
        } while ( $active );

        foreach ( $urls as $i => $url ) {
            curl_multi_remove_handle ( $mh, $conn [$i] );
            curl_close ( $conn [$i] );
            fclose ( $fp [$i] );
        }

        curl_multi_close ( $mh );

    }


    /*
     * �ϴ���ƷͼƬ
     * @param string $url ͼƬ����
     * @param int $user_id �û�ID
     * @return json
     */
    function upload_works_pic($url,$user_id)
    {
        $user_id = (int)$user_id;
        if(!$user_id)
        {
            return false;
        }

        $this->download_pic($url,$user_id,$save_dir);

        $upload_url  = 'http://sendmedia-w.yueus.com:8079/upload.cgi';//target url

        $fields['opus'] = '@'.$save_dir;
        $fields['poco_id'] = $user_id;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $upload_url );
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $ret = curl_exec( $ch );

        if ($error = curl_error($ch) ) {
            die($error);
        }
        curl_close($ch);

        //ɾ����ʱ�ļ�
        unlink($save_dir);

        return $ret;
    }


    /*
     * �ϴ�������ͷ��
     * @param string $url ͼƬ����
     * @param int $user_id �û�ID
     * @return json
     */
    function upload_user_icon($url,$user_id)
    {
        $user_id = (int)$user_id;
        if(!$user_id)
        {
            return false;
        }

        $this->download_pic($url,$user_id,$save_dir);

        $upload_url  = 'http://sendmedia-w.yueus.com:8078/icon.cgi';//target url

        $fields['opus'] = '@'.$save_dir;
        $fields['poco_id'] = $user_id;
        $fields['hash'] =  md5($user_id."YUE_PAI_POCO!@#456");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $upload_url );
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $ret = curl_exec( $ch );

        if ($error = curl_error($ch) ) {
            die($error);
        }
        curl_close($ch);

        //ɾ����ʱ�ļ�
        unlink($save_dir);

        return $ret;
    }

}

?>