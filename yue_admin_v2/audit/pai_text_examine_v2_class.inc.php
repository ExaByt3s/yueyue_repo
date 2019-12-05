<?php
/**
 * @desc:   ���������, �������� ����_���� ʾ����text_examine_pass �������ͨ����
 * text_pass_to_del ͼƬ�����ͨ������ͨ��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/10
 * @Time:   09:38
 * version: 2.0
 */
class pai_text_examine_v2_class extends POCO_TDG
{
    private $type_arr = array();//��������
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
        $this->type_arr = array(
            0=>array('type'=> 'nickname_text','name'=>'�������ǳ�'),
            1=>array('type'=>'customer_remark','name'=>'�����߸��˼��')
        );
    }

    /**
     *  ����_������˱�
     */
    private function set_text_examine_tbl()
    {
        $this->setTableName( 'text_examine_log' );
    }

    /**
     * ��ȡ��������
     * @return array
     */
    public function get_type_list()
    {
        return $this->type_arr;
    }

    /**
     * ͨ���������ƻ�ȡ������
     * @param string $type
     * @return string
     */
    public function  get_role_by_type($type)
    {
        $type = trim($type);
        if(strlen($type) <1) return '';
        foreach($this->type_arr as $key=>$val)
        {
            if($type == $val['type']) return $this->type_arr[$key]['name'];
        }
    }

    /**
     * ����ͼƬͨ����
     * @param $date
     * @param bool $no_create_tbl Ϊtrue�����������ҷ���false
     * @return bool
     * @throws App_Exception
     */
    private function set_text_pass_tbl($date,$no_create_tbl = false)
    {
        $date = trim($date);
        if(strlen($date) <1 || date('Y-m-d',strtotime($date)) != $date) $date = date('Y-m-d',time());
        $table_num = date('Ym',strtotime($date));
        $table_name = "text_examine_pass_log_{$table_num}";
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if (empty($res) || !is_array($res)) //�����ڴ���
        {
            if($no_create_tbl == true) return false;//û������
            $sql_str = "CREATE TABLE IF NOT EXISTS {$this->_db_name}.{$table_name} (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `user_id` int(10) unsigned NOT NULL,
                      `type` varchar(50) NOT NULL,
                      `before_edit` text NOT NULL,
                      `after_edit` text NOT NULL,
                      `add_time` datetime NOT NULL,
                      `audit_id` int(10) unsigned NOT NULL,
                      `audit_time` datetime NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      KEY `audit_id` (`audit_id`)
                    ) ENGINE=InnoDB";
            $this->query($sql_str);
        }
        $this->setTableName( $table_name );
        return true;
    }


    /**
     * ����_����ɾ����
     * @param $date
     * @param bool $no_create_tbl Ϊtrue�����������ҷ���false
     * @return bool
     * @throws App_Exception
     */
    private function set_text_del_tbl($date,$no_create_tbl = false)
    {
        $date = trim($date);
        if(strlen($date) <1 || date('Y-m-d',strtotime($date)) != $date) $date = date('Y-m-d',time());
        $table_num = date('Ym',strtotime($date));
        $table_name = "text_examine_del_log_{$table_num}";
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if (empty($res) || !is_array($res)) //�����ڴ���
        {
            if($no_create_tbl == true) return false;//û������
            $sql_str = "CREATE TABLE IF NOT EXISTS {$this->_db_name}.{$table_name} (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `user_id` int(10) unsigned NOT NULL,
                      `type` varchar(50) NOT NULL,
                      `before_edit` text NOT NULL,
                      `after_edit` text NOT NULL,
                      `add_time` datetime NOT NULL,
                      `audit_id` int(10) unsigned NOT NULL,
                      `audit_time` datetime NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      KEY `audit_id` (`audit_id`)
                    ) ENGINE=InnoDB";
            $this->query($sql_str);
        }
        $this->setTableName( $table_name );
        return true;
    }

    #######################################################����˲��ֲ���##############################################################

    /**
     * ��ȡ_�������_�б�
     * @param bool $b_select_count true|false
     * @param string $type ����
     * @param int $user_id �û�ID,ʾ��:100000
     * @param string $where_str ������$where_str ="user_id=100000";
     * @param string $order_by  ����,$order_by="id DESC";
     * @param string $limit     ѭ����$limit="0,10"
     * @param string $fields    ��ѯ�ֶ�,fields="user_id,name";
     * @return array|int
     */
    public function get_text_examine_list($b_select_count = false,$type='',$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $this->set_text_examine_tbl();
        $type = trim($type);
        $user_id = intval($user_id);
        if(strlen($type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($user_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id={$user_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * @param $id
     * @return array|bool
     */
    private function get_text_examine_by_id($id)
    {
        $id = (int)$id;
        if($id <1) return false;
        $this->set_text_examine_tbl();
        return $this->find("id={$id}");
    }

    /**
     * @param $id
     * @return bool
     * @throws App_Exception
     */
    private function text_examine_del_by_id($id)
    {
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
        }
        $this->set_text_examine_tbl();
        return $this->delete("id={$id}");
    }

    #####################################################################���ͨ�����ֲ���####################################################################

    /**
     * ��ȡ_����ͨ��_�б�
     * @param bool $b_select_count  true|false
     * @param string $date  ����,ʾ����'2015-09-12'
     * @param string $type  ���ͣ�ʾ����'yueseller'
     * @param int $audit_id �����ID��
     * @param int $user_id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_text_pass_list($b_select_count = false,$date,$type,$audit_id=0,$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $date = trim($date);
        $type = trim($type);
        $audit_id = intval($audit_id);
        $user_id = intval($user_id);
        $retID = $this->set_text_pass_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        if(strlen($type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id={$audit_id}";
        }
        if($user_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id={$user_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * @param $id
     * @param $date
     * @return array|bool
     * @throws App_Exception
     */
    private function get_text_pass_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
        }
        $retID = $this->set_text_pass_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        return $this->find("id={$id}");
    }

    /**
     * @param $insert_data
     * @param $date
     * @return int
     * @throws App_Exception
     */
    private function text_insert_pass($insert_data,$date)
    {
        global $yue_login_id;
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
        }
        $this->set_text_pass_tbl($date);
        $insert_data['audit_time'] = date('Y-m-d H:i:s',time());
        $insert_data['audit_id'] = $yue_login_id;
        return $this->insert($insert_data);
    }

    /**
     * @param $data
     * @param string $date
     * @param bool $b_select_count
     * @return int
     * @throws App_Exception
     */
    private function text_insert_pass_info($data,$date ='',$b_select_count = false)
    {
        $date = trim($date);
        if(!is_array($data) || empty($data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
        }
        $insert_data['user_id'] = intval($data['user_id']);
        $insert_data['type'] = trim($data['type']);
        $insert_data['before_edit'] = $data['before_edit'];
        $insert_data['after_edit'] = $data['after_edit'];
        $insert_data['add_time'] = $data['add_time'];
        return $this->text_insert_pass($insert_data,$date);
    }

    /**
     * ͨ��ID_ɾ��ͨ�����ֵ�����
     * @param int $id
     * @param string $date
     * @return bool
     * @throws App_Exception
     */
    private function text_delete_pass_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
        }
        $this->set_text_pass_tbl($date,true);
        return $this->delete("id={$id}");
    }

    #####################################################################ɾ�����ֲ��ֲ���####################################################################

    /**
     * ��ȡ_����ɾ��_�б�
     * @param bool $b_select_count false|true
     * @param string $date  ����
     * @param string $type  ����
     * @param int $audit_id ���
     * @param int $user_id �û�ID
     * @param string $where_str  ����
     * @param string $order_by   ����
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_text_del_list($b_select_count = false,$date ='',$type ='',$audit_id=0,$user_id =0,$where_str ='', $order_by = 'id DESC',$limit = '0,10',$fields = '*')
    {
        $date = trim($date);
        $type = trim($type);
        $audit_id = intval($audit_id);
        $user_id = intval($user_id);
        $retID = $this->set_text_del_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        if($retID <1) return false;
        if(strlen($type)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type=:x_type";
            sqlSetParam($where_str,'x_type',$type);
        }
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id={$audit_id}";
        }
        if($user_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id={$user_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * ͨ��ID_��ȡɾ����һ������
     * @param int $id
     * @param string $date
     * @return array|bool
     * @throws App_Exception
     */
    private function get_text_del_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
        }
        $retID = $this->set_text_del_tbl($date,true);
        $retID = intval($retID);
        if($retID <1) return false;
        return $this->find("id={$id}");
    }

    /**
     * ͨ��ID_ɾ��ͨ�����ֵ�����
     * @param int $id
     * @param string $date
     * @return bool
     * @throws App_Exception
     */
    private function text_delete_del_by_id($id,$date)
    {
        $date = trim($date);
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
        }
        $this->set_text_del_tbl($date,true);
        return $this->delete("id={$id}");
    }

    /**
     * @param $insert_data
     * @param $date
     * @return int
     * @throws App_Exception
     */
    private function text_insert_del($insert_data,$date)
    {
        global $yue_login_id;
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
        }
        $this->set_text_del_tbl($date);
        $insert_data['audit_time'] = date('Y-m-d H:i:s',time());
        $insert_data['audit_id'] = $yue_login_id;
        return $this->insert($insert_data);
    }

    /**
     * @param $data
     * @param string $date
     * @param bool $b_select_count
     * @return int
     * @throws App_Exception
     */
    private function text_insert_del_info($data,$date ='',$b_select_count = false)
    {
        $date = trim($date);
        if(!is_array($data) || empty($data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
        }
        $insert_data['user_id'] = intval($data['user_id']);
        $insert_data['type'] = trim($data['type']);
        $insert_data['before_edit'] = $data['before_edit'];
        $insert_data['after_edit'] = $data['after_edit'];
        $insert_data['add_time'] = $data['add_time'];
        return $this->text_insert_del($insert_data,$date);
    }
    ##########################################��������########################################################################################

    /**
     * �����ִ����_��ͨ������
     * @param  array $ids ʾ����array(1,2,3);
     * @return array  $return_data = array('code'=>0,'err'=>''); ���code=1��ʾ�ɹ�������ʧ��
     * @throws App_Exception
     */
    public function text_examine_pass($ids)
    {
        $return_data = array('code'=>0);//����ֵ
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '������������Ϊ��';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_text_examine_by_id($id);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->text_insert_pass_info($ret);
                    $del_ret = $this->text_examine_del_by_id($id);
                    if(!$insert_ret || !$del_ret){//����ع�
                        POCO_TRAN::rollback($this->getServerId());
                    }else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //�����ύ
                    }

                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * �����ִ����_��ɾ������
     * @param  array $ids ʾ����array(1,2,3);
     * @return array  $return_data = array('code'=>0,'err'=>''); ���code=1��ʾ�ɹ�������ʧ��
     * @throws App_Exception
     */
    public function text_examine_del($ids)
    {
        $return_data = array('code'=>0);//����ֵ
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '������������Ϊ��';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_text_examine_by_id($id);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->text_insert_del_info($ret);
                    $del_ret = $this->text_examine_del_by_id($id);
                    if(!$insert_ret || !$del_ret){//����ع�
                        POCO_TRAN::rollback($this->getServerId());
                    }else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //�����ύ
                        $this->del_app_text_info($ret);
                    }

                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * ���ִ�ͨ����_ɾ������
     * @param array $ids  ʾ����array(1,2,3);
     * @param string $date
     * @return array  $return_data = array('code'=>0,'err'=>''); ���code=1��ʾ�ɹ�������ʧ��
     * @throws App_Exception
     */
    public function text_pass_to_del($ids,$date)
    {
        $return_data = array('code'=>0);//����ֵ
        $date = trim($date);
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '������������Ϊ��';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_text_pass_by_id($id,$date);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->text_insert_del_info($ret);
                    $del_ret = $this->text_delete_pass_by_id($id,$date);
                    if(!$insert_ret || !$del_ret){//����ع�
                        POCO_TRAN::rollback($this->getServerId());
                    }else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //�����ύ
                        $this->del_app_text_info($ret);
                    }
                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

    /**
     * ��ɾ������_��ͨ����������
     * @param array $ids
     * @param string $date
     * @return mixed
     * @throws App_Exception
     */
    public function text_del_to_pass($ids,$date)
    {
        $date = trim($date);
        if(!is_array($ids) || empty($ids)) return $return_data['err'] = '������������Ϊ��';
        foreach($ids as $id)
        {
            $id = intval($id);
            if($id >0)
            {
                POCO_TRAN::begin($this->getServerId());
                $ret = $this->get_text_del_by_id($id,$date);
                if(is_array($ret) && !empty($ret))
                {
                    $insert_ret = $this->text_insert_pass_info($ret,'',true);
                    $del_ret = $this->text_delete_del_by_id($id,$date);
                    if(!$insert_ret || !$del_ret){//����ع�
                        POCO_TRAN::rollback($this->getServerId());
                    } else
                    {
                        POCO_TRAN::commmit($this->getServerId()); //�����ύ
                        $this->recover_app_text_info($ret);//�ָ���������
                    }

                }
            }
        }
        $return_data['code'] = 1;
        return $return_data;
    }

   ###################################################ɾ��APP����#################################################
    /**
     * ɾ��������תվ
     * @param array $data ɾ��������
     * @return bool
     */
    private function del_app_text_info($data)
    {
        $type = trim($data['type']);
        if(strlen($type) <1) return false;
        if($type == 'nickname_text')//�������ǳ�
        {
           return $this->del_app_nickname($data['user_id'],$data['after_edit']);
        }
        elseif($type == 'customer_remark')//�����߼���޸�
        {
           return $this->del_app_intro($data['user_id'],$data['after_edit']);
        }
    }

    /**
     * �޸��������ǳ�����
     * @param int $user_id
     * @param $after_edit
     * @return bool
     */
    private function del_app_nickname($user_id,$after_edit)
    {
        $user_obj = POCO::singleton ('pai_user_class');
        $user_id = intval($user_id);
        $after_edit = trim($after_edit);
        if($user_id <1) return false;
        $user_res = $user_obj->get_user_info($user_id);
        if($user_res['nickname'] == $after_edit)
        {
            $nickname = '�ֻ��û�'.substr($user_res['cellphone'], -4);
            $user_obj->update_nickname($user_id,$nickname);
        }
        return true;
    }

    /**
     * ɾ��������ԭ����ע��Ϣ
     * @param int $user_id  �û�����
     * @param string $after_edit
     * @return bool
     */
    private function del_app_intro($user_id,$after_edit ='')
    {
        $user_obj = POCO::singleton ('pai_user_class');
        $user_id = intval($user_id);
        $after_edit = trim($after_edit);
        if($user_id <1) return false;
        $user_res = $user_obj->get_user_info($user_id);
        if($user_res['remark'] == $after_edit)
        {
            $update_data ['remark'] = '';
            $user_obj->update_user($update_data, $user_id);
        }
        unset($update_data);
        return true;
    }

    ###################################################ɾ���ָ�APP����#################################################

    /**
     * �ָ�������תվ
     * @param array $data �ָ�������
     * @return bool
     */
    private function recover_app_text_info($data)
    {
        $type = trim($data['type']);
        if(strlen($type) <1) return false;
        if($type == 'nickname_text')//�������ǳ�
        {
            return $this->recover_app_nickname($data['user_id'],$data['after_edit']);
        }
        elseif($type == 'customer_remark')//�����߼���޸�
        {
            return $this->recover_app_intro($data['user_id'],$data['after_edit']);
        }
    }

    /**
     * �ָ�������ԭ����ע��Ϣ
     * @param int $user_id  �û�����
     * @param string $after_edit
     * @return bool
     */
    private function recover_app_nickname($user_id,$after_edit ='')
    {
        $user_obj = POCO::singleton ('pai_user_class');
        $user_id = intval($user_id);
        $after_edit = trim($after_edit);
        if($user_id <1) return false;
        $user_res = $user_obj->get_user_info($user_id);
        $nickname = '�ֻ��û�'.substr($user_res['cellphone'], -4);
        if($user_res['nickname'] == $nickname)
        {
            $user_obj->update_nickname($user_id,$after_edit);
        }
        return true;
    }

    /**
     * �ָ�������ԭ����ע��Ϣ
     * @param int $user_id  �û�����
     * @param string $after_edit
     * @return bool
     */
    private function recover_app_intro($user_id,$after_edit ='')
    {
        $user_obj = POCO::singleton ('pai_user_class');
        $user_id = intval($user_id);
        $after_edit = trim($after_edit);
        if($user_id <1) return false;
        $user_res = $user_obj->get_user_info($user_id);
        if($user_res['remark'] == '')
        {
            $update_data ['remark'] = $after_edit;
            $user_obj->update_user($update_data, $user_id);
        }
        unset($update_data);
        return true;
    }


}