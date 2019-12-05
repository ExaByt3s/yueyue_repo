<?php
/**
 * 微信公众平台开发者接口
 * 
 * @author Henry
 * @copyright 2015-01-05
 */

class pai_weixin_helper_class extends POCO_TDG
{
	/**
	 * 消息接收表的字段
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
	 * 消息回复表的字段
	 * @var array
	 */
	private $reply_tbl_fields = array(
		'reply_id','bind_id','receive_id','ToUserName','FromUserName',
		'CreateTime','MsgType','Content','MediaId','Title',
		'Description','MusicUrl','HQMusicUrl','ThumbMediaId','ArticleCount',
		'FuncFlag','Articles','add_time',
	);
	
	/**
	 * 客服消息表的字段
	 * @var array
	 */
	private $message_custom_tbl_fields = array(
		'id', 'bind_id', 'touser', 'msgtype', 'content',
		'errcode', 'errmsg', 'add_time',
	);
	
	/**
	 * 模板消息表的字段
	 * @var array
	 */
	private $message_template_tbl_fields = array(
		'id', 'bind_id', 'touser', 'template_id','url',
		'topcolor', 'data', 'errcode', 'errmsg', 'msgid', 'status', 'add_time',
	);
	
	/**
	 * 微信接口地址
	 * @var string
	 */
	private $wx_api_url = 'https://101.226.90.58/';
	//private $wx_api_url = 'https://api.weixin.qq.com/';
	
	/*
	*场景二维码类型
	×QR_SCENE 为临时,
	*QR_LIMIT_SCENE 为永久,
	*QR_LIMIT_STR_SCENE 为永久字符串
	*@var array
	*/
	private	$qr_type = array(
		'QR_SCENE',
		'QR_LIMIT_SCENE',
		'QR_LIMIT_STR_SCENE',
	);
	
	/**
	 * 群发最小用户数
	 * @var int
	 */
	private $message_mass_send_min = 2;
	
