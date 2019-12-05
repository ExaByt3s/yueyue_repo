<?php
/**
 * @desc:   ԼԼ����Ա��ɫ��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   9:38
 * version: 1.0
 */
class pai_admin_role_index_class extends POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_user_library_db' );
    }

    /**
     * ���ý�ɫ��
     */
    private function  set_admin_role_tbl()
    {
        $this->setTableName( 'pai_admin_role_tbl' );
    }

    /**
     * ���ý�ɫ��������Ա��
     */
    private  function set_admin_ref_role_tbl()
    {
        $this->setTableName( 'pai_admin_index_ref_role_tbl' );
    }
    /**
     * ��ȡ��ɫ�б�
     * @param bool   $b_select_count �Ƿ��ѯ����(true��ʾ��ѯ����,false ��ѯ����)
     * @param string $where_str  ����
     * @param string $order_by  ����
     * @param string $limit   ѭ������
     * @param string $fields  ��ѯ�ֶ�
     * @return array|int
     */
    public function get_admin_role_index_list($b_select_count = false,$where_str = '', $order_by = 'sort DESC,role_id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_admin_role_tbl();
        if($b_select_count == true)
        {
            return $this->findCount ( $where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;
    }

    /**
     * �������
     * @param array $insert_data �������
     * @return int
     * @throws App_Exception
     */
    private function add_info($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        $this->set_admin_role_tbl();
        return $this->insert ( $insert_data );
    }

    /**
     * ��ӽ�ɫ
     * @param string $role_name  ��ɫ���� ����:'��������Ա'
     * @param int $sort      ���� ����:100
     * @param array $op_arr    Ȩ������ ����:array(1,2,3);
     * @return int           ����ֵ
     * @throws App_Exception
     */
    public function add_info_role($role_name,$sort=100,$op_arr)
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $role_name = trim($role_name);
        $sort = intval($sort);
        $data['role_name'] = $role_name;
        $data['sort'] = $sort;
        $retID = $this->add_info($data);
        $retID = intval($retID);
        if($retID >0)
        {
            $admin_op_obj->add_role_op($retID,$op_arr);
        }
        return intval($retID);
    }

    /**
     * ��Ӷ����û���ɫ���ݽ���ɫ�����û���
     * @param int $user_id �û�ID,ʾ��:100000
     * @param int $role_id  ��ɫһά����ID��ʾ��: array(1,2,3);
     * @return bool ����ֵ
     */
    private function add_admin_ref_role_info($user_id,$role_id)
    {
        $user_id = intval($user_id);
        $role_id = intval($role_id);
        if($user_id <1 || $role_id <1) return false;
        $this->set_admin_ref_role_tbl();
        $data['user_id'] = $user_id;
        $data['role_id'] = $role_id;
        $this->insert($data,'REPLACE');
        return true;
    }

    /**
     * ͨ���û�IDɾ���û����ɫ�Ĺ�������
     * @param int $user_id  �û�ID,ʾ��:100000
     * @return bool
     * @throws App_Exception
     */
    public function del_admin_ref_role_info($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->set_admin_ref_role_tbl();
        return $this->delete("user_id={$user_id}");
    }

    /**
     * ��Ӷ��� �û���ɫ���ݽ�����
     * @param int $user_id    �û�ID,ʾ��:100000
     * @param int $role_id ��ɫһά����ID��ʾ��: array(1,2,3);
     * @return bool  ����ֵ
     */
    public function add_admin_ref_role($user_id,$role_id)
    {
        $user_id = intval($user_id);
        $role_id = intval($role_id);
        if($user_id <1 || $role_id <1) return false;
        $this->add_admin_ref_role_info($user_id,$role_id);
    }

    /**
     * �޸��û���ɫ���ݽ�����
     * @param int $user_id  �û�ID,ʾ��:100000
     * @param int $role_id  ��ɫһά����ID��ʾ��:1
     * @return bool
     */
    public function update_admin_ref_role($user_id,$role_id)
    {
        $user_id = intval($user_id);
        $role_id = intval($role_id);
        if($user_id >0)
        {
            $this->del_admin_ref_role_info($user_id);
        }
        if($role_id >0)
        {
            $this->add_admin_ref_role_info($user_id,$role_id);
        }
       return true;
    }


    /**
     * ���ݽ�ɫID��ȡ��������
     * @param int $role_id
     * @return array|int ����ֵ
     */
    public function get_info_by_role_id($role_id)
    {
        $role_id = intval($role_id);
        if($role_id <1)
        {
           return 0;
        }
        $this->set_admin_role_tbl();
        return $this->find("role_id={$role_id}");
    }

    /**
     * ���ݽ�ɫID ��������
     * @param int $role_id  ��ɫID ����:1
     * @param array $update_data   �������ݵ�����
     * @return mixed
     * @throws App_Exception
     */
    public function update_info($role_id,$update_data)
    {
        $role_id = intval($role_id);
        if($role_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��ɫID����Ϊ��' );
        }
        if (empty ( $update_data ) || !is_array($update_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        $this->set_admin_role_tbl();
        return $this->update($update_data, "role_id={$role_id}");
    }

    /**
     * ���½�ɫ����
     * @param int $role_id       ��ɫID,ʾ��:$role_id=1
     * @param string $role_name  ��ɫ��,ʾ��:$role_name ='��������Ա'
     * @param int $sort          ����,ʾ��:$sort=10
     * @param array $op_arr      ����IDһά����,ʾ��:$op_arr = array(1,2,3);
     * @return int
     * @throws App_Exception
     */
    public function update_info_role($role_id,$role_name,$sort,$op_arr)
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $role_id = intval($role_id);
        $role_name = trim($role_name);
        $sort = intval($sort);
        if($role_id < 1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��ɫID����Ϊ��' );
        }
        $data['role_name'] = $role_name;
        $data['sort'] = $sort;
        $retID = $this->update_info($role_id,$data);
        if($retID >=0)
        {
            $admin_op_obj->update_role_op($role_id,$op_arr);
        }
        return true;
    }

    /**
     * ͨ����ɫIDɾ����������
     * @param int $role_id  ��ɫID,ʾ��:1
     * @return int
     * @throws App_Exception
     */
    public function del_info_by_role_id($role_id)
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $role_id = intval($role_id);
        if($role_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��ɫID����Ϊ��' );
        }
        $this->set_admin_role_tbl();
        $retID = $this->delete("role_id={$role_id}");
        if($retID >0)
        {
            //ɾ����ɫ������op����
            $admin_op_obj->del_op_id_by_role_id($role_id);
        }
        return intval($retID);
    }

    /**
     * ��ȡ����Ա������ɫ�б�
     * @param bool $b_select_count  $b_select_count �Ƿ��ѯ����(true��ʾ��ѯ����,false ��ѯ����)
     * @param int $user_id  �û�ID,ʾ����100000
     * @param int $role_id  ��ɫID,ʾ��:1
     * @param string $where_str ��ѯ����,ʾ��:$where_str ="user_id=100000";
     * @param string $order_by  ����,ʾ�� user_id DESC,role_id DESC
     * @param string $limit     ѭ������,ʾ��:0,100
     * @param string $fields    ��ѯ�ֶ�,ʾ��:* ��ȡ user_id,role_id
     * @return array|int
     */
    public function get_admin_ref_role_list($b_select_count = false,$user_id,$role_id,$where_str = '', $order_by = 'user_id DESC,role_id DESC', $limit = '0,20', $fields = '*')
    {
        $user_id = intval($user_id);
        $role_id = intval($role_id);
        if($user_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($role_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "role_id = {$role_id}";
        }
        $this->set_admin_ref_role_tbl();
        if($b_select_count == true)
        {
            return $this->findCount ( $where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;

    }

    /**
     * ͨ��user_id��ȡ������ɫ
     * @param int $user_id �û�ID
     * @return int|bool
     */
    public function get_admin_role_by_user_id($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->set_admin_ref_role_tbl();
        $row = $this->find("user_id={$user_id}",null,"role_id");
        return intval($row['role_id']);
    }

    /**
     * ��ȡ����Ա�б�
     * @param int $user_id �û�ID
     * @return array|int
     */
    public function get_role_sort_by_user_id($user_id =0)
    {
        $user_id = intval($user_id);
        $role_ret = $this->get_admin_role_index_list(false,'','sort DESC,role_id DESC','0,99999999','*'); //��ȡ��ɫ����
        if(!is_array($role_ret)) $role_ret = array();
        if($user_id <1) return $role_ret;
        $role_id = $this->get_admin_role_by_user_id($user_id);//��ѡ��Ľ�ɫ
        if($role_id <1) return $role_ret;
        //������Աѡ��Ľ�ɫ
        foreach($role_ret as &$v)
        {
            if($v['role_id']== $role_id) {
                $v['selected'] = true;
            }

        }
        return $role_ret;
    }

}