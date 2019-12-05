<?php
/**
 * ΢�Ź���ƽ̨�����߽ӿ�
 * 
 * @author Henry
 * @copyright 2015-01-05
 */

class pai_weixin_helper_class extends POCO_TDG
{
	/**
	 * ��Ϣ���ձ���ֶ�
	 * @var array
	 */
	private $receive_tbl_fields = array(
		'receive_id','bind_id','ToUserName','FromUserName','CreateTime',
		'MsgType','Event','EventKey','MsgId','Content',
		'PicUrl','MediaId','ThumbMediaId','Format','Recognition',
		'Location_X','Location_Y','Scale','Label','Title',
		'Description','Url','Ticket','Latitude','Longitude',
		'Precision_Str','cmd_id','add_time',
	);
	
	/**
	 * ��Ϣ�ظ�����ֶ�
	 * @var array
	 */
	private $reply_tbl_fields = array(
		'reply_id','bind_id','receive_id','ToUserName','FromUserName',
		'CreateTime','MsgType','Content','MediaId','Title',
		'Description','MusicUrl','HQMusicUrl','ThumbMediaId','ArticleCount',
		'FuncFlag','Articles','add_time',
	);
	
	/**
	 * �ͷ���Ϣ����ֶ�
	 * @var array
	 */
	private $message_custom_tbl_fields = array(
		'id', 'bind_id', 'touser', 'msgtype', 'content',
		'errcode', 'errmsg', 'add_time',
	);
	
	/**
	 * ģ����Ϣ����ֶ�
	 * @var array
	 */
	private $message_template_tbl_fields = array(
		'id', 'bind_id', 'touser', 'template_id','url',
		'topcolor', 'data', 'errcode', 'errmsg', 'msgid', 'status', 'add_time',
	);
	
	/**
	 * ΢�Žӿڵ�ַ
	 * @var string
	 */
	private $wx_api_url = 'https://101.226.90.58/';
	//private $wx_api_url = 'https://api.weixin.qq.com/';
	
	/*
	*������ά������
	��QR_SCENE Ϊ��ʱ,
	*QR_LIMIT_SCENE Ϊ����,
	*QR_LIMIT_STR_SCENE Ϊ�����ַ���
	*@var array
	*/
	private	$qr_type = array(
		'QR_SCENE',
		'QR_LIMIT_SCENE',
		'QR_LIMIT_STR_SCENE',
	);
	
	/**
	 * Ⱥ����С�û���
	 * @var int
	 */
	private $message_mass_send_min = 2;
	
