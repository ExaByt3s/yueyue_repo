<?php
/**
 * @desc:   活动群发消息类[活动_群发_消息]
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/6
 * @Time:   10:06
 * version: 1.0
 */
class pai_event_mass_message_class extends POCO_TDG
{
    /**
     * 限制角色
     * @var array
     */
    private $send_role_arr = array('yueseller','yuebuyer');
    /**
     * @var array 支持发送格式
     */
    //private $type_arr = array('text');

    private $type_arr = array('text','notify','card');//暂时只支持文本就行了

    /**
     * @var array  卡片格式
     */
    private $card_style_arr = array(1,2);

    /**
     *构造函数
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
        $this->setTableName( 'pai_event_mass_message_tbl' );
    }

    /**
     * 添加数据
     * @param $insert_data
     * @return int
     */
    private function add_mass_message($insert_data)
    {
        if(!is_array($insert_data) || empty($insert_data))
        {
            //throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
            return false;
        }
        return $this->insert($insert_data);
    }

    /**
     * 通过ID更新数据
     * @param int $id
     * @param $update_data
     * @return mixed
     */
    private function update_info_by_id($id,$update_data)
    {
        $id = (int)$id;
        if($id <1)
        {
            //throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
            return false;
        }
        if(!is_array($update_data) || empty($update_data))
        {
            //throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
            return false;
        }
        return $this->update($update_data,"id={$id}");
    }


    /**
     * 接收需要添加的数据并构造完，传给添加数据函数
     * @param array $post_data
     * @return bool|int
     */
    public function add_mass_message_info($post_data)
    {
        if(!is_array($post_data) || empty($post_data)) return false;
        $data = array();
        $data['add_time'] = time();
        $data['message_data'] = serialize($post_data);
        return $this->add_mass_message($data);
    }

    //addlog
    private function add_send_message_info_log($send_user_id,$send_role,$user_arr,$content,$type='text',$link_url,$wifi_url,$card_style,$card_title,$card_text1,$card_text2)
    {
        $send_user_id = (int)$send_user_id;
        $send_role = trim($send_role);
        $content = trim($content);
        $type = trim($type);
        $link_url =  trim($link_url);
        $wifi_url =  trim($wifi_url);
        $card_style =  intval($card_style);
        $card_title = trim($card_title);
        $card_text1 = trim($card_text1);
        $card_text2 = trim($card_text2);
        $data = array();
        $post_data['send_user_id'] = $send_user_id;
        $post_data['send_user_id'] = $send_user_id;
        $post_data['send_role'] = $send_role;
        $post_data['user_arr'] = $user_arr;
        $post_data['content'] = $content;
        $post_data['type'] = $type;
        $post_data['link_url'] = $link_url;
        $post_data['wifi_url'] = $wifi_url;
        $post_data['card_style'] = $card_style;
        $post_data['card_title'] = $card_title;
        $post_data['card_text1'] = $card_text1;
        $post_data['card_text2'] = $card_text2;
        $data['add_time'] = time();
        $data['message_data'] = serialize($post_data);
        $data['reponse'] = 'add_log';
        return $this->add_mass_message($data);

    }


