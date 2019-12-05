<?php
/**
 * @desc:   �Ⱥ����Ϣ��[�_Ⱥ��_��Ϣ]
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/6
 * @Time:   10:06
 * version: 1.0
 */
class pai_event_mass_message_class extends POCO_TDG
{
    /**
     * ���ƽ�ɫ
     * @var array
     */
    private $send_role_arr = array('yueseller','yuebuyer');
    /**
     * @var array ֧�ַ��͸�ʽ
     */
    //private $type_arr = array('text');

    private $type_arr = array('text','notify','card');//��ʱֻ֧���ı�������

    /**
     * @var array  ��Ƭ��ʽ
     */
    private $card_style_arr = array(1,2);

    /**
     *���캯��
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
        $this->setTableName( 'pai_event_mass_message_tbl' );
    }

    /**
     * �������
     * @param $insert_data
     * @return int
     */
    private function add_mass_message($insert_data)
    {
        if(!is_array($insert_data) || empty($insert_data))
        {
            //throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
            return false;
        }
        return $this->insert($insert_data);
    }

    /**
     * ͨ��ID��������
     * @param int $id
     * @param $update_data
     * @return mixed
     */
    private function update_info_by_id($id,$update_data)
    {
        $id = (int)$id;
        if($id <1)
        {
            //throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
            return false;
        }
        if(!is_array($update_data) || empty($update_data))
        {
            //throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
            return false;
        }
        return $this->update($update_data,"id={$id}");
    }


    /**
     * ������Ҫ��ӵ����ݲ������꣬����������ݺ���
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
     * ��ʼ����Ⱥ����Ϣ ����ʱֻ֧�ַ������֡�
     * @param int $send_user_id  ������ID
     * @param string $send_role  �����߽�ɫ
     * @param array $user_arr    �û�����,ʾ����array(100293,100580);
     * @param string $content    ���з��͵����� yueseller�������̼ҽ�ɫ����yuebuyer(�����߽�ɫ)
     * @param string $type       text|notify|card ����ʱֻ֧��text��ʽ���ı���ʽ��
     * @return array $return_data ����һ�������ʽΪarray(code=>0,'err'=>'')�����code=1��ʾ���ͳɹ�,���򷵻�ʧ�ܣ�err��ʾ����ʧ�ܵ���ʾ��Ϣ
     */
    /*public function start_mass_message($send_user_id,$send_role,$user_arr,$content,$type='text')
    {
        $post_data = array();
        $return_data['code'] = 0;
        $send_user_id = (int)$send_user_id;
        $send_role = trim($send_role);
        $content = trim($content);
        $type = trim($type);
        if($send_user_id <1) return $return_data['err'] = '�����û�IDΪ��';
        if(strlen($send_role) <1 || !in_array($send_role,$this->send_role_arr)) return $return_data['err'] = '��ɫ�Ƿ�';
        if(!is_array($user_arr) || empty($user_arr)) return $return_data['err'] = '�����߷Ƿ�';
        if(strlen($content) <1) return $return_data['err'] = '���ݷǷ�';
        if(strlen($type) <1 || !in_array($type,$this->type_arr)) return $return_data['err'] = '��ʽ�Ƿ�';
        //��ɫת�����ѷ����ߵĽ�ɫƥ�����ΪĿ���ߵĽ�ɫ
        if($send_role == 'yuebuyer')
        {
            $send_role = 'yueseller';
        }else{
            $send_role = 'yuebuyer';
        }
        //�û�����ȥ�������ϵ��û��������Ͻ�ɫ
        $user_list = array();
        foreach($user_arr as $user_id)
        {
            $user_id = (int)$user_id;
            if($user_id >=100000) $user_list[] = "{$send_role}/{$user_id}";
        }
        if(!is_array($user_list) || empty($user_list)) return $return_data['err'] = '���������߷Ƿ�';
        //�������������
        $duration_start_time = date('Ymd',time());
        $duration_end_time = date('Ymd',time()+2*3600);//Ϊ�˷�ֹ�պ�12�㷢�ͷ�ֹ��ʱ
        $valid_time = '000000-235959'; //��������
        $duration = "{$duration_start_time}-$duration_end_time";//����ʱ��
        $post_data['type'] = $type;
        $post_data['valid_time'] = $valid_time;
        $post_data['duration'] = $duration;
        $post_data['user_list'] = $user_list;//�û�����
        $post_data['send_uid'] = $send_role;
        $post_data['send_uid'] = trim($send_user_id);
        $post_data['only_send_online'] = 0;
        $post_data['auto_send'] = 1;
        $post_data['content'] = $this->gbk_to_utf8($content);
        //���log
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
        //�жϷ������Ƿ�ɹ�
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
        return $return_data['err'] = '�ύ���ݵĸ�ʽ����';
    }*/

