<?php
/**
 * @desc:   ���͵�����Ϣ��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/29
 * @Time:   16:30
 * version: 2.0
 */
class pai_send_single_message_class extends POCO_TDG
{
    /**
     * �û������ֽ�ɫ
     * @var array
     */
    private $role_arr = array('yuebuyer','yueseller');

    /**
     * �ı������ָ�ʽ
     * @var array
     */
    private $type_arr = array('text','notify','card');

    /**
     * �����û�������ID
     * @var array
     */
    private $send_arr = array(10002,10005);

    /**
     * ��Ƭ��ʽ���޶�
     * @var array
     */
    private $card_style_arr = array(1,2);
    /**
     * ���캯��
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
        $this->setTableName( 'pai_single_message_log' );
    }

    /**
     * ���뵥����Ϣ
     * @param  array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_info($insert_data)
    {
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
        }
        return $this->insert($insert_data,"IGNORE");
    }

    /**
     * ������Ҫ��ӵ��������ݵ�����
     * @param string $type ��Ϣ��ʽ��ʾ������text������notify������card��
     * @param int $send_uid ���Ǹ��û����ͣ�ʾ����10002|10005
     * @param int $user_id ���͸��û���ʾ����100580
     * @param int $role  ���͸��Ǹ��ͻ��ˡ�
     * @param String $content �ı���ʽ������
     * @param string $link_url �ƶ�������
     * @param string $wifi_url wifi������
     * @param int $is_change_to_yueyue �Ƿ��޸�����
     * @param int $card_style ��Ƭ��ʽ��ʾ��1|2
     * @param string $card_text1  ��Ƭ����
     * @param string $card_title ��Ƭ����
     * @param $card_text2 ��Ƭ1��ʽ�ĸ�����
     * @return int
     * @throws App_Exception
     */
    public function add_message_info($type,$send_uid,$user_id,$role,$content,$link_url,$wifi_url,$is_change_to_yueyue,$card_style,$card_text1,$card_title,$card_text2)
    {
        global $yue_login_id;
        $type = trim($type);
        $send_uid = (int)$send_uid;
        $user_id = (int)$user_id;
        $role = trim($role);
        $content = trim($content);
        $link_url = trim($link_url);
        $wifi_url = trim($wifi_url);
        $is_change_to_yueyue = (int)$is_change_to_yueyue;
        $card_style = (int)$card_style;
        $card_text1 = trim($card_text1);
        $card_title = trim($card_title);
        $card_text2 = trim($card_text2);
        if(strlen($type) <1 || !in_array($type,$this->type_arr)) return false;
        if($send_uid <1) return false;
        if($user_id <1) return false;
        if(strlen($role) <1 || !in_array($role,$this->role_arr)) return false;
        if(strlen($content) <1 && strlen($card_text1) <1) return false;
        $data = array();
        $data['add_time'] = time();
        $data['add_id'] = $yue_login_id;
        $data['type'] = $type;
        $data['send_uid'] = $send_uid;
        $data['user_id'] = $user_id;
        $data['role'] = $role;
        $data['content'] = $content;
        $data['link_url'] = $link_url;
        $data['wifi_url'] = $wifi_url;
        $data['is_change_to_yueyue'] = $is_change_to_yueyue;
        $data['card_style'] = $card_style;
        $data['card_text1'] = $card_text1;
        $data['card_title'] = $card_title;
        $data['card_text2'] = $card_text2;
        return $this->add_info($data);
    }

    /**
     * ��ȡ����������
     * @param string $role
     * @param string $url
     * @return string
     */
    private function get_real_url($role,$url)
    {
        $role = trim($role);
        $url = trim($url);
        if(strlen($role)<1 || !in_array($role,$this->role_arr)) return false;
        if(strlen($url) <1) return false;
        if($role == 'yuebuyer') $role = 'yueyue';

        $url_arr = parse_url($url);
        $httts = trim($url_arr['scheme']);
        if($httts == 'http' || $httts == 'https')//�Ƿ�Ϊhttp||https
        {
            $wifi_url = str_replace('yp.yueus.com','yp-wifi.yueus.com',$url);
            $curl = "{$role}://goto?type=inner_web&url=".urlencode(iconv('gbk', 'utf8', $url))."&wifi_url=".urlencode(iconv('gbk', 'utf8', $wifi_url));
        }
        elseif($httts == 'yueyue' || $httts == 'yueseller')
        {
            $curl = $url;
        }
        return $curl;
    }

