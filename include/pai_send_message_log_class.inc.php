<?php
/*
 * 信息推送操作类
 * xiao xiao
 */

class pai_send_message_log_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'send_message_log' );
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_info($insert_data)
	{
		global $yue_login_id;
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		$insert_data['add_id'] = $yue_login_id;
		return $this->insert ( $insert_data, "IGNORE" );
	
	}
    
    public function del_info($id)
    {
        $id = (int)$id;

		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->delete($where_str);
        
    }
    
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_info($data, $id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}
	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_info_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	/**
	* 获取信息
	* @param  [int] $id  [任务ID]
	* @return [boolean]  [true|false]
	*/
   public function get_info($status)
   {
		$status = ( int ) $status;
		$ret = $this->find ( "status={$status}","add_time ASC" );
		return $ret;
   }

   /*public function add_table()
   {
   	   $table = "pai_log_db.send_message_log";
   	   $sql = "ALTER TABLE {$table} ADD location_id int(10) unsigned NOT NULL default '0'";
   	   $this->query($sql);
   }*/

   /**
    * 查询信息发送返回数据接口
    */
   public function get_send_data($tid)
   {
   	  $tid = (int)$tid;
   	  if(empty($tid))
   	  {
   	  	return false;
   	  }
   	  /*$gmclient= new GearmanClient();
      # Add default server (localhost).
      $gmclient->addServers("172.18.5.211:9830");
      $gmclient->setTimeout(5000); // 设置超时
      do
      {
      	
      	$req_param['tid'] = $tid;
        $result= $gmclient->do("get_qunfa_send_info",json_encode($req_param) );
      }
      while(false);*/
      //while($gmclient->returnCode() != GEARMAN_SUCCESS);
      //echo "Success: $result\n";

       $gmclient = POCO::singleton('pai_gearman_base_class');
       $gmclient->connect('172.18.5.211', '9830');

       $req_param['tid'] = $tid;
       $result= $gmclient->_do("get_qunfa_send_info",$req_param);

      return $result;
   }

    /* *
     * 保存 form 表单提交过来的数据
     * @param [string]    $type        发送类型      text文本 card卡片
     * @param [string]    $valid_time  发送日期      有效发送时间(hhMMss-hhMMss, 如: 100000-150000)
     * @param [string]    $duration    发送时间段    持续发送时间(yyyymmdd-yyyymmdd, 如: 20150301-20150305)
     * @param [array]     $user_arr   用户string   有效用户数组$user_arr = array(1000000,1000002);
     * @param [intval]    $send_uid    由什么约约小助手还是约约系统助手发 有效ID 10002,10005
     * @param [array]     $option      拓展参数
     * $option = array
     * (
     * 'content'    =>'', //文本
     *
     * 'card_text1' =>'', //卡片
     * 'card_title' =>'', //卡片
     * );
     *
     * return [array] array('result'=>0, 'message'=>'', 'response'=>'')
     */

    public function save_group_message($type,$valid_time,$duration,$user_arr,$send_uid,$option = array())
    {
        //初始化
        $result = array('result'=>0, 'message'=>'', 'response'=>'');
        global $yue_login_id;

        //数据整理
        $type       = trim($type);
        $valid_time = trim($valid_time);
        $duration   = trim($duration);
        $send_uid   = intval($send_uid);

        //判断形参必须参数
        if(strlen($type) <1 || strlen($valid_time) <1 || strlen($duration) <1
            || !is_array($user_arr) || empty($user_arr) || strlen($send_uid) <1)
        {
            $result['result']  = -1;
            $result['message'] = '参数错误';
            return $result;
        }
        if(!is_array($option)) $option = array();

        $only_send_online = intval($option['only_send_online']);
        $auto_send        = intval($option['auto_send']);
        $is_multi         = intval($option['is_multi']);
        $role             = trim($option['role']);
        $location_id      = intval($option['location_id']);

        //校验用户ID
        $user_list = array();
        foreach($user_arr as $val)
        {
            $val = intval($val);
            if($val>0) $user_list[] = trim($val);
        }
        //系列化用户数组
        $serialize_user = serialize($user_list);

        if($type == 'text')//文本
        {
            $content = trim($option['content']);
            if(strlen($content) <1)
            {
                $result['result']  = -2;
                $result['message'] = '参数错误';
                return $result;
            }
            //request_md5 数组
            $request_data = array
            (
                'type'             => $type,
                'valid_time'       => $valid_time,
                'duration'         => $duration,
                'user_list'        => $user_list,
                'send_uid'         => trim($send_uid),
                'only_send_online' => $only_send_online,
                'auto_send'        => $auto_send,
                'content'          => $this->gbk_to_utf8($content)
            );

            $json_data = json_encode($request_data);
            unset($request_data);
            //request_md5和$content_md5生成
            $request_md5      = md5($json_data);
            $content_md5      = md5($this->gbk_to_utf8($content));
            $data = array
            (
                'content'          => $content,
                'content_md5'      => $content_md5,
                'request_md5'      => $request_md5
            );
        }
        elseif($type == 'notify') //带有url文本格式
        {
            $content  = trim($option['content']);
            $link_url = trim($option['link_url']);
            $wifi_url = trim($option['wifi_url']);
            if(strlen($content) <1 || strlen($link_url) < 1 || strlen($wifi_url) <1)
            {
                $result['result']  = -2;
                $result['message'] = '参数错误';
                return $result;
            }
            //request_md5 数组
            $request_data = array
            (
                'type'             => $type,
                'valid_time'       => $valid_time,
                'duration'         => $duration,
                'user_list'        => $user_list,
                'send_uid'         => trim($send_uid),
                'only_send_online' => $only_send_online,
                'auto_send'        => $auto_send,
                'content'          => $this->gbk_to_utf8($content),
                'link_url'         => $link_url,
                'wifi_url'         => $wifi_url
            );
            $json_data = json_encode($request_data);
            unset($request_data);
            //request_md5和$content_md5生成
            $request_md5      = md5($json_data);
            $content_md5      = md5($this->gbk_to_utf8($content));
            $data = array
            (
                'content'          => $content,
                'link_url'         => $link_url,
                'wifi_url'         => $wifi_url,
                'content_md5'      => $content_md5,
                'request_md5'      => $request_md5
            );
        }

        elseif($type == 'card')//卡片
        {
            $card_text1 = trim($option['card_text1']);
            $card_title = trim($option['card_title']);
            //判断数组必须参数
            if(strlen($card_text1) < 1 || strlen($card_title) <1)
            {
                $result['result'] = -3;
                $result['message']= '参数出错';
                return $result;
            }
            //参数整理
            $is_me            = intval($option['is_me']);
            //1表示有第二个标题的，2表示没有第二个标题
            $card_style       = isset($option['card_style']) ? intval($option['card_style']) : 1;
            $link_url         = trim($option['link_url']);
            $wifi_url         = trim($option['wifi_url']);
            $card_text2       = trim($option['card_text2']);
            //request_md5 数组整理
            $request_data = array
            (
                'type'       => $type,
                'valid_time' => $valid_time,
                'duration'   => $duration,
                'user_list'  => $user_list,
                'send_uid'   => trim($send_uid),
                'only_send_online' => $only_send_online,
                'auto_send'  => $auto_send,
                'is_me'      => trim($is_me),
                'card_style' => trim($card_style),
                'card_title' => $this->gbk_to_utf8($card_title),
                'card_text1' => $this->gbk_to_utf8($card_text1),
                'link_url'   => $link_url,
                'wifi_url'   => $wifi_url,
                'card_text2' => $this->gbk_to_utf8($card_text2)
            );
            $json_data = json_encode($request_data);
            print_r($json_data);
            unset($request_data);
            $request_md5 = md5($json_data);
            $data = array
            (
                'is_me'      => $is_me,
                'card_style' => $card_style,
                'card_title' => $card_title,
                'card_text1' => $card_text1,
                'link_url'   => $link_url,
                'wifi_url'   => $wifi_url,
                'card_text2' => $card_text2,
                'request_md5'=> $request_md5
            );
        }
        //其他类型,尚未支持
        else
        {
            $result['result'] = -4;
            $result['message']='类型不支持';
            return $result;
        }

        $data['type']             = $type;
        $data['valid_time']       = $valid_time;
        $data['duration']         = $duration;
        $data['user_list']        = $serialize_user;
        $data['send_uid']         = $send_uid;
        $data['only_send_online'] = $only_send_online;
        $data['auto_send']        = $auto_send;
        $data['add_id']           = $yue_login_id;
        $data['is_multi']         = $is_multi;
        $data['role']             = $role;
        $data['location_id']      = $location_id;
        $data['add_time']         = time();
        $data['update_time']      = time();
        $data['total']            = count($user_list);
        //$data['status'] = 4;

        $result['response'] =  $this->add_info($data);
        unset($data);
        return $result;
    }

    /**
     * 查询未发送的数据并且拼凑成发送格式的数据
     *
     * return [void] array('result'=>0, 'message'=>'', 'response'=>'')
     */
    public function send_group_message_by_log()
    {
        $ret = $this->get_info(1);
        //存在未执行完成的消息
        if(is_array($ret) && !empty($ret))
        {

            $tid   = intval($ret['tid']);
            $total = intval($ret['total']);
            $id    = intval($ret['id']);
            //获取发送状态
            $response       = $this->get_send_data($tid);
            $response       = trim($response);

            if(strlen($response)>0)
            {
                $response_arr   = json_decode($response, TRUE);
                $response_total = intval($response_arr['data']);
                if ($response_total == $total)
                {
                    $update_data['status'] = 2;
                }
                $update_data['response']    = $response;
                $update_data['send_total']  = $response_total;
            }
            else
            {
                $update_data['log'] = '无返回值';
            }
            //更新消息状态
            $update_data['update_time'] = time();
            return  $this->update_info($update_data, $id);
            unset($ret);
        }
        //发送消息
        else
        {
            $not_send_ret    = $this->get_info(0);
            if(!is_array($not_send_ret) || empty($not_send_ret)) return false;

            $data        = array();
            $option      = array();
            //整理数据
            $id               = intval($not_send_ret['id']);

            $type             = trim($not_send_ret['type']);
            $valid_time       = trim($not_send_ret['valid_time']);
            $duration         = trim($not_send_ret['duration']);
            $user_str         = trim($not_send_ret['user_list']);
            $send_uid         = intval($not_send_ret['send_uid']);
            $request_md5      = trim($not_send_ret['request_md5']);
            $only_send_online = intval($not_send_ret['only_send_online']);
            $auto_send        = intval($not_send_ret['auto_send']);
            //必须数据不能为空
            if(strlen($type) <1 || strlen($valid_time) <1 || strlen($duration) <1
                || strlen($user_str) <1 || $send_uid<1 || strlen($request_md5) <1 )
            {
                //处理
                $data['status'] = 4;
                $data['log']    = '参数出错';
                $this->update_info($data,$id);
                return false;
            }

            //反系列化用户ID
            $user_arr = unserialize($user_str);
            if(!is_array($user_arr) || empty($user_arr))
            {
                //处理
                $data['status'] = 5;
                $data['log']    = '用户ID反系列化出错';
                $this->update_info($data,$id);
                return false;
            }
            //整理用户ID
            $user_list = array();
            foreach($user_arr as $val)
            {
                $val = intval($val);
                if($val>0) $user_list[] = trim($val);
            }

            if($type =='text')//文本信息处理
            {
                $content     = trim($not_send_ret['content']);
                $content_md5 = trim($not_send_ret['content_md5']);
                //判断必须的数据
                if(strlen($content) <1 || strlen($content_md5)<1)
                {
                    //处理
                    $data['status'] = 6;
                    $data['log']    = '参数出错';
                    $this->update_info($data,$id);
                    return false;
                }

                $option = array
                (
                    'content'     => $content,
                    'content_md5' => $content_md5
                );
            }
            elseif($type =='notify')//文本信息处理
            {
                $content     = trim($not_send_ret['content']);
                $content_md5 = trim($not_send_ret['content_md5']);
                $link_url    = trim($not_send_ret['link_url']);
                $wifi_url    = trim($not_send_ret['wifi_url']);
                //判断必须的数据
                if(strlen($content) <1 || strlen($content_md5)<1 || strlen($link_url)<1 || strlen($wifi_url)<1 )
                {
                    //处理
                    $data['status'] = 7;
                    $data['log']    = '参数出错';
                    $this->update_info($data,$id);
                    return false;
                }

                $option = array
                (
                    'content'     => $content,
                    'content_md5' => $content_md5,
                    'link_url'    => $link_url,
                    'wifi_url'    => $wifi_url
                );
            }
            elseif($type == 'card')//卡片处理
            {
                $card_title  = trim($not_send_ret['card_title']);
                $card_text1  = trim($not_send_ret['card_text1']);
                //判断数组必须参数
                if(strlen($card_title) < 1 || strlen($card_text1) <1)
                {
                    //处理
                    $data['status'] = 7;
                    $data['log']    = '参数出错';
                    $this->update_info($data,$id);
                    return false;
                }
                $is_me       = intval($not_send_ret['is_me']);
                //1表示有第二个标题的，2表示没有第二个标题
                $card_style  = isset($not_send_ret['card_style']) ? intval($not_send_ret['card_style']) : 1;
                $link_url    = trim($not_send_ret['link_url']);
                $wifi_url    = trim($not_send_ret['wifi_url']);
                $card_text2  = trim($not_send_ret['card_text2']);
                //数组
                $option = array
                (
                    'card_title' => $card_title,
                    'card_text1' => $card_text1,
                    'is_me'      => $is_me,
                    'card_style' => $card_style,
                    'link_url'   => $link_url,
                    'wifi_url'   => $wifi_url,
                    'card_text2' => $card_text2
                );
            }
            $option['only_send_online'] = $only_send_online;
            $option['auto_send']        = $auto_send;
            $result = $this->send_group_message($type,$valid_time,$duration,$user_list,$send_uid,$request_md5,$option);
            $response = trim($result['response']);

            //服务器有返回值的
            if(strlen($response) >0)
            {
                $response_arr = json_decode($response,TRUE);

                if(!is_array($response_arr)) $response_arr = array();

                $tid          = intval($response_arr['tid']);
                $code         = intval($response_arr['code']);

                if($code == 1 && $tid >0)
                {
                    $data['status'] = 1;
                    $data['tid']    = $tid;
                    $data['log']    = '提交成功';
                }
                else
                {
                    $data['status'] = 3;
                    $data['log']    = '服务器失效';
                }
                $data['response'] = $response;
                return $this->update_info($data,$id);
            }
            $data['status'] = 7;
            $data['log']    = "{$result['message']}服务器没有返回值";
            return $this->update_info($data,$id);
            //然后对$ret 处理
        }
    }

   /**
    * 发送群信息
    *
    * @param [string]    $type        发送类型   text文本 card卡片
    * @param [string]    $valid_time  发送日期   有效发送时间(hhMMss-hhMMss, 如: 100000-150000)
    * @param [string]    $duration    发送时间段 持续发送时间(yyyymmdd-yyyymmdd, 如: 20150301-20150305)
    * @param [array]     $user_arr    用户数组   有效用户数组$user_arr = array(1000000,1000002);
    * @param [intval]    $send_uid    由什么约约小助手还是约约系统助手发，可以传10002或者10005
    * @param [string]    $request_md5 所要发送内容的md5 有所要发送内容解析出来的 $arr = array();$resqust_md5 = md5(json_encode($arr));
    * @param [array]     $option  拓展参数
    * $option = array
    * (
    * 'content'     => '', //文本类型
    * 'content_md5' => '',//文本类型
    *
    * 'card_text1' => '', //卡片
    * 'card_title' => ''
    * );
    *
    *@return array array('result'=>0, 'message'=>'', 'response'=>'')
    */

   private  function send_group_message($type,$valid_time,$duration,$user_arr,$send_uid,$request_md5,$option=array())
   {
       //初始化返回内容
       $result = array('result'=>0, 'message'=>'', 'response'=>'');
       //数据整理
       $type        = trim($type);
       $valid_time  = trim($valid_time);
       $duration    = trim($duration);
       $send_uid    = intval($send_uid);
       $request_md5 = trim($request_md5);
       //检查参数
       if(strlen($type) <1 || strlen($valid_time) <1 || strlen($duration) <1
           || !is_array($user_arr) || empty($user_arr) || $send_uid <1 || strlen($request_md5) <1)
       {
           $result['result']   = -1;
           $result['message']  = '参数错误';
           return $result;
       }
       if(!is_array($option)) $option = array();
       $only_send_online = intval($option['only_send_online']);
       $auto_send        = intval($option['auto_send']);
       //整理用户ID
       $user_list = array();
       foreach($user_arr as $val)
       {
           $val = intval($val);
           if($val>0) $user_list[] = trim($val);
       }
       if($type == 'text')//文本类型
       {
           $content     = trim($option['content']);
           $content_md5 = trim($option['content_md5']);
           if(strlen($option['content']) <1 || strlen($option['content_md5']) <1)
           {
               $result['result']  = -2;
               $result['message'] = '参数错误';
           }
           //参数数组
           $data = array
           (
               'type'             => $type,
               'valid_time'       => $valid_time,
               'duration'         => $duration,
               'user_list'        => $user_list,
               'send_uid'         => trim($send_uid),
               'only_send_online' => $only_send_online,
               'auto_send'        => $auto_send,
               'content'          => $this->gbk_to_utf8($content)
           );


           $json_data = json_encode($data);
           unset($data);
           //验证md5是否相同
           if(md5($json_data) != $request_md5 || md5($this->gbk_to_utf8($content)) != $content_md5)
           {
               $result['result']  = -3;
               $result['message'] = 'md5校验失败';
               return $result;
           }
           $post_data = array
           (
               'data' => $json_data,
               'request_md5' => $request_md5,
               'content_md5' => $content_md5
           );
       }
       elseif($type == 'notify')//文本类型 url
       {
           $content     = trim($option['content']);
           $content_md5 = trim($option['content_md5']);
           $link_url    = trim($option['link_url']);
           $wifi_url    = trim($option['wifi_url']);
           if(strlen($option['content']) <1 || strlen($option['content_md5']) <1 || strlen($link_url) < 1 || strlen($wifi_url) <1)
           {
               $result['result']  = -2;
               $result['message'] = '参数错误';
           }
           //参数数组
           $data = array
           (
               'type'             => $type,
               'valid_time'       => $valid_time,
               'duration'         => $duration,
               'user_list'        => $user_list,
               'send_uid'         => trim($send_uid),
               'only_send_online' => $only_send_online,
               'auto_send'        => $auto_send,
               'content'          => $this->gbk_to_utf8($content),
               'link_url'         => $link_url,
               'wifi_url'         => $wifi_url
           );


           $json_data = json_encode($data);
           unset($data);
           //验证md5是否相同
           if(md5($json_data) != $request_md5 || md5($this->gbk_to_utf8($content)) != $content_md5)
           {
               $result['result']  = -3;
               $result['message'] = 'md5校验失败';
               return $result;
           }
           $post_data = array
           (
               'data' => $json_data,
               'request_md5' => $request_md5,
               'content_md5' => $content_md5
           );
       }
       elseif($type == 'card')//卡片类型
       {
           $card_text1 = trim($option['card_text1']);
           $card_title = trim($option['card_title']);
           if(strlen($card_text1) <1 || strlen($card_title) <1)
           {
               $result['result']  = -4;
               $result['message'] ='参数错误';
               return $result;
           }
           //参数整理
           $is_me            = intval($option['is_me']);
           //1表示有第二个标题的，2表示没有第二个标题
           $card_style       = isset($option['card_style']) ? intval($option['card_style']) : 1;
           $link_url         = trim($option['link_url']);
           $wifi_url         = trim($option['wifi_url']);
           $card_text2       = trim($option['card_text2']);
           //数组整理
           $data = array
           (
               'type'       => $type,
               'valid_time' => $valid_time,
               'duration'   => $duration,
               'user_list'  => $user_list,
               'send_uid'   => trim($send_uid),
               'only_send_online' => $only_send_online,
               'auto_send'  => $auto_send,
               'is_me'      => trim($is_me),
               'card_style' => trim($card_style),
               'card_title' => $this->gbk_to_utf8($card_title),
               'card_text1' => $this->gbk_to_utf8($card_text1),
               'link_url'   => $link_url,
               'wifi_url'   => $wifi_url,
               'card_text2' => $this->gbk_to_utf8($card_text2)
           );
           $json_data = json_encode($data);
           unset($data);
           print_r($json_data);
           echo md5($json_data).'<br/>';
           echo $request_md5;
           //md5校验
           if(md5($json_data) != $request_md5)
           {
               $result['result'] = -5;
               $result['message']='md5校验失败';
               return $result;
           }

           $post_data = array
           (
               'data' => $json_data,
               'request_md5' => $request_md5
           );
       }
       else//其他类型，尚未支持
       {
           $result['result'] = -6;
           $result['message']='类型不支持';
           return $result;
       }

       //判断是否存在这个数组
       if(!is_array($post_data) || empty($post_data))
       {
           $result['result'] = -7;
           $result['message']='非法操作';
           return $result;
       }

       //开始发送
       $url = "http://172.18.5.211:8085/sysmessage.cgi";
       $ci = curl_init();
       curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 10);
       curl_setopt($ci, CURLOPT_TIMEOUT, 20);
       curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ci, CURLOPT_URL, $url);
       curl_setopt($ci, CURLOPT_POST, true);
       curl_setopt($ci, CURLOPT_POSTFIELDS, $post_data);
       $response = curl_exec($ci);
       curl_close($ci);

       $result['response'] = $response;
       return $result;
   }

    /**
     * GBK转成UTF-8
     *
     * @param [string|array] $str 字符串或者数组 如: $str ="hello world";
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
                $str[$key] = gbk_to_utf8($val);
            }
        }
        return $str;
    }

    /*
     * UTF-8转成GBK
     * @param string|array $str
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
                $str[$key] = utf8_to_gbk($val);
            }
        }
        return $str;
    }

}

?>