	/**
	 * 群发最大用户数
	 * @var int
	 */
	//private $message_mass_send_max = 10000;
	private $message_mass_send_max = 3;
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_weixin_db');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_bind_tbl()
	{
		$this->setTableName('weixin_bind_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_receive_tbl()
	{
		$this->setTableName('weixin_receive_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_reply_tbl()
	{
		$this->setTableName('weixin_reply_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_message_custom_tbl()
	{
		$this->setTableName('weixin_message_custom_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_message_template_tbl()
	{
		$this->setTableName('weixin_message_template_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_cmd_tbl()
	{
		$this->setTableName('weixin_cmd_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_user_tbl()
	{
		$this->setTableName('weixin_user_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_material_tbl()
	{
		$this->setTableName('weixin_material_tbl');
	}

	/**
	 * 指定表
	 */
	private function set_weixin_mass_send_mission_tbl()
	{
		$this->setTableName('weixin_mass_send_mission_tbl');
	}

	/**
	 * 指定表
	 */
	private function set_weixin_mass_send_msg_tbl()
	{
		$this->setTableName('weixin_mass_send_msg_tbl');
	}

	/**
	 * 指定表
	 */
	private function set_weixin_mass_send_user_log_tbl()
	{
		$this->setTableName('weixin_mass_send_user_log_tbl');
	}

	/**
	 * 指定表
	 */
	private function set_weixin_user_mode_tbl()
	{
		$this->setTableName('weixin_user_mode_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_menu_tbl()
	{
		$this->setTableName('weixin_menu_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_news_tbl()
	{
		$this->setTableName('weixin_news_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_weixin_cache_tbl()
	{
		$this->setTableName('weixin_cache_tbl');
	}
	
	/**
	 * 发出HTTP请求
	 * @param string $url
	 * @param string $method GET|POST
	 * @param string|array $postfields 这个参数可以通过urlencoded后的字符串类似'para1=val1&para2=val2&...'或使用一个以字段名为键值，字段数据为值的数组。如果value是一个数组，Content-Type头将会被设置成multipart/form-data。
	 * @param array $headers 例如 array('Content-type: text/plain', 'Content-length: 100')
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
	 * GBK转成UTF-8
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
	 * UTF-8转成GBK
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
	 * 只保留指定的字段
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
	 * 获取公众号绑定信息
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
	 * 获取公众号绑定信息，根据app_id
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
     * 取数据列表
     *
     * @param string $search_arr    查询条件数组 array('app_id'=>'')
     * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit        查询条数
     * @param string $order_by     排序条件
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
	 * 添加
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
	 * 修改
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
	 * 获取最近一次关注
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
	 * 添加
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
	 * 添加
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
	 * 修改
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
	 * 添加
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
	 * 修改
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
	 * 修改
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
	 * 添加cmd
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
	 * 通过ID删除
	 * 
	 * @param int    $cmd_id
	 * @return bool 
	 */
    public function del_cmd($cmd_id)
    {
        $cmd_id = (int)$cmd_id;
		if (empty($cmd_id)) 
		{
			trace("非法参数 cmd_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_weixin_cmd_tbl();
		$where_str = " cmd_id={$cmd_id} ";
		return $this->delete($where_str);
 
    }

	/**
	 * 更新
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
	 * 获取信息 
	 *
	 * @param int $cmd_id
	 * @return bool
	 */
	public function get_cmd_info($cmd_id)
	{
		$cmd_id = ( int ) $cmd_id;
		if (empty($cmd_id)) 
		{
			trace("非法参数 cmd_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_weixin_cmd_tbl();
		$ret = $this->find ( "cmd_id={$cmd_id}" );
		return $ret;
	}

	/**
	 * 获取信息 
	 *
	 * @param int $cmd_type
	 * @return bool
	 */
	public function get_cmd_info_by_type($cmd_type,$bind_id)
	{
		if (empty($cmd_type)) 
		{
			trace("非法参数 cmd_type 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_weixin_cmd_tbl();
		$ret = $this->find ( "cmd_type='{$cmd_type}' AND bind_id={$bind_id}" );
		return $ret;
	}

	/**
	 * 获取列表
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
		
		//整理查询条件
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
		
		//查询
		$this->set_weixin_cmd_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		$list = $this->findAll($sql_where, $limit, $order_by, $fields);
		return $list;
	}

		/**
	 * 获取用户信息
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
		
		//获取
		$this->set_weixin_user_tbl();
		$info = $this->find($where_str);
		
		return $info;
	}
	
	/**
	 * 保存用户信息
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
	 * 获取用户模式
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
		
		//获取
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
			//未开始
			return '';
		}
		if( $end_time>0 && $end_time<$cur_time )
		{
			//已结束
			return '';
		}
		return trim($info['mode_code']);
	}
	
	/**
	 * 保存用户模式
	 * @param int $bind_id
	 * @param string $open_id
	 * @param string $mode_code 空表示清空模式
	 * @param int $seconds 0不设置过期
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
	 * 写入数据(一维数组形式)
	 *
	 * @param array $data
	 * @return bool
	 */
	public function add_menu($data,$insert_type='IGNORE')
	{

		if ( empty( $data ) ) 
		{
			trace("非法参数 data 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
			return false;
		}
		$this->set_weixin_menu_tbl();
		$ret = $this->insert($data, $insert_type);
		return $ret;

	}

    /**
	 * 通过ID删除
	 * 
	 * @param int    $menu_id
	 * @return bool 
	 */
    public function del_menu($menu_id)
    {
        $menu_id = (int)$menu_id;
		if (empty($menu_id)) 
		{
			trace("非法参数 menu_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_weixin_menu_tbl();
		$where_str = " menu_id={$menu_id} ";
		$ret = $this->delete($where_str);
		if($ret){
			//删除子类
			 $where_str = " parent_id={$menu_id} ";
			 $ret1 		= $this->delete($where_str);

		}
		return $ret; 
 
    }

	 /**
	 * 更新菜单
	 *
	 * @param array  $data    更新的数据
	 * @param int 	 $menu_id
	 * @return bool
	 */
	public function update_menu($data,$menu_id)
	{
		if (empty($data)) 
		{
			trace("非法参数 data 不能为空",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}

        $menu_id = (int)$menu_id;
		if (empty($menu_id)) 
		{
			trace("非法参数 menu_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_weixin_menu_tbl();
		$where_str = " menu_id = {$menu_id}";
		return $this->update($data, $where_str);
	}

	/**
	 * 同步更新关键字表  
	 *
	 * @param  int $bind_id
	 * @return int
	 */
	public function menu_sync_cmd($bind_id){

		$bind_id = ( int ) $bind_id;
		if (empty($bind_id)) 
		{
			trace("非法参数 bind_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
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
		//菜单有数据 cmd表也有  获取需要更新的menu_id
		$need_insert = array_diff($menu_id_arr,$cmd_rid_arr);
		//菜单有数据 cmd表没    需要将缺少的数据插入  
		$affect_rows = 0;
		if( !empty($need_update) ){

			foreach ( $need_update as $k => $menu_id ) {

				$cmd_data  = array();
				$menu_info = $menu_list[$menu_id];  //获取需要同步更新的菜单
				$cmd_id    = $cmd_id_arr[$menu_id]; //获取所关联的cmd_id
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
		//cmd有  菜单没有  说明菜单已被删除、或更改了非click 类型
		if( !empty($need_del_menu) ){

			foreach ($need_del_menu as $k => $menu_id) {

				$cmd_id = $cmd_id_arr[$menu_id];
				$this->del_cmd($cmd_id);

			}

		}
		return true;
	}	

	/**
	 * 获取菜单信息 
	 *
	 * @param int $menu_id
	 * @return array
	 */
	public function get_menu_info($menu_id)
	{
		$menu_id = ( int ) $menu_id;
		if (empty($menu_id)) 
		{
			trace("非法参数 menu_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_weixin_menu_tbl();
		$ret = $this->find ( "menu_id={$menu_id}" );
		return $ret;
	}

	/**
	 * 获取菜单树形列表
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
	 * 获取列表
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
		
		//整理查询条件
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
		
		//查询
		$this->set_weixin_menu_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		$list = $this->findAll($sql_where, $limit, $order_by, $fields);
		return $list;
	}

	/**
	 * 获取绑定信息 
	 *
	 * @param int $news_id
	 * @return array
	 */
	public function get_news_info($news_id)
	{
		$news_id = ( int ) $news_id;
		if (empty($news_id)) 
		{
			trace("非法参数 news_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
		}
		$this->set_weixin_news_tbl();
		$ret = $this->find ( "news_id={$news_id}" );
		return $ret;
	}

	/**
	 * 添加
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
	 * 更新
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
	 * 删除
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
	 * 获取列表
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
		
		//整理查询条件
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
		
		//查询
		$this->set_weixin_news_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		$list = $this->findAll($sql_where, $limit, $order_by, $fields);
		return $list;
	}
	
	/**
	 * 设置缓存
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
		
		//保存db
		$info = array(
			'cache_key' => $cache_key,
			'cache_data' => serialize($cache_data),
			'life_time' => $life_time,
			'cache_time' => time(),
		);
		$this->set_weixin_cache_tbl();
		$this->insert($info, 'REPLACE');
		
		//保存cache
		POCO::setCache($cache_key, $info, array('life_time'=>31536000));
		
		return true;
	}
	
	/**
	 * 获取缓存
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
		
		//获取cache
		$info = POCO::getCache($cache_key);
		if( empty($info) )
		{
			//获取db
			$where_str = "cache_key=:x_cache_key";
			sqlSetParam($where_str, 'x_cache_key', $cache_key);
			$this->set_weixin_cache_tbl();
			$info = $this->find($where_str);
			if( empty($info) )
			{
				return null;
			}
			
			//保存cache
			POCO::setCache($cache_key, $info, array('life_time'=>31536000));
		}
		
		//判断过期
		$life_time = intval($info['life_time']);
		$cache_time = intval($info['cache_time']);
		if( $life_time>0 && ($cache_time+$life_time)<=time() )
		{
			return null;
		}
		
		//整理数据
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
	 * 删除缓存
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
		
		//删除db
		$where_str = "cache_key=:x_cache_key";
		sqlSetParam($where_str, 'x_cache_key', $cache_key);
		$this->set_weixin_cache_tbl();
		$this->delete($where_str);
		
		//删除cache
		POCO::deleteCache($cache_key);
		
		return true;
	}
	
	/**
	 * 检查签名
	 * @param string $token 密钥
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机数
	 * @param string $signature 微信加密签名
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
	 * 获取access_token
	 * @param $bind_id
	 * @param bool $b_cache
	 * @return string
	 */
	public function wx_get_access_token($bind_id, $b_cache=true)
	{
		$access_token = '';
		
		//检查参数
		$bind_id = intval($bind_id);
		if( $bind_id<1 )
		{
			return $access_token;
		}
		
		//获取cache
		$cache_key = 'weixin_helper_access_token_'.  $bind_id;
		if( $b_cache )
		{
			$cache_data = $this->get_cache($cache_key);//POCO::getCache($cache_key);
			$access_token = trim($cache_data['access_token']);
		}
		
		//临时日志
		$log_arr = array(
			'access_token' => $access_token,
		);
		pai_log_class::add_log($log_arr, 'wx_get_access_token');
		
		//发出请求
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
			
			//保存cache
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
	 * 获取网页授权链接
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
	 * 获取网页授权信息
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
	 * 获取JS API Ticket
	 * @param int $bind_id
	 * @param boolean $b_cache
	 * @return string
	 */
	private function wx_get_js_api_ticket($bind_id, $b_cache=true)
	{		
		$ticket = '';
		
		//检查参数
		$bind_id = intval($bind_id);
		if( $bind_id<1 )
		{
			return $ticket;
		}
		
		//获取cache
		$cache_key = 'weixin_helper_js_api_ticket_'.  $bind_id;
		if( $b_cache )
		{
			$cache_data = $this->get_cache($cache_key);//POCO::getCache($cache_key);
			$ticket = trim($cache_data['ticket']);
		}
		
		//临时日志
		$log_arr = array(
			'ticket' => $ticket,
		);
		pai_log_class::add_log($log_arr, 'wx_get_js_api_ticket');
		
		//发出请求
		if( strlen($ticket)<1 )
		{
			$access_token = $this->wx_get_access_token($bind_id);
			$url = $this->wx_api_url . "cgi-bin/ticket/getticket?type=jsapi&access_token={$access_token}";
			$ret_json = $this->http($url, 'GET');
			$ret_info = json_decode($ret_json, true);
			if( !is_array($ret_info) ) $ret_info = array();
			$ticket = trim($ret_info['ticket']);
			
			//保存cache
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
	 * 获取JS API签名数据
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
		
		//这里参数的顺序要按照 key 值 ASCII 码升序排序
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
	 * 发布自定义菜单
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
	 * 发送客服消息
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
		
		//新增记录
		$data = $message_data;
		$data['bind_id'] = $bind_id;
		$data['add_time'] = time();
		$id = $this->add_message_custom($data);
		
		//TODO 目前仅支持文本
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
		
		//更新记录
		$data = array(
			'errcode' => trim($ret_info['errcode']),
			'errmsg' => trim($ret_info['errmsg']),
		);
		$this->update_message_custom($data, $id);
		
		return $ret;
	}
	
	/**
	 * 发送模板消息
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
		
		//新增记录
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
		
		//更新记录
		$info = array(
			'errcode' => trim($ret_info['errcode']),
			'errmsg' => trim($ret_info['errmsg']),
			'msgid' => trim($ret_info['msgid']),
		);
		$this->update_message_template($info, $id);
		
		return $ret;
	}
	
	/**
	 * 将消息推送的xml转成数组
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
	 * 将数组转成消息回复的xml
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
	 * 获取回复消息，根据推送数据
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array
	 */
	public function wx_get_reply_by_push($bind_id, $push_data)
	{
		//检查参数
		$bind_id = intval($bind_id);
		if( $bind_id<1 || empty($push_data) )
		{
			return array();
		}
		
		//保存接收消息
		$data = $push_data;
		$data['bind_id'] = $bind_id;
		$data['add_time'] = time();
		if( isset($data['Precision']))
		{
			//Precision是mysql的关键词
			$data['Precision_Str'] = $data['Precision'];
			unset($data['Precision']);
		}
		$receive_id = $this->add_receive($data);
		if( $receive_id<1 )
		{
			return array();
		}
		
		//获取指令信息，根据推送数据
		$cmd_info = array();
		$MsgType = trim($push_data['MsgType']);
		$method_name = 'wx_get_cmd_by_push_' . $MsgType;
		if( method_exists($this, $method_name) )
		{
			$cmd_info = call_user_func(array($this, $method_name), $bind_id, $push_data);
		}
		//若是数据，并且为空，则默认
		if( is_array($cmd_info) && empty($cmd_info) )
		{
			$cmd_info = call_user_func(array($this, 'wx_get_cmd_by_push_default'), $bind_id, $push_data);
		}
		if( !is_array($cmd_info) || empty($cmd_info) )
		{
			return array();
		}
		//注意：$cmd_info可能只有exec_type，其cmd_id也可能是负数
		
		//更新接收消息
		$cmd_id = intval($cmd_info['cmd_id']);
		$this->update_receive(array('cmd_id'=>$cmd_id), $receive_id);
		
		//获取回复信息，根据指令信息
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
		
		//保存消息回复
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
	 * 获取命令，根据文本
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array|false
	 */
	private function wx_get_cmd_by_push_text($bind_id, $push_data)
	{
		//拆取关键词
		$content = trim($push_data['Content']);
		$content_arr = preg_split('/#/', $content);
		$keyword = trim($content_arr[0]);
		if( strlen($keyword)<1 )
		{
			return array();
		}
		$keyword_upper = strtoupper($keyword);
		
		//获取关键词指令
		$cmd_list = $this->get_cmd_list($bind_id, false, "cmd_type IN ('keywords','keywords_contain')", 'cmd_id ASC', '0,99999999');
		if( empty($cmd_list) )
		{
			return array();
		}
		
		//匹配
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
	 * 获取命令，根据事件
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array|false
	 */
	private function wx_get_cmd_by_push_event($bind_id, $push_data)
	{
		$event = trim($push_data['Event']);
		$event = strtoupper($event); //转成大写字母
		$event_key = trim($push_data['EventKey']);
		if( $event=='SUBSCRIBE' )
        {
            //添加用户
            $subscribe_open_id = trim($push_data['FromUserName']);
            $data = array('is_subscribe'=>1, 'subscribe_time'=>time());
            $this->save_user($data, $bind_id, $subscribe_open_id);

            //更新用户基本信息
            $this->sync_user_base_info($bind_id, "open_id='{$subscribe_open_id}'");

			//关注自动回复
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
            //更改用户关注状态
            $subscribe_open_id = $push_data['FromUserName'];
            $data = array('is_subscribe'=>0, 'subscribe_time'=>time());
            $this->save_user($data, $bind_id, $subscribe_open_id);

			//取消关注
			return false;
		}
		elseif( $event=='VIEW' )
		{
			//自定义菜单，跳转URL
			return false;
		}
		elseif( $event=='CLICK' )
		{
			//自定义菜单，点击推事件
			$where_str = "cmd_type='click' AND cmd_val=:x_cmd_val";
			sqlSetParam($where_str, 'x_cmd_val', $event_key);
			$cmd_list = $this->get_cmd_list($bind_id, false, $where_str, 'cmd_id ASC', '0,1');
			if( empty($cmd_list) )
			{
				//更改自定义菜单的Key时，可能会导致找不到对应的命令！
				return array();
			}
			$cmd_info = $cmd_list[0];
			if( !is_array($cmd_info) ) $cmd_info = false;
			
			return $cmd_info;
		}
		elseif( $event=='LOCATION' )
		{
			//地理位置
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
			//模板消息回执
			$data = array(
				'status' => $push_data['Status'],
			);
			$this->update_message_template_by_msgid($data, $bind_id, $push_data['MsgID']);
			
			return false;
        }
        elseif( $event=='MASSSENDJOBFINISH')//群发, 结果事件推送
        {
        	//更新群发信息状态
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
	 * 获取命令，根据图片
	 * @param int $bind_id
	 * @param array $push_data
	 * @return array|false
	 */
	private function wx_get_cmd_by_push_image($bind_id, $push_data)
	{
		$open_id = trim($push_data['FromUserName']);
		
		//检查用户当前模式
		$mode_code = $this->get_user_mode_code($bind_id, $open_id);
		if( $mode_code=='YUE_CREDIT2_STEP1' )
		{
			//信用等级V2，身份认证
			return array('cmd_id'=>-1, 'exec_type'=>'YUE_CREDIT2_STEP2');
		}
		
		return $this->wx_get_cmd_by_push_default($bind_id, $push_data);
	}
	
	/**
	 * 获取命令，根据默认
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
		
		//默认回复之后，半小时内不再默认回复
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
	 * 获取回复数据，根据文本
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
	 * 获取回复数据，根据图文
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_news($bind_id, $push_data, $cmd_info)
	{
		//整理图文ID
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
		
		//获取图文列表，微信限制最多10条
		$news_list = array();
		$news_list_tmp = $this->get_news_list($bind_id, false, "news_id IN ({$news_id_str})", 'news_id ASC', '0,10');
		foreach ($news_list_tmp as $news_info_tmp)
		{
			$news_id_tmp = intval($news_info_tmp['news_id']);
			$news_list[$news_id_tmp] = $news_info_tmp;
		}
		
		//整理图文内容
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
	 * 获取回复数据，根据图片
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
     */
	private function wx_get_reply_by_exec_image($bind_id, $push_data, $cmd_info)
    {
        //检查素材的资源id
        $exec_val = trim($cmd_info['exec_val']);
        $media_id = $exec_val;
        if( strlen($media_id)<1 )
        {
            return array();
        }

        //获取获取素材信息, 被动回复只支持一张图片
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
	 * 获取回复数据，根据转发到多客服
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_transfer_customer_service($bind_id, $push_data, $cmd_info)
	{
		//发送客服消息
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
	 * 默认回复数据
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_default($bind_id, $push_data, $cmd_info)
	{
		//获取默认指令
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
	 * 信用等级V2，身份认证第一步
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_yue_credit2_step1($bind_id, $push_data, $cmd_info)
	{
		$open_id = trim($push_data['FromUserName']);
		
		//检查绑定情况
		$pai_bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
		$bind_info = $pai_bind_weixin_obj->get_bind_info_by_open_id($open_id);
		if( empty($bind_info) )
		{
			return array('MsgType'=>'text', 'Content'=>'抱歉，请先登录并绑定微信，<a href="http://yp.yueus.com/m/auth.php?route=mine">点击这里</a>进行操作。');
		}
		$user_id = intval($bind_info['user_id']);
		
		//检查信用等级
		$pai_user_level_obj = POCO::singleton('pai_user_level_class');
		$user_level = $pai_user_level_obj->get_user_level($user_id);
		if( $user_level!=1 )
		{
			return array('MsgType'=>'text', 'Content'=>'你好，你已经是V2/V3用户了，无需再次认证。');
		}
		
		//设置用户模式
		$this->save_user_mode($bind_id, $open_id, 'YUE_CREDIT2_STEP1', 1800);
		
		return array('MsgType'=>'text', 'Content'=>'你好，请<a href="http://yp.yueus.com/wap/others/wx_v2jc.php">点击此处</a>查看认证流程。并按要求在30分钟内发送V2认证资料。');
	}
	
	/**
	 * 信用等级V2，身份认证第二步
	 * @param int $bind_id
	 * @param array $push_data
	 * @param array $cmd_info
	 * @return array
	 */
	private function wx_get_reply_by_exec_yue_credit2_step2($bind_id, $push_data, $cmd_info)
	{
		$open_id = trim($push_data['FromUserName']);
		
		//设置用户模式
		$this->save_user_mode($bind_id, $open_id, '');
		
		//检查绑定情况
		$pai_bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
		$bind_info = $pai_bind_weixin_obj->get_bind_info_by_open_id($open_id);
		if( empty($bind_info) )
		{
			//这种情况在step1已经检查，不可能进到step2
			return array();
		}
		$user_id = intval($bind_info['user_id']);
		
		//检查信用等级
		$pai_user_level_obj = POCO::singleton('pai_user_level_class');
		$user_level = $pai_user_level_obj->get_user_level($user_id);
		if( $user_level!=1 )
		{
			return array('MsgType'=>'text', 'Content'=>'你好，你已经是V2/V3用户了，无需再次认证。');
		}
		
		$pai_id_audit_obj = POCO::singleton('pai_id_audit_class');
		$insert_data = array();
		$insert_data['user_id'] = $user_id;
		$insert_data['img'] = trim($push_data['PicUrl']);
		$pai_id_audit_obj->add_audit($insert_data);
		
		return array('MsgType'=>'text', 'Content'=>'审核资料已提交成功，请耐心等待审核。请留意微信通知。');
	}
	
	/**
	 * 生成带场景值的二维码
	 * @param int $bind_id      绑定号
	 * @param string $type      二维码类型，
	 *                          支持三种类型QR_SCENE为临时，QR_LIMIT_SCENE为永久，QR_LIMIT_STR_SCENE为永久的字符串
	 * @param int $scene_id     场景值，类型为临时或永久时必填，不能大于10万
	 * @param int $expires_time 过期时间，类型为临时时必填，单位秒，最不超过七天，小于60秒微信会重置为60秒
	 * @param string $scene_str 字符串场景值，类型为字符串型是必填
	 * @return array 			ticket 二维码链接，url 二维码内容，[expire_time] 临时场景码的过期时间
	 */
	public function wx_creat_scene_qr_code($bind_id, $type, $scene_id = null, $expire_time = 604800, $scene_str = '')
	{
		$rst = array();
		if( $bind_id<1 ) return $rst;
		if( !in_array($type,$this->qr_type) ) return $rst;
		if( $type == 'QR_SCENE'&&( ($expire_time<1||$expire_time>604800)||($scene_id<=0||$scene_id>100000) ) ) return $rst;//必填值检查
		if( $type == 'QR_LIMIT_SCENE'&&($scene_id<=0||$scene_id>100000) ) return $rst;//必填值检查
		if( $type == 'QR_LIMIT_STR_SCENE'&&strlen($scene_str)<1 ) return $rst;//必填值检查
		
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
	 * 回复文本消息
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_text($array)
	{
		$keys = array('Content');
		return $this->array_to_xml($array, $keys);
	}
	
	/**
	 * 回复图片消息
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
	 * 回复语音消息
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
	 * 回复视频消息
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
	 * 回复音乐消息
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
	 * 回复图文消息
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
	 * 回复多客服消息
	 * @param array $array
	 * @return string
	 */
	private function reply_array_to_xml_transfer_customer_service($array)
	{
		$keys = array();
		return $this->array_to_xml($array, $keys);
	}
	
	/**
	 * 客服消息JSON，文本
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
	 * 获取JSON格式，根据菜单信息
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
	 * 获取xml
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
	 * 格式化JSON值
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
	 * 生成
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
     * @Synopsis 取得公众号的关注列表
     * @Param $bind_id int 绑定ID
     * @Param $next_openid string 微信返回的next_openid
     * @Returns array openid组成的数组
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
        if( !isset($ret_info['total']) )//请求不成功
        {
            return array();
        }

        $next_openid = $ret_info['next_openid'];
        $open_id_list = $ret_info['data']['openid'];
        //总数大于10000, 且当前取到10000, 列表还没有取完
        if( $ret_info['total']>10000&&$ret_info['count']==10000 )
        {
            $next_batch_open_id_list = $this->wx_get_subscribe_list($bind_id, $next_openid);
            return array_merge($open_id_list, $next_batch_open_id_list);
        }
        return $open_id_list;
    }

    /**
     * @Synopsis 更新关注列表
     *
     * @Param $bind_id int 绑定号
     *
     * @Returns array 带提示的返回结果
     */
    public function sync_subscribe_list($bind_id)
    {
        $rst = array('err_code'=>0, 'message'=>'', 'subscribe_summary'=>array(), );

        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '参数错误';
            return $rst;
        }

        //从微信取出关注列表
        $subscribe_open_id_list = $this->wx_get_subscribe_list($bind_id);
        if( empty($subscribe_open_id_list) )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '关注列表为空';
            return $rst;
        }

        //从数据库取出关注列表
        $user_list = $this->get_user_list($bind_id, false, 'is_subscribe=1', 'id ASC', '0,999999999', 'open_id');
        //降维
        $user_open_id_list = array_map('array_shift', $user_list);

        //计算新增关注用户
        $new_subscribe_open_id_list = array_diff($subscribe_open_id_list, $user_open_id_list);
        //计算取消关注用户
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
        $rst['message'] = '更新成功';
        $rst['subscribe_summary'] = array(
            'subscribed'=>count($new_subscribe_open_id_list),
            'unsubscribed'=>count($unsubscribed_open_id_list), 
        );
        return $rst;
    }

    /**
     * @Synopsis 取用户基本信息
     *
     * @Param $bind_id int 绑定号
     * @Param $openid array 用户openid列表
     *
     * @Returns array 用户信息多维数组
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

        if( !isset($ret_info['user_info_list']) )//请求不成功
        {
            return array();
        }
        return $ret_info['user_info_list'];
    }

    /**
     * @Synopsis 更新用户基本信息
     *
     * @Param $bind_id int 绑定号
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
            $rst['message'] = '参数错误';
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
            $rst['message'] = '用户关注列表为空';
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
            $rst['message'] = '无法取得关注列表';
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
            if( $is_subscribe==1 )//用户已关注
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
            //已取消的关注的用户只更新 关注状态, subscribe_time
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
        $rst['message'] = '更新成功';
        $rst['sync_summary'] = array(
            'count_request'=>count($user_open_id_list), 
            'count_synced'=>$i, 
        );
        return $rst;
    }

    /**
     * @Synopsis 取得用户列表
     *
     * @Param $bind_id int 绑定号
     * @Param $b_select_count boolean 取总数标志
     * @Param $where_str string 附加的搜索条件
     * @Param $order_by string 排序条件
     * @Param $limit string 分页
     * @Param $field string 字段
     *
     * @Returns array 用户列表 二维数组
     */
    public function get_user_list($bind_id, $b_select_count=false, $where_str='', $order_by='id DESC', $limit='0,20', $fields='*')
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return array();
        }

        //整理查询条件
        $sql_where = "bind_id={$bind_id}";

        if (strlen($where_str) > 0)
        {
            $sql_where .= " AND {$where_str}";
        }

        //查询
        $this->set_weixin_user_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        return $this->findAll($sql_where, $limit, $order_by, $fields);
    }
	
    /**
     * 群发接口
     * @param int $bind_id
     * @param array $open_id_list
     * @param string $media_id
     * @return array array( 'errcode'=>0, 'errmsg'=>'send job submission success', 'msg_id'=>34182)
     */
    private function wx_mass_send($bind_id, $open_id_list, $media_id)
    {
    	//检查参数
        $bind_id = intval($bind_id);
        $media_id = trim($media_id);
        if( $bind_id<1 || !is_array($open_id_list) || empty($open_id_list) || strlen($media_id)<1 )
        {
            return array();
        }
        
        //检查数量
        $open_id_count = count($open_id_list);
		if( $open_id_count<$this->message_mass_send_min || $open_id_count>$this->message_mass_send_max )
		{
			return array();
		}
		
        //提交
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
     * 群发接口
     * @param int $bind_id
     * @param string $open_id
     * @param string $media_id
     * @return array array( 'errcode'=>0, 'errmsg'=>'send job submission success', 'msg_id'=>0)
     */
    private function wx_mass_send_preview($bind_id, $open_id, $media_id)
    {
    	//检查参数
    	$bind_id = intval($bind_id);
    	$open_id = trim($open_id);
    	$media_id = trim($media_id);
    	if( $bind_id<1 || strlen($open_id)<1 || strlen($media_id)<1 )
    	{
    		return array();
    	}
    	
    	//获取素材信息
    	$material_info = $this->get_material_info($bind_id, $media_id);
    	if( empty($material_info) )
    	{
    		return array();
    	}
    	
    	//提交
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
        * @Synopsis 预览群发消息
        *
        * @Param $bind_id int 公众号绑定号
        * @Param $open_id string 用户open_id
        * @Param $media_id string 素材id
        *
        * @Returns array
     */
    public function mass_send_preview($bind_id, $open_id, $media_id)
    {
        $rst = array('err_code'=>0, 'message'=>'');
        
        //检查参数
        $bind_id = intval($bind_id);
        $open_id = trim($open_id);
        $media_id = trim($media_id);
        if( $bind_id<1 || strlen($open_id)<1 || strlen($media_id)<1 )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '参数错误';
            return $rst;
        }
		
        //获取素材信息
        $material_info = $this->get_material_info($bind_id, $media_id);
        if( empty($material_info) )
        {
            $rst['err_code'] = -2;
            $rst['message'] = '找不到该素材';
            return $rst;
        }
        
        //提交
        $preview_rst = $this->wx_mass_send_preview($bind_id, $open_id, $media_id);
        if( $preview_rst['errcode']!==0 )
        {
        	$rst['err_code'] = -3;
        	$rst['message'] = '发送预览失败';
        	return $rst;
            
        }
        
        $rst['err_code'] = 1;
        $rst['message'] = '发送预览成功';
        return $rst;
    }

    /**
        * @Synopsis 群发信息
        *
        * @Param $bind_id
        * @Param $where_str string 筛选用户的条件
        * @Param $media_id string 素材id
        * @Param $remark 任务备注
        *
        * @Returns array
     */
    public function mass_send($mission_info)
    {
        $rst = array('err_code'=>0, 'message'=>'',);
        
        //检查参数
        $mission_id = intval($mission_info['mission_id']);
        $bind_id = intval($mission_info['bind_id']);
        if( !is_array($mission_info) || empty($mission_info) || $mission_id<1 || $bind_id<1 )
        {
        	$rst['err_code'] = -1;
        	$rst['message'] = '参数错误';
        	return $rst;
        }
        $media_id = trim($mission_info['media_id']);
        $condition_str = trim($mission_info['condition_str']);
        $user_count = intval($mission_info['user_count']);
        $status = intval($mission_info['status']);
        
        //检查状态
        if( $status!=0 )
        {
            $rst['err_code'] = -2;
            $rst['message'] = '任务状态错误';
            return $rst;
        }
        
        //处理中
        $rst = $this->handle_mass_send_mission_status($mission_id);
        if( !$rst )
        {
        	$rst['err_code'] = -3;
        	$rst['message'] = '修改任务状态失败';
        	return $rst;
        }
		
        //获取素材信息
        $material_info = $this->get_material_info($bind_id, $media_id);
        if( empty($material_info) )
        {
            $more_info = array(
            	'err_msg' => '素材为空',
            );
            $this->fail_mass_send_mission_status($mission_id, $more_info);
            
            $rst['err_code'] = -4;
            $rst['message'] = $more_info['err_msg'];
            return $rst;
        }
		
        //检查条件
        if( strlen($condition_str)<1 )
        {
        	$more_info = array(
        		'err_msg' => '任务条件错误',
        	);
        	$this->fail_mass_send_mission_status($mission_id, $more_info);
        	
            $rst['err_code'] = -5;
            $rst['message'] = $more_info['err_msg'];
            return $rst;
        }
		
        //获取用户列表
        $user_list = $this->get_user_list($bind_id, false, $condition_str, 'id DESC', '0,99999999', 'open_id');
        $user_list_count = count($user_list);
        if( !is_array($user_list) || $user_list_count<$this->message_mass_send_min )
        {
        	$more_info = array(
        		'err_msg' => '群发用户数不能低于' . $this->message_mass_send_min,
        	);
        	$this->fail_mass_send_mission_status($mission_id, $more_info);
        	
        	$rst['err_code'] = -6;
        	$rst['message'] = $more_info['err_msg'];
        	return $rst;
        }
        
        //加强判断
        if( abs($user_list_count-$user_count)>50 )//与提交时的用户数量比较，如果相差50个以上，不开始任务
        {
        	$more_info = array(
        		'err_msg' => '群发用户数变动过大',
        	);
        	$this->fail_mass_send_mission_status($mission_id, $more_info);
        	 
        	$rst['err_code'] = -7;
        	$rst['message'] = $more_info['err_msg'];
        	return $rst;
        }
        
        //降为一维，分组
        $open_id_arr = array_map('array_shift', $user_list);
        unset($user_list);
        $open_id_arr_chunk = array_chunk($open_id_arr, $this->message_mass_send_max);
        
        //产生子任务
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
		
        //获取子任务列表
        $msg_list = $this->get_mass_send_msg_list($bind_id, false, "mission_id={$mission_id} AND status=0", 'msg_id ASC', '0,99999999');
        foreach($msg_list as $msg_info)
        {
        	//检查父任务状态
        	$mission_info = $this->get_mass_send_mission_info($mission_id);
        	if( $mission_info['status']!=1 )
        	{
        		break;
        	}
        	$msg_id = intval($msg_info['msg_id']);
        	
        	//处理中
        	$rst = $this->handle_mass_send_msg_status($msg_id);
        	if( !$rst )
        	{
        		break;
        	}
        	
        	//获取子任务用户
        	$user_send_log_list = $this->get_mass_send_user_log_list($bind_id, false, "mission_id={$mission_id} AND msg_id='{$msg_id}'", 'log_id ASC', '0,99999999', 'open_id');
        	$open_id_arr = array_map('array_shift', $user_send_log_list);
        	unset($user_send_log_list);
        	
        	//提交
        	//$mass_send_rst = $this->wx_mass_send_preview($bind_id, $open_id_arr[0], $media_id); //预览接口
        	$mass_send_rst = $this->wx_mass_send($bind_id, $open_id_arr, $media_id); //正式接口
        	$wx_msg_id = trim($mass_send_rst['msg_id']);
        	$wx_err_code = trim($mass_send_rst['errcode']);
        	$wx_err_msg = trim($mass_send_rst['errmsg']);
        	if( empty($mass_send_rst) )
        	{
        		$wx_err_msg = '返回了空数组';
        	}
        	
        	//错误
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
        	
        	//成功
        	$more_info = array(
        		'wx_msg_id' => $wx_msg_id,
        		'wx_err_code' => $wx_err_code,
        		'wx_err_msg' => $wx_err_msg,
        	);
        	$this->success_mass_send_msg_status($msg_id, $more_info);
        }
        
        //处理完成
        $this->success_mass_send_mission_status($mission_id);
        
        $rst['err_code'] = 1;
        $rst['message'] = '处理完成';
        return $rst;
    }
	
    /**
     * @Synopsis 获取微信的永久素材列表,
     *
     * @Param $bind_id int 绑定号
     * @Param $type string 素材类型
     * @Param $offset int 偏移量 零始
     * @Param $count int 分页数量, 最大20
     *
     * @Returns array 素材列表
     */
    private function wx_get_material($bind_id, $type = 'news', $offset = 0, $count = 20)
    {
        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            return array();
        }
        //仅支持以下类型
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
        if( !isset($ret_info['item_count']) )//请求不成功
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
     * @Synopsis 同步素材
     *
     * @Param $bind_id int 绑定号
     * @Param $type string 类型
     *
     * @Returns array 带提示的返回结果
     */
    public function sync_material($bind_id, $type = 'news')
    {
        $rst = array('err_code'=>0, 'message'=>'', 'quantity'=>0, );

        $bind_id = intval($bind_id);
        if( $bind_id<1 )
        {
            $rst['err_code'] = -1;
            $rst['message'] = '参数错误';
            return $rst;
        }

        $material_list = $this->wx_get_material($bind_id, $type);
        if( empty($material_list) )
        {
            $rst['err_code'] = -2;
            $rst['message'] = '无法取得素材';
            return $rst;
        }

        //先更新所有为已删除
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
                //去掉html 文本，影响jsondecode
                foreach( $content_tmp as &$val)
                {
                    unset($val['content']);
                }

                //多图文素材类型为m_news
                if( count($content_tmp)>1 )
                {
                    $data['material_type'] = 'm_news';
                }
            }
            elseif( isset($material_info['name'])&&isset($material_info['url']) )
            {
                //图片, 语音, 视频素材, 记录其名称和预览地址
                $content_tmp = array(
                    'name'=>$material_info['name'],
                    'url'=>$material_info['url'],
                );
            }

            $data['item_content'] = json_encode($content_tmp);
            $this->add_material($data);
        }

        $rst['err_code'] = 1;
        $rst['message'] = '同步成功';
        $rst['quantity'] = count($material_list);
        return $rst;
    }

    /**
     * @Synopsis 添加素材
     *
     * @Param $material_info array 素材
     *
     * material_id    内部ID                                                                                                       
     * bind_id        绑定号                                                                                                      
     * media_id       微信的资源ID                                                                                              
     * title          素材的名称,有些类型可能没有名称                                                                 
     * digest         素材摘要,有些类型可能没有                                                                          
     * url            预览地址                                                                                                   
     * update_time    修改时间,指的是在微信公众后台的最后一次编辑时间                                         
     * material_type  类型,多图文素材(m_news),图文素材(news),图片素材(image),视频素材(video),语音素材(voice)  
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
     * 获取素材列表
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

        //整理查询条件
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

        //查询
        $this->set_weixin_material_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }

    /**
        * @Synopsis 获取素材信息
        *
        * @Param $bind_id int 绑定号
        * @Param $media_id string 资源ID
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
        * @Synopsis 更新素材
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
     * @Synopsis 新增群发任务
     *
     * @Param $data array 
     * array(
     *       mass_send_id                                                                                                                                                                                                                                                                                                         
     *       bind_id     公众号绑定号                                                                                                                                                                                                                                                                                       
     *       mission_id  群发任务id, 一个mission_id包含多个msg_id                                                                                                                                                                                                                                                                                           
     *       media_id    资源id                                                                                                                                                                                                                                                                                                 
     *       condition_str   此次推送的搜索添加                                                                                                                                                                                                                                                                                                
     *       mission_status      群发任务的提交状态, 全部成功2, 部分成功1, 提交状态0 
     *       mission_desc     任务描述
     *       send_status      群发任务的发送状态, 全部成功2, 部分成功1, 提交状态0 
     *       err_code  提交任务时的错误代码
     *       message 提交任务是的错误信息
     *       add_time    添加时间
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
     * @Synopsis 更新群发任务
     *
     * @Param $mission_id string 任务ID
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
     * 处理
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
     * 处理失败
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
     * 处理成功
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
     * 处理取消 
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
        //待处理和处理中的任务可以取消
        $rst = $this->update($data, "mission_id={$mission_id} AND status IN (0,1)");
        return $rst>0?true:false;
    }

    
    /**
     * 取消任务 
     * @param int $mission_id
     * @param array $more_info
     * @return bool
     */
    public function cancel_mass_send_mission($mission_id, $more_info = array())
    {
        return $this->cancel_mass_send_mission_status($mission_id, $more_info);
    }
    
    /**
        * @Synopsis 获取任务详情
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
        * @Synopsis 获取任务列表
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

        //整理查询条件
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

        //查询
        $this->set_weixin_mass_send_mission_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }

    /**
        * @Synopsis 添加群发信息
        *
        * @Param $data array 
        *
        * msg_id 微信返回的msg_id
        * mission_id 群发任务ID
        * send_status 微信推送的状态
        * total_count 此群发信息的发送用户总数
        * err_count   未能成功送达的用户数量
        * sent_count  成功送达的用户数量
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
     * @Synopsis 更新群发信息
     *
     * @Param $data array
     * @Param $bind_id int 绑定号
     * @Param $wx_msg_id string 群发消息号
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
     * 处理
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
     * 处理失败
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
     * 处理成功
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
        * @Synopsis 取得群发信息详情
        *
        * @Param $bind_id
        * @Param $msg_id
        * @Param $append_where string 附加的搜索条件
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
        * @Synopsis 获取群发信息列表
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

        //整理查询条件
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

        //查询
        $this->set_weixin_mass_send_msg_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }

    /**
        * @Synopsis 插入用户群发记录
        *
        * @Param $data array
        * 'bind_id'    公众号
        * 'msg_id'     群发信息id
        * 'open_id'    用户open_id
        * 'mission_id' 群发任务id
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
        * @Synopsis 查询用户的群发记录列表
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

        //整理查询条件
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

        //查询
        $this->set_weixin_mass_send_user_log_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }

    /**
        * @Synopsis 更新用户发送记录
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
        * @Synopsis 拼接任务条件语句和描述信息
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
        $mission_desc = '此任务群发的对象为：';

        $sex_enum = array('1'=>'男', '2'=>'女',);

        $temp_arr = array();
        $sex = intval($data['sex']);
        if( $sex>0 )
        {
            $mission_desc .= ' 性别为 '.$sex_enum[$sex];
            $temp_arr[] = $sex;
        }
        $no_sex = intval($data['no_sex']);
        if( $no_sex>0 )
        {
            $mission_desc .= ' 或性别未知';
            $temp_arr[] = 0;
        }
        if( !empty($temp_arr) )
        {
            $mission_desc .= "；";
            $temp_str = implode(',', $temp_arr);
            $where .= " AND sex in ({$temp_str})";
        }

        $temp_arr = array();
        $country = trim($data['country']);
        if( strlen($country)>0 )
        {
            $mission_desc .= ' 国家为 '.$country;
            $temp_arr[] = "'".$country."'";
        }
        $no_country = intval($data['no_country']);
        if( $no_country>0 )
        {
            $mission_desc .= ' 或国家信息为空';
            $temp_arr[] = '\'\'';
        }
        if( !empty($temp_arr) )
        {
            $mission_desc .= "；";
            $temp_str = implode(',', $temp_arr);
            $where .= " AND country in ({$temp_str})";
        }

        $temp_arr = array();
        $province = trim($data['province']);
        if( strlen($province)>0 )
        {
            $mission_desc .= ' 省份为 '.$province;
            $temp_arr[] = "'".$province."'";
        }
        $no_province = intval($data['no_province']);
        if( $no_province>0 )
        {
            $mission_desc .= ' 或省份信息为空';
            $temp_arr[] = '\'\'';
        }
        if( !empty($temp_arr) )
        {
            $mission_desc .= "；";
            $temp_str = implode(',', $temp_arr);
            $where .= " AND province in ({$temp_str})";
        }

        $temp_arr = array();
        $city = trim($data['city']);
        if( strlen($city)>0 )
        {
            $mission_desc .= ' 城市为 '.$city;
            $temp_arr[] = "'".$city."'";
        }
        $no_city = intval($data['no_city']);
        if( $no_city>0 )
        {
            $mission_desc .= ' 或城市信息为空';
            $temp_arr[] = '\'\'';
        }
        if( !empty($temp_arr) )
        {
            $mission_desc .= "；";
            $temp_str = implode(',', $temp_arr);
            $where .= " AND city in ({$temp_str})";
        }

        $min_time = trim($data['min_time']);
        if( strlen($min_time)>0 )
        {
            $mission_desc .= " 关注时间在 {$min_time} 开始之后；";
            $min_time = strtotime($min_time);
            $where .= " AND subscribe_time>={$min_time}";
        }
        $max_time = trim($data['max_time']);
        if( strlen($max_time)>0 )
        {
            $mission_desc .= " 关注时间在 {$max_time} 结束之前；";
            $max_time = strtotime($max_time)+86400-1;
            $where .= " AND subscribe_time<={$max_time}";
        }
        $rst['condition_str'] = $where;
        $rst['mission_desc'] = "{$mission_desc}。";
        return $rst;
    }
}
