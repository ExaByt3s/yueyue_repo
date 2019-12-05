<?php
/**
 * @desc:   �ٱ��ͺ�������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/28
 * @Time:   15:59
 * version: 1.0
 */
class pai_log_inform_v2_class extends POCO_TDG
{
    /**
     * @var string ������ʾ��Ϣ����
     */
    private $send_message = '�𾴵�ԼԼ�û������������ʺ����ӷ���������Ϣ���û��ٱ���ϵͳ�������������͵��κ���Ϣ���������ߣ��벦�� 400-082-9003 ��ϵ�ͷ�����';

    /**
     * @var string
     */
    private $cause_str = '����Ա�ֶ�����';
    /**
     * ���캯��
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
    }

    /**
     * ���þٱ���
     */
    private function set_inform_tbl()
    {
        $this->setTableName( 'pai_examine_inform_tbl' );
    }

    /**
     *  ���ú�������
     */
    private function set_inform_shield_tbl()
    {
        $this->setTableName( 'pai_examine_inform_shield_tbl' );
    }

    /**
     * ��ȡ�ٱ��б�����
     * @param bool $b_select_count �����Ҫ��ȡ������ʹ��true��������дfalse
     * @param int $by_informer_id  �ٱ���ID��ʾ����100293
     * @param int $to_informer_id  ���ٱ���ID��ʾ����100580
     * @param string $where_str    ��ѯ������ʾ����$where_str ="DATE_FORMAT(add_time,'%Y-%m-%d')>='2015-10-28'"
     * @param string $order_by     ����
     * @param string $limit        ѭ���������±��0��ʼѭ��10��ʾ����"0,10"
     * @param string $fields       ��ѯ�ֶΣ�ʾ����$fields="id"����$fields="id,add_time"
     * @return array|int           ����ֵ
     */
    public function get_inform_list($b_select_count = false,$by_informer_id,$to_informer_id,$where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_inform_tbl();
        $by_informer_id = (int)$by_informer_id;
        $to_informer_id = (int)$to_informer_id;
        if($by_informer_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "by_informer={$by_informer_id}";
        }
        if($to_informer_id>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "to_informer={$to_informer_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str);
        }
        $ret = $this->findAll($where_str, $limit, $order_by, $fields);
        return $ret;
    }


    /**
     * ͨ������ID����ȡ�ٱ���Ϣ
     * @param int $id ����ID
     * @return array|bool
     */
    public function get_inform_info($id)
    {
        $id = (int)$id;
        if($id <1) return false;
        $this->set_inform_tbl();
        return $this->find("id = {$id}");
    }

    /**
     * ����û����ٱ�������
     * @param array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_inform_info($insert_data)
    {
        if (empty($insert_data) || !is_array($insert_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
        }
        $this->set_inform_tbl();
        return $this->insert($insert_data);
    }

    /**
     * ͨ���û�IDƴ������׼����ӵ��ٱ�����
     * @param int $user_id ���ٱ����û�ID
     * @param string $data_str ����Ա��Ӻ�������ע
     * @return int
     * @throws App_Exception
     */
    public function add_inform_by_user_id($user_id,$data_str ='')
    {
        global $yue_login_id;
        $user_id = (int)$user_id;
        $data_str = trim($data_str);
        if($user_id <1)return false;
        $data['by_informer'] = $yue_login_id;
        $data['to_informer'] = $user_id;
        $data['cause_str'] = trim($this->cause_str);
        $data['state'] = 1;
        $data['data_str'] = trim($data_str);
        $data['add_time'] = date('Y-m-d H:i:s', time());
        return $this->add_inform_info($data);
    }