    /**
     * 开始发送群发消息 【暂时只支持发送文字】
     * @param int $send_user_id  发送者ID
     * @param string $send_role  发送者角色
     * @param array $user_arr    用户数组,示例：array(100293,100580);
     * @param string $content    所有发送的内容 yueseller（代表商家角色），yuebuyer(消费者角色)
     * @param string $type       text|notify|card ，暂时只支持text格式（文本格式）
     * @return array $return_data 返回一个数组格式为array(code=>0,'err'=>'')，如果code=1表示发送成功,否则返回失败，err表示发送失败的提示信息
     */
    /*public function start_mass_message($send_user_id,$send_role,$user_arr,$content,$type='text')
    {
        $post_data = array();
        $return_data['code'] = 0;
        $send_user_id = (int)$send_user_id;
        $send_role = trim($send_role);
        $content = trim($content);
        $type = trim($type);
        if($send_user_id <1) return $return_data['err'] = '发送用户ID为空';
        if(strlen($send_role) <1 || !in_array($send_role,$this->send_role_arr)) return $return_data['err'] = '角色非法';
        if(!is_array($user_arr) || empty($user_arr)) return $return_data['err'] = '接收者非法';
        if(strlen($content) <1) return $return_data['err'] = '内容非法';
        if(strlen($type) <1 || !in_array($type,$this->type_arr)) return $return_data['err'] = '格式非法';
        //角色转化，把发送者的角色匹配更改为目标者的角色
        if($send_role == 'yuebuyer')
        {
            $send_role = 'yueseller';
        }else{
            $send_role = 'yuebuyer';
        }
        //用户处理，去除不符合的用户，并加上角色
        $user_list = array();
        foreach($user_arr as $user_id)
        {
            $user_id = (int)$user_id;
            if($user_id >=100000) $user_list[] = "{$send_role}/{$user_id}";
        }
        if(!is_array($user_list) || empty($user_list)) return $return_data['err'] = '处理后接收者非法';
        //内容整理与过滤
        $duration_start_time = date('Ymd',time());
        $duration_end_time = date('Ymd',time()+2*3600);//为了防止刚好12点发送防止超时
        $valid_time = '000000-235959'; //发送日期
        $duration = "{$duration_start_time}-$duration_end_time";//发送时间
        $post_data['type'] = $type;
        $post_data['valid_time'] = $valid_time;
        $post_data['duration'] = $duration;
        $post_data['user_list'] = $user_list;//用户数组
        $post_data['send_uid'] = $send_role;
        $post_data['send_uid'] = trim($send_user_id);
        $post_data['only_send_online'] = 0;
        $post_data['auto_send'] = 1;
        $post_data['content'] = $this->gbk_to_utf8($content);
        //添加log
        $retID = $this->add_mass_message_info($post_data);
        $json_data = json_encode($post_data);
        $content_md5 = md5($this->gbk_to_utf8($content));
        $request_md5 = md5($json_data);
        $data = array
        (
            'data' => $json_data,
            'request_md5' => $request_md5,
            'content_md5' => $content_md5
        );
        unset($post_data);
        $response = $this->curl_mass_message($data);
        //判断服务器是否成功
        if($response)
        {
            $this->update_info_by_id($retID,array('reponse'=>$response,'service_return_time'=>time()));
            $result = json_decode($response, TRUE);
            $code = (int)$result['code'];
            if($code == 1)
            {
                $return_data['code'] = 1;
                return $return_data;
            }
        }
        return $return_data['err'] = '提交数据的格式有误';
    }*/

