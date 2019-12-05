<?php
/**
 * @desc:   ����Ա������,���й�������Ա�����������ɫ����������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   11:17
 * version: 1.0
 */
class pai_admin_op_class extends POCO_TDG
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
     * ���ù���Ա������
     */
    private function set_admin_op_tbl()
    {
        $this->setTableName ( 'pai_admin_op_tbl' );
    }

    /**
     *����Ա���������
     */
    private function set_admin_ref_op_tbl()
    {
        $this->setTableName( 'pai_admin_index_ref_op_tbl' );
    }
    /**
     * ���ý�ɫ����������
     */
    private function set_role_ref_op_tbl()
    {
        $this->setTableName( 'pai_admin_role_ref_op_tbl' );
    }

    ##########################������ķ���#################################################
    /**
     * ��ȡ�����б�
     * @param bool $b_select_count �Ƿ��ѯ����(true��ʾ��ѯ����,false ��ѯ����)
     * @param int $op_id        ����ID 1
     * @param int $parent_id    ��������ID
     * @param string $where_str ���� $where_str ="user_id={$user_id}";
     * @param string $order_by  ����
     * @param string $limit    ѭ������
     * @param string $fields   ��ѯ�ֶ�
     * @return array|int
     */
    public function get_op_list($b_select_count = false,$op_id,$parent_id,$where_str = '', $order_by = 'sort DESC,op_id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_admin_op_tbl();
        $op_id = intval($op_id);
        $parent_id = intval($parent_id);
        if($op_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_id = {$op_id}";
        }
        if($parent_id >=0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "parent_id={$parent_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;

    }

    /**
     * ͨ������ID��ȡ��������
     * @param $op_id ����ID ����1
     * @return array
     * @throws App_Exception
     */
    public function  get_op_info_by_op_id($op_id)
    {
        $op_id = intval($op_id);
        if($op_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
        }
        $this->set_admin_op_tbl();
        return $this->find("op_id={$op_id}");
    }

    /**
     * ��ȡ����ѡ�����
     * @param int $selected
     * @param int $parent_id
     * @param int $n
     * @return string
     */
    public function get_op_sort_option($selected=0,$parent_id=0,$n=-1)
    {
        $options = '';
        static $i = 0;
        if ($i == 0)
        {
            $options .= "<option value='0'>== �������� ==</option>";
        }
        $res = $this->get_op_list(false,0,$parent_id,'','sort DESC,op_id DESC','0,99999999','*');
        if(!is_array($res)) $res = array();
        $n++;
        foreach($res as $key=>$val)
        {
            $i++;
            $options .="<option value='{$val['op_id']}'";
            if ($val['op_id'] == $selected)
            {
                $options .=' selected ';
            }
            $val['parent_id'] != 0 ? $options .=">".str_repeat('&nbsp;&nbsp;&nbsp;',$n).'|-'.$val['op_name']."</option>\n" : $options .=">".str_repeat('&nbsp;&nbsp;&nbsp;',$n).$val['op_name']."</option>\n";
            $options .= self::get_op_sort_option($selected,$val['op_id'],$n);
        }
        return $options;
    }

    /**
     * ��ȡ���в�ѯ���б����ݣ��Ա��ķ�ʽչʾ����
     * @param int $parent_id ����ID
     * @param int $n         �������
     * @return string
     */
    public function get_op_sort_list($parent_id=0,$n=-1)
    {
        $str = '';
        static $i = 0;
        $res = $this->get_op_list(false,0,$parent_id,'','sort DESC,op_id DESC','0,99999999','*');
        if(!is_array($res)) $res = array();
        $n++;
        foreach($res as $key=>$val)
        {
            $i++;
            $is_nav = $val['op_is_nav'] ==1 ? '��':'��';
            $str .="<tr><td align='center'>{$val['op_id']}</td><td>";
            $val['parent_id'] != 0 ? $str .=str_repeat('&nbsp;&nbsp;',$n).'|-'.$val['op_name']."</td>\n" : $str .=str_repeat('&nbsp;&nbsp;',$n).$val['op_name']."</td>\n";
            $str .= "<td align='center'>{$val['op_code']}</td>";
            $str .= "<td align='center'>{$val['op_url']}</td>";
            $str .= "<td align='center'>{$val['op_level']}</td>";
            $str .= "<td align='center'>{$is_nav}</td>";
            $str .= "<td align='center'><input type='text' value='{$val['sort']}' name='op[{$val['op_id']}]' class='input-text' size='4'/></td>";
            $str .= "<td align='center'><a href='admin_op_edit.php?op_id=".$val['op_id']."&act=edit'>�༭</a>&nbsp;|&nbsp;<a href='admin_op_edit.php?op_id=".$val['op_id']."&act=del'>ɾ��</a></td></tr>";
            $str .= self::get_op_sort_list($val['op_id'],$n);
        }
        return $str;

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
        $this->set_admin_op_tbl();
        return $this->insert ( $insert_data );
    }

    /**
     * ��Ӳ���
     * @param int $op_type_id ��������
     * @param string $op_name   ������
     * @param string $op_code   ���������
     * @param string $op_url    ��������
     * @param int $parent_id    ����ID û�и���������Ϊ0
     * @param int $sort         ����
     * @param int $op_is_nav    �Ƿ���ʾ���˵�
     * @param array $option      ��չ����
     * @return int              ����ֵ
     * @throws App_Exception
     */
    public function add_info_op($op_type_id,$op_name,$op_code,$op_url,$parent_id = 0,$sort=100,$op_is_nav,$option = array())
    {
        $op_type_id = intval($op_type_id);
        $op_name = trim($op_name);
        $op_code = trim($op_code);
        $op_url = trim($op_url);
        $parent_id = intval($parent_id);
        $option = (array)$option;
        $op_level = 1;
        if($parent_id > 0) {//��ȡ����
            $row = $this->get_op_info_by_op_id($parent_id);
            if(!is_array($row)) $row = array();
            $op_level = intval($row['op_level'])+1;
        }
        $sort = intval($sort);
        $op_is_nav = intval($op_is_nav);
        $data['op_type_id'] = $op_type_id;
        $data['op_name'] = $op_name;
        $data['op_code'] = $op_code;
        $data['op_url'] = $op_url;
        $data['parent_id'] = $parent_id;
        $data['op_level'] = $op_level;
        $data['sort'] = $sort;
        $data['op_is_nav'] = $op_is_nav;
        $data['op_location'] = trim($option['op_location']);
        $retID = $this->add_info($data);
        return intval($retID);
    }


    /**
     * ͨ����������ID��ȡ��������
     * @param $parent_id ����ID ����1
     * @return array
     * @throws App_Exception
     */
    public function  get_op_info_by_parent_id($parent_id)
    {
        $parent_id = intval($parent_id);
        if($parent_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
        }
        $this->set_admin_op_tbl();
        return $this->find("parent_id={$parent_id}");
    }

    /**
     * ͨ������IDɾ����������
     * @param $op_id ����ID ����1
     * @return bool
     */
    public function del_op_info_by_op_id($op_id)
    {
        $op_id = intval($op_id);
        if($op_id <1) return false;
        $this->set_admin_op_tbl();
        $retID = $this->delete("op_id={$op_id}");
        return intval($retID);
    }

    /**
     * ���ݲ���ID ��������
     * @param int $op_id  ����ID ����:1
     * @param array $update_data   �������ݵ�����
     * @return mixed
     * @throws App_Exception
     */
    private function update_info($op_id,$update_data)
    {
        $op_id = intval($op_id);
        if($op_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
        }
        if (empty ( $update_data ) || !is_array($update_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        $this->set_admin_op_tbl();
        return $this->update($update_data, "op_id={$op_id}");
    }


    /**
     * ��������
     * @param array $op ����ID�������ȹ�����һά����,ʾ��array(1=>3,2=>4,3=>8),1Ϊop_id,3Ϊsort
     * @return bool
     * @throws App_Exception
     */
    public function op_id_sort_again($op)
    {
       if(!is_array($op) || empty($op)) return false;
        $data = array();
        foreach($op as $op_id=>$sort)
        {
            $op_id = intval($op_id);
            $data['sort'] = intval($sort);
            $this->update_info($op_id,$data);
        }
        return true;
    }


    /**
     * ���� ����
     * @param int $op_id   ����ID,ʾ��1
     * @param int $op_type_id  ����,ʾ��1
     * @param string $op_name
     * @param string $op_code
     * @param string $op_url
     * @param int $parent_id
     * @param  int $sort
     * @param  int $op_is_nav
     * @param  array $option
     * @return int
     * @throws App_Exception
     */
    public function update_info_op($op_id,$op_type_id,$op_name,$op_code,$op_url,$parent_id,$sort,$op_is_nav,$option)
    {
        $op_id = intval($op_id);
        $op_type_id = intval($op_type_id);
        $op_name = trim($op_name);
        $parent_id = trim($parent_id);
        $op_code = trim($op_code);
        $op_url = trim($op_url);
        $sort = intval($sort);
        $op_is_nav = intval($op_is_nav);
        $option = (array)$option;
        if($op_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
        }
        $op_level = 1;
        if($parent_id > 0) {//��ȡ����
            $row = $this->get_op_info_by_op_id($parent_id);
            if(!is_array($row)) $row = array();
            $op_level = intval($row['op_level'])+1;
        }
        $data['op_type_id'] = $op_type_id;
        $data['op_name'] = $op_name;
        $data['op_code'] = $op_code;
        $data['op_url'] = $op_url;
        $data['parent_id'] = $parent_id;
        $data['op_level'] = $op_level;
        $data['sort'] = $sort;
        $data['op_is_nav'] = $op_is_nav;
        $data['op_location'] = $option['op_location'];
        $retID = $this->update_info($op_id,$data);
        return intval($retID);
    }


    /**
     * �жϸ��������Ƿ���� һ��$op_id,�����޷�ѡ�������������
     * @param int $parent_id ����ID
     * @return array $arr = array(1,3,3);
     */
    public function is_check_parent_id($parent_id)
    {
        static $arr = array();
        $parent_id = intval($parent_id);
        if($parent_id <1) return array();
        $this->set_admin_op_tbl();
        $list = $this->get_op_list(false,0,$parent_id,'','sort DESC,op_id DESC','0,99999999','op_id');
        if(!is_array($list)) $list = array();
        foreach($list as $v)
        {
            $arr[] = $v['op_id'];
            self::is_check_parent_id($v['op_id']);
        }
       return $arr;
    }

    ############################������ķ���#########################################################################

    /**
     * ��ȡ��ɫ�����Ĳ�������   �Ƿ��ѯ����(true��ʾ��ѯ����,false ��ѯ����)
     * @param bool $b_select_count
     * @param int $role_id ��ɫID,ʾ��:2
     * @param int $op_id   ����ID,ʾ��:3
     * @param string $where_str ��ѯ����,ʾ��:$where_str ="role_id=1";
     * @param string $order_by  ����,ʾ��:$order_by="role_id DESC";
     * @param string $limit     ѭ������,ʾ��:0,10
     * @param string $fields    ��ѯ�ֶΣ�ʾ��:'op';
     * @return array|int
     */
    public function get_op_ref_role($b_select_count = false,$role_id,$op_id,$where_str = '', $order_by = 'role_id DESC,op_id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_role_ref_op_tbl();
        $role_id = intval($role_id);
        $op_id = intval($op_id);
        if($role_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "role_id = {$role_id}";
        }
        if($op_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "op_id = {$op_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;
    }

    /**
     * ��ȡ����ѡ�����
     * @param array $selected
     * @param int $parent_id
     * @param int $n
     * @return string
     */
    public function get_op_option_list($selected = array(),$parent_id=0,$n=-1)
    {
        if(!is_array($selected)) $selected = array();
        $options = '';
        static $i = 0;
        $res = $this->get_op_list(false,0,$parent_id,'','sort DESC,op_id DESC','0,99999999','*');
        if(!is_array($res)) $res = array();
        $n++;
        foreach($res as $val)
        {
            $i++;
            $options .= "<p>";
            $val['parent_id'] != 0 ? $options .= str_repeat('&nbsp;&nbsp;&nbsp;',$n)."|-\n" : $options .=str_repeat('&nbsp;&nbsp;&nbsp;',$n)."\n";
            $options .="<input type='checkbox' popid='{$val['parent_id']}' name='op_id[]' value='{$val['op_id']}'";
            foreach($selected as $key=>$v)
            {
                if ($val['op_id'] == $v['op_id'])
                {
                    $options .=' checked=true ';
                    unset($selected[$key]);
                }
            }
            $options .="/>".$val['op_name']."</p>\n";
            $options .= self::get_op_option_list($selected,$val['op_id'],$n);
        }
        return $options;
    }


    /**
     * ��ӽ�ɫ����Ȩ��
     * @param int $role_id ��ɫID��ʾ��:1
     * @param array $op_arr ��ɫ����һά���飬ʾ��: $op_arr = array(1,2,3);
     *  @return bool
     */
    public function add_role_op($role_id,$op_arr)
    {
        $role_id = intval($role_id);
        if($role_id <1) return false;
        if(!is_array($op_arr) || empty($op_arr)) return false;
        $this->add_role_op_info($role_id,$op_arr);
    }

    /**
     * ���½�ɫ����Ȩ��
     * @param int $role_id ��ɫID��ʾ��:1
     * @param array $op_arr ��ɫ����һά���飬ʾ��: $op_arr = array(1,2,3);
     * @return bool
     */
    public function update_role_op($role_id,$op_arr)
    {
        $role_id = intval($role_id);
        if($role_id <1) return false;
        $this->del_op_id_by_role_id($role_id);
        if(is_array($op_arr) && !empty($op_arr)) $this->add_role_op_info($role_id,$op_arr);
    }

    /**
     * ��ӽ�ɫȨ�����ɫ������������
     * @param int $role_id ��ɫID��ʾ��:1
     * @param array $op_arr ��ɫ����һά���飬ʾ��: $op_arr = array(1,2,3);
     * @return bool
     */
    private function add_role_op_info($role_id,$op_arr)
    {
        $role_id = intval($role_id);
        if($role_id <1) return false;
        if(!is_array($op_arr) || empty($op_arr)) return false;

        $this->set_role_ref_op_tbl();//ָ����
        $data['role_id'] = $role_id;
        foreach($op_arr as $op_id)
        {
            $data['op_id'] = intval($op_id);
            $this->insert($data,'REPLACE');
        }
    }

    /**
     * ͨ����ɫIDɾ����ɫ�������е�����
     * @param int $role_id ��ɫID��ʾ��:1
     * @return bool
     */
    public function del_op_id_by_role_id($role_id)
    {
        $role_id = intval($role_id);
        if($role_id <1) return false;

        $this->set_role_ref_op_tbl();
        return $this->delete("role_id={$role_id}");
    }

    /**
     * ��Ӷ��� �û��Ͳ������ݽ�����
     * @param int $user_id  �û�ID,ʾ��:100000
     * @param array $op_arr  ����һά����ID��ʾ��: array(1,2,3);
     * @return bool
     */
    public function add_op_ref_user($user_id,$op_arr)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        if(!is_array($op_arr) || empty($op_arr)) return false;
        $this->add_op_ref_user_info($user_id,$op_arr);
    }

    /**
     * �޸��û��Ͳ������ݽ�����
     * @param int $user_id �û�ID,ʾ��:100000
     * @param array $op_arr ����һά����ID��ʾ��: array(1,2,3);
     * @return bool
     */
    public function update_op_ref_user($user_id,$op_arr)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->del_op_ref_by_user_id($user_id);
        if(is_array($op_arr) && !empty($op_arr)) $this->add_op_ref_user_info($user_id,$op_arr);
    }

    /**
     * ��Ӷ����û��Ͳ������ݽ�����ͨ���û�ID
     * @param int $user_id �û�ID,ʾ��:100000
     * @param array $op_arr ����һά����ID��ʾ��: array(1,2,3);
     * @return bool
     */
    private function add_op_ref_user_info($user_id,$op_arr)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        if(!is_array($op_arr) || empty($op_arr)) return false;
        $this->set_admin_ref_op_tbl();
        $data['user_id'] = $user_id;
        foreach($op_arr as $op_id)
        {
            $data['op_id'] = intval($op_id);
            $this->insert($data,'REPLACE');
        }
        return true;
    }

    /**
     * ͨ���û�ID ɾ���û������Ĳ���
     * @param int $user_id �û�ID,ʾ��:100000
     * @return bool
     * @throws App_Exception
     */
    public function del_op_ref_by_user_id($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->set_admin_ref_op_tbl();
        return $this->delete("user_id={$user_id}");
    }

    /**
     * ��ȡ����Ա�����Ĳ�������
     * @param bool $b_select_count   �Ƿ��ѯ����(true��ʾ��ѯ����,false ��ѯ����)
     * @param int $user_id �û�ID,ʾ��:100000
     * @param int $op_id   ����ID,ʾ��:3
     * @param string $where_str ��ѯ����,ʾ��:$where_str ="role_id=1";
     * @param string $order_by  ����,ʾ��:$order_by="role_id DESC";
     * @param string $limit     ѭ������,ʾ��:0,10
     * @param string $fields    ��ѯ�ֶΣ�ʾ��:'op';
     * @return array|int
     */
    public function get_op_ref_admin_list($b_select_count = false,$user_id,$op_id,$where_str = '', $order_by = 'user_id DESC,op_id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_admin_ref_op_tbl();
        $user_id = intval($user_id);
        $op_id = intval($op_id);
        if($user_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($op_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "op_id = {$op_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;
    }
    /**
     * ͨ����ɫID��ȡȨ���б���string ���ص�
     * @param int $role_id ��ɫID
     * @return string
     */
    public function get_op_option_by_role_id($role_id)
    {
        $role_id = intval($role_id);
        if($role_id < 1) return $this->get_op_option_list();
        $selected = $this->get_op_ref_role(false,$role_id,0,'','role_id DESC,op_id DESC','0,99999999','*');
        //print_r($selected);
        return $this->get_op_option_list($selected);
    }

    /**
     * ͨ���û�ID��ȡȨ���б���string ���ص�
     * @param int $user_id �û�ID,ʾ��:1000000
     * @return string
     */
    public function get_admin_op_by_user_id($user_id)
    {
        $user_id = intval($user_id);
        if($user_id < 1) return $this->get_op_option_list();
        $selected = $this->get_op_ref_admin_list($b_select_count = false,$user_id,0,'','user_id DESC,op_id DESC','0,99999999','op_id');
        return $this->get_op_option_list($selected);
    }

    /**
     * ��ȡ�����Ƿ���Ȩ��
     * @param bool $b_select_count
     * @param int $user_id
     * @param string $op_code
     * @param string $op_url
     * @param int $op_level
     * @param int $op_is_nav
     * @param int $parent_id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function  get_op_full_list($b_select_count = false,$user_id,$op_code,$op_url,$op_level,$op_is_nav,$parent_id =0,$where_str = '', $order_by = 'sort DESC,op_id DESC', $limit = '0,20', $fields = '*')
    {
        $admin_index_obj = POCO::singleton( 'pai_admin_index_class' );
        $admin_role_obj = POCO::singleton( 'pai_admin_role_index_class' );
        $user_id = intval($user_id);
        $op_code = trim($op_code);
        $op_url = trim($op_url);
        $op_level = intval($op_level);
        $op_is_nav = intval($op_is_nav);

        $admin_ret = $admin_index_obj->get_info_by_user_id($user_id);//��ȡ����Ա����
        $status = intval($admin_ret['status']);
        if($status <1) return false;//�ر�״̬���˳�
        $role_id = $admin_role_obj->get_admin_role_by_user_id($user_id);//��ȡ��ɫID
        $op_arr = array(); //��������
        if($role_id >0)//ִ��role_ref_op ȥ������
        {
            $op_arr = $this->get_op_ref_role(false,$role_id,0,'','role_id DESC,op_id DESC','0,99999999','op_id');
        }
        else
        {
            $op_arr = $this->get_op_ref_admin_list(false,$user_id,0,'','user_id DESC,op_id DESC','0,99999999','op_id');
        }
        if(!is_array($op_arr) || empty($op_arr)) return false;
        $sql_tmp_str = '';
        foreach($op_arr as $key=>$val)
        {
           if($key !=0) $sql_tmp_str .= ',';
            $sql_tmp_str .= $val['op_id'];
        }
        if(strlen($sql_tmp_str) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_id IN ({$sql_tmp_str})";
        }
        if(strlen($op_code) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_code=:x_op_code";
            sqlSetParam($where_str,'x_op_code',$op_code);
        }
        if(strlen($op_url)>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_url=:x_op_url";
            sqlSetParam($where_str,'x_op_url',$op_url);
        }
        if($op_level>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_level={$op_level}";
        }
        if($op_is_nav>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_is_nav={$op_is_nav}";
        }
        if($parent_id>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "parent_id={$parent_id}";
        }
        $ret = $this->get_op_list($b_select_count,0,-1,$where_str, $order_by, $limit, $fields);
        return $ret;
    }

    /**
     * ҳ�����Ȩ��
     * @param int $user_id    �û�ID
     * @param string $op_code �����
     * @param string $op_url         ��������
     * @param int $parent_id  ����ID��ֹ�������߲�������������ģ���ظ�
     *  @return bool|array
     */
    public function check_op($user_id,$op_code,$op_url = '',$parent_id =0)
    {
        $user_id = intval($user_id);
        $op_code = trim($op_code);
        $op_url = trim($op_url);
        $parent_id = intval($parent_id);
        if($user_id <1) return false;
        if(strlen($op_code)<1 && strlen($op_url) <1)return false;
        $ret = $this->get_op_full_list(false,$user_id,$op_code,$op_url,0,0,$parent_id,'','sort DESC,op_id DESC','0,1','*');
        return $ret;
    }

    //����ͷ����ȡleft
    public function create_nav_list($user_id,$op_code ='',$param='',$op_is_nav = 1)
    {
        $str = '';
        $user_id = intval($user_id);
        $op_code = trim($op_code);
        $param = trim($param);
        $op_is_nav = intval($op_is_nav);
        if($user_id <1 || strlen($op_code) <1)return $str;
        $top_op = $this->get_op_full_list(false,$user_id,$op_code,'',0,$op_is_nav);
        if(!is_array($top_op)) $top_op = array();
        if(strlen($param) <1)//��ȡͷ��
        {
            foreach($top_op as $key=>$val)
            {
                $str .= "<li><span id=\"top_menu_{$val['op_id']}\" role-data=\"{$val['op_id']}\"><a href=\"#this\">{$val['op_name']}</a></span></li>";
            }
        }
        elseif($param == 'left')
        {
            foreach($top_op as $key=>$val)
            {
                $str .="<dl id=\"nav_{$val['op_id']}\" style=\"display: none;\" class=\"nav_info\">";
                $str .=" <dt>{$val['op_name']}</dt>";
                $left_op = $this->get_op_full_list(false,$user_id,'','',4,$op_is_nav,$val['op_id']);
                if(!is_array($left_op)) $left_op = array();
                foreach($left_op as $v)
                {
                    $str .="<dd class=\"off\">";
                    $str .="<span><a href=\"{$v['op_url']}\" target=\"main\">{$v['op_name']}</a></span>";
                    $str .="</dd>";
                }
                $str .="</dl>";
            }
        }
        elseif($param == 'left_v2') //�ж�����
        {
            foreach($top_op as $key=>$val)
            {
                $str .="<dl id=\"nav_{$val['op_id']}\" style=\"display: none;\" class=\"nav_info\">";
                $str .=" <dt>{$val['op_name']}</dt>";
                $left_op = $this->get_op_full_list(false,$user_id,'','',4,$op_is_nav,$val['op_id']);
                if(!is_array($left_op)) $left_op = array();
                $str .="<dd class=\"off\">";
                foreach($left_op as $v)
                {
                    $str .="<span><a href=\"{$v['op_url']}\" target=\"main\"><strong>{$v['op_name']}</strong></a></span>";
                    $left_v2_op = $this->get_op_full_list(false,$user_id,'','',5,$op_is_nav,$v['op_id']);
                    if(!is_array($left_v2_op)) $left_v2_op = array();
                    foreach($left_v2_op as $vo)
                    {
                        $str .= "<div><a href=\"{$vo['op_url']}\" target=\"main\">{$vo['op_name']}</a></div>";
                    }
                }
                $str .="</dd>";
                $str .="</dl>";
            }

        }
        return $str;

    }


    /**
     * �Ѻ���ʾ��Ϣ
     * @param string $msg ��Ϣ
     * @param bool $b_reload
     * @param null $url
     * @param bool $parent �Ƿ���·���
     *
     */
    public static function pop_msg($msg,$b_reload = false,$url=NULL,$parent = true)
    {
        echo "<script language='javascript'>alert('{$msg}');";
        if($url && $parent) echo "parent.location.href = '{$url}';";
        if($url && !$parent) echo "location.href = '{$url}';";
        if($b_reload) echo "history.back();";
        echo "</script>";
        exit;
    }


}