    /**
     * ͨ�����ٱ���ID����������
     * @param array $data
     * @param int $to_informer �ٱ���ID
     * @return mixed
     * @throws App_Exception
     */
    private function update_info($data, $to_informer)
    {
        if (empty($data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
        }
        $to_informer = (int)$to_informer;
        if ($to_informer <1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
        }
        $this->set_inform_tbl();
        $where_str = "to_informer = {$to_informer}";
        $ret = $this->update($data, $where_str);
        return $ret;
    }

    /**
     * �Ӿٱ��е��������������
     * @param int $id
     * @param string $reason
     * @return string
     * @throws App_Exception
     */
    public function update_inform_by_id($id,$reason)
    {
        $return_data = array('code'=>0);//����ֵ
        $id = (int)$id;
        $reason = trim($reason);
        if($id <1) return $return_data['err'] = 'ID����Ϊ��';
        //��ȡ���ٱ��ߵ�ID
        $ret = $this->get_inform_info($id);
        $to_informer = (int)$ret['to_informer'];
        if($to_informer <1) return $return_data['err']= '���ٱ���ID��ȡ����';
        POCO_TRAN::begin($this->getServerId());//��������
        $ret = $this->update_info(array('state'=>1),$to_informer); //�޸ľٱ���״̬
        $sheld_result = $this->insert_inform_shield_by_to_informer($to_informer,$id,$reason);

        if($ret && $sheld_result )//�ٱ���ͺ��������ɹ�
        {
            POCO_TRAN::commmit($this->getServerId());//�ύ����
            $send_msg = trim($this->send_message);
            send_message_for_10002($to_informer, $send_msg, '', 'all', 'sys_msg');
            $return_data['code'] = 1;
            return $return_data;
        }
        //��������ع���������false
        POCO_TRAN::rollback($this->getServerId());
        return $return_data['err']= 'ϵͳ�ύʧ��';
    }

    ##############################################################��ȡ����������##########################################################

    /**
     * ��ȡ�������б�
     * @param bool $b_select_count true|false
     * @param int $user_id  ������ID
     * @param int $audit_id  �����ID
     * @param string $where_str ����
     * @param string $order_by ����ʽ
     * @param string $limit ѭ��������ʾ����'0,10'
     * @param string $fields   ��ѯ�ֶ�
     * @return array|int
     */
    public function get_sheld_list($b_select_count = false,$user_id,$audit_id,$where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_inform_shield_tbl();
        $user_id = (int)$user_id;
        $audit_id = (int)$audit_id;
        if($user_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($audit_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "audit_id={$audit_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * ͨ�����ٱ���ID��ȡ�Ƿ�����������
     * @param int $to_informer_id
     * @return bool �������������true�����򷵻�false
     */
    public function get_info_by_to_informer_id($to_informer_id)
    {
        $to_informer_id = (int)$to_informer_id;
        if($to_informer_id <1) return false;
        $this->set_inform_shield_tbl();
        $ret = $this->find("user_id={$to_informer_id}");
        if (is_array($ret) && !empty($ret)) return true;
        return false;
    }

    /**
     * �������ݵ���������
     * @param  array $insert_data
     * @return bool
     * @throws App_Exception
     */
    private function insert_sheld_info($insert_data)
    {
        if (empty($insert_data) || !is_array($insert_data))
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
        }
        $this->set_inform_shield_tbl();
        $this->insert($insert_data,"IGNORE");
        return true;
    }

    /**
     * ������Ҫ��������ݲ��ұ����ݲ��뵽��������
     * @param int $to_informer ���ٱ����û�
     * @param int $id   �ٱ�����ID
     * @param string $reason  ���������ԭ��˵����
     * @return bool
     * @throws App_Exception
     */
    public function insert_inform_shield_by_to_informer($to_informer,$id,$reason)
    {
        global $yue_login_id;
        $data = array();
        $to_informer = (int)$to_informer;
        $id = (int)$id;
        $reason = trim($reason);
        if($to_informer <1) return false;
        $data['reason'] = $reason;
        $data['user_id'] = $to_informer;
        $data['inform_id'] = $id;
        $data['add_time'] = time();
        $data['audit_id'] = $yue_login_id;
        $ret = $this->insert_sheld_info($data);//�������ݽ�������
        $ser_ret = $this->shield_user_v2($to_informer);
        if($ret && $ser_ret) return true;
        return false;
    }

    /**
     * ͨ��������ID��ȡ����������
     * @param int $id ����������ID
     * @return array|bool
     */
    public function get_shield_by_id($id)
    {
        $id = (int)$id;
        if($id <1)return false;
        $this->set_inform_shield_tbl();
        return $this->find("id={$id}");
    }

    /**
     * ͨ������������IDɾ������
     * @param int $id  ����ID
     * @return bool
     * @throws App_Exception
     */
    public function delete_shield_by_id($id)
    {
        $id = (int)$id;
        if ($id<1)
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
        }
        $this->set_inform_shield_tbl();
        return $this->delete("id={$id}");
    }

    /**
     * ��������ID�;ٱ�ID,��ɾ�����������ͷ���������������
     * @param int $id ����ID
     * @param int $user_id  ���ٱ����ߵ�ID
     * @return bool
     * @throws App_Exception
     */
    public function delete_shield_by_id_info($id,$user_id)
    {
        $id = (int)$id;
        $user_id = (int)$user_id;
        if($id <1 || $user_id <1) return false;
        $result = $this->delete_shield_by_id($id);
        $server_result = $this->shield_user_v2($user_id,'remove');
        if($result && $server_result) return true;
        return false;
    }

    /**
     * �Ƴ�������
     * @param int $id �Ƴ�������ID��������������ID
     * @return array|string
     * @throws App_Exception
     */
    public function mouve_out_blacklist_by_id($id)
    {
        $return_data = array('code'=>0);//����ֵ
        $id = (int)$id;
        if($id <1) return $return_data['err'] = '�Ƿ�����';
        $ret = $this->get_shield_by_id($id);
        if(!is_array($ret)) $ret = array();
        $user_id = (int)$ret['user_id'];
        if($user_id <1) return $return_data['err'] = '�����������ڸ��û�';
        POCO_TRAN::begin($this->getServerId());//��������
        $ret = $this->update_info(array('state'=>0), $user_id);
        $delete_ret = $this->delete_shield_by_id_info($id,$user_id);
        if($ret && $delete_ret)
        {
            POCO_TRAN::commmit($this->getServerId());//�ύ����
            $return_data['code'] = 1;
            return $return_data;
        }
        POCO_TRAN::rollback($this->getServerId()); //�ع�����
        return $return_data['err'] = 'ϵͳ�ύ����';
    }

    /**
     * ����Ա�ֶ������û�
     * @param int $user_id ��Ҫ�ֶ����ڵ��û�ID
     * @param int $data_str ����Ա��Ӻ�������ע
     * @return array|string
     */
    public function admin_add_user_id_into_blacklist($user_id,$data_str)
    {
        $return_data = array('code'=>0);//����ֵ
        $user_id = (int)$user_id;
        $data_str = trim($data_str);
        if($user_id <1) return $return_data['err'] = '�û�ID����Ϊ��';
        if($user_id <100000) return $return_data['err'] = '���޷�����ϵͳ�û�';

        POCO_TRAN::begin($this->getServerId());//��������
        $inform_id = $this->add_inform_by_user_id($user_id,$data_str);//��Ӿٱ�
        $this->update_info(array('state'=>1),$user_id); //����ٱ��������û�ID,�޸ľٱ���״̬
        $shield_result = $this->insert_inform_shield_by_to_informer($user_id,$inform_id,$this->cause_str);//����˺�����
        if($inform_id && $shield_result)
        {
            POCO_TRAN::commmit($this->getServerId());//�ύ����
            $send_msg = trim($this->send_message);
            send_message_for_10002($user_id, $send_msg, '', 'all', 'sys_msg');
            $return_data['code'] = 1;
            return $return_data;
        }
        POCO_TRAN::rollback($this->getServerId()); //�ع�����
        return $return_data['err'] = 'ϵͳ�ύ����';
    }


    ####################################################�������˴���###########################################################################
    /**
     * ���û�����������еĺ�������
     * @param $user_id  $user_id [�û���ID]
     * @param string $type ����
     * @param string $role yueseller yuebuyer both
     * @return bool
     */
    private function shield_user_v2($user_id, $type = 'add',$role='both')
    {
        $user_id = (int)$user_id;
        $type = trim($type);
        $role = trim($role);
        if (!$user_id){return false;}
        $gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.211:9830");
        $gmclient->setTimeout(5000); // ���ó�ʱ
        do
        {
            $req_param['uid'] = $user_id;
            $req_param['role'] = $role;
            if($type == 'add')
            {
                $gmclient->doBackground("add_blacklist",json_encode($req_param) );
            }
            else
            {
                $gmclient->doBackground("del_blacklist",json_encode($req_param) );
            }
        }
        while(false);
        //while($gmclient->returnCode() != GEARMAN_SUCCESS);
        return true;
    }
}