    /**
     * 开始发送群发消息 【暂时只支持发送文字】
     * @param int $send_user_id  发送者ID
     * @param string $send_role  发送者角色
     * @param array $user_arr    用户数组,示例：array(100293,100580);
     * @param string $content    所有发送的内容 yueseller（代表商家角色），yuebuyer(消费者角色)
     * @param string $type       text|notify|card
     * @param string $link_url      移动端链接
     * @param string $wifi_url      wifi下链接
     * @param string $card_style    卡片格式2|1
     * @param string $card_title    卡片标题
     * @param string $card_text1    卡片内容
     * @param string $card_text2    内容2
     * @return string
     */
    public function start_mass_message($send_user_id,$send_role,$user_arr,$content,$type='text',$link_url,$wifi_url,$card_style,$card_title,$card_text1,$card_text2)
    {
        //添加log不管是否成功
        $this->add_send_message_info_log($send_user_id,$send_role,$user_arr,$content,$type,$link_url,$wifi_url,$card_style,$card_title,$card_text1,$card_text2);

        $post_data = array();
        $return_data['code'] = 0;
        //参数整理
        $send_user_id = (int)$send_user_id;
        $send_role = trim($send_role);
        $content = trim($content);
        $type = trim($type);
        $link_url = trim($link_url);
        $wifi_url = trim($wifi_url);
        $card_style = (int)$card_style;
        $card_title = trim($card_title);
        $card_text1 = trim($card_text1);
        $card_text2 = trim($card_text2);
        $is_me = 0;
        //参数验证
        if($send_user_id <1) return $return_data['err'] = '发送用户ID为空';
        if(strlen($send_role) <1 || !in_array($send_role,$this->send_role_arr)) return $return_data['err'] = '角色非法';
        if(!is_array($user_arr) || empty($user_arr)) return $return_data['err'] = '接收者非法';
        if(strlen($type) <1 || !in_array($type,$this->type_arr)) return $return_data['err'] = '格式非法';
        if($type == 'text' || $type == 'notify') //文字格式出错
        {
            if(strlen($content) <1) return $return_data['err'] = '内容非法';
            if($type == 'notify')
            {
                if(strlen($link_url) <1 || strlen($wifi_url) <1) return $return_data['err'] = '链接非法';
            }
        }
        if($type == 'card')//卡片
        {
            if($card_style <1 || !in_array($card_style,$this->card_style_arr)) return $return_data['err'] = '卡片类型非法';
            if(strlen($card_text1) <1 || strlen($card_title) <1) return $return_data['err'] = '卡片内容或者标题不能为空';
            if($card_style == 1)
            {
                if(strlen($card_text2) <1) return $return_data['err'] = '卡片副标题不能为空';
            }
        }

        //角色转化，把发送者的角色匹配更改为目标者的角色
        if($send_role == 'yuebuyer')
        {
            $send_role = 'yueseller';
        }else{
            $send_role = 'yuebuyer';
        }
        //用户处理，去除不符合的用户，并加上角色
        $user_list = array();
        foreach($user_arr as $user_id)
        {
            $user_id = (int)$user_id;
            if($user_id >=100000) $user_list[] = "{$send_role}/{$user_id}";
        }
        if(!is_array($user_list) || empty($user_list)) return $return_data['err'] = '处理后接收者非法';
        //内容整理与过滤
        $duration_start_time = date('Ymd',time());
        $duration_end_time = date('Ymd',time()+2*3600);//为了防止刚好12点发送防止超时
        $valid_time = '000000-235959'; //发送日期
        $duration = "{$duration_start_time}-$duration_end_time";//发送时间
        $post_data['type'] = $type;
        $post_data['valid_time'] = $valid_time;
        $post_data['duration'] = $duration;
        $post_data['user_list'] = $user_list;//用户数组
        $post_data['send_uid'] = $send_role;
        $post_data['send_uid'] = trim($send_user_id);
        $post_data['only_send_online'] = 0;
        $post_data['auto_send'] = 1;
        //整理发送
        if($type == 'text' || $type == 'notify')  //文字格式
        {
            $post_data['content'] = $this->gbk_to_utf8($content);
            if($type == 'notify')
            {
                $post_data['link_url'] = $link_url;
                $post_data['wifi_url'] = $wifi_url;
            }
            $json_data = json_encode($post_data);
            $content_md5 = md5($this->gbk_to_utf8($content));
            $request_md5 = md5($json_data);
            $data = array
            (
                'data' => $json_data,
                'request_md5' => $request_md5,
                'content_md5' => $content_md5
            );

        }
        if($type == 'card')  //卡片类型
        {
            $post_data['is_me'] = trim($is_me);
            $post_data['card_style'] = trim($card_style);
            $post_data['card_title'] = $this->gbk_to_utf8($card_title);
            $post_data['card_text1'] = $this->gbk_to_utf8($card_text1);
            if(strlen($link_url)>0)$post_data['link_url'] = $link_url;
            if(strlen($wifi_url)>0)$post_data['wifi_url'] = $wifi_url;
            if($card_style == 1) $post_data['card_text2'] = $this->gbk_to_utf8($card_text2);
            $json_data = json_encode($post_data);
            $request_md5 = md5($json_data);
            $data = array
            (
                'data' => $json_data,
                'request_md5' => $request_md5
            );
        }
        unset($post_data);
        $retID = $this->add_mass_message_info($data); //添加log
        $response = $this->curl_mass_message($data);  //发送数据
        //判断服务器是否成功
        if($response)
        {
            $this->update_info_by_id($retID,array('reponse'=>$response,'service_return_time'=>time()));
            $result = json_decode($response, TRUE);
            $code = (int)$result['code'];
            if($code == 1)
            {
                $return_data['code'] = 1;
                return $return_data;
            }
        }
        return $return_data['err'] = '提交数据的格式有误';
    }

    /**
     * 消息提交到服务器中
     * @param array $post_data
     * @return mixed
     */
    private function curl_mass_message($post_data)
    {
        if(!is_array($post_data) || empty($post_data)) return false;
        $url = "http://172.18.5.216:8084/sendall.cgi";
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ci, CURLOPT_TIMEOUT, 20);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_POST, true);
        curl_setopt($ci, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ci);
        curl_close($ci);
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
                $str[$key] = $this->gbk_to_utf8($val);
            }
        }
        return $str;
    }
}