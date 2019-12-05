<?php
/**
 * @desc:   ԼԼ����Ա��V2
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/27
 * @Time:   15:53
 * version: 1.0
 */
class pai_admin_index_class extends POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_user_library_db' );
        $this->setTableName ( 'pai_admin_index_tbl' );
    }

    /**
     * ��ȡ����Ա�б�
     * @param bool $b_select_count
     * @param int $user_id �û�ID ����:100000
     * @param int $status  �û�״̬ Ĭ��ֵΪȫ��(-1��ʾȫ����0�ر��У�1��ʾ����)
     * @param string $where_str  ��ѯ����
     * @param string $order_by   ����
     * @param string $limit      ѭ������ 0,10
     * @param string $fields    �ֶ�����Ĭ��*������'user_id,status'
     * @return array|int        ����ֵ
     */
    public function get_admin_index_list($b_select_count = false,$user_id,$status =-1,$where_str = '', $order_by = 'add_time DESC,user_id DESC', $limit = '0,20', $fields = '*')
    {
        $user_id = intval($user_id);
        $status = intval($status);
        if($user_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($status >=0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "status = {$status}";
        }
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
        return $this->insert ($insert_data,'REPLACE');
    }

    /**
     * ͨ���û�ID ɾ������
     * @param int $user_id
     * @return bool
     * @throws App_Exception
     */
    private function del_info($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
        }
        return $this->delete("user_id={$user_id}");
    }

    /**
     * ͨ���û�IDɾ������Ա��һ������
     * @param int $user_id
     * @return bool
     * @throws App_Exception
     */
    public function del_admin_index_by_user_id($user_id)
    {
        $admin_role_index_obj = POCO::singleton( 'pai_admin_role_index_class' );
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $user_id = intval($user_id);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
        }
        $retID = $this->del_info($user_id);
        if($retID >0)
        {
            $admin_role_index_obj->del_admin_ref_role_info($user_id);
            $admin_op_obj->del_op_ref_by_user_id($user_id);
        }
        return $retID;
    }

    /**
     * ��ӹ���Ա
     * @param int $user_id �û�ID ����:100000
     * @param int $status  �û�״̬ Ĭ��ֵΪȫ��(-1��ʾȫ����0�ر��У�1��ʾ����)
     * @param string $real_name ��ʵ����
     * @param string $department ���ڲ���
     * @param array $role_id ��ɫһά����
     * @param array $op_arr  ����һά����
     * @return int
     * @throws App_Exception
     */
    public function  add_info_index($user_id,$status,$real_name,$department,$role_id,$op_arr)
    {
        $admin_role_index_obj = POCO::singleton( 'pai_admin_role_index_class' );
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $user_id = intval($user_id);
        $status = intval($status);
        $real_name = trim($real_name);
        $department = trim($department);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
        }
        $data['user_id'] = $user_id;
        $data['status'] = $status;
        $data['real_name'] = $real_name;
        $data['department'] = $department;
        $data['add_time'] = time();
        $retID = $this->add_info($data);
        if($retID >=0)
        {
            $admin_role_index_obj->add_admin_ref_role($user_id,$role_id);
            $admin_op_obj->add_op_ref_user($user_id,$op_arr);
        }
        return true;
    }

    /**
     * �����û�ID��ȡ��������
     * @param int $user_id �û�ID ����:100000
     * @return array|bool
     */
    public function get_info_by_user_id($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        return $this->find("user_id={$user_id}");
    }

    /**
     * �����û�ID ��������
     * @param int $user_id  �û�ID ����:100000
     * @param array $update_data   �������ݵ�����
     * @return mixed
     * @throws App_Exception
     */
    public function update_info($user_id,$update_data)
    {
        $user_id = intval($user_id);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
        }
        if (empty ( $update_data ) || !is_array($update_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        return $this->update($update_data, "user_id={$user_id}");
    }

    /**
     * ���¹���Ա����
     * @param int $user_id �û�ID ����:100000
     * @param int $status  �û�״̬ Ĭ��ֵΪȫ��(-1��ʾȫ����0�ر��У�1��ʾ����)
     * @param string $real_name ��ʵ����
     * @param string $department ���ڲ���
     * @param int $role_id
     * @param array $op_arr
     * @return int
     * @throws App_Exception
     */
    public function update_info_index($user_id,$status,$real_name,$department,$role_id,$op_arr)
    {
        $admin_role_index_obj = POCO::singleton( 'pai_admin_role_index_class' );
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $user_id = intval($user_id);
        $status = intval($status);
        $real_name = trim($real_name);
        $department = trim($department);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
        }
        $data['status'] = $status;
        $data['real_name'] = $real_name;
        $data['department'] = $department;
        $retID = $this->update_info($user_id,$data);
        if($retID >=0)
        {
            $admin_role_index_obj->update_admin_ref_role($user_id,$role_id);
            $admin_op_obj->update_op_ref_user($user_id,$op_arr);
        }
        return true;
    }
}