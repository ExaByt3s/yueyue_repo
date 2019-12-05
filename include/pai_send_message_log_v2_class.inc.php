<?php
/**
 * @desc:   发送商家或者卖家的信息
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/5
 * @Time:   15:29
 * version: 1.0
 */

class pai_send_message_log_v2_class extends POCO_TDG
{

    /**
     * @var array
     */
    private $user_role_arr = array();
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
        $this->user_role_arr = array('yuebuyer','yueseller');//角色限定;

	}

    /**
     * @return array
     */
    public function get_role_arr()
    {
        return $this->user_role_arr;
    }


    /**
     *
     */
    private function set_mall_message_tbl()
    {
        $this->setTableName ( 'pai_mall_all_message_log' );
    }

    /**
     *
     */
    private function set_mall_code_tbl()
    {
        $this->setTableName ( 'pai_mall_send_tmp_code_log' );
    }

    /**
     * 添加基本数据
     * @param array $insert_data
     * @return int
     * @throws App_Exception
     */
    public function add_info($insert_data)
	{
		if (empty ( $insert_data ) || !is_array($insert_data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
        $this->set_mall_message_tbl();
		return $this->insert ( $insert_data, "IGNORE" );
	
	}

    /**
     * 更新数据
     * @param array $update_data
     * @param $id
     * @return mixed
     * @throws App_Exception
     */
    public function update_info($update_data, $id)
    {
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        if (empty($update_data) || !is_array($update_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        $this->set_mall_message_tbl();
        return $this->update($update_data,"id={$id}");
    }

    /**
     * 获取单独的数据
     * @param string $role
     * @param string $user_str
     * @return array
     */
    public function get_sign_mall_list($role,$user_str)
    {
        $role = trim($role);
        $user_str = trim($user_str);
        if(!in_array($role,$this->user_role_arr) || strlen($role) <1) return false;
        if(strlen($user_str)<1) return false;
        $user_list = array();
        $user_id_arr = explode(',', $user_str);
        if(!is_array($user_id_arr)) $user_id_arr = array();
        foreach($user_id_arr as $user_id)
        {
            $user_id = intval($user_id);
            if($user_id >=100000)$user_list[] = "{$role}/".trim($user_id);
        }
        unset($user_id_arr);;
        return array_unique($user_list);
    }

    /**
     * 获取消费者列表
     * @param int $location_id 地区ID
     * @param string $user_str 用户user_str
     * @param string $where_str 条件
     * @return array
     */
    public function get_mall_buyer_list($location_id,$user_str ='',$where_str='')
    {
        $user_obj = POCO::singleton( 'pai_user_class' );
        $location_id = intval($location_id);
        $user_str = trim($user_str);
        if($location_id >0)
        {
           if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .="location_id={$location_id}";
        }
        $user_arr = $user_obj->get_user_list(false, $where_str, 'user_id DESC',"0,99999999","user_id");
        if(!is_array($user_arr)) $user_arr = array();
        $user_list = array();
        foreach($user_arr as $v)
        {
            if($v['user_id'] >=100000)$user_list[] = "yuebuyer/{$v['user_id']}";
        }
        if(strlen($user_str)>0) //填入的数据处理
        {
            $user_id_arr = explode(',', $user_str);
            if(!is_array($user_id_arr)) $user_id_arr = array();
            foreach($user_id_arr as $user_id)
            {
                $user_id = intval($user_id);
                if($user_id >=100000)$user_list[] = "yuebuyer/{$user_id}";
            }
        }
        unset($user_arr);
        return array_values(array_unique($user_list));
    }

    /**
     * 获取商家ID
     * @param int $type_id
     * @param $location_id
     * @param string $user_str 用户user_str
     * @param int $status
     * @param array $option
     * @return array
     */
    public function get_mall_seller_list($type_id,$location_id,$user_str ='',$status =1,$option = array())
    {
        $mall_obj = POCO::singleton('pai_mall_seller_class');
        $user_list = array();
        $data = array();
        $type_id = intval($type_id);
        $location_id = intval($location_id);
        $user_str = trim($user_str);
        $page_size = 1000;
        $start_limit = 0;
        if($location_id >0)
        {
            $data['location_id'] = $location_id;
        }
        if($status>0)
        {
            $data['status'] = $status;
        }
        $ret = array();
        if($type_id >0)
        {
            if($type_id == 311)//为非模特商家准备的
            {
                $all_ret = array();
                $model_ret = array();
                $sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
                $total_count = intval($sign_ret['total']);
                $page_count = ceil($total_count/$page_size);
                for($i=0;$i<= $page_count-1;$i++)
                {
                    $start_limit = $i*$page_size;
                    $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
                    $all_ret = array_merge($all_ret,$list['data']);
                }
                $data['type_id'] = 31;
                $model_sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
                $total_count = intval($model_sign_ret['total']);
                $page_count = ceil($total_count/$page_size);
                for($i=0;$i<= $page_count-1;$i++)
                {
                    $start_limit = $i*$page_size;
                    $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
                    $model_ret = array_merge($model_ret,$list['data']);
                }
                foreach($all_ret as $v)
                {
                    if($v['user_id'] >=100000) $all_list[] = "yueseller/{$v['user_id']}";
                }
                foreach($model_ret as $val)
                {
                    if($val['user_id'] >=100000) $model_list[] = "yueseller/{$val['user_id']}";
                }
                $user_list = array_values(array_diff($all_list,$model_list));
            }
            else //有类型type_id 准备的
            {
                $data['type_id'] = $type_id;
                $sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
                $total_count = intval($sign_ret['total']);
                $page_count = ceil($total_count/$page_size);
                for($i=0;$i<= $page_count-1;$i++)
                {
                    $start_limit = $i*$page_size;
                    $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
                    $ret = array_merge($ret,$list['data']);
                }
            }
        }else //没有type_id
        {
            $sign_ret = $mall_obj->search_seller_list_by_fulltext($data,"0,1");
            $total_count = intval($sign_ret['total']);
            $page_count = ceil($total_count/$page_size);
            for($i=0;$i<= $page_count-1;$i++)
            {
                $start_limit = $i*$page_size;
                $list = $mall_obj->search_seller_list_by_fulltext($data,"{$start_limit},{$page_size}");
                $ret = array_merge($ret,$list['data']);
            }
        }
        if(!is_array($ret)) $ret = array();
        foreach($ret as $v)
        {
            if($v['user_id'] >=100000) $user_list[] = "yueseller/{$v['user_id']}";
        }
        if(strlen($user_str)>0) //填入的数据处理
        {
            $user_id_arr = explode(',', $user_str);
            if(!is_array($user_id_arr)) $user_id_arr = array();
            foreach($user_id_arr as $user_id)
            {
                $user_id = intval($user_id);
                if($user_id >=100000) $user_list[] = "yueseller/{$v['user_id']}";
            }
            unset($user_id_arr);
        }
        unset($ret);
        return array_values(array_unique($user_list));
    }


    /**
     * 添加数据接收器
     * @param string $type 发送消息的格式 目前支持的格式text(文本格式),notify(链接格式)，card(卡片格式)
     * @param string $role 角色 目前只支持yuebuyer和yueseller,示例:'yuebuyer'
     * @param time $start_interval 开始时间，示例:'100000'
     * @param time $end_interval   结束时间，示例:'110000'
     * @param date $start_time     开始日期，示例:'2015-04-06'
     * @param date $end_time       结束日期，示例:'2015-05-06'
     * @param array $user_list  用户数组 有效用户数组，示例:$user_arr = array(1000000,1000002);
     * @param int $send_uid  由什么约约小助手还是约约系统助手发，可以传10002或者10005,示例:10002
     * @param array $option  拓展参数
     * $option = array
     * (
     * 'content'    =>'', //文本
     *
     * 'card_text1' =>'', //卡片
     * 'card_title' =>'', //卡片
     * );
     * @return array 返回值 示例： array('result'=>0, 'message'=>'', 'response'=>'');
     */
    public function add_message_all_v2($type,$role,$start_interval,$end_interval,$start_time,$end_time,$user_list,$send_uid,$option)
    {
        global $yue_login_id;
        $result = array('result'=>0, 'message'=>'', 'response'=>'');//返回值
        $type = trim($type);
        $role = trim($role);
        $start_interval = trim($start_interval);
        $end_interval = trim($end_interval);
        $start_time = trim($start_time);
        $end_time = trim($end_time);
        $user_list = (array)$user_list;
        if(!is_array($option)) $option = array();
        $only_send_online = intval($option['only_send_online']);
        $auto_send = intval($option['auto_send']);
        $is_multi = intval($option['is_multi']);
        $location_id = intval($option['location_id']);
        if($auto_send == 1 && count($user_list)== 1) //发单条
        {
            $start_interval = '00:00:00';
            $end_interval   = '23:59:59';
        }
        //校验时间
        if(date('H:i:s', strtotime($start_interval)) != $start_interval || date('H:i:s', strtotime($end_interval)) != $end_interval || date('Y-m-d', strtotime($start_time)) != $start_time || date('Y-m-d', strtotime($end_time)) != $end_time)
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }
        $start_interval = date('His', strtotime($start_interval));
        $end_interval = date('His', strtotime($end_interval));
        $start_time = date('Y-m-d', strtotime($start_time));
        $end_time = date('Y-m-d', strtotime($end_time));
        //判断形参必须参数
        if(strlen($type) <1 || !is_array($user_list) || empty($user_list) || strlen($send_uid) <1 || strlen($role) <1)
        {
            $result['result']  = -2;
            $result['message'] = '参数错误';
            return $result;
        }
        if(!in_array($role,$this->get_role_arr()))
        {
            $result['result']  = -3;
            $result['message'] = '角色非法';
            return $result;
        }
        //系列化用户数组
        $serialize_user = serialize($user_list);
        //格式化日期和时间
        $valid_time = date('His', strtotime($start_interval)).'-'.date('His', strtotime($end_interval));
        $duration   = date('Ymd',strtotime($start_time)).'-'.date('Ymd',strtotime($end_time));
        if($type == 'text')//文本
        {
            $content = trim($option['content']);
            if(strlen($content) <1) {
                $result['result']  = -4;
                $result['message'] = '参数错误';
                return $result;
            }
            $request_data = array //request_md5 数组
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
            print_r($request_data) ;
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
                $result['result']  = -4;
                $result['message'] = '参数错误';
                return $result;
            }
            $request_data = array //request_md5 数组
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
                $result['result'] = -4;
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
        else //其他类型,尚未支持
        {
            $result['result'] = -5;
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
        print_r($data);
        $result['response'] = $this->add_info($data);
        unset($data);
        $result['result'] = 1;
        return $result;
    }

    /**
     * 添加数据接收器
     * @param string $type 发送消息的格式 目前支持的格式text(文本格式),notify(链接格式)，card(卡片格式)
     * @param string $role 角色 目前只支持yuebuyer和yueseller,示例:'yuebuyer'
     * @param time $start_interval 开始时间，示例:'100000'
     * @param time $end_interval   结束时间，示例:'110000'
     * @param date $start_time     开始日期，示例:'2015-04-06'
     * @param date $end_time       结束日期，示例:'2015-05-06'
     * @param array $user_arr  用户数组 有效用户数组，示例:$user_arr = array(1000000,1000002);
     * @param int $send_uid  由什么约约小助手还是约约系统助手发，可以传10002或者10005,示例:10002
     * @param array $option  拓展参数
     * $option = array
     * (
     * 'content'    =>'', //文本
     *
     * 'card_text1' =>'', //卡片
     * 'card_title' =>'', //卡片
     * );
     * @return array 返回值 示例： array('result'=>0, 'message'=>'', 'response'=>'');
     */
    public function add_message_all($type,$role,$start_interval,$end_interval,$start_time,$end_time,$user_arr,$send_uid,$option)
    {
        //校验用户角色,非商家报错
        $mall_obj = POCO::singleton('pai_mall_seller_class');

        global $yue_login_id;
        $result = array('result'=>0, 'message'=>'', 'response'=>'');//返回值
        $type = trim($type);
        $role = trim($role);
        $start_interval = trim($start_interval);
        $end_interval = trim($end_interval);
        $start_time = trim($start_time);
        $end_time = trim($end_time);
        $user_arr = (array)$user_arr;
        if(!is_array($option)) $option = array();
        $only_send_online = intval($option['only_send_online']);
        $auto_send = intval($option['auto_send']);
        $is_multi = intval($option['is_multi']);
        $location_id = intval($option['location_id']);
        $type_id = intval($option['type_id']);
        if($auto_send == 1 && count($user_arr)) //发单条
        {
            $start_interval = '00:00:00';
            $end_interval   = '23:59:59';
        }
        //校验时间
        if(date('H:i:s', strtotime($start_interval)) != $start_interval || date('H:i:s', strtotime($end_interval)) != $end_interval || date('Y-m-d', strtotime($start_time)) != $start_time || date('Y-m-d', strtotime($end_time)) != $end_time)
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }
        $start_interval = date('His', strtotime($start_interval));
        $end_interval = date('His', strtotime($end_interval));
        $start_time = date('Y-m-d', strtotime($start_time));
        $end_time = date('Y-m-d', strtotime($end_time));
        //判断形参必须参数
        if(strlen($type) <1 || !is_array($user_arr) || empty($user_arr) || strlen($send_uid) <1 || strlen($role) <1)
        {
            $result['result']  = -2;
            $result['message'] = '参数错误';
            return $result;
        }
        if(!in_array($role,$this->get_role_arr()))
        {
            $result['result']  = -3;
            $result['message'] = '角色非法';
            return $result;
        }
        $user_list = array();
        foreach($user_arr as $val) //校验用户ID
        {
            $val = intval($val);
            if($role == 'yueseller')//卖家
            {
                $seller_info=$mall_obj->get_seller_info($val,2);
                $seller_name=$seller_info['seller_data']['name'];
                if($val>0 && strlen($seller_name) >0)
                {
                    if($type_id <1)
                    {
                        $user_list[] = $role.'/'.trim($val);
                    }else
                    {
                        $type_list = $mall_obj->get_store_type_id_by_user_id($val);
                        $type_arr = array();
                        foreach($type_list as $v)
                        {
                            $type_arr[]  = $v['id'];
                        }
                        if($type_id ==311) //非模特商家
                        {
                            if(!in_array(31,$type_arr)) $user_list[] = $role.'/'.trim($val);
                        }else //化妆服务，模特服务
                        {
                            if(in_array($type_id,$type_arr)) $user_list[] = $role.'/'.trim($val);
                        }
                    }
                }
            }
            else//买家
            {
                if($val>0) $user_list[] = $role.'/'.trim($val);
            }
        }
        if(!is_array($user_list) || empty($user_list))//再次判断是否为空
        {
            $result['result']  = -3;
            $result['message'] = '用户数组不能为空';
            return $result;
        }
        //系列化用户数组
        $serialize_user = serialize($user_list);
        //格式化日期和时间
        $valid_time = date('His', strtotime($start_interval)).'-'.date('His', strtotime($end_interval));
        $duration   = date('Ymd',strtotime($start_time)).'-'.date('Ymd',strtotime($end_time));
        if($type == 'text')//文本
        {
            $content = trim($option['content']);
            if(strlen($content) <1) {
                $result['result']  = -4;
                $result['message'] = '参数错误';
                return $result;
            }
            $request_data = array //request_md5 数组
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
                $result['result']  = -4;
                $result['message'] = '参数错误';
                return $result;
            }
            $request_data = array //request_md5 数组
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
                $result['result'] = -4;
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
        else //其他类型,尚未支持
        {
            $result['result'] = -5;
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
        $result['response'] = $this->add_info($data);
        unset($data);
        $result['result'] = 1;
        return $result;
    }

    /**
     * 查询未发送的数据并且拼凑成发送格式的数据
     *
     * return [void] array('result'=>0, 'message'=>'', 'response'=>'')
     */
    public function send_group_message_by_log()
    {
        $ret = $this->get_info(1);//存在未执行完成的消息
        if(is_array($ret) && !empty($ret))
        {
            $tid = intval($ret['tid']);
            $total = intval($ret['total']);
            $id= intval($ret['id']);
            //获取发送状态
            $response= $this->get_send_data($tid);
            $response= trim($response);
            if(strlen($response)>0) {
                $response_arr   = json_decode($response, TRUE);
                $response_total = intval($response_arr['data']);
                if ($response_total == $total) {
                    $update_data['status'] = 2;
                }
                $update_data['response']    = $response;
                $update_data['send_total']  = $response_total;
            }
            else {
                $update_data['log'] = '无返回值';
            }
            //更新消息状态
            $update_data['update_time'] = time();
            return  $this->update_info($update_data, $id);
            unset($ret);
        }
        else //发送消息
        {
            $not_send_ret    = $this->get_info(0);
            if(!is_array($not_send_ret) || empty($not_send_ret)) return false;
            $data = array();
            $option = array();
            //整理数据
            $id = intval($not_send_ret['id']);
            $type = trim($not_send_ret['type']);
            $valid_time = trim($not_send_ret['valid_time']);
            $duration = trim($not_send_ret['duration']);
            $role = trim($not_send_ret['role']);
            $user_str = trim($not_send_ret['user_list']);
            $send_uid = intval($not_send_ret['send_uid']);
            $request_md5 = trim($not_send_ret['request_md5']);
            $only_send_online = intval($not_send_ret['only_send_online']);
            $auto_send = intval($not_send_ret['auto_send']);
            //必须数据不能为空
            if(strlen($type) <1 || strlen($valid_time) <1 || strlen($duration) <1
                || strlen($user_str) <1 || $send_uid<1 || strlen($request_md5) <1 ) { //处理
                $data['status'] = 4;
                $data['log']    = '参数出错';
                $this->update_info($data,$id);
                return false;
            }
            //反系列化用户ID
            $user_arr = unserialize($user_str);
            if($send_uid == 1 && count($user_arr)>1)
            {
                $data['status'] = 5;
                $data['log']    = '自动发送时user_arr大于1';
                $this->update_info($data,$id);
                return false;
            }
            if(!is_array($user_arr) || empty($user_arr))
            {
                //处理
                $data['status'] = 5;
                $data['log']    = '用户ID反系列化出错';
                $this->update_info($data,$id);
                return false;
            }
            /*if($auto_send ==1 && count($user_arr) >1)//自动发送只能发送单条  && count($user_arr) >1
            {
                $data['status'] = 5;
                $data['log']    = '用户非法操作';
                $this->update_info($data,$id);
                return false;
            }*/
            if(!in_array($role,$this->get_role_arr()))//角色限定
            {
                $data['status'] = 5;
                $data['log']    = '角色出错';
                $this->update_info($data,$id);return false;
            }
            //整理用户ID
            $user_list = array();
            foreach($user_arr as $val) {

                if(strlen($val)>0) $user_list[] = trim($val);
            }
            if($type =='text') { //文本信息处理
                $content     = trim($not_send_ret['content']);
                $content_md5 = trim($not_send_ret['content_md5']);
                //判断必须的数据
                if(strlen($content) <1 || strlen($content_md5)<1) {
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
            elseif($type =='notify') { //文本信息处理
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
        $user_list = $user_arr;
       /* foreach($user_arr as $val)
        {
            if(strlen($val)>0) $user_list[] = trim($val);
        }*/
        /*if($auto_send ==1 && count($user_list) >1)
        {
            $result['result']  = -2;
            $result['message'] = '非法操作';
            return $result;
        }*/
        if($type == 'text')//文本类型
        {
            $content     = trim($option['content']);
            $content_md5 = trim($option['content_md5']);
            if(strlen($option['content']) <1 || strlen($option['content_md5']) <1)
            {
                $result['result']  = -2;
                $result['message'] = '参数错误';
                return $result;
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

            print_r($data);
            $json_data = json_encode($data);
            unset($data);
            echo md5($json_data).'||'.$request_md5.'||=>'.md5($this->gbk_to_utf8($content)).'||'.$content_md5;
            //exit;
            //$request_md5 = md5($json_data);
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
        print_r($result);
        //echo "测试";
        //exit;
        //开始发送
        $url = "http://172.18.5.211:8085/sendall.cgi";
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
        //print_r($result);
        return $result;
    }


    /**
     * 获取列表数据
     * @param bool $b_select_count  是否查询条数，示例：true（表示条数）,false 表示数组
     * @param string $role 角色，有买家和卖家两个,示例：yuebuyer（买家），yueseller（卖家）
     * @param int $add_id 添加者ID,示例：100000
     * @param string $type 发送类型，text（文本类型）notify（文本+链接）card 卡片类型
     * @param int $status  发送状态，-1表示所有，0未发送
     * @param string $where_str 条件
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_info_list($b_select_count = false,$role,$add_id,$type,$status =-1,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
        $this->set_mall_message_tbl();
		$role = trim($role);
        $add_id = intval($add_id);
        $type = trim($type);
        $status = intval($status);
        if(strlen($role) >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "role=:x_role";
            sqlSetParam($where_str,'x_role',$role);
        }
        if($add_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "add_id={$add_id}";
        }
        if(strlen($type) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($status >=0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "status=$status";
        }
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
     * @param $status [任务状态]
     * @return array
     */
    public function get_info($status)
    {
        $this->set_mall_message_tbl();
        $status = ( int ) $status;
        $ret = $this->find ( "status={$status}","add_time DESC,id DESC" );
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
     * @param int $tid 服务器返回的ID
     * @return mixed
     */
    public function get_send_data($tid)
   {
   	  $tid = (int)$tid;
   	  if(empty($tid))
   	  {
   	  	return false;
   	  }
   	  $gmclient= new GearmanClient();
      # Add default server (localhost).
      $gmclient->addServers("172.18.5.211:9830");
      $gmclient->setTimeout(5000); // 设置超时
      do
      {
      	$req_param['tid'] = $tid;
        $result= $gmclient->do("get_qunfa_send_info",json_encode($req_param) );
      }
      while(false);
      //while($gmclient->returnCode() != GEARMAN_SUCCESS);
      //echo "Success: $result\n";

       /*$gmclient = POCO::singleton('pai_gearman_base_class');
       $gmclient->connect('172.18.5.211', '9830');

       $req_param['tid'] = $tid;
       $result= $gmclient->_do("get_qunfa_send_info",$req_param);*/

      return $result;
   }


  #############################################发送信息表部分############################################


    /**
     * 添加发送消息code
     * @param  array $insert_data
     * @return int
     * @throws App_Exception
     */
    public function add_message_code($insert_data)
    {
        global $yue_login_id;
        if (empty ( $insert_data ))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        $this->set_mall_code_tbl();
        $insert_data['user_id'] = $yue_login_id;
        $this->insert ( $insert_data ,"REPLACE" );
        return true;

    }

    /**
     * @param $user_id
     * @return array|bool
     */
    public function get_code_info($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->set_mall_code_tbl();
        return $this->find("user_id={$user_id}");
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
    /**
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
                $str[$key] = utf8_to_gbk($val);
            }
        }
        return $str;
    }

}

?>