	/**
	 * Ⱥ������û���
	 * @var int
	 */
	//private $message_mass_send_max = 10000;
	private $message_mass_send_max = 3;
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_weixin_db');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_bind_tbl()
	{
		$this->setTableName('weixin_bind_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_receive_tbl()
	{
		$this->setTableName('weixin_receive_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_reply_tbl()
	{
		$this->setTableName('weixin_reply_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_message_custom_tbl()
	{
		$this->setTableName('weixin_message_custom_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_message_template_tbl()
	{
		$this->setTableName('weixin_message_template_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_cmd_tbl()
	{
		$this->setTableName('weixin_cmd_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_user_tbl()
	{
		$this->setTableName('weixin_user_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_material_tbl()
	{
		$this->setTableName('weixin_material_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_weixin_mass_send_mission_tbl()
	{
		$this->setTableName('weixin_mass_send_mission_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_weixin_mass_send_msg_tbl()
	{
		$this->setTableName('weixin_mass_send_msg_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_weixin_mass_send_user_log_tbl()
	{
		$this->setTableName('weixin_mass_send_user_log_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_weixin_user_mode_tbl()
	{
		$this->setTableName('weixin_user_mode_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_menu_tbl()
	{
		$this->setTableName('weixin_menu_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_news_tbl()
	{
		$this->setTableName('weixin_news_tbl');
	}
	
	/**
	 * ָ����
	 */
	private function set_weixin_cache_tbl()
	{
		$this->setTableName('weixin_cache_tbl');
	}
	
	/**
	 * ����HTTP����
	 * @param string $url
	 * @param string $method GET|POST
	 * @param string|array $postfields �����������ͨ��urlencoded����ַ�������'para1=val1&para2=val2&...'��ʹ��һ�����ֶ���Ϊ��ֵ���ֶ�����Ϊֵ�����顣���value��һ�����飬Content-Typeͷ���ᱻ���ó�multipart/form-data��
	 * @param array $headers ���� array('Content-type: text/plain', 'Content-length: 100')
	 * @return string
	 */
	private function http($url, $method, $postfields=null, $headers=null)
	{
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	
		if( is_array($headers) )
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
	
		if($method == 'POST')
		{
			curl_setopt($ch, CURLOPT_POST, true);
			if( !empty($postfields) )
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			}
		}
		
		$response = curl_exec($ch);
		curl_close($ch);
	
		return $response;
	}

	/**
	 * GBKת��UTF-8
	 * @param $str
	 * @return array|string
	 */
	private function gbk_to_utf8($str)
	{
		if( is_string($str) )
		{
			$str = iconv('gbk', 'utf-8', $str);
		}
		elseif( is_array($str) )
		{
			foreach ($str as $key=>$val)
			{
				$str[$key] = $this->gbk_to_utf8($val);
			}
		}
		return $str;
	}

	/**
	 * UTF-8ת��GBK
	 * @param $str
	 * @return array|string
	 */
	private function utf8_to_gbk($str)
	{
		if( is_string($str) )
		{
			$str = iconv('utf-8', 'gbk//IGNORE', $str);
		}
		elseif( is_array($str) )
		{
			foreach ($str as $key=>$val)
			{
				$str[$key] = $this->utf8_to_gbk($val);
			}
		}
		return $str;
	}
	
	/**
	 * ֻ����ָ�����ֶ�
	 * @param array $data
	 * @param array $fields
	 * @return array
	 */
	private function filter($data, $fields)
	{
		$array = array();
		if( !is_array($data) || !is_array($fields) ) return $array;
		foreach ($data as $key=>$val)
		{
			if( in_array($key, $fields, true) )
			{
				$array[$key] = $val;
			}
		}
		return $array;
	}
	
	/**
	 * ��ȡ���ںŰ���Ϣ
	 * @param int $bind_id
	 * @return array
	 */
	public function get_bind_info($bind_id)
	{
		$bind_id = intval($bind_id);
		if( $bind_id<1 )
		{
			return array();
		}
		$this->set_weixin_bind_tbl();
		$info = $this->find("bind_id='{$bind_id}'");
		return $info;
	}
	
	/**
	 * ��ȡ���ںŰ���Ϣ������app_id
	 * @param string $app_id
	 * @return array
	 */
	public function get_bind_info_by_app_id($app_id)
	{
		$app_id = trim($app_id);
		if( strlen($app_id)<1 )
		{
			return array();
		}
		$where_str = "app_id=:x_app_id";
		sqlSetParam($where_str, 'x_app_id', $app_id);
		$this->set_weixin_bind_tbl();
		$info = $this->find($where_str);
		return $info;
	}
	
	 /**
     * ȡ�����б�
     *
     * @param string $search_arr    ��ѯ�������� array('app_id'=>'')
     * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
     * @param string $limit        ��ѯ����
     * @param string $order_by     ��������
     * @return array|int
     */
    public function get_bind_list_by_search($search_arr, $b_select_count = false, $limit = '0,10', $order_by = 'bind_id DESC', $fields = '*')
    {
        $where_str = " 1";
        if( !empty($search_arr['app_id']) ){

        	$where_str .= " AND app_id='{$search_arr['app_id']}'"; 

        }
        $this->set_weixin_bind_tbl();
		if( $b_select_count )
		{
			return $this->findCount($where_str);
		}
		$list = $this->findAll($where_str, $limit, $order_by, $fields);
		return $list;

    }

	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_receive($data)
	{
		$data = $this->filter($data, $this->receive_tbl_fields);
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_weixin_receive_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $receive_id
	 * @return boolean
	 */
	public function update_receive($data, $receive_id)
	{
		$receive_id = intval($receive_id);
		if( empty($data) || $receive_id<1 )
		{
			return false;
		}
		$this->set_weixin_receive_tbl();
		$this->update($data, "receive_id='{$receive_id}'");
		return true;
	}

	/**
	 * ��ȡ���һ�ι�ע
	 * @param $bind_id
	 * @param $open_id
	 * @return array
	 */
	public function get_receive_info_by_subscribe($bind_id, $open_id)
	{
		$bind_id = intval($bind_id);
		$open_id = trim($open_id);
		if( $bind_id<1 || strlen($open_id)<1 )
		{
			return array();
		}
		$this->set_weixin_receive_tbl();
		return $this->find("bind_id={$bind_id} AND FromUserName='{$open_id}' AND MsgType='event' AND Event='subscribe'", "receive_id DESC");
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_reply($data)
	{
		$data = $this->filter($data, $this->reply_tbl_fields);
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_weixin_reply_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_message_custom($data)
	{
		$data = $this->filter($data, $this->message_custom_tbl_fields);
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_weixin_message_custom_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public function update_message_custom($data, $id)
	{
		$id = intval($id);
		if( empty($data) || $id<1 )
		{
			return false;
		}
		$this->set_weixin_message_custom_tbl();
		$this->update($data, "id='{$id}'");
		return true;
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_message_template($data)
	{
		$data = $this->filter($data, $this->message_template_tbl_fields);
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_weixin_message_template_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public function update_message_template($data, $id)
	{
		$id = intval($id);
		if( empty($data) || $id<1 )
		{
			return false;
		}
		$this->set_weixin_message_template_tbl();
		$this->update($data, "id='{$id}'");
		return true;
	}
	
	/**
	 * �޸�
	 * @param array $data
	 * @param int $bind_id
	 * @param string $msgid
	 * @return boolean
	 */
	public function update_message_template_by_msgid($data, $bind_id, $msgid)
	{
		$bind_id = intval($bind_id);
		$msgid = trim($msgid);
		if( empty($data) || $bind_id<1 || strlen($msgid)<1 )
		{
			return false;
		}
		
		$where_str = "bind_id=:x_bind_id AND msgid=:x_msgid";
		sqlSetParam($where_str, 'x_bind_id', $bind_id);
		sqlSetParam($where_str, 'x_msgid', $msgid);
		
		$this->set_weixin_message_template_tbl();
		$this->update($data, $where_str);
		return true;
	}

	/**
	 * ���cmd
	 * 
	 * @param array  $insert_data
	 * array(
	 *
	 *  	bind_id  =>'',
	 * 	    cmd_type =>'',
	 * 		cmd_val  =>'',
	 * 		cmd_rid  =>'',
	 * 		exec_type=>'',
	 * 		exec_val =>'',
	 * 		remark   =>'',
 	 * 
	 * ) 
	 * 
	 * @return bool 
	 */
	public function add_cmd($insert_data,$insert_type='IGNORE')
	{
		if( !is_array($insert_data) || empty($insert_data) )
		{
			return false;
		}
		$this->set_weixin_cmd_tbl();
		return $this->insert($insert_data ,$insert_type);
	
	}

    /**
	 * ͨ��IDɾ��
	 * 
	 * @param int    $cmd_id
	 * @return bool 
	 */
    public function del_cmd($cmd_id)
    {
        $cmd_id = (int)$cmd_id;
		if (empty($cmd_id)) 
		{
			trace("�Ƿ����� cmd_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_weixin_cmd_tbl();
		$where_str = " cmd_id={$cmd_id} ";
		return $this->delete($where_str);
 
    }

	/**
	 * ����
	 * @param array $data
	 * @param int   $cmd_id
	 * @return int
	 */
	public function modify_cmd($data,$cmd_id)
	{
		if( !is_array($data) || empty($data) || empty($cmd_id) )
		{
			return false;
		}
		$where_str = " cmd_id = {$cmd_id} ";
		$this->set_weixin_cmd_tbl();
		$ret = $this->update($data,$where_str);
		return $ret; 

	}

	/**
	 * ��ȡ��Ϣ 
	 *
	 * @param int $cmd_id
	 * @return bool
	 */
	public function get_cmd_info($cmd_id)
	{
		$cmd_id = ( int ) $cmd_id;
		if (empty($cmd_id)) 
		{
			trace("�Ƿ����� cmd_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_weixin_cmd_tbl();
		$ret = $this->find ( "cmd_id={$cmd_id}" );
		return $ret;
	}

	/**
	 * ��ȡ��Ϣ 
	 *
	 * @param int $cmd_type
	 * @return bool
	 */
	public function get_cmd_info_by_type($cmd_type,$bind_id)
	{
		if (empty($cmd_type)) 
		{
			trace("�Ƿ����� cmd_type ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_weixin_cmd_tbl();
		$ret = $this->find ( "cmd_type='{$cmd_type}' AND bind_id={$bind_id}" );
		return $ret;
	}

	/**
	 * ��ȡ�б�
	 * @param int $bind_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_cmd_list($bind_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$bind_id = intval($bind_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $bind_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "bind_id={$bind_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_weixin_cmd_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		$list = $this->findAll($sql_where, $limit, $order_by, $fields);
		return $list;
	}

		/**
	 * ��ȡ�û���Ϣ
	 * @param int $bind_id
	 * @param string $open_id
	 * @return array
	 */
	public function get_user_info($bind_id, $open_id)
	{
		$bind_id = intval($bind_id);
		$open_id = trim($open_id);
		if( $bind_id<1 || strlen($open_id)<1 )
		{
			return array();
		}
		
		$where_str = "bind_id=:x_bind_id AND open_id=:x_open_id";
		sqlSetParam($where_str, 'x_bind_id', $bind_id);
		sqlSetParam($where_str, 'x_open_id', $open_id);
		
		//��ȡ
		$this->set_weixin_user_tbl();
		$info = $this->find($where_str);
		
		return $info;
	}
	
	/**
	 * �����û���Ϣ
	 * @param array $data
	 * @param int $bind_id
	 * @param string $open_id
	 * @return boolean
	 */
	public function save_user($data, $bind_id, $open_id)
	{
		$bind_id = intval($bind_id);
		$open_id = trim($open_id);
		if( !is_array($data) || $bind_id<1 || strlen($open_id)<1 )
		{
			return false;
		}
		
		$tmp = $this->get_user_info($bind_id, $open_id);
		if( empty($tmp) )
		{
			$data['bind_id'] = $bind_id;
			$data['open_id'] = $open_id;
			if( !isset($data['add_time']) ) $data['add_time'] = time();
			
			$this->set_weixin_user_tbl();
			$this->insert($data, 'IGNORE');
			
			return true;
		}
		else
		{
			$where_str = "bind_id=:x_bind_id AND open_id=:x_open_id";
			sqlSetParam($where_str, 'x_bind_id', $bind_id);
			sqlSetParam($where_str, 'x_open_id', $open_id);
			
			$this->set_weixin_user_tbl();
			$this->update($data, $where_str);
			
			return true;
		}
	}
	
	/**
	 * ��ȡ�û�ģʽ
	 * @param int $bind_id
	 * @param string $open_id
	 * @return string
	 */
	public function get_user_mode_code($bind_id, $open_id)
	{
		$bind_id = intval($bind_id);
		$open_id = trim($open_id);
		if( $bind_id<1 || strlen($open_id)<1 )
		{
			return '';
		}
		
		//��ȡ
		$where_str = "bind_id=:x_bind_id AND open_id=:x_open_id";
		sqlSetParam($where_str, 'x_bind_id', $bind_id);
		sqlSetParam($where_str, 'x_open_id', $open_id);
		$this->set_weixin_user_mode_tbl();
		$info = $this->find($where_str);
		if( empty($info) )
		{
			return '';
		}
		
		$cur_time = time();
		$begin_time = intval($info['begin_time']);
		$end_time = intval($info['end_time']);
		if( $cur_time<$begin_time )
		{
			//δ��ʼ
			return '';
		}
		if( $end_time>0 && $end_time<$cur_time )
		{
			//�ѽ���
			return '';
		}
		return trim($info['mode_code']);
	}
	
	/**
	 * �����û�ģʽ
	 * @param int $bind_id
	 * @param string $open_id
	 * @param string $mode_code �ձ�ʾ���ģʽ
	 * @param int $seconds 0�����ù���
	 * @return boolean
	 */
	public function save_user_mode($bind_id, $open_id, $mode_code, $seconds=0)
	{
		$bind_id = intval($bind_id);
		$open_id = trim($open_id);
		$mode_code = trim($mode_code);
		$seconds = intval($seconds);
		if( $bind_id<1 || strlen($open_id)<1 || $seconds<0 )
		{
			return false;
		}
		$begin_time = time();
		$end_time = 0;
		if( $seconds>0 ) $end_time = $begin_time + $seconds;
		$data = array(
			'bind_id' => $bind_id,
			'open_id' => $open_id,
			'mode_code' => $mode_code,
			'seconds' => $seconds,
			'begin_time' => $begin_time,
			'end_time' => $end_time,
		);
		$this->set_weixin_user_mode_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}

	/**
	 * д������(һά������ʽ)
	 *
	 * @param array $data
	 * @return bool
	 */
	public function add_menu($data,$insert_type='IGNORE')
	{

		if ( empty( $data ) ) 
		{
			trace("�Ƿ����� data ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
			return false;
		}
		$this->set_weixin_menu_tbl();
		$ret = $this->insert($data, $insert_type);
		return $ret;

	}

    /**
	 * ͨ��IDɾ��
	 * 
	 * @param int    $menu_id
	 * @return bool 
	 */
    public function del_menu($menu_id)
    {
        $menu_id = (int)$menu_id;
		if (empty($menu_id)) 
		{
			trace("�Ƿ����� menu_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_weixin_menu_tbl();
		$where_str = " menu_id={$menu_id} ";
		$ret = $this->delete($where_str);
		if($ret){
			//ɾ������
			 $where_str = " parent_id={$menu_id} ";
			 $ret1 		= $this->delete($where_str);

		}
		return $ret; 
 
    }

	 /**
	 * ���²˵�
	 *
	 * @param array  $data    ���µ�����
	 * @param int 	 $menu_id
	 * @return bool
	 */
	public function update_menu($data,$menu_id)
	{
		if (empty($data)) 
		{
			trace("�Ƿ����� data ����Ϊ��",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}

        $menu_id = (int)$menu_id;
		if (empty($menu_id)) 
		{
			trace("�Ƿ����� menu_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_weixin_menu_tbl();
		$where_str = " menu_id = {$menu_id}";
		return $this->update($data, $where_str);
	}

	/**
	 * ͬ�����¹ؼ��ֱ�  
	 *
	 * @param  int $bind_id
	 * @return int
	 */
	public function menu_sync_cmd($bind_id){

		$bind_id = ( int ) $bind_id;
		if (empty($bind_id)) 
		{
			trace("�Ƿ����� bind_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$where_str = " menu_type='click'";
		$menu_list = $this->get_menu_list($bind_id,false,$where_str,'sort ASC,menu_id ASC','');
		if( empty($menu_list) )
			return false;
		$menu_id_arr = array();
		$__menu_list = array();
		foreach ( $menu_list as $k => $v ) {

			$__menu_list[$v['menu_id']] = $v;
			$menu_id_arr[] 				= $v['menu_id'];	
		
		}
		$menu_list   = $__menu_list;		
		$where_str   = " cmd_type='click'";
		$cmd_list    = $this->get_cmd_list($bind_id,false,$where_str,'','');
		$cmd_id_arr = array(); 
		$cmd_rid_arr = array(); 
		foreach ( $cmd_list as $k => $v ) {
			
			$cmd_id_arr[$v['cmd_rid']]  =  $v['cmd_id'];
			$cmd_rid_arr[] 				=  $v['cmd_rid'];

		}
		$need_update = array_intersect($menu_id_arr,$cmd_rid_arr);
		//�˵������� cmd��Ҳ��  ��ȡ��Ҫ���µ�menu_id
		$need_insert = array_diff($menu_id_arr,$cmd_rid_arr);
		//�˵������� cmd��û    ��Ҫ��ȱ�ٵ����ݲ���  
		$affect_rows = 0;
		if( !empty($need_update) ){

			foreach ( $need_update as $k => $menu_id ) {

				$cmd_data  = array();
				$menu_info = $menu_list[$menu_id];  //��ȡ��Ҫͬ�����µĲ˵�
				$cmd_id    = $cmd_id_arr[$menu_id]; //��ȡ��������cmd_id
				$cmd_data['cmd_val']   = $menu_info['menu_name'];
				$cmd_data['exec_type'] = $menu_info['exec_type'];
				$cmd_data['exec_val']  = $menu_info['exec_val'];
				$ret   	   = $this->modify_cmd($cmd_data,$cmd_id);
				$ret&&$affect_rows++;

			}

		}
		if( !empty($need_insert) ){

			foreach ( $need_insert as $k => $menu_id ) {

				$add_data  			   = array();
				$menu_info 			   = $menu_list[$menu_id];
				$add_data['bind_id']   = $menu_info['bind_id'];
				$add_data['cmd_type']  = 'click';
				$add_data['cmd_val']   = $menu_info['menu_name'];
				$add_data['cmd_rid']   = $menu_info['menu_id'];
				$add_data['exec_type'] = $menu_info['exec_type'];
				$add_data['exec_val']  = $menu_info['exec_val'];
				$ret 				   = $this->add_cmd($add_data);
				$ret&&$affect_rows++;

			}

		}
		$need_del_menu = array_diff($cmd_rid_arr,$menu_id_arr);
		//cmd��  �˵�û��  ˵���˵��ѱ�ɾ����������˷�click ����
		if( !empty($need_del_menu) ){

			foreach ($need_del_menu as $k => $menu_id) {

				$cmd_id = $cmd_id_arr[$menu_id];
				$this->del_cmd($cmd_id);

			}

		}
		return true;
	}	

	/**
	 * ��ȡ�˵���Ϣ 
	 *
	 * @param int $menu_id
	 * @return array
	 */
	public function get_menu_info($menu_id)
	{
		$menu_id = ( int ) $menu_id;
		if (empty($menu_id)) 
		{
			trace("�Ƿ����� menu_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_weixin_menu_tbl();
		$ret = $this->find ( "menu_id={$menu_id}" );
		return $ret;
	}

	/**
	 * ��ȡ�˵������б�
	 * @param $bind_id
	 * @return array|bool|int
	 */
	public function get_menu_tree($bind_id){

		$bind_id = intval($bind_id);
		if( $bind_id<1 )
		{
			return false;
		}
		$this->set_weixin_menu_tbl();
		$where_str = " bind_id={$bind_id} AND parent_id=0";		
		$menu_list = $this->get_menu_list($bind_id,false, $where_str,'sort ASC,menu_id ASC','');
		if( !empty($menu_list) ){

			foreach ($menu_list as $k => $v) {
				
				$__where_str = " parent_id={$v['menu_id']}";
				$menu_list[$k]['subclass'] = $this->get_menu_list($bind_id,false,$__where_str,'sort ASC,menu_id ASC','');

			}

		}
		return $menu_list;

	}

	/**
	 * ��ȡ�б�
	 * @param int $bind_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_menu_list($bind_id, $b_select_count=false, $where_str='', $order_by='sort ASC,menu_id ASC', $limit='0,20', $fields='*')
	{
		$bind_id = intval($bind_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $bind_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "bind_id={$bind_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_weixin_menu_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		$list = $this->findAll($sql_where, $limit, $order_by, $fields);
		return $list;
	}

	/**
	 * ��ȡ����Ϣ 
	 *
	 * @param int $news_id
	 * @return array
	 */
	public function get_news_info($news_id)
	{
		$news_id = ( int ) $news_id;
		if (empty($news_id)) 
		{
			trace("�Ƿ����� news_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
		}
		$this->set_weixin_news_tbl();
		$ret = $this->find ( "news_id={$news_id}" );
		return $ret;
	}

	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_news($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$data['add_time'] = time();
		$this->set_weixin_news_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * ����
	 * @param array $data
	 * @param int   $news_id
	 * @return int
	 */
	public function modify_news($data,$news_id)
	{
		if( !is_array($data) || empty($data) || empty($news_id) )
		{
			return false;
		}
		$where_str = " news_id = {$news_id} ";
		$this->set_weixin_news_tbl();
		$ret 	   = $this->update($data,$where_str);
		return $ret; 

	}

	/**
	 * ɾ��
	 * @param int   $news_id
	 * @return int
	 */
	public function del_news($news_id)
	{
		if( empty($news_id) )
		{
			return false;
		}
		$where_str = " news_id = {$news_id} ";
		$this->set_weixin_news_tbl();
		$ret = $this->delete($where_str);
		return $ret;

	}

	/**
	 * ��ȡ�б�
	 * @param int $bind_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_news_list($bind_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$bind_id = intval($bind_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $bind_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "bind_id={$bind_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_weixin_news_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		$list = $this->findAll($sql_where, $limit, $order_by, $fields);
		return $list;
	}
	
	/**
	 * ���û���
	 * @param string $cache_key
	 * @param mixed $cache_data
	 * @param array $options array('life_time'=>7200)
	 * @return boolean
	 */
	public function set_cache($cache_key, $cache_data, $options)
	{
		$cache_key = trim($cache_key);
		if( strlen($cache_key)<1 )
		{
			return false;
		}
		if( !is_array($options) ) $options = array();
		$life_time = intval($options['life_time']);
		
		//����db
		$info = array(
			'cache_key' => $cache_key,
			'cache_data' => serialize($cache_data),
			'life_time' => $life_time,
			'cache_time' => time(),
		);
		$this->set_weixin_cache_tbl();
		$this->insert($info, 'REPLACE');
		
		//����cache
		POCO::setCache($cache_key, $info, array('life_time'=>31536000));
		
		return true;
	}
	
	/**
	 * ��ȡ����
	 * @param string $cache_key
	 * @return mixed
	 */
	public function get_cache($cache_key)
	{
		$cache_key = trim($cache_key);
		if( strlen($cache_key)<1 )
		{
			return null;
		}
		
		//��ȡcache
		$info = POCO::getCache($cache_key);
		if( empty($info) )
		{
			//��ȡdb
			$where_str = "cache_key=:x_cache_key";
			sqlSetParam($where_str, 'x_cache_key', $cache_key);
			$this->set_weixin_cache_tbl();
			$info = $this->find($where_str);
			if( empty($info) )
			{
				return null;
			}
			
			//����cache
			POCO::setCache($cache_key, $info, array('life_time'=>31536000));
		}
		
		//�жϹ���
		$life_time = intval($info['life_time']);
		$cache_time = intval($info['cache_time']);
		if( $life_time>0 && ($cache_time+$life_time)<=time() )
		{
			return null;
		}
		
		//��������
		$cache_data = trim($info['cache_data']);
		if( strlen($cache_data)>0 )
		{
			$cache_data = unserialize($cache_data);
		}
		else
		{
			$cache_data = null;
		}
		
		return $cache_data;
	}
	
	/**
	 * ɾ������
	 * @param string $cache_key
	 * @return bool
	 */
	public function del_cache($cache_key)
	{
		$cache_key = trim($cache_key);
		if( strlen($cache_key)<1 )
		{
			return false;
		}
		
		//ɾ��db
		$where_str = "cache_key=:x_cache_key";
		sqlSetParam($where_str, 'x_cache_key', $cache_key);
		$this->set_weixin_cache_tbl();
		$this->delete($where_str);
		
		//ɾ��cache
		POCO::deleteCache($cache_key);
		
		return true;
	}
	
	/**
	 * ���ǩ��
	 * @param string $token ��Կ
	 * @param string $timestamp ʱ���
	 * @param string $nonce �����
	 * @param string $signature ΢�ż���ǩ��
	 * @return bool
	 */
	public function wx_check_signature($token, $timestamp, $nonce, $signature)
	{
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		if($tmpStr == $signature)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * ��ȡaccess_token
	 * @param $bind_id
	 * @param bool $b_cache
	 * @return string
	 */
	public function wx_get_access_token($bind_id, $b_cache=true)
	{
		$access_token = '';
		
		//������
		$bind_id = intval($bind_id);
		if( $bind_id<1 )
		{
			return $access_token;
		}
		
		//��ȡcache
		$cache_key = 'weixin_helper_access_token_'.  $bind_id;
		if( $b_cache )
		{
			$cache_data = $this->get_cache($cache_key);//POCO::getCache($cache_key);
			$access_token = trim($cache_data['access_token']);
		}
		
		//��ʱ��־
		$log_arr = array(
			'access_token' => $access_token,
		);
		pai_log_class::add_log($log_arr, 'wx_get_access_token');
		
		//��������
		if( strlen($access_token)<1 )
		{
			$bind_info = $this->get_bind_info($bind_id);
			$app_id = trim($bind_info['app_id']);
			$app_secret = trim($bind_info['app_secret']);
			$url = $this->wx_api_url . "cgi-bin/token?grant_type=client_credential&appid={$app_id}&secret={$app_secret}";
			$ret_json = $this->http($url, 'GET');
			$ret_info = json_decode($ret_json, true);
			if( !is_array($ret_info) ) $ret_info = array();
			$access_token = trim($ret_info['access_token']);
			
			//����cache
			if( strlen($access_token)>0 )
			{
				$expires_in = intval($ret_info['expires_in']);
				$this->set_cache($cache_key, $ret_info, array('life_time'=>$expires_in-200));
				//POCO::setCache($cache_key, $ret_info, array('life_time'=>$expires_in-200));
			}
		}
		
		return $access_token;
	}
	
	/**
	 * ��ȡ��ҳ��Ȩ����
	 * @param int $bind_id
	 * @param string $redirect_uri
	 * @param array $params array('mode'=>'', 'url'=>'')
	 * @param string $scope snsapi_userinfo|snsapi_base
	 * @param string $state
	 * @return string
	 */
	public function wx_get_auth2_authorize_url($bind_id, $redirect_uri, $params=array(), $scope='snsapi_base', $state='')
	{
		$bind_id = intval($bind_id);
		$redirect_uri = trim($redirect_uri);
		if( !is_array($params) ) $params = array();
		$scope = trim($scope);
		$state = trim($state);
		
		$params_str = '';
		$params_sp = '';
		foreach ($params as $key=>$val)
		{
			$params_str .= $params_sp . $key . '=' . urlencode($val);
			$params_sp = '&';
		}
		if( strlen($params_str)>0 )
		{
			if( strpos($redirect_uri, '?')===false )
			{
				$redirect_uri = $redirect_uri . '?' . $params_str;
			}
			else
			{
				$redirect_uri = $redirect_uri . '&' . $params_str;
			}
		}
		
		$bind_info = $this->get_bind_info($bind_id);
		$app_id = trim($bind_info['app_id']);
		
		$redirect_uri = urlencode($redirect_uri);
		$scope = urlencode($scope);
		$state = urlencode($state);
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$app_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
		
		return $url;
	}
	
	/**
	 * ��ȡ��ҳ��Ȩ��Ϣ
	 * @param int $bind_id
	 * @param string $code
	 * @return array
	 */
	public function wx_get_oauth2_access_info($bind_id, $code)
	{
		$code = trim($code);
		if( strlen($code)<1 )
		{
			return array();
		}
		$code = urlencode($code);
		
		$bind_info = $this->get_bind_info($bind_id);
		$app_id = trim($bind_info['app_id']);
		$app_secret = trim($bind_info['app_secret']);
		$url = $this->wx_api_url . "sns/oauth2/access_token?appid={$app_id}&secret={$app_secret}&code={$code}&grant_type=authorization_code";
		$ret_json = $this->http($url, 'GET');
		$ret_info = json_decode($ret_json, true);
		if( !is_array($ret_info) ) $ret_info = array();
		return $ret_info;
	}
	
	/**
	 * ��ȡJS API Ticket
	 * @param int $bind_id
	 * @param boolean $b_cache
	 * @return string
	 */
	private function wx_get_js_api_ticket($bind_id, $b_cache=true)
	{		
		$ticket = '';
		
		//������
		$bind_id = intval($bind_id);
		if( $bind_id<1 )
		{
			return $ticket;
		}
		
		//��ȡcache
		$cache_key = 'weixin_helper_js_api_ticket_'.  $bind_id;
		if( $b_cache )
		{
			$cache_data = $this->get_cache($cache_key);//POCO::getCache($cache_key);
			$ticket = trim($cache_data['ticket']);
		}
		
		//��ʱ��־
		$log_arr = array(
			'ticket' => $ticket,
		);
		pai_log_class::add_log($log_arr, 'wx_get_js_api_ticket');
		
		//��������
		if( strlen($ticket)<1 )
		{
			$access_token = $this->wx_get_access_token($bind_id);
			$url = $this->wx_api_url . "cgi-bin/ticket/getticket?type=jsapi&access_token={$access_token}";
			$ret_json = $this->http($url, 'GET');
			$ret_info = json_decode($ret_json, true);
			if( !is_array($ret_info) ) $ret_info = array();
			$ticket = trim($ret_info['ticket']);
			
			//����cache
			if( strlen($ticket)>0 )
			{
				$expires_in = intval($ret_info['expires_in']);
				$this->set_cache($cache_key, $ret_info, array('life_time'=>$expires_in-200));
				//POCO::setCache($cache_key, $ret_info, array('life_time'=>$expires_in-200));
			}
		}
		
		return $ticket;
	}
	
	/**
	 * ��ȡJS APIǩ������
	 * @param string $app_id
	 * @param string $url
	 * @return array
	 */
	public function wx_get_js_api_sign_package_by_app_id($app_id, $url)
	{
		$bind_info = $this->get_bind_info_by_app_id($app_id);
		if( empty($bind_info) )
		{
			return array();
		}
		$bind_id = intval($bind_info['bind_id']);
		$url = trim($url);
		
		$ticket = $this->wx_get_js_api_ticket($bind_id);
		$timestamp = time();
		$nonce_str = $this->create_nonce_str();
		
		//���������˳��Ҫ���� key ֵ ASCII ����������
		$string = "jsapi_ticket={$ticket}&noncestr={$nonce_str}&timestamp={$timestamp}&url={$url}";
		$signature = sha1($string);
		$sign_package = array(
			'appId'     => $app_id,
			'nonceStr'  => $nonce_str,
			'timestamp' => $timestamp,
			'signature' => $signature,
		);
		return $sign_package;
	}

	/**
	 * �����Զ���˵�
	 * @param $bind_id
	 * @return array|mixed
	 */
	public function wx_menu_create($bind_id)
	{
		$bind_id = intval($bind_id);
		if( $bind_id<1 )
		{
			return array();
		}
		
		$menu_list = $this->get_menu_list($bind_id, false, "parent_id=0", 'sort ASC, menu_id ASC', '0,99999999');
		if( empty($menu_list) )
		{
			return array();
		}
		
		$menu_json = '{"button":[';
		$menu_json_sp = '';
		foreach ($menu_list as $menu_info)
		{
			$menu_id = intval($menu_info['menu_id']);
			$menu_list_sub = $this->get_menu_list($bind_id, false, "parent_id={$menu_id}", 'sort ASC, menu_id ASC', '0,99999999');
			if( empty($menu_list_sub) )
			{
				$json_str = $this->menu_array_to_json($menu_info);
			}
			else
			{
				$menu_name = $this->format_json_val($menu_info['menu_name']);
				$json_str = '{"name":"'.$menu_name.'","sub_button":[';
				$json_str_sp = '';
				foreach ($menu_list_sub as $menu_info_sub)
				{
					$json_str_tmp = $this->menu_array_to_json($menu_info_sub);
					$json_str .= $json_str_sp . $json_str_tmp;
					$json_str_sp = ',';
				}
				$json_str .= ']}';
			}
			$menu_json .= $menu_json_sp . $json_str;
			$menu_json_sp = ',';
		}
		$menu_json .= ']}';
		
		$access_token = $this->wx_get_access_token($bind_id);
		$url = $this->wx_api_url . "cgi-bin/menu/create?access_token={$access_token}";
		$ret_json = $this->http($url, 'POST', $this->gbk_to_utf8($menu_json));
		$ret_info = json_decode($ret_json, true);
		if( !is_array($ret_info) ) $ret_info = array();
		return $ret_info;
	}
	
	/**
	 * ���Ϳͷ���Ϣ
	 * @param int $bind_id
	 * @param array $message_data array('touser'=>'', 'msgtype'=>'text', 'content'=>'')
	 * @return boolean
	 */
	public function wx_message_custom_send($bind_id, $message_data)
	{
		$bind_id = intval($bind_id);
		if( $bind_id<1 || !is_array($message_data) || empty($message_data) )
		{
			return false;
		}
		
		//������¼
		$data = $message_data;
		$data['bind_id'] = $bind_id;
		$data['add_time'] = time();
		$id = $this->add_message_custom($data);
		
		//TODO Ŀǰ��֧���ı�
		$json = $this->message_custom_array_to_json_text($message_data);
		
		$ret = false;
		$access_token = $this->wx_get_access_token($bind_id);
		$url = $this->wx_api_url . "cgi-bin/message/custom/send?access_token={$access_token}";
		$ret_json = $this->http($url, 'POST', $this->gbk_to_utf8($json));
		$ret_info = json_decode($ret_json, true);
		if( !is_array($ret_info) ) $ret_info = array();
		if( !empty($ret_info) && $ret_info['errcode']===0 )
		{
			$ret = true;
		}
		
		//���¼�¼
		$data = array(
			'errcode' => trim($ret_info['errcode']),
			'errmsg' => trim($ret_info['errmsg']),
		);
		$this->update_message_custom($data, $id);
		
		return $ret;
	}
	
	/**
	 * ����ģ����Ϣ
	 * @param int $bind_id
	 * @param string $touser
	 * @param string $template_id
	 * @param string $data array( 'keyword1'=>array('value'=>'', 'color'=>''), 'keyword2'=>array('value'=>'', 'color'=>'') )
	 * @param string $url
	 * @param string $topcolor #FF0000
	 * @return boolean
	 */
	public function wx_message_template_send($bind_id, $touser, $template_id, $data, $url='', $topcolor='')
	{
		$bind_id = intval($bind_id);
		$touser = trim($touser);
		$template_id = trim($template_id);
		if( $bind_id<1 || strlen($touser)<1 || strlen($template_id)<1 )
		{
			return false;
		}
		if( !is_array($data) ) $data = array();
		
		$params = array(
			'touser' => $touser,
			'template_id' => $template_id,
			'url' => $url,
			'topcolor' => $topcolor,
			'data' => $data,
		);
		
		//������¼
		$info = $params;
		$info['data'] = serialize($info['data']);
		$info['bind_id'] = $bind_id;
		$info['add_time'] = time();
		$id = $this->add_message_template($info);
		
		$ret = false;
		$access_token = $this->wx_get_access_token($bind_id);
		$url = $this->wx_api_url . "cgi-bin/message/template/send?access_token={$access_token}";
		$params = $this->gbk_to_utf8($params);
		$params_json = json_encode($params);
		$ret_json = $this->http($url, 'POST', $params_json);
		$ret_info = json_decode($ret_json, true);
		if( !is_array($ret_info) ) $ret_info = array();
		if( !empty($ret_info) && $ret_info['errcode']===0 )
		{
			$ret = true;
		}
		
		//���¼�¼
		$info = array(
			'errcode' => trim($ret_info['errcode']),
			'errmsg' => trim($ret_info['errmsg']),
			'msgid' => trim($ret_info['msgid']),
		);
		$this->update_message_template($info, $id);
		
		return $ret;
	}
	
	/**
	 * ����Ϣ���͵�xmlת������
	 * @param string $xml
	 * @return array
	 */
	public function push_xml_to_array($xml)
	{
		$push_data = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$push_data = $this->utf8_to_gbk($push_data);
		return $push_data;
	}
	
	/**
	 * ������ת����Ϣ�ظ���xml
	 * @param array $array
	 * @return string
	 */
	public function reply_array_to_xml($array)
	{
		$xml = '';
		if( !is_array($array) || empty($array) )
		{
			return $xml;
		}
		
		$MsgType = trim($array['MsgType']);
		$method_name = 'reply_array_to_xml_' . $MsgType;
		if( !method_exists($this, $method_name) )
		{
			return $xml;
		}
		
		$xml_ext = call_user_func(array($this, $method_name), $array);
		if( $xml_ext===false )
		{
			return $xml;
		}
		
		$keys = array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType');
		$xml_main = $this->array_to_xml($array, $keys);
		
		$xml = "<xml>\r\n" . $xml_main . $xml_ext . "</xml>";
		$xml = $this->gbk_to_utf8($xml);
		return $xml;
	}
	
	/**
	 * ��ȡ�ظ���Ϣ��������������
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array
	 */
	public function wx_get_reply_by_push($bind_id, $push_data)
	{
		//������
		$bind_id = intval($bind_id);
		if( $bind_id<1 || empty($push_data) )
		{
			return array();
		}
		
		//���������Ϣ
		$data = $push_data;
		$data['bind_id'] = $bind_id;
		$data['add_time'] = time();
		if( isset($data['Precision']))
		{
			//Precision��mysql�Ĺؼ���
			$data['Precision_Str'] = $data['Precision'];
			unset($data['Precision']);
		}
		$receive_id = $this->add_receive($data);
		if( $receive_id<1 )
		{
			return array();
		}
		
		//��ȡָ����Ϣ��������������
		$cmd_info = array();
		$MsgType = trim($push_data['MsgType']);
		$method_name = 'wx_get_cmd_by_push_' . $MsgType;
		if( method_exists($this, $method_name) )
		{
			$cmd_info = call_user_func(array($this, $method_name), $bind_id, $push_data);
		}
		//�������ݣ�����Ϊ�գ���Ĭ��
		if( is_array($cmd_info) && empty($cmd_info) )
		{
			$cmd_info = call_user_func(array($this, 'wx_get_cmd_by_push_default'), $bind_id, $push_data);
		}
		if( !is_array($cmd_info) || empty($cmd_info) )
		{
			return array();
		}
		//ע�⣺$cmd_info����ֻ��exec_type����cmd_idҲ�����Ǹ���
		
		//���½�����Ϣ
		$cmd_id = intval($cmd_info['cmd_id']);
		$this->update_receive(array('cmd_id'=>$cmd_id), $receive_id);
		
		//��ȡ�ظ���Ϣ������ָ����Ϣ
		$exec_type = trim($cmd_info['exec_type']);
		$method_name = 'wx_get_reply_by_exec_' . $exec_type;
		if( !method_exists($this, $method_name) ) $method_name = 'wx_get_reply_by_exec_default';
		$reply_data = call_user_func(array($this, $method_name), $bind_id, $push_data, $cmd_info);
		if( $reply_data===false || empty($reply_data) )
		{
			return array();
		}
		$reply_data['ToUserName'] = $push_data['FromUserName'];
		$reply_data['FromUserName'] = $push_data['ToUserName'];
		$reply_data['CreateTime'] = time();
		
		//������Ϣ�ظ�
		$data = $reply_data;
		$data['bind_id'] = $bind_id;
		$data['receive_id'] = $receive_id;
		$data['add_time'] = time();
		foreach ($data as $key=>$val)
		{
			if( is_array($val) ) $val = serialize($val);
			$data[$key] = $val;
		}
		$this->add_reply($data);
		
		return $reply_data;
	}
	
	/**
	 * ��ȡ��������ı�
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array|false
	 */
	private function wx_get_cmd_by_push_text($bind_id, $push_data)
	{
		//��ȡ�ؼ���
		$content = trim($push_data['Content']);
		$content_arr = preg_split('/#/', $content);
		$keyword = trim($content_arr[0]);
		if( strlen($keyword)<1 )
		{
			return array();
		}
		$keyword_upper = strtoupper($keyword);
		
		//��ȡ�ؼ���ָ��
		$cmd_list = $this->get_cmd_list($bind_id, false, "cmd_type IN ('keywords','keywords_contain')", 'cmd_id ASC', '0,99999999');
		if( empty($cmd_list) )
		{
			return array();
		}
		
		//ƥ��
		$cmd_info = array();
		foreach ($cmd_list as $cmd_info_tmp)
		{
			$cmd_type_tmp = trim($cmd_info_tmp['cmd_type']);
			$cmd_val_tmp = trim($cmd_info_tmp['cmd_val']);
			$keyword_arr_tmp = preg_split('/[\s,]+/', $cmd_val_tmp, -1, PREG_SPLIT_NO_EMPTY);
			foreach ($keyword_arr_tmp as $keyword_tmp)
			{
				$keyword_upper_tmp = strtoupper($keyword_tmp);
				if( $keyword_upper==$keyword_upper_tmp || ($cmd_type_tmp=='keywords_contain' && strpos($keyword_upper, $keyword_upper_tmp)!==false ) )
				{
					$cmd_info = $cmd_info_tmp;
					break;
				}
			}
			if( !empty($cmd_info) )
			{
				break;
			}
		}
		
		return $cmd_info;
	}
	
	/**
	 * ��ȡ��������¼�
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array|false
	 */
	private function wx_get_cmd_by_push_event($bind_id, $push_data)
	{
		$event = trim($push_data['Event']);
		$event = strtoupper($event); //ת�ɴ�д��ĸ
		$event_key = trim($push_data['EventKey']);
		if( $event=='SUBSCRIBE' )
        {
            //����û�
            $subscribe_open_id = trim($push_data['FromUserName']);
            $data = array('is_subscribe'=>1, 'subscribe_time'=>time());
            $this->save_user($data, $bind_id, $subscribe_open_id);

            //�����û�������Ϣ
            $this->sync_user_base_info($bind_id, "open_id='{$subscribe_open_id}'");

			//��ע�Զ��ظ�
			$cmd_list = $this->get_cmd_list($bind_id, false, "cmd_type='subscribe'", 'cmd_id ASC', '0,1');
			if( empty($cmd_list) )
			{
				return false;
			}
			$cmd_info = $cmd_list[0];
			if( !is_array($cmd_info) ) $cmd_info = false;
			
			return $cmd_info;
		}
		elseif( $event=='UNSUBSCRIBE' )
		{
            //�����û���ע״̬
            $subscribe_open_id = $push_data['FromUserName'];
            $data = array('is_subscribe'=>0, 'subscribe_time'=>time());
            $this->save_user($data, $bind_id, $subscribe_open_id);

			//ȡ����ע
			return false;
		}
		elseif( $event=='VIEW' )
		{
			//�Զ���˵�����תURL
			return false;
		}
		elseif( $event=='CLICK' )
		{
			//�Զ���˵���������¼�
			$where_str = "cmd_type='click' AND cmd_val=:x_cmd_val";
			sqlSetParam($where_str, 'x_cmd_val', $event_key);
			$cmd_list = $this->get_cmd_list($bind_id, false, $where_str, 'cmd_id ASC', '0,1');
			if( empty($cmd_list) )
			{
				//�����Զ���˵���Keyʱ�����ܻᵼ���Ҳ�����Ӧ�����
				return array();
			}
			$cmd_info = $cmd_list[0];
			if( !is_array($cmd_info) ) $cmd_info = false;
			
			return $cmd_info;
		}
		elseif( $event=='LOCATION' )
		{
			//����λ��
			$data = array(
				'latitude' => $push_data['Latitude'],
				'longitude' => $push_data['Longitude'],
				'precision_str' => $push_data['Precision'],
				'location_time' => time(),
			);
			$this->save_user($data, $bind_id, $push_data['FromUserName']);
			
			return false;
		}
		elseif( $event=='TEMPLATESENDJOBFINISH' )
		{
			//ģ����Ϣ��ִ
			$data = array(
				'status' => $push_data['Status'],
			);
			$this->update_message_template_by_msgid($data, $bind_id, $push_data['MsgID']);
			
			return false;
        }
        elseif( $event=='MASSSENDJOBFINISH')//Ⱥ��, ����¼�����
        {
        	//����Ⱥ����Ϣ״̬
            $data = array(
                'push_time' => time(),
                'wx_status' => trim($push_data['Status']),
                'wx_total_count' => trim($push_data['TotalCount']),
                'wx_sent_count' => trim($push_data['SentCount']),
                'wx_filter_count' => trim($push_data['Filter_count']),
                'wx_error_count' => trim($push_data['ErrorCount']),
            );
            $this->update_mass_send_msg_by_wx($data, $bind_id, $push_data['MsgID']);
            
            return false;
        }
		
		return false;
	}

	/**
	 * ��ȡ�������ͼƬ
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array|false
	 */
	private function wx_get_cmd_by_push_image($bind_id, $push_data)
	{
		$open_id = trim($push_data['FromUserName']);
		
		//����û���ǰģʽ
		$mode_code = $this->get_user_mode_code($bind_id, $open_id);
		if( $mode_code=='YUE_CREDIT2_STEP1' )
		{
			//���õȼ�V2�������֤
			return array('cmd_id'=>-1, 'exec_type'=>'YUE_CREDIT2_STEP2');
		}
		
		return $this->wx_get_cmd_by_push_default($bind_id, $push_data);
	}
	
	/**
	 * ��ȡ�������Ĭ��
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array|false
	 */
	private function wx_get_cmd_by_push_default($bind_id, $push_data)
	{
		$cmd_list = $this->get_cmd_list($bind_id, false, "cmd_type='default'", 'cmd_id ASC', '0,1');
		if( empty($cmd_list) )
		{
			return false;
		}
		$cmd_info = $cmd_list[0];
		if( !is_array($cmd_info) ) $cmd_info = false;
		
		//Ĭ�ϻظ�֮�󣬰�Сʱ�ڲ���Ĭ�ϻظ�
		$from_user_name = trim($push_data['FromUserName']);
		$exec_val = trim($cmd_info['exec_val']);
		if( strlen($from_user_name)>0 && strlen($exec_val)>0 )
		{
			$cache_key = 'weixin_helper_get_cmd_default_' . $bind_id . '_'.  $from_user_name;
			$cache_data = POCO::getCache($cache_key);
			if( !empty($cache_data) )
			{
				$cmd_info['exec_val'] = '';
				return $cmd_info;
			}
			else
			{
				POCO::setCache($cache_key, array('time'=>time()), array('life_time'=>1800));
			}
		}
		
		return $cmd_info;
	}
	
	/**
	 * ��ȡ�ظ����ݣ������ı�
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_text($bind_id, $push_data, $cmd_info)
	{
		$content = trim($cmd_info['exec_val']);
		if( strlen($content)<1 )
		{
			return array();
		}
		
		$reply_data = array(
			'MsgType' => 'text',
			'Content' => $content,
		);
		return $reply_data;
	}
	
	/**
	 * ��ȡ�ظ����ݣ�����ͼ��
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_news($bind_id, $push_data, $cmd_info)
	{
		//����ͼ��ID
		$news_id_arr = array();
		$exec_val = trim($cmd_info['exec_val']);
		$exec_val_arr = explode(',', $exec_val);
		foreach ($exec_val_arr as $val)
		{
			$val = intval($val);
			if( $val>0 ) $news_id_arr[] = $val;
		}
		if( empty($news_id_arr) )
		{
			return array();
		}
		$news_id_str = implode(',', $news_id_arr);
		
		//��ȡͼ���б�΢���������10��
		$news_list = array();
		$news_list_tmp = $this->get_news_list($bind_id, false, "news_id IN ({$news_id_str})", 'news_id ASC', '0,10');
		foreach ($news_list_tmp as $news_info_tmp)
		{
			$news_id_tmp = intval($news_info_tmp['news_id']);
			$news_list[$news_id_tmp] = $news_info_tmp;
		}
		
		//����ͼ������
		$articles = array();
		foreach ($news_id_arr as $news_id)
		{
			$news_info = $news_list[$news_id];
			if( !is_array($news_info) || empty($news_info) )
			{
				continue;
			}
			
			$title = trim($news_info['title']);
			$description = trim($news_info['description']);
			$pic_url = trim($news_info['pic_url']);
			$url = trim($news_info['url']);
			
			$articles[] = array(
				'Title' => $title,
				'Description' => $description,
				'PicUrl' => $pic_url,
				'Url' => $url
			);
		}
		if( empty($articles) )
		{
			return array();
		}
		
		$reply_data = array(
			'MsgType' => 'news',
			'ArticleCount' => count($articles),
			'Articles' => $articles,
		);
		return $reply_data;
	}

	/**
	 * ��ȡ�ظ����ݣ�����ͼƬ
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
     */
	private function wx_get_reply_by_exec_image($bind_id, $push_data, $cmd_info)
    {
        //����زĵ���Դid
        $exec_val = trim($cmd_info['exec_val']);
        $media_id = $exec_val;
        if( strlen($media_id)<1 )
        {
            return array();
        }

        //��ȡ��ȡ�ز���Ϣ, �����ظ�ֻ֧��һ��ͼƬ
        $material_info = $this->get_material_info($bind_id, $media_id);
        if( !is_array($material_info)||empty($material_info)||$material_info['material_type']!='image' )
        {
            return array();
        }

        return array(
			'MsgType' => 'image',
            'MediaId'=>$media_id, 
        );
    }
	
	/**
	 * ��ȡ�ظ����ݣ�����ת������ͷ�
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_transfer_customer_service($bind_id, $push_data, $cmd_info)
	{
		//���Ϳͷ���Ϣ
		$content = trim($cmd_info['exec_val']);
		if( strlen($content)>0 )
		{
			$message_data = array(
				'touser' => $push_data['FromUserName'],
				'msgtype' => 'text',
				'content' => $content,
			);
			$this->wx_message_custom_send($bind_id, $message_data);
		}
		
		$reply_data = array(
			'MsgType' => 'transfer_customer_service',
		);
		return $reply_data;
	}
	
	/**
	 * Ĭ�ϻظ�����
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_default($bind_id, $push_data, $cmd_info)
	{
		//��ȡĬ��ָ��
		$cmd_info_default = $this->wx_get_cmd_by_push_default($bind_id, $push_data);
		
		$content = trim($cmd_info_default['exec_val']);
		if( strlen($content)<1 )
		{
			return array();
		}
		
		$reply_data = array(
			'MsgType' => 'text',
			'Content' => $content,
		);
		return $reply_data;
	}
	
	/**
	 * ���õȼ�V2�������֤��һ��
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_yue_credit2_step1($bind_id, $push_data, $cmd_info)
	{
		$open_id = trim($push_data['FromUserName']);
		
		//�������
		$pai_bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
		$bind_info = $pai_bind_weixin_obj->get_bind_info_by_open_id($open_id);
		if( empty($bind_info) )
		{
			return array('MsgType'=>'text', 'Content'=>'��Ǹ�����ȵ�¼����΢�ţ�<a href="http://yp.yueus.com/m/auth.php?route=mine">�������</a>���в�����');
		}
		$user_id = intval($bind_info['user_id']);
		
		//������õȼ�
		$pai_user_level_obj = POCO::singleton('pai_user_level_class');
		$user_level = $pai_user_level_obj->get_user_level($user_id);
		if( $user_level!=1 )
		{
			return array('MsgType'=>'text', 'Content'=>'��ã����Ѿ���V2/V3�û��ˣ������ٴ���֤��');
		}
		
		//�����û�ģʽ
		$this->save_user_mode($bind_id, $open_id, 'YUE_CREDIT2_STEP1', 1800);
		
		return array('MsgType'=>'text', 'Content'=>'��ã���<a href="http://yp.yueus.com/wap/others/wx_v2jc.php">����˴�</a>�鿴��֤���̡�����Ҫ����30�����ڷ���V2��֤���ϡ�');
	}
	
	/**
	 * ���õȼ�V2�������֤�ڶ���
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_yue_credit2_step2($bind_id, $push_data, $cmd_info)
	{
		$open_id = trim($push_data['FromUserName']);
		
		//�����û�ģʽ
		$this->save_user_mode($bind_id, $open_id, '');
		
		//�������
		$pai_bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
		$bind_info = $pai_bind_weixin_obj->get_bind_info_by_open_id($open_id);
		if( empty($bind_info) )
		{
			//���������step1�Ѿ���飬�����ܽ���step2
			return array();
		}
		$user_id = intval($bind_info['user_id']);
		
		//������õȼ�
		$pai_user_level_obj = POCO::singleton('pai_user_level_class');
		$user_level = $pai_user_level_obj->get_user_level($user_id);
		if( $user_level!=1 )
		{
			return array('MsgType'=>'text', 'Content'=>'��ã����Ѿ���V2/V3�û��ˣ������ٴ���֤��');
		}
		
		$pai_id_audit_obj = POCO::singleton('pai_id_audit_class');
		$insert_data = array();
		$insert_data['user_id'] = $user_id;
		$insert_data['img'] = trim($push_data['PicUrl']);
		$pai_id_audit_obj->add_audit($insert_data);
		
		return array('MsgType'=>'text', 'Content'=>'����������ύ�ɹ��������ĵȴ���ˡ�������΢��֪ͨ��');
	}
	
	/**
	 * ���ɴ�����ֵ�Ķ�ά��
	 * @param int $bind_id      �󶨺�
	 * @param string $type      ��ά�����ͣ�
	 *                          ֧����������QR_SCENEΪ��ʱ��QR_LIMIT_SCENEΪ���ã�QR_LIMIT_STR_SCENEΪ���õ��ַ���
	 * @param int $scene_id     ����ֵ������Ϊ��ʱ������ʱ������ܴ���10��
	 * @param int $expires_time ����ʱ�䣬����Ϊ��ʱʱ�����λ�룬��������죬С��60��΢�Ż�����Ϊ60��
	 * @param string $scene_str �ַ�������ֵ������Ϊ�ַ������Ǳ���
	 * @return array 			ticket ��ά�����ӣ�url ��ά�����ݣ�[expire_time] ��ʱ������Ĺ���ʱ��
	 */
	public function wx_creat_scene_qr_code($bind_id, $type, $scene_id = null, $expire_time = 604800, $scene_str = '')
	{
		$rst = array();
		if( $bind_id<1 ) return $rst;
		if( !in_array($type,$this->qr_type) ) return $rst;
		if( $type == 'QR_SCENE'&&( ($expire_time<1||$expire_time>604800)||($scene_id<=0||$scene_id>100000) ) ) return $rst;//����ֵ���
		if( $type == 'QR_LIMIT_SCENE'&&($scene_id<=0||$scene_id>100000) ) return $rst;//����ֵ���
		if( $type == 'QR_LIMIT_STR_SCENE'&&strlen($scene_str)<1 ) return $rst;//����ֵ���
		
		$token_str = $this->wx_get_access_token($bind_id);
		$request_url = $this->wx_api_url."cgi-bin/qrcode/create?access_token={$token_str}";
		
		if( $type == 'QR_SCENE' ) $data_arr = array(
			'expire_seconds' => $expire_time,
			'action_name' => $type,
			'action_info' => array(
				'scene' => array(
					'scene_id' => $scene_id,
					),
				),
		);
		if( $type == 'QR_LIMIT_SCENE' ) $data_arr = array(
			'action_name' => $type,
			'action_info' => array(
				'scene' => array(
				'scene_id' => $scene_id,
				),
			),
		);		
		if( $type == 'QR_LIMIT_STR_SCENE' ) $data_arr = array(
			'action_name' => $type,
			'action_info' => array(
			'scene' => array(
				'scene_str' => iconv('GBK', 'utf-8', $scene_str),
				),
			),
		);
		
		$data_json = json_encode($data_arr);
		$response_str = $this->http($request_url, 'POST', $data_json);
		$rst = json_decode($response_str, true);
		if( isset($rst['ticket']) ) $rst['wx_qr_code_link'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($rst['ticket']);
		return $rst;
	}

	/**
	 * �ظ��ı���Ϣ
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_text($array)
	{
		$keys = array('Content');
		return $this->array_to_xml($array, $keys);
	}
	
	/**
	 * �ظ�ͼƬ��Ϣ
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_image($array)
	{
		$keys = array('MediaId');
		$xml_ext = $this->array_to_xml($array, $keys);
		if( strlen($xml_ext)<1 ) return '';
		
		$xml = "<Image>\r\n" . $xml_ext . "</Image>\r\n";
		return $xml;
	}
	
	/**
	 * �ظ�������Ϣ
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_voice($array)
	{
		$keys = array('MediaId');
		$xml_ext = $this->array_to_xml($array, $keys);
		if( strlen($xml_ext)<1 ) return '';
		
		$xml = "<Voice>\r\n" . $xml_ext . "</Voice>\r\n";
		return $xml;
	}
	
	/**
	 * �ظ���Ƶ��Ϣ
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_video($array)
	{
		$keys = array('MediaId', 'Title', 'Description');
		$xml_ext = $this->array_to_xml($array, $keys);
		if( strlen($xml_ext)<1 ) return '';
		
		$xml = "<Video>\r\n" . $xml_ext . "</Video>\r\n";
		return $xml;
	}
	
	/**
	 * �ظ�������Ϣ
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_music($array)
	{
		$keys = array('Title', 'Description', 'MusicUrl', 'HQMusicUrl', 'ThumbMediaId');
		$xml_ext = $this->array_to_xml($array, $keys);
		if( strlen($xml_ext)<1 ) return '';
	
		$xml = "<Music>\r\n" . $xml_ext . "</Music>\r\n";
		return $xml;
	}
	
	/**
	 * �ظ�ͼ����Ϣ
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_news($array)
	{
		$articles = $array['Articles'];
		if( !is_array($articles) || empty($articles) ) return '';
		
		$article_count = 0;
		$xml_main = '';
		foreach ($articles as $article)
		{
			$keys = array('Title', 'Description', 'PicUrl', 'Url');
			$xml_ext = $this->array_to_xml($article, $keys);
			if( strlen($xml_ext)<1 ) continue;
			
			$article_count++;
			$xml_main .= "<item>\r\n" . $xml_ext . "</item>\r\n";
		}
		if( strlen($xml_main)<1 ) return '';
		
		$xml = "<ArticleCount>" . $article_count . "</ArticleCount>\r\n";
		$xml .= "<Articles>\r\n" . $xml_main . "</Articles>\r\n";
		
		return $xml;
	}
	
	/**
	 * �ظ���ͷ���Ϣ
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_transfer_customer_service($array)
	{
		$keys = array();
		return $this->array_to_xml($array, $keys);
	}
	
	/**
	 * �ͷ���ϢJSON���ı�
	 * @param array $array
	 * @return boolean
	 */
	private function message_custom_array_to_json_text($array)
	{
		$touser = $this->format_json_val($array['touser']);
		$content = $this->format_json_val($array['content']);
		$json = '{"touser":"'.$touser.'","msgtype":"text","text":{"content":"'.$content.'"}}';
		return $json;
	}
	
	/**
	 * ��ȡJSON��ʽ�����ݲ˵���Ϣ
	 * @param unknown $menu_info
	 * @return string
	 */
	private function menu_array_to_json($menu_info)
	{
		$menu_json = '';
		
		$menu_name = $this->format_json_val($menu_info['menu_name']);
		$menu_type = trim($menu_info['menu_type']);
		if( $menu_type=='normal' ) $menu_type = '';
		if( $menu_type=='view' )
		{
			$menu_url = $this->format_json_val($menu_info['menu_url']);
			$menu_json = '{"type":"view", "name":"'.$menu_name.'", "url":"'.$menu_url.'"}';
		}
		else 
		{
			$menu_type = $this->format_json_val($menu_type);
			$menu_key = $this->format_json_val($menu_info['menu_key']);
			$menu_json = '{"type":"'.$menu_type.'", "name":"'.$menu_name.'", "key":"'.$menu_key.'"}';
		}
		
		return $menu_json;
	}
	
	/**
	 * ��ȡxml
	 * @param array $array
	 * @param array $keys
	 * @return string
	 */
	private function array_to_xml($array, $keys)
	{
		$xml = '';
		if( !is_array($array) || empty($array) || !is_array($keys) )
		{
			return $xml;
		}
		
		foreach ($keys as $key)
		{
			$val = trim($array[$key]);
			if( preg_match('/^[0-9.]+$/', $val) )
			{
				$xml .= "<{$key}>{$val}</{$key}>\r\n";
			}
			else
			{
				$xml .= "<{$key}><![CDATA[{$val}]]></{$key}>\r\n";
			}
		}
		
		return $xml;
	}
	
	/**
	 * ��ʽ��JSONֵ
	 * @param string $str
	 * @return string
	 */
	private function format_json_val($str)
	{
		$str = trim($str);
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('"', '\"', $str);
		$str = str_replace("\r\n", '\r\n', $str);
		$str = str_replace("\n", '\n', $str);
		return $str;
	}
	
	/**
	 * ����
	 * @param int $length
	 * @return string
	 */
	private function create_nonce_str($length = 16)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = '';
		for ($i = 0; $i < $length; $i++)
		{
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

    /**
     * @Synopsis ȡ�ù��ںŵĹ�ע�б�
     * @Param $bind_id int ��ID
     * @Param $next_openid string ΢�ŷ��ص�next_openid
     * @Returns array openid��ɵ�����
     */
    private function wx_get_subscribe_list($bind_id, $next_openid = '')
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return array();
        }

        $token_str = $this->wx_get_access_token($bind_id);
        $url = $this->wx_api_url."cgi-bin/user/get?access_token={$token_str}&next_openid={$next_openid}";
        $ret_json = $this->http($url, 'GET');
        $ret_info = json_decode($ret_json, true);   
        if( !isset($ret_info['total']) )//���󲻳ɹ�
        {
            return array();
        }

        $next_openid = $ret_info['next_openid'];
        $open_id_list = $ret_info['data']['openid'];
        //��������10000, �ҵ�ǰȡ��10000, �б�û��ȡ��
        if( $ret_info['total']>10000&&$ret_info['count']==10000 )
        {
            $next_batch_open_id_list = $this->wx_get_subscribe_list($bind_id, $next_openid);
            return array_merge($open_id_list, $next_batch_open_id_list);
        }
        return $open_id_list;
    }

    /**
     * @Synopsis ���¹�ע�б�
     *
     * @Param $bind_id int �󶨺�
     *
     * @Returns array ����ʾ�ķ��ؽ��
     */
    public function sync_subscribe_list($bind_id)
    {
        $rst = array('err_code'=>0, 'message'=>'', 'subscribe_summary'=>array(), );

        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '��������';
            return $rst;
        }

        //��΢��ȡ����ע�б�
        $subscribe_open_id_list = $this->wx_get_subscribe_list($bind_id);
        if( empty($subscribe_open_id_list) )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '��ע�б�Ϊ��';
            return $rst;
        }

        //�����ݿ�ȡ����ע�б�
        $user_list = $this->get_user_list($bind_id, false, 'is_subscribe=1', 'id ASC', '0,999999999', 'open_id');
        //��ά
        $user_open_id_list = array_map('array_shift', $user_list);

        //����������ע�û�
        $new_subscribe_open_id_list = array_diff($subscribe_open_id_list, $user_open_id_list);
        //����ȡ����ע�û�
        $unsubscribed_open_id_list = array_diff($user_open_id_list, $subscribe_open_id_list);

        $data = array('is_subscribe'=>1, 'subscribe_time'=>time(), );
        foreach($new_subscribe_open_id_list as $subscribe_open_id)
        {
            $this->save_user($data, $bind_id, $subscribe_open_id);
        }

        $data = array('is_subscribe'=>0, 'subscribe_time'=>time());
        foreach($unsubscribed_open_id_list as $subscribe_open_id)
        {
            $this->save_user($data, $bind_id, $subscribe_open_id);
        }

        $rst['err_code'] = 1;
        $rst['message'] = '���³ɹ�';
        $rst['subscribe_summary'] = array(
            'subscribed'=>count($new_subscribe_open_id_list),
            'unsubscribed'=>count($unsubscribed_open_id_list), 
        );
        return $rst;
    }

    /**
     * @Synopsis ȡ�û�������Ϣ
     *
     * @Param $bind_id int �󶨺�
     * @Param $openid array �û�openid�б�
     *
     * @Returns array �û���Ϣ��ά����
     */
    private function wx_get_user_base_info($bind_id, $open_id_list)
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return array();
        }
        if( !is_array($open_id_list)||count($open_id_list)<1 )
        {
            return array();
        }

        if( count($open_id_list)>100 )
        {
            $f_open_id_list = array_slice($open_id_list, 0, 100);
            $remind_open_id_list = array_slice($open_id_list, 99);

            unset($open_id_list);
            return array_merge($this->wx_get_user_base_info($bind_id, $f_open_id_list), $this->wx_get_user_base_info($bind_id, $remind_open_id_list));
        }

        $token_str = $this->wx_get_access_token($bind_id);
        $url = $this->wx_api_url."cgi-bin/user/info/batchget?access_token={$token_str}";
        $data_arr = array('user_list'=>$open_id_list);
        $data_json = json_encode($data_arr);

        $ret_json = $this->http($url, 'POST', $data_json);
        $ret_info = json_decode($ret_json, true);   

        unset($token_str);
        unset($url);
        unset($data_arr);
        unset($data_json);
        unset($ret_json);

        if( !isset($ret_info['user_info_list']) )//���󲻳ɹ�
        {
            return array();
        }
        return $ret_info['user_info_list'];
    }

    /**
     * @Synopsis �����û�������Ϣ
     *
     * @Param $bind_id int �󶨺�
     *
     * @Returns array
     */
    public function sync_user_base_info($bind_id, $where_append = '', $limit = '0,100')
    {
        $rst = array('err_code'=>0, 'message'=>'', 'sync_summary'=>array(), );

        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '��������';
            return $rst;
        }
        $where_str = 'is_subscribe=1';
        if(strlen($where_append)>0)
        {
            $where_str .= " AND {$where_append}";
        }

        $user_list = $this->get_user_list($bind_id, false, $where_str, 'add_time DESC,id DESC', $limit, 'open_id');
        if( empty($user_list) )
        {
            $rst['err_code'] = -2;
            $rst['message'] = '�û���ע�б�Ϊ��';
            return $rst;
        }

        $user_open_id_list = array();
        foreach($user_list as $user_info)
        {
            $user_open_id_list[] = array('openid'=>$user_info['open_id']); 
        }
        unset($user_list);

        $user_info_list = $this->wx_get_user_base_info($bind_id, $user_open_id_list);
        if( empty($user_info_list) )
        {
            $rst['err_code'] = -3;
            $rst['message'] = '�޷�ȡ�ù�ע�б�';
            $rst['sync_summary'] = array(
                'count_request'=>count($user_open_id_list), 
                'count_synced'=>0, 
            );
            return $rst;
        }

        $i = 0;
        foreach($user_info_list as $user_info)
        {
            $i++;
            $is_subscribe = intval($user_info['subscribe']);
            $subscribe_time = time();
            if( $is_subscribe==1 )//�û��ѹ�ע
            {
                $nickname = preg_replace('/\\\\/', '', $this->utf8_to_gbk($user_info['nickname']));
                $sex = $user_info['sex'];
                $language = $user_info['language'];
                $city = $this->utf8_to_gbk($user_info['city']);
                $province = $this->utf8_to_gbk($user_info['province']);
                $country = $this->utf8_to_gbk($user_info['country']);
                $headimgurl = $user_info['headimgurl'];
                $subscribe_time = $user_info['subscribe_time'];
                $unionid = $user_info['unionid'];
                $remark = $this->utf8_to_gbk($user_info['remark']);
                $group_id = $user_info['groupid'];
            }
            //��ȡ���Ĺ�ע���û�ֻ���� ��ע״̬, subscribe_time
            $data = compact(
                'is_subscribe', 
                'nickname', 
                'sex', 
                'language', 
                'city', 
                'province', 
                'country', 
                'headimgurl', 
                'unionid', 
                'remark', 
                'group_id', 
                'subscribe_time'
            );
            $data = array_map('mysql_escape_string', $data);
            $this->save_user($data, $bind_id, $user_info['openid']);
        }

        $rst['err_code'] = 1;
        $rst['message'] = '���³ɹ�';
        $rst['sync_summary'] = array(
            'count_request'=>count($user_open_id_list), 
            'count_synced'=>$i, 
        );
        return $rst;
    }

    /**
     * @Synopsis ȡ���û��б�
     *
     * @Param $bind_id int �󶨺�
     * @Param $b_select_count boolean ȡ������־
     * @Param $where_str string ���ӵ���������
     * @Param $order_by string ��������
     * @Param $limit string ��ҳ
     * @Param $field string �ֶ�
     *
     * @Returns array �û��б� ��ά����
     */
    public function get_user_list($bind_id, $b_select_count=false, $where_str='', $order_by='id DESC', $limit='0,20', $fields='*')
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return array();
        }

        //�����ѯ����
        $sql_where = "bind_id={$bind_id}";

        if (strlen($where_str) > 0)
        {
            $sql_where .= " AND {$where_str}";
        }

        //��ѯ
        $this->set_weixin_user_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        return $this->findAll($sql_where, $limit, $order_by, $fields);
    }
	
    /**
     * Ⱥ���ӿ�
     * @param int $bind_id
     * @param array $open_id_list
     * @param string $media_id
     * @return array array( 'errcode'=>0, 'errmsg'=>'send job submission success', 'msg_id'=>34182)
     */
    private function wx_mass_send($bind_id, $open_id_list, $media_id)
    {
    	//������
        $bind_id = intval($bind_id);
        $media_id = trim($media_id);
        if( $bind_id<1 || !is_array($open_id_list) || empty($open_id_list) || strlen($media_id)<1 )
        {
            return array();
        }
        
        //�������
        $open_id_count = count($open_id_list);
		if( $open_id_count<$this->message_mass_send_min || $open_id_count>$this->message_mass_send_max )
		{
			return array();
		}
		
        //�ύ
        $token_str = $this->wx_get_access_token($bind_id);
        $url = $this->wx_api_url."cgi-bin/message/mass/send?access_token={$token_str}";
        $data_arr = array(
            'touser' => $open_id_list,
            'mpnews' => array('media_id'=>$media_id),
            'msgtype' => 'mpnews',
        );
        $data_json = json_encode($data_arr);
        $rst_json = $this->http($url, 'POST', $data_json);
        $rst_info = json_decode($rst_json, true);
        if( !is_array($rst_info) ) $rst_info = array();
        return $rst_info;
    }
    
    /**
     * Ⱥ���ӿ�
     * @param int $bind_id
     * @param string $open_id
     * @param string $media_id
     * @return array array( 'errcode'=>0, 'errmsg'=>'send job submission success', 'msg_id'=>0)
     */
    private function wx_mass_send_preview($bind_id, $open_id, $media_id)
    {
    	//������
    	$bind_id = intval($bind_id);
    	$open_id = trim($open_id);
    	$media_id = trim($media_id);
    	if( $bind_id<1 || strlen($open_id)<1 || strlen($media_id)<1 )
    	{
    		return array();
    	}
    	
    	//��ȡ�ز���Ϣ
    	$material_info = $this->get_material_info($bind_id, $media_id);
    	if( empty($material_info) )
    	{
    		return array();
    	}
    	
    	//�ύ
    	$token_str = $this->wx_get_access_token($bind_id);
    	$url = $this->wx_api_url."cgi-bin/message/mass/preview?access_token={$token_str}";
    	$data_arr = array(
    		'touser' => $open_id,
    		'mpnews' => array('media_id'=>$media_id),
    		'msgtype' => 'mpnews',
    	);
    	$data_json = json_encode($data_arr);
    	$rst_json = $this->http($url, 'POST', $data_json);
    	$rst_info = json_decode($rst_json, true);
    	if( !is_array($rst_info) ) $rst_info = array();
    	return $rst_info;
    }
    
    /**
        * @Synopsis Ԥ��Ⱥ����Ϣ
        *
        * @Param $bind_id int ���ںŰ󶨺�
        * @Param $open_id string �û�open_id
        * @Param $media_id string �ز�id
        *
        * @Returns array
     */
    public function mass_send_preview($bind_id, $open_id, $media_id)
    {
        $rst = array('err_code'=>0, 'message'=>'');
        
        //������
        $bind_id = intval($bind_id);
        $open_id = trim($open_id);
        $media_id = trim($media_id);
        if( $bind_id<1 || strlen($open_id)<1 || strlen($media_id)<1 )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '��������';
            return $rst;
        }
		
        //��ȡ�ز���Ϣ
        $material_info = $this->get_material_info($bind_id, $media_id);
        if( empty($material_info) )
        {
            $rst['err_code'] = -2;
            $rst['message'] = '�Ҳ������ز�';
            return $rst;
        }
        
        //�ύ
        $preview_rst = $this->wx_mass_send_preview($bind_id, $open_id, $media_id);
        if( $preview_rst['errcode']!==0 )
        {
        	$rst['err_code'] = -3;
        	$rst['message'] = '����Ԥ��ʧ��';
        	return $rst;
            
        }
        
        $rst['err_code'] = 1;
        $rst['message'] = '����Ԥ���ɹ�';
        return $rst;
    }

    /**
        * @Synopsis Ⱥ����Ϣ
        *
        * @Param $bind_id
        * @Param $where_str string ɸѡ�û�������
        * @Param $media_id string �ز�id
        * @Param $remark ����ע
        *
        * @Returns array
     */
    public function mass_send($mission_info)
    {
        $rst = array('err_code'=>0, 'message'=>'',);
        
        //������
        $mission_id = intval($mission_info['mission_id']);
        $bind_id = intval($mission_info['bind_id']);
        if( !is_array($mission_info) || empty($mission_info) || $mission_id<1 || $bind_id<1 )
        {
        	$rst['err_code'] = -1;
        	$rst['message'] = '��������';
        	return $rst;
        }
        $media_id = trim($mission_info['media_id']);
        $condition_str = trim($mission_info['condition_str']);
        $user_count = intval($mission_info['user_count']);
        $status = intval($mission_info['status']);
        
        //���״̬
        if( $status!=0 )
        {
            $rst['err_code'] = -2;
            $rst['message'] = '����״̬����';
            return $rst;
        }
        
        //������
        $rst = $this->handle_mass_send_mission_status($mission_id);
        if( !$rst )
        {
        	$rst['err_code'] = -3;
        	$rst['message'] = '�޸�����״̬ʧ��';
        	return $rst;
        }
		
        //��ȡ�ز���Ϣ
        $material_info = $this->get_material_info($bind_id, $media_id);
        if( empty($material_info) )
        {
            $more_info = array(
            	'err_msg' => '�ز�Ϊ��',
            );
            $this->fail_mass_send_mission_status($mission_id, $more_info);
            
            $rst['err_code'] = -4;
            $rst['message'] = $more_info['err_msg'];
            return $rst;
        }
		
        //�������
        if( strlen($condition_str)<1 )
        {
        	$more_info = array(
        		'err_msg' => '������������',
        	);
        	$this->fail_mass_send_mission_status($mission_id, $more_info);
        	
            $rst['err_code'] = -5;
            $rst['message'] = $more_info['err_msg'];
            return $rst;
        }
		
        //��ȡ�û��б�
        $user_list = $this->get_user_list($bind_id, false, $condition_str, 'id DESC', '0,99999999', 'open_id');
        $user_list_count = count($user_list);
        if( !is_array($user_list) || $user_list_count<$this->message_mass_send_min )
        {
        	$more_info = array(
        		'err_msg' => 'Ⱥ���û������ܵ���' . $this->message_mass_send_min,
        	);
        	$this->fail_mass_send_mission_status($mission_id, $more_info);
        	
        	$rst['err_code'] = -6;
        	$rst['message'] = $more_info['err_msg'];
        	return $rst;
        }
        
        //��ǿ�ж�
        if( abs($user_list_count-$user_count)>50 )//���ύʱ���û������Ƚϣ�������50�����ϣ�����ʼ����
        {
        	$more_info = array(
        		'err_msg' => 'Ⱥ���û����䶯����',
        	);
        	$this->fail_mass_send_mission_status($mission_id, $more_info);
        	 
        	$rst['err_code'] = -7;
        	$rst['message'] = $more_info['err_msg'];
        	return $rst;
        }
        
        //��Ϊһά������
        $open_id_arr = array_map('array_shift', $user_list);
        unset($user_list);
        $open_id_arr_chunk = array_chunk($open_id_arr, $this->message_mass_send_max);
        
        //����������
		foreach($open_id_arr_chunk as $arr)
        {
        	$data = array(
        		'mission_id' => $mission_id,
				'bind_id' => $bind_id,
        		'status' => 0,
			);
        	$msg_id_tmp = $this->add_mass_send_msg($data);
            foreach($arr as $val)
            {
            	$data = array(
            		'msg_id' => $msg_id_tmp,
            		'mission_id' => $mission_id,
            		'bind_id' => $bind_id,
            		'open_id' => $val,
            	);
            	$this->add_mass_send_user_log($data);
            }
        }
		
        //��ȡ�������б�
        $msg_list = $this->get_mass_send_msg_list($bind_id, false, "mission_id={$mission_id} AND status=0", 'msg_id ASC', '0,99999999');
        foreach($msg_list as $msg_info)
        {
        	//��鸸����״̬
        	$mission_info = $this->get_mass_send_mission_info($mission_id);
        	if( $mission_info['status']!=1 )
        	{
        		break;
        	}
        	$msg_id = intval($msg_info['msg_id']);
        	
        	//������
        	$rst = $this->handle_mass_send_msg_status($msg_id);
        	if( !$rst )
        	{
        		break;
        	}
        	
        	//��ȡ�������û�
        	$user_send_log_list = $this->get_mass_send_user_log_list($bind_id, false, "mission_id={$mission_id} AND msg_id='{$msg_id}'", 'log_id ASC', '0,99999999', 'open_id');
        	$open_id_arr = array_map('array_shift', $user_send_log_list);
        	unset($user_send_log_list);
        	
        	//�ύ
        	//$mass_send_rst = $this->wx_mass_send_preview($bind_id, $open_id_arr[0], $media_id); //Ԥ���ӿ�
        	$mass_send_rst = $this->wx_mass_send($bind_id, $open_id_arr, $media_id); //��ʽ�ӿ�
        	$wx_msg_id = trim($mass_send_rst['msg_id']);
        	$wx_err_code = trim($mass_send_rst['errcode']);
        	$wx_err_msg = trim($mass_send_rst['errmsg']);
        	if( empty($mass_send_rst) )
        	{
        		$wx_err_msg = '�����˿�����';
        	}
        	
        	//����
        	if(  empty($mass_send_rst) || $mass_send_rst['errcode']!==0 )
        	{
        		$more_info = array(
        			'wx_msg_id' => $wx_msg_id,
        			'wx_err_code' => $wx_err_code,
        			'wx_err_msg' => $wx_err_msg,
        		);
        		$this->fail_mass_send_msg_status($msg_id, $more_info);
        		break;
        	}
        	
        	//�ɹ�
        	$more_info = array(
        		'wx_msg_id' => $wx_msg_id,
        		'wx_err_code' => $wx_err_code,
        		'wx_err_msg' => $wx_err_msg,
        	);
        	$this->success_mass_send_msg_status($msg_id, $more_info);
        }
        
        //�������
        $this->success_mass_send_mission_status($mission_id);
        
        $rst['err_code'] = 1;
        $rst['message'] = '�������';
        return $rst;
    }
	
    /**
     * @Synopsis ��ȡ΢�ŵ������ز��б�,
     *
     * @Param $bind_id int �󶨺�
     * @Param $type string �ز�����
     * @Param $offset int ƫ���� ��ʼ
     * @Param $count int ��ҳ����, ���20
     *
     * @Returns array �ز��б�
     */
    private function wx_get_material($bind_id, $type = 'news', $offset = 0, $count = 20)
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return array();
        }
        //��֧����������
        $type_arr = array('image', 'video', 'voice', 'news', );
        $type = trim($type);
        if( !in_array($type, $type_arr) )
        {
            return array();
        }

        $offset = intval($offset);
        $count = intval($count);

        $token_str = $this->wx_get_access_token($bind_id);
        $url = $this->wx_api_url."cgi-bin/material/batchget_material?access_token={$token_str}";
        $data_arr = array(
            'type'=>$type, 
            'offset'=>$offset, 
            'count'=>$count, 
        );
        $data_json = json_encode($data_arr);

        $ret_json = $this->http($url, 'POST', $data_json);
        $ret_info = json_decode($ret_json, true);   
        if( !isset($ret_info['item_count']) )//���󲻳ɹ�
        {
            return array();
        }

        $item_list = $ret_info['item'];
        $count_item = $ret_info['item_count'];
        unset($ret_info);

        if( $count_item==20 )
        {
            return array_merge($item_list, $this->wx_get_material($bind_id, $type, $offset+20));
        }

        return $item_list;
    }

    /**
     * @Synopsis ͬ���ز�
     *
     * @Param $bind_id int �󶨺�
     * @Param $type string ����
     *
     * @Returns array ����ʾ�ķ��ؽ��
     */
    public function sync_material($bind_id, $type = 'news')
    {
        $rst = array('err_code'=>0, 'message'=>'', 'quantity'=>0, );

        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '��������';
            return $rst;
        }

        $material_list = $this->wx_get_material($bind_id, $type);
        if( empty($material_list) )
        {
            $rst['err_code'] = -2;
            $rst['message'] = '�޷�ȡ���ز�';
            return $rst;
        }

        //�ȸ�������Ϊ��ɾ��
        $e_row = $this->update_material($bind_id, null, array('is_delete'=>0), "bind_id={$bind_id} AND is_delete=0 ");

        foreach( $material_list as $material_info )
        {
            $data = array();
            $data['bind_id'] = $bind_id;
            $data['media_id'] = $material_info['media_id'];
            $data['update_time'] = $material_info['update_time'];
            $data['material_type'] = $type;
            $data['is_delete'] = 0;

            $content_tmp = array();

            if( isset($material_info['content']) )
            {
                $content_tmp = $material_info['content']['news_item'];
                //ȥ��html �ı���Ӱ��jsondecode
                foreach( $content_tmp as &$val)
                {
                    unset($val['content']);
                }

                //��ͼ���ز�����Ϊm_news
                if( count($content_tmp)>1 )
                {
                    $data['material_type'] = 'm_news';
                }
            }
            elseif( isset($material_info['name'])&&isset($material_info['url']) )
            {
                //ͼƬ, ����, ��Ƶ�ز�, ��¼�����ƺ�Ԥ����ַ
                $content_tmp = array(
                    'name'=>$material_info['name'],
                    'url'=>$material_info['url'],
                );
            }

            $data['item_content'] = json_encode($content_tmp);
            $this->add_material($data);
        }

        $rst['err_code'] = 1;
        $rst['message'] = 'ͬ���ɹ�';
        $rst['quantity'] = count($material_list);
        return $rst;
    }

    /**
     * @Synopsis ����ز�
     *
     * @Param $material_info array �ز�
     *
     * material_id    �ڲ�ID                                                                                                       
     * bind_id        �󶨺�                                                                                                      
     * media_id       ΢�ŵ���ԴID                                                                                              
     * title          �زĵ�����,��Щ���Ϳ���û������                                                                 
     * digest         �ز�ժҪ,��Щ���Ϳ���û��                                                                          
     * url            Ԥ����ַ                                                                                                   
     * update_time    �޸�ʱ��,ָ������΢�Ź��ں�̨�����һ�α༭ʱ��                                         
     * material_type  ����,��ͼ���ز�(m_news),ͼ���ز�(news),ͼƬ�ز�(image),��Ƶ�ز�(video),�����ز�(voice)  
     *
     * @Returns int 
     */
    private function add_material($material_info)
    {
        if(!is_array($material_info)||count($material_info)<1)
        {
            return 0;
        }
        $material_info['add_time'] = time();

        $this->set_weixin_material_tbl();
        return $this->insert($material_info, 'REPLACE');
    }

    /**
     * ��ȡ�ز��б�
     * @param int $bind_id
     * @param boolean $b_select_count
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_material_list($bind_id, $b_select_count=false, $where_str='', $order_by='update_time DESC, material_id ASC', $limit='0,20', $fields='*')
    {
        $bind_id = intval($bind_id);

        //�����ѯ����
        $sql_where = '';

        if( $bind_id>0 )
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= "bind_id={$bind_id}";
        }

        if (strlen($where_str) > 0)
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= $where_str;
        }

        //��ѯ
        $this->set_weixin_material_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }

    /**
        * @Synopsis ��ȡ�ز���Ϣ
        *
        * @Param $bind_id int �󶨺�
        * @Param $media_id string ��ԴID
        *
        * @Returns array
     */
	public function get_material_info($bind_id, $media_id)
    {
        $bind_id = intval($bind_id);
        $media_id = trim($media_id);
        if( $bind_id<1 || strlen($media_id)<1 )
        {
            return array();
        }
        $where_str = "bind_id={$bind_id} AND media_id=:x_media_id";
        sqlSetParam($where_str, 'x_media_id', $media_id);
        $this->set_weixin_material_tbl();
        return $this->find($where_str);
    }

    /**
        * @Synopsis �����ز�
        *
        * @Param $bind_id
        * @Param $material_id
        * @Param $data
        * @Param $append_where
        *
        * @Returns 
     */
    public function update_material($bind_id, $material_id=null, $data, $append_where = '')
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return 0;
        }

        if( !is_array($data) || count($data)<1 )
        {
            return 0;
        }

        $where_str = "bind_id={$bind_id}";

        $material_id = intval($material_id);
        if( $material_id>0 )
        {

            $where_str .=" AND material_id='{$material_id}'";
        }

        if( strlen($append_where)>0 )
        {
            $where_str .=" AND {$append_where}";
        }

        $this->set_weixin_material_tbl();
        return $this->update($data, $where_str);
    }

    /**
     * @Synopsis ����Ⱥ������
     *
     * @Param $data array 
     * array(
     *       mass_send_id                                                                                                                                                                                                                                                                                                         
     *       bind_id     ���ںŰ󶨺�                                                                                                                                                                                                                                                                                       
     *       mission_id  Ⱥ������id, һ��mission_id�������msg_id                                                                                                                                                                                                                                                                                           
     *       media_id    ��Դid                                                                                                                                                                                                                                                                                                 
     *       condition_str   �˴����͵��������                                                                                                                                                                                                                                                                                                
     *       mission_status      Ⱥ��������ύ״̬, ȫ���ɹ�2, ���ֳɹ�1, �ύ״̬0 
     *       mission_desc     ��������
     *       send_status      Ⱥ������ķ���״̬, ȫ���ɹ�2, ���ֳɹ�1, �ύ״̬0 
     *       err_code  �ύ����ʱ�Ĵ������
     *       message �ύ�����ǵĴ�����Ϣ
     *       add_time    ���ʱ��
     *       remark
     *       );
     *
     * @Returns int
     */
    public function add_mass_send_mission($data)
    {
        if( !is_array($data) || empty($data) )
        {
            return 0;
        }
        $data['add_time'] = time();
        $this->set_weixin_mass_send_mission_tbl(); 
        return $this->insert($data, 'IGNORE');
    }

    /**
     * @Synopsis ����Ⱥ������
     *
     * @Param $mission_id string ����ID
     * @Param $data array
     *
     * @Returns int
     */
    public function update_mass_send_mission($mission_id, $data)
    {
        $mission_id = intval($mission_id);
        if( $mission_id<1 || !is_array($data) || count($data)<1 )
        {
            return 0;
        }
        $where_str = "mission_id='{$mission_id}'";
        $this->set_weixin_mass_send_mission_tbl();
        return $this->update($data, $where_str);
    }
    
    /**
     * ����
     * @param int $mission_id
     * @param array $more_info
     * @return bool
     */
    private function handle_mass_send_mission_status($mission_id, $more_info=array())
    {
    	$mission_id = intval($mission_id);
    	if( $mission_id<1 )
    	{
    		return false;
    	}
    	if( !is_array($more_info) ) $more_info = array();
    	$data = array(
    		'status' => 1,
    		'handle_time' => time(),
    	);
    	$data = array_merge($more_info, $data);
    	$this->set_weixin_mass_send_mission_tbl();
    	$rst = $this->update($data, "mission_id={$mission_id} AND status=0");
    	return $rst>0?true:false;
    }
    
    /**
     * ����ʧ��
     * @param int $mission_id
     * @param array $more_info
     * @return bool
     */
    private function fail_mass_send_mission_status($mission_id, $more_info=array())
    {
    	$mission_id = intval($mission_id);
    	if( $mission_id<1 )
    	{
    		return false;
    	}
    	if( !is_array($more_info) ) $more_info = array();
    	$data = array(
    		'status' => 7,
    		'fail_time' => time(),
    	);
    	$data = array_merge($more_info, $data);
    	$this->set_weixin_mass_send_mission_tbl();
    	$rst = $this->update($data, "mission_id={$mission_id} AND status=1");
    	return $rst>0?true:false;
    }
	
    /**
     * ����ɹ�
     * @param int $mission_id
     * @param array $more_info
     * @return bool
     */
    private function success_mass_send_mission_status($mission_id, $more_info=array())
    {
    	$mission_id = intval($mission_id);
    	if( $mission_id<1 )
    	{
    		return false;
    	}
    	if( !is_array($more_info) ) $more_info = array();
    	$data = array(
    		'status' => 8,
    		'success_time' => time(),
    	);
    	$data = array_merge($more_info, $data);
    	$this->set_weixin_mass_send_mission_tbl();
    	$rst = $this->update($data, "mission_id={$mission_id} AND status=1");
    	return $rst>0?true:false;
    }

    /**
     * ����ȡ�� 
     * @param int $mission_id
     * @param array $more_info
     * @return bool
     */
    private function cancel_mass_send_mission_status($mission_id, $more_info=array())
    {
        $mission_id = intval($mission_id);
        if( $mission_id<1 )
        {
            return false;
        }
        if( !is_array($more_info) ) $more_info = array();
        $data = array(
            'status' => 2,
    		//'handle_time' => time(),
        );
        $data = array_merge($more_info, $data);
        $this->set_weixin_mass_send_mission_tbl();
        //������ʹ����е��������ȡ��
        $rst = $this->update($data, "mission_id={$mission_id} AND status IN (0,1)");
        return $rst>0?true:false;
    }

    
    /**
     * ȡ������ 
     * @param int $mission_id
     * @param array $more_info
     * @return bool
     */
    public function cancel_mass_send_mission($mission_id, $more_info = array())
    {
        return $this->cancel_mass_send_mission_status($mission_id, $more_info);
    }
    
    /**
        * @Synopsis ��ȡ��������
        *
        * @Param $mission_id int
        *
        * @Returns array
     */
	public function get_mass_send_mission_info($mission_id)
    {
        $mission_id = intval($mission_id);
        if( $mission_id<1 )
        {
            return array();
        }
        $this->set_weixin_mass_send_mission_tbl();
        return $this->find("mission_id={$mission_id}");
    }		


    /**
        * @Synopsis ��ȡ�����б�
        *
        * @Param $bind_id
        * @Param $b_select_count
        * @Param $where_str
        * @Param $order_by
        * @Param $limit
        * @Param 20''
        * @Param ''
        *
        * @Returns array|int
     */
    public function get_mass_send_mission_list($bind_id=0, $b_select_count=false, $where_str='', $order_by='mission_id ASC', $limit='0,20', $fields='*')
    {
        $bind_id = intval($bind_id);

        //�����ѯ����
        $sql_where = '';

        if( $bind_id>0 )
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= "bind_id={$bind_id}";
        }

        if (strlen($where_str) > 0)
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= $where_str;
        }

        //��ѯ
        $this->set_weixin_mass_send_mission_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }

    /**
        * @Synopsis ���Ⱥ����Ϣ
        *
        * @Param $data array 
        *
        * msg_id ΢�ŷ��ص�msg_id
        * mission_id Ⱥ������ID
        * send_status ΢�����͵�״̬
        * total_count ��Ⱥ����Ϣ�ķ����û�����
        * err_count   δ�ܳɹ��ʹ���û�����
        * sent_count  �ɹ��ʹ���û�����
        * @Returns 
     */
    public function add_mass_send_msg($data)
    {
        if( !is_array($data) || empty($data) )
        {
            return 0;
        }
        $data['add_time'] = time();
        $this->set_weixin_mass_send_msg_tbl(); 
        return $this->insert($data, 'IGNORE');
    }

    /**
     * @Synopsis ����Ⱥ����Ϣ
     *
     * @Param $data array
     * @Param $bind_id int �󶨺�
     * @Param $wx_msg_id string Ⱥ����Ϣ��
     * 
     *
     * @Returns int
     */
    public function update_mass_send_msg_by_wx($data, $bind_id, $wx_msg_id)
    {
        $bind_id = intval($bind_id);
        $wx_msg_id = trim($wx_msg_id);
        if( !is_array($data) || count($data)<1 || $bind_id<1 || strlen($wx_msg_id)<1 )
        {
            return 0;
        }
        $where_str = "bind_id={$bind_id} AND wx_msg_id=:x_wx_msg_id";
        sqlSetParam($where_str, 'x_wx_msg_id', $wx_msg_id);
        $this->set_weixin_mass_send_msg_tbl();
        return $this->update($data, $where_str);
    }
	
    /**
     * ����
     * @param int $msg_id
     * @param array $more_info
     * @return bool
     */
    private function handle_mass_send_msg_status($msg_id, $more_info=array())
    {
    	$msg_id = intval($msg_id);
    	if( $msg_id<1 )
    	{
    		return false;
    	}
    	if( !is_array($more_info) ) $more_info = array();
    	$data = array(
    		'status' => 1,
    		'handle_time' => time(),
    	);
    	$data = array_merge($more_info, $data);
    	$this->set_weixin_mass_send_msg_tbl();
    	$rst = $this->update($data, "msg_id={$msg_id} AND status=0");
    	return $rst>0?true:false;
    }
    
    /**
     * ����ʧ��
     * @param int $msg_id
     * @param array $more_info
     * @return bool
     */
    private function fail_mass_send_msg_status($msg_id, $more_info=array())
    {
    	$msg_id = intval($msg_id);
    	if( $msg_id<1 )
    	{
    		return false;
    	}
    	if( !is_array($more_info) ) $more_info = array();
    	$data = array(
    		'status' => 7,
    		'fail_time' => time(),
    	);
    	$data = array_merge($more_info, $data);
    	$this->set_weixin_mass_send_msg_tbl();
    	$rst = $this->update($data, "msg_id={$msg_id} AND status=1");
    	return $rst>0?true:false;
    }
    
    /**
     * ����ɹ�
     * @param int $msg_id
     * @param array $more_info
     * @return bool
     */
    private function success_mass_send_msg_status($msg_id, $more_info=array())
    {
    	$msg_id = intval($msg_id);
    	if( $msg_id<1 )
    	{
    		return false;
    	}
    	if( !is_array($more_info) ) $more_info = array();
    	$data = array(
    		'status' => 8,
    		'success_time' => time(),
    	);
    	$data = array_merge($more_info, $data);
    	$this->set_weixin_mass_send_msg_tbl();
    	$rst = $this->update($data, "msg_id={$msg_id} AND status=1");
    	return $rst>0?true:false;
    }
    
    /**
        * @Synopsis ȡ��Ⱥ����Ϣ����
        *
        * @Param $bind_id
        * @Param $msg_id
        * @Param $append_where string ���ӵ���������
        *
        * @Returns 
     */
	public function get_mass_send_msg_info($bind_id, $msg_id = 0, $append_where)
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return array();
        }
        $where_str = "bind_id={$bind_id}";
        $msg_id = intval($msg_id);
        if( $msg_id>0 )
        {
         $where_str .= " AND msg_id='{$msg_id}'";
        }

        if( strlen($append_where)>0 )
        {
            $where_str .= " AND {$append_where}";
        }

        $this->set_weixin_mass_send_msg_tbl();
        $ret = $this->find( $where_str );
        return $ret;
    }		

    /**
        * @Synopsis ��ȡȺ����Ϣ�б�
        *
        * @Param $bind_id
        * @Param $b_select_count
        * @Param $where_str
        * @Param $order_by
        * @Param $limit
        * @Param 20''
        * @Param ''
        *
        * @Returns 
     */
    public function get_mass_send_msg_list($bind_id, $b_select_count=false, $where_str='', $order_by='msg_id ASC', $limit='0,20', $fields='*')
    {
        $bind_id = intval($bind_id);

        //�����ѯ����
        $sql_where = '';

        if( $bind_id>0 )
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= "bind_id={$bind_id}";
        }

        if (strlen($where_str) > 0)
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= $where_str;
        }

        //��ѯ
        $this->set_weixin_mass_send_msg_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }

    /**
        * @Synopsis �����û�Ⱥ����¼
        *
        * @Param $data array
        * 'bind_id'    ���ں�
        * 'msg_id'     Ⱥ����Ϣid
        * 'open_id'    �û�open_id
        * 'mission_id' Ⱥ������id
        *
        * @Returns 
     */
    public function add_mass_send_user_log($data)
    {
        if( !is_array($data) || empty($data) )
        {
            return 0;
        }
        $data['add_time'] = time();
        $this->set_weixin_mass_send_user_log_tbl(); 
        return $this->insert($data, 'IGNORE');
    }

    /**
        * @Synopsis ��ѯ�û���Ⱥ����¼�б�
        *
        * @Param $bind_id
        * @Param $b_select_count
        * @Param $where_str
        * @Param $order_by
        * @Param $limit
        * @Param 20''
        * @Param ''
        *
        * @Returns array|int
     */
    public function get_mass_send_user_log_list($bind_id, $b_select_count=false, $where_str='', $order_by='log_id ASC', $limit='0,20', $fields='*')
    {
        $bind_id = intval($bind_id);

        //�����ѯ����
        $sql_where = '';

        if( $bind_id>0 )
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= "bind_id={$bind_id}";
        }

        if (strlen($where_str) > 0)
        {
            if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
            $sql_where .= $where_str;
        }

        //��ѯ
        $this->set_weixin_mass_send_user_log_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }

    /**
        * @Synopsis �����û����ͼ�¼
        *
        * @Param $bind_id
        * @Param $log_id
        * @Param $data
        * @Param $append_where
        *
        * @Returns 
     */
    public function update_mass_send_user_log($bind_id, $log_id=0, $data, $append_where = '')
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return 0;
        }

        $log_id = intval($log_id);
        if($log_id<1)
        {
            return 0;
        }

        if( !is_array($data) || count($data)<1 )
        {
            return 0;
        }

        $where_str = "bind_id={$bind_id}";
        if($log_id>0)
        {
            $where_str .= " AND log_id='{$log_id}'";
        }
        if( strlen($append_where)>0 )
        {
            $where_str .= " AND {$append_where}";
        }

        $this->set_weixin_mass_send_user_log_tbl(); 
        return $this->update($data, $where_str);
    }
    
    /**
        * @Synopsis ƴ��������������������Ϣ
        *
        * @Param $data
        *
        * @Returns 
     */
    public function create_mission_condition_str($data)
    {
        $rst = array('condition_str'=>'', 'mission_desc'=>'',);
        if( !is_array($data)||empty($data) )
        {
            return $rst;
        }

        $where = 'is_subscribe=1';
        $mission_desc = '������Ⱥ���Ķ���Ϊ��';

        $sex_enum = array('1'=>'��', '2'=>'Ů',);

        $temp_arr = array();
        $sex = intval($data['sex']);
        if( $sex>0 )
        {
            $mission_desc .= ' �Ա�Ϊ '.$sex_enum[$sex];
            $temp_arr[] = $sex;
        }
        $no_sex = intval($data['no_sex']);
        if( $no_sex>0 )
        {
            $mission_desc .= ' ���Ա�δ֪';
            $temp_arr[] = 0;
        }
        if( !empty($temp_arr) )
        {
            $mission_desc .= "��";
            $temp_str = implode(',', $temp_arr);
            $where .= " AND sex in ({$temp_str})";
        }

        $temp_arr = array();
        $country = trim($data['country']);
        if( strlen($country)>0 )
        {
            $mission_desc .= ' ����Ϊ '.$country;
            $temp_arr[] = "'".$country."'";
        }
        $no_country = intval($data['no_country']);
        if( $no_country>0 )
        {
            $mission_desc .= ' �������ϢΪ��';
            $temp_arr[] = '\'\'';
        }
        if( !empty($temp_arr) )
        {
            $mission_desc .= "��";
            $temp_str = implode(',', $temp_arr);
            $where .= " AND country in ({$temp_str})";
        }

        $temp_arr = array();
        $province = trim($data['province']);
        if( strlen($province)>0 )
        {
            $mission_desc .= ' ʡ��Ϊ '.$province;
            $temp_arr[] = "'".$province."'";
        }
        $no_province = intval($data['no_province']);
        if( $no_province>0 )
        {
            $mission_desc .= ' ��ʡ����ϢΪ��';
            $temp_arr[] = '\'\'';
        }
        if( !empty($temp_arr) )
        {
            $mission_desc .= "��";
            $temp_str = implode(',', $temp_arr);
            $where .= " AND province in ({$temp_str})";
        }

        $temp_arr = array();
        $city = trim($data['city']);
        if( strlen($city)>0 )
        {
            $mission_desc .= ' ����Ϊ '.$city;
            $temp_arr[] = "'".$city."'";
        }
        $no_city = intval($data['no_city']);
        if( $no_city>0 )
        {
            $mission_desc .= ' �������ϢΪ��';
            $temp_arr[] = '\'\'';
        }
        if( !empty($temp_arr) )
        {
            $mission_desc .= "��";
            $temp_str = implode(',', $temp_arr);
            $where .= " AND city in ({$temp_str})";
        }

        $min_time = trim($data['min_time']);
        if( strlen($min_time)>0 )
        {
            $mission_desc .= " ��עʱ���� {$min_time} ��ʼ֮��";
            $min_time = strtotime($min_time);
            $where .= " AND subscribe_time>={$min_time}";
        }
        $max_time = trim($data['max_time']);
        if( strlen($max_time)>0 )
        {
            $mission_desc .= " ��עʱ���� {$max_time} ����֮ǰ��";
            $max_time = strtotime($max_time)+86400-1;
            $where .= " AND subscribe_time<={$max_time}";
        }
        $rst['condition_str'] = $where;
        $rst['mission_desc'] = "{$mission_desc}��";
        return $rst;
    }
}