    /**
     * ��ʼ����Ⱥ����Ϣ ����ʱֻ֧�ַ������֡�
     * @param int $send_user_id  ������ID
     * @param string $send_role  �����߽�ɫ
     * @param array $user_arr    �û�����,ʾ����array(100293,100580);
     * @param string $content    ���з��͵����� yueseller�������̼ҽ�ɫ����yuebuyer(�����߽�ɫ)
     * @param string $type       text|notify|card
     * @param string $link_url      �ƶ�������
     * @param string $wifi_url      wifi������
     * @param string $card_style    ��Ƭ��ʽ2|1
     * @param string $card_title    ��Ƭ����
     * @param string $card_text1    ��Ƭ����
     * @param string $card_text2    ����2
     * @return string
     */
    public function start_mass_message($send_user_id,$send_role,$user_arr,$content,$type='text',$link_url,$wifi_url,$card_style,$card_title,$card_text1,$card_text2)
    {
        //���log�����Ƿ�ɹ�
        $this->add_send_message_info_log($send_user_id,$send_role,$user_arr,$content,$type,$link_url,$wifi_url,$card_style,$card_title,$card_text1,$card_text2);

        $post_data = array();
        $return_data['code'] = 0;
        //��������
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
        //������֤
        if($send_user_id <1) return $return_data['err'] = '�����û�IDΪ��';
        if(strlen($send_role) <1 || !in_array($send_role,$this->send_role_arr)) return $return_data['err'] = '��ɫ�Ƿ�';
        if(!is_array($user_arr) || empty($user_arr)) return $return_data['err'] = '�����߷Ƿ�';
        if(strlen($type) <1 || !in_array($type,$this->type_arr)) return $return_data['err'] = '��ʽ�Ƿ�';
        if($type == 'text' || $type == 'notify') //���ָ�ʽ����
        {
            if(strlen($content) <1) return $return_data['err'] = '���ݷǷ�';
            if($type == 'notify')
            {
                if(strlen($link_url) <1 || strlen($wifi_url) <1) return $return_data['err'] = '���ӷǷ�';
            }
        }
        if($type == 'card')//��Ƭ
        {
            if($card_style <1 || !in_array($card_style,$this->card_style_arr)) return $return_data['err'] = '��Ƭ���ͷǷ�';
            if(strlen($card_text1) <1 || strlen($card_title) <1) return $return_data['err'] = '��Ƭ���ݻ��߱��ⲻ��Ϊ��';
            if($card_style == 1)
            {
                if(strlen($card_text2) <1) return $return_data['err'] = '��Ƭ�����ⲻ��Ϊ��';
            }
        }

        //��ɫת�����ѷ����ߵĽ�ɫƥ�����ΪĿ���ߵĽ�ɫ
        if($send_role == 'yuebuyer')
        {
            $send_role = 'yueseller';
        }else{
            $send_role = 'yuebuyer';
        }
        //�û�����ȥ�������ϵ��û��������Ͻ�ɫ
        $user_list = array();
        foreach($user_arr as $user_id)
        {
            $user_id = (int)$user_id;
            if($user_id >=100000) $user_list[] = "{$send_role}/{$user_id}";
        }
        if(!is_array($user_list) || empty($user_list)) return $return_data['err'] = '���������߷Ƿ�';
        //�������������
        $duration_start_time = date('Ymd',time());
        $duration_end_time = date('Ymd',time()+2*3600);//Ϊ�˷�ֹ�պ�12�㷢�ͷ�ֹ��ʱ
        $valid_time = '000000-235959'; //��������
        $duration = "{$duration_start_time}-$duration_end_time";//����ʱ��
        $post_data['type'] = $type;
        $post_data['valid_time'] = $valid_time;
        $post_data['duration'] = $duration;
        $post_data['user_list'] = $user_list;//�û�����
        $post_data['send_uid'] = $send_role;
        $post_data['send_uid'] = trim($send_user_id);
        $post_data['only_send_online'] = 0;
        $post_data['auto_send'] = 1;
        //������
        if($type == 'text' || $type == 'notify')  //���ָ�ʽ
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
        if($type == 'card')  //��Ƭ����
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
        $retID = $this->add_mass_message_info($data); //���log
        $response = $this->curl_mass_message($data);  //��������
        //�жϷ������Ƿ�ɹ�
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
        return $return_data['err'] = '�ύ���ݵĸ�ʽ����';
    }

    /**
     * ��Ϣ�ύ����������
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
     * GBKת��UTF-8
     *
     * @param [string|array] $str �ַ����������� ��: $str ="hello world";
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