    /**
     * �����û���Ϣ���ҷ�����Ϣ���ͻ���
     * @param string $type   ��Ϣ��ʽ��ʾ������text������notify������card��
     * @param int $send_uid  ���Ǹ��û����ͣ�ʾ����10002|10005
     * @param int $user_id   ���͸��û���ʾ����100580
     * @param string $role   ���͸��Ǹ��ͻ���
     * @param string $content �ı���ʽ������
     * @param string $link_url �ƶ�������
     * @param string $wifi_url wifi������
     * @param int $is_change_to_yueyue  �Ƿ��޸�����
     * @param int $card_style
     * @param string $card_text1
     * @param string $card_title
     * @param string  $card_text2
     * @param string  $card_text2
     * @return array|string
     * @throws App_Exception
     */
    public function add_single_text_info($type,$send_uid,$user_id,$role,$content ='',$link_url ='',$wifi_url ='',$is_change_to_yueyue = 0,$card_style = 0,$card_text1 = '',$card_title = '',$card_text2 = '')
    {
        $return_data = array('code'=>0);//����ֵ
        $type = trim($type);
        $send_uid = (int)$send_uid;
        $user_id = (int)$user_id;
        $role = trim($role);
        $content = trim($content);
        $link_url = trim($link_url);
        $wifi_url = trim($wifi_url);
        $is_change_to_yueyue = (int)$is_change_to_yueyue;
        $card_style = (int)$card_style;
        $card_text1 = trim($card_text1);
        $card_title = trim($card_title);
        $card_text2 = trim($card_text2);
        if(strlen($type) <1 || !in_array($type,$this->type_arr)) return $return_data['err'] = '���ͷǷ�';
        if($type == 'notify')
        {
            if(strlen($content) <1) return $return_data['err'] = '���ݷǷ�';
            if(strlen($link_url) <1 || strlen($wifi_url) <1) return $return_data['err'] = '���ӷǷ�';
            if($is_change_to_yueyue == 1)
            {
                $link_url = $this->get_real_url($role,$link_url);
                $wifi_url = $this->get_real_url($role,$wifi_url);
            }
        }
        elseif($type == 'text')//�����Ӹ�Ϊ��
        {
            if(strlen($content) <1) return $return_data['err'] = '���ݷǷ�';
            $link_url = '';
            $wifi_url = '';
        }
        elseif($type == 'card')
        {
            if($card_style <1 || !in_array($card_style,$this->card_style_arr)) return $return_data['err'] = '��Ƭ���Ͳ���';
            if(strlen($card_text1) <1) return $return_data['err'] = '��Ƭ���ݲ���Ϊ��';
            if(strlen($card_title) <1) return $return_data['err'] = '��Ƭ���ⲻ��Ϊ��';
            if($card_style == 1)
            {
                if(strlen($card_text2) <1) return $return_data['err'] = '�����ⲻ��Ϊ��';
            }
            else
            {
                $card_text2 = '';
            }
            if($is_change_to_yueyue == 1)
            {
                if(strlen($link_url)>0) $link_url = $this->get_real_url($role,$link_url);
                if(strlen($wifi_url)>0) $wifi_url = $this->get_real_url($role,$wifi_url);
            }
        }
        //�����޸�
        if($user_id <1) return $return_data['err'] = '�û�ID����Ϊ��';
        if(strlen($role) <1 || !in_array($role,$this->role_arr)) return $return_data['err'] = '��ɫ�Ƿ�';
        if($send_uid <1 || !in_array($send_uid,$this->send_arr)) return $return_data['err']='�����˺ŷǷ�';

        POCO_TRAN::begin($this->getServerId());//��������
        //���log
        $ret = $this->add_message_info($type,$send_uid,$user_id,$role,$content,$link_url,$wifi_url,$is_change_to_yueyue,$card_style,$card_text1,$card_title,$card_text2);
        if($ret)
        {
            //������Ϣ
            $ser_ret = $this->send_message($type,$send_uid,$user_id,$role,$content,$link_url,$wifi_url,$card_style,$card_text1,$card_title,$card_text2);
            if(!is_array($ser_ret)) $return_data = array();
            $status = (int)$ser_ret['code'];
            if($status >0)//���������������
            {
                POCO_TRAN::commmit($this->getServerId());//�ύ����
                $return_data['code'] = 1;
                return $return_data;
            }
        }
        POCO_TRAN::rollback($this->getServerId());
        return $return_data['err'] = '����Ƶ������';
    }


    /**
     * ׼��������Ϣ����
     * @param string $type ��Ϣ��ʽ��ʾ������text������notify������card��
     * @param int $send_uid ���Ǹ��û����ͣ�ʾ����10002|10005
     * @param int $user_id  ���͸��û���ʾ����100580
     * @param string $role  ���͸��Ǹ��ͻ���
     * @param string $content  �ı���ʽ������
     * @param string $link_url  �ƶ�������
     * @param string $wifi_url  wifi������
     * @param int $card_style
     * @param string $card_text1
     * @param string $card_title
     * @param string $card_text2
     * @return bool
     */
    private function send_message($type,$send_uid,$user_id,$role,$content,$link_url,$wifi_url,$card_style,$card_text1,$card_title,$card_text2)
    {
        $return_data = array('code'=>0);//����ֵ
        $pai_information_push_obj = POCO::singleton( 'pai_information_push' );
        $type = trim($type);
        $send_uid = (int)$send_uid;
        $user_id = intval($user_id);
        $role = trim($role);
        $content = trim($content);
        $link_url = trim($link_url);
        $wifi_url = trim($wifi_url);
        $card_style = (int)$card_style;
        $card_text1 = trim($card_text1);
        $card_title = trim($card_title);
        $card_text2 = trim($card_text2);
        if(strlen($type) <1 || !in_array($type,$this->type_arr)) return $return_data['err'] = '���ͷǷ�';
        if($type == 'notify')
        {
            if(strlen($content) <1) return $return_data['err'] = '���ݷǷ�';
            if(strlen($link_url) <1 || strlen($wifi_url) <1) return $return_data['err'] = '���ӷǷ�';
            $post_data['content'] = $content;
            $post_data['link_url'] = $link_url;
            $post_data['wifi_url'] = $wifi_url;
        }
        elseif($type == 'text')
        {
            if(strlen($content) <1) return $return_data['err'] = '���ݷǷ�';
            $post_data['content'] = $content;
        }
        elseif($type == 'card')
        {
            if($card_style <1 || !in_array($card_style,$this->card_style_arr)) return $return_data['err'] = '��Ƭ���Ͳ���';
            if(strlen($card_text1) <1) return $return_data['err'] = '��Ƭ���ݲ���Ϊ��';
            if(strlen($card_title) <1) return $return_data['err'] = '��Ƭ���ⲻ��Ϊ��';
            if($card_style == 1)//�н���
            {
                if(strlen($card_text2) <1) return $return_data['err'] = '�����ⲻ��Ϊ��';
                $post_data['card_text2'] = $card_text2;
            }
            $post_data['card_style'] = $card_style;
            $post_data['card_text1'] = $card_text1;
            $post_data['card_title'] = $card_title;
            if(strlen($link_url)>0) $post_data['link_url'] = $link_url;
            if(strlen($wifi_url)>0) $post_data['wifi_url'] = $wifi_url;
        }
        if($user_id <1) return $return_data['err'] = '�û�ID����Ϊ��';
        if(strlen($role) <1 || !in_array($role,$this->role_arr)) return $return_data['err'] = '��ɫ�Ƿ�';

        if($send_uid <1 || !in_array($send_uid,$this->send_arr)) return $return_data['err']='�����˺ŷǷ�';
        //��ɫ��ת,����Ϊ��ȷ�����Ǹ���ɫ���͵�����һ����ɫ
        if($role == 'yuebuyer')
        {
            $role = "yueseller";
        }
        elseif($role == "yueseller")
        {
            $role = "yuebuyer";
        }
        $post_data['media_type'] = $type;
        $post_data['send_user_id'] = $send_uid;
        $post_data['to_user_id'] = $user_id;
        $post_data['send_user_role'] = $role;
        foreach($post_data AS &$val) //���ݸ�ʽ��
        {
            $val = (string)iconv ( 'gbk', 'utf-8', $val );
        }
        $ser_ret = $pai_information_push_obj->send_msg($post_data);
        if(!is_array($ser_ret)) $ser_ret = array();
        $code = (int)$ser_ret['code'];
        if($code == 1)
        {
            $return_data['code'] = 1;
            return $return_data;
        }
        return $return_data['err'] = '���������ش���';
    }

    /**
     * ��ȡ������¼���͵�log
     * @param bool $b_select_count
     * @param $role
     * @param $add_id
     * @param $type
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_single_list($b_select_count = false,$role,$add_id,$type,$where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $role = trim($role);
        $add_id = (int)$add_id;
        $type = trim($type);
        if(strlen($role)>0 && in_array($role,$this->role_arr))
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "role=:x_role";
            sqlSetParam($where_str,'x_role',$role);
        }
        if($add_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .="add_id={$add_id}";
        }
        if(strlen($type)>0 && in_array($type,$this->type_arr))
        {
            if(strlen($where_str)